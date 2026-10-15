<?php
defined('ABSPATH') || exit;

function wlf_register_enquiries() {
    register_post_type('wlf_enquiry', array(
        'labels' => array('name' => 'WLF Teklif talepleri', 'singular_name' => 'Teklif talebi', 'edit_item' => 'Teklif talebini görüntüle', 'search_items' => 'Teklif talebi ara', 'all_items' => 'Tüm talepler', 'not_found' => 'Talep bulunamadı'),
        'public' => false, 'publicly_queryable' => false, 'exclude_from_search' => true,
        'show_ui' => true, 'show_in_menu' => true, 'show_in_rest' => false,
        'rewrite' => false, 'query_var' => false, 'menu_icon' => 'dashicons-email-alt',
        'supports' => array('title'), 'map_meta_cap' => false,
        'capabilities' => array(
            'edit_post' => 'manage_options', 'read_post' => 'manage_options', 'delete_post' => 'manage_options',
            'edit_posts' => 'manage_options', 'edit_others_posts' => 'manage_options', 'publish_posts' => 'manage_options',
            'read_private_posts' => 'manage_options', 'delete_posts' => 'manage_options', 'delete_private_posts' => 'manage_options',
            'delete_published_posts' => 'manage_options', 'delete_others_posts' => 'manage_options',
            'edit_private_posts' => 'manage_options', 'edit_published_posts' => 'manage_options', 'create_posts' => 'do_not_allow',
        ),
    ));
}
add_action('init', 'wlf_register_enquiries');

add_action('rest_api_init', function () {
    register_rest_route('wlf/v1', '/enquiries', array(
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 'wlf_submit_enquiry',
        'permission_callback' => 'wlf_enquiry_permission',
    ));
});

function wlf_enquiry_permission($request) {
    if (!wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest')) {
        return new WP_Error('wlf_invalid_nonce', 'Please reload this page and try again.', array('status' => 403));
    }
    return true;
}

/** A short-lived keyed hash prevents keeping visitors' raw IP addresses in records. */
function wlf_enquiry_rate_key() {
    $ip = isset($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
    return 'wlf_rfq_' . substr(hash_hmac('sha256', $ip, wp_salt('auth')), 0, 48);
}

function wlf_enquiry_rate_limit() {
    $key = wlf_enquiry_rate_key();
    $current = get_transient($key);
    $now = time();
    if (!is_array($current) || !isset($current['until']) || $current['until'] <= $now) {
        $current = array('count' => 0, 'until' => $now + 10 * MINUTE_IN_SECONDS);
    }
    if ($current['count'] >= 5) {
        return new WP_Error('wlf_rate_limited', 'Please wait a few minutes before trying again.', array('status' => 429));
    }
    $current['count']++;
    set_transient($key, $current, max(1, $current['until'] - $now));
    return true;
}

function wlf_enquiry_bad_request($message) {
    return new WP_Error('wlf_invalid_enquiry', $message, array('status' => 400));
}

/** Validate quantities and resolve all item names/references from published server records. */
function wlf_validate_enquiry($raw) {
    if (!is_array($raw)) { return wlf_enquiry_bad_request('A JSON object is required.'); }
    if (isset($raw['website']) && (!is_string($raw['website']) || trim($raw['website']) !== '')) {
        return wlf_enquiry_bad_request('The enquiry could not be accepted.');
    }
    $data = array();
    $limits = array('contact' => 200, 'company' => 200, 'email' => 254, 'country' => 200, 'details' => 5000, 'phone' => 200, 'machine' => 200);
    foreach ($limits as $key => $max) {
        $value = isset($raw[$key]) ? $raw[$key] : '';
        $length = is_string($value) ? (function_exists('mb_strlen') ? mb_strlen($value) : strlen($value)) : $max + 1;
        if (!is_string($value) || $length > $max) { return wlf_enquiry_bad_request('An enquiry field is invalid or too long.'); }
        $data[$key] = $key === 'details' ? wlf_clean_description($value, $max) : wlf_clean_text($value, $max);
        if (in_array($key, array('contact', 'company', 'email', 'country', 'details'), true) && $data[$key] === '') {
            return wlf_enquiry_bad_request('Please complete all required fields.');
        }
    }
    if (!is_email($data['email'])) { return wlf_enquiry_bad_request('A valid email address is required.'); }
    $data['email'] = sanitize_email($data['email']);
    $lang = isset($raw['lang']) ? $raw['lang'] : 'en';
    if (!is_string($lang) || !in_array($lang, array('en', 'es', 'ru'), true)) { return wlf_enquiry_bad_request('The selected language is invalid.'); }
    $data['lang'] = $lang;
    $raw_items = isset($raw['items']) ? $raw['items'] : array();
    if (!is_array($raw_items) || count($raw_items) > 50) { return wlf_enquiry_bad_request('Please request no more than 50 different parts.'); }
    $items = array();
    foreach ($raw_items as $item) {
        if (!is_array($item) || !isset($item['id'], $item['quantity']) || (!is_string($item['id']) && !is_int($item['id'])) || (!is_string($item['quantity']) && !is_int($item['quantity']))) {
            return wlf_enquiry_bad_request('A selected part or quantity is invalid.');
        }
        if (!preg_match('/^[1-9][0-9]*$/D', (string) $item['id']) || !preg_match('/^[1-9][0-9]*$/D', (string) $item['quantity'])) {
            return wlf_enquiry_bad_request('Part identifiers and quantities must be positive whole numbers.');
        }
        $id = (int) $item['id']; $quantity = (int) $item['quantity'];
        if ($id < 1 || $quantity < 1 || $quantity > 1000000 || strlen((string) $item['id']) > 18) { return wlf_enquiry_bad_request('A selected part or quantity is invalid.'); }
        $post = get_post($id);
        if (!$post || $post->post_type !== 'wlf_product' || $post->post_status !== 'publish') { return wlf_enquiry_bad_request('A selected part is no longer available. Please refresh your parts list.'); }
        if (isset($items[$id])) {
            $items[$id]['quantity'] += $quantity;
            if ($items[$id]['quantity'] > 1000000) { return wlf_enquiry_bad_request('The quantity is too large.'); }
        } else {
            $product = wlf_product_data($post);
            $items[$id] = array('id' => (string) $id, 'quantity' => $quantity, 'code' => $product['code'], 'oem' => $product['oem'], 'name' => $product['name'][$lang]);
        }
    }
    $data['items'] = array_values($items);
    return $data;
}

function wlf_submit_enquiry($request) {
    if (strlen($request->get_body()) > 65536) { return wlf_enquiry_bad_request('The enquiry is too large.'); }
    $allowed = wlf_enquiry_rate_limit();
    if (is_wp_error($allowed)) { return $allowed; }
    $data = wlf_validate_enquiry($request->get_json_params());
    if (is_wp_error($data)) { return $data; }
    $id = wp_insert_post(wp_slash(array('post_type' => 'wlf_enquiry', 'post_status' => 'private', 'post_title' => $data['company'] . ' — ' . $data['contact'])), true);
    if (is_wp_error($id) || !$id) { return new WP_Error('wlf_save_failed', 'Your enquiry could not be saved. Please try again or contact us by email.', array('status' => 500)); }
    $reference = 'WLF-' . $id;
    update_post_meta($id, '_wlf_reference', $reference);
    update_post_meta($id, '_wlf_enquiry', $data);
    wp_update_post(wp_slash(array('ID' => $id, 'post_title' => $reference . ' · ' . $data['company'])));

    $recipient = sanitize_email(get_theme_mod('wlf_sales_email', 'sales@wlfagriparts.com'));
    $lines = array('WLF Agri Parts enquiry — ' . $reference, '', 'Contact: ' . $data['contact'], 'Company: ' . $data['company'], 'Email: ' . $data['email'], 'Country: ' . $data['country'], 'Phone: ' . $data['phone'], 'Machine: ' . $data['machine'], 'Language: ' . $data['lang'], '', 'Requested parts:');
    foreach ($data['items'] as $item) { $lines[] = $item['code'] . ' | OEM ' . $item['oem'] . ' | ' . $item['name'] . ' | Qty ' . $item['quantity']; }
    $lines[] = ''; $lines[] = 'Requirement:'; $lines[] = $data['details'];
    $lines[] = ''; $lines[] = 'View this private enquiry in WordPress: ' . admin_url('post.php?post=' . $id . '&action=edit');
    $mail_accepted = false;
    if (is_email($recipient)) {
        try {
            $mail_accepted = (bool) wp_mail($recipient, '[' . $reference . '] New parts enquiry', implode("\n", $lines), array('Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $data['email']));
        } catch (Throwable $error) {
            $mail_accepted = false;
        }
    }
    update_post_meta($id, '_wlf_mail_accepted', $mail_accepted ? 'yes' : 'no');
    // wp_mail accepting a message does not prove that the recipient received it.
    return new WP_REST_Response(array('saved' => true, 'reference' => $reference, 'mailAccepted' => $mail_accepted), 201);
}

add_action('add_meta_boxes_wlf_enquiry', function () {
    add_meta_box('wlf-enquiry-record', 'Talep bilgileri', 'wlf_enquiry_metabox', 'wlf_enquiry', 'normal', 'high');
});
function wlf_enquiry_metabox($post) {
    if (!current_user_can('manage_options')) { return; }
    $data = get_post_meta($post->ID, '_wlf_enquiry', true);
    if (!is_array($data)) { echo '<p>Kayıt bilgileri bulunamadı.</p>'; return; }
    $labels = array('contact' => 'Ad soyad', 'company' => 'Şirket', 'email' => 'E-posta', 'country' => 'Ülke', 'phone' => 'Telefon', 'machine' => 'Makine', 'lang' => 'Dil', 'details' => 'Talep ayrıntıları');
    echo '<p><strong>Referans: ' . esc_html(get_post_meta($post->ID, '_wlf_reference', true)) . '</strong></p><table class="widefat striped wlf-record">';
    foreach ($labels as $key => $label) { echo '<tr><th>' . esc_html($label) . '</th><td style="white-space:pre-wrap">' . esc_html(isset($data[$key]) ? $data[$key] : '') . '</td></tr>'; }
    echo '</table><h3>İstenen ürünler</h3><table class="widefat striped"><thead><tr><th>WLF / OEM</th><th>Ürün</th><th>Adet</th></tr></thead><tbody>';
    foreach (isset($data['items']) ? $data['items'] : array() as $item) { echo '<tr><td>' . esc_html($item['code'] . ' / ' . $item['oem']) . '</td><td>' . esc_html($item['name']) . '</td><td>' . esc_html($item['quantity']) . '</td></tr>'; }
    echo '</tbody></table><h3>E-posta durumu</h3><p>' . (get_post_meta($post->ID, '_wlf_mail_accepted', true) === 'yes' ? 'Sunucu e-posta bildirimini kabul etti. Alıcıya teslim edildiği doğrulanmış değildir.' : 'E-posta bildirimi sunucu tarafından kabul edilmedi veya alıcı adresi geçersiz. Talep bu panelde kayıtlıdır. SMTP ayarlarını kontrol edin.') . '</p><p>Bu özel kayıt yalnızca yöneticilere açıktır. Gerekmeyen kayıtları Çöp kutusuna taşıyıp kalıcı olarak silebilirsiniz.</p>';
}
add_filter('manage_wlf_enquiry_posts_columns', function ($columns) {
    return array('cb' => $columns['cb'], 'title' => 'Referans / Şirket', 'wlf_contact' => 'İletişim', 'wlf_country' => 'Ülke', 'wlf_mail' => 'E-posta bildirimi', 'date' => 'Tarih');
});
add_action('manage_wlf_enquiry_posts_custom_column', function ($column, $post_id) {
    $data = get_post_meta($post_id, '_wlf_enquiry', true);
    if (!is_array($data)) { return; }
    if ($column === 'wlf_contact') { echo esc_html($data['contact'] . ' · ' . $data['email']); }
    if ($column === 'wlf_country') { echo esc_html($data['country']); }
    if ($column === 'wlf_mail') { echo get_post_meta($post_id, '_wlf_mail_accepted', true) === 'yes' ? 'Sunucu kabul etti' : 'Gönderim ayarlarını kontrol edin'; }
}, 10, 2);
add_filter('post_row_actions', function ($actions, $post) {
    if ($post->post_type === 'wlf_enquiry') { unset($actions['inline hide-if-no-js'], $actions['view']); }
    return $actions;
}, 10, 2);
