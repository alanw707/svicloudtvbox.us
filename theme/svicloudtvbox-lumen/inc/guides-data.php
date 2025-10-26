<?php
if (!function_exists('svic_guides_get_content')) {
    function svic_guides_get_content() {
        static $content = null;

        if ($content !== null) {
            return $content;
        }

        $content = [
            'hero_callouts' => [
                'guides.hero.callouts.remote',
                'guides.hero.callouts.network',
                'guides.hero.callouts.apps',
            ],
            'highlight_cards' => [
                [
                    'title_key' => 'guides.highlights.items.install.title',
                    'copy_key'  => 'guides.highlights.items.install.copy',
                ],
                [
                    'title_key' => 'guides.highlights.items.models.title',
                    'copy_key'  => 'guides.highlights.items.models.copy',
                ],
                [
                    'title_key' => 'guides.highlights.items.concierge.title',
                    'copy_key'  => 'guides.highlights.items.concierge.copy',
                ],
            ],
            'setup_steps' => [
                [
                    'title_key' => 'guides.setup.steps.connect.title',
                    'copy_key'  => 'guides.setup.steps.connect.copy',
                ],
                [
                    'title_key' => 'guides.setup.steps.language.title',
                    'copy_key'  => 'guides.setup.steps.language.copy',
                ],
                [
                    'title_key' => 'guides.setup.steps.disclaimer.title',
                    'copy_key'  => 'guides.setup.steps.disclaimer.copy',
                ],
                [
                    'title_key' => 'guides.setup.steps.remote.title',
                    'copy_key'  => 'guides.setup.steps.remote.copy',
                ],
                [
                    'title_key' => 'guides.setup.steps.time.title',
                    'copy_key'  => 'guides.setup.steps.time.copy',
                ],
                [
                    'title_key' => 'guides.setup.steps.network.title',
                    'copy_key'  => 'guides.setup.steps.network.copy',
                ],
                [
                    'title_key' => 'guides.setup.steps.apps.title',
                    'copy_key'  => 'guides.setup.steps.apps.copy',
                ],
            ],
            'app_cards' => [
                [
                    'title_key' => 'guides.apps.items.live.title',
                    'copy_key'  => 'guides.apps.items.live.copy',
                ],
                [
                    'title_key' => 'guides.apps.items.kids.title',
                    'copy_key'  => 'guides.apps.items.kids.copy',
                ],
                [
                    'title_key' => 'guides.apps.items.karaoke.title',
                    'copy_key'  => 'guides.apps.items.karaoke.copy',
                ],
                [
                    'title_key' => 'guides.apps.items.regional.title',
                    'copy_key'  => 'guides.apps.items.regional.copy',
                ],
                [
                    'title_key' => 'guides.apps.items.cherry.title',
                    'copy_key'  => 'guides.apps.items.cherry.copy',
                ],
            ],
            'post_setup_cards' => [
                [
                    'title_key' => 'guides.post_setup.items.explore.title',
                    'copy_key'  => 'guides.post_setup.items.explore.copy',
                ],
                [
                    'title_key' => 'guides.post_setup.items.install.title',
                    'copy_key'  => 'guides.post_setup.items.install.copy',
                ],
                [
                    'title_key' => 'guides.post_setup.items.tune.title',
                    'copy_key'  => 'guides.post_setup.items.tune.copy',
                ],
            ],
            'troubleshooting_cards' => [
                [
                    'title_key' => 'guides.troubleshooting.items.remote.title',
                    'copy_key'  => 'guides.troubleshooting.items.remote.copy',
                ],
                [
                    'title_key' => 'guides.troubleshooting.items.streaming.title',
                    'copy_key'  => 'guides.troubleshooting.items.streaming.copy',
                ],
                [
                    'title_key' => 'guides.troubleshooting.items.orz.title',
                    'copy_key'  => 'guides.troubleshooting.items.orz.copy',
                ],
            ],
            'resource_articles' => [],
            'resource_links'    => [],
            'anchor_items' => [
                [
                    'key'              => 'overview',
                    'id'               => 'guides-highlights',
                    'slug'             => 'guides-overview',
                    'label_key'        => 'guides.nav.overview',
                    'summary_key'      => 'guides.nav_summaries.overview',
                    'translation_root' => 'guides.highlights',
                    'content_key'      => 'highlight_cards',
                ],
                [
                    'key'              => 'setup',
                    'id'               => 'setup-guide',
                    'slug'             => 'guides-setup',
                    'label_key'        => 'guides.nav.setup',
                    'summary_key'      => 'guides.nav_summaries.setup',
                    'translation_root' => 'guides.setup',
                    'content_key'      => 'setup_steps',
                ],
                [
                    'key'              => 'apps',
                    'id'               => 'guides-apps',
                    'slug'             => 'guides-apps',
                    'label_key'        => 'guides.nav.apps',
                    'summary_key'      => 'guides.nav_summaries.apps',
                    'translation_root' => 'guides.apps',
                    'content_key'      => 'app_cards',
                ],
                [
                    'key'              => 'post_setup',
                    'id'               => 'guides-after-setup',
                    'slug'             => 'guides-after-setup',
                    'label_key'        => 'guides.nav.post_setup',
                    'summary_key'      => 'guides.nav_summaries.post_setup',
                    'translation_root' => 'guides.post_setup',
                    'content_key'      => 'post_setup_cards',
                ],
                [
                    'key'              => 'troubleshooting',
                    'id'               => 'guides-troubleshooting',
                    'slug'             => 'guides-troubleshooting',
                    'label_key'        => 'guides.nav.troubleshooting',
                    'summary_key'      => 'guides.nav_summaries.troubleshooting',
                    'translation_root' => 'guides.troubleshooting',
                    'content_key'      => 'troubleshooting_cards',
                ],
                [
                    'key'              => 'resources',
                    'id'               => 'guides-resources',
                    'slug'             => 'guides-resources',
                    'label_key'        => 'guides.nav.resources',
                    'summary_key'      => 'guides.nav_summaries.resources',
                    'translation_root' => 'guides.resources',
                    'content_key'      => 'resource_links',
                ],
                [
                    'key'              => 'support',
                    'id'               => 'guides-support',
                    'slug'             => 'guides-support',
                    'label_key'        => 'guides.nav.support',
                    'summary_key'      => 'guides.nav_summaries.support',
                    'translation_root' => 'guides.support',
                    'content_key'      => null,
                ],
            ],
        ];

        $resource_articles = [
            'why-buy' => [
                'slug'        => 'why-buy',
                'title_key'   => 'guides.resources.articles.why.title',
                'summary_key' => 'guides.resources.items.why.copy',
                'hero'        => [
                    'badge_key'  => 'guides.resources.articles.shared.badge',
                    'title_key'  => 'guides.resources.articles.why.title',
                    'lead_key'   => 'guides.resources.articles.why.lead',
                    'updated_key'=> 'guides.resources.articles.why.updated',
                ],
                'sections'    => [
                    [
                        'id'          => 'fulfillment',
                        'heading_key' => 'guides.resources.articles.why.sections.fulfillment.heading',
                        'body'        => [
                            ['key' => 'guides.resources.articles.why.sections.fulfillment.body'],
                        ],
                    ],
                    [
                        'id'          => 'warranty',
                        'heading_key' => 'guides.resources.articles.why.sections.warranty.heading',
                        'body'        => [
                            ['key' => 'guides.resources.articles.why.sections.warranty.body'],
                        ],
                    ],
                    [
                        'id'          => 'concierge',
                        'heading_key' => 'guides.resources.articles.why.sections.concierge.heading',
                        'body'        => [
                            [
                                'key'    => 'guides.resources.articles.why.sections.concierge.body',
                                'tokens' => [
                                    'contact_url' => 'contact',
                                ],
                            ],
                        ],
                    ],
                    [
                        'id'          => 'billing',
                        'heading_key' => 'guides.resources.articles.why.sections.billing.heading',
                        'body'        => [
                            ['key' => 'guides.resources.articles.why.sections.billing.body'],
                        ],
                    ],
                    [
                        'id'          => 'next-steps',
                        'heading_key' => 'guides.resources.articles.why.sections.next_steps.heading',
                        'body'        => [
                            [
                                'key'    => 'guides.resources.articles.why.sections.next_steps.body',
                                'tokens' => [
                                    'setup_url' => 'guides-setup',
                                    'legal_url' => 'legal-disclaimer',
                                    'contact_url' => 'contact',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'channel-guide' => [
                'slug'        => 'channel-guide',
                'title_key'   => 'guides.resources.articles.channels.title',
                'summary_key' => 'guides.resources.items.channels.copy',
                'hero'        => [
                    'badge_key'  => 'guides.resources.articles.shared.badge',
                    'title_key'  => 'guides.resources.articles.channels.title',
                    'lead_key'   => 'guides.resources.articles.channels.lead',
                    'updated_key'=> 'guides.resources.articles.channels.updated',
                ],
                'sections'    => [
                    [
                        'id'          => 'rights',
                        'heading_key' => 'guides.resources.articles.channels.sections.rights.heading',
                        'body'        => [
                            ['key' => 'guides.resources.articles.channels.sections.rights.body'],
                        ],
                    ],
                    [
                        'id'          => 'regional',
                        'heading_key' => 'guides.resources.articles.channels.sections.regional.heading',
                        'body'        => [
                            ['key' => 'guides.resources.articles.channels.sections.regional.body'],
                        ],
                    ],
                    [
                        'id'          => 'security',
                        'heading_key' => 'guides.resources.articles.channels.sections.security.heading',
                        'body'        => [
                            ['key' => 'guides.resources.articles.channels.sections.security.body'],
                        ],
                    ],
                    [
                        'id'          => 'documentation',
                        'heading_key' => 'guides.resources.articles.channels.sections.documentation.heading',
                        'body'        => [
                            ['key' => 'guides.resources.articles.channels.sections.documentation.body'],
                        ],
                    ],
                    [
                        'id'          => 'network',
                        'heading_key' => 'guides.resources.articles.channels.sections.network.heading',
                        'body'        => [
                            [
                                'key'    => 'guides.resources.articles.channels.sections.network.body',
                                'tokens' => [
                                    'troubleshooting_url' => 'guides-troubleshooting',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'top-features' => [
                'slug'        => 'top-features',
                'title_key'   => 'guides.resources.articles.features.title',
                'summary_key' => 'guides.resources.items.features.copy',
                'hero'        => [
                    'badge_key'  => 'guides.resources.articles.shared.badge',
                    'title_key'  => 'guides.resources.articles.features.title',
                    'lead_key'   => 'guides.resources.articles.features.lead',
                    'updated_key'=> 'guides.resources.articles.features.updated',
                ],
                'sections'    => [
                    [
                        'id'          => 'hardware',
                        'heading_key' => 'guides.resources.articles.features.sections.hardware.heading',
                        'body'        => [
                            ['key' => 'guides.resources.articles.features.sections.hardware.body'],
                        ],
                    ],
                    [
                        'id'          => 'streaming',
                        'heading_key' => 'guides.resources.articles.features.sections.streaming.heading',
                        'body'        => [
                            ['key' => 'guides.resources.articles.features.sections.streaming.body'],
                        ],
                    ],
                    [
                        'id'          => 'remote',
                        'heading_key' => 'guides.resources.articles.features.sections.remote.heading',
                        'body'        => [
                            ['key' => 'guides.resources.articles.features.sections.remote.body'],
                        ],
                    ],
                    [
                        'id'          => 'family',
                        'heading_key' => 'guides.resources.articles.features.sections.family.heading',
                        'body'        => [
                            ['key' => 'guides.resources.articles.features.sections.family.body'],
                        ],
                    ],
                    [
                        'id'          => 'service',
                        'heading_key' => 'guides.resources.articles.features.sections.service.heading',
                        'body'        => [
                            [
                                'key'    => 'guides.resources.articles.features.sections.service.body',
                                'tokens' => [
                                    'contact_url' => 'contact',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'model-selector' => [
                'slug'        => 'model-selector',
                'title_key'   => 'guides.resources.articles.models.title',
                'summary_key' => 'guides.resources.items.which.copy',
                'hero'        => [
                    'badge_key'  => 'guides.resources.articles.shared.badge',
                    'title_key'  => 'guides.resources.articles.models.title',
                    'lead_key'   => 'guides.resources.articles.models.lead',
                    'updated_key'=> 'guides.resources.articles.models.updated',
                ],
                'sections'    => [
                    [
                        'id'          => 'summary',
                        'heading_key' => 'guides.resources.articles.models.sections.summary.heading',
                        'body'        => [
                            ['key' => 'guides.resources.articles.models.sections.summary.body'],
                        ],
                    ],
                    [
                        'id'          => 'ten-p-plus',
                        'heading_key' => 'guides.resources.articles.models.sections.ten_p.heading',
                        'body'        => [
                            ['key' => 'guides.resources.articles.models.sections.ten_p.body'],
                        ],
                    ],
                    [
                        'id'          => 'ten-s',
                        'heading_key' => 'guides.resources.articles.models.sections.ten_s.heading',
                        'body'        => [
                            ['key' => 'guides.resources.articles.models.sections.ten_s.body'],
                        ],
                    ],
                    [
                        'id'          => 'next-steps',
                        'heading_key' => 'guides.resources.articles.models.sections.next_steps.heading',
                        'body'        => [
                            [
                                'key'    => 'guides.resources.articles.models.sections.next_steps.body',
                                'tokens' => [
                                    'setup_url' => 'guides-setup',
                                    'legal_url' => 'legal-disclaimer',
                                    'contact_url' => 'contact',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $content['resource_articles'] = $resource_articles;
        $content['resource_links'] = [];

        foreach ($resource_articles as $article) {
            $content['resource_links'][] = [
                'title_key' => $article['title_key'],
                'copy_key'  => $article['summary_key'] ?? '',
                'slug'      => $article['slug'],
            ];
        }

        return $content;
    }
}

if (!function_exists('svic_guides_get_content_item')) {
    function svic_guides_get_content_item($key, $default = []) {
        $content = svic_guides_get_content();

        if (array_key_exists($key, $content)) {
            return $content[$key];
        }

        return $default;
    }
}

if (!function_exists('svic_guides_get_resource_articles')) {
    function svic_guides_get_resource_articles() {
        $articles = svic_guides_get_content_item('resource_articles', []);

        return is_array($articles) ? $articles : [];
    }
}

if (!function_exists('svic_guides_get_resource_article')) {
    function svic_guides_get_resource_article($slug) {
        if (!is_string($slug) || $slug === '') {
            return null;
        }

        $normalized = sanitize_title($slug);
        if ($normalized === '') {
            return null;
        }

        $articles = svic_guides_get_resource_articles();
        if (isset($articles[$normalized])) {
            return $articles[$normalized];
        }

        return null;
    }
}

if (!function_exists('svic_guides_get_anchor_items')) {
    function svic_guides_get_anchor_items() {
        return svic_guides_get_content_item('anchor_items', []);
    }
}

if (!function_exists('svic_guides_get_section_by_key')) {
    function svic_guides_get_section_by_key($section_key) {
        $items = svic_guides_get_anchor_items();

        foreach ($items as $item) {
            if (!isset($item['key'])) {
                continue;
            }

            if ($item['key'] === $section_key) {
                return $item;
            }
        }

        return null;
    }
}

if (!function_exists('svic_guides_list_sections')) {
    function svic_guides_list_sections() {
        $items = svic_guides_get_anchor_items();

        $keys = array_map(function ($item) {
            return $item['key'] ?? null;
        }, $items);

        return array_values(array_filter($keys));
    }
}

if (!function_exists('svic_guides_get_section_content')) {
    function svic_guides_get_section_content($section_key) {
        $section = svic_guides_get_section_by_key($section_key);

        if (!$section) {
            return null;
        }

        $content_key = $section['content_key'] ?? null;
        $items = [];

        if ($content_key) {
            $items = svic_guides_get_content_item($content_key, []);
        }

        return [
            'section' => $section,
            'items'   => $items,
        ];
    }
}

if (!function_exists('svic_guides_resolve_section_key')) {
    function svic_guides_resolve_section_key($slug) {
        if (!is_string($slug) || $slug === '') {
            return null;
        }

        $normalized = sanitize_title($slug);
        if ($normalized === '') {
            return null;
        }

        $manual_map = [
            'guides-setup'           => 'setup',
            'setup-guide'            => 'setup',
            'guides-apps'            => 'apps',
            'guides-after-setup'     => 'post_setup',
            'guides-post-setup'      => 'post_setup',
            'guides-troubleshooting' => 'troubleshooting',
            'guides-resources'       => 'resources',
            'guides-support'         => 'support',
        ];

        if (isset($manual_map[$normalized])) {
            return $manual_map[$normalized];
        }

        $stripped = preg_replace('/^(guides-|guide-)/', '', $normalized);
        $stripped = preg_replace('/-(guide|hub)$/', '', $stripped ?? '');
        $stripped = str_replace('-', '_', $stripped);

        $available = svic_guides_list_sections();
        if ($stripped && in_array($stripped, $available, true)) {
            return $stripped;
        }

        return null;
    }
}

if (!function_exists('svic_guides_get_section_link')) {
    function svic_guides_get_section_link($section_key) {
        $section = svic_guides_get_section_by_key($section_key);
        if (!$section) {
            return null;
        }

        $slug = $section['slug'] ?? '';
        if (!$slug) {
            return null;
        }

        $page = get_page_by_path($slug);
        if ($page instanceof WP_Post) {
            return get_permalink($page);
        }

        return null;
    }
}

if (!function_exists('svic_guides_get_resource_link')) {
    function svic_guides_get_resource_link(array $resource, ?string $locale = null): string {
        $link = '';

        if (!empty($resource['external']) && !empty($resource['url'])) {
            $link = (string) $resource['url'];
        } elseif (!empty($resource['slug'])) {
            $base = svic_guides_get_section_link('resources');
            if ($base) {
                $link = add_query_arg('topic', sanitize_title($resource['slug']), $base);
            }
        } elseif (!empty($resource['url'])) {
            $link = (string) $resource['url'];
        }

        if ($link === '') {
            return '';
        }

        return svic_url_with_lang($link, $locale);
    }
}
