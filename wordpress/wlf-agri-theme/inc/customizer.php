<?php
if (!defined('ABSPATH')) { exit; }

function wlf_sanitize_phone($value) {
    return trim((string) preg_replace('/[^0-9+() .-]/', '', sanitize_text_field($value)));
}

function wlf_customizer($customize) {
    $customize->add_section('wlf_business', array('title' => 'WLF — İletişim ve marka', 'priority' => 30, 'description' => 'Telefon ve WhatsApp bilgilerini siz ekleyebilirsiniz. Boş bırakılan telefon ve WhatsApp bağlantıları ziyaretçiye gösterilmez.'));
    $fields = array(
        'wlf_sales_email' => array('Satış e-postası', 'sales@wlfagriparts.com', 'email', 'sanitize_email'),
        'wlf_phone' => array('İşletme telefonu (ülke koduyla)', '', 'text', 'wlf_sanitize_phone'),
        'wlf_whatsapp_number' => array('WhatsApp numarası (ülke koduyla)', '', 'text', 'wlf_sanitize_phone'),
        'wlf_whatsapp_enabled' => array('Küçük hareketli WhatsApp düğmesini göster', false, 'checkbox', 'rest_sanitize_boolean'),
        'wlf_footer_blurb' => array('Alt bölüm metni — English', 'Agricultural machinery parts. From Türkiye, for the next harvest.', 'textarea', 'sanitize_textarea_field'),
        'wlf_footer_blurb_es' => array('Alt bölüm metni — Español', 'Repuestos de maquinaria agrícola. Desde Türkiye, para la próxima cosecha.', 'textarea', 'sanitize_textarea_field'),
        'wlf_footer_blurb_ru' => array('Alt bölüm metni — Русский', 'Запчасти для сельхозтехники. Из Турции — к новому урожаю.', 'textarea', 'sanitize_textarea_field'),
    );
    foreach ($fields as $id => $field) {
        $customize->add_setting($id, array('default' => $field[1], 'sanitize_callback' => $field[3], 'capability' => 'edit_theme_options'));
        $customize->add_control($id, array('label' => $field[0], 'section' => 'wlf_business', 'type' => $field[2]));
    }
    foreach (array('wlf_horizontal_logo' => array('Üst menü — yatay logo', 'logo-horizontal.png'), 'wlf_primary_logo' => array('Alt bölüm — birincil logo', 'logo-primary.png'), 'wlf_hero_image' => array('Yeni başlangıç sayfaları için ana görsel', 'hero-farm.jpg')) as $id => $field) {
        $customize->add_setting($id, array('default' => wlf_asset('images/' . $field[1]), 'sanitize_callback' => 'esc_url_raw', 'capability' => 'edit_theme_options'));
        $customize->add_control(new WP_Customize_Image_Control($customize, $id, array('label' => $field[0], 'section' => 'wlf_business')));
    }
}
add_action('customize_register', 'wlf_customizer');
