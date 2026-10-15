<?php if (!defined('ABSPATH')) { exit; } ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#174d36">
<?php if (!has_site_icon()) : ?><link rel="icon" type="image/png" href="<?php echo esc_url(wlf_asset('images/favicon.png')); ?>"><?php endif; ?>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> data-page="<?php echo esc_attr(wlf_current_role()); ?>">
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php echo wlf_text('Skip to content', 'Saltar al contenido', 'Перейти к содержимому'); ?></a>
<div class="topbar"><div class="container topbar-inner"><?php echo wlf_text('FROM STEEL TO SOIL.', 'DEL ACERO A LA TIERRA.', 'ОТ СТАЛИ К ЗЕМЛЕ.'); ?><span class="topbar-location"><?php echo wlf_text('AGRICULTURAL PARTS · TÜRKİYE → WORLDWIDE', 'REPUESTOS AGRÍCOLAS · TÜRKİYE → TODO EL MUNDO', 'ЗАПЧАСТИ ДЛЯ СЕЛЬХОЗТЕХНИКИ · ТУРЦИЯ → ВЕСЬ МИР'); ?></span></div></div>
<header class="header"><div class="container header-inner">
<a href="<?php echo esc_url(wlf_page_url('home')); ?>" class="logo" aria-label="WLF Agri Parts"><img src="<?php echo esc_url(get_theme_mod('wlf_horizontal_logo', wlf_asset('images/logo-horizontal.png'))); ?>" width="2171" height="724" alt="WLF Agri Parts"></a>
<nav class="main-nav" id="main-nav" aria-label="Main navigation" data-label-en="Main navigation" data-label-es="Navegación principal" data-label-ru="Главная навигация">
<?php wp_nav_menu(array('theme_location' => 'primary', 'container' => false, 'fallback_cb' => 'wlf_default_menu', 'depth' => 2)); ?>
</nav>
<div class="header-actions">
<details class="language-picker"><summary id="language-switch" class="language-switch" aria-label="Select language" data-label-en="Select language" data-label-es="Seleccionar idioma" data-label-ru="Выбрать язык">EN</summary><div class="language-options"><button type="button" data-language="en">English</button><button type="button" data-language="es">Español</button><button type="button" data-language="ru">Русский</button></div></details>
<a href="<?php echo esc_url(wlf_page_url('quote')); ?>" class="button button-small"><?php echo wlf_text('Get a quote', 'Pedir presupuesto', 'Получить предложение'); ?><span class="quote-badge" hidden>0</span><span aria-hidden="true">→</span></a>
<button class="mobile-menu-toggle" type="button" aria-controls="main-nav" aria-expanded="false" aria-label="Open menu" data-label-en="Open menu" data-label-es="Abrir menú" data-label-ru="Открыть меню"><svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button>
</div></div></header>
<main id="main" class="wlf-page-content">
