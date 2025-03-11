<?php
if (!defined('ABSPATH')) exit;

// Shortcode to display QR Code for current post
add_shortcode('qr_code', function() {
    if (!is_singular()) return '';
    return "<img src='" . qrg_generate_qr(get_the_ID(), 200) . "' width='200'>";
});

// Shortcode to display QR Codes for multiple posts
add_shortcode('qr_code_list', function($atts) {
    $atts = shortcode_atts(['post_type' => 'product'], $atts);
    $query = new WP_Query(['post_type' => $atts['post_type'], 'posts_per_page' => 10]);

    $output = '<div style="display: flex; flex-wrap: wrap; gap: 20px;">';
    while ($query->have_posts()) {
        $query->the_post();
        $output .= "<div><img src='" . qrg_generate_qr(get_the_ID(), 200) . "' width='200'><p>" . get_the_title() . "</p></div>";
    }
    wp_reset_postdata();
    
    return $output . '</div>';
});
?>
