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
    $per_page = 12;
    $offset = ($paged - 1) * $per_page;
    $date_filter = isset($_GET['date_filter']) ? sanitize_text_field($_GET['date_filter']) : '';
    $category_filter = isset($_GET['category_filter']) ? sanitize_text_field($_GET['category_filter']) : '';

    // Fetch Posts
    $query_args = [
        'post_type'      => $post_type,
        'posts_per_page' => $per_page,
        'paged'          => $paged,
    ];

    if (!empty($date_filter)) {
        $query_args['date_query'] = [['after' => $date_filter]];
    }

    if (!empty($category_filter)) {
        $query_args['category_name'] = $category_filter;
    }

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
            <div class="col-md-3">
                <label for="post_type" class="form-label"><i class="fa-solid fa-filter"></i> Post Type:</label>
                <select name="post_type" id="post_type" class="form-select" onchange="this.form.submit()">
                    <option value="product" <?php selected($post_type, 'product'); ?>>WooCommerce Products</option>
                    <option value="post" <?php selected($post_type, 'post'); ?>>Blog Posts</option>
                    <option value="page" <?php selected($post_type, 'page'); ?>>Pages</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="date_filter" class="form-label"><i class="fa-solid fa-calendar"></i> Date Filter:</label>
                <input type="date" name="date_filter" id="date_filter" class="form-control" value="<?php echo esc_attr($date_filter); ?>" onchange="this.form.submit()">
            </div>
            <div class="col-md-3">
                <label for="category_filter" class="form-label"><i class="fa-solid fa-folder"></i> Category:</label>
                <select name="category_filter" id="category_filter" class="form-select" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php
                    $categories = get_categories();
                    foreach ($categories as $category) {
                        echo '<option value="' . esc_attr($category->slug) . '" ' . selected($category_filter, $category->slug, false) . '>' . esc_html($category->name) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="layout" class="form-label"><i class="fa-solid fa-eye"></i> View Mode:</label>
                <select name="layout" id="layout" class="form-select" onchange="this.form.submit()">
                    <option value="table" <?php selected($layout, 'table'); ?>>🔳 Table View</option>
                    <option value="card" <?php selected($layout, 'card'); ?>>🃏 Card View</option>
                </select>
            </div>
        </form>

        <div class="row row-cols-1 row-cols-md-4 g-4">
            <?php if ($posts_query->have_posts()) : while ($posts_query->have_posts()) : $posts_query->the_post();
                $post_id = get_the_ID();
                $permalink = get_permalink($post_id);
                $qr_url = 'https://quickchart.io/qr?text=' . urlencode($permalink) . '&size=500x500';
                ?>
                <div class="col">
                    <div class="card text-center shadow-sm h-100 border">
                        <div class="card-header fw-bold bg-white"> <?php the_title(); ?> </div>
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <a href="<?php echo esc_url($qr_url); ?>" target="_blank">
                                <img src="<?php echo esc_url($qr_url); ?>" class="img-fluid rounded" width="200" height="200">
                            </a>
                        </div>
                        <div class="card-footer bg-white">
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
