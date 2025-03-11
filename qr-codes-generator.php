<?php
/**
 * Plugin Name: QR Codes Generator for WordPress
 * Plugin URI:  https://github.com/vaibhav-pratap/qr-code-generator
 * Description: Generates QR codes for WooCommerce Products, Orders, Posts & Pages.
 * Version:     1.0.5
 * Author:      Vaibhav Singh
 * Author URI:  https://github.com/vaibhav-pratap
 * License:     GPL v2 or later
 * Text Domain: qr-codes-generator
 */

if (!defined('ABSPATH')) exit;

define('QRG_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('QRG_PLUGIN_URL', plugin_dir_url(__FILE__));
define('QRG_PLUGIN_VERSION', '1.0.0');

// Include Core Files
include_once QRG_PLUGIN_DIR . 'includes/qr-functions.php';
include_once QRG_PLUGIN_DIR . 'includes/admin-qr-list.php';
include_once QRG_PLUGIN_DIR . 'includes/shortcodes.php';
include_once QRG_PLUGIN_DIR . 'includes/updater.php';

// Activation Hook
function qrg_activate() {
    wp_mkdir_p(wp_upload_dir()['basedir'] . '/qr_codes/');
}
register_activation_hook(__FILE__, 'qrg_activate');

// Deactivation Hook
register_deactivation_hook(__FILE__, function () {});
?>
