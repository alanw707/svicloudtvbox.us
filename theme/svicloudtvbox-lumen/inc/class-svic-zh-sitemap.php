<?php
/**
 * Custom sitemap provider to expose /zh/ localized URLs for indexing.
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WP_Sitemaps_Provider')) {
    $provider_class = ABSPATH . 'wp-includes/sitemaps/class-wp-sitemaps-provider.php';
    if (file_exists($provider_class)) {
        require_once $provider_class;
    }
}

if (!class_exists('SVIC_ZH_Sitemap_Provider') && class_exists('WP_Sitemaps_Provider')) {
    class SVIC_ZH_Sitemap_Provider extends WP_Sitemaps_Provider
    {
        public function __construct()
        {
            $this->name        = 'zh';
            $this->object_type = 'zh';
        }

        /**
         * Provider name used in sitemap URLs (e.g., wp-sitemap-zh-urls-1.xml).
         *
         * @var string
         */
        /**
         * Return a single logical subtype so we emit one set (paginated) of zh URLs.
         *
         * @return array<string,string>
         */
        public function get_object_subtypes()
        {
            return ['urls' => 'urls'];
        }

        /**
         * Total pages for this sitemap.
         */
        public function get_max_num_pages($object_subtype = '')
        {
            if (!function_exists('wp_sitemaps_get_max_urls')) {
                return 0;
            }

            $per_page = (int) wp_sitemaps_get_max_urls($this->object_type);
            if ($per_page <= 0) {
                $per_page = 2000;
            }

            $query = $this->build_query([
                'fields'         => 'ids',
                'posts_per_page' => 1,
                'no_found_rows'  => false,
            ]);

            if (!($query instanceof WP_Query)) {
                return 0;
            }

            $total = (int) $query->found_posts;
            if ($total <= 0) {
                return 0;
            }

            return (int) ceil($total / $per_page);
        }

        /**
         * Return a list of zh-localized URLs for the given page number.
         *
         * @return array<int,array{loc:string,lastmod?:string}>
         */
        public function get_url_list($page_num, $object_subtype = '')
        {
            if (!function_exists('svic_url_with_lang')) {
                return [];
            }

            $per_page = function_exists('wp_sitemaps_get_max_urls') ? (int) wp_sitemaps_get_max_urls($this->object_type) : 2000;
            if ($per_page <= 0) {
                $per_page = 2000;
            }

            $page_num = max(1, (int) $page_num);
            $offset   = ($page_num - 1) * $per_page;

            $query = $this->build_query([
                'posts_per_page' => $per_page,
                'offset'         => $offset,
                'no_found_rows'  => true,
            ]);

            if (!($query instanceof WP_Query) || empty($query->posts)) {
                return [];
            }

            $entries = [];
            foreach ($query->posts as $post) {
                if (!($post instanceof WP_Post)) {
                    continue;
                }

                $loc = svic_url_with_lang(get_permalink($post), 'zh');
                if (!is_string($loc) || $loc === '') {
                    continue;
                }

                $lastmod_gmt = $post->post_modified_gmt ?: $post->post_date_gmt;
                $lastmod     = $lastmod_gmt ? mysql2date(DATE_W3C, $lastmod_gmt) : '';

                $entry = ['loc' => $loc];
                if ($lastmod) {
                    $entry['lastmod'] = $lastmod;
                }

                $entries[] = $entry;
            }

            return $entries;
        }

        /**
         * Build a base query across supported post types.
         */
        private function build_query(array $overrides = []): WP_Query
        {
            $types = $this->supported_post_types();

            $args = [
                'post_type'           => $types,
                'post_status'         => 'publish',
                'has_password'        => false,
                'orderby'             => 'modified',
                'order'               => 'DESC',
                'ignore_sticky_posts' => true,
            ];

            $args = array_merge($args, $overrides);

            return new WP_Query($args);
        }

        /**
         * The post types we mirror into zh URLs.
         */
        private function supported_post_types(): array
        {
            $types = ['page'];

            if (post_type_exists('post')) {
                $types[] = 'post';
            }

            if (post_type_exists('product')) {
                $types[] = 'product';
            }

            /**
             * Allow customization of included post types.
             */
            return apply_filters('svic_zh_sitemap_post_types', $types);
        }
    }

    // Register the provider during sitemap bootstrap as well as on init fallback.
    add_action('wp_sitemaps_register_providers', function ($registry) {
        if (!is_object($registry) || !method_exists($registry, 'add_provider')) {
            return;
        }
        $registry->add_provider('zh', new SVIC_ZH_Sitemap_Provider());
    });

    add_action('init', function () {
        if (!function_exists('wp_sitemaps_get_server')) {
            return;
        }

        $server = wp_sitemaps_get_server();
        if (!is_object($server) || !isset($server->registry)) {
            return;
        }

        $registry = $server->registry;
        if (!is_object($registry) || !method_exists($registry, 'add_provider')) {
            return;
        }

        $providers = method_exists($registry, 'get_providers') ? $registry->get_providers() : [];
        if (!is_array($providers) || !isset($providers['zh'])) {
            $registry->add_provider('zh', new SVIC_ZH_Sitemap_Provider());
        }
    }, 20);

    if (!function_exists('svic_output_zh_sitemap_page')) {
        function svic_output_zh_sitemap_page(int $page_num): void
        {
            $page_num = max(1, $page_num);

            $provider = new SVIC_ZH_Sitemap_Provider();
            $entries  = $provider->get_url_list($page_num, 'urls');

            if (!headers_sent()) {
                header('Content-Type: application/xml; charset=UTF-8');
                header('X-Robots-Tag: noindex');
                header('Cache-Control: no-cache, must-revalidate, max-age=0, no-store, private');
                header('Pragma: no-cache');
                header('Expires: 0');
            }

            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

            foreach ($entries as $entry) {
                $loc     = isset($entry['loc']) ? esc_url($entry['loc']) : '';
                $lastmod = isset($entry['lastmod']) ? esc_html($entry['lastmod']) : '';

                if ($loc === '') {
                    continue;
                }

                echo '<url>' . "\n";
                echo '  <loc>' . $loc . '</loc>' . "\n";
                if ($lastmod !== '') {
                    echo '  <lastmod>' . $lastmod . '</lastmod>' . "\n";
                }
                echo '</url>' . "\n";
            }

            echo '</urlset>';
            exit;
        }
    }

    add_action('init', function () {
        if (is_admin()) {
            return;
        }

        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        if (!is_string($request_uri) || $request_uri === '') {
            return;
        }

        $path = (string) parse_url($request_uri, PHP_URL_PATH);
        $match = [];

        if ($path) {
            if ($path === '/zh-sitemap.xml' || $path === '/zh-sitemap-1.xml') {
                svic_output_zh_sitemap_page(1);
            }

            if (preg_match('#^/zh-sitemap-(\\d+)\\.xml$#', $path, $match)) {
                svic_output_zh_sitemap_page((int) $match[1]);
            }

            if (preg_match('#^/wp-sitemap-zh-urls-(\\d+)\\.xml$#', $path, $match)) {
                svic_output_zh_sitemap_page((int) $match[1]);
            }
        }

        if (isset($_GET['zh_sitemap_page'])) {
            $page_param = (int) sanitize_text_field(wp_unslash((string) $_GET['zh_sitemap_page']));
            if ($page_param > 0) {
                svic_output_zh_sitemap_page($page_param);
            }
        }

        if (isset($_GET['svic_zh_sitemap'])) {
            $page_param = (int) sanitize_text_field(wp_unslash((string) $_GET['svic_zh_sitemap']));
            if ($page_param > 0) {
                svic_output_zh_sitemap_page($page_param);
            }
        }

        if (isset($_GET['sitemap'])) {
            $sitemap_param = sanitize_text_field(wp_unslash((string) $_GET['sitemap']));
            if (preg_match('/^zh(?:-urls)?-(\\d+)$/', $sitemap_param, $match)) {
                svic_output_zh_sitemap_page((int) $match[1]);
            }
        }
    }, 1);
}
