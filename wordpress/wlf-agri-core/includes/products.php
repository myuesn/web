<?php
defined('ABSPATH') || exit;

function wlf_product_categories() {
    return array('tractor' => 'Traktör parçaları', 'harvester' => 'Hasat makinesi parçaları', 'tillage' => 'Toprak işleme');
}
function wlf_product_groups() {
    return array('hydraulic' => 'Hidrolik', 'transmission' => 'Şanzıman ve aktarma', 'engine' => 'Motor', 'axle' => 'Aks ve fren', 'cutting' => 'Kesme ve hasat', 'soil' => 'Toprak işleme');
}
function wlf_register_products() {
    register_post_type('wlf_product', array(
        'labels' => array('name' => 'WLF Ürünler', 'singular_name' => 'Ürün', 'add_new' => 'Ürün ekle', 'add_new_item' => 'Yeni ürün ekle', 'edit_item' => 'Ürünü düzenle', 'search_items' => 'Ürün ara', 'not_found' => 'Ürün bulunamadı', 'all_items' => 'Tüm ürünler'),
        'public' => false, 'show_ui' => true, 'show_in_menu' => true,
        'show_in_rest' => false, 'publicly_queryable' => false,
        'menu_icon' => 'dashicons-products', 'menu_position' => 21,
        'supports' => array('title', 'editor', 'thumbnail', 'revisions'),
        'capability_type' => 'post', 'map_meta_cap' => true, 'rewrite' => false,
    ));
}
add_action('init', 'wlf_register_products');

add_action('add_meta_boxes_wlf_product', function () {
    add_meta_box('wlf-product-fields', 'Ürün bilgileri ve çeviriler', 'wlf_product_metabox', 'wlf_product', 'normal', 'high');
});

function wlf_admin_input($name, $label, $value, $type = 'text') {
    echo '<label class="wlf-field"><span>' . esc_html($label) . '</span>';
    if ($type === 'textarea') {
        echo '<textarea name="' . esc_attr($name) . '" rows="4">' . esc_textarea($value) . '</textarea>';
    } else {
        echo '<input type="text" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '">';
    }
    echo '</label>';
}
function wlf_admin_select($name, $label, $value, $options) {
    echo '<label class="wlf-field"><span>' . esc_html($label) . '</span><select name="' . esc_attr($name) . '">';
    foreach ($options as $key => $text) {
        echo '<option value="' . esc_attr($key) . '" ' . selected($value, $key, false) . '>' . esc_html($text) . '</option>';
    }
    echo '</select></label>';
}
function wlf_product_metabox($post) {
    wp_nonce_field('wlf_save_product_' . $post->ID, 'wlf_product_nonce');
    $meta = function ($key) use ($post) { return get_post_meta($post->ID, '_wlf_' . $key, true); };
    echo '<p><strong>Üstteki başlık ve açıklama İngilizcedir.</strong> Ürün görselini sağdaki “Öne çıkan görsel” bölümünden seçin. Yalnızca yayımlanan ürünler katalogda görünür. Ürünü kaldırmak için Çöp kutusuna taşıyın.</p>';
    if ($meta('sample_key')) {
        echo '<div class="notice notice-warning inline"><p>Bu bir örnek katalog kaydıdır. OEM, uyum, teknik bilgiler ve görseller doğrulanmış ticari veri değildir. Gerçek bilgilerle değiştirip kontrol ettikten sonra yayımlayın.</p></div>';
    }
    echo '<div class="wlf-admin-grid">';
    wlf_admin_input('wlf[code]', 'WLF parça kodu', $meta('code'));
    wlf_admin_input('wlf[oem]', 'OEM / referans numarası', $meta('oem'));
    wlf_admin_select('wlf[category]', 'Kategori', $meta('category') ?: 'tractor', wlf_product_categories());
    wlf_admin_select('wlf[group]', 'Aksam grubu', $meta('group') ?: 'hydraulic', wlf_product_groups());
    wlf_admin_select('wlf[sourcing]', 'Tedarik bilgisi', $meta('sourcing') ?: 'enquiry', array('enquiry' => 'Teklifle teyit edilir', 'made-to-order' => 'Talebe göre tedarik'));
    $brands = $meta('brands'); $models = $meta('models');
    wlf_admin_input('wlf[brands]', 'Markalar (her satırda bir marka)', is_array($brands) ? implode("\n", $brands) : '', 'textarea');
    wlf_admin_input('wlf[models]', 'Uyumlu modeller (her satırda bir model)', is_array($models) ? implode("\n", array_map(function ($model) { return is_array($model) ? ($model['en'] ?? '') : $model; }, $models)) : '', 'textarea');
    echo '</div><hr><h3>İspanyolca / Español</h3>';
    wlf_admin_input('wlf[name_es]', 'İspanyolca ürün adı', $meta('name_es'));
    wlf_admin_input('wlf[description_es]', 'İspanyolca açıklama', $meta('description_es'), 'textarea');
    echo '<h3>Rusça / Русский</h3>';
    wlf_admin_input('wlf[name_ru]', 'Rusça ürün adı', $meta('name_ru'));
    wlf_admin_input('wlf[description_ru]', 'Rusça açıklama', $meta('description_ru'), 'textarea');
    echo '<p class="description">Çeviri boş bırakılırsa katalogda İngilizce metin gösterilir. Yeni ürünleri yayımlamadan önce üç dili de tamamlayın.</p><hr><h3>Teknik özellikler</h3><p>Özellik adını ve değerini her dilde yazın. Boş satırlar kaydedilmez.</p>';
    $specs = $meta('specs'); $specs = is_array($specs) ? $specs : array();
    echo '<div id="wlf-spec-rows">';
    foreach ($specs as $index => $spec) { wlf_spec_admin_row($index, $spec); }
    echo '</div><button type="button" class="button" id="wlf-add-spec">Özellik ekle</button><template id="wlf-spec-template">';
    wlf_spec_admin_row('__INDEX__', array());
    echo '</template>';
}
function wlf_spec_admin_row($index, $spec) {
    echo '<fieldset class="wlf-spec-row"><legend>Teknik özellik</legend><div class="wlf-admin-grid">';
    foreach (array('en' => 'İngilizce', 'es' => 'İspanyolca', 'ru' => 'Rusça') as $lang => $label) {
        wlf_admin_input('wlf[specs][' . $index . '][label][' . $lang . ']', $label . ' özellik adı', isset($spec['label'][$lang]) ? $spec['label'][$lang] : '');
        wlf_admin_input('wlf[specs][' . $index . '][value][' . $lang . ']', $label . ' değer', isset($spec['value'][$lang]) ? $spec['value'][$lang] : '');
    }
    echo '</div><button type="button" class="button-link-delete wlf-remove-spec">Bu özelliği kaldır</button></fieldset>';
}
add_action('admin_enqueue_scripts', function () {
    $screen = get_current_screen();
    if ($screen && $screen->post_type === 'wlf_product') {
        wp_enqueue_style('wlf-products-admin', WLF_CORE_URL . 'assets/admin.css', array(), WLF_CORE_VERSION);
        wp_enqueue_script('wlf-products-admin', WLF_CORE_URL . 'assets/admin.js', array(), WLF_CORE_VERSION, true);
    }
});

/** Only scalar strings are accepted from form arrays. */
function wlf_clean_text($value, $max = 200) {
    if (!is_string($value) && !is_numeric($value)) { return ''; }
    $value = sanitize_text_field((string) $value);
    return function_exists('mb_substr') ? mb_substr($value, 0, $max) : substr($value, 0, $max);
}
function wlf_clean_description($value, $max = 10000) {
    if (!is_string($value)) { return ''; }
    $value = sanitize_textarea_field($value);
    return function_exists('mb_substr') ? mb_substr($value, 0, $max) : substr($value, 0, $max);
}
function wlf_clean_lines($value) {
    if (!is_string($value)) { return array(); }
    $lines = preg_split('/\r\n|\r|\n/', $value);
    return array_values(array_unique(array_filter(array_map('wlf_clean_text', array_slice($lines, 0, 100)), 'strlen')));
}
function wlf_clean_models($models) {
    if (!is_array($models)) { return array(); }
    $result = array();
    foreach (array_slice($models, 0, 100) as $model) {
        if (is_array($model)) {
            $result[] = wlf_translated_value(wlf_clean_text($model['en'] ?? ''), wlf_clean_text($model['es'] ?? ''), wlf_clean_text($model['ru'] ?? ''));
        } else {
            $text = wlf_clean_text($model);
            if ($text !== '') { $result[] = $text; }
        }
    }
    return $result;
}
function wlf_clean_specs($specs) {
    $clean = array();
    if (!is_array($specs)) { return $clean; }
    foreach (array_slice($specs, 0, 50) as $spec) {
        if (!is_array($spec)) { continue; }
        $item = array('label' => array(), 'value' => array());
        foreach (array('en', 'es', 'ru') as $lang) {
            $item['label'][$lang] = wlf_clean_text(isset($spec['label'][$lang]) ? $spec['label'][$lang] : '');
            $item['value'][$lang] = wlf_clean_text(isset($spec['value'][$lang]) ? $spec['value'][$lang] : '', 500);
        }
        if (implode('', $item['label']) !== '' && implode('', $item['value']) !== '') { $clean[] = $item; }
    }
    return $clean;
}
add_action('save_post_wlf_product', function ($post_id) {
    if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || wp_is_post_revision($post_id) || !current_user_can('edit_post', $post_id)) { return; }
    $nonce = isset($_POST['wlf_product_nonce']) && is_string($_POST['wlf_product_nonce']) ? sanitize_text_field(wp_unslash($_POST['wlf_product_nonce'])) : '';
    if (!wp_verify_nonce($nonce, 'wlf_save_product_' . $post_id) || !isset($_POST['wlf']) || !is_array($_POST['wlf'])) { return; }
    $data = wp_unslash($_POST['wlf']);
    foreach (array('code', 'oem', 'name_es', 'name_ru') as $key) { update_post_meta($post_id, '_wlf_' . $key, wlf_clean_text(isset($data[$key]) ? $data[$key] : '')); }
    foreach (array('description_es', 'description_ru') as $key) { update_post_meta($post_id, '_wlf_' . $key, wlf_clean_description(isset($data[$key]) ? $data[$key] : '')); }
    foreach (array('category' => wlf_product_categories(), 'group' => wlf_product_groups(), 'sourcing' => array('enquiry' => '', 'made-to-order' => '')) as $key => $options) {
        $value = isset($data[$key]) ? wlf_clean_text($data[$key]) : '';
        update_post_meta($post_id, '_wlf_' . $key, array_key_exists($value, $options) ? $value : array_key_first($options));
    }
    foreach (array('brands', 'models') as $key) { update_post_meta($post_id, '_wlf_' . $key, wlf_clean_lines(isset($data[$key]) ? $data[$key] : '')); }
    update_post_meta($post_id, '_wlf_specs', wlf_clean_specs(isset($data['specs']) ? $data['specs'] : array()));
});

function wlf_translated_value($en, $es, $ru) {
    $fallback = $en !== '' ? $en : ($es !== '' ? $es : $ru);
    return array('en' => $en !== '' ? $en : $fallback, 'es' => $es !== '' ? $es : $fallback, 'ru' => $ru !== '' ? $ru : $fallback);
}
function wlf_product_data($post) {
    $meta = function ($key) use ($post) { return get_post_meta($post->ID, '_wlf_' . $key, true); };
    $image = get_the_post_thumbnail_url($post->ID, 'large');
    if (!$image && $meta('sample_key')) {
        $sample_image = sanitize_file_name((string) $meta('sample_image'));
        if ($sample_image && is_file(WLF_CORE_DIR . 'assets/' . $sample_image)) { $image = WLF_CORE_URL . 'assets/' . $sample_image; }
    }
    $image = $image ?: WLF_CORE_URL . 'assets/part-placeholder.svg';
    $specs = array();
    foreach (wlf_clean_specs($meta('specs')) as $spec) {
        foreach (array('label', 'value') as $key) { $spec[$key] = wlf_translated_value($spec[$key]['en'], $spec[$key]['es'], $spec[$key]['ru']); }
        $specs[] = $spec;
    }
    $category = $meta('category'); $group = $meta('group');
    return array(
        'id' => (string) $post->ID, 'code' => wlf_clean_text($meta('code')), 'oem' => wlf_clean_text($meta('oem')),
        'name' => wlf_translated_value(wp_strip_all_tags(get_the_title($post)), wlf_clean_text($meta('name_es')), wlf_clean_text($meta('name_ru'))),
        'description' => wlf_translated_value(wp_strip_all_tags($post->post_content), wlf_clean_description($meta('description_es')), wlf_clean_description($meta('description_ru'))),
        'category' => array_key_exists($category, wlf_product_categories()) ? $category : 'tractor',
        'group' => array_key_exists($group, wlf_product_groups()) ? $group : 'hydraulic',
        'brands' => is_array($meta('brands')) ? array_values(array_map('wlf_clean_text', $meta('brands'))) : array(),
        'models' => wlf_clean_models($meta('models')),
        'sourcing' => $meta('sourcing') === 'made-to-order' ? 'made-to-order' : 'enquiry',
        'image' => esc_url_raw($image), 'specs' => $specs,
        'isSample' => (bool) $meta('sample_key'),
    );
}
/** Public catalogue contract shared with the WLF theme; no illustrative fallback. */
function wlf_products_data() {
    return array_map('wlf_product_data', get_posts(array('post_type' => 'wlf_product', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC', 'suppress_filters' => false)));
}
add_filter('manage_wlf_product_posts_columns', function ($columns) {
    $columns['wlf_code'] = 'WLF / OEM'; $columns['wlf_languages'] = 'Çeviriler'; return $columns;
});
add_action('manage_wlf_product_posts_custom_column', function ($column, $post_id) {
    if ($column === 'wlf_code') { echo esc_html(get_post_meta($post_id, '_wlf_code', true) . ' / ' . get_post_meta($post_id, '_wlf_oem', true)); }
    if ($column === 'wlf_languages') {
        $ready = array();
        foreach (array('es' => 'ES', 'ru' => 'RU') as $lang => $label) { $ready[] = $label . (get_post_meta($post_id, '_wlf_name_' . $lang, true) && get_post_meta($post_id, '_wlf_description_' . $lang, true) ? ' ✓' : ' — eksik'); }
        echo esc_html(implode(' · ', $ready));
    }
}, 10, 2);

add_action('admin_menu', function () {
    add_submenu_page('edit.php?post_type=wlf_product', 'Örnek ürünler', 'Örnek ürünler', 'manage_options', 'wlf-sample-import', 'wlf_sample_import_page');
});
function wlf_sample_import_page() {
    if (!current_user_can('manage_options')) { return; }
    echo '<div class="wrap"><h1>Örnek ürünleri içe aktar</h1><p>Örnek kayıtlar taslak olarak eklenir. Gerçek ürün, uyum, stok veya teknik özellik garantisi içermez. Bilgileri kontrol edip düzenledikten sonra tek tek yayımlayın.</p><p>Mevcut ürünler değiştirilmez. Daha önce içe aktarılmış örnekler yeniden eklenmez.</p>';
    if (isset($_GET['wlf_imported'])) { echo '<div class="notice notice-success"><p>Eklenen taslak: ' . esc_html(absint($_GET['wlf_imported'])) . '</p></div>'; }
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '"><input type="hidden" name="action" value="wlf_import_samples">';
    wp_nonce_field('wlf_import_samples'); submit_button('Örnekleri taslak olarak ekle'); echo '</form></div>';
}
/** Explicit import only. Never replace content, even when a prior sample is trashed. */
function wlf_import_sample_products() {
    $path = WLF_CORE_DIR . 'samples/products.json';
    if (!is_file($path)) { return new WP_Error('wlf_missing_samples', 'Örnek ürün dosyası bulunamadı.'); }
    $products = json_decode(file_get_contents($path), true);
    if (!is_array($products)) { return new WP_Error('wlf_invalid_samples', 'Örnek ürün dosyası geçersiz.'); }
    $imported = (array) get_option('wlf_sample_imported_keys', array()); $count = 0;
    foreach ($products as $product) {
        if (!is_array($product) || empty($product['id']) || empty($product['name']['en'])) { continue; }
        $key = sanitize_key($product['id']);
        if (!$key || in_array($key, $imported, true)) { continue; }
        $existing = get_posts(array('post_type' => 'wlf_product', 'post_status' => array('publish', 'draft', 'pending', 'private', 'future', 'trash'), 'posts_per_page' => 1, 'meta_key' => '_wlf_sample_key', 'meta_value' => $key, 'fields' => 'ids'));
        if ($existing) { $imported[] = $key; continue; }
        $post_id = wp_insert_post(wp_slash(array('post_type' => 'wlf_product', 'post_status' => 'draft', 'post_title' => wlf_clean_text($product['name']['en']), 'post_content' => wlf_clean_description(isset($product['description']['en']) ? $product['description']['en'] : ''))), true);
        if (is_wp_error($post_id)) { continue; }
        foreach (array('code', 'oem') as $meta) { update_post_meta($post_id, '_wlf_' . $meta, wlf_clean_text(isset($product[$meta]) ? $product[$meta] : '')); }
        foreach (array('category' => wlf_product_categories(), 'group' => wlf_product_groups(), 'sourcing' => array('enquiry' => '', 'made-to-order' => '')) as $meta => $options) {
            $value = isset($product[$meta]) ? wlf_clean_text($product[$meta]) : '';
            update_post_meta($post_id, '_wlf_' . $meta, array_key_exists($value, $options) ? $value : array_key_first($options));
        }
        foreach (array('es', 'ru') as $lang) {
            update_post_meta($post_id, '_wlf_name_' . $lang, wlf_clean_text(isset($product['name'][$lang]) ? $product['name'][$lang] : ''));
            update_post_meta($post_id, '_wlf_description_' . $lang, wlf_clean_description(isset($product['description'][$lang]) ? $product['description'][$lang] : ''));
        }
        foreach (array('brands', 'models') as $meta) {
            $values = isset($product[$meta]) && is_array($product[$meta]) ? $product[$meta] : array();
            update_post_meta($post_id, '_wlf_' . $meta, $meta === 'models' ? wlf_clean_models($values) : array_values(array_filter(array_map('wlf_clean_text', $values), 'strlen')));
        }
        update_post_meta($post_id, '_wlf_specs', wlf_clean_specs(isset($product['specs']) ? $product['specs'] : array()));
        update_post_meta($post_id, '_wlf_sample_key', $key);
        update_post_meta($post_id, '_wlf_sample_image', sanitize_file_name(basename(isset($product['image']) ? $product['image'] : '')));
        $imported[] = $key; update_option('wlf_sample_imported_keys', array_unique($imported), false); $count++;
    }
    return $count;
}
add_action('admin_post_wlf_import_samples', function () {
    if (!current_user_can('manage_options')) { wp_die('Bu işlem için yetkiniz yok.', '', array('response' => 403)); }
    check_admin_referer('wlf_import_samples'); $count = wlf_import_sample_products();
    if (is_wp_error($count)) { wp_die(esc_html($count->get_error_message())); }
    wp_safe_redirect(add_query_arg('wlf_imported', $count, admin_url('edit.php?post_type=wlf_product&page=wlf-sample-import'))); exit;
});
