<?php
/**
 * Plugin Name: QR Codes Generator for WordPress
 * Plugin URI:  https://github.com/vaibhav-pratap/qr-code-generator
 * Description: Automatically generates QR codes for WooCommerce Products, Orders, WordPress Posts, and Pages.
 * Version:     1.0.1
 * Author:      Vaibhav Singh
 * Author URI:  https://github.com/vaibhav-pratap
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 6.0
 * Tested up to: 6.5
 * Requires PHP: 8.0
 * Text Domain: qr-codes-generator
 */

if (!defined('ABSPATH')) exit;

// Define Plugin Path
define('QRG_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('QRG_PLUGIN_URL', plugin_dir_url(__FILE__));
define('QRG_PLUGIN_VERSION', '1.0.0');

// Include Core Files
include_once QRG_PLUGIN_DIR . 'includes/qr-generator.php';
include_once QRG_PLUGIN_DIR . 'includes/admin-qr-list.php';
include_once QRG_PLUGIN_DIR . 'includes/post-type-qr.php';
include_once QRG_PLUGIN_DIR . 'includes/updater.php';

// Plugin Activation Hook
function qrg_activate() {
    wp_mkdir_p(wp_upload_dir()['basedir'] . '/qr_codes/');
}
register_activation_hook(__FILE__, 'qrg_activate');

// Plugin Deactivation Hook
function qrg_deactivate() {
    // Clean up options if needed
}
register_deactivation_hook(__FILE__, 'qrg_deactivate');
?>
