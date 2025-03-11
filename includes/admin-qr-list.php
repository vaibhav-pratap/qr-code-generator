<?php
if (!defined('ABSPATH')) exit;

function qrg_admin_qr_list_page() {
    $post_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : 'post';
    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $posts_per_page = 12;

    $query_args = [
        'post_type'      => $post_type,
        'posts_per_page' => $posts_per_page,
        'paged'          => $paged,
    ];

    if ($post_type === 'post' || $post_type === 'product') {
        if (!empty($_GET['category'])) {
            $query_args['category_name'] = sanitize_text_field($_GET['category']);
        }
    }

    if (!empty($_GET['date'])) {
        $query_args['date_query'] = [
            [
                'after'     => sanitize_text_field($_GET['date']),
                'inclusive' => true,
            ],
        ];
    }

    $query = new WP_Query($query_args);
    ?>

    <div class="wrap">
        <h1 class="wp-heading-inline">QR Code List</h1>
        <hr class="wp-header-end">

        <div class="qrg-filter-bar">
            <form method="GET">
                <input type="hidden" name="page" value="qrg-admin-qr-list">
                <select name="post_type">
                    <option value="post" <?php selected($post_type, 'post'); ?>>Posts</option>
                    <option value="page" <?php selected($post_type, 'page'); ?>>Pages</option>
                    <option value="product" <?php selected($post_type, 'product'); ?>>Products</option>
                </select>
                <?php if ($post_type !== 'page') : ?>
                    <select name="category">
                        <option value="">All Categories</option>
                        <?php
                        $categories = get_categories(['taxonomy' => ($post_type === 'product' ? 'product_cat' : 'category')]);
                        foreach ($categories as $category) {
                            echo '<option value="' . esc_attr($category->slug) . '" ' . selected($_GET['category'] ?? '', $category->slug, false) . '>' . esc_html($category->name) . '</option>';
                        }
                        ?>
                    </select>
                <?php endif; ?>
                <input type="date" name="date" value="<?php echo esc_attr($_GET['date'] ?? ''); ?>">
                <button type="submit" class="button button-primary">Filter</button>
            </form>

            <div class="qrg-view-switch">
                <a href="?page=qrg-admin-qr-list&post_type=<?php echo $post_type; ?>&view=table"><i class="fas fa-table"></i></a>
                <a href="?page=qrg-admin-qr-list&post_type=<?php echo $post_type; ?>&view=card"><i class="fas fa-th-large"></i></a>
            </div>
        </div>

        <?php if ($_GET['view'] === 'table') : ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>QR Code</th>
                        <th>Download</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($query->have_posts()) : $query->the_post(); ?>
                        <tr>
                            <td><?php the_title(); ?></td>
                            <td><img src="<?php echo esc_url(qrg_generate_qr(get_permalink())); ?>" width="100"></td>
                            <td><a href="<?php echo esc_url(qrg_generate_qr(get_permalink(), true)); ?>" download class="button">Download</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else : ?>
            <div class="qrg-card-grid">
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <div class="qrg-card">
                        <h3 class="qrg-card-title"><?php the_title(); ?></h3>
                        <div class="qrg-card-body">
                            <img src="<?php echo esc_url(qrg_generate_qr(get_permalink())); ?>" class="qrg-qr-image">
                        </div>
                        <div class="qrg-card-footer">
                            <a href="<?php echo esc_url(qrg_generate_qr(get_permalink(), true)); ?>" download class="button button-primary">Download QR</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <div class="qrg-pagination">
            <?php
            echo paginate_links([
                'total'   => $query->max_num_pages,
                'current' => $paged,
                'format'  => '?page=qrg-admin-qr-list&post_type=' . $post_type . '&paged=%#%',
                'prev_text' => __('« Prev'),
                'next_text' => __('Next »'),
            ]);
            ?>
        </div>
    </div>

    <style>
        .qrg-card-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .qrg-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 20px;
        }

        .qrg-card-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .qrg-card-body {
            display: flex;
            justify-content: center;
        }

        .qrg-qr-image {
            width: 200px;
            height: 200px;
        }

        .qrg-card-footer {
            margin-top: 15px;
        }

        .qrg-filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .qrg-view-switch a {
            font-size: 20px;
            margin-left: 10px;
            text-decoration: none;
        }

        .qrg-pagination {
            margin-top: 20px;
            text-align: center;
        }
    </style>
    <?php
    wp_reset_postdata();
}
?>
