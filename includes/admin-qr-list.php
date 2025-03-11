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
    $layout = isset($_GET['layout']) ? sanitize_text_field($_GET['layout']) : 'table'; 
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

    <div class="wrap container">
        <h1 class="text-center my-4"><i class="fa-solid fa-qrcode"></i> QR Codes List</h1>

        <!-- Load Bootstrap & FontAwesome -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

        <!-- Filter Form -->
        <form method="get" class="row g-3 mb-4">
            <input type="hidden" name="page" value="qrg-admin-qr-list">

            <div class="col-md-4">
                <label for="post_type" class="form-label"><i class="fa-solid fa-filter"></i> Filter by Type:</label>
                <select name="post_type" id="post_type" class="form-select" onchange="this.form.submit()">
                    <option value="product" <?php selected($post_type, 'product'); ?>>WooCommerce Products</option>
                    <option value="post" <?php selected($post_type, 'post'); ?>>Blog Posts</option>
                    <option value="page" <?php selected($post_type, 'page'); ?>>Pages</option>
                </select>
            </div>

            <div class="col-md-4">
                <label for="layout" class="form-label"><i class="fa-solid fa-eye"></i> View Mode:</label>
                <select name="layout" id="layout" class="form-select" onchange="this.form.submit()">
                    <option value="table" <?php selected($layout, 'table'); ?>>🔳 Table View</option>
                    <option value="card" <?php selected($layout, 'card'); ?>>🃏 Card View</option>
                </select>
            </div>
        </form>

        <?php if ($layout === 'table') : ?>
            <!-- Table View -->
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>QR Code</th>
                            <th>Download</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($posts_query->have_posts()) : while ($posts_query->have_posts()) : $posts_query->the_post();
                            $post_id = get_the_ID();
                            $permalink = get_permalink($post_id);
                            $qr_url = 'https://quickchart.io/qr?text=' . urlencode($permalink) . '&size=500x500';
                            ?>
                            <tr>
                                <td><?php echo $post_id; ?></td>
                                <td><strong><?php the_title(); ?></strong></td>
                                <td>
                                    <a href="<?php echo esc_url($qr_url); ?>" target="_blank">
                                        <img src="<?php echo esc_url($qr_url); ?>" class="img-thumbnail" width="100" height="100">
                                    </a>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url($qr_url); ?>" download="qr-<?php echo $post_id; ?>.png" class="btn btn-primary">
                                        <i class="fa-solid fa-download"></i> Download
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; wp_reset_postdata(); else : ?>
                            <tr><td colspan="4">No posts found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <!-- Card View -->
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php if ($posts_query->have_posts()) : while ($posts_query->have_posts()) : $posts_query->the_post();
                    $post_id = get_the_ID();
                    $permalink = get_permalink($post_id);
                    $qr_url = 'https://quickchart.io/qr?text=' . urlencode($permalink) . '&size=500x500';
                    ?>
                    <div class="col">
                        <div class="card text-center shadow-sm h-100">
                            <div class="card-header fw-bold"><?php the_title(); ?></div>
                            <div class="card-body">
                                <a href="<?php echo esc_url($qr_url); ?>" target="_blank">
                                    <img src="<?php echo esc_url($qr_url); ?>" class="img-fluid rounded" width="200" height="200">
                                </a>
                            </div>
                            <div class="card-footer">
                                <a href="<?php echo esc_url($qr_url); ?>" download="qr-<?php echo $post_id; ?>.png" class="btn btn-primary w-100">
                                    <i class="fa-solid fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; wp_reset_postdata(); else : ?>
                    <p>No posts found.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Pagination -->
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
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
            </ul>
        </nav>
    </div>

<?php
}
?>
