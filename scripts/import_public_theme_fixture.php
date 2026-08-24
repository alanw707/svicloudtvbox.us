<?php
/**
 * Imports a public WordPress/WooCommerce fixture supplied as JSON by
 * sync_public_theme_fixture.py. Run only through WP-CLI.
 */

if (!defined('ABSPATH') || !defined('WP_CLI')) {
    exit(1);
}

$fixture_path = $args[count($args) - 1] ?? '';
if (!is_string($fixture_path) || !is_readable($fixture_path)) {
    WP_CLI::error('Pass the fixture JSON path as the final WP-CLI argument.');
}

$fixture = json_decode((string) file_get_contents($fixture_path), true);
if (!is_array($fixture)) {
    WP_CLI::error('Fixture JSON is invalid.');
}

$svic_fixture_errors = array();

function svic_fixture_record_error(string $message): void {
    global $svic_fixture_errors;
    $svic_fixture_errors[] = $message;
    WP_CLI::warning($message);
}

function svic_fixture_fail_if_incomplete(): void {
    global $svic_fixture_errors;
    if (!$svic_fixture_errors) {
        return;
    }
    WP_CLI::error('Fixture import incomplete:\n- ' . implode("\n- ", $svic_fixture_errors));
}

$source_url = rtrim((string) ($fixture['source_url'] ?? ''), '/');
$local_url = rtrim((string) ($fixture['local_url'] ?? ''), '/');
if ($source_url === '' || $local_url === '') {
    WP_CLI::error('Fixture must contain source_url and local_url.');
}

function svic_fixture_replace_urls(string $value, string $source_url, string $local_url): string {
    return str_replace($source_url, $local_url, $value);
}

function svic_fixture_is_managed_post(int $post_id): bool {
    return get_post_meta($post_id, '_svic_source_fixture_id', true) !== ''
        || get_post_meta($post_id, '_svic_local_fixture_key', true) !== '';
}

function svic_fixture_term_has_preserved_objects(int $term_id, string $taxonomy): bool {
    $object_ids = get_objects_in_term($term_id, $taxonomy);
    if (is_wp_error($object_ids)) {
        return false;
    }
    foreach ($object_ids as $object_id) {
        if (get_post((int) $object_id) instanceof WP_Post) {
            return true;
        }
    }
    return false;
}

function svic_fixture_delete_content(): void {
    $preserved_attachment_ids = array_filter(array(
        (int) get_theme_mod('custom_logo'),
        (int) get_option('site_icon'),
    ));
    $post_types = array('page', 'post', 'product', 'product_variation', 'attachment', 'wp_navigation', 'nav_menu_item');
    foreach ($post_types as $post_type) {
        $ids = get_posts(array('post_type' => $post_type, 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids'));
        foreach ($ids as $id) {
            $id = (int) $id;
            if ($post_type === 'attachment' && in_array($id, $preserved_attachment_ids, true)) {
                continue;
            }

            $managed = svic_fixture_is_managed_post($id);
            $status = (string) get_post_status($id);
            $delete = $managed || in_array($status, array('publish', 'inherit'), true);

            if ($post_type === 'product_variation') {
                $parent_id = (int) wp_get_post_parent_id($id);
                $parent_status = $parent_id ? (string) get_post_status($parent_id) : '';
                $delete = $managed || $parent_id === 0 || $parent_status === '' || in_array($parent_status, array('publish', 'inherit'), true);
            } elseif ($post_type === 'attachment') {
                $parent_id = (int) wp_get_post_parent_id($id);
                $parent_status = $parent_id ? (string) get_post_status($parent_id) : '';
                // Unmanaged, unattached media may belong to private local work.
                $delete = $managed || ($parent_id > 0 && in_array($parent_status, array('publish', 'inherit'), true));
            }

            if ($delete) {
                wp_delete_post($id, true);
            }
        }
    }

    foreach (wp_get_nav_menus() as $menu) {
        wp_delete_nav_menu((int) $menu->term_id);
    }

    $taxonomies = array('category', 'post_tag', 'product_cat', 'product_tag');
    if (function_exists('wc_get_attribute_taxonomies')) {
        foreach (wc_get_attribute_taxonomies() as $attribute) {
            $taxonomies[] = wc_attribute_taxonomy_name((string) $attribute->attribute_name);
        }
    }
    foreach ($taxonomies as $taxonomy) {
        if (!taxonomy_exists($taxonomy)) {
            continue;
        }
        $terms = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => false));
        foreach ($terms as $term) {
            if (($taxonomy === 'category' && (int) $term->term_id === (int) get_option('default_category'))
                || svic_fixture_term_has_preserved_objects((int) $term->term_id, $taxonomy)) {
                continue;
            }
            wp_delete_term((int) $term->term_id, $taxonomy);
        }
    }
    if (function_exists('wc_get_attribute_taxonomies') && function_exists('wc_delete_attribute')) {
        foreach (wc_get_attribute_taxonomies() as $attribute) {
            $taxonomy = wc_attribute_taxonomy_name((string) $attribute->attribute_name);
            $terms = taxonomy_exists($taxonomy) ? get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => false)) : array();
            $used_by_preserved_content = false;
            foreach ($terms as $term) {
                if (svic_fixture_term_has_preserved_objects((int) $term->term_id, $taxonomy)) {
                    $used_by_preserved_content = true;
                    break;
                }
            }
            if (!$used_by_preserved_content) {
                wc_delete_attribute((int) $attribute->attribute_id);
            }
        }
    }
}

function svic_fixture_import_terms(array $terms, string $taxonomy, array &$maps): void {
    if (!taxonomy_exists($taxonomy)) {
        return;
    }
    usort($terms, static fn(array $a, array $b): int => ((int) ($a['parent'] ?? 0)) <=> ((int) ($b['parent'] ?? 0)));
    foreach ($terms as $term) {
        $source_id = (int) ($term['id'] ?? 0);
        if ($source_id <= 0) {
            svic_fixture_record_error("{$taxonomy} term is missing a source ID");
            continue;
        }
        $parent = $maps['terms'][$taxonomy][(int) ($term['parent'] ?? 0)] ?? 0;
        $created = wp_insert_term((string) ($term['name'] ?? ''), $taxonomy, array(
            'slug' => sanitize_title((string) ($term['slug'] ?? '')),
            'description' => (string) ($term['description'] ?? ''),
            'parent' => $parent,
        ));
        if (is_wp_error($created)) {
            if ($created->get_error_code() === 'term_exists') {
                $existing_id = (int) $created->get_error_data('term_exists');
                if ($existing_id > 0) {
                    $maps['terms'][$taxonomy][$source_id] = $existing_id;
                    $maps['counts']['terms'][$taxonomy] = (int) ($maps['counts']['terms'][$taxonomy] ?? 0) + 1;
                    continue;
                }
            }
            svic_fixture_record_error("Could not create {$taxonomy} term {$source_id}: " . $created->get_error_message());
            continue;
        }
        $maps['terms'][$taxonomy][$source_id] = (int) $created['term_id'];
        $maps['counts']['terms'][$taxonomy] = (int) ($maps['counts']['terms'][$taxonomy] ?? 0) + 1;
    }
}

function svic_fixture_import_media(array $media, string $source_url, string $local_url, array &$maps): void {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    foreach ($media as $item) {
        $remote_url = (string) ($item['source_url'] ?? '');
        $source_id = (int) ($item['id'] ?? 0);
        if ($remote_url === '' || $source_id <= 0) {
            svic_fixture_record_error("Media item is missing a source URL or ID");
            continue;
        }
        $attachment_id = media_sideload_image($remote_url, 0, (string) ($item['title'] ?? ''), 'id');
        if (is_wp_error($attachment_id)) {
            svic_fixture_record_error("Could not import media {$source_id}: " . $attachment_id->get_error_message());
            continue;
        }
        wp_update_post(array(
            'ID' => (int) $attachment_id,
            'post_title' => (string) ($item['title'] ?? ''),
            'post_excerpt' => svic_fixture_replace_urls((string) ($item['caption'] ?? ''), $source_url, $local_url),
            'post_content' => svic_fixture_replace_urls((string) ($item['description'] ?? ''), $source_url, $local_url),
        ));
        update_post_meta((int) $attachment_id, '_wp_attachment_image_alt', (string) ($item['alt_text'] ?? ''));
        update_post_meta((int) $attachment_id, '_svic_source_fixture_id', $source_id);
        update_post_meta((int) $attachment_id, '_svic_source_fixture_url', $remote_url);
        $maps['media'][$source_id] = (int) $attachment_id;
        $maps['counts']['media'] = (int) ($maps['counts']['media'] ?? 0) + 1;
        $maps['media_by_source_url'][$remote_url] = (int) $attachment_id;
        $maps['urls'][$remote_url] = (string) wp_get_attachment_url((int) $attachment_id);
    }
}

function svic_fixture_restore_brand_logo(array $media, array $maps): void {
    $current_logo_id = (int) get_theme_mod('custom_logo');
    if ($current_logo_id && wp_get_attachment_url($current_logo_id)) {
        return;
    }
    foreach ($media as $item) {
        $filename = wp_basename((string) parse_url((string) ($item['source_url'] ?? ''), PHP_URL_PATH));
        if ($filename !== 'cropped-logo-white-text.png') {
            continue;
        }
        $attachment_id = $maps['media'][(int) ($item['id'] ?? 0)] ?? 0;
        if ($attachment_id) {
            set_theme_mod('custom_logo', $attachment_id);
        }
        return;
    }
}

function svic_fixture_import_posts(array $posts, string $post_type, string $source_url, string $local_url, array &$maps): void {
    foreach ($posts as $item) {
        $source_id = (int) ($item['id'] ?? 0);
        if ($source_id <= 0) {
            svic_fixture_record_error("{$post_type} is missing a source ID");
            continue;
        }
        $post_id = wp_insert_post(array(
            'post_type' => $post_type,
            'post_status' => 'publish',
            'post_title' => (string) ($item['title'] ?? ''),
            'post_name' => sanitize_title((string) ($item['slug'] ?? '')),
            'post_content' => svic_fixture_replace_urls((string) ($item['content'] ?? ''), $source_url, $local_url),
            'post_excerpt' => svic_fixture_replace_urls((string) ($item['excerpt'] ?? ''), $source_url, $local_url),
            'post_date' => (string) ($item['date'] ?? current_time('mysql')),
            'menu_order' => (int) ($item['menu_order'] ?? 0),
        ), true);
        if (is_wp_error($post_id)) {
            svic_fixture_record_error("Could not import {$post_type} {$source_id}: " . $post_id->get_error_message());
            continue;
        }
        $maps['posts'][$source_id] = (int) $post_id;
        $maps['counts'][$post_type] = (int) ($maps['counts'][$post_type] ?? 0) + 1;
        update_post_meta((int) $post_id, '_svic_source_fixture_id', $source_id);
        if (!empty($item['featured_media'])) {
            if (isset($maps['media'][(int) $item['featured_media']])) {
                set_post_thumbnail((int) $post_id, $maps['media'][(int) $item['featured_media']]);
            } else {
                svic_fixture_record_error("Missing featured media " . (int) $item['featured_media'] . " for {$post_type} {$source_id}");
            }
        }
        foreach ((array) ($item['terms'] ?? array()) as $taxonomy => $source_terms) {
            if (!taxonomy_exists((string) $taxonomy)) {
                continue;
            }
            $term_ids = array_filter(array_map(static fn($id): int => (int) ($maps['terms'][$taxonomy][(int) $id] ?? 0), (array) $source_terms));
            wp_set_object_terms((int) $post_id, $term_ids, (string) $taxonomy, false);
        }
    }

    foreach ($posts as $item) {
        $post_id = $maps['posts'][(int) ($item['id'] ?? 0)] ?? 0;
        $parent_id = $maps['posts'][(int) ($item['parent'] ?? 0)] ?? 0;
        if ($post_id && $parent_id) {
            wp_update_post(array('ID' => $post_id, 'post_parent' => $parent_id));
        }
    }
}

function svic_fixture_image_id(array $image, array $maps): int {
    $source_id = (int) ($image['id'] ?? 0);
    if ($source_id && isset($maps['media'][$source_id])) {
        return (int) $maps['media'][$source_id];
    }
    $source_url = (string) ($image['src'] ?? '');
    return (int) ($maps['media_by_source_url'][$source_url] ?? 0);
}

function svic_fixture_import_woocommerce_pages(array $pages, array $maps): void {
    $options = array(
        'shop'       => 'woocommerce_shop_page_id',
        'cart'       => 'woocommerce_cart_page_id',
        'checkout'   => 'woocommerce_checkout_page_id',
        'my-account' => 'woocommerce_myaccount_page_id',
    );
    foreach ($pages as $page) {
        $option = $options[(string) ($page['slug'] ?? '')] ?? '';
        $page_id = $maps['posts'][(int) ($page['id'] ?? 0)] ?? 0;
        if ($option !== '' && $page_id) {
            update_option($option, $page_id);
        }
    }
}

function svic_fixture_import_display_settings(array $settings, array $maps): void {
    foreach (array('blogname', 'blogdescription') as $key) {
        if (array_key_exists($key, $settings)) {
            update_option($key, (string) $settings[$key]);
        }
    }
    if (($settings['show_on_front'] ?? '') === 'page') {
        $front_page = $maps['posts'][(int) ($settings['page_on_front'] ?? 0)] ?? 0;
        if ($front_page) {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $front_page);
        }
    }
    $posts_page = $maps['posts'][(int) ($settings['page_for_posts'] ?? 0)] ?? 0;
    if ($posts_page) {
        update_option('page_for_posts', $posts_page);
    }
    $site_icon = $maps['media'][(int) ($settings['site_icon'] ?? 0)] ?? 0;
    if ($site_icon) {
        update_option('site_icon', $site_icon);
    }
}

function svic_fixture_import_global_attributes(array $attributes, array $attribute_terms, array &$maps): void {
    if (!function_exists('wc_create_attribute')) {
        WP_CLI::error('WooCommerce is not active locally.');
    }
    foreach ($attributes as $attribute) {
        $source_id = (int) ($attribute['id'] ?? 0);
        $slug = sanitize_title((string) ($attribute['slug'] ?? $attribute['name'] ?? ''));
        if (!$source_id || $slug === '') {
            svic_fixture_record_error('Product attribute is missing a source ID or slug');
            continue;
        }
        $existing_attributes = function_exists('wc_get_attribute_taxonomy_ids') ? wc_get_attribute_taxonomy_ids() : array();
        $local_id = (int) ($existing_attributes[$slug] ?? 0);
        if ($local_id === 0) {
            $local_id = wc_create_attribute(array(
                'name' => (string) ($attribute['name'] ?? ''),
                'slug' => $slug,
                'type' => (string) ($attribute['type'] ?? 'select'),
                'order_by' => (string) ($attribute['order_by'] ?? 'menu_order'),
                'has_archives' => (bool) ($attribute['has_archives'] ?? false),
            ));
            if (is_wp_error($local_id)) {
                svic_fixture_record_error("Could not create product attribute {$source_id}: " . $local_id->get_error_message());
                continue;
            }
        }
        $taxonomy = wc_attribute_taxonomy_name($slug);
        if (!taxonomy_exists($taxonomy)) {
            register_taxonomy($taxonomy, array('product'), array('hierarchical' => false, 'public' => false));
        }
        $maps['attributes'][$source_id] = array('id' => (int) $local_id, 'taxonomy' => $taxonomy, 'options' => array());
        $maps['counts']['product_attributes'] = (int) ($maps['counts']['product_attributes'] ?? 0) + 1;
        foreach ((array) ($attribute_terms[(string) $source_id] ?? array()) as $term) {
            $created = wp_insert_term((string) ($term['name'] ?? ''), $taxonomy, array('slug' => sanitize_title((string) ($term['slug'] ?? ''))));
            if (is_wp_error($created) && $created->get_error_code() === 'term_exists') {
                $existing_id = (int) $created->get_error_data('term_exists');
                if ($existing_id > 0) {
                    $maps['attributes'][$source_id]['options'][(string) ($term['name'] ?? '')] = $existing_id;
                }
            } elseif (!is_wp_error($created)) {
                $maps['attributes'][$source_id]['options'][(string) ($term['name'] ?? '')] = (int) $created['term_id'];
            } else {
                svic_fixture_record_error("Could not create product attribute term for {$source_id}: " . $created->get_error_message());
            }
        }
    }
}

function svic_fixture_import_products(array $products, array $variations, string $source_url, string $local_url, array &$maps): void {
    if (!function_exists('wc_get_product_object')) {
        WP_CLI::error('WooCommerce is not active locally.');
    }
    foreach ($products as $item) {
        $source_id = (int) ($item['id'] ?? 0);
        if ($source_id <= 0) {
            svic_fixture_record_error('Product is missing a source ID');
            continue;
        }
        $type = in_array((string) ($item['type'] ?? 'simple'), array('simple', 'variable', 'grouped', 'external'), true) ? (string) $item['type'] : 'simple';
        $product = wc_get_product_object($type);
        if (!$product instanceof WC_Product) {
            svic_fixture_record_error("Could not initialize product {$source_id}");
            continue;
        }
        $product->set_name((string) ($item['name'] ?? ''));
        $product->set_slug(sanitize_title((string) ($item['slug'] ?? '')));
        $product->set_status('publish');
        $product->set_catalog_visibility((string) ($item['catalog_visibility'] ?? 'visible'));
        $product->set_description(svic_fixture_replace_urls((string) ($item['description'] ?? ''), $source_url, $local_url));
        $product->set_short_description(svic_fixture_replace_urls((string) ($item['short_description'] ?? ''), $source_url, $local_url));
        $product->set_sku((string) ($item['sku'] ?? ''));
        $product->set_regular_price((string) ($item['regular_price'] ?? ''));
        $product->set_sale_price((string) ($item['sale_price'] ?? ''));
        $product->set_virtual((bool) ($item['virtual'] ?? false));
        $product->set_featured((bool) ($item['featured'] ?? false));
        $product->set_menu_order((int) ($item['menu_order'] ?? 0));
        $product->set_reviews_allowed((bool) ($item['reviews_allowed'] ?? false));
        $category_ids = array_filter(array_map(static fn($id): int => (int) ($maps['terms']['product_cat'][(int) $id] ?? 0), (array) ($item['categories'] ?? array())));
        $tag_ids = array_filter(array_map(static fn($id): int => (int) ($maps['terms']['product_tag'][(int) $id] ?? 0), (array) ($item['tags'] ?? array())));
        $product->set_category_ids($category_ids);
        $product->set_tag_ids($tag_ids);
        $images = (array) ($item['images'] ?? array());
        if ($images) {
            $image_id = svic_fixture_image_id((array) $images[0], $maps);
            if ($image_id) {
                $product->set_image_id($image_id);
            }
            $gallery_ids = array();
            foreach (array_slice($images, 1) as $image) {
                $image_id = svic_fixture_image_id((array) $image, $maps);
                if (!$image_id) {
                    svic_fixture_record_error("Missing product image for product {$source_id}");
                    continue;
                }
                $gallery_ids[] = $image_id;
            }
            $product->set_gallery_image_ids($gallery_ids);
        }
        $attributes = array();
        foreach ((array) ($item['attributes'] ?? array()) as $attribute_data) {
            $attribute = new WC_Product_Attribute();
            $source_attribute_id = (int) ($attribute_data['id'] ?? 0);
            $global_attribute = $maps['attributes'][$source_attribute_id] ?? null;
            $attribute->set_id((int) ($global_attribute['id'] ?? 0));
            $attribute->set_name((string) ($global_attribute['taxonomy'] ?? $attribute_data['name'] ?? ''));
            $options = array_values((array) ($attribute_data['options'] ?? array()));
            if ($global_attribute) {
                $options = array_filter(array_map(static fn($option): int => (int) ($global_attribute['options'][(string) $option] ?? 0), $options));
            }
            $attribute->set_options($options);
            $attribute->set_position((int) ($attribute_data['position'] ?? 0));
            $attribute->set_visible((bool) ($attribute_data['visible'] ?? false));
            $attribute->set_variation((bool) ($attribute_data['variation'] ?? false));
            $attributes[] = $attribute;
        }
        $product->set_attributes($attributes);
        $product_id = $product->save();
        if (!$product_id) {
            svic_fixture_record_error("Could not save product {$source_id}");
            continue;
        }
        $maps['products'][$source_id] = (int) $product_id;
        $maps['counts']['products'] = (int) ($maps['counts']['products'] ?? 0) + 1;
        update_post_meta((int) $product_id, '_svic_source_fixture_id', $source_id);
    }

    foreach ($variations as $item) {
        $parent_id = $maps['products'][(int) ($item['product_id'] ?? 0)] ?? 0;
        if (!$parent_id) {
            svic_fixture_record_error("Could not resolve variation parent for product " . (int) ($item['product_id'] ?? 0));
            continue;
        }
        $variation = new WC_Product_Variation();
        $variation->set_parent_id($parent_id);
        $variation->set_status('publish');
        $variation->set_sku((string) ($item['sku'] ?? ''));
        $variation->set_regular_price((string) ($item['regular_price'] ?? ''));
        $variation->set_sale_price((string) ($item['sale_price'] ?? ''));
        $variation->set_price((string) ($item['price'] ?? ''));
        $variation->set_description(svic_fixture_replace_urls((string) ($item['description'] ?? ''), $source_url, $local_url));
        $variation->set_attributes((array) ($item['attributes'] ?? array()));
        $image_id = svic_fixture_image_id((array) ($item['image'] ?? array()), $maps);
        if ($image_id) {
            $variation->set_image_id($image_id);
        } elseif (!empty($item['image'])) {
            svic_fixture_record_error("Missing variation image for product " . (int) ($item['product_id'] ?? 0));
        }
        if (!$variation->save()) {
            svic_fixture_record_error("Could not save variation for product " . (int) ($item['product_id'] ?? 0));
        } else {
            $maps['counts']['variations'] = (int) ($maps['counts']['variations'] ?? 0) + 1;
        }
    }
}

function svic_fixture_import_local_media(array $items, array &$maps): void {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $theme_directory = realpath(get_stylesheet_directory());
    $uploads = wp_upload_dir();
    if (!is_string($theme_directory) || !empty($uploads['error'])) {
        WP_CLI::error('Could not resolve the theme or uploads directory for local fixture media.');
    }

    foreach ($items as $key => $item) {
        $source_path = realpath($theme_directory . '/' . ltrim((string) ($item['path'] ?? ''), '/'));
        if (!is_string($source_path) || !str_starts_with($source_path, $theme_directory . DIRECTORY_SEPARATOR) || !is_readable($source_path)) {
            WP_CLI::error("Local fixture media is missing: {$key}");
        }
        $filename = wp_unique_filename((string) $uploads['path'], wp_basename($source_path));
        $destination = trailingslashit((string) $uploads['path']) . $filename;
        if (!copy($source_path, $destination)) {
            WP_CLI::error("Could not copy local fixture media: {$key}");
        }
        $filetype = wp_check_filetype($filename);
        $attachment_id = wp_insert_attachment(array(
            'post_mime_type' => (string) ($filetype['type'] ?? 'image/webp'),
            'post_title' => (string) ($item['title'] ?? ''),
            'post_status' => 'inherit',
        ), $destination, 0, true);
        if (is_wp_error($attachment_id)) {
            WP_CLI::error("Could not import local fixture media {$key}: " . $attachment_id->get_error_message());
        }
        wp_update_attachment_metadata((int) $attachment_id, wp_generate_attachment_metadata((int) $attachment_id, $destination));
        update_post_meta((int) $attachment_id, '_wp_attachment_image_alt', (string) ($item['alt'] ?? ''));
        update_post_meta((int) $attachment_id, '_svic_local_fixture_key', '15p-' . $key);
        $maps['local_media'][$key] = (int) $attachment_id;
    }
}

function svic_fixture_import_local_15p(array &$maps): void {
    $media = array(
        'primary_studio' => array('path' => 'assets/images/products/svicloud-15p-primary-studio-v2-watermarked.webp', 'title' => 'SVICLOUD 15P studio product image', 'alt' => 'SVICLOUD 15P TV Box front studio view'),
        'angle_studio' => array('path' => 'assets/images/products/svicloud-15p-angle-studio-v2-watermarked.webp', 'title' => 'SVICLOUD 15P angled studio view', 'alt' => 'SVICLOUD 15P TV box angled view showing rear ports'),
        'lifestyle_studio' => array('path' => 'assets/images/products/svicloud-15p-lifestyle-studio-v2-watermarked.webp', 'title' => 'SVICLOUD 15P media console studio image', 'alt' => 'SVICLOUD 15P TV Box on media console'),
        'detail_studio' => array('path' => 'assets/images/products/svicloud-15p-detail-studio-v2-watermarked.webp', 'title' => 'SVICLOUD 15P front detail studio image', 'alt' => 'SVICLOUD 15P TV Box front detail view'),
        'marketing_feature' => array('path' => 'assets/images/products/svicloud-15p-marketing-v5-watermarked.webp', 'title' => 'SVICLOUD 15P in-stock feature graphic', 'alt' => 'SVICLOUD 15P Android 14 in-stock feature graphic'),
    );
    svic_fixture_import_local_media($media, $maps);

    $product = new WC_Product_Simple();
    $product->set_name('SVICLOUD 15P TV Box');
    $product->set_slug('svicloud-15p');
    $product->set_status('publish');
    $product->set_catalog_visibility('visible');
    $product->set_short_description('In stock now: Android 14, Amlogic S905Y5, 4 GB DDR3 memory, 64 GB eMMC storage, dual-band Wi-Fi 6, Bluetooth 5.4, and 4K HDR playback.');
    $product->set_description('<p>The SVICLOUD 15P TV Box runs Android 14 on an Amlogic S905Y5 quad-core ARM Cortex-A55 processor.</p><h2>Core specifications</h2><ul><li>4 GB DDR3 memory and 64 GB eMMC storage</li><li>Dual-band 2.4/5 GHz Wi-Fi 6 with 2T2R and Bluetooth 5.4</li><li>HDR10+, HDR10, and HLG processing</li><li>AV1, VP9, H.265/HEVC, and H.264 hardware decoding</li><li>HDMI 2.1, two USB 2.0 ports, RJ45 Ethernet, optical audio, and Type-C 5V/2A power</li></ul><h2>In the box</h2><p>Gift box, AC adapter, HDMI cable, Bluetooth voice remote, and user manual.</p><p><strong>In stock now at $288.00 (regular $379.00).</strong></p>');
    $product->set_regular_price('379');
    $product->set_sale_price('288');
    $product->set_price('288');
    $product->set_manage_stock(false);
    $product->set_stock_quantity(null);
    $product->set_backorders('no');
    $product->set_stock_status('instock');
    $product->set_weight('1.7');
    $product->set_length('13');
    $product->set_width('8');
    $product->set_height('3');
    $product->set_reviews_allowed(false);
    $product->set_image_id((int) $maps['local_media']['primary_studio']);
    $product->set_gallery_image_ids(array_values(array_map(static fn(string $key): int => (int) $maps['local_media'][$key], array('angle_studio', 'lifestyle_studio', 'detail_studio', 'marketing_feature'))));
    $categories = array_filter(array_map(static function (string $slug): int {
        $term = get_term_by('slug', $slug, 'product_cat');
        return $term instanceof WP_Term ? (int) $term->term_id : 0;
    }, array('svicloud-tv-box', 'android-tv-box')));
    $product->set_category_ids($categories);
    $product_id = $product->save();
    update_post_meta((int) $product_id, '_svic_local_fixture_key', '15p');
    delete_post_meta((int) $product_id, '_svic_coming_soon');
    $maps['local_products']['15p'] = (int) $product_id;
}

function svic_fixture_import_menus(array $menus, array $items, string $source_url, string $local_url, array &$maps): void {
    $locations = (array) get_theme_mod('nav_menu_locations', array());
    $registered_locations = get_registered_nav_menus();
    $assigned_primary = false;
    foreach ($menus as $menu) {
        $source_id = (int) ($menu['id'] ?? 0);
        $created = wp_create_nav_menu((string) ($menu['name'] ?? 'Main Menu'));
        if (is_wp_error($created)) {
            svic_fixture_record_error("Could not create menu {$source_id}: " . $created->get_error_message());
            continue;
        }
        $maps['menus'][$source_id] = (int) $created;
        $maps['counts']['menus'] = (int) ($maps['counts']['menus'] ?? 0) + 1;
        foreach ((array) ($menu['locations'] ?? array()) as $location) {
            if (isset($registered_locations[$location])) {
                $locations[$location] = (int) $created;
                $assigned_primary = $assigned_primary || $location === 'primary';
            }
        }
        if (!$assigned_primary && isset($registered_locations['primary'])) {
            $locations['primary'] = (int) $created;
            $assigned_primary = true;
        }
    }
    set_theme_mod('nav_menu_locations', $locations);
    usort($items, static fn(array $a, array $b): int => ((int) ($a['menu_order'] ?? 0)) <=> ((int) ($b['menu_order'] ?? 0)));
    foreach ($items as $item) {
        $source_id = (int) ($item['id'] ?? 0);
        $menu_id = $maps['menus'][(int) (($item['menus'][0] ?? 0))] ?? 0;
        if (!$source_id || !$menu_id) {
            svic_fixture_record_error('Menu item is missing a source ID or resolvable menu');
            continue;
        }
        $type = (string) ($item['type'] ?? 'custom');
        $object_id = (int) ($item['object_id'] ?? 0);
        if ($type === 'post_type') {
            $object_id = (int) ($maps['posts'][$object_id] ?? $maps['products'][$object_id] ?? 0);
        }
        $created = wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-title' => (string) ($item['title'] ?? ''),
            'menu-item-type' => $type,
            'menu-item-object' => (string) ($item['object'] ?? ''),
            'menu-item-object-id' => $object_id,
            'menu-item-url' => svic_fixture_replace_urls((string) ($item['url'] ?? ''), $source_url, $local_url),
            'menu-item-status' => 'publish',
            'menu-item-position' => (int) ($item['menu_order'] ?? 0),
            'menu-item-target' => (string) ($item['target'] ?? ''),
            'menu-item-classes' => implode(' ', (array) ($item['classes'] ?? array())),
            'menu-item-xfn' => (string) ($item['xfn'] ?? ''),
            'menu-item-description' => (string) ($item['description'] ?? ''),
        ));
        if (!is_wp_error($created)) {
            $maps['menu_items'][$source_id] = (int) $created;
            $maps['counts']['menu_items'] = (int) ($maps['counts']['menu_items'] ?? 0) + 1;
            update_post_meta((int) $created, '_svic_source_fixture_id', $source_id);
        } else {
            svic_fixture_record_error("Could not create menu item {$source_id}: " . $created->get_error_message());
        }
    }
    foreach ($items as $item) {
        $item_id = $maps['menu_items'][(int) ($item['id'] ?? 0)] ?? 0;
        $parent_id = $maps['menu_items'][(int) ($item['parent'] ?? 0)] ?? 0;
        if (!$item_id) {
            continue;
        }
        // Menu hierarchy lives in this meta key, not in post_parent.
        update_post_meta($item_id, '_menu_item_menu_item_parent', (string) $parent_id);
    }
}

function svic_fixture_assert_complete(array $fixture, array $maps): void {
    $expected = array(
        'pages' => count(array_filter((array) ($fixture['pages'] ?? array()), static fn(array $item): bool => (int) ($item['id'] ?? 0) > 0)),
        'posts' => count(array_filter((array) ($fixture['posts'] ?? array()), static fn(array $item): bool => (int) ($item['id'] ?? 0) > 0)),
        'media' => count(array_filter((array) ($fixture['media'] ?? array()), static fn(array $item): bool => (int) ($item['id'] ?? 0) > 0 && (string) ($item['source_url'] ?? '') !== '')),
        'products' => count(array_filter((array) ($fixture['products'] ?? array()), static fn(array $item): bool => (int) ($item['id'] ?? 0) > 0)),
        'variations' => count((array) ($fixture['variations'] ?? array())),
        'product_attributes' => count(array_filter((array) ($fixture['product_attributes'] ?? array()), static fn(array $item): bool => (int) ($item['id'] ?? 0) > 0)),
        'menus' => count(array_filter((array) ($fixture['menus'] ?? array()), static fn(array $item): bool => (int) ($item['id'] ?? 0) > 0)),
        'menu_items' => count(array_filter((array) ($fixture['menu_items'] ?? array()), static fn(array $item): bool => (int) ($item['id'] ?? 0) > 0)),
        'navigation' => count((array) ($fixture['navigation'] ?? array())),
    );
    foreach (array('categories' => 'category', 'tags' => 'post_tag', 'product_categories' => 'product_cat', 'product_tags' => 'product_tag') as $fixture_key => $taxonomy) {
        $expected["terms.{$taxonomy}"] = count(array_filter((array) ($fixture[$fixture_key] ?? array()), static fn(array $item): bool => (int) ($item['id'] ?? 0) > 0));
    }
    $actual = array(
        'pages' => (int) ($maps['counts']['page'] ?? 0),
        'posts' => (int) ($maps['counts']['post'] ?? 0),
        'media' => (int) ($maps['counts']['media'] ?? 0),
        'products' => (int) ($maps['counts']['products'] ?? 0),
        'variations' => (int) ($maps['counts']['variations'] ?? 0),
        'product_attributes' => (int) ($maps['counts']['product_attributes'] ?? 0),
        'menus' => (int) ($maps['counts']['menus'] ?? 0),
        'menu_items' => (int) ($maps['counts']['menu_items'] ?? 0),
        'navigation' => (int) ($maps['counts']['navigation'] ?? 0),
    );
    foreach (array('category', 'post_tag', 'product_cat', 'product_tag') as $taxonomy) {
        $actual["terms.{$taxonomy}"] = (int) ($maps['counts']['terms'][$taxonomy] ?? 0);
    }
    foreach ($expected as $key => $count) {
        if (($actual[$key] ?? 0) !== $count) {
            svic_fixture_record_error("Complete manifest mismatch for {$key}: expected {$count}, imported " . (int) ($actual[$key] ?? 0));
        }
    }
}

function svic_fixture_rewrite_media_urls(array $maps): void {
    if (!$maps['urls']) {
        return;
    }
    $posts = get_posts(array('post_type' => array('page', 'post', 'product', 'product_variation', 'wp_navigation', 'nav_menu_item'), 'post_status' => 'any', 'numberposts' => -1));
    foreach ($posts as $post) {
        $content = str_replace(array_keys($maps['urls']), array_values($maps['urls']), (string) $post->post_content);
        $excerpt = str_replace(array_keys($maps['urls']), array_values($maps['urls']), (string) $post->post_excerpt);
        if ($content !== $post->post_content || $excerpt !== $post->post_excerpt) {
            wp_update_post(array('ID' => $post->ID, 'post_content' => $content, 'post_excerpt' => $excerpt));
        }
    }
}

function svic_fixture_import_navigation(array $navigation, string $source_url, string $local_url, array &$maps): void {
    foreach ($navigation as $item) {
        $created = wp_insert_post(array(
            'post_type' => 'wp_navigation',
            'post_status' => 'publish',
            'post_title' => (string) ($item['title'] ?? ''),
            'post_name' => sanitize_title((string) ($item['slug'] ?? '')),
            'post_content' => svic_fixture_replace_urls((string) ($item['content'] ?? ''), $source_url, $local_url),
        ), true);
        if (is_wp_error($created)) {
            svic_fixture_record_error('Could not import block navigation: ' . $created->get_error_message());
            continue;
        }
        $maps['counts']['navigation'] = (int) ($maps['counts']['navigation'] ?? 0) + 1;
    }
}

$maps = array('media' => array(), 'media_by_source_url' => array(), 'local_media' => array(), 'urls' => array(), 'posts' => array(), 'products' => array(), 'local_products' => array(), 'terms' => array(), 'attributes' => array(), 'menus' => array(), 'menu_items' => array(), 'counts' => array('terms' => array()));
svic_fixture_delete_content();
svic_fixture_import_terms((array) ($fixture['categories'] ?? array()), 'category', $maps);
svic_fixture_import_terms((array) ($fixture['tags'] ?? array()), 'post_tag', $maps);
svic_fixture_import_terms((array) ($fixture['product_categories'] ?? array()), 'product_cat', $maps);
svic_fixture_import_terms((array) ($fixture['product_tags'] ?? array()), 'product_tag', $maps);
svic_fixture_import_media((array) ($fixture['media'] ?? array()), $source_url, $local_url, $maps);
svic_fixture_restore_brand_logo((array) ($fixture['media'] ?? array()), $maps);
svic_fixture_import_posts((array) ($fixture['pages'] ?? array()), 'page', $source_url, $local_url, $maps);
svic_fixture_import_posts((array) ($fixture['posts'] ?? array()), 'post', $source_url, $local_url, $maps);
svic_fixture_import_display_settings((array) ($fixture['settings'] ?? array()), $maps);
svic_fixture_import_woocommerce_pages((array) ($fixture['pages'] ?? array()), $maps);
svic_fixture_import_global_attributes((array) ($fixture['product_attributes'] ?? array()), (array) ($fixture['attribute_terms'] ?? array()), $maps);
svic_fixture_import_products((array) ($fixture['products'] ?? array()), (array) ($fixture['variations'] ?? array()), $source_url, $local_url, $maps);
svic_fixture_import_local_15p($maps);
svic_fixture_import_menus((array) ($fixture['menus'] ?? array()), (array) ($fixture['menu_items'] ?? array()), $source_url, $local_url, $maps);
svic_fixture_import_navigation((array) ($fixture['navigation'] ?? array()), $source_url, $local_url, $maps);
svic_fixture_assert_complete($fixture, $maps);
svic_fixture_fail_if_incomplete();
svic_fixture_rewrite_media_urls($maps);
wp_cache_flush();
WP_CLI::success(sprintf('Imported public fixture: %d pages, %d posts, %d products, %d media items, %d menus.', count((array) ($fixture['pages'] ?? array())), count((array) ($fixture['posts'] ?? array())), count((array) ($fixture['products'] ?? array())) + count($maps['local_products']), count((array) ($fixture['media'] ?? array())) + count($maps['local_media']), count((array) ($fixture['menus'] ?? array()))));
