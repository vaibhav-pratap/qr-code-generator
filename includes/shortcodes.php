<?php
if (!defined('ABSPATH')) exit;

// Register Shortcodes
add_shortcode('qr_code', 'qrg_shortcode_single');
add_shortcode('qr_code_list', 'qrg_shortcode_list');

// Shortcode for Individual QR Code
function qrg_shortcode_single() {
    if (!is_singular()) return ''; // Only works on single posts, pages, products
    global $post;
    
    $qr_url = qrg_generate_qr($post->ID);
    return "<div style='text-align: center;'>
                <img src='{$qr_url}' width='200' height='200' style='border: 1px solid #ccc; padding: 10px;' />
            </div>";
}

// Shortcode for Listing All QR Codes
function qrg_shortcode_list($atts) {
    $atts = shortcode_atts(['post_type' => 'post'], $atts);
    $post_type = sanitize_text_field($atts['post_type']);

    $query = new WP_Query([
        'post_type'      => $post_type,
        'posts_per_page' => 10, // Change as needed
    ]);

    if (!$query->have_posts()) return '<p>No QR codes found.</p>';

    $output = '<div style="display: flex; flex-wrap: wrap; gap: 20px;">';
    while ($query->have_posts()) {
        $query->the_post();
        $qr_url = qrg_generate_qr(get_the_ID());
        $output .= "<div style='text-align: center;'>
                        <img src='{$qr_url}' width='200' height='200' />
                        <p>" . get_the_title() . "</p>
                    </div>";
    }
    wp_reset_postdata();
    
    return $output . '</div>';
}
?>
