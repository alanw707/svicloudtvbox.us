<?php
/** Hardware purchase terms; no claims about third-party licensing or device preloads. */
declare(strict_types=1);
if (!defined('ABSPATH')) { exit; }

function svic_hardware_notice_copy(?string $locale = null): array {
    $locale = $locale ?? (function_exists('svic_current_locale') ? svic_current_locale() : get_locale());
    $copy = [
        'en_US' => [
            'title' => 'Hardware-only purchase & third-party software',
            'body' => 'SVICLOUDTVBOX.US sells hardware units and accessories only. We do not provide or endorse third-party streaming software, and we do not guarantee its availability or performance or the uptime of external servers. Your purchase does not include a subscription or grant rights to third-party services or media. Buyers are solely responsible for the third-party software and apps they choose to run, including obtaining all required licenses, permissions and subscriptions and complying with applicable law.',
            'rights' => 'These terms do not limit your statutory consumer rights or exclude responsibilities that cannot lawfully be excluded. Hardware warranty and return terms remain as stated in our store policies.',
            'link' => 'Read the hardware and lawful-use terms',
        ],
        'zh_TW' => [
            'title' => '僅販售硬體與第三方軟體責任',
            'body' => 'SVICLOUDTVBOX.US 僅販售硬體設備與配件。我們不提供或背書任何第三方串流軟體，也不保證其可用性、效能或外部伺服器的持續運作。購買硬體不包含訂閱，也不授予第三方服務或媒體的使用權。買家須自行負責其選擇執行的第三方軟體與應用程式，包括取得所有必要的授權、許可與訂閱，並遵守適用法律。',
            'rights' => '本條款不限制消費者依法享有的權利，也不排除依法不得免除的責任。硬體保固與退貨條件仍依本店公布的政策辦理。',
            'link' => '閱讀硬體與合法使用條款',
        ],
        'zh_CN' => [
            'title' => '仅销售硬件与第三方软件责任',
            'body' => 'SVICLOUDTVBOX.US 仅销售硬件设备与配件。我们不提供或认可任何第三方流媒体软件，也不保证其可用性、性能或外部服务器的持续运行。购买硬件不包含订阅，也不授予第三方服务或媒体的使用权。买家须自行负责其选择运行的第三方软件与应用程序，包括取得所有必要的授权、许可与订阅，并遵守适用法律。',
            'rights' => '本条款不限制消费者依法享有的权利，也不排除依法不得免除的责任。硬件保修与退货条件仍按本店公布的政策办理。',
            'link' => '阅读硬件与合法使用条款',
        ],
    ];
    return $copy[$locale] ?? $copy['en_US'];
}

function svic_render_hardware_notice(bool $include_link = true): void {
    $copy = svic_hardware_notice_copy();
    echo '<aside class="hardware-notice"><h2 class="h4">' . esc_html($copy['title']) . '</h2><p>' . esc_html($copy['body']) . '</p><p class="text-small">' . esc_html($copy['rights']) . '</p>';
    if ($include_link) {
        $url = home_url('/legal-disclaimer/');
        if (function_exists('svic_url_with_lang')) { $url = svic_url_with_lang($url); }
        echo '<p><a href="' . esc_url($url) . '">' . esc_html($copy['link']) . '</a></p>';
    }
    echo '</aside>';
}
