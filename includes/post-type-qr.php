<?php
if (!defined('ABSPATH')) exit;

// Add QR Code Metabox to Products, Posts & Pages
function qrg_add_qr_metabox() {
    $post_types = ['product', 'post', 'page'];
    foreach ($post_types as $post_type) {
        add_meta_box(
            'qrg_qr_code',
            'QR Code',
            'qrg_display_qr_metabox',
            $post_type,
            'side',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'qrg_add_qr_metabox');

// Display QR Code in Metabox
function qrg_display_qr_metabox($post) {
    $permalink = get_permalink($post->ID);
    $qr_url = 'https://quickchart.io/qr?text=' . urlencode($permalink) . '&size=500x500';
    ?>
    <div style="text-align: center;">
        <img src="<?php echo esc_url($qr_url); ?>" width="200" height="200" alt="QR Code">
        <p><a href="<?php echo esc_url($qr_url); ?>" download="qr-<?php echo $post->ID; ?>.png" class="button">Download QR Code</a></p>
    </div>
    <?php
}
?>
