<?php
/**
 * SVICLOUD Lumen Theme translations (Chinese - Simplified)
 */

$base = include __DIR__ . '/zh_TW.php';

$overrides = [
    'header' => [
        'nav' => [
            'home'     => '首页',
            'compare'  => '比较机型',
            'compare_evpad'  => '小云电视盒 10P+ vs EVPAD 10 Pro',
            'compare_ubox12' => '小云电视盒 10P+ vs 安博电视盒 12 代',
            'blog'     => '博客',
            'shop'     => '选购',
            'faq'      => '常见问题',
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
            'apps'            => '内容与合规',
            'post_setup'      => '安装后设定',
            'troubleshooting' => '疑难排解',
            'resources'       => '延伸资源',
            'support'         => '寻求支援',
            'faq'             => '常见问题',
        ],
        'nav_summaries' => [
            'overview'        => '快速了解重点与礼宾支援。',
            'setup'           => '完成接线、语言选择与语音遥控配对。',
            'apps'            => '了解如何挑选合法串流服务。',
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
            'badge' => '内容合规',
            'title' => '选择合法串流服务的提醒',
            'lead'  => '新增服务前，请先确认授权与可用地区。以下说明如何评估常见内容类别。',
            'items' => [
                'live' => [
                    'title' => '直播电视频道',
                    'copy'  => '订阅时请确认业者公开授权资讯、使用条款与联系方式，确保来源可信。',
                ],
                'kids' => [
                    'title' => '亲子与儿童内容',
                    'copy'  => '选择具备家长监护、分龄标示与合法播映权的服务，保护孩子观影安全。',
                ],
                'karaoke' => [
                    'title' => '音乐与卡拉 OK',
                    'copy'  => '仅使用已取得词曲授权的音乐与卡拉 OK 平台，尊重演出与版权规范。',
                ],
                'regional' => [
                    'title' => '区域性内容',
                    'copy'  => '海外观赏时请再次确认是否具有跨国播映权，并遵守地区限制与法律。',
                ],
                'cherry' => [
                    'title' => '成人内容',
                    'copy'  => '成人节目受法律严格管制，仅在合法地区并符合年龄限制下才可启用。',
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
            'lead'  => '遇到问题先试试以下步骤，通常能解决开箱第一天的状况。',
            'items' => [
                'remote' => [
                    'title' => '遥控器无法配对',
                    'copy'  => '取出电池重新装回，靠近机身再按 <kbd>VOL-</kbd> + <kbd>VOL+</kbd>，直到看到配对成功提示。',
                ],
                'streaming' => [
                    'title' => '画面模糊或缓冲',
                    'copy'  => '重新启动路由器、靠近 Wi-Fi，或改用网路线提升稳定度。',
                ],
                'orz' => [
                    'title' => '串流 App 无法开启',
                    'copy'  => '确认该服务为合法来源，检查网路连线，并直接联系该平台客服。非官方安装来源不在支援范围内。',
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
                            'body'    => '<p>所有小云电视盒 10P+ 与 10S 皆由内华达州库存备货，检查序号、封条与最新韧体后才寄出，并提供物流追踪码。</p>\n<ul>\n  <li>提供 UPS／USPS 需签收的配送选项，包裹更安全。</li>\n  <li>美国现货免去海关延误与水货或翻新机的风险。</li>\n  <li>包装内含原厂语音遥控器、美规电源供应器与完整配件。</li>\n</ul>',
                        ],
                        'warranty' => [
                            'heading' => '一年美国保固与简单退换货',
                            'body'    => '<p>每台机器皆附一年美国保固，由礼宾团队协助检测与维修；正常使用下若发生问题，会在美国本地安排零件或换机。</p>\n<ul>\n  <li>购买后 30 天内保留未拆封，可申请退换货。</li>\n  <li>维修或更换全程在美国处理，不需跨国寄件等待。</li>\n  <li>韧体更新、功能咨询与故障排除都由礼宾客服跟进。</li>\n</ul>',
                        ],
                        'concierge' => [
                            'heading' => '中英文礼宾客服全程陪伴',
                            'body'    => '<p>需要设定语音遥控器或调整网路环境吗？透过 <a href=\"{{contact_url}}\">礼宾客服表单</a> 留言，我们会在一个工作天内以中英文回复。</p>\n<ul>\n  <li>提供视讯截图与逐步指引，协助完成开箱与连线。</li>\n  <li>提醒仅安装具授权的串流服务，确保合法使用。</li>\n  <li>安装完成后持续协助韧体更新与问题排解。</li>\n</ul>',
                        ],
                        'billing' => [
                            'heading' => '安全结帐，价格透明',
                            'body'    => '<p>网站采用 SSL 安全结帐，支援信用卡与 PayPal，依法开立销售税。价格透明，结帐画面即会显示运费与税额，并附上物流追踪码。</p>\n<ul>\n  <li>无额外的处理费或盒装加价。</li>\n  <li>一次购买终身使用，没有月费或自动续约。</li>\n  <li>配送后立即寄送收据与追踪码，方便掌握物流。</li>\n</ul>',
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
                            'body'    => '<p>仅安装清楚揭示授权资讯的服务，若无法提供合法权利证明或联络方式，请勿使用。</p>\n<ul>\n  <li>阅读服务条款、隐私政策与智慧财产权声明。</li>\n  <li>确认业者提供有效客服信箱或公司资料。</li>\n  <li>保存订阅或购买凭证，必要时可提供给执法单位或 ISP。</li>\n</ul>',
                        ],
                        'regional' => [
                            'heading' => '检查地区限制',
                            'body'    => '<p>许多平台仅授权特定国家播放。安装前请确认是否允许在您所在地使用，并遵守地方法规。</p>\n<ul>\n  <li>了解是否禁止使用 VPN 或代理跨区观看。</li>\n  <li>确认自己符合年龄、居住或身份条件。</li>\n  <li>若涉及进口或报关，也应先行了解相关规范。</li>\n</ul>',
                        ],
                        'security' => [
                            'heading' => '维护装置安全',
                            'body'    => '<p>避免从未知来源下载 APK。请透过官方商店或可信平台取得安装档，降低恶意软体风险。</p>\n<ul>\n  <li>下载前使用防毒软体扫描安装档案。</li>\n  <li>启用强密码与双重验证保护帐号。</li>\n  <li>留意平台是否提供定期安全更新与公告。</li>\n</ul>',
                        ],
                        'documentation' => [
                            'heading' => '保留合规纪录',
                            'body'    => '<p>建议将交易记录、客服往来与授权证明整理存档，必要时可快速提供证明。</p>\n<ul>\n  <li>截图订阅页面、收据与授权条款。</li>\n  <li>整理客服往来纪录，方便后续追踪。</li>\n  <li>如有多人共用，请同步相关规范与文件。</li>\n</ul>',
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
                            'body'    => '<p>10P+ 采用 Amlogic S928X 八核心处理器，搭配 4GB RAM 与 64GB 储存；10S 则配置四核心处理器、4GB RAM 与 32GB 储存。两款皆支援 4K HDR、HDMI 2.1、双频 Wi-Fi 6 与 Gigabit 有线网路。</p>\n<ul>\n  <li>USB 3.0／USB 2.0 可外接硬碟、麦克风或周边配件。</li>\n  <li>HDMI eARC 与光纤输出提供家庭剧院等级的音讯选择。</li>\n  <li>低噪音散热设计，长时间追剧也能维持稳定。</li>\n</ul>',
                        ],
                        'streaming' => [
                            'heading' => 'AV1 编码与智慧缓冲',
                            'body'    => '<p>两款机型皆支援 AV1、HEVC、VP9 等高效率编码，让画质更清晰、网路用量更低；自动缓冲与帧率匹配，确保体育与戏剧播放流畅。</p>\n<ul>\n  <li>根据节目自动切换 24p／30p／60p，减少抖动。</li>\n  <li>全球 CDN 加速路径优化北美连线，海外内容一样顺畅。</li>\n  <li>定期韧体更新加入新功能与效能调校。</li>\n</ul>',
                        ],
                        'remote' => [
                            'heading' => '语音遥控，一键找到节目',
                            'body'    => '<p>蓝牙语音遥控支援语音搜寻、数字快捷键与媒体控制，只要长按音量键即可重新配对，搜寻频道或 App 更快速。</p>\n<ul>\n  <li>可学习电视与音响的电源、音量，一支遥控搞定。</li>\n  <li>按键具背光设计，晚上追剧也看得清楚。</li>\n  <li>遥控器韧体可 OTA 更新，维持灵敏度。</li>\n</ul>',
                        ],
                        'family' => [
                            'heading' => '全家娱乐一次满足',
                            'body'    => '<p>小云电视盒不只有直播，也支援儿童分龄模式、居家卡拉 OK 与密码锁定的成人专区，可依需求调整内容权限。</p>\n<ul>\n  <li>建立儿童模式，仅显示教育与卡通内容。</li>\n  <li>卡拉 OK 功能支援排歌、合唱与歌词显示。</li>\n  <li>可依需求扩充各地语系的合法串流服务。</li>\n</ul>',
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
                            'body'    => '<table class=\"guides-article__table\">\n  <thead>\n    <tr>\n      <th>项目</th>\n      <th>小云电视盒 10P+</th>\n      <th>小云电视盒 10S</th>\n    </tr>\n  </thead>\n  <tbody>\n    <tr>\n      <td>处理器与记忆体</td>\n      <td>Amlogic S928X · 4GB RAM · 64GB 储存</td>\n      <td>四核心 CPU · 4GB RAM · 32GB 储存</td>\n    </tr>\n    <tr>\n      <td>连线能力</td>\n      <td>Wi-Fi 6 双频 · Gigabit LAN · USB 3.0 + USB 2.0</td>\n      <td>Wi-Fi 6 双频 · Gigabit LAN · 双 USB 2.0</td>\n    </tr>\n    <tr>\n      <td>音讯输出</td>\n      <td>HDMI 2.1 · 光纤 SPDIF</td>\n      <td>HDMI 2.1 · 3.5mm AUX</td>\n    </tr>\n    <tr>\n      <td>最佳使用情境</td>\n      <td>家庭剧院、多 App 安装、KTV 加强</td>\n      <td>日常串流、小空间、副电视</td>\n    </tr>\n  </tbody>\n</table>\n<p>两款机型皆支援相同韧体功能，差别在于储存空间、连接介面与周边扩充性。</p>',
                        ],
                        'ten_p' => [
                            'heading' => '适合选择 10P+ 的情境',
                            'body'    => '<p>以下情境建议选择 10P+：</p>\n<ul>\n  <li>需要连接扩大机、Soundbar 或光纤音响的家庭剧院。</li>\n  <li>需要外接大容量储存装置或管理大型媒体资料库。</li>\n  <li>常使用 Karaoke、Party 模式，想要最强的处理效能。</li>\n</ul>',
                        ],
                        'ten_s' => [
                            'heading' => '适合选择 10S 的情境',
                            'body'    => '<p>以下情境适合 10S：</p>\n<ul>\n  <li>卧室、租屋或小坪数空间，需要精巧机身。</li>\n  <li>以直播与随选内容为主，只需安装少量额外 App。</li>\n  <li>想要礼宾服务与最新韧体，但预算更精简。</li>\n</ul>',
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
            'badge'   => '美国授权经销商',
            'title'   => '中文电视盒天花板，全球娱乐任一电视即插即用',
            'title_lead' => '中文电视盒天花板',
            'title_separator' => '，',
            'title_tail' => '全球娱乐任一电视即插即用',
            'copy'    => '小云电视盒 美国代理，提供 SVICLOUD 10P+ 与 10S 现货，一次打包 4K 体育、亚洲戏剧、卡拉 OK 与儿童内容，中英双语礼宾服务与一年保固，无月费。',
            'bullets' => [
                'shipping' => '美国现货配送',
                'warranty' => '一年美国保固',
                'fees'     => '无月租费',
            ],
            'cta' => [
                'primary' => '选购 10P+',
                'bundles' => '查看优惠组合',
                'compare' => '比较各机型',
            ],
            'card' => [
                'badge'     => '直播',
                'headline'  => '小云电视盒 10P+ 硬件一览',
                'timestamp' => '美国最新批次 · 2024 年 7 月',
                'stat'      => '4GB 内存 · 64GB 存储',
                'specs'     => [
                    'processor'    => [
                        'label' => '处理器',
                        'value' => 'Amlogic S928X · 八核心',
                    ],
                    'connectivity' => [
                        'label' => '连线能力',
                        'value' => 'Wi-Fi 6 双频 + Gigabit 有线网络',
                    ],
                    'video'        => [
                        'label' => '高画质解码',
                        'value' => 'AV1 解码 · 4K HDR',
                    ],
                    'extras'       => [
                        'label' => '遥控与配件',
                        'value' => '蓝牙语音遥控 · USB 3.0 扩充',
                    ],
                ],
                'footer' => [
                    'shipping' => '美国配送 · 配送时程依承运商为准',
                    'support'  => '一年美国保固 · 专人安装服务',
                ],
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
            'subtitle' => '专为超高清 4K 体育、追剧马拉松与卡拉 OK 夜晚打造，不卡顿。',
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
        'blog' => [
            'badge'     => '最新博客文章',
            'title'     => '最新教学、机型比较与礼宾提醒',
            'lead'      => '每周推出英文与中文内容，分享安装步骤、硬件解析与合法收看资讯，掌握小云最新消息。',
            'read_more' => '阅读文章',
            'cta_label' => '浏览博客',
        ],
        'traffic' => [
            'badge' => '面向在美华人/亚洲家庭的 10P+',
            'title' => '美国现货、双语礼宾、保修齐全的 10P+',
            'lead'  => '服务在美华人/亚洲家庭：内华达仓 48 小时发货，中文/English 礼宾代客设置，硬件可跑 4K 体育、追剧、K 歌与儿童模式。',
            'bullets' => [
                'shipping'  => '48 小时美国发货，附本地电源/HDMI 与追踪号',
                'concierge' => '双语礼宾：远程设置、Wi-Fi 调优、K 歌麦克风配对、Kids Mode 指南',
                'warranty'  => '1 年美国保修 + 30 天退换货，礼宾全程协助',
            ],
            'links' => [
                'pdp'     => '购买 10P+',
                'compare' => '比较 10P+ 与 10S',
                'faq'     => '查看常见问题',
                'contact' => '联系礼宾客服',
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
                            'answer'   => '在 SVICLOUDTVBOX.US 购买的装置皆享一年美国原厂保固与 30 天内退换货。提供订单编号给礼宾客服，我们会先协助检测并安排换货或退款流程。',
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
                    ],
                ],
            ],
        ],
    ],
    'product' => [
        'hero' => [
            'subtitle' => '美国仓库配送，提供中英文专人安装服务。',
            'detail'   => '安全结账 • 美国免运 • 英/中文客服',
        ],
        'highlights' => [
            'inventory' => '美国现货与一年保固',
            'concierge' => '英/中文专属开箱服务',
            'no_fees'   => '无月费与隐藏续费',
        ],
        'traffic' => [
            'badge' => '为什么 10P+ 适合在美华人/亚洲家庭',
            'title' => '美国现货、双语礼宾、保修齐全的 10P+',
            'lead'  => '内华达仓 48 小时发货，中文/English 礼宾代客设置，硬件可跑 4K 体育、追剧、K 歌与儿童模式。',
            'bullets' => [
                'shipping'  => '48 小时美国发货，附本地电源/HDMI 与追踪号',
                'concierge' => '双语礼宾：远程设置、Wi-Fi 调优、K 歌麦克风配对、Kids Mode 指南',
                'warranty'  => '1 年美国保修 + 30 天退换货，礼宾全程协助',
            ],
            'links' => [
                'compare' => '比较 10P+ 与 10S',
                'faq'     => '查看 FAQ',
                'contact' => '联系礼宾客服',
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
                    'a' => 'SVICLOUDTVBOX.US 全站含 1 年美国硬件保修与 30 天退换货。提供订单号即可由礼宾协助排查、换货或退货。',
                ],
                'concierge' => [
                    'q' => '能用中文帮我安装吗？',
                    'a' => '可以。礼宾可用中文或英文远程带你设置，含 Kids Mode、K 歌麦克风配对与应用更新。',
                ],
            ],
        ],
    ],
    'compare' => [
        'hero' => [
            'badge'    => '机型比较',
            'title'    => '小云电视盒 10P+ 与 10S 比较',
            'subtitle' => '逐项比较硬件规格、功能与使用情境，帮你挑到最适合家庭的小云电视盒。',
        ],
        'traffic' => [
            'badge' => '美国购买更安心',
            'title' => '双语客服、48 小时出货、完整保修（两款皆适用）',
            'lead'  => '为在美华人/亚洲家庭设计：内华达仓配货，中文/English 礼宾，并清楚指引哪款适合你的房间使用。',
            'bullets' => [
                'shipping'  => '48 小时美国发货，附本地电源/HDMI 与追踪号',
                'concierge' => '双语礼宾协助安装、Wi-Fi 调优、K 歌麦克风、Kids Mode',
                'warranty'  => '1 年美国保修 + 30 天退换货，礼宾协助处理',
            ],
            'links' => [
                'p10p'    => '购买 10P+',
                'p10s'    => '购买 10S',
                'faq'     => '查看 FAQ',
                'contact' => '联系礼宾客服',
            ],
        ],
        'differences' => [
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
            '10p' => [
                'lead'    => '旗舰硬件、专属家庭功能与最快速的运算效能。',
                'bullets' => [
                    'ram_storage' => '4GB 内存、64GB 存储与 AV1 解码',
                    'apps'        => '内建儿童模式与卡拉 OK 应用',
                    'remote'      => '蓝牙 AI 语音遥控 + Wi-Fi 6',
                ],
                'cta'     => '选购 10P+',
            ],
            '10s' => [
                'lead'    => '提供 4K 影音播放的精省配置，适合重视性价比的家庭。',
                'bullets' => [
                    'ram_storage' => '2GB 内存、32GB 存储，满足日常播放',
                    'remote'      => '4K HDR + AV1 解码，搭配语音遥控',
                    'ports'       => '内建 HDMI、USB 3.0 与有线网络接口',
                ],
                'cta'     => '选购 10S',
            ],
        ],
        'comparison' => [
            'title' => '功能比较',
            'rows'  => [
                'ram_storage' => [
                    'label' => '内存 / 存储',
                    'p10p'  => '4GB / 64GB',
                    'p10s'  => '2GB / 32GB',
                ],
                'video_quality' => [
                    'label' => '影像品质',
                    'p10p'  => '4K HDR、AV1 解码',
                    'p10s'  => '4K HDR、AV1 解码',
                ],
                'voice_remote' => [
                    'label' => '语音遥控器',
                    'p10p'  => '支持',
                    'p10s'  => '支持',
                ],
                'kids_app' => [
                    'label' => '儿童应用',
                    'p10p'  => '10P+ 独享',
                    'p10s'  => '无',
                ],
                'karaoke_mode' => [
                    'label' => '卡拉 OK 模式',
                    'p10p'  => '10P+ 独享',
                    'p10s'  => '无',
                ],
                'best_for' => [
                    'label' => '最适合',
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
                            'answer'   => '主机、美规电源适配器、HDMI 线、蓝牙语音遥控器、电池、快速入门指南。',
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
                        'answer'   => '主机、美规电源、HDMI 线、蓝牙语音遥控器、电池、快速入门指南。',
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
                    'contact_support' => [
                        'question' => '怎么联系客服？',
                        'answer'   => '通过联系表单并附上订单编号、装置型号与截图：<a href="{{support_url}}">联络礼宾客服</a>。',
                    ],
                ],
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
