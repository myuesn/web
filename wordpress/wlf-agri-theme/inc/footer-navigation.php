<?php
/** Resolve legacy footer links against the site's current WLF page records. */
if (!defined('ABSPATH')) { exit; }

function wlf_footer_target_url($role) {
    $ids = (array) get_option('wlf_page_ids', array());
    if (in_array($role, array('faq', 'order-export'), true)) {
        $ready = (array) get_option('wlf_ready_pages_v1', array());
        $ids[$role] = isset($ready['wlf-' . $role]) ? $ready['wlf-' . $role] : 0;
    }
    $id = isset($ids[$role]) ? absint($ids[$role]) : 0;
    if ($id && get_post_status($id) === 'publish') { return get_permalink($id); }
    $page = get_page_by_path('wlf-' . sanitize_title($role));
    if ($page && $page->post_status === 'publish') { return get_permalink($page); }
    return $role === 'home' ? home_url('/') : '';
}

function wlf_footer_navigation_links($items, $args) {
    if (!isset($args->theme_location) || $args->theme_location !== 'footer') { return $items; }
    $aliases = array(
        'home' => array('home', 'index', 'wlf-home', 'ana-sayfa'),
        'products' => array('products', 'product', 'shop', 'catalog', 'catalogue', 'wlf-products', 'urunler'),
        'about' => array('about', 'about-us', 'wlf-about', 'wlf-about-us', 'hakkimizda'),
        'quote' => array('quote', 'request-a-quote', 'request-quote', 'wlf-quote', 'wlf-request-a-quote', 'teklif-iste'),
        'contact' => array('contact', 'contact-us', 'wlf-contact', 'iletisim'),
        'faq' => array('faq', 'faqs', 'frequently-asked-questions', 'wlf-faq', 'wlf-frequently-asked-questions', 'sik-sorulan-sorular'),
        'order-export' => array('order-export', 'order-export-process', 'order-and-export-process', 'wlf-order-export', 'wlf-order-export-process'),
    );
    $home_host = preg_replace('/^www\./i', '', (string) wp_parse_url(home_url('/'), PHP_URL_HOST));
    foreach ($items as $index => $item) {
        $url = isset($item->url) ? (string) $item->url : '';
        if ($url === '' || $url[0] === '#') { continue; }
        $parts = wp_parse_url($url);
        if ($parts === false || (!empty($parts['scheme']) && !in_array(strtolower($parts['scheme']), array('http', 'https'), true))) { continue; }
        if (!empty($parts['host']) && strcasecmp(preg_replace('/^www\./i', '', $parts['host']), $home_host) !== 0) { continue; }
        $path = isset($parts['path']) ? basename(untrailingslashit($parts['path'])) : '';
        $keys = array(sanitize_title(preg_replace('/\.(?:html?|php)$/i', '', $path)), sanitize_title(wp_strip_all_tags($item->title)));
        $post_id = isset($item->object, $item->object_id) && $item->object === 'page' ? absint($item->object_id) : 0;
        if (!empty($parts['query'])) {
            parse_str($parts['query'], $query);
            if (!empty($query['page_id'])) { $post_id = absint($query['page_id']); }
            if (!empty($query['pagename']) && is_string($query['pagename'])) { $keys[] = sanitize_title(basename($query['pagename'])); }
        }
        if ($post_id) { $keys[] = sanitize_title((string) get_post_field('post_name', $post_id)); }
        foreach ($aliases as $role => $names) {
            if (!array_intersect($keys, $names)) { continue; }
            $target = wlf_footer_target_url($role);
            if ($target !== '') {
                $replacement = clone $item;
                $replacement->url = $target;
                $items[$index] = $replacement;
            }
            break;
        }
    }
    return $items;
}
add_filter('wp_nav_menu_objects', 'wlf_footer_navigation_links', 20, 2);
