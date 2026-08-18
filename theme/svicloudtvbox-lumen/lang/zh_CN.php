<?php
/**
 * SVICLOUD Lumen Theme translations (Chinese - Simplified)
 */

$base = include __DIR__ . '/zh_TW.php';

$overrides = [
    'core' => [
        'cart' => [
            'added_notice' => '已加入您的购物车',
            'view_cart'    => '查看购物车',
        ],
    ],
    'announcement' => [
        'message' => '目前商品暂时缺货，新库存即将到来！',
        'cta'     => '',
        'cta_url' => '',
    ],

    'promotion' => [
        'july_4' => [
            'aria_label'          => '美国国庆优惠',
            'eyebrow'             => '7 月 4 日假期优惠',
            'message'             => '庆祝假期，SVICLOUD 商品省 5%。',
            'offers_label'        => '7 月 4 日适用优惠',
            'offer_10p'           => 'SVICLOUD 全线商品省 5%',
            'offer_10s'           => 'SVICLOUD 全线商品省 5%',
            'code_label'          => '代码 {{code}}',
            'cta'                 => '选购优惠',
            'coupon_not_active'   => 'JULY4 适用日期为 2026 年 7 月 4 日。',
            'coupon_not_eligible' => 'JULY4 适用于 SVICLOUD 商品。',
        ],
    ],

    'recent_shipments' => [
        'badge'                  => '近期出货',
        'aria_label'             => '近期出货与预估送达时程',
        'disclaimer'             => '依据近期出货标签与承运商预估时程整理。时效仍会因目的地、周末与假期而不同。',
        'item'                   => '{{location}} · 约 {{time}}',
        'item_title'             => '承运服务：{{service}}',
        'location_city_state'    => '{{city}}，{{state}}',
        'estimated_day_singular' => '1 天',
        'estimated_day_plural'   => '{{count}} 天',
    ],

    'header' => [
        'nav' => [
            'home'     => '首页',
            'compare'  => '比较机型',
            'compare_evpad'  => '小云电视盒 10P+ vs EVPAD 10 Pro',
            'compare_ubox12' => '小云电视盒 10P+ vs 安博电视盒 12 代',
            'blog'     => '博客',
            'shop'     => '选购',
            'faq'      => '常见问题',
            'fifteen_p' => '小云 15P 电视盒',
            'ten_p'    => '小云电视盒 10P+ 旗舰款',
            'ten_s'    => '小云电视盒 10S 轻巧款',
            'concierge'=> '礼宾客服',
            'legal'    => '法律声明',
            'guides'   => '使用指南',
            'about'    => '关于我们',
            'return_policy' => '退换货政策',
            'order_tracking' => '订单查询',
            'account'  => '会员中心',
            'my-account' => '会员中心',
            'submenu_expand'   => '展开次选单',
            'submenu_collapse' => '收合次选单',
            'skip_content'      => '跳至主要内容',
            'mobile_navigation' => '移动版导航',
            'open_navigation'   => '打开导航',
            'close_navigation'  => '关闭导航',
        ],
    ],
    'frontpage' => [
        'hero' => [
            'store_rating' => [
                'label' => 'Google 商店评分',
                'note'  => 'Google 验证商店反馈',
                'aria'  => 'SVICLOUDTVBOX.US 的 Google 商店评分 5.0 满分 5 分',
            ],
        ],
    ],
    'shop' => [
        'hero' => [
            'badge'    => '选购',
            'title'    => '小云电视盒',
            'subtitle' => '以 US$288 预订规格已确认的小云 15P（原价 US$379），或选购现售 10P+、10S 与配件。',
        ],
        'cards' => [
            '15p' => [
                'title'   => '小云 15P 电视盒',
                'lead'    => '搭载 Android 14、Amlogic S905Y5、4 GB DDR3、64 GB eMMC、双频 Wi-Fi 6、蓝牙 5.4 与 4K HDR。',
                'button'  => '预订 15P',
                'badge'   => '接受预订',
                'best_for' => 'Android 14、Wi-Fi 6、蓝牙 5.4 与 4K 解码支持',
                'price_label' => '预订价',
                'price_tbc'   => '接受预订',
                'price_note'  => '发货日期尚未公布',
                'image_alt'   => '小云 15P Android 14 预订图',
                'image_tbc'   => '小云 15P 产品图片',
                'features' => [
                    'hardware' => 'Amlogic S905Y5 + Android 14',
                    'apps'     => '4 GB DDR3 + 64 GB eMMC',
                    'remote'   => 'Wi-Fi 6 + 蓝牙 5.4 + AV1 解码',
                ],
                'assurance' => [
                    'shipping' => '接受预订',
                    'warranty' => '特价 US$288 · 原价 US$379',
                    'support'  => '发货日期尚未公布',
                ],
            ],
            '10p' => [
                'features' => [
                    'ram_storage' => 'Android 12 + 4GB 内存 / 64GB 存储与 AV1 解码',
                ],
            ],
            '10s' => [
                'features' => [
                    'ram_storage' => 'Android 12 + 2GB 内存 / 32GB 存储，满足日常播放',
                ],
            ],
            'rating_label' => 'Google 平均评分',
            'rating_aria'  => 'SVICLOUDTVBOX.US 的 Google 商店平均评分',
            'rating_note'  => 'Google 验证商店反馈',
            'product_rating_label' => '产品评分',
            'product_rating_count' => '%d 条产品评价',
            'product_rating_aria'  => '产品评分 {{rating}} 满分 5 分，来自 {{count}} 条评价',
            'remote' => [
                'title'    => '10P+ 蓝牙遥控器',
                'lead'     => '小云 10P+ 官方替换或备用遥控器，由美国现货发货。',
                'button'   => '查看遥控器',
                'badge'    => '配件',
                'best_for' => '需要替换遥控器或多人共用的家庭',
                'features' => [
                    'bluetooth' => '蓝牙 5.0 配对，最远约 10 米',
                    'voice'     => '内置麦克风，支持语音搜索',
                    'controls'  => '通用电视电源、音量与输入控制',
                ],
            ],
        ],
    ],
    'cart_page' => [
        'quantity' => [
            'decrease' => '减少数量',
            'increase' => '增加数量',
        ],
    ],
    'checkout_page' => [
        'payment' => [
            'unavailable_action' => '目前无可用付款方式',
        ],
    ],
    'order_thankyou' => [
        'review' => [
            'badge' => 'Google 评价邀请',
            'copy'  => '如果您的订单符合 Google Customer Reviews 条件，商品送达后 Google 可能会发送一份简短问卷。这些真实评价有助于提升我们在 Google 上的商家信任度。',
        ],
        'next' => [
            'badge' => '下单后建议',
            'title' => '先收藏安装指南，也把支援与比较页留好',
            'copy'  => '多数客户在下单后会先做三件事：收藏安装指南、把比较页分享给家人，以及保留礼宾客服链接。',
            'cards' => [
                'setup' => [
                    'title' => '先打开安装指南',
                    'copy'  => '现在先收藏中英双语快速安装教学，收到机器后会更容易上手。',
                    'cta'   => '查看安装指南',
                ],
                'share' => [
                    'title' => '之后可能还需要第二台？',
                    'copy'  => '把比较页分享给家人，之后要在 10P+ 与 10S 之间做选择会更轻松。',
                    'cta'   => '打开比较页',
                ],
                'support' => [
                    'title' => '保留礼宾客服链接',
                    'copy'  => '如果对发货、安装或配件有问题，英文与中文客服都能协助你。',
                    'cta'   => '联系礼宾客服',
                ],
                'product_review' => [
                    'title' => '收到后为产品留下评价',
                    'copy'  => '收到并实际使用后，欢迎留下已购买产品评价，帮助之后的买家参考。',
                    'cta'   => '评价产品',
                ],
                'review' => [
                    'title' => '分享您的 Google 评价',
                    'copy'  => '如果整体体验顺利，您的公开 Google 评价能帮助其他美国家庭安心选购。',
                    'cta'   => '留下 Google 评价',
                ],
            ],
        ],
    ],
    'guides' => [
        'hero' => [
            'badge'            => '安装与支援中心',
            'title'            => '小云电视盒指南中心',
            'lead'             => '提供中英文安装清单与疑难排解，快速完成小云电视盒开箱。',
            'callouts_headline' => '指南重点',
            'pill_headline'    => '分步教学，10P+ / 10S 全涵盖',
            'pill_copy'        => '依照清单完成设定、调整主要选项，遇到问题也有中英双语支援。',
            'callouts'         => [
                'remote'  => '配对蓝牙语音遥控',
                'network' => '最佳化 Wi-Fi 或网路线连线',
                'apps'    => '了解内容合规守则',
            ],
            'primary_label'   => '开始安装',
            'secondary_label' => '联系礼宾客服',
        ],
        'highlights' => [
            'badge' => '为什么选这份指南',
            'title' => '10 分钟完成设定，一次搞定',
            'items' => [
                'install' => [
                    'title' => '10 分钟搞定',
                    'copy'  => '依照双语清单完成接线与连网设定，不用猜测。',
                ],
                'models' => [
                    'title' => '支援 10P+ 与 10S',
                    'copy'  => '涵盖语音遥控配对、KTV 功能与目前所有小云型号的最佳做法。',
                ],
                'concierge' => [
                    'title' => '随时有人协助',
                    'copy'  => '需要真人支援吗？英文与中文礼宾客服可在安装过程中即时接手。',
                ],
            ],
        ],
        'meta' => [
            'anchor_nav_badge'       => '指南索引',
            'anchor_nav_title'       => '选择你需要的协助',
            'anchor_nav_description' => '直接跳到安装步骤、内容合规提示、疑难排解或客服支援。',
        ],
        'nav' => [
            'overview'        => '指南亮点',
            'setup'           => '安装流程',
            'apps'            => 'App 安装教学',
            'post_setup'      => '安装后设定',
            'troubleshooting' => '疑难排解',
            'resources'       => '延伸资源',
            'support'         => '寻求支援',
            'faq'             => '常见问题',
        ],
        'nav_summaries' => [
            'overview'        => '快速了解重点与礼宾支援。',
            'setup'           => '完成接线、语言选择与语音遥控配对。',
            'apps'            => '学会从可信来源安装 App，并完成首次打开。',
            'post_setup'      => '个人化设定，让小云长期稳定运作。',
            'troubleshooting' => '修复遥控、缓冲与网路等常见问题。',
            'resources'       => '收藏频道指南、选购建议与最新 10 系列亮点。',
            'support'         => '联络礼宾客服或查看常见问题。',
        ],
        'setup' => [
            'badge' => '安装清单',
            'title' => '7 个步骤完成小云电视盒设定',
            'lead'  => '逐步完成以下流程，遥控器请随手放在旁边。拥有 10P+ 的用户别忘了语音配对步骤。',
            'steps' => [
                'connect' => [
                    'title' => '连接硬体',
                    'copy'  => '接上 HDMI 与电源，确认机身前方指示灯亮起。',
                ],
                'language' => [
                    'title' => '选择系统语言',
                    'copy'  => '使用方向键选择语言，按下 OK 后再按 Next 进入下一步。',
                ],
                'disclaimer' => [
                    'title' => '同意免责声明',
                    'copy'  => '选择 Continue 确认并继续。',
                ],
                'remote' => [
                    'title' => '配对语音遥控器',
                    'copy'  => '同时按住 <kbd>VOL-</kbd> 与 <kbd>VOL+</kbd> 直到萤幕显示配对成功（仅 10P+ 需要）。',
                ],
                'time' => [
                    'title' => '设定时区与时间',
                    'copy'  => '选择正确时区，电子节目表才会显示准确播放时间。',
                ],
                'network' => [
                    'title' => '连上 Wi-Fi 或网路线',
                    'copy'  => '选择 Set up WiFi 输入密码，或插上 Ethernet 取得更稳定的串流品质。',
                ],
                'apps' => [
                    'title' => '检视内容合规提醒',
                    'copy'  => '安装任何服务前，请先确认来源合法并仅透过官方管道下载，确保遵守所在地的版权与使用条款。',
                ],
            ],
            'note_title' => '即将加入完整截图教学',
            'note_copy'  => '我们正在拍摄 HDMI 接线、语言选择、遥控器配对与 Wi-Fi 设定画面，完成后会于此更新。',
        ],
        'apps' => [
            'badge' => 'App 安装',
            'title' => '如何在小云电视盒安装 App',
            'lead'  => '请依照以下步骤在小云电视盒安装或重新安装 Yogurt TV。若从其他来源安装名称相似的 App，可能会要求 VIP 会员，也可能不是正确版本。',
            'items' => [
                'internet' => [
                    'title' => '1. 确认电视盒已连上网络',
                    'copy'  => '打开「设置 → 网络」，确认 Wi-Fi 或 Ethernet 显示已连接。若网络不稳，请先重启路由器或改用网线，再安装 Yogurt TV。',
                ],
                'installer' => [
                    'title' => '2. 打开 Orz Browser',
                    'copy'  => '从主画面打开 Orz Browser。如果没有马上看到，请到 Apps 或所有应用程式清单中寻找。',
                ],
                'search' => [
                    'title' => '3. 输入安装网址',
                    'copy'  => '在 Orz Browser 的网址列输入 8989c.cc 并打开页面。请完全照着输入。',
                ],
                'install' => [
                    'title' => '4. 找到并安装 Yogurt TV',
                    'copy'  => '在 App 页面找到 Yogurt TV，然后下载并安装。请不要从其他 App Store 随便安装名称相似的 App。',
                ],
                'open' => [
                    'title' => '5. 如有权限提示请允许',
                    'copy'  => '如果 Android 询问是否允许从此来源安装，请选择允许并继续。请等待安装完整完成后再拔电源。',
                ],
                'support' => [
                    'title' => '6. 打开 Yogurt TV 并测试',
                    'copy'  => '安装完成后打开 Yogurt TV。如果仍要求 VIP 会员、无法打开或出现错误，请传清楚的电视画面照片与订单编号给我们，以便确认是否安装到正确 App。',
                ],
            ],
        ],
        'post_setup' => [
            'badge' => '安装完成后',
            'title' => '上线后别错过这些设定',
            'lead'  => '设定喜爱的应用程式、调整画面与音讯，让小云成为全家的娱乐中心。',
            'items' => [
                'explore' => [
                    'title' => '熟悉主画面',
                    'copy'  => '找到直播、随选影音与设定列，打造自己的首页。',
                ],
                'install' => [
                    'title' => '加装可信赖的 Android App',
                    'copy'  => '如 Netflix、Disney+、YouTube 等常用服务，记得选择可信来源。',
                ],
                'tune' => [
                    'title' => '调整音讯与显示',
                    'copy'  => '打开设定调整字幕、显示模式与网路偏好。',
                ],
            ],
        ],
        'troubleshooting' => [
            'badge' => '疑难排解',
            'title' => '常见问题快速解法',
            'lead'  => '联系礼宾服务前，请先试试以下步骤，能解决绝大多数安装与串流问题。',
            'items' => [
                'remote' => [
                    'title' => '遥控器无法配对',
                    'copy'  => '取出电池，等待 10 秒后重新装回，靠近机身 1 米内，同时按住 <kbd>VOL-</kbd> + <kbd>VOL+</kbd> 3 秒，直到 LED 闪烁并看到配对成功提示。若仍失败，请更换新的 AAA 电池。',
                ],
                'streaming' => [
                    'title' => '画面模糊或不断缓冲',
                    'copy'  => '建议改用网线连接以获得最稳定的速度。若使用 Wi-Fi，请连接 5 GHz 频段，并将机器移至靠近路由器的位置，避免放在电视后方。持续缓冲时请重启路由器。建议带宽至少 15 Mbps 以确保 4K 流畅播放。',
                ],
                'no_signal' => [
                    'title' => '电视显示「无信号」',
                    'copy'  => '确认电视已切换至正确的 HDMI 输入口。从两端拔除 HDMI 线，等待 10 秒后重新插紧。也可尝试电视上的其他 HDMI 插口。若使用 AV 功放或 Soundbar 中继，请先直接连接电视，排除设备问题。',
                ],
                'wifi_disconnect' => [
                    'title' => 'Wi-Fi 持续断线',
                    'copy'  => '前往 <strong>设置 &gt; 网络</strong>，先忘记当前的 Wi-Fi，再重新连接。建议选择 5 GHz 频段以减少干扰。若路由器支持 Wi-Fi 6，请确认已启用。避免将机器放在微波炉、婴儿监控器或其他 2.4 GHz 设备附近。建议最终改用 Ethernet 有线连接。',
                ],
                'audio_sync' => [
                    'title' => '音效不同步或无声',
                    'copy'  => '前往 <strong>设置 &gt; 显示与音效</strong>，若使用电视扬声器请选择 PCM；若使用 Soundbar 或 AV 功放则选 Auto 或 Passthrough。可尝试关闭再开启 HDMI CEC。若音效有延迟，请更换 HDMI 线或插口。重启机器通常可解决临时性同步问题。',
                ],
                'frozen' => [
                    'title' => '机器卡死或无响应',
                    'copy'  => '按住机身电源键 8 秒强制重启。若遥控器也无反应，请拔除电源适配器，等待 30 秒后重新插上。若频繁卡死，可能需要更新固件，或机器过热 — 请确保机器四周保持良好散热空间。',
                ],
                'stuck_loading' => [
                    'title' => '停在加载画面或小云 Logo',
                    'copy'  => '先拔掉电源 2-3 分钟，移除 USB、外接硬盘或其他配件，只保留电源线和 HDMI 线。重新插上电源后，马上对着盒子连续按遥控器红色电源键约 5-20 次，直到 Recovery 或系统菜单出现。请先选择 <strong>Reboot system now</strong>。如果仍停在加载画面，请再次进入 Recovery，选择 <strong>Factory reset</strong> 或 <strong>Wipe data/factory data reset</strong>。恢复出厂设置会清除设置与已安装 App，重置后第一次开机可能需要 5-10 分钟。',
                ],
                'firmware' => [
                    'title' => '如何更新固件',
                    'copy'  => '前往 <strong>设置 &gt; 关于 &gt; 系统更新</strong>，点选「检查更新」。机器须连接至网络，更新约需 3 至 5 分钟。更新期间请<strong>切勿</strong>拔除电源。若未显示更新，表示固件已是最新版本。遇到错误信息请联系礼宾服务。',
                ],
                'voice_remote' => [
                    'title' => '语音搜索无法使用',
                    'copy'  => '请确认遥控器已通过蓝牙配对（而非仅使用红外线）。按住麦克风键并在手臂可及的距离内清楚说话。若无反应，请至 <strong>设置 &gt; 遥控器与配件</strong> 重新配对。语音搜索需要网络连接，固件更新期间可能暂时无法使用。',
                ],
                'hdmi_resolution' => [
                    'title' => '画面比例错误或被裁切',
                    'copy'  => '前往 <strong>设置 &gt; 显示与音效 &gt; 分辨率</strong>，选择「自动」或符合电视原生分辨率（通常为 4K/2160p）。若画面过度放大，请在电视端将画面比例设为「点对点」、「1:1」或「Just Scan」（依电视品牌不同）。请关闭电视的任何缩放模式。',
                ],
                'orz' => [
                    'title' => '串流 App 无法开启',
                    'copy'  => '确认该 App 来自合法授权平台。前往 <strong>设置 &gt; 应用程序</strong> 清除缓存后重新开启。若 App 持续闪退，请卸载后重新安装。第三方或未授权 App 的问题超出我们的支持范围，请直接联系该 App 的客服。',
                ],
            ],
        ],
        'resources' => [
            'badge' => '延伸阅读',
            'title' => '推荐收藏的指南',
            'lead'  => '安装完成后，这些资源提供频道覆盖、选购建议与 10 系列特色的完整说明。',
            'items' => [
                'why' => [
                    'title' => '为什么要在 SVICLOUDTVBOX.US 购买',
                    'copy'  => '了解正版来源、礼宾服务与美国保固如何让你安心选购 10 系列机种。',
                ],
                'channels' => [
                    'title' => '授权内容检查清单',
                    'copy'  => '在安装前先检视串流服务的授权与地区限制，确保合法使用。',
                ],
                'features' => [
                    'title' => '小云电视盒 10 系列核心特色',
                    'copy'  => '深入了解 10P+ 与 10S 的硬体规格、编码能力与家庭娱乐体验。',
                ],
                'which' => [
                    'title' => '我适合哪款小云电视盒？',
                    'copy'  => '比较 10P+ 与 10S 的使用情境，第一次就选对机种。',
                ],
            ],
            'articles' => [
                'shared' => [
                    'badge'             => '延伸指南',
                    'back_label'        => '返回延伸资源',
                    'more_guides_title' => '更多延伸指南',
                ],
                'why' => [
                    'title'   => '为什么要在 SVICLOUDTVBOX.US 购买',
                    'lead'    => '保证正版小云电视盒 10 系列机种、礼宾安装与一年美国保固，全程提供中英双语客服。',
                    'updated' => '更新：2025 年 1 月 15 日',
                    'sections' => [
                        'fulfillment' => [
                            'heading' => '内华达州现货，配送前完成验证',
                            'body'    => '<p>所有小云电视盒 10P+ 与 10S 皆由内华达州库存备货，检查序号、封条与最新韧体后才寄出，并提供物流追踪码。</p>
<ul>
  <li>提供 UPS／USPS 需签收的配送选项，包裹更安全。</li>
  <li>美国现货免去海关延误与水货或翻新机的风险。</li>
  <li>包装内含原厂语音遥控器、美规电源供应器与完整配件。</li>
</ul>',
                        ],
                        'warranty' => [
                            'heading' => '一年美国保固与简单退换货',
                            'body'    => '<p>每台机器皆附一年美国保固，由礼宾团队协助检测与维修；正常使用下若发生问题，会在美国本地安排零件或换机。</p>
<ul>
  <li>购买后 14 天内保留未拆封，可申请退换货。</li>
  <li>维修或更换全程在美国处理，不需跨国寄件等待。</li>
  <li>韧体更新、功能咨询与故障排除都由礼宾客服跟进。</li>
</ul>',
                        ],
                        'concierge' => [
                            'heading' => '中英文礼宾客服全程陪伴',
                            'body'    => '<p>需要设定语音遥控器或调整网路环境吗？透过 <a href=\"{{contact_url}}\">礼宾客服表单</a> 留言，我们会在一个工作天内以中英文回复。</p>
<ul>
  <li>提供视讯截图与逐步指引，协助完成开箱与连线。</li>
  <li>提醒仅安装具授权的串流服务，确保合法使用。</li>
  <li>安装完成后持续协助韧体更新与问题排解。</li>
</ul>',
                        ],
                        'billing' => [
                            'heading' => '安全结帐，价格透明',
                            'body'    => '<p>网站采用 SSL 安全结帐，支援信用卡与 PayPal，依法开立销售税。价格透明，结帐画面即会显示运费与税额，并附上物流追踪码。</p>
<ul>
  <li>无额外的处理费或盒装加价。</li>
  <li>一次购买终身使用，没有月费或自动续约。</li>
  <li>配送后立即寄送收据与追踪码，方便掌握物流。</li>
</ul>',
                        ],
                        'next_steps' => [
                            'heading' => '下单后的下一步',
                            'body'    => '<p>下单后会先收到订单确认，再寄出物流追踪与开箱技巧。建议先阅读 <a href=\"{{setup_url}}\">10 分钟安装指南</a>，若需要协助随时透过 <a href=\"{{contact_url}}\">礼宾客服表单</a> 联系我们。</p>',
                        ],
                    ],
                ],
                'channels' => [
                    'title'   => '授权内容检查清单',
                    'lead'    => '安装任何串流服务前，请先确认授权、地区限制与安全性，确保合法安心地使用。',
                    'updated' => '更新：2025 年 1 月 15 日',
                    'sections' => [
                        'rights' => [
                            'heading' => '确认授权来源',
                            'body'    => '<p>仅安装清楚揭示授权资讯的服务，若无法提供合法权利证明或联络方式，请勿使用。</p>
<ul>
  <li>阅读服务条款、隐私政策与智慧财产权声明。</li>
  <li>确认业者提供有效客服信箱或公司资料。</li>
  <li>保存订阅或购买凭证，必要时可提供给执法单位或 ISP。</li>
</ul>',
                        ],
                        'regional' => [
                            'heading' => '检查地区限制',
                            'body'    => '<p>许多平台仅授权特定国家播放。安装前请确认是否允许在您所在地使用，并遵守地方法规。</p>
<ul>
  <li>了解是否禁止使用 VPN 或代理跨区观看。</li>
  <li>确认自己符合年龄、居住或身份条件。</li>
  <li>若涉及进口或报关，也应先行了解相关规范。</li>
</ul>',
                        ],
                        'security' => [
                            'heading' => '维护装置安全',
                            'body'    => '<p>避免从未知来源下载 APK。请透过官方商店或可信平台取得安装档，降低恶意软体风险。</p>
<ul>
  <li>下载前使用防毒软体扫描安装档案。</li>
  <li>启用强密码与双重验证保护帐号。</li>
  <li>留意平台是否提供定期安全更新与公告。</li>
</ul>',
                        ],
                        'documentation' => [
                            'heading' => '保留合规纪录',
                            'body'    => '<p>建议将交易记录、客服往来与授权证明整理存档，必要时可快速提供证明。</p>
<ul>
  <li>截图订阅页面、收据与授权条款。</li>
  <li>整理客服往来纪录，方便后续追踪。</li>
  <li>如有多人共用，请同步相关规范与文件。</li>
</ul>',
                        ],
                        'network' => [
                            'heading' => '确保播放品质',
                            'body'    => '<p>建议使用 Wi-Fi 6 或有线网路提升稳定度。若遇到缓冲，可依 <a href=\"{{troubleshooting_url}}\">疑难排解指南</a> 调整设备或重新启动路由器。</p>',
                        ],
                    ],
                ],
                'features' => [
                    'title'   => '小云电视盒 10 系列核心特色',
                    'lead'    => '一次掌握小云电视盒 10P+ 与 10S 的硬体规格、串流技术与家庭娱乐优势。',
                    'updated' => '更新：2025 年 1 月 15 日',
                    'sections' => [
                        'hardware' => [
                            'heading' => '旗舰硬体，4K HDR 无痛播放',
                            'body'    => '<p>10P+ 采用 Amlogic S928X 八核心处理器，搭配 4GB RAM 与 64GB 储存；10S 则配置四核心处理器、4GB RAM 与 32GB 储存。两款皆支援 4K HDR、HDMI 2.1、双频 Wi-Fi 6 与 Gigabit 有线网路。</p>
<ul>
  <li>USB 3.0／USB 2.0 可外接硬碟、麦克风或周边配件。</li>
  <li>HDMI eARC 与光纤输出提供家庭剧院等级的音讯选择。</li>
  <li>低噪音散热设计，长时间追剧也能维持稳定。</li>
</ul>',
                        ],
                        'streaming' => [
                            'heading' => 'AV1 编码与智慧缓冲',
                            'body'    => '<p>两款机型皆支援 AV1、HEVC、VP9 等高效率编码，让画质更清晰、网路用量更低；自动缓冲与帧率匹配，确保体育与戏剧播放流畅。</p>
<ul>
  <li>根据节目自动切换 24p／30p／60p，减少抖动。</li>
  <li>全球 CDN 加速路径优化北美连线，海外内容一样顺畅。</li>
  <li>定期韧体更新加入新功能与效能调校。</li>
</ul>',
                        ],
                        'remote' => [
                            'heading' => '语音遥控，一键找到节目',
                            'body'    => '<p>蓝牙语音遥控支援语音搜寻、数字快捷键与媒体控制，只要长按音量键即可重新配对，搜寻频道或 App 更快速。</p>
<ul>
  <li>可学习电视与音响的电源、音量，一支遥控搞定。</li>
  <li>按键具背光设计，晚上追剧也看得清楚。</li>
  <li>遥控器韧体可 OTA 更新，维持灵敏度。</li>
</ul>',
                        ],
                        'family' => [
                            'heading' => '全家娱乐一次满足',
                            'body'    => '<p>小云电视盒不只有直播，也支援儿童分龄模式、居家卡拉 OK 与密码锁定的成人专区，可依需求调整内容权限。</p>
<ul>
  <li>建立儿童模式，仅显示教育与卡通内容。</li>
  <li>卡拉 OK 功能支援排歌、合唱与歌词显示。</li>
  <li>可依需求扩充各地语系的合法串流服务。</li>
</ul>',
                        ],
                        'service' => [
                            'heading' => '终身礼宾服务',
                            'body'    => '<p>每台机器都享有终身礼宾服务。需要安装教学、韧体公告或节目推荐，可随时透过 <a href=\"{{contact_url}}\">礼宾客服表单</a> 联络我们。</p>',
                        ],
                    ],
                ],
                'models' => [
                    'title'   => '我适合哪款小云电视盒？',
                    'lead'    => '依照收视习惯、周边需求与预算，挑选最适合的小云电视盒 10P+ 或 10S。',
                    'updated' => '更新：2025 年 1 月 15 日',
                    'sections' => [
                        'summary' => [
                            'heading' => '差异一目了然',
                            'body'    => '<table class=\"guides-article__table\">
  <thead>
    <tr>
      <th>项目</th>
      <th>小云电视盒 10P+</th>
      <th>小云电视盒 10S</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>处理器与记忆体</td>
      <td>Amlogic S928X · 4GB RAM · 64GB 储存</td>
      <td>四核心 CPU · 4GB RAM · 32GB 储存</td>
    </tr>
    <tr>
      <td>连线能力</td>
      <td>Wi-Fi 6 双频 · Gigabit LAN · USB 3.0 + USB 2.0</td>
      <td>Wi-Fi 6 双频 · Gigabit LAN · 双 USB 2.0</td>
    </tr>
    <tr>
      <td>音讯输出</td>
      <td>HDMI 2.1 · 光纤 SPDIF</td>
      <td>HDMI 2.1 · 3.5mm AUX</td>
    </tr>
    <tr>
      <td>最佳使用情境</td>
      <td>家庭剧院、多 App 安装、KTV 加强</td>
      <td>日常串流、小空间、副电视</td>
    </tr>
  </tbody>
</table>
<p>两款机型皆支援相同韧体功能，差别在于储存空间、连接介面与周边扩充性。</p>',
                        ],
                        'ten_p' => [
                            'heading' => '适合选择 10P+ 的情境',
                            'body'    => '<p>以下情境建议选择 10P+：</p>
<ul>
  <li>需要连接扩大机、Soundbar 或光纤音响的家庭剧院。</li>
  <li>需要外接大容量储存装置或管理大型媒体资料库。</li>
  <li>常使用 Karaoke、Party 模式，想要最强的处理效能。</li>
</ul>',
                        ],
                        'ten_s' => [
                            'heading' => '适合选择 10S 的情境',
                            'body'    => '<p>以下情境适合 10S：</p>
<ul>
  <li>卧室、租屋或小坪数空间，需要精巧机身。</li>
  <li>以直播与随选内容为主，只需安装少量额外 App。</li>
  <li>想要礼宾服务与最新韧体，但预算更精简。</li>
</ul>',
                        ],
                        'next_steps' => [
                            'heading' => '选好机型后别忘了',
                            'body'    => '<p>确定机型后，记得先完成 <a href=\"{{setup_url}}\">安装指南</a>、阅读 <a href=\"{{legal_url}}\">法律声明</a>，并收藏 <a href=\"{{contact_url}}\">礼宾客服表单</a> 以便日后提问。</p>',
                        ],
                    ],
                ],
            ],
        ],
        'detail' => [
            'back_to_hub'  => '返回指南首页',
            'more_guides'  => '浏览其他指南',
            'open_section' => '开启指南',
        ],
        'support' => [
            'badge' => '需要协助吗？',
            'title' => '中英文礼宾客服随时待命',
            'copy'  => '提供订单编号或安装问题，我们会在一个工作天内以英文或中文回复您。',
            'primary_label'   => '联系礼宾客服',
            'secondary_label' => '查看常见问题',
        ],
    ],
    'frontpage' => [
        'hero' => [
            'badge'   => '美国授权经销 · 内华达',
            'title'   => '小云 15P 开放预订。亚洲娱乐，重新想象。',
            'title_lead' => '小云 15P 开放预订',
            'title_separator' => '。',
            'title_tail' => '亚洲娱乐，重新想象。',
            'copy'    => '小云 15P 现以 US$288 接受预订（原价 US$379），搭载 Android 14、Amlogic S905Y5、4 GB DDR3、64 GB eMMC、Wi-Fi 6、蓝牙 5.4 与 4K HDR。',
            'launch'  => [
                'badge' => '新品',
                'text'  => '小云 15P 预订已开放',
            ],
            'showcase' => [
                'kicker'  => '小云 15P 电视盒',
                'release' => '接受预订',
                'title'   => 'Android 14、Wi-Fi 6、4K HDR。',
                'copy'    => '全新小云 15P 搭载 Amlogic S905Y5、4 GB DDR3、64 GB eMMC、蓝牙 5.4 与 AV1 解码。',
                'image_alt' => '小云 15P 电视盒正面',
                'points'  => [
                    'usa'     => 'Android 14',
                    'fees'    => 'Wi-Fi 6 + 蓝牙 5.4',
                    'support' => '4K HDR + AV1 解码',
                ],
                'cta'     => '预订 15P',
                'compare' => '比较全系列',
            ],
            'bullets' => [
                'shipping' => 'Amlogic S905Y5 四核心处理器',
                'warranty' => '4 GB DDR3 + 64 GB eMMC',
                'fees'     => '双频 Wi-Fi 6 + 蓝牙 5.4',
            ],
            'cta' => [
                'primary'   => '预订 15P',
                'tenp'      => '选购 10P+',
                'bundles'   => '查看优惠组合',
                'compare'   => '查看价格',
                'secondary' => '查看价格',
            ],
            'card' => [
                'badge'     => '直播',
                'headline'  => '小云电视盒 10P+ 硬件一览',
                'timestamp' => '美国最新批次 · 2026',
                'stat'      => '4GB 内存 · 64GB 存储',
                'specs'     => [
                    'processor'    => [
                        'benefit' => '4K 直播体育不卡顿',
                        'label'   => '处理器',
                        'value'   => 'Amlogic S928X · 八核心',
                    ],
                    'connectivity' => [
                        'benefit' => '家中网络再多设备也稳定串流',
                        'label'   => '连线能力',
                        'value'   => 'Wi-Fi 6 双频 + Gigabit 有线网络',
                    ],
                    'video'        => [
                        'benefit' => '任何 4K 电视都有电影院级画质',
                        'label'   => '高画质解码',
                        'value'   => 'AV1 解码 · 4K HDR',
                    ],
                    'extras'       => [
                        'benefit' => '支持中英双语语音遥控',
                        'label'   => '遥控与配件',
                        'value'   => '蓝牙语音遥控 · USB 3.0 扩充',
                    ],
                ],
                'footer' => [
                    'shipping' => '美国配送 · 配送时程依承运商为准',
                    'support'  => '一年美国保固 · 专人安装服务',
                ],
            ],
        ],
        'sticky_buy' => [
            'label' => '小云电视盒 10P+',
            'cta'   => '立即购买',
        ],
        'inbox' => [
            'title' => '包装盒内容物',
            'items' => [
                'box'      => '小云电视盒主机',
                'power'    => '美规电源适配器',
                'hdmi'     => 'HDMI 线',
                'remote'   => '蓝牙 AI 语音遥控器',
                'guide'    => '快速入门指南（中文／English）',
            ],
        ],
        'certification' => [
            'badge' => '原厂授权',
            'title' => 'SVI.STUDIO 官方授权证书',
            'lead' => 'SVI.STUDIO 正式授权 168 Media Group LLC（SVICLOUDTVBOX.US）于美洲地区销售并提供原厂保固。',
            'meta' => [
                'number' => [
                    'label' => '授权编号',
                    'value' => 'US2025092609217',
                ],
                'territory' => [
                    'label' => '授权区域',
                    'value' => '美洲（Americas）',
                ],
                'term' => [
                    'label' => '有效期间',
                    'value' => '2025 年 9 月 26 日 - 2026 年 9 月 26 日',
                ],
            ],
            'footnote' => '此证书由 SVI.STUDIO 核发，内容与原件完全一致。',
            'cta' => '查看完整证书',
            'alt' => 'SVI.STUDIO 授权 168 Media Group LLC 的官方证书',
        ],
        'metrics' => [
            'shipping' => [
                'title' => '配送时程依承运商为准',
                'copy'  => '美国库存快速配货',
            ],
            'concierge' => [
                'title' => '专人安装服务',
                'copy'  => '中英双语开通协助',
            ],
            'security' => [
                'title' => '安全结账',
                'copy'  => 'SSL 加密付款与保固保障',
            ],
            'dealer' => [
                'title' => '顶级评价经销商',
                'copy'  => '自 2019 年起深受美国小云电视盒用户信赖',
            ],
        ],
        'feature_grid' => [
            'title'    => '为什么小云电视盒胜过一般串流盒',
            'subtitle' => '专为 2026 家庭串流需求打造：超高清 4K 体育、追剧马拉松与卡拉 OK 夜晚都稳定流畅。',
            'cards'    => [
                'entertainment' => [
                    'title' => '全方位娱乐',
                    'copy'  => '涵盖亚洲与北美的 4K 直播频道，还有双语随选影片、卡拉 OK 与亲子专区。',
                ],
                'hardware' => [
                    'title' => '新世代硬件',
                    'copy'  => '最新 Amlogic 芯片、AV1 解码与 Wi-Fi 6，让直播与点播在繁忙网络下仍稳定。',
                ],
                'support' => [
                    'title' => '在地专家支援',
                    'copy'  => '美国在地小云电视盒专家协助安装、更新与挑选喜爱频道。',
                ],
            ],
        ],
        'experience' => [
            'badge'      => '真人客服，立即支援。',
            'title'      => '从安装到观影夜晚的全程礼宾服务',
            'lead'       => '我们替您设置、排除故障并更新设备，让您专心享受内容。所有方案皆包含小云电视盒中英双语专属客服。',
            'card_title' => '我们替您处理',
            'services'   => [
                'activation' => '设备设置与固件检查',
                'wifi'       => 'Wi-Fi 最佳化建议',
                'karaoke'    => '卡拉 OK 歌单与麦克风配对',
                'kids'       => '儿童安全账号与观看时间设置',
            ],
            'cta' => '联络专家',
        ],
        'testimonials' => [
            'badge'    => '真实用户',
            'title'    => '美国家庭怎么说小云电视盒',
            'subtitle' => '来自全美用户的真实心得，因为熟悉的中文频道而选择小云电视盒。',
            // TODO: 在启用 SVIC_TESTIMONIALS_ENABLED 之前，请以真实顾客留言取代
            // 以下占位文字（FTC 规定不可造假，可使用 email、FB 或 Google
            // 评论的真实留言）。
            'quotes' => [
                [
                    'quote'  => '[占位文字] 这里放真实顾客留言，1-3 句描述安装经验、客服或频道内容。',
                    'name'   => '名 L.',
                    'city'   => '城市, ST',
                    'source' => '已验证买家',
                ],
                [
                    'quote'  => '[占位文字] 第二则留言，建议聚焦双语礼宾或快速发货。',
                    'name'   => '名 L.',
                    'city'   => '城市, ST',
                    'source' => '已验证买家',
                ],
                [
                    'quote'  => '[占位文字] 第三则留言，建议来自华人家庭谈中文客服或频道体验。',
                    'name'   => '名 L.',
                    'city'   => '城市, ST',
                    'source' => '已验证买家',
                ],
            ],
        ],
        'blog' => [
            'badge'     => '最新博客文章',
            'title'     => '最新教学、机型比较与礼宾提醒',
            'lead'      => '每周推出英文与中文内容，分享安装步骤、硬件解析与合法收看资讯，掌握小云最新消息。',
            'read_more' => '阅读文章',
            'cta_label' => '浏览博客',
        ],
        'traffic' => [
            'badge' => '2026 面向在美华人/亚洲家庭的 10P+',
            'title' => '免去水货烦恼。中文或英文礼宾代客设置，内华达 48 小时发货。',
            'lead'  => '服务 2026 在美华人/亚洲家庭：内华达仓 48 小时发货，中文/English 礼宾代客设置，硬件可跑 4K 体育、追剧、K 歌与儿童模式。',
            'bullets' => [
                'shipping'  => '48 小时美国发货，附本地电源/HDMI 与追踪号',
                'concierge' => '双语礼宾：远程设置、Wi-Fi 调优、K 歌麦克风配对、Kids Mode 指南',
                'warranty'  => '1 年美国保修 + 14 天退换货，礼宾全程协助',
            ],
            'links' => [
                'pdp'     => '购买 10P+',
                'compare' => '比较 10P+ 与 10S',
                'faq'     => '查看常见问题',
                'contact' => '联系礼宾客服',
                'guide_2026' => '阅读 2026 购买指南',
            ],
        ],
        'faq' => [
            'badge' => '常见问题',
            'title' => '美加客户最常询问的重点',
            'lead'  => '结账前先了解配送时程、美国保固与繁体礼宾服务，安心挑选最适合的机型。',
            'cta'   => '查看完整客服 FAQ',
            'groups' => [
                'orders' => [
                    'title' => '订单与配送',
                    'items' => [
                        'fulfillment' => [
                            'question' => '美国／加拿大多久可以收到货？',
                            'answer'   => '美西时间下午 2 点前成立的订单，配送时程依承运商为准由内华达州仓库以 USPS、UPS 或 FedEx 配送，寄出后会自动寄送追踪码。寄往加拿大通常 5–7 个工作天（含清关时间）。',
                        ],
                        'warranty' => [
                            'question' => '有美国保固与退换货服务吗？',
                            'answer'   => '在 SVICLOUDTVBOX.US 购买的装置皆享一年美国原厂保固与 14 天内退换货。提供订单编号给礼宾客服，我们会先协助检测并安排换货或退款流程。',
                        ],
                        'payment' => [
                            'question' => '支持哪些付款方式？',
                            'answer'   => '支持所有主流信用卡与借记卡（Visa、Mastercard、Amex、Discover）、PayPal 及 Apple Pay，通过安全结账页面完成付款。不接受电汇或加密货币。',
                        ],
                        'invoice' => [
                            'question' => '可以索取发票或收据供商业用途吗？',
                            'answer'   => '付款完成后系统会自动发送 PDF 收据。若需要附有公司名称与地址的正式发票，请提供订单编号及公司资料联系礼宾客服。',
                        ],
                    ],
                ],
                'setup' => [
                    'title' => '装置与安装',
                    'items' => [
                        'compatibility' => [
                            'question' => '我的电视与家中网络是否相容？',
                            'answer'   => '只要具备 HDMI 介面的电视即可使用，可透过 2.4G / 5G Wi-Fi 或盒子附的 Gigabit 有线网络连线。包装内含语音遥控器、HDMI 线与美规变压器。',
                        ],
                        'concierge' => [
                            'question' => '可以用繁体中文协助我完成安装吗？',
                            'answer'   => '可以，礼宾客服提供英文／繁体中文远端指导，陪你完成界面设置、Kids Mode、安全权限、卡拉 OK 与 App 更新等问题。',
                        ],
                        'preloaded' => [
                            'question' => '盒子出厂预装了哪些应用程序与频道？',
                            'answer'   => '装置搭载标准 Android TV 启动器，并预装儿童模式与卡拉 OK 等双语工具。礼宾客服可协助引导你安全安装所需的串流服务。',
                        ],
                        'vpn' => [
                            'question' => '使用小云电视盒需要 VPN 吗？',
                            'answer'   => '装置本身无需 VPN 即可正常运作。是否需要 VPN 取决于你选择使用的应用程序或服务，而非盒子本身。请遵守当地法规，使用合法授权的服务。',
                        ],
                    ],
                ],
                'models' => [
                    'title' => '机型与比较',
                    'items' => [
                        'vs_competitors' => [
                            'question' => '小云电视盒与 EVPAD、UnblockTech 或 UBOX 有何不同？',
                            'answer'   => '小云电视盒 10P+ 与 10S 是在美国仓储、附一年美国硬件保固，并提供中英双语礼宾客服的正版设备。相较之下，平行进口灰市产品通常不提供本地保固或安装协助。',
                        ],
                        'after_warranty' => [
                            'question' => '一年保固到期后怎么办？',
                            'answer'   => '硬件本身不会过期，保固到期后仍可正常使用。往后若需要技术支持或设置协助，仍可通过礼宾客服预约付费服务。',
                        ],
                    ],
                ],
            ],
        ],
        'pricing' => [
            'stock_note'           => '现货 — 48 小时内由内华达州发货',
            'sr_sale_announcement' => '特价 %2$s，原价 %1$s',
            'savings_label'        => '节省 %1$s（%2$s%% 折扣）',
            'cards'                => [
                '15p' => [
                    'badge'     => '接受预订',
                    'title'     => '小云 15P 电视盒',
                    'image_alt' => '小云 15P Android 14 预订图',
                    'interval'  => '台',
                    'copy'      => '搭载 Android 14、Amlogic S905Y5、4 GB DDR3、64 GB eMMC、Wi-Fi 6、蓝牙 5.4 与 4K HDR。',
                    'stock_note' => '发货日期尚未公布',
                    'features'  => [
                        'processor' => 'Amlogic S905Y5 + Android 14',
                        'no_fees' => '4 GB DDR3 + 64 GB eMMC',
                        'support' => 'Wi-Fi 6 + 蓝牙 5.4 + AV1',
                    ],
                    'cta'      => '预订 15P',
                    'buy_cta'  => '预订 15P',
                ],
                '10p' => [
                    'image_alt'=> '小云电视盒 10P+ 产品图',
                    'copy'     => 'Android 12、4GB 内存 / 64GB 存储，内建儿童与卡拉 OK 应用。',
                    'features' => [
                        'hdr' => 'Android 12 + 4K HDR + AV1 解码',
                    ],
                    'buy_cta' => '立即购买 10P+',
                ],
                '10s' => [
                    'image_alt'=> '小云电视盒 10S 产品图',
                    'copy'     => 'Android 12、2GB 内存 / 32GB 存储，适合卧室或第二台电视。',
                    'features' => [
                        'hdr' => 'Android 12 + 4K HDR + AV1 解码',
                    ],
                    'buy_cta' => '立即购买 10S',
                ],
            ],
        ],
        'aria' => [
            'hero_visual'    => '小云电视盒语音助理界面，含 Google Play、电影与 YouTube 应用',
            'traffic_actions'=> '小云电视盒主要操作',
            'shop_now'       => '立即选购',
            'metrics'        => '小云电视盒重点优势',
        ],
        'schema' => [
            'item_list_name' => '北美可购买的小云电视盒设备',
        ],
        'confidence' => [
            'badge' => '安心下单',
            'title' => '为什么直接在 SVICLOUDTVBOX.US 下单',
            'subtitle' => '不靠假星等与模糊评价，只提供官方美国现货、双语协助，以及清楚的售后流程。',
            'cards' => [
                'official' => [
                    'title' => '美国官方授权通路',
                    'copy'  => '原厂授权的美国销售网站，销售正版 SVICLOUD 硬件并提供保修支持。',
                ],
                'shipping' => [
                    'title' => '内华达本地发货',
                    'copy'  => '美国现货发货附追踪码，盒内也备妥本地使用的电源与 HDMI 配件。',
                ],
                'concierge' => [
                    'title' => '中英双语礼宾',
                    'copy'  => '可协助首次安装、Wi-Fi 调优，以及开箱后的基本设置问题。',
                ],
                'warranty' => [
                    'title' => '保修与退换货清楚',
                    'copy'  => '每台设备皆含 1 年美国硬件保修与 14 天退换货期间。',
                ],
            ],
            'timeline' => [
                'badge' => '下单之后',
                'aria_label' => '下单之后会发生什么',
                'title' => '接下来会发生什么',
                'lead'  => '流程很简单：安全结账、内华达发货、Email 追踪，以及需要时可联系礼宾。',
                'steps' => [
                    'order' => [
                        'title' => '安全完成订单',
                        'copy'  => '通过安全结账页使用主流信用卡、PayPal 与可用的快捷支付方式完成付款。',
                    ],
                    'dispatch' => [
                        'title' => '由内华达州发货',
                        'copy'  => '美国现货本地包装，通常 48 小时内寄出，并通过 Email 提供追踪资讯。',
                    ],
                    'setup' => [
                        'title' => '开箱后可获得安装协助',
                        'copy'  => '您可以先依照快速入门指南安装，也可请礼宾用中文或英文协助设置。',
                    ],
                    'support' => [
                        'title' => '后续支援仍找得到人',
                        'copy'  => '若后续遇到硬件问题，保修与退换货流程都维持清楚且在美国处理。',
                    ],
                ],
            ],
        ],
    ],
    'product' => [
        'hero' => [
            'subtitle' => '美国仓库配送，提供中英文专人安装服务。',
            'detail'   => '安全结账 • 美国免运 • 英/中文客服',
            'reassurance' => [
                'badge' => '安心下单',
                'title' => '结账后的流程一样清楚',
                'copy'  => '今天下单后，后续细节我们会接手处理：安全付款、可追踪发货，以及需要时提供中英双语安装协助。',
                'bullets' => [
                    'shipping'  => '内华达州现货美国免运，发货后会通过 email 提供追踪号',
                    'warranty'  => '附 1 年美国保修与 14 天退换货，由礼宾客服协助处理',
                    'concierge' => '首次安装、Wi‑Fi 调优与遥控器配对都可提供英/中文支持',
                ],
            ],
        ],
        'highlights' => [
            'inventory' => '美国现货与一年保固',
            'concierge' => '英/中文专属开箱服务',
            'no_fees'   => '无月费与隐藏续费',
        ],
        'traffic' => [
            'badge' => '面向在美华人/亚洲家庭打造',
            'title' => '美国现货、双语礼宾与完整保修支持',
            'lead'  => '内华达仓 48 小时发货，中文/English 礼宾代客设置，提供适合 4K 体育、追剧与日常串流的正版 SVICLOUD 硬件。',
            'bullets' => [
                'shipping'  => '48 小时美国发货，附本地电源/HDMI 与追踪号',
                'concierge' => '双语礼宾：远程设置、Wi-Fi 调优与设备使用指导',
                'warranty'  => '1 年美国保修 + 14 天退换货，礼宾全程协助',
            ],
            'links' => [
                'compare' => '比较 10P+ 与 10S',
                'faq'     => '查看 FAQ',
                'contact' => '联系礼宾客服',
            ],
        ],
        'aria' => [
            'traffic_actions' => '小云电视盒产品主要操作',
        ],
        'reviews' => [
            'badge'           => 'Google 商店反馈',
            'title'           => 'Google 商店评价',
            'lead'            => '此评分来自 Google Customer Reviews 与 Merchant Center 收集的商店层级反馈。',
            'badge_title'     => 'Google 官方评分',
            'average_label'   => 'Google 平均评分',
            'average_score'   => '5.0',
            'average_scale'   => '满分 5 分',
            'average_note'    => '来自 Google Customer Reviews 与 Merchant Center 的商店层级反馈平均。',
            'average_aria'    => 'Google 商店平均评分 5.0 满分 5 分',
            'quotes_label'    => 'Google 商店评价摘录',
            'five_star_label' => '5 星评价',
            'items' => [
                [
                    'quote'  => '视频品质很高，发货也很快。',
                    'name'   => 'Google 评论者',
                    'source' => 'Google 商店评价',
                ],
                [
                    'quote'  => '发货快速，产品质量好，卖家回复也很积极。',
                    'name'   => 'Alan Wang',
                    'source' => 'Google 商店评价',
                ],
                [
                    'quote'  => '产品很棒。视频品质高且发货快速，会推荐给其他人。',
                    'name'   => 'Alan Wang',
                    'source' => 'Google 商店评价',
                ],
            ],
        ],
        'faq' => [
            'badge' => '常见问题',
            'title' => '购买前最常问的 3 个问题',
            'lead'  => '配送时效、保修退换、中文安装协助。',
            'items' => [
                'shipping' => [
                    'q' => '美国/加拿大发货速度多久？',
                    'a' => '太平洋时间下午 2 点前下单，48 小时内由内华达仓用 USPS/UPS/FedEx 寄出。加拿大通常 5–7 个工作日（含清关）。',
                ],
                'warranty' => [
                    'q' => '包含哪些保修和退换货？',
                    'a' => 'SVICLOUDTVBOX.US 全站含 1 年美国硬件保修与 14 天退换货。提供订单号即可由礼宾协助排查、换货或退货。',
                ],
                'concierge' => [
                    'q' => '能用中文帮我安装吗？',
                    'a' => '可以。礼宾可用中文或英文远程带你设置，含 Kids Mode、K 歌麦克风配对与应用更新。',
                ],
            ],
        ],
    ],
    'products' => [
        'svicloud-10p-plus' => [
            'short_description' => 'Android 12 旗舰 4K 流媒体盒，4GB 内存 / 64GB 存储，内建儿童模式、卡拉 OK 与双语礼宾支持。',
            'description' => '<p>小云电视盒 10P+ 搭载 Android 12，是家庭客厅的旗舰机，结合语音操控、卡拉 OK 与最快的硬件性能。</p><ul><li>4GB 内存与 64GB 存储、AV1 解码，直播体育赛事与戏剧都能流畅 4K 播放。</li><li>内建儿童模式与卡拉 OK 应用，附英文 / 中文礼宾带领完成设置。</li><li>标配 Wi-Fi 6、Gigabit 有线网络、蓝牙语音遥控、USB 3.0 与扩展端口，满足多种安装场景。</li><li>支持 Dolby 环绕音效，可搭配相容的电视、Soundbar 与功放使用。</li><li>美国内华达州仓库配送，14 天安心退换、一年美国保修与真人礼宾客服。</li></ul><p>盒内附 HDMI 线、电源适配器、语音遥控，支持双无线麦克风卡拉 OK。</p>',
            'best_for' => [
                'badge' => '最适合',
                'title' => '客厅主机、卡拉 OK 聚会，以及想一次买到位的买家',
                'copy'  => '如果你想要最完整的客厅配置、旗舰性能，以及从第一天就能用上的家庭娱乐功能，10P+ 会是更稳妥的选择。',
                'bullets' => [
                    'primary'   => '4GB 内存 / 64GB 存储，更适合多 App、多任务与更高性能需求',
                    'secondary' => '内建儿童模式、卡拉 OK 应用与语音遥控支持',
                    'tertiary'  => '特别适合爱看体育赛事、家庭共用客厅，以及不想之后再升级的家庭',
                ],
            ],
            'crosslink' => [
                'badge'  => '新机登场',
                'title'  => '小云 15P 预订已开放',
                'lead'   => '小云 15P 现以 US$288 接受预订（原价 US$379）；发货日期尚未公布。',
                'cta'    => '预订小云 15P',
                'target' => 'svicloud-15p',
            ],
        ],
        'svicloud-10s' => [
            'short_description' => 'Android 12 高性价比 4K 串流盒，2GB 内存 / 32GB 存储，适合卧室、客房、第二台电视与小空间使用。',
            'description' => '<p>小云电视盒 10S 搭载 Android 12，是主打高性价比的 4K 串流机型，适合想要 SVICLOUD 正版内容、但不需要旗舰级附加功能的家庭。</p><ul><li>2GB 内存与 32GB 存储搭配 AV1 解码，可顺畅播放直播、剧集与点播内容。</li><li>体积轻巧，特别适合卧室、客房、宿舍、出租屋或第二台电视使用。</li><li>具备 HDMI、以太网与 USB 接口，可快速完成 Wi‑Fi 或有线安装。</li><li>美国现货发货，附一年保修、中英双语客服与礼宾式开箱协助。</li></ul><p>如果你重视价格与基本影音需求，10S 是最划算的选择。若你需要更强性能、K 歌与语音控制，则更适合 10P+。</p>',
            'best_for' => [
                'badge' => '最适合',
                'title' => '卧室、客房、第二台电视与重视预算的买家',
                'copy'  => '如果你想用更合理的预算获得稳定的 4K SVICLOUD 体验、美国现货，以及双语支持，10S 就很值得直接入手。',
                'bullets' => [
                    'primary'   => '以较低门槛进入 SVICLOUD 系列，同时保留核心 4K 播放与 AV1 解码',
                    'secondary' => '很适合次要房间、客房、租屋处与较简单的日常使用场景',
                    'tertiary'  => '只有在你需要卡拉 OK、儿童模式或系列中最高性能时，才需要升级到 10P+',
                ],
            ],
            'traffic' => [
                'badge' => '面向在美华人/亚洲家庭打造的 10S',
                'title' => '适合卧室、客房与第二台电视的高性价比 SVICLOUD',
                'lead'  => '10S 适合想用更低预算获得稳定 SVICLOUD 影音体验，同时享有美国现货与双语客服的买家。',
                'bullets' => [
                    'shipping'  => '48 小时美国发货，附本地电源/HDMI 配件与追踪号',
                    'concierge' => '双语礼宾协助安装、Wi‑Fi 调优与应用开箱教学',
                    'warranty'  => '1 年美国保修 + 14 天退换货，礼宾客服协助处理',
                ],
            ],
            'crosslink' => [
                'badge'  => '新机登场',
                'title'  => '预订小云 15P',
                'lead'   => 'Android 14、Wi-Fi 6 与蓝牙 5.4 小云 15P 现以 US$288 接受预订（原价 US$379）。',
                'cta'    => '预订小云 15P',
                'target' => 'svicloud-15p',
            ],
        ],
        'svicloud-9p' => [
            'short_description' => '保留给现有用户的旧款小云 9P 页面，方便查找支持信息与比较后续机型。',
            'description' => '<p>小云 9P 是上一代机型。本页持续保留，不删除也不隐藏，供现有用户查找与比较。</p><ul><li>9P 若仍符合需求，无需急着更换。</li><li>15P 硬件规格已公布，可按实际差异比较。</li><li>15P 现以 US$288 接受预订（原价 US$379）；发货日期尚未公布。</li></ul>',
            'best_for' => [
                'badge' => '旧款机型',
                'title' => '正在评估下一台盒子的 9P 用户',
                'copy'  => '如果 9P 仍正常使用，请继续保留。可比较 15P 已确认的硬件与目前 US$288 预订价后再决定。',
                'bullets' => [
                    'primary'   => '保留旧款页面与搜索排名',
                    'secondary' => '不催促更换正常运行的盒子',
                    'tertiary'  => '只依正式规格做升级比较',
                ],
            ],
            'crosslink' => [
                'badge'  => '接受预订',
                'title'  => '考虑从 9P 升级？',
                'lead'   => '比较小云 15P 已确认的规格，并以 US$288 预订（原价 US$379）。',
                'cta'    => '预订小云 15P',
                'target' => 'svicloud-15p',
            ],
        ],
        'svicloud-15p' => [
            'title' => '小云 15P 电视盒',
            'meta' => [
                'title'       => '小云 15P 预订 US$288｜Android 14 电视盒',
                'description' => '小云 15P 现以 US$288 接受预订（原价 US$379），搭载 Android 14、Amlogic S905Y5、4 GB DDR3、64 GB eMMC、Wi-Fi 6 与蓝牙 5.4。',
                'image_alt'   => '小云 15P 电视盒正面',
            ],
            'short_description' => '现以 US$288 接受预订（原价 US$379）：Android 14、Amlogic S905Y5、4 GB DDR3、64 GB eMMC、Wi-Fi 6、蓝牙 5.4 与 4K HDR。',
            'description' => '<p>全新小云 15P 电视盒搭载 Android 14 与 Amlogic S905Y5 四核心 ARM Cortex-A55 处理器。</p><h2>核心规格</h2><ul><li>4 GB DDR3 内存与 64 GB eMMC 存储空间。</li><li>2.4/5 GHz 双频 Wi-Fi 6（2T2R）与蓝牙 5.4。</li><li>HDR10+、HDR10 与 HLG 画面处理。</li><li>支持 AV1、VP9、H.265/HEVC 与 H.264 硬件解码；AV1、VP9、H.265/HEVC 最高支持 4K × 2K 60 fps。</li><li>HDMI 2.1、两个 USB 2.0、RJ45 有线网络、光纤音频与 Type-C 5V/2A 电源。</li></ul><h2>盒内配件</h2><p>礼盒、AC 适配器、HDMI 线、蓝牙语音飞鼠遥控器与用户手册。</p><p><strong>现以 US$288 接受预订（原价 US$379）。</strong>发货日期尚未公布。</p>',
            'footer' => [
                'tagline' => '小云 15P 预订信息',
                'summary' => '根据供应商来源整理的小云 15P 硬件资料；特价 US$288（原价 US$379），发货日期尚未公布。',
                'badges' => [
                    'coming_soon'   => '接受预订',
                    'specifications'=> '硬件规格已公布',
                    'commerce'      => '特价 US$288 · 原价 US$379',
                ],
                'benefits' => [
                    'platform' => [
                        'label'       => '已确认平台',
                        'description' => 'Android 14、Amlogic S905Y5、4 GB DDR3 与 64 GB eMMC。',
                    ],
                    'connectivity' => [
                        'label'       => '已确认连接',
                        'description' => '双频 Wi-Fi 6、蓝牙 5.4、RJ45、HDMI 2.1、光纤音频与 Type-C 电源。',
                    ],
                    'availability' => [
                        'label'       => '预订状态',
                        'description' => '现以 US$288 接受预订；发货日期尚未公布。',
                    ],
                ],
            ],
            'inbox' => [
                'items' => [
                    'box'    => '小云 15P 电视盒',
                    'power'  => 'AC 适配器（插头版本未指定）',
                    'hdmi'   => 'HDMI 线',
                    'remote' => '蓝牙语音飞鼠遥控器',
                    'manual' => '用户手册',
                ],
            ],
            'prelaunch' => [
                'subtitle' => '接受预订，搭载 Android 14、Amlogic S905Y5、Wi-Fi 6、蓝牙 5.4 与 4K HDR。',
                'detail'   => '特价 US$288 · 原价 US$379 · 发货日期尚未公布',
                'image_placeholder' => '小云 15P 电视盒正面',
                'badges' => [
                    'specs'        => 'Android 14',
                    'availability' => '接受预订',
                    'policy'       => '4 GB + 64 GB',
                ],
                'highlights' => [
                    'specs'        => 'Amlogic S905Y5 四核心 ARM Cortex-A55 处理器',
                    'availability' => '双频 Wi-Fi 6（2T2R）+ 蓝牙 5.4',
                    'policy'       => 'HDR10+/HDR10/HLG + AV1 硬件解码',
                ],
                'reassurance' => [
                    'badge' => '已确认产品资料',
                    'title' => '小云 15P 已确认硬件重点',
                    'copy'  => '下单前可查看已确认的硬件与盒内配件；预订价 US$288，发货日期尚未公布。',
                    'bullets' => [
                        'shipping'  => 'Android 14 + Amlogic S905Y5',
                        'warranty'  => '4 GB DDR3 + 64 GB eMMC',
                        'concierge' => 'Wi-Fi 6、蓝牙 5.4、4K HDR 与 AV1',
                    ],
                ],
                'faq_header' => [
                    'badge' => '15P 预订信息',
                    'title' => '已确认规格与预订详情',
                    'lead'  => '供应商资料确认所列硬件与盒内配件；预订价 US$288（原价 US$379），发货日期尚未公布。',
                ],
                'faq' => [
                    'specs' => [
                        'q' => '小云 15P 有哪些已确认规格？',
                        'a' => '15P 搭载 Android 14、Amlogic S905Y5 四核心 ARM Cortex-A55、4 GB DDR3、64 GB eMMC、双频 Wi-Fi 6、蓝牙 5.4，并支持 AV1 等主要格式硬件解码。',
                    ],
                    'availability' => [
                        'q' => '现在可以订购小云 15P 吗？',
                        'a' => '可以。15P 现以 US$288 接受预订（原价 US$379）；发货日期尚未公布。',
                    ],
                    'policy' => [
                        'q' => '小云 15P 盒内有哪些配件？',
                        'a' => '来源包装清单包含电视盒、AC 适配器、HDMI 线、蓝牙语音遥控器、用户手册与礼盒；不承诺特定插头版本。',
                    ],
                ],
            ],
            'best_for' => [
                'badge' => '已确认平台',
                'title' => 'Android 14、Wi-Fi 6 与蓝牙 5.4',
                'copy'  => '供应商规格列出 Android 14、Amlogic S905Y5、双频 Wi-Fi 6、蓝牙 5.4 与所列 4K 解码支持。',
                'bullets' => [
                    'primary'   => '4 GB DDR3 与 64 GB eMMC',
                    'secondary' => '双频 Wi-Fi 6、RJ45 有线网络与蓝牙 5.4',
                    'tertiary'  => '4K HDR 与 AV1、VP9、H.265/HEVC、H.264 硬件解码',
                ],
            ],
            'traffic' => [
                'badge' => '小云 15P 核心规格',
                'title' => 'Android 14 与规格表列出的有线、无线连接',
                'lead'  => '15P 规格列出 AV1、VP9、H.265/HEVC、H.264、双频 Wi-Fi 6、蓝牙 5.4、HDMI 2.1、RJ45、光纤音频、USB 与 Type-C 电源。',
                'bullets' => [
                    'shipping'  => 'HDMI 2.1、两个 USB 2.0、RJ45、光纤音频与 Type-C 电源',
                    'concierge' => '蓝牙语音飞鼠遥控器与 HDMI CEC 电视控制',
                    'warranty'  => '支持 HDR10+、HDR10、HLG、AV1、VP9、H.265/HEVC 与 H.264',
                ],
            ],
            'comparison' => [
                'badge' => '机型比较',
                'title' => '小云 15P vs 10P+ vs 9P',
                'lead'  => '以已确认的 15P 规格比较现有机型，不设置性能排名。',
                'cards' => [
                    'vs_10p' => [
                        'title'   => '15P vs 10P+',
                        'summary' => '两款皆提供 4 GB / 64 GB 配置；请依已确认的平台功能，以及是否需要立即购买来选择。',
                        'bullets' => [
                            'one'   => '15P：Amlogic S905Y5、Android 14、4 GB DDR3 与 64 GB eMMC',
                            'two'   => '15P：双频 Wi-Fi 6、蓝牙 5.4、HDMI 2.1 与光纤音频',
                            'three' => '15P 现以 US$288 接受预订（原价 US$379）；发货日期尚未公布',
                        ],
                        'link_label' => '查看小云 10P+',
                    ],
                    'vs_9p' => [
                        'title'   => '15P vs 9P',
                        'summary' => '15P 已确认采用 Android 14 与 Wi-Fi 6，但来源没有提供与 9P 的实测性能比较。',
                        'bullets' => [
                            'one'   => '15P：Android 14、Amlogic S905Y5、4 GB DDR3 与 64 GB eMMC',
                            'two'   => '15P：Wi-Fi 6、蓝牙 5.4、4K HDR 与 AV1 解码',
                            'three' => '升级前请比较目前 US$288 预订方案与您的 9P',
                        ],
                        'link_label' => '查看旧款小云 9P 页面',
                    ],
                ],
                'upgrade' => [
                    'title' => '比较说明',
                    'items' => [
                        'from_9p'   => '15P 来源确认 Android 14、Wi-Fi 6、蓝牙 5.4 与所列解码格式；未提供与 9P 的实测比较。',
                        'from_10p'  => '15P 来源未提供与 10P 或 10P+ 的实测性能比较。',
                        'new_buyer' => '15P 现以 US$288 接受预订（原价 US$379）；现有机型维持各自销售状态。',
                    ],
                ],
                'assurance' => [
                    'title' => '已确认与尚未公布的信息',
                    'items' => [
                        'shipping' => '已确认：硬件、接口、无线规格、解码格式与盒内配件',
                        'support'  => '预订价：特价 US$288、原价 US$379；发货日期尚未公布',
                        'warranty' => '尚未公布：15P 专属保修与退换货条款',
                    ],
                ],
            ],
        ],
    ],
    'compare' => [
        'meta' => [
            'title'       => '小云 15P vs 10P+ vs 10S｜机型规格比较',
            'description' => '比较 US$288 预订的小云 15P（原价 US$379）与 10P+、10S，包括硬件、存储、连接、影音支持、价格与销售状态。',
            'image_alt'   => '小云 15P、10P+ 与 10S 电视盒',
        ],
        'hero' => [
            'badge'    => '机型比较',
            'title'    => '小云 15P vs 10P+ vs 10S',
            'subtitle' => '比较 US$288 预订的 15P 与现售 10P+、10S 的价格、规格及销售状态。',
        ],
        'traffic' => [
            'badge' => '美国购买更安心',
            'title' => '比较 15P 预订与美国现货机型',
            'lead'  => '15P 现以 US$288 接受预订（原价 US$379），发货日期尚未公布；10P+ 与 10S 维持各自现售条款。',
            'bullets' => [
                'shipping'  => '48 小时美国发货，附本地电源/HDMI 与追踪号',
                'concierge' => '双语礼宾协助安装、Wi-Fi 调优、K 歌麦克风、Kids Mode',
                'warranty'  => '1 年美国保修 + 14 天退换货，礼宾协助处理',
            ],
            'links' => [
                'p15p'    => '预订 15P',
                'p10p'    => '购买 10P+',
                'p10s'    => '购买 10S',
                'faq'     => '查看 FAQ',
                'contact' => '联系礼宾客服',
            ],
        ],
        'differences' => [
            'next_generation' => [
                'title'       => 'Android 14 + 蓝牙 5.4',
                'description' => '15P 结合 Android 14、Amlogic S905Y5、双频 Wi-Fi 6 与蓝牙 5.4，预订价 US$288。',
            ],
            'premium_performance' => [
                'title'       => '顶级效能',
                'description' => '双倍内存与存储空间，多工处理更顺畅，可安装更多应用程式。',
            ],
            'family_entertainment' => [
                'title'       => '家庭娱乐',
                'description' => '独家儿童模式与卡拉 OK 功能，全家同乐。',
            ],
            'smart_value' => [
                'title'       => '聪明首选',
                'description' => '维持 4K 画质，同时拥有更亲民的价格。',
            ],
        ],
        'products' => [
            '15p' => [
                'lead'     => '接受预订的 Android 14 硬件，规格列有 Wi-Fi 6、蓝牙 5.4 与 4K 解码。',
                'fit_label'=> '接受预订',
                'fit_copy' => 'Android 14、Amlogic S905Y5、双频 Wi-Fi 6、蓝牙 5.4 与所列 4K 解码支持；特价 US$288（原价 US$379）。',
                'bullets' => [
                    'processor_os'        => 'Amlogic S905Y5 四核心 Cortex-A55 + Android 14',
                    'memory_connectivity' => '4 GB DDR3 / 64 GB eMMC + Wi-Fi 6 / 蓝牙 5.4',
                    'video_remote'        => '4K HDR + AV1 解码 + 蓝牙语音飞鼠遥控器',
                ],
                'cta' => '预订 15P',
            ],
            '10p' => [
                'lead'    => '旗舰硬件、专属家庭功能与最快速的运算效能。',
                'fit_label'=> '最适合',
                'fit_copy' => '客厅主机、K 歌聚会、爱看体育赛事，以及想要最快硬件体验的家庭。',
                'bullets' => [
                    'ram_storage' => 'Android 12、4GB 内存、64GB 存储与 AV1 解码',
                    'apps'        => '内建儿童模式与卡拉 OK 应用',
                    'remote'      => '蓝牙 AI 语音遥控 + Wi-Fi 6',
                ],
                'cta'     => '选购 10P+',
            ],
            '10s' => [
                'lead'    => '提供 4K 影音播放的精省配置，适合重视性价比的家庭。',
                'fit_label'=> '最适合',
                'fit_copy' => '卧室、客房、第二台电视，以及希望用更低预算稳定收看的买家。',
                'bullets' => [
                    'ram_storage' => 'Android 12、2GB 内存、32GB 存储，满足日常播放',
                    'remote'      => '4K HDR + AV1 解码，搭配语音遥控',
                    'ports'       => '内建 HDMI、USB 3.0 与有线网络接口',
                ],
                'cta'     => '选购 10S',
            ],
        ],
        'aria' => [
            'hero_actions'   => '主要产品操作',
            'hero_highlights'=> '为什么大家会选小云电视盒',
            'traffic_actions'=> '比较页主要操作',
            'differences'    => '机型重点差异',
            'product_list'   => '产品重点卡片',
            'product_alt_15p'=> '小云 15P 电视盒',
            'product_alt_10p'=> '小云电视盒 10P+',
            'product_alt_10s'=> '小云电视盒 10S',
            'comparison_15p' => '小云 15P 功能比较',
            'comparison_10p' => '小云电视盒 10P+ 功能比较',
            'comparison_10s' => '小云电视盒 10S 功能比较',
            'final_cta'      => '最终行动号召',
        ],
        'schema' => [
            'item_list_name' => '小云电视盒比较机型列表',
        ],
        'confidence' => [
            'badge' => '下单更清楚',
            'title' => '挑好机型后，后续流程也有本地支援',
            'lead'  => '以已确认规格与价格比较三款机型；15P 接受预订，发货日期尚未公布。',
            'cards' => [
                'official' => [
                    'title' => '美国官方销售通路',
                    'copy'  => '向原厂授权的美国经销商购买正版 SVICLOUD 设备。',
                ],
                'shipping' => [
                    'title' => '内华达州追踪发货',
                    'copy'  => '从美国现货寄出，附追踪资讯与适合本地使用的完整配件。',
                ],
                'concierge' => [
                    'title' => '双语安装协助',
                    'copy'  => '提供 English / 中文 开箱协助、Wi-Fi 调优与遥控器配对说明。',
                ],
                'warranty' => [
                    'title' => '购买后仍有保修',
                    'copy'  => '已公布的保修与退换货条款只适用目前可购买的机型；15P 专属条款尚未公布。',
                ],
            ],
            'timeline' => [
                'badge' => '订单流程',
                'title' => '从选机到家中安装',
                'lead'  => '比较页的目的，是让下一步更明确，而不是让你承担更多风险。',
                'steps' => [
                    'choose' => [
                        'title' => '先选适合房间与预算的机型',
                        'copy'  => '用比较表与「最适合」说明，先判断哪一台最符合你的使用情境。',
                    ],
                    'order' => [
                        'title' => '从美国官方网站下单',
                        'copy'  => '直接在官方美国站结账，比起灰市卖场更容易取得保修与售后协助。',
                    ],
                    'dispatch' => [
                        'title' => '收到追踪与发货通知',
                        'copy'  => '内华达州本地配货让物流更清楚，若有问题也更容易追踪与处理。',
                    ],
                    'setup' => [
                        'title' => '需要时可找礼宾协助安装',
                        'copy'  => '第一次安装、Wi-Fi 设置或基本功能上手，都可以联系礼宾客服协助。',
                    ],
                ],
            ],
        ],
        'comparison' => [
            'title' => '功能比较',
            'rows'  => [
                'processor' => [
                    'label' => '处理器',
                    'p15p'  => 'Amlogic S905Y5 四核心 Cortex-A55',
                    'p10p'  => 'Amlogic S928X 八核心',
                    'p10s'  => '四核心处理器',
                ],
                'ram_storage' => [
                    'label' => '内存 / 存储',
                    'p15p'  => '4GB DDR3 / 64GB eMMC',
                    'p10p'  => '4GB / 64GB',
                    'p10s'  => '2GB / 32GB',
                ],
                'operating_system' => [
                    'label' => '操作系统',
                    'p15p'  => 'Android 14',
                    'p10p'  => 'Android 12',
                    'p10s'  => 'Android 12',
                ],
                'connectivity' => [
                    'label' => '连接能力',
                    'p15p'  => 'Wi-Fi 6 2T2R、蓝牙 5.4、RJ45',
                    'p10p'  => 'Wi-Fi 6、蓝牙语音遥控、Gigabit LAN',
                    'p10s'  => '双频 Wi-Fi、蓝牙、有线网络',
                ],
                'video_quality' => [
                    'label' => '影像品质',
                    'p15p'  => '4K HDR10+/HDR10/HLG、AV1 解码',
                    'p10p'  => '4K HDR、AV1 解码',
                    'p10s'  => '4K HDR、AV1 解码',
                ],
                'voice_remote' => [
                    'label' => '语音遥控器',
                    'p15p'  => '附蓝牙语音飞鼠遥控器',
                    'p10p'  => '支持',
                    'p10s'  => '支持',
                ],
                'kids_app' => [
                    'label' => '儿童应用',
                    'p15p'  => '内含',
                    'p10p'  => '内含',
                    'p10s'  => '无',
                ],
                'karaoke_mode' => [
                    'label' => '卡拉 OK 模式',
                    'p15p'  => '内含',
                    'p10p'  => '内含',
                    'p10s'  => '无',
                ],
                'best_for' => [
                    'label' => '机型定位',
                    'p15p'  => 'Android 14、Wi-Fi 6、蓝牙 5.4；预订 US$288',
                    'p10p'  => '家庭、运动迷、4K 家庭剧院',
                    'p10s'  => '精省用户 / 次要房间',
                ],
            ],
        ],
        'final_cta' => [
            'badge'   => '选择您的机型',
            'title'   => '准备好升级家的观影体验了吗？',
            'copy'    => '选择最符合您家庭需求与预算的小云电视盒机型。',
            'cta_10p' => '选购小云电视盒 10P+',
            'cta_10s' => '选购小云电视盒 10S',
            'cta_15p' => '预订小云 15P',
        ],
        'sticky_buy' => [
            'aria_label' => '固定比较操作',
            'label'      => '准备好选机了？直接下单美国现货',
            'cta_10p'    => '购买 10P+',
            'cta_10s'    => '购买 10S',
            'cta_15p'    => '预订 15P',
        ],
        'faq' => [
            'sections' => [
                'device_models' => [
                    'title' => '装置与选购',
                    'items' => [
                        'model_choice' => [
                            'question' => '要买哪一款小云电视盒？',
                            'answer'   => '想要旗舰性能、儿童模式与 K 歌请选择 10P+；次要房间或精省预算选 10S。详细请看<a href="{{compare_url}}">比较表</a>。',
                        ],
                        'international_use' => [
                            'question' => '小云电视盒在海外可以用吗？',
                            'answer'   => '可以。装置使用所在地的网络（美加与海外皆可）。各串流服务仍受自身地区政策限制，请使用合法官方来源。',
                        ],
                        'box_contents' => [
                            'question' => '盒内附带哪些配件？',
                            'answer'   => '主机、美规电源适配器、HDMI 线、蓝牙语音遥控器、快速入门指南。',
                        ],
                    ],
                ],
                'setup_activation' => [
                    'title' => '安装与启动',
                    'items' => [
                        'power_on' => [
                            'question' => '如何启动小云电视盒？',
                            'answer'   => '接上 HDMI 与电源，切换到对应的 HDMI 输入，按照画面指示完成设置。参考<a href="{{setup_guide_url}}">安装指南</a>有图解。',
                        ],
                        'change_language' => [
                            'question' => '如何切换系统语言？',
                            'answer'   => '设置 → 语言与输入法，选 English 或 中文，随时可切换，不需恢复出厂设置。',
                        ],
                        'remote_pairing' => [
                            'question' => '遥控器没有反应怎么办？',
                            'answer'   => '先换电池，再按住 VOL- + VOL+ 靠近主机配对；如有需要重启主机后再配对。',
                        ],
                    ],
                ],
                'apps_content' => [
                    'title' => 'App 与内容',
                    'items' => [
                        'preinstalled' => [
                            'question' => '小云专属 App 会预先安装吗？',
                            'answer'   => '出厂是原生 Android 桌面，未预载第三方串流 App。礼宾客服可指引安全安装。',
                        ],
                        'third_party' => [
                            'question' => '可以安装 Netflix 或其他 App 吗？',
                            'answer'   => '可从 Google Play 或官方来源安装。相容性与画质依各服务而定，请使用合法授权来源。',
                        ],
                        'family_content' => [
                            'question' => '有适合孩子的内容吗？',
                            'answer'   => '支持儿童模式、双语内容与家长监护，并可安装适合全家的官方 App。',
                        ],
                        'adult_content' => [
                            'question' => '有成人内容吗？',
                            'answer'   => '没有。我们不预载成人内容，也不支持非法或未成年不宜的服务。请遵守当地法律与平台政策。',
                        ],
                    ],
                ],
                'features_limitations' => [
                    'title' => '功能与限制',
                    'items' => [
                        'karaoke_support' => [
                            'question' => '每台都支持 K 歌吗？',
                            'answer'   => '10P+ 支持 K 歌与双麦克风；10S 专注核心串流功能，没有额外 K 歌特色。',
                        ],
                        'voice_control' => [
                            'question' => '语音控制怎么用？',
                            'answer'   => '蓝牙语音遥控可搜索、开 App、控制播放；如需重配对，按 VOL- + VOL+。',
                        ],
                        'subtitle_speed' => [
                            'question' => '字幕速度可以调吗？',
                            'answer'   => '字幕时间由各 App/播放器控制，请在 App 内调整；系统没有全域字幕速度设定。',
                        ],
                    ],
                ],
                'troubleshooting_support' => [
                    'title' => '疑难排解与客服',
                    'items' => [
                        'buffering' => [
                            'question' => '画面模糊或常缓冲，怎么办？',
                            'answer'   => '测速、优先使用有线或 Wi-Fi 6，将主机靠近路由器并重启主机/路由器。更多建议见<a href="{{setup_guide_url}}">安装指南</a>。',
                        ],
                        'orz_installer' => [
                            'question' => '某串流 App 或安装程序无法开启怎么办？',
                            'answer'   => '请从官方来源重新安装、清除 App 缓存并重启。避免非官方 APK。如仍有问题，提供 App 名称与错误讯息给客服。',
                        ],
                        'contact_support' => [
                            'question' => '怎么联系客服电话？',
                            'answer'   => '通过联系表单并附上订单编号、装置型号与截图：<a href="{{support_url}}">联络礼宾客服</a>。',
                        ],
                    ],
                ],
            ],
        ],
    ],

    // FAQ page (简体中文)
    'faq' => [
        'hero' => [
            'badge'      => '支持中心',
            'title'      => '小云电视盒常见问题',
            'subtitle'   => '快速了解选购、安装与使用小云电视盒的重点。',
            'cta_primary'=> '查看安装指南',
            'cta_secondary' => '比较机型',
        ],
        'sections' => [
            'device_models' => [
                'title' => '装置与选购',
                'items' => [
                    'model_choice' => [
                        'question' => '要买哪一款小云电视盒？',
                        'answer'   => '旗舰性能、儿童模式与 K 歌请选择 10P+；精省/次卧选 10S。详见<a href="{{compare_url}}">比较表</a>。',
                    ],
                    'international_use' => [
                        'question' => '小云电视盒在海外可以用吗？',
                        'answer'   => '可以，在美加及海外使用本地网络即可。各串流服务仍受自身地区政策限制，请使用合法官方来源。',
                    ],
                    'box_contents' => [
                        'question' => '盒内附带哪些配件？',
                        'answer'   => '主机、美规电源、HDMI 线、蓝牙语音遥控器、快速入门指南。',
                    ],
                ],
            ],
            'setup_activation' => [
                'title' => '安装与启动',
                'items' => [
                    'power_on' => [
                        'question' => '如何启动小云电视盒？',
                        'answer'   => '接上 HDMI 与电源，切到对应 HDMI 输入，依画面完成设置。图解请看<a href="{{setup_guide_url}}">安装指南</a>。',
                    ],
                    'change_language' => [
                        'question' => '如何切换系统语言？',
                        'answer'   => '设置 → 语言与输入法，选 English 或 中文，可随时切换，无需恢复出厂设置。',
                    ],
                    'remote_pairing' => [
                        'question' => '遥控器没有反应怎么办？',
                        'answer'   => '先换电池，再按 VOL- + VOL+ 靠近主机配对；必要时重启后再配对。',
                    ],
                ],
            ],
            'apps_content' => [
                'title' => 'App 与内容',
                'items' => [
                    'preinstalled' => [
                        'question' => '小云专属 App 会预先安装吗？',
                        'answer'   => '出厂是原生 Android 桌面，未预载第三方串流 App。礼宾客服可指引安全安装。',
                    ],
                    'third_party' => [
                        'question' => '可以安装 Netflix 或其他 App 吗？',
                        'answer'   => '可从 Google Play 或官方来源安装。相容性与画质依各服务而定，请使用合法授权来源。',
                    ],
                    'family_content' => [
                        'question' => '有适合孩子的内容吗？',
                        'answer'   => '支持儿童模式、双语内容与家长监护，并可安装适合全家的官方 App。',
                    ],
                    'adult_content' => [
                        'question' => '有成人内容吗？',
                        'answer'   => '没有。不预载成人内容，也不支持非法或未成年不宜的服务。请遵守当地法律与平台政策。',
                    ],
                ],
            ],
            'features_limitations' => [
                'title' => '功能与限制',
                'items' => [
                    'karaoke_support' => [
                        'question' => '每台都支持 K 歌吗？',
                        'answer'   => '10P+ 支持 K 歌与双麦克风；10S 专注核心串流功能，无额外 K 歌特色。',
                    ],
                    'voice_control' => [
                        'question' => '语音控制怎么用？',
                        'answer'   => '蓝牙语音遥控可搜索、开 App、控制播放；如需重配对，按 VOL- + VOL+。',
                    ],
                    'subtitle_speed' => [
                        'question' => '字幕速度可以调吗？',
                        'answer'   => '字幕时间由各 App/播放器控制，请在 App 内调整；系统没有全域字幕速度。',
                    ],
                ],
            ],
            'troubleshooting_support' => [
                'title' => '疑难排解与客服',
                'items' => [
                    'buffering' => [
                        'question' => '画面模糊或常缓冲，怎么办？',
                        'answer'   => '测速，优先用有线或 Wi-Fi 6，把主机靠近路由器并重启主机/路由器。更多建议见<a href="{{setup_guide_url}}">安装指南</a>。',
                    ],
                    'orz_installer' => [
                        'question' => '某串流 App 或安装程序无法开启怎么办？',
                        'answer'   => '请从官方来源重新安装、清除 App 缓存并重启。避免非官方 APK。如仍有问题，提供 App 名称与错误信息给客服。',
                    ],
                    'stuck_loading' => [
                        'question' => '小云盒子停在加载画面或 Logo 怎么办？',
                        'answer'   => '请先拔掉电源 2-3 分钟，移除 USB 或其他配件，只保留电源与 HDMI。重新插电后，马上对着盒子连续按遥控器红色电源键约 5-20 次，直到 Recovery 出现。请先选 <strong>Reboot system now</strong>；若仍卡住，再次进入 Recovery 后选 <strong>Factory reset</strong> 或 <strong>Wipe data/factory data reset</strong>。恢复出厂设置会清除设置与已安装 App。',
                    ],
                    'contact_support' => [
                        'question' => '怎么联系客服？',
                        'answer'   => '通过联系表单并附上订单编号、装置型号与截图：<a href="{{support_url}}">联络礼宾客服</a>。',
                    ],
                ],
            ],
        ],
    ],
    'shipping_policy' => [
        'hero' => [
            'badge'    => '运送信息',
            'title'    => '运送政策',
            'subtitle' => '从内华达州仓库快速可靠地送达您家门口。美国境内全面免运费。',
        ],
        'sections' => [
            'processing' => [
                'title' => '订单处理',
                'items' => [
                    'cutoff'   => '周一至周五太平洋时间下午 2:00 前下单，当日即可处理出货。',
                    'dispatch' => '大多数订单在付款确认后 24-48 小时内出货。',
                    'tracking' => '出货后您将收到包含追踪号码的确认邮件。',
                ],
            ],
            'domestic' => [
                'title' => '美国境内运送',
                'items' => [
                    'standard' => '标准运送依地区不同，通常 2-5 个工作天送达。',
                    'carriers' => '依包裹大小与目的地，我们使用 USPS、UPS 或 FedEx 配送。',
                    'free'     => '美国境内所有订单皆享免运费，无最低消费限制。',
                ],
            ],
            'canada' => [
                'title' => '加拿大运送',
                'items' => [
                    'timeframe' => '加拿大订单通常 5-7 个工作天送达，含海关处理时间。',
                    'customs'   => '包裹会附上正确的海关申报以顺利通关。',
                    'duties'    => '进口关税、税金与海关费用由收件人负担，不含在商品价格内。',
                ],
            ],
            'issues' => [
                'title' => '运送问题',
                'items' => [
                    'delays'  => '旺季或恶劣天气可能造成运送延迟。请追踪包裹以获取最新状态。',
                    'damaged' => '若包裹送达时有损坏，请在 48 小时内联系客服并附上损坏照片。',
                    'lost'    => '若包裹显示「已送达」但未收到，请在 7 天内联系我们。我们会与承运商协调处理。',
                ],
            ],
        ],
        'support' => [
            'title' => '对运送有疑问？',
            'copy'  => '双语客服团队随时为您追踪订单或解决任何运送问题。',
            'cta'   => '联系客服',
        ],
    ],
    'privacy_policy' => [
        'hero' => [
            'badge'     => '重视您的隐私',
            'title'     => '隐私权政策',
            'subtitle'  => '我们尊重您的隐私，致力保护您的个人信息。',
            'effective' => '生效日期：2025 年 1 月 1 日',
        ],
        'sections' => [
            'collect' => [
                'title' => '我们收集的信息',
                'items' => [
                    'personal' => '<strong>个人信息：</strong>您下单或联系我们时提供的姓名、电子邮件、电话号码与送货地址。',
                    'payment'  => '<strong>付款信息：</strong>信用卡资料通过我们的支付服务商安全处理，不会储存在我们的服务器上。',
                    'device'   => '<strong>设备信息：</strong>您浏览本网站时的浏览器类型、IP 地址与设备识别码。',
                    'usage'    => '<strong>使用数据：</strong>浏览页面、停留时间与互动信息，用以改善您的体验。',
                ],
            ],
            'use' => [
                'title' => '我们如何使用您的信息',
                'items' => [
                    'orders'      => '处理并完成您的订单，包括运送与到货通知。',
                    'support'     => '提供客户支持并回复您的询问。',
                    'improve'     => '根据您的反馈与使用模式改善我们的网站、产品与服务。',
                    'communicate' => '发送订单确认、运送更新，以及偶尔的促销优惠（您可随时取消订阅）。',
                ],
            ],
            'sharing' => [
                'title' => '信息分享',
                'items' => [
                    'processors' => '<strong>支付处理商：</strong>我们与符合 PCI 规范的安全支付处理商分享付款信息以完成交易。',
                    'shipping'   => '<strong>物流承运商：</strong>我们与承运商（USPS、UPS、FedEx）分享您的姓名与地址以配送订单。',
                    'legal'      => '<strong>法律要求：</strong>当法律要求或为保护我们的权益与安全时，我们可能披露信息。',
                    'nosell'     => '<strong>我们不会出售您的个人信息</strong>给第三方作为营销用途。',
                ],
            ],
            'cookies' => [
                'title' => 'Cookie 与追踪',
                'items' => [
                    'essential' => '<strong>必要 Cookie：</strong>网站功能、购物车与结账流程所必需。',
                    'analytics' => '<strong>分析 Cookie：</strong>帮助我们了解访客如何使用网站以改善性能。',
                    'control'   => '您可通过浏览器设置控制 Cookie。停用 Cookie 可能影响网站功能。',
                ],
            ],
            'security' => [
                'title' => '数据安全',
                'items' => [
                    'ssl'    => '所有传输至本网站的数据均使用 SSL/TLS 技术加密。',
                    'pci'    => '付款处理由符合 PCI-DSS 规范的服务商处理。',
                    'access' => '个人信息的访问仅限于授权人员。',
                ],
            ],
            'rights' => [
                'title' => '您的权利',
                'items' => [
                    'access'  => '<strong>访问：</strong>要求获取我们持有的您的个人信息副本。',
                    'correct' => '<strong>更正：</strong>要求更正不正确或不完整的信息。',
                    'delete'  => '<strong>删除：</strong>要求删除您的个人信息，但须遵守法定保留要求。',
                    'optout'  => '<strong>退出：</strong>随时使用邮件中的链接取消订阅营销邮件。',
                ],
            ],
        ],
        'support' => [
            'title' => '有隐私相关问题？',
            'copy'  => '如对本政策有疑问或希望行使您的隐私权，请联系我们的团队。',
            'cta'   => '联系我们',
        ],
    ],
    'support' => [
        'hero' => [
            'badge'    => '支持表单',
            'title'    => '提交礼宾客服支持请求',
            'subtitle' => '留下设备信息与遇到的情况，中英双语客服最快一个工作天内回复。',
        ],
        'review' => [
            'title' => '对购买体验满意吗？',
            'copy'  => 'Google 顾客评论已整合至结账流程。商品送达后，Google 可能会邀请符合资格的买家填写简短问卷，为我们留下商店评分。',
            'note'  => '我们只希望收集真实的顾客反馈，因此本页说明评论流程，不显示虚假星级或占位文字。',
            'cta'   => '留下 Google 评价',
        ],
        'help' => [
            'contact' => [
                'title' => '直接联系我们',
                'copy'  => '电话、邮件或礼宾表单均可。',
                'cta'   => '联系礼宾客服',
            ],
            'install' => [
                'title' => 'App 安装教学',
                'copy'  => '退货前，可先依步骤完成 App 安装或更新。',
                'cta'   => '打开 App 教学',
            ],
            'faq' => [
                'title' => '快速自助',
                'copy'  => '想先自行查询？常见问题整理了选购、安装与疑难解答重点。',
                'cta'   => '前往 FAQ',
            ],
        ],
    ],
    'about' => [
        'story' => [
            'warehouse_alt'     => '小云电视盒内华达州仓库，展示库存与出货作业',
            'warehouse_caption' => '我们的内华达州仓库：本地库存，美国配送更迅速',
        ],
    ],
];

return array_replace_recursive($base, $overrides);
