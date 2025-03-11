<?php
if (!defined('ABSPATH')) exit;

add_action('admin_menu', 'qrg_add_admin_page');
function qrg_add_admin_page() {
    add_menu_page(
        'QR Codes',
        'QR Codes',
        'manage_options',
        'qrg-admin',
        'qrg_admin_page',
        QRG_PLUGIN_URL . 'assets/icon.png',
        56
    );
}

function qrg_admin_page() {
    echo '<div class="wrap"><h1>QR Codes Generator</h1>';
    echo '<p>View all QR codes for products, posts, and pages.</p>';
    echo '</div>';
}
?>
