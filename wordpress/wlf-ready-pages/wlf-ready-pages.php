<?php
/**
 * Plugin Name: WLF Hazır Sayfalar
 * Description: Sık Sorulan Sorular ve Sipariş / İhracat Süreci sayfalarını EN/ES/RU bloklarla taslak olarak ekler. Mevcut içerikleri değiştirmez.
 * Version: 1.0.0
 * Requires at least: 6.5
 * Requires PHP: 7.4
 * Author: WLF Agri Parts
 * License: GPL-2.0-or-later
 */
if (!defined('ABSPATH')) { exit; }

function wlf_ready_pages_activate() {
    $pages = json_decode(file_get_contents(__DIR__ . '/pages.json'), true);
    if (!is_array($pages) || count($pages) !== 2) {
        wp_die('WLF sayfa paketi okunamadı. ZIP dosyasını yeniden yükleyin.');
    }
    $ids = (array) get_option('wlf_ready_pages_v1', array());
    foreach ($pages as $page) {
        $slug = sanitize_key($page['slug']);
        $existing = isset($ids[$slug]) ? get_post(absint($ids[$slug])) : null;
        if ($existing && $existing->post_type === 'page' && get_post_meta($existing->ID, '_wlf_ready_page', true) === $slug) {
            continue;
        }
        $found = get_posts(array(
            'post_type' => 'page', 'post_status' => array('publish', 'draft', 'pending', 'private', 'future', 'trash'),
            'meta_key' => '_wlf_ready_page', 'meta_value' => $slug,
            'numberposts' => 1, 'fields' => 'ids',
        ));
        if ($found) { $ids[$slug] = $found[0]; continue; }
        $id = wp_insert_post(wp_slash(array(
            'post_type' => 'page', 'post_status' => 'draft',
            'post_title' => $page['title'], 'post_name' => $slug,
            'post_content' => $page['content'], 'post_author' => get_current_user_id(),
            'comment_status' => 'closed', 'ping_status' => 'closed',
            'meta_input' => array('_wlf_ready_page' => $slug),
        )), true);
        if (is_wp_error($id)) {
            update_option('wlf_ready_pages_v1', $ids, false);
            wp_die(esc_html('Sayfa oluşturulamadı: ' . $id->get_error_message()));
        }
        $ids[$slug] = $id;
    }
    update_option('wlf_ready_pages_v1', $ids, false);
}
register_activation_hook(__FILE__, 'wlf_ready_pages_activate');

function wlf_ready_pages_notice() {
    if (!current_user_can('edit_pages')) { return; }
    $screen = get_current_screen();
    if (!$screen || $screen->id !== 'plugins') { return; }
    $ids = (array) get_option('wlf_ready_pages_v1', array());
    if (!$ids) { return; }
    echo '<div class="notice notice-success is-dismissible"><p><strong>WLF sayfaları hazır.</strong> İngilizce, İspanyolca ve Rusça metinler bloklara eklendi. Sayfaları açıp önizleyebilir ve yayımlayabilirsiniz:</p><ul>';
    foreach ($ids as $id) {
        $page = get_post(absint($id));
        if (!$page || $page->post_type !== 'page' || $page->post_status === 'trash' || !current_user_can('edit_post', $page->ID)) { continue; }
        echo '<li><a href="' . esc_url(get_edit_post_link($page->ID)) . '">' . esc_html($page->post_title) . '</a> — ' . esc_html($page->post_status === 'publish' ? 'Yayımlandı' : 'Taslak / inceleme') . '</li>';
    }
    echo '</ul><p>Mevcut sayfalarınız korunur. Bu eklentiyi kapatmak veya silmek oluşturulan sayfaları kaldırmaz. Tasarımlar ve dil seçimi WLF Agri temasını kullanır.</p></div>';
}
add_action('admin_notices', 'wlf_ready_pages_notice');

function wlf_ready_pages_styles() {
    if (!is_admin() && !get_post_meta(get_queried_object_id(), '_wlf_ready_page', true)) { return; }
    wp_enqueue_style('wlf-ready-pages', plugins_url('pages.css', __FILE__), array(), '1.0.0');
}
add_action('enqueue_block_assets', 'wlf_ready_pages_styles');
add_action('wp_enqueue_scripts', 'wlf_ready_pages_styles');
