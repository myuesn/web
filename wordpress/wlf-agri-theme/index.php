<?php
if (!defined('ABSPATH')) { exit; }
get_header();
if (have_posts()) {
    while (have_posts()) {
        the_post();
        echo '<article class="container section">';
        if (!is_page()) { echo '<h1 class="wp-block-heading">' . esc_html(get_the_title()) . '</h1>'; }
        the_content();
        echo '</article>';
    }
    the_posts_navigation();
} else {
    echo '<section class="container wlf-no-content"><h1>' . esc_html(get_bloginfo('name')) . '</h1>';
    echo wlf_text('Your parts conversation starts here.', 'Su solicitud de repuestos empieza aquí.', 'Ваш запрос на запчасти начинается здесь.', 'p');
    if (current_user_can('edit_theme_options')) { echo '<p><a href="' . esc_url(admin_url('themes.php?page=wlf-getting-started')) . '">WLF başlangıç sayfalarını hazırlayın →</a></p>'; }
    echo '</section>';
}
get_footer();
