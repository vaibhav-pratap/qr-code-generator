<?php
if (!defined('ABSPATH')) exit;

add_action('add_meta_boxes', 'qrg_add_metabox');
function qrg_add_metabox() {
    add_meta_box('qrg_qr_code', 'Post QR Code', 'qrg_display_qr', ['post', 'page', 'product'], 'side', 'default');
}

function qrg_display_qr($post) {
    $qr_url = qrg_generate_qr($post->ID);
    $nonce = wp_create_nonce('qrg_download_qr');
    $download_url = admin_url("admin.php?page=qrg-admin&qrg_download={$post->ID}&type=" . get_post_type() . "&nonce={$nonce}");

    echo "<div style='text-align: center;'>
            <img src='{$qr_url}' width='200' height='200' style='border: 1px solid #ccc; padding: 10px;' />
            <p><a href='{$download_url}' class='button'>Download QR Code</a></p>
          </div>";
}
?>
