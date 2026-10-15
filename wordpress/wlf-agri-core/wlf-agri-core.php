<?php
/**
 * Plugin Name: WLF Agri Parts Core
 * Description: Fiyatsız B2B ürün kataloğu, üç dilde ürün alanları ve özel teklif kayıtları.
 * Version: 1.0.0
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * Author: WLF Agri Parts
 * Text Domain: wlf-agri-core
 */

defined('ABSPATH') || exit;

define('WLF_CORE_VERSION', '1.0.0');
define('WLF_CORE_DIR', plugin_dir_path(__FILE__));
define('WLF_CORE_URL', plugin_dir_url(__FILE__));

require_once WLF_CORE_DIR . 'includes/products.php';
require_once WLF_CORE_DIR . 'includes/enquiries.php';

/** Business records deliberately survive plugin deactivation and removal. */
register_activation_hook(__FILE__, function () {
    wlf_register_products();
    wlf_register_enquiries();
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, function () {
    flush_rewrite_rules();
});
