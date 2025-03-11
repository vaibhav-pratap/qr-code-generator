<?php
if (!defined('ABSPATH')) exit;

// Add Admin Menu for QR Codes
function qrg_admin_menu() {
    add_menu_page(
        'QR Codes List',
        'QR Codes',
        'manage_options',
        'qrg-admin-qr-list',
        'qrg_admin_qr_list_page',
        'dashicons-admin-generic', // Temporary icon, replaced by FontAwesome via CSS
        20
    );
}

add_action('admin_menu', 'qrg_admin_menu');


function qrg_admin_enqueue_styles() {
    wp_enqueue_style('qrg-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css');
    wp_add_inline_style('qrg-fontawesome', '
        #adminmenu .toplevel_page_qrg-admin-qr-list div.wp-menu-image:before {
            font-family: "Font Awesome 6 Free"; 
            content: "\f029"; /* FontAwesome QR Code Unicode */
            font-weight: 900;
        }
    ');
}
add_action('admin_enqueue_scripts', 'qrg_admin_enqueue_styles');

// Enqueue Bootstrap and FontAwesome
function qrg_admin_enqueue_assets($hook) {
    if (strpos($hook, 'qrg-qr-codes') === false) return;

    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true);
    wp_enqueue_style('fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css');
}
add_action('admin_enqueue_scripts', 'qrg_admin_enqueue_assets');

// Get Filters
$post_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : 'product';
$category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
$date_filter = isset($_GET['date_filter']) ? sanitize_text_field($_GET['date_filter']) : '';

// Pagination
$items_per_page = 12;
$paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$offset = ($paged - 1) * $items_per_page;

// Query Args
$args = [
    'post_type' => $post_type,
    'posts_per_page' => $items_per_page,
    'offset' => $offset,
];

if ($post_type == 'post' && $category) {
    $args['category_name'] = $category;
}
if ($date_filter) {
    $args['date_query'] = [['after' => $date_filter]];
}

$query = new WP_Query($args);
$total_posts = $query->found_posts;
$total_pages = ceil($total_posts / $items_per_page);
?>

<div class="wrap">
    <h1 class="text-center">QR Codes List</h1>

    <!-- Filters -->
    <form method="GET" class="mb-4 d-flex gap-3 align-items-end">
        <input type="hidden" name="page" value="qrg-qr-codes">
        
        <select name="post_type" class="form-select">
            <option value="product" <?php selected($post_type, 'product'); ?>>WooCommerce Products</option>
            <option value="post" <?php selected($post_type, 'post'); ?>>Blog Posts</option>
            <option value="page" <?php selected($post_type, 'page'); ?>>Pages</option>
        </select>

        <?php if ($post_type == 'post') : ?>
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                <?php foreach (get_categories() as $cat) : ?>
                    <option value="<?php echo esc_attr($cat->slug); ?>" <?php selected($category, $cat->slug); ?>><?php echo esc_html($cat->name); ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>

        <input type="date" name="date_filter" value="<?php echo esc_attr($date_filter); ?>" class="form-control">
        <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Apply Filters</button>
    </form>

    <!-- View Mode Switch -->
    <div class="d-flex justify-content-end mb-3">
        <a href="?page=qrg-qr-codes&view=grid" class="btn btn-outline-primary"><i class="fa fa-th-large"></i></a>
        <a href="?page=qrg-qr-codes&view=list" class="btn btn-outline-secondary ms-2"><i class="fa fa-list"></i></a>
    </div>

    <!-- QR Code Display -->
    <div class="row">
        <?php if ($query->have_posts()) : ?>
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <?php
                $post_id = get_the_ID();
                $post_title = get_the_title();
                $post_url = get_permalink($post_id);
                $qr_url = 'https://quickchart.io/qr?size=500x500&text=' . urlencode($post_url);
                $qr_thumb_url = 'https://quickchart.io/qr?size=200x200&text=' . urlencode($post_url);
                ?>
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm text-center">
                        <h5 class="card-header bg-white"><?php echo esc_html($post_title); ?></h5>
                        <div class="card-body">
                            <img src="<?php echo esc_url($qr_thumb_url); ?>" alt="QR Code" class="img-fluid">
                        </div>
                        <div class="card-footer bg-white">
                            <a href="<?php echo esc_url($qr_url); ?>" download="QR-<?php echo esc_attr($post_id); ?>.png" class="btn btn-success w-100"><i class="fa fa-download"></i> Download QR</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <p class="text-center">No items found.</p>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php if ($paged > 1) : ?>
                <li class="page-item"><a class="page-link" href="?page=qrg-qr-codes&paged=<?php echo ($paged - 1); ?>"><i class="fa fa-chevron-left"></i></a></li>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                <li class="page-item <?php echo ($paged == $i) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=qrg-qr-codes&paged=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <?php if ($paged < $total_pages) : ?>
                <li class="page-item"><a class="page-link" href="?page=qrg-qr-codes&paged=<?php echo ($paged + 1); ?>"><i class="fa fa-chevron-right"></i></a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<?php wp_reset_postdata(); ?>

