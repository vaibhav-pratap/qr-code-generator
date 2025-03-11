<?php
if (!defined('ABSPATH')) exit;

// Generate QR Code URL using QuickChart
function qrg_generate_qr($post_id, $size = 500) {
    $permalink = get_permalink($post_id);
    if (!$permalink) return '';

    return "https://quickchart.io/qr?text=" . urlencode($permalink) . "&size={$size}";
}

// Download QR Code as PNG
function qrg_download_qr($post_id) {
    $qr_url = qrg_generate_qr($post_id, 500);
    $upload_dir = wp_upload_dir();
    $file_path = $upload_dir['basedir'] . "/qr_codes/qr-{$post_id}.png";

    if (!file_exists($file_path)) {
        file_put_contents($file_path, file_get_contents($qr_url));
    }

    return $upload_dir['baseurl'] . "/qr_codes/qr-{$post_id}.png";
}
?>
