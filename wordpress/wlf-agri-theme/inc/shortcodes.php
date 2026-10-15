<?php
if (!defined('ABSPATH')) { exit; }

function wlf_shortcode_template($name) {
    ob_start();
    include get_template_directory() . '/inc/partials/' . $name . '.php';
    return ob_get_clean();
}

add_shortcode('wlf_catalogue', function () { return wlf_shortcode_template('catalogue'); });
add_shortcode('wlf_enquiry', function () { return wlf_shortcode_template('enquiry'); });
add_shortcode('wlf_featured', function () { return '<div class="products-grid featured-products" id="featured-products"></div>'; });

add_shortcode('wlf_home_search', function () {
    $out = '<form class="home-search" action="' . esc_url(wlf_page_url('products')) . '" method="get" role="search"><label for="home-search">' . wlf_text('Already know your part?', '¿Ya sabe qué pieza necesita?', 'Знаете, какая деталь нужна?') . '</label><div class="search-input-wrap"><span aria-hidden="true">⌕</span>';
    $out .= '<input id="home-search" name="q" type="search" maxlength="120" placeholder="Part name, OEM number or WLF code…" data-placeholder-en="Part name, OEM number or WLF code…" data-placeholder-es="Nombre de pieza, número OEM o código WLF…" data-placeholder-ru="Название детали, номер OEM или код WLF…" aria-label="Search parts" data-label-en="Search parts" data-label-es="Buscar repuestos" data-label-ru="Поиск запчастей">';
    $out .= '<button class="button" type="submit">' . wlf_text('Find a part', 'Buscar pieza', 'Найти деталь') . '<span aria-hidden="true">→</span></button></div></form>';
    return $out;
});

add_shortcode('wlf_contact_details', function () {
    $email = wlf_sales_email();
    $out = '<div class="contact-grid"><article class="contact-card"><span class="contact-icon" aria-hidden="true">@</span>';
    $out .= wlf_text('Sales & export enquiries', 'Consultas comerciales y de exportación', 'Запросы по продажам и экспорту', 'h2');
    $out .= '<a href="' . esc_url('mailto:' . $email) . '">' . esc_html($email) . ' →</a></article>';
    $phone = sanitize_text_field(get_theme_mod('wlf_phone', ''));
    $digits = wlf_phone_digits($phone);
    if ($digits) {
        $out .= '<article class="contact-card" id="contact-phone-card"><span class="contact-icon" aria-hidden="true">↗</span>' . wlf_text('Call us', 'Llámenos', 'Позвоните нам', 'h2');
        $out .= '<a id="company-phone" href="' . esc_url('tel:+' . $digits) . '">' . esc_html($phone) . '</a></article>';
    }
    $out .= '</div>';
    return $out;
});
