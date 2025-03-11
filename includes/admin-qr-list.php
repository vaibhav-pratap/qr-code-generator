<?php
if (!defined('ABSPATH')) exit;

// Add QR Code List to Admin Menu
function qrg_admin_menu() {
    add_menu_page(
        'QR Codes List',
        'QR Codes',
        'manage_options',
        'qrg-admin-qr-list',
        'qrg_admin_qr_list_page',
        'dashicons-qrcode',
        25
    );
}
add_action('admin_menu', 'qrg_admin_menu');

// QR Code List Page
function qrg_admin_qr_list_page() {
    global $wpdb;

    $post_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : 'product';
    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $per_page = 10;
    $offset = ($paged - 1) * $per_page;

    // Fetch Posts
    $query_args = [
        'post_type'      => $post_type,
        'posts_per_page' => $per_page,
        'paged'          => $paged,
    ];
    $posts_query = new WP_Query($query_args);
    $total_pages = $posts_query->max_num_pages;

    ?>
    <div class="wrap">
        <h1>QR Codes List</h1>

        <!-- Filter Form -->
        <form method="get">
            <input type="hidden" name="page" value="qrg-admin-qr-list">
            <label for="post_type">Filter by Post Type: </label>
            <select name="post_type" id="post_type">
                <option value="product" <?php selected($post_type, 'product'); ?>>WooCommerce Products</option>
                <option value="post" <?php selected($post_type, 'post'); ?>>Blog Posts</option>
                <option value="page" <?php selected($post_type, 'page'); ?>>Pages</option>
            </select>
            <button type="submit" class="button button-primary">Filter</button>
        </form>

        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th>Post ID</th>
                    <th>Title</th>
                    <th>QR Code (Thumbnail)</th>
                    <th>Download</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($posts_query->have_posts()) :
                    while ($posts_query->have_posts()) :
                        $posts_query->the_post();
                        $post_id = get_the_ID();
                        $permalink = get_permalink($post_id);
                        $qr_url = 'https://quickchart.io/qr?text=' . urlencode($permalink) . '&size=500x500';
                        $qr_thumb = 'https://quickchart.io/qr?text=' . urlencode($permalink) . '&size=200x200';
                        ?>
                        <tr>
                            <td><?php echo $post_id; ?></td>
                            <td><?php the_title(); ?></td>
                            <td><img src="<?php echo esc_url($qr_thumb); ?>" width="100" height="100" alt="QR Code"></td>
                            <td>
                                <a href="<?php echo esc_url($qr_url); ?>" download="qr-<?php echo $post_id; ?>.png" class="button button-secondary">Download</a>
                            </td>
                        </tr>
                    <?php endwhile;
                    wp_reset_postdata();
                else : ?>
                    <tr>
                        <td colspan="4">No posts found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <?php
                if ($total_pages > 1) {
                    $pagination_args = [
                        'base'      => add_query_arg('paged', '%#%'),
                        'format'    => '',
                        'current'   => $paged,
                        'total'     => $total_pages,
                        'prev_text' => '« Prev',
                        'next_text' => 'Next »',
                    ];
                    echo paginate_links($pagination_args);
                }
                ?>
            </div>
        </div>
    </div>
    <?php
}
?>
