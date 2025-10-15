<?php
/**
 * SVICLOUD Lumen Theme translations (English US)
 */

return [
    'core' => [
        'locale' => 'English (US)',
        'cart'   => [
            'adding'             => 'Adding…',
            'added'              => 'Added to cart!',
            'count_label_single' => '{{count}} item in cart',
            'count_label_plural' => '{{count}} items in cart',
            'count_label_empty'  => 'Cart is empty',
            'error'              => 'Unable to add to cart. Please try again.',
        ],
        'badges' => [
            'authorized_dealer' => 'Authorized Dealer',
            'ships_from_usa'    => 'Ships from USA',
            'one_year_warranty' => '1-Year US Warranty',
        ],
    ],

    'contact' => [
        'hero' => [
            'badge'      => 'Concierge support',
            'title'      => 'Talk to the SviCloud concierge team',
            'subtitle'   => 'English & 中文 experts help with orders, setup, Wi-Fi fixes, and app updates. Real humans reply within one business day.',
            'primary_cta'=> 'Chat with concierge',
            'secondary_cta' => 'Submit a support form',
        ],
        'hours' => [
            'title' => 'Concierge hours',
            'copy'  => 'Live support: Monday–Saturday · 9am–6pm Pacific',
            'note'  => 'Messages received after hours are replied to the next morning.',
        ],
        'channels' => [
            'title' => 'Contact methods',
            'items' => [
                'phone' => [
                    'label' => 'SMS / Phone',
                    'value' => '702-398-3416',
                    'cta'   => 'Text or call now',
                ],
                'whatsapp' => [
                    'label' => 'WhatsApp',
                    'value' => '702-398-3416',
                    'cta'   => 'Open WhatsApp',
                ],
                'email' => [
                    'label' => 'Email',
                    'value' => 'support@svicloudtvbox.us',
                    'cta'   => 'Email concierge',
                ],
            ],
        ],
        'form' => [
            'title' => 'Need hands-on help?',
            'copy'  => 'Send the concierge team your order number, device, and issue—we reply within one business day.',
            'cta'   => 'Open support form',
        ],
        'faq' => [
            'title' => 'Quick answers',
            'copy'  => 'Need to self-serve first? The FAQ covers model, setup, and troubleshooting questions.',
            'cta'   => 'Visit FAQ',
        ],
    ],
    'cart_page' => [
        'title'             => 'Your cart',
        'continue_shopping' => 'Continue shopping',
        'empty'             => 'Your cart is currently empty.',
        'empty_cta'         => 'Browse products',
        'table'             => [
            'thumbnail' => 'Item',
            'product'   => 'Product',
            'price'     => 'Price',
            'quantity'  => 'Quantity',
            'total'     => 'Total',
        ],
        'meta'              => [
            'authorized' => 'Authorized U.S. Dealer',
            'shipping'   => '48-hour U.S. shipping',
            'warranty'   => '1-Year U.S. warranty',
        ],
        'remove'            => 'Remove item',
        'remove_aria'       => 'Remove %s from cart',
        'update'            => 'Update cart',
        'coupon'            => [
            'label'       => 'Promo code',
            'placeholder' => 'Enter code',
            'apply'       => 'Apply',
            'hint'        => 'Codes are case-sensitive. Leaving this blank won’t affect checkout.',
        ],
        'summary'           => [
            'title' => 'Order summary',
            'intro'        => 'Ships from our California warehouse with bilingual concierge support.',
            'empty'        => 'Add a device to see shipping speeds, warranty coverage, and your estimated total.',
            'reassurance'  => 'Secure checkout with PayPal, Visa, Mastercard, and American Express.',
        ],
        'benefits'          => [
            'shipping' => [
                'title' => 'Fast fulfillment',
                'copy'  => 'Orders leave California within 48 hours on business days.',
            ],
            'warranty' => [
                'title' => 'U.S. warranty',
                'copy'  => 'Every SVICLOUD box includes a 1-year U.S. warranty.',
            ],
            'concierge' => [
                'title' => 'Concierge support',
                'copy'  => 'Bilingual experts help install, update, and troubleshoot.',
            ],
        ],
    ],
    'checkout_page' => [
        'badge'    => 'Secure checkout',
        'title'    => 'Complete your SVICLOUD order',
        'subtitle' => 'Ships from California within 48 hours with bilingual concierge support.',
        'summary'  => [
            'title'         => 'Order summary',
            'intro'         => 'Review your items, totals, and payment method before placing your order.',
            'reassurance'   => 'SSL encrypted checkout with PayPal, Visa, Mastercard, and American Express.',
            'order_heading' => 'Review your order',
        ],
        'benefits' => [
            'shipping'  => '48-hour U.S. shipping',
            'warranty'  => '1-year U.S. warranty',
            'concierge' => 'English & 中文 concierge support',
        ],
        'assurance' => [
            'title' => 'Need a hand?',
            'copy'  => 'Our English & 中文 concierge will email tracking, setup tips, and live support once your order ships.',
        ],
        'payment' => [
            'title' => 'Payment & confirmation',
            'intro' => 'Choose a saved card or enter a new payment method, then place your order with secure checkout.',
            'cards_label' => 'Accepted cards: Visa, Mastercard, American Express, and Discover.',
        ],
        'coupon' => [
            'toggle_lead' => 'Have a promo code?',
            'toggle_cta'  => 'Click to enter it',
            'toggle_aria' => 'Enter your promo code',
            'intro'       => 'Add your code to apply eligible savings before placing your order.',
            'label'       => 'Promo code',
            'placeholder' => 'Enter code',
            'apply'       => 'Apply',
            'hint'        => 'Codes are case-sensitive. You can still place your order without a code.',
            'applied_label' => 'Applied code',
            'remove'        => 'Remove',
        ],
    ],

    'order_tracking' => [
        'badge'              => 'Order lookup',
        'title'              => 'Track your order',
        'subtitle'           => 'Enter the order number and billing email from your confirmation message.',
        'order_id_label'     => 'Order number',
        'order_id_placeholder' => 'e.g. 1234 or WC-1234',
        'email_label'        => 'Billing email',
        'email_placeholder'  => 'Email used at checkout',
        'submit'             => 'Track order',
        'submit_value'       => 'Track order',
        'help_text'          => "Need help or have a new tracking number?",
        'help_link'          => "Contact concierge support",
        'support_hours'      => 'Concierge hours: 9am–6pm PT · Mon–Sat',
        'summary' => [
            'order_label'    => 'Order details',
            'shipping_label' => 'Shipping status',
            'shipping_note'  => "We'll email you as soon as tracking updates.",
            'details_heading' => 'Items in this order',
            'details_note'    => 'Here’s a breakdown of everything in your shipment, including totals and billing details.',
            'placed'         => 'Placed on {{order_date}}',
            'total'          => 'Total {{order_total}}{{dot}}{{payment_method}}',
        ],
        'status_heading'     => "Order status",
        'status'             => 'Order {{order_number}} was placed on {{order_date}} and is currently {{order_status}}.',
        'customer_label'     => "Customer:",
        'updates_heading'    => "Latest updates",
        'no_updates'         => "No updates yet. We'll email you as soon as there's news.",
      ],

    'order_thankyou' => [
        'title'        => 'Thank you — your order is confirmed',
        'email_intro'  => 'We emailed your receipt and updates to {{name}} {{dot}} {{email}}',
        'summary' => [
            'order_label'    => 'Order details',
            'next_label'     => 'Next steps',
            'placed_on'      => 'Placed on {{order_date}}',
            'total'          => 'Total {{order_total}} · {{payment_method}}',
            'tracking_note'  => "We’ll email tracking details as soon as your package ships.",
            'details_heading'=> 'Items in this order',
            'details_note'   => 'Here’s a breakdown of everything in your shipment, including totals and billing details.',
        ],
        'cta' => [
            'track'    => 'Track order',
            'continue' => 'Continue shopping',
        ],
        'failed' => [
            'title'      => 'Order failed',
            'copy'       => 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.',
            'pay_now'    => 'Pay now',
            'my_account' => 'My account',
        ],
    ],

    'faq' => [
        'hero' => [
            'badge'      => 'Support center',
            'title'      => 'SviCloud TV Box FAQ',
            'subtitle'   => 'Quick answers for choosing, installing, and enjoying your SviCloud TV box.',
            'cta_primary'=> 'Read setup guide',
            'cta_secondary' => 'Compare models',
        ],
        'sections' => [
            'device_models' => [
                'title' => 'Device & models',
                'items' => [
                    'model_choice' => [
                        'question' => 'Which SviCloud model should I buy?',
                        'answer'   => 'Pick SviCloud 10P+ if you want voice search and karaoke. SviCloud 10S streams the same catalog but lacks Karaoke KTV and the mic-ready remote. Review the <a href="{{setup_guide_url}}">setup guide</a> after you choose, and compare specs on the <a href="{{compare_url}}">10P+ vs 10S page</a>.',
                    ],
                    'international_use' => [
                        'question' => 'Does the TV box work outside of Asia?',
                        'answer'   => 'Yes. SviCloud is built for international use—connect through any broadband or stable Wi-Fi network.',
                    ],
                    'box_contents' => [
                        'question' => 'What comes in the box?',
                        'answer'   => 'Every unit includes the TV box, voice remote, HDMI cable, power adapter, and a quick-start leaflet.',
                    ],
                ],
            ],
            'setup_activation' => [
                'title' => 'Setup & activation',
                'items' => [
                    'power_on' => [
                        'question' => 'How do I turn on my SviCloud TV box?',
                        'answer'   => 'Plug in the HDMI and power cables. The device boots automatically when it receives power, and the front LED confirms it is on. Follow the <a href="{{setup_guide_url}}">step-by-step setup guide</a> for screenshots.',
                    ],
                    'change_language' => [
                        'question' => 'How do I change the system language?',
                        'answer'   => 'On first boot, use the remote arrows to highlight your preferred language, press <code>OK</code>, then choose <code>Next</code>. You can revisit language settings anytime under system preferences.',
                    ],
                    'remote_pairing' => [
                        'question' => 'My remote is not responding. What should I do?',
                        'answer'   => 'Swap in fresh batteries, then hold <code>VOL-</code> and <code>VOL+</code> together to re-pair. Stay within a few feet of the box during pairing—this resolves most remote issues.',
                    ],
                ],
            ],
            'apps_content' => [
                'title' => 'Apps & content',
                'items' => [
                    'preinstalled' => [
                        'question' => 'Are SviCloud apps pre-installed?',
                        'answer'   => 'No. Launch the <code>Orz</code> installer, visit <code>8989c.cc</code>, and download Yogurt TV, Yogurt Kids, Cherry TV, Karaoke KTV, and other essentials.',
                    ],
                    'third_party' => [
                        'question' => 'Can I install Netflix or other third-party apps?',
                        'answer'   => 'Yes. SviCloud runs Android, so you can sideload popular streaming apps. Always download from trusted sources and confirm Android TV compatibility.',
                    ],
                    'family_content' => [
                        'question' => 'What content is available for families?',
                        'answer'   => 'Yogurt Kids (also labeled LUCA Kids) offers educational shows, interactive games, and safe cartoons. We Fun / 閤家歡 karaoke adds family-friendly singalongs.',
                    ],
                    'adult_content' => [
                        'question' => 'Is there adult content?',
                        'answer'   => 'Cherry TV contains mature programming and is protected by a password. You must opt in and enter the credential each time.',
                    ],
                ],
            ],
            'features_limitations' => [
                'title' => 'Features & limitations',
                'items' => [
                    'karaoke_support' => [
                        'question' => 'Does every SviCloud unit support karaoke?',
                        'answer'   => 'Only SviCloud 10P+. If karaoke is essential, select 10P+ instead of 10S.',
                    ],
                    'voice_control' => [
                        'question' => 'How does voice control work?',
                        'answer'   => 'After pairing the remote (hold <code>VOL-</code> + <code>VOL+</code>), press the mic button and say the show or movie name. Voice control is unavailable on SviCloud 10S.',
                    ],
                    'subtitle_speed' => [
                        'question' => 'Can I adjust subtitle speed?',
                        'answer'   => 'Yes. Subtitle pace and multi-language options live in the playback settings menu.',
                    ],
                ],
            ],
            'troubleshooting_support' => [
                'title' => 'Troubleshooting & support',
                'items' => [
                    'buffering' => [
                        'question' => 'Streaming looks fuzzy or keeps buffering. What should I try?',
                        'answer'   => 'Restart your router, move the box closer to Wi-Fi, or connect via Ethernet. Heavy household traffic may require a faster broadband plan. The <a href="{{setup_guide_url}}">setup guide</a> covers additional troubleshooting steps.',
                    ],
                    'orz_installer' => [
                        'question' => 'The Orz installer site will not load. What now?',
                        'answer'   => 'Double-check the URL (<code>8989c.cc</code>, which redirects to <code>6868c.cc</code>). If it still fails, reboot the box and confirm you have internet access.',
                    ],
                    'contact_support' => [
                        'question' => 'How do I reach support?',
                        'answer'   => 'Contact our concierge team through the <a href="{{support_url}}">svicloudtvbox.us support portal</a> or the email listed in your order confirmation. English and Traditional Chinese assistance is available.',
                    ],
                ],
            ],
    ],
    ],

    'support' => [
        'hero' => [
            'badge'    => 'Support form',
            'title'    => 'Submit a concierge support request',
            'subtitle' => 'Share your device details and issue below. Our bilingual team replies within one business day.',
        ],
        'form' => [
            'title'        => 'Tell us what’s happening',
            'description'  => 'Fill out the form and we’ll route your request to the right specialist.',
            'required_note'=> '* Required fields',
            'fields' => [
                'name' => [
                    'label'       => 'Full name *',
                    'placeholder' => 'e.g. Angela Lin',
                ],
                'email' => [
                    'label'       => 'Email address *',
                    'placeholder' => 'you@example.com',
                ],
                'phone' => [
                    'label'       => 'Phone / WhatsApp',
                    'placeholder' => '702-398-3416',
                ],
                'order' => [
                    'label'       => 'Order number',
                    'placeholder' => 'e.g. WC-1234',
                ],
                'device' => [
                    'label'       => 'Device',
                    'placeholder' => 'Select device',
                ],
                'issue' => [
                    'label'       => 'Issue type *',
                    'placeholder' => 'Choose an issue category',
                ],
                'message' => [
                    'label'       => 'How can we help? *',
                    'placeholder' => 'Share the steps to reproduce the issue or the help you need…',
                ],
            ],
            'device_options' => [
                '10p'   => 'SVICLOUD 10P+',
                '10s'   => 'SVICLOUD 10S',
                'other' => 'Other / Not sure',
            ],
            'issue_options' => [
                'setup'     => 'Setup & activation',
                'streaming' => 'Streaming or buffering',
                'billing'   => 'Billing or order',
                'apps'      => 'Apps, account, or remote',
                'other'     => 'Something else',
            ],
            'submit' => 'Send message',
        ],
        'success' => [
            'title' => 'Message sent',
            'body'  => 'Thanks! A concierge specialist will reply from support@svicloudtvbox.us within one business day. If you do not see our email, remember to check your spam or promotions folder.',
        ],
        'error'   => 'We could not send your message. Please try again or email support@svicloudtvbox.us.',
        'help' => [
            'contact' => [
                'title' => 'Need live concierge?',
                'copy'  => 'Call, text, WhatsApp, or email our bilingual team for real-time assistance.',
                'cta'   => 'Go to contact page',
            ],
            'faq' => [
                'title' => 'Browse quick answers',
                'copy'  => 'Our FAQ covers installation, Orz apps, remote pairing, and streaming tips.',
                'cta'   => 'Open FAQ',
            ],
        ],
    ],

    'header' => [
        'nav' => [
            'home'     => 'Home',
            'compare'  => 'Compare',
            'faq'      => 'FAQ',
            'ten_p'    => 'SVICLOUD 10P+',
            'ten_s'    => 'SVICLOUD 10S',
            'concierge'=> 'Concierge',
            'guides'   => 'Guides',
            'about'    => 'About',
            'order_tracking' => 'Order Tracking',
            'account'  => 'Account',
            'my-account' => 'Account',
            'submenu_expand'   => 'Expand submenu',
            'submenu_collapse' => 'Collapse submenu',
        ],
    ],


    'footer' => [
        'tagline'   => 'Authorized U.S. distributor · California fulfillment',
        'summary'   => 'Stream top Chinese and global entertainment with California-based fulfillment and warranty support.',
        'badges'    => [
            'ships'     => 'Ships from the USA',
            'warranty'  => '1-Year U.S. Warranty',
            'concierge' => 'English & 中文 Concierge',
        ],
        'benefits'  => [
            'shipping'  => [
                'label'       => '48-Hour U.S. Shipping',
                'description' => 'Most orders leave our California warehouse within two business days.',
            ],
            'dealer'    => [
                'label'       => 'Authorized Dealer',
                'description' => 'Every SVICLOUD device is genuine with U.S. warranty support.',
            ],
            'concierge' => [
                'label'       => 'Bilingual Concierge',
                'description' => 'Live support in English & 中文 to help you install, stream, and troubleshoot.',
            ],
        ],
        'copyright' => '© {{year}} SVICLOUDTVBOX.US · All rights reserved.',
    ],
    'frontpage' => [
        'hero' => [
            'badge'   => 'Authorized U.S. Dealer',
            'title'   => 'Top-of-the-line Chinese TV box, global entertainment ready for any TV',
            'title_lead' => 'Top-of-the-line Chinese TV box',
            'title_separator' => ', ',
            'title_tail' => 'global entertainment ready for any TV',
            'copy'    => 'SVICLOUD packs premium Chinese and international channels, karaoke, and kids favorites into one flagship box—ships fast from the USA with bilingual concierge support.',
            'bullets' => [
                'shipping' => 'Ships from USA',
                'warranty' => '1-Year U.S. Warranty',
                'fees'     => 'No Monthly Fees',
            ],
            'cta' => [
                'primary' => 'Shop 10P+',
                'bundles' => 'View Bundles',
                'compare' => 'Compare Models',
            ],
            'card' => [
                'badge'     => 'Live',
                'headline'  => 'SVICLOUD 10P+ hardware snapshot',
                'timestamp' => 'Latest U.S. batch · July 2024',
                'stat'      => '4GB RAM · 64GB storage',
                'specs'     => [
                    'processor'    => [
                        'label' => 'Processor',
                        'value' => 'Amlogic S928X · Octa-core',
                    ],
                    'connectivity' => [
                        'label' => 'Connectivity',
                        'value' => 'Wi-Fi 6 dual-band + Gigabit LAN',
                    ],
                    'video'        => [
                        'label' => 'High Quality Video Decoding',
                        'value' => 'AV1 decode · 4K HDR',
                    ],
                    'extras'       => [
                        'label' => 'Remote & extras',
                        'value' => 'Bluetooth voice remote · USB 3.0 expansion',
                    ],
                ],
                'footer' => [
                    'shipping' => 'Ships from USA · 48-hour dispatch',
                    'support'  => '1-Year U.S. warranty · Concierge setup',
                ],
            ],
        ],
        'certification' => [
            'badge' => 'Manufacturer Certified',
            'title' => 'SVI.STUDIO Authorized Dealer Certification',
            'lead' => 'SVI.STUDIO authorizes 168 Media Group LLC (SVICLOUDTVBOX.US) to distribute SVICLOUD devices across the Americas with full warranty support.',
            'meta' => [
                'number' => [
                    'label' => 'Authorization No.',
                    'value' => 'US2025092609217',
                ],
                'territory' => [
                    'label' => 'Territory',
                    'value' => 'Americas',
                ],
                'term' => [
                    'label' => 'Effective dates',
                    'value' => 'September 26, 2025 - September 26, 2026',
                ],
            ],
            'footnote' => 'Presented exactly as issued by SVI.STUDIO to 168 Media Group LLC / SVICLOUDTVBOX.US.',
            'cta' => 'Open full certificate',
            'alt' => 'SVI.STUDIO authorized dealer certificate for 168 Media Group LLC',
        ],
        'metrics' => [
            'shipping' => [
                'title' => '48-hour shipping',
                'copy'  => 'Fast fulfillment from U.S. inventory',
            ],
            'concierge' => [
                'title' => 'White-glove setup',
                'copy'  => 'English & 中文 onboarding assistance',
            ],
            'security' => [
                'title' => 'Secure checkout',
                'copy'  => 'SSL encrypted payment and warranty',
            ],
            'dealer' => [
                'title' => 'Top-rated dealer',
                'copy'  => 'Trusted by U.S. SVICLOUD users since 2019',
            ],
        ],
        'feature_grid' => [
            'title'    => 'Why SVICLOUD Beats Generic IPTV Boxes',
            'subtitle' => 'Engineered for crystal-clear 4K sports, drama marathons, and karaoke nights without buffering.',
            'cards'    => [
                'entertainment' => [
                    'title' => 'Complete Entertainment',
                    'copy'  => '4K live channels across Asia & North America, plus bilingual VOD, karaoke, and kids zones curated for families.',
                ],
                'hardware' => [
                    'title' => 'Next-Gen Hardware',
                    'copy'  => 'Latest Amlogic chipset, AV1 decode, and Wi-Fi 6 keep streams stable—even on crowded networks.',
                ],
                'support' => [
                    'title' => 'Local Expert Support',
                    'copy'  => 'U.S.-based SVICLOUD pros walk you through setup, updates, and your favorite channel lineup.',
                ],
            ],
        ],
        'experience' => [
            'badge'      => 'Real People. Real Support.',
            'title'      => 'White-Glove Concierge From Setup To Streaming Night',
            'lead'       => 'We configure, troubleshoot, and update your box so you can just enjoy the content. Every bundle includes priority access to our bilingual SVICLOUD specialists.',
            'card_title' => 'What We Handle For You',
            'services'   => [
                'activation' => 'IPTV activation & renewals',
                'wifi'       => 'Wi-Fi optimization tips',
                'karaoke'    => 'Karaoke playlists & mic pairing',
                'kids'       => 'Kid-safe profiles & timers',
            ],
            'cta' => 'Talk to an expert',
        ],
        'pricing' => [
            'title'    => 'Choose Your SVICLOUD Device',
            'subtitle' => 'Pick the hardware that fits your home. No IPTV bundles—just authentic SVICLOUD boxes shipping from the USA.',
            'cards'    => [
                '10p' => [
                    'badge'    => 'Most Popular',
                    'title'    => 'SVICLOUD 10P+',
                    'interval' => 'device',
                    'copy'     => 'Flagship 4GB RAM / 64GB storage with Kids & Karaoke apps included.',
                    'features' => [
                        'hdr'    => '4K HDR + AV1 decode',
                        'apps'   => 'Kids & Karaoke apps included',
                        'wifi'   => 'AI voice remote + dual-band Wi-Fi',
                    ],
                    'cta'  => 'View 10P+',
                    'meta' => [
                        'shipping'  => '✔ Ships from USA',
                        'warranty'  => '✔ 1-Year U.S. Warranty',
                        'concierge' => '✔ English/中文 support',
                    ],
                ],
                '10s' => [
                    'title'    => 'SVICLOUD 10S',
                    'interval' => 'device',
                    'copy'     => 'Best value with 2GB RAM / 32GB storage—ideal for bedrooms or secondary TVs.',
                    'features' => [
                        'hdr'    => '4K HDR + AV1 decode',
                        'remote' => 'AI voice remote',
                        'bundle' => 'Includes HDMI & power accessories',
                    ],
                    'cta'  => 'View 10S',
                    'cta_style' => 'primary',
                    'meta' => [
                        'shipping' => '✔ Ships from USA',
                        'warranty' => '✔ 1-Year U.S. Warranty',
                        'fees'     => '✔ No monthly device fees',
                    ],
                ],
            ],
        ],
        'concierge' => [
            'personalized_walkthrough' => 'Personalized channel walkthroughs and favorites setup',
            'remote_updates'           => 'Firmware & app updates pushed remotely',
            'community_access'         => 'Access to community events, karaoke playlists, and seasonal sports packages',
        ],
    ],
    'shop' => [
        'hero' => [
            'badge'     => 'Shop',
            'title'     => 'SVICLOUD TV Boxes',
            'subtitle'  => 'Authorized U.S. dealer with fast domestic shipping, 1-year warranty, and English/中文 support.',
            'highlights' => [
                'shipping' => 'Free 2–4 day U.S. shipping',
                'warranty' => '1-year U.S. warranty included',
                'support'  => 'English & 中文 concierge support',
                'fees'     => 'No monthly device or channel fees',
            ],
        ],
        'cards' => [
            'price_label'    => 'One-time purchase',
            'price_note'     => 'All prices in USD · No monthly device fees',
            'best_for_label' => 'Best for',
            'assurance'      => [
                'shipping' => 'Ships from California warehouse',
                'warranty' => 'Includes 1-year U.S. warranty',
                'support'  => 'Bilingual concierge onboarding',
            ],
            '10p' => [
                'title'   => 'SVICLOUD 10P+',
                'lead'    => 'Flagship hardware with exclusive family features and the fastest performance we ship.',
                'button'  => 'View 10P+',
                'badge'   => 'Most popular',
                'best_for' => 'Family rooms, sports fans, karaoke nights',
                'features' => [
                    'ram_storage' => '4GB RAM / 64GB storage with AV1 decode',
                    'apps'        => 'Kids Mode & Karaoke apps included',
                    'remote'      => 'Bluetooth AI voice remote + Wi-Fi 6',
                ],
            ],
            '10s' => [
                'title'   => 'SVICLOUD 10S',
                'lead'    => 'Essential 4K streaming with streamlined hardware for value-conscious homes.',
                'button'  => 'View 10S',
                'badge'   => 'Best value',
                'best_for' => 'Bedrooms, guest suites, casual streaming',
                'features' => [
                    'ram_storage' => '2GB RAM / 32GB storage for everyday streaming',
                    'remote'      => '4K HDR + AV1 decode with voice remote',
                    'ports'       => 'HDMI, USB 3.0, and wired Ethernet included',
                ],
            ],
        ],
    ],
    'compare' => [
        'hero' => [
            'badge'    => 'Compare Models',
            'title'    => 'SVICLOUD 10P+ vs 10S',
            'subtitle' => 'See the hardware, features, and best-use scenarios side-by-side to pick the perfect SVICLOUD for your home.',
        ],
        'differences' => [
            'premium_performance' => [
                'title'       => 'Premium Performance',
                'description' => 'Double the RAM and storage for smoother multitasking and more apps.',
            ],
            'family_entertainment' => [
                'title'       => 'Family Entertainment',
                'description' => 'Exclusive Kids Mode and Karaoke features for the whole family.',
            ],
            'smart_value' => [
                'title'       => 'Smart Value',
                'description' => 'Same 4K picture quality at a more approachable price.',
            ],
        ],
        'products' => [
            '10p' => [
                'lead'    => 'Premium hardware, exclusive family features, and the fastest performance available.',
                'bullets' => [
                    'ram_storage' => '4GB RAM / 64GB storage with AV1 decode',
                    'apps'        => 'Kids Mode and Karaoke apps included',
                    'remote'      => 'Bluetooth AI voice remote + Wi-Fi 6',
                ],
                'cta'     => 'Buy 10P+',
            ],
            '10s' => [
                'lead'    => 'Essential 4K streaming with streamlined hardware for value-conscious homes.',
                'bullets' => [
                    'ram_storage' => '2GB RAM / 32GB storage for everyday streaming',
                    'remote'      => '4K HDR + AV1 decode with voice remote',
                    'ports'       => 'HDMI, USB 3.0, and wired Ethernet included',
                ],
                'cta'     => 'Buy 10S',
            ],
        ],
        'comparison' => [
            'title' => 'Feature Comparison',
            'rows'  => [
                'ram_storage' => [
                    'label' => 'RAM / Storage',
                    'p10p'  => '4GB / 64GB',
                    'p10s'  => '2GB / 32GB',
                ],
                'video_quality' => [
                    'label' => 'Video Quality',
                    'p10p'  => '4K HDR, AV1 decode',
                    'p10s'  => '4K HDR, AV1 decode',
                ],
                'voice_remote' => [
                    'label' => 'Voice Remote',
                    'p10p'  => 'Included',
                    'p10s'  => 'Included',
                ],
                'kids_app' => [
                    'label' => 'Kids App',
                    'p10p'  => 'Exclusive',
                    'p10s'  => 'Not available',
                ],
                'karaoke_mode' => [
                    'label' => 'Karaoke Mode',
                    'p10p'  => 'Exclusive',
                    'p10s'  => 'Not available',
                ],
                'best_for' => [
                    'label' => 'Best For',
                    'p10p'  => 'Families, sports, 4K home theaters',
                    'p10s'  => 'Value / secondary rooms',
                ],
            ],
        ],
        'final_cta' => [
            'badge'   => 'Make Your Choice',
            'title'   => 'Ready to upgrade your home viewing experience?',
            'copy'    => 'Pick the SVICLOUD model that fits your needs and budget.',
            'cta_10p' => 'Buy SVICLOUD 10P+',
            'cta_10s' => 'Buy SVICLOUD 10S',
        ],
    ],
    'about' => [
        'hero' => [
            'badge'         => 'Manufacturer Authorized Dealer',
            'title'         => 'The U.S. home for official SVI.STUDIO devices',
            'lead'          => 'We are 168 Media Group LLC, the California-based team that helps SVICLOUD families stream in minutes—with installation support, local inventory, and bilingual experts.',
            'cta'           => 'Talk to concierge',
            'secondary_cta' => 'Compare devices',
        ],
        'story' => [
            'title' => 'Our story',
            'lead'  => 'From living-room demos to the manufacturer’s official U.S. partner.',
            'body'  => '<p>We began in 2019 by helping friends and neighboring families replace unreliable IPTV boxes with authentic SVICLOUD hardware. Word spread quickly—karaoke nights, sports meetups, and Lunar New Year watch parties all wanted the same thing: a trusted source with real warranty support.</p><p>Investing in local inventory, bilingual onboarding, and responsive concierge service turned us into the go-to SVICLOUD contact on the West Coast. That dedication earned SVI.STUDIO’s formal authorization so every customer gets factory-backed devices delivered fast from California.</p>',
        ],
        'stats' => [
            'orders' => [
                'value' => '6,500+',
                'label' => 'Orders fulfilled since 2019',
            ],
            'concierge_hours' => [
                'value' => '9,800+',
                'label' => 'Concierge support minutes logged',
            ],
            'rating' => [
                'value' => '4.9/5',
                'label' => 'Customer satisfaction across surveys',
            ],
        ],
        'certification' => [
            'badge'  => 'Official documentation',
            'title'  => 'SVI.STUDIO’s dealer approval process',
            'lead'   => 'Our certification confirms we passed SVI.STUDIO’s vetting across operations, post-sale support, and logistics for the Americas region.',
            'points' => [
                'vetting'   => 'Factory audits verified our serial tracking, warranty handling, and customer education practices.',
                'inventory' => 'We maintain U.S. warehouse inventory that ships domestically within 48 hours.',
                'support'   => 'Dedicated English & 中文 specialists help customers with activation, renewals, and updates.',
            ],
        ],
        'timeline' => [
            'badge' => 'Milestones',
            'title' => 'A quick journey from hobby to official partner',
            'lead'  => 'Highlights from our path to becoming SVI.STUDIO’s authorized distributor in America.',
            'items' => [
                '2019' => [
                    'year'  => '2019',
                    'title' => 'Community launch',
                    'copy'  => 'Hosted living-room SVICLOUD demos for Bay Area families seeking reliable Chinese programming.',
                ],
                '2020' => [
                    'year'  => '2020',
                    'title' => 'Concierge support begins',
                    'copy'  => 'Introduced bilingual remote activation, firmware updates, and curated channel tips.',
                ],
                '2022' => [
                    'year'  => '2022',
                    'title' => 'Streamlined U.S. logistics',
                    'copy'  => 'Opened California fulfillment, slashing delivery times and improving warranty turnaround.',
                ],
                '2024' => [
                    'year'  => '2024',
                    'title' => 'Customer community scaling',
                    'copy'  => 'Expanded to nationwide shipments with recurring karaoke, sport, and festival streaming events.',
                ],
                '2025' => [
                    'year'  => '2025',
                    'title' => 'Manufacturer authorized',
                    'copy'  => 'SVI.STUDIO signed our regional certification, granting official dealer status through 2026.',
                ],
            ],
        ],
        'values' => [
            'badge' => 'What to expect from us',
            'title' => 'We lead with authenticity, concierge support, and community',
            'lead'  => 'Every SVICLOUD customer gets genuine hardware, fast help, and shared experiences that keep the box at the center of family time.',
            'authentic' => [
                'title' => 'Genuine hardware only',
                'copy'  => 'Direct-from-factory inventory, serialized and covered by SVI.STUDIO’s one-year U.S. warranty.',
            ],
            'concierge' => [
                'title' => 'Concierge for every box',
                'copy'  => 'English & 中文 onboarding, remote troubleshooting, and proactive tips tailored to each household.',
            ],
            'community' => [
                'title' => 'Built for families & friends',
                'copy'  => 'We host seasonal watch parties, karaoke playlists, and community updates so the fun never stops.',
            ],
        ],
        'concierge' => [
            'badge'          => 'Concierge program',
            'title'          => 'Your streaming concierge is a message away',
            'lead'           => 'Whether you’re unboxing a new SVICLOUD, renewing IPTV access, or prepping a karaoke night, our specialists respond quickly with bilingual support.',
            'points'         => [
                'setup'    => 'White-glove setup & personalization over video or chat',
                'renewals' => 'Hands-on IPTV renewals and firmware updates without the headache',
                'events'   => 'Seasonal watch party schedules, karaoke playlists, and channel guides',
            ],
            'cta_primary'   => 'Schedule a concierge call',
            'cta_secondary' => 'Browse SVICLOUD devices',
        ],
    ],

    'guides' => [
        'hero' => [
            'badge'            => 'Setup & support hub',
            'title'            => 'SVICLOUD TV Box Guides',
            'lead'             => 'Step-by-step installation checklists, app downloads, and troubleshooting in English & 中文.',
            'callouts_headline' => 'Inside this hub',
            'pill_headline'    => 'Step-by-step help for every SVICLOUD',
            'pill_copy'        => 'Follow the checklist, install the must-have apps, and troubleshoot in English & 中文 whenever you need it.',
            'callouts'         => [
                'remote'  => 'Pair the Bluetooth voice remote',
                'network' => 'Optimize Wi-Fi or Ethernet for streaming',
                'apps'    => 'Install Yogurt TV, Kids, Karaoke, and more',
            ],
            'primary_label'   => 'Start installation',
            'secondary_label' => 'Talk to concierge',
        ],
        'highlights' => [
            'badge' => 'Why this guide',
            'title' => 'Everything you need to get streaming fast',
            'items' => [
                'install' => [
                    'title' => '10-minute setup',
                    'copy'  => 'Follow the bilingual checklist to connect hardware, join Wi-Fi, and install apps without guesswork.',
                ],
                'models' => [
                    'title' => 'Works with 10P+ & 10S',
                    'copy'  => 'Covers voice remote pairing, karaoke support, and best practices for every current SVICLOUD model.',
                ],
                'concierge' => [
                    'title' => 'Concierge-ready',
                    'copy'  => 'Need a human? Our English & 中文 concierge team can jump in anytime during setup.',
                ],
            ],
        ],
        'meta' => [
            'anchor_nav_badge'        => 'Guide directory',
            'anchor_nav_title'        => 'Pick where you need help',
            'anchor_nav_description'  => 'Jump straight to setup steps, app installs, troubleshooting, and concierge support.',
        ],
        'nav' => [
            'overview'        => 'Highlights',
            'setup'           => 'Setup guide',
            'apps'            => 'Apps & downloads',
            'post_setup'      => 'After setup',
            'troubleshooting' => 'Troubleshooting',
            'resources'       => 'Resources',
            'support'         => 'Need support',
        ],
        'nav_summaries' => [
            'overview'        => 'See the concierge overview and why these guides matter.',
            'setup'           => 'Connect hardware, choose language, and pair the voice remote.',
            'apps'            => 'Install Yogurt TV, Kids Mode, Karaoke, and other essentials.',
            'post_setup'      => 'Personalize settings and keep your SVICLOUD running smoothly.',
            'troubleshooting' => 'Fix remote pairing, buffering, and Orz installer hiccups.',
            'resources'       => 'Bookmark channel guides, buying advice, and the latest 10-series highlights.',
            'support'         => 'Reach concierge support or review the FAQ options.',
        ],
        'setup' => [
            'badge' => 'Setup checklist',
            'title' => '7 steps to install your SviCloud TV Box',
            'lead'  => 'Walk through each step and keep the remote nearby. Use the voice pairing step if you own a 10P+.',
            'steps' => [
                'connect' => [
                    'title' => 'Connect hardware',
                    'copy'  => 'Attach HDMI to your TV, plug in power, and confirm the front LED turns on.',
                ],
                'language' => [
                    'title' => 'Pick your language',
                    'copy'  => 'Use the arrow keys to highlight your preferred language, press OK, then Next.',
                ],
                'disclaimer' => [
                    'title' => 'Accept the disclaimer',
                    'copy'  => 'Select Continue to proceed. This confirms you understand the on-screen terms.',
                ],
                'remote' => [
                    'title' => 'Pair the voice remote',
                    'copy'  => 'Hold <kbd>VOL-</kbd> and <kbd>VOL+</kbd> together until the pairing success message appears (10P+ only).',
                ],
                'time' => [
                    'title' => 'Set time & region',
                    'copy'  => 'Choose the correct time zone so live program guides display accurate schedules.',
                ],
                'network' => [
                    'title' => 'Join Wi-Fi or Ethernet',
                    'copy'  => 'Pick "Set up WiFi", enter your password, or plug in Ethernet for the most stable stream.',
                ],
                'apps' => [
                    'title' => 'Install SviCloud apps',
                    'copy'  => 'Open the Orz installer, type 8989c.cc (redirects to 6868c.cc), then download Yogurt TV, Yogurt Kids, Cherry TV, and Karaoke KTV.',
                ],
            ],
            'note_title' => 'Photo walkthrough coming soon',
            'note_copy'  => 'We are capturing HDMI hookup, language selection, remote pairing, Wi-Fi onboarding, and Orz installer screenshots to embed here.',
        ],
        'apps' => [
            'badge' => 'Essential apps',
            'title' => 'Must-install SviCloud apps',
            'lead'  => 'Download these first inside Orz to unlock live TV, karaoke, and kid-friendly content.',
            'items' => [
                'yogurt' => [
                    'title' => 'Yogurt TV / LUCA TV',
                    'copy'  => 'Global live TV channels, movies, and dramas with fast search and category filters.',
                ],
                'kids' => [
                    'title' => 'Yogurt Kids / LUCA Kids',
                    'copy'  => 'Curated educational shows, songs, and interactive games for families.',
                ],
                'karaoke' => [
                    'title' => 'We Fun / 閤家歡 Karaoke',
                    'copy'  => 'In-home karaoke playlists with on-screen lyrics and duet-friendly queues.',
                ],
                'regional' => [
                    'title' => 'Regional live apps',
                    'copy'  => 'Malaysia Live, Indonesia Live, and other localized channel packs for overseas viewers.',
                ],
                'cherry' => [
                    'title' => 'Cherry TV (Adults)',
                    'copy'  => 'Password-protected adult content—only enable it if you intend to use it.',
                ],
            ],
        ],
        'post_setup' => [
            'badge' => 'After setup',
            'title' => 'Keep exploring once you are online',
            'lead'  => 'Install your favorite apps, fine-tune video and audio, and make SVICLOUD feel like home.',
            'items' => [
                'explore' => [
                    'title' => 'Browse the home screen',
                    'copy'  => 'Find Live TV, VOD, and settings rows to personalize your launcher experience.',
                ],
                'install' => [
                    'title' => 'Add trusted Android apps',
                    'copy'  => 'Install streaming services like Netflix, Disney+, or YouTube from sources you trust.',
                ],
                'tune' => [
                    'title' => 'Adjust audio & display',
                    'copy'  => 'Open Settings to fine-tune subtitles, display modes, and network preferences.',
                ],
            ],
        ],
        'troubleshooting' => [
            'badge' => 'Troubleshooting',
            'title' => 'Quick fixes for common roadblocks',
            'lead'  => 'Try these remedies before contacting concierge. They solve most launch-day hiccups.',
            'items' => [
                'remote' => [
                    'title' => 'Remote not pairing',
                    'copy'  => 'Remove the batteries, reinsert them, stand close to the box, and press <kbd>VOL-</kbd> + <kbd>VOL+</kbd> again until you see the success banner.',
                ],
                'streaming' => [
                    'title' => 'Stream buffering or blurry',
                    'copy'  => 'Restart your router, move closer to Wi-Fi, or plug in Ethernet for the most stable connection.',
                ],
                'orz' => [
                    'title' => 'Orz installer not loading',
                    'copy'  => 'Double-check the URL 8989c.cc, then reboot the box. If it still fails, verify your network connection.',
                ],
            ],
        ],
        'resources' => [
            'badge' => 'More resources',
            'title' => 'Guides worth bookmarking',
            'lead'  => 'Explore channel coverage, model recommendations, and 10-series feature breakdowns after you finish setup.',
            'items' => [
                'why' => [
                    'title' => 'Why buy from SVICLOUDTVBOX.US',
                    'copy'  => 'See why the U.S. concierge store is the fastest, safest way to get SVICLOUD 10-series hardware.',
                ],
                'channels' => [
                    'title' => 'Live channels & apps guide',
                    'copy'  => 'Preview Yogurt TV categories, on-demand libraries, and the must-install apps for every household.',
                ],
                'features' => [
                    'title' => 'Top SVICLOUD 10-series features',
                    'copy'  => 'Look under the hood of the 10P+ and 10S hardware, codecs, and family entertainment extras.',
                ],
                'which' => [
                    'title' => 'Which SVICLOUD model fits me?',
                    'copy'  => 'Compare 10P+ and 10S use cases so you can pick the right box the first time.',
                ],
            ],
            'articles' => [
                'shared' => [
                    'badge'             => 'Resource guide',
                    'back_label'        => 'Back to resources',
                    'more_guides_title' => 'More resource guides',
                ],
                'why' => [
                    'title'   => 'Why buy from SVICLOUDTVBOX.US',
                    'lead'    => 'Order direct from our California warehouse to guarantee genuine SVICLOUD 10-series hardware, concierge setup, and a 1-year U.S. warranty.',
                    'updated' => 'Updated January 15, 2025',
                    'sections' => [
                        'fulfillment' => [
                            'heading' => 'Certified U.S. inventory with 48-hour shipping',
                            'body'    => '<p>All SVICLOUD 10P+ and 10S boxes leave our California warehouse with tamper-evident packaging, verified serial numbers, and the latest firmware. Orders placed before 12pm PT ship within two business days.</p>
<ul>
  <li>Signature-required UPS and USPS options keep deliveries secure.</li>
  <li>Domestic inventory eliminates customs delays and grey-market surprises.</li>
  <li>Every unit includes fresh accessories, the Bluetooth voice remote, and a U.S.-ready power adapter.</li>
</ul>',
                        ],
                        'warranty' => [
                            'heading' => '1-year U.S. warranty and hassle-free returns',
                            'body'    => '<p>Your purchase includes a 1-year U.S. warranty handled by our concierge team. If anything fails under normal use, we coordinate diagnostics, replacements, or parts from California—no overseas shipping required.</p>
<ul>
  <li>30-day satisfaction window for returns or exchanges on unopened units.</li>
  <li>Hardware repairs or replacements processed domestically for faster turnaround.</li>
  <li>Firmware updates and troubleshooting support provided throughout the warranty period.</li>
</ul>',
                        ],
                        'concierge' => [
                            'heading' => 'Bilingual concierge for setup and lifetime support',
                            'body'    => '<p>Need help installing apps or pairing the voice remote? Our bilingual concierge team responds in English or 中文 within one business day. Share your order number and questions via <a href="{{contact_url}}">the concierge form</a>.</p>
<ul>
  <li>Live guidance for unboxing, network setup, and Orz installer downloads.</li>
  <li>Screen recordings and step-by-step instructions tailored to your home.</li>
  <li>Ongoing help for channel refreshes, app updates, and troubleshooting.</li>
</ul>',
                        ],
                        'billing' => [
                            'heading' => 'Secure checkout with transparent pricing',
                            'body'    => '<p>Checkout is protected by SSL and supports Visa, Mastercard, American Express, and PayPal. Sales tax is calculated at checkout for states where we are required to collect it.</p>
<ul>
  <li>Free U.S. shipping on every SVICLOUD box—no surprise handling fees.</li>
  <li>No monthly subscription or hidden renewal charges—hardware is a one-time purchase.</li>
  <li>Receipts and tracking numbers are emailed the moment your order leaves the warehouse.</li>
</ul>',
                        ],
                        'next_steps' => [
                            'heading' => 'What happens after you order',
                            'body'    => '<p>After checkout you will receive a confirmation email, a shipping notification with tracking, and concierge tips for first-time setup. Start with our <a href="{{setup_url}}">10-minute setup guide</a>, then reach out via <a href="{{contact_url}}">the concierge form</a> anytime you need a hand.</p>',
                        ],
                    ],
                ],
                'channels' => [
                    'title'   => 'Live channels & apps guide',
                    'lead'    => 'Preview Yogurt TV, LUCA Kids, and Karaoke coverage so you know exactly what streams are available before you plug in.',
                    'updated' => 'Updated January 15, 2025',
                    'sections' => [
                        'live' => [
                            'heading' => '2000+ live channels across regions',
                            'body'    => '<p>Yogurt TV streams 2,000+ live channels spanning Hong Kong, Taiwan, Mainland China, Southeast Asia, Japan, Korea, North America, and Europe. Each region includes news, variety, sports, and premium movie feeds in high-definition.</p>
<ul>
  <li>Quick filters jump between Cantonese, Mandarin, and English channel groups.</li>
  <li>Dedicated sports hubs cover NBA, MLB, soccer, combat sports, and seasonal events.</li>
  <li>24/7 Cantonese, Mandarin, and bilingual lifestyle channels keep every household entertained.</li>
</ul>',
                        ],
                        'vod' => [
                            'heading' => 'On-demand dramas, variety shows, and replay',
                            'body'    => '<p>The VOD library refreshes daily with new dramas, variety programs, kids animation, films, documentaries, and replay content. Recent episodes are tagged with catch-up windows so you never miss prime-time releases.</p>
<ul>
  <li>Binge entire drama seasons with multiple audio tracks and subtitle options.</li>
  <li>Replay variety shows and news segments within hours of airing.</li>
  <li>Dedicated kids, education, and documentary shelves simplify discovery.</li>
</ul>',
                        ],
                        'apps' => [
                            'heading' => 'Must-install apps for every household',
                            'body'    => '<p>Install the essentials first: Yogurt TV / LUCA TV for live content, Yogurt Kids for family-safe programming, WeFun Karaoke for lyrics-on-screen singing, and regional packs like Malaysia Live. Follow the steps inside the <a href="{{apps_url}}">Apps & downloads guide</a> to grab each one from Orz.</p>',
                        ],
                        'updates' => [
                            'heading' => 'Request the full channel spreadsheet',
                            'body'    => '<p>Need the complete list of channels with logos and language tags? Request the latest spreadsheet from concierge via <a href="{{contact_url}}">our support form</a>. We refresh the document whenever new channels are added.</p>',
                        ],
                        'network' => [
                            'heading' => 'Streaming tips for smooth playback',
                            'body'    => '<p>For the smoothest streams, connect via Ethernet or Wi-Fi 6. If you encounter buffering, review the tips in our <a href="{{troubleshooting_url}}">troubleshooting guide</a>, reboot your router, or switch to wired networking.</p>',
                        ],
                    ],
                ],
                'features' => [
                    'title'   => 'Top SVICLOUD 10-series features',
                    'lead'    => 'See what makes the SVICLOUD 10P+ and 10S stand out—from flagship chipsets to bilingual entertainment experiences.',
                    'updated' => 'Updated January 15, 2025',
                    'sections' => [
                        'hardware' => [
                            'heading' => 'Flagship hardware ready for 4K HDR',
                            'body'    => '<p>SVICLOUD 10P+ uses the Amlogic S928X octa-core processor with 4GB RAM and 64GB storage, while the 10S pairs a smooth quad-core CPU with 4GB RAM and 32GB storage. Both deliver 4K HDR output, HDMI 2.1, dual-band Wi-Fi 6, and Gigabit Ethernet.</p>
<ul>
  <li>USB 3.0 and USB 2.0 ports support external drives, cameras, and accessories.</li>
  <li>Optical/SPDIF and HDMI eARC provide flexible audio for home theaters.</li>
  <li>Quiet chassis and smart cooling keep playback stable during long marathons.</li>
</ul>',
                        ],
                        'streaming' => [
                            'heading' => 'AV1 decoding and adaptive streaming',
                            'body'    => '<p>Both models decode AV1, HEVC, and VP9 for sharper streams at lower bitrates. Adaptive buffering keeps live sports and drama replays fluid, even on busy household networks.</p>
<ul>
  <li>Automatic frame-rate matching reduces judder on 24p dramas and 60fps sports.</li>
  <li>Cloud CDN acceleration prioritizes North American routes for overseas content.</li>
  <li>Frequent firmware updates add new codecs, UI polish, and performance tuning.</li>
</ul>',
                        ],
                        'remote' => [
                            'heading' => 'Voice remote built for quick navigation',
                            'body'    => '<p>The Bluetooth voice remote features far-field microphones, number pad shortcuts, and dedicated media keys. Pair it in seconds using the volume buttons, then launch apps or search channels with voice commands.</p>
<ul>
  <li>Programmable power and volume keys integrate with most TVs and soundbars.</li>
  <li>Backlit buttons and an ergonomic grip make nighttime navigation easy.</li>
  <li>OTA remote firmware updates keep responsiveness snappy.</li>
</ul>',
                        ],
                        'family' => [
                            'heading' => 'Entertainment for every generation',
                            'body'    => '<p>SVICLOUD bundles more than live TV. Yogurt Kids locks into age-appropriate zones, WeFun Karaoke provides lyrics with scoring, and Cherry TV stays PIN-protected for adults.</p>
<ul>
  <li>Create kid profiles so the launcher only shows curated education and animation.</li>
  <li>Queue songs, duet modes, and weekly new releases for karaoke gatherings.</li>
  <li>Regional add-on apps expand Cantonese, Mandarin, Malay, and English programming.</li>
</ul>',
                        ],
                        'service' => [
                            'heading' => 'Lifetime concierge service included',
                            'body'    => '<p>Every SVICLOUD device includes lifetime concierge help from our U.S.-based team. Contact us via <a href="{{contact_url}}">the concierge form</a> for setup help, firmware announcements, and personalized recommendations.</p>',
                        ],
                    ],
                ],
                'models' => [
                    'title'   => 'Which SVICLOUD model fits me?',
                    'lead'    => 'Use this guide to decide between SVICLOUD 10P+ and 10S based on performance, ports, and the way your family watches.',
                    'updated' => 'Updated January 15, 2025',
                    'sections' => [
                        'summary' => [
                            'heading' => 'Quick comparison at a glance',
                            'body'    => '<table class="guides-article__table">
  <thead>
    <tr>
      <th>Feature</th>
      <th>SVICLOUD 10P+</th>
      <th>SVICLOUD 10S</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Processor &amp; memory</td>
      <td>Amlogic S928X · 4GB RAM · 64GB storage</td>
      <td>Quad-core CPU · 4GB RAM · 32GB storage</td>
    </tr>
    <tr>
      <td>Connectivity</td>
      <td>Wi-Fi 6 dual-band · Gigabit LAN · USB 3.0 + USB 2.0</td>
      <td>Wi-Fi 6 dual-band · Gigabit LAN · dual USB 2.0</td>
    </tr>
    <tr>
      <td>Audio outputs</td>
      <td>HDMI 2.1 · Optical SPDIF</td>
      <td>HDMI 2.1 · 3.5mm AUX</td>
    </tr>
    <tr>
      <td>Best for</td>
      <td>Home theaters, heavy app installs, karaoke setups</td>
      <td>Everyday streaming, compact spaces, secondary TVs</td>
    </tr>
  </tbody>
</table>
<p>Both models stream the same Yogurt TV apps and receive identical firmware updates. Choose based on storage, ports, and how many accessories you plug in.</p>',
                        ],
                        'ten_p' => [
                            'heading' => 'Choose SVICLOUD 10P+ if you need…',
                            'body'    => '<p>Pick the flagship 10P+ when you want maximum performance and expandability.</p>
<ul>
  <li>You run a home theater with receivers, soundbars, or optical audio gear.</li>
  <li>You install lots of third-party APKs or store local files on external drives.</li>
  <li>You host karaoke nights and prefer the fastest processor for lyric rendering.</li>
</ul>',
                        ],
                        'ten_s' => [
                            'heading' => 'Choose SVICLOUD 10S if you prefer…',
                            'body'    => '<p>The 10S keeps the same channel lineup in a smaller, lower-priced package.</p>
<ul>
  <li>Secondary TVs, bedrooms, dorms, or apartments where space is limited.</li>
  <li>Families who primarily watch live TV and VOD with a few extra apps.</li>
  <li>Anyone who wants concierge setup help without paying for extra ports.</li>
</ul>',
                        ],
                        'next_steps' => [
                            'heading' => 'Next steps once you pick a model',
                            'body'    => '<p>Whichever box you choose, follow the <a href="{{setup_url}}">setup guide</a>, install essentials from the <a href="{{apps_url}}">apps guide</a>, and save <a href="{{contact_url}}">the concierge link</a> for future questions.</p>',
                        ],
                    ],
                ],
            ],
        ],
        'detail' => [
            'back_to_hub'  => 'Back to Guides Hub',
            'more_guides'  => 'Explore other guides',
            'open_section' => 'Open guide',
        ],
        'support' => [
            'badge' => 'Need help?',
            'title' => 'Bilingual concierge is ready',
            'copy'  => 'Share your order number or installation question and we will reply in English or 中文 within one business day.',
            'primary_label'   => 'Contact concierge',
            'secondary_label' => 'Read the FAQ',
        ],
    ],

'product' => [

        'hero' => [
            'subtitle' => 'Shipped from the U.S. with concierge onboarding in English & Chinese.',
            'detail'   => 'Secure checkout • Free U.S. shipping • English/中文 support',
        ],
        'highlights' => [
            'inventory' => 'Certified U.S. inventory & warranty',
            'concierge' => 'Bilingual concierge setup support',
            'no_fees'   => 'No monthly fees or hidden renewals',
        ],
    ],
];
