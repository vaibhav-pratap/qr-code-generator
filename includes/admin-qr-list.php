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
    $layout = isset($_GET['layout']) ? sanitize_text_field($_GET['layout']) : 'table'; // Default to table view
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
        <h1 class="qrg-title"><span class="dashicons dashicons-qrcode"></span> QR Codes List</h1>

        <!-- Load FontAwesome for Better Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

        <!-- Filter Form & View Switch -->
        <form method="get" class="qrg-filter-form">
            <input type="hidden" name="page" value="qrg-admin-qr-list">

            <label for="post_type"><i class="fa-solid fa-filter"></i> Filter by Type: </label>
            <select name="post_type" id="post_type" onchange="this.form.submit()">
                <option value="product" <?php selected($post_type, 'product'); ?>>WooCommerce Products</option>
                <option value="post" <?php selected($post_type, 'post'); ?>>Blog Posts</option>
                <option value="page" <?php selected($post_type, 'page'); ?>>Pages</option>
            </select>

            <label for="layout"><i class="fa-solid fa-eye"></i> View Mode: </label>
            <select name="layout" id="layout" onchange="this.form.submit()">
                <option value="table" <?php selected($layout, 'table'); ?>>🔳 Table View</option>
                <option value="card" <?php selected($layout, 'card'); ?>>🃏 Card View</option>
            </select>
        </form>

        <?php if ($layout === 'table') : ?>
            <!-- Table View -->
            <table class="widefat fixed striped qrg-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>QR Code (Click to Enlarge)</th>
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
                            ?>
                            <tr>
                                <td><?php echo $post_id; ?></td>
                                <td><strong><?php the_title(); ?></strong></td>
                                <td>
                                    <a href="<?php echo esc_url($qr_url); ?>" target="_blank">
                                        <img src="<?php echo esc_url($qr_url); ?>" class="qrg-thumbnail" alt="QR Code">
                                    </a>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url($qr_url); ?>" download="qr-<?php echo $post_id; ?>.png" class="button button-primary">Download</a>
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
        <?php else : ?>
            <!-- Card View -->
            <div class="qrg-card-grid">
                <?php
                if ($posts_query->have_posts()) :
                    while ($posts_query->have_posts()) :
                        $posts_query->the_post();
                        $post_id = get_the_ID();
                        $permalink = get_permalink($post_id);
                        $qr_url = 'https://quickchart.io/qr?text=' . urlencode($permalink) . '&size=500x500';
                        ?>
                        <div class="qrg-card">
                            <h3 class="qrg-card-title"><?php the_title(); ?></h3>
                            <a href="<?php echo esc_url($qr_url); ?>" target="_blank">
                                <img src="<?php echo esc_url($qr_url); ?>" class="qrg-card-thumbnail" alt="QR Code">
                            </a>
                            <a href="<?php echo esc_url($qr_url); ?>" download="qr-<?php echo $post_id; ?>.png" class="button button-primary"><i class="fa-solid fa-download"></i> Download</a>
                        </div>
                    <?php endwhile;
                    wp_reset_postdata();
                else : ?>
                    <p>No posts found.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Pagination -->
        <div class="qrg-pagination">
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

    <!-- Custom Styles -->
    <style>
        .qrg-title {
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .qrg-filter-form {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .qrg-table {
            width: 100%;
        }
        .qrg-thumbnail {
            width: 100px;
            height: 100px;
        }
        .qrg-card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .qrg-card {
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .qrg-card-title {
            margin-bottom: 10px;
            font-weight: bold;
        }
        .qrg-card-thumbnail {
            width: 150px;
            height: 150px;
            margin-bottom: 10px;
        }
        .qrg-pagination {
            text-align: center;
            margin-top: 20px;
        }
    </style>

<?php
}
?>
