<?php
if (!defined('ABSPATH')) exit;

function qrg_generate_qr($id, $type = 'post') {
    if (!is_numeric($id)) return ''; // Security Check

    $link = esc_url(get_permalink($id)); // Get Permalink based on WordPress Settings
    return "https://quickchart.io/qr?text=" . urlencode($link) . "&size=500x500&margin=10";
}

// QR Code Download as PNG
if (isset($_GET['qrg_download']) && isset($_GET['nonce'])) {
    if (!wp_verify_nonce($_GET['nonce'], 'qrg_download_qr')) die('Invalid request');

    $id = intval($_GET['qrg_download']);
    $type = sanitize_text_field($_GET['type'] ?? 'post');
    $qr_url = qrg_generate_qr($id, $type);

    // Fetch QR code image and serve as a PNG download
    $image = file_get_contents($qr_url);
    header('Content-Type: image/png');
    header('Content-Disposition: attachment; filename="QR_{$type}_{$id}.png"');
    echo $image;
    exit;
}
?>
