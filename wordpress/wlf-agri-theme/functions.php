<?php
/** WLF presentation and editor integration. */
if (!defined('ABSPATH')) { exit; }

function wlf_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('editor-styles');
    add_theme_support('custom-logo');
    add_theme_support('html5', array('search-form', 'gallery', 'caption', 'style', 'script'));
    add_editor_style(array('assets/style.css', 'assets/theme.css'));
    register_nav_menus(array('primary' => 'Ana menü', 'footer' => 'Alt menü'));
}
add_action('after_setup_theme', 'wlf_theme_setup');

function wlf_asset($path) {
    return trailingslashit(get_template_directory_uri()) . 'assets/' . ltrim($path, '/');
}

function wlf_page_url($role) {
    $ids = (array) get_option('wlf_page_ids', array());
    if (!empty($ids[$role]) && get_post_status((int) $ids[$role]) === 'publish') {
        return get_permalink((int) $ids[$role]);
    }
    if ($role === 'home') { return home_url('/'); }
    $fallback = get_page_by_path('wlf-' . sanitize_title($role));
    return $fallback && $fallback->post_status === 'publish' ? get_permalink($fallback) : home_url('/wlf-' . sanitize_title($role) . '/');
}

function wlf_current_role() {
    $ids = (array) get_option('wlf_page_ids', array());
    foreach ($ids as $role => $id) {
        if (is_page((int) $id)) { return sanitize_key($role); }
    }
    return is_front_page() ? 'home' : 'page';
}

function wlf_phone_digits($value) {
    $digits = preg_replace('/\D/', '', (string) $value);
    return strlen($digits) >= 8 && strlen($digits) <= 15 ? $digits : '';
}

function wlf_sales_email() {
    $email = sanitize_email(get_theme_mod('wlf_sales_email', 'sales@wlfagriparts.com'));
    return is_email($email) ? $email : 'sales@wlfagriparts.com';
}

function wlf_theme_assets() {
    $base = get_template_directory();
    wp_enqueue_style('wlf-site', wlf_asset('style.css'), array(), is_file($base . '/assets/style.css') ? (string) filemtime($base . '/assets/style.css') : '1.0');
    wp_enqueue_style('wlf-wordpress', wlf_asset('theme.css'), array('wlf-site'), (string) filemtime($base . '/assets/theme.css'));
    wp_enqueue_script('wlf-app', wlf_asset('app.js'), array(), is_file($base . '/assets/app.js') ? (string) filemtime($base . '/assets/app.js') : '1.0', true);
    $urls = array();
    foreach (array('home', 'products', 'about', 'quote', 'contact') as $role) { $urls[$role] = wlf_page_url($role); }
    $phone = sanitize_text_field(get_theme_mod('wlf_phone', ''));
    $whatsapp = wlf_phone_digits(get_theme_mod('wlf_whatsapp_number', ''));
    $config = array(
        'urls' => $urls,
        'phone' => wlf_phone_digits($phone) ? $phone : '',
        'whatsapp' => $whatsapp,
        'whatsappEnabled' => (bool) get_theme_mod('wlf_whatsapp_enabled', false) && $whatsapp !== '',
        'salesEmail' => wlf_sales_email(),
        'restUrl' => esc_url_raw(rest_url('wlf/v1/enquiries')),
        'nonce' => wp_create_nonce('wp_rest'),
    );
    $products = function_exists('wlf_products_data') ? wlf_products_data() : array();
    $flags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
    wp_add_inline_script('wlf-app', 'window.WLF_PRODUCTS=' . wp_json_encode($products, $flags) . ';window.WLF_SITE=' . wp_json_encode($config, $flags) . ';', 'before');
}
add_action('wp_enqueue_scripts', 'wlf_theme_assets');

// Quotation forms contain a short-lived WordPress nonce and must not be page-cached.
add_action('template_redirect', function () {
    if (wlf_current_role() === 'quote') {
        if (!defined('DONOTCACHEPAGE')) { define('DONOTCACHEPAGE', true); }
        nocache_headers();
        do_action('litespeed_control_set_nocache', 'WLF quotation form');
    }
}, 1);

/** Safe, multilingual short UI strings. */
function wlf_text($en, $es, $ru, $tag = 'span', $class = '') {
    $allowed = array('span', 'p', 'h2', 'h3', 'strong');
    if (!in_array($tag, $allowed, true)) { $tag = 'span'; }
    return '<' . $tag . ($class ? ' class="' . esc_attr($class) . '"' : '') . ' data-en="' . esc_attr($en) . '" data-es="' . esc_attr($es) . '" data-ru="' . esc_attr($ru) . '">' . esc_html($en) . '</' . $tag . '>';
}

function wlf_default_menu($args = array()) {
    $labels = array(
        'home' => array('Home', 'Inicio', 'Главная'),
        'products' => array('Products', 'Productos', 'Продукция'),
        'about' => array('About us', 'Quiénes somos', 'О компании'),
        'quote' => array('Request a quote', 'Solicitar presupuesto', 'Запросить предложение'),
        'contact' => array('Contact', 'Contacto', 'Контакты'),
    );
    foreach ($labels as $role => $label) {
        echo '<a href="' . esc_url(wlf_page_url($role)) . '"' . (wlf_current_role() === $role ? ' class="active" aria-current="page"' : '') . '>' . wlf_text($label[0], $label[1], $label[2]) . '</a>';
    }
}

/** Extend native blocks with visual-editor translation fields. */
function wlf_block_attributes($args, $name) {
    if (in_array($name, array('core/heading', 'core/paragraph', 'core/button'), true)) {
        $args['attributes']['wlfEs'] = array('type' => 'string', 'default' => '');
        $args['attributes']['wlfRu'] = array('type' => 'string', 'default' => '');
    }
    return $args;
}
add_filter('register_block_type_args', 'wlf_block_attributes', 10, 2);

function wlf_translate_block($content, $block) {
    if (!in_array($block['blockName'], array('core/heading', 'core/paragraph', 'core/button'), true) || !class_exists('WP_HTML_Tag_Processor')) { return $content; }
    $es = isset($block['attrs']['wlfEs']) ? sanitize_textarea_field($block['attrs']['wlfEs']) : '';
    $ru = isset($block['attrs']['wlfRu']) ? sanitize_textarea_field($block['attrs']['wlfRu']) : '';
    if ($es === '' && $ru === '') { return $content; }
    $processor = new WP_HTML_Tag_Processor($content);
    $tag = $block['blockName'] === 'core/button' ? 'A' : ($block['blockName'] === 'core/paragraph' ? 'P' : null);
    if (!$processor->next_tag($tag ? array('tag_name' => $tag) : null)) { return $content; }
    $en = trim(wp_strip_all_tags(html_entity_decode($content, ENT_QUOTES, 'UTF-8')));
    $processor->set_attribute('data-en', $en);
    $processor->set_attribute('data-es', $es !== '' ? $es : $en);
    $processor->set_attribute('data-ru', $ru !== '' ? $ru : $en);
    return $processor->get_updated_html();
}
add_filter('render_block', 'wlf_translate_block', 10, 2);

function wlf_editor_assets() {
    wp_enqueue_script('wlf-block-translations', wlf_asset('editor.js'), array('wp-hooks', 'wp-compose', 'wp-element', 'wp-components', 'wp-block-editor'), (string) filemtime(get_template_directory() . '/assets/editor.js'), true);
}
add_action('enqueue_block_editor_assets', 'wlf_editor_assets');

require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/shortcodes.php';
require_once get_template_directory() . '/inc/starter.php';
require_once get_template_directory() . '/inc/footer-navigation.php';
