<?php
/**
 * SVICLOUD Lumen Theme translations (Traditional Chinese)
 */

return [
    'core' => [
        'locale' => '繁體中文',
        'cart'   => [
            'adding'             => '加入中…',
            'added'              => '已加入購物車！',
            'count_label_single' => '購物車內有 {{count}} 件商品',
            'count_label_plural' => '購物車內有 {{count}} 件商品',
            'count_label_empty'  => '購物車目前為空',
            'error'              => '加入購物車失敗，請再試一次。',
        ],
        'badges' => [
            'authorized_dealer' => '官方授權經銷商',
            'ships_from_usa'    => '美國本地出貨',
            'one_year_warranty' => '一年美國保固',
        ],
    ],

    'blog' => [
        'cta' => [
            'title'           => '比較你的選擇',
            'copy'            => '想選對小雲機型嗎？前往官方比一比或與禮賓客服聊聊，獲得專屬建議。',
            'primary_label'   => '查看 10P+ vs 10S 比較',
            'secondary_label' => '聯絡禮賓客服',
        ],
    ],

    'support' => [
        'hero' => [
            'badge'    => '支援表單',
            'title'    => '提交禮賓客服支援請求',
            'subtitle' => '留下裝置資訊與遇到的狀況，中英雙語客服最快一個工作天內回覆。',
        ],
        'form' => [
            'title'        => '告訴我們目前的情況',
            'description'  => '請填寫以下欄位，我們會為您安排最合適的客服專員協助。',
            'required_note'=> '＊ 必填欄位',
            'fields' => [
                'name' => [
                    'label'       => '聯絡人姓名＊',
                    'placeholder' => '範例：林小雲',
                ],
                'email' => [
                    'label'       => 'Email＊',
                    'placeholder' => 'you@example.com',
                ],
                'phone' => [
                    'label'       => '電話／WhatsApp',
                    'placeholder' => '702-398-3416',
                ],
                'order' => [
                    'label'       => '訂單編號',
                    'placeholder' => '例如 WC-1234',
                ],
                'device' => [
                    'label'       => '使用的機型',
                    'placeholder' => '選擇裝置',
                ],
                'issue' => [
                    'label'       => '問題類型＊',
                    'placeholder' => '選擇目前的狀況',
                ],
                'message' => [
                    'label'       => '需要協助的內容＊',
                    'placeholder' => '請描述遇到的問題、顯示的訊息或希望我們協助的步驟…',
                ],
            ],
            'device_options' => [
                '10p'   => '小雲電視盒 10P+',
                '10s'   => '小雲電視盒 10S',
                'other' => '其他／不確定',
            ],
            'issue_options' => [
                'setup'     => '安裝與啟用',
                'streaming' => '播放或緩衝問題',
                'billing'   => '訂單或付款',
                'apps'      => 'App／帳號／遙控器',
                'other'     => '其他問題',
            ],
            'submit' => '送出訊息',
        ],
        'success' => [
            'title' => '訊息已送出',
            'body'  => '感謝您！禮賓客服將於一個工作天內，從 support@svicloudtvbox.us 寄出回覆。如果沒有收到，請檢查垃圾郵件或促銷匣。',
        ],
        'error'   => '訊息寄送失敗，請稍後再試或直接來信 support@svicloudtvbox.us。',
        'help' => [
            'contact' => [
                'title' => '想立即與真人聯絡？',
                'copy'  => '透過電話、簡訊、WhatsApp 或 Email 與我們的雙語客服即時聯繫。',
                'cta'   => '前往聯絡客服頁面',
            ],
            'faq' => [
                'title' => '先查看常見問題',
                'copy'  => 'FAQ 蒐集安裝重點、遙控器配對與常見排解技巧，可先自行檢查。',
                'cta'   => '開啟 FAQ',
            ],
        ],
    ],

    'contact' => [
        'hero' => [
            'badge'        => '禮賓客服',
            'title'        => '聯繫小雲禮賓客服團隊',
            'subtitle'     => '中英文專員協助訂單、安裝、Wi-Fi 優化與 App 更新。真人客服最快一個工作天內回覆。',
            'primary_cta'  => '立即聯絡客服',
            'secondary_cta'=> '填寫支援表單',
        ],
        'hours' => [
            'title' => '禮賓客服時間',
            'copy'  => '真人客服：週一至週六 · 太平洋時間 09:00–18:00',
            'note'  => '非客服時段來信，將於下一個工作日上午回覆。',
        ],
        'channels' => [
            'title' => '聯絡方式',
            'items' => [
                'phone' => [
                    'label' => '電話／簡訊',
                    'value' => '702-398-3416',
                    'cta'   => '撥打或傳送簡訊',
                ],
                'whatsapp' => [
                    'label' => 'WhatsApp',
                    'value' => '+85363403380',
                    'cta'   => '開啟 WhatsApp',
                ],
                'email' => [
                    'label' => 'Email',
                    'value' => 'support@svicloudtvbox.us',
                    'cta'   => '寄送 Email',
                ],
            ],
        ],
        'form' => [
            'title' => '需要更深入的協助？',
            'copy'  => '請提供訂單編號、使用的機型與遇到的問題，禮賓客服將於一個工作天內回覆。',
            'cta'   => '開啟客服表單',
        ],
        'faq' => [
            'title' => '快速自助',
            'copy'  => '想先自行查詢？常見問題整理了選購、安裝與疑難排解重點。',
            'cta'   => '前往 FAQ',
        ],
    ],
    'return_policy' => [
        'hero' => [
            'badge'    => '退換貨服務',
            'title'    => '退換貨與退款政策',
            'subtitle' => '我們希望您喜愛小雲電視盒。如需退換貨，請在交貨後 30 天內依照以下步驟申請。',
        ],
        'timeline' => [
            'title' => '退貨流程時間表',
            'items' => [
                'request'  => '請在交貨日起 30 天內提出退貨申請。',
                'approval' => '我們會在 1-2 個工作天內確認申請資格。',
                'ship'     => '核准後請在 7 天內寄回商品。',
                'refund'   => '完成檢查後的 3-5 個工作天內退回款項。',
            ],
        ],
        'sections' => [
            'eligibility' => [
                'title' => '申請前請先確認資格',
                'items' => [
                    'window'    => '退貨申請需在交貨日起 30 天內提出。',
                    'condition' => '商品需保持近乎全新並附上原包裝、遙控器與線材。',
                    'activation'=> '串流服務的啟用或續費一經啟動恕不退款。',
                    'proof'     => '若為瑕疵或損壞，請提供照片或影片以加速處理。',
                ],
            ],
            'start' => [
                'title' => '如何提出退貨申請',
                'steps' => [
                    'contact' => '請以訂單號碼聯絡客服並說明情況。',
                    'label'   => '我們會以電子郵件提供寄回指示與必要時的預付運單。',
                    'pack'    => '請妥善包裝商品並附上我們提供的 RMA 表單。',
                ],
            ],
            'shipping' => [
                'title' => '運費與整新費用',
                'items' => [
                    'responsibility' => '除非商品到貨即有損壞或瑕疵，否則退貨運費需由客戶負擔。',
                    'restock'        => '缺少配件的退貨可能需支付最高 15% 的整新費。',
                    'tracking'       => '請保留運送追蹤編號，直到收到退款確認。',
                ],
            ],
            'refunds' => [
                'title' => '退款與換貨',
                'items' => [
                    'inspection' => '商品送達後我們會在兩個工作天內完成檢查。',
                    'method'     => '核准退款後將退回原支付方式；運費不予退還。',
                    'exchange'   => '若需要更換商品，請告知我們；退貨包裹被承運人掃描後即可安排寄出替換品。',
                ],
            ],
        ],
        'support' => [
            'title' => '需要退貨協助嗎？',
            'copy'  => '雙語客服團隊可在您寄回商品前協助排除故障、安排換貨或解答保固問題。',
            'hours' => '服務時間：週一至週六 · 太平洋時間 09:00-18:00',
            'cta'   => '聯絡客服',
        ],
    ],

    'legal_disclaimer' => [
        'hero' => [
            'badge'    => '法律聲明',
            'title'    => '合規與法律聲明',
            'subtitle' => 'SVICLOUDTVBOX.US 僅販售原廠硬體。安裝任何軟體或內容前，請先閱讀以下聲明並確保遵守所在地法律。',
        ],
        'sections' => [
            'hardware' => [
                'title' => '僅販售原廠硬體',
                'copy'  => '我們提供封膜的 SVICLOUD 主機、原裝配件與禮賓服務，不會預載、側載或宣傳任何串流軟體與媒體來源。',
                'items' => [
                    'line1' => '出貨時僅保留原廠啟用精靈與系統韌體。',
                    'line2' => '不會預先安裝第三方串流 App、播放清單或多媒體檔案。',
                    'line3' => '完成安裝後，客戶自行決定要新增哪些軟體與服務。',
                ],
            ],
            'responsibilities' => [
                'title' => '使用者責任',
                'copy'  => '請僅安裝您擁有合法授權的應用程式或串流服務。',
                'items' => [
                    'line1' => '確認每項服務均授權您在所在國家／地區收看。',
                    'line2' => '遵守所有著作權、通訊與進出口等相關法規。',
                    'line3' => '保存訂閱收據、授權聲明與客服往來，以備未來查驗。',
                ],
            ],
            'prohibited' => [
                'title' => '禁止的使用方式',
                'copy'  => '請勿透過小雲電視盒進行未授權的串流、重播或任何違法行為。',
                'items' => [
                    'line1' => '不要從來路不明的連結、破解平台或盜版論壇下載軟體。',
                    'line2' => '未經授權不得轉播、重製或販售任何受保護的內容。',
                    'line3' => '不得竄改序號、韌體安全機制或 DRM 系統。',
                ],
            ],
            'support' => [
                'title' => '支援範圍限制',
                'copy'  => '禮賓客服僅提供硬體安裝、網路設定與一般排解建議，無法協助安裝、設定或恢復任何未授權的服務。',
                'items' => [
                    'line1' => '涉及非官方安裝程式、代碼或播放清單的需求將婉拒處理。',
                    'line2' => '合法服務的帳號與播放問題，請直接聯繫該服務的客服。',
                    'line3' => '若判定有違規風險，我們得拒絕後續支援或訂單。',
                ],
            ],
            'updates' => [
                'title' => '政策可能調整',
                'copy'  => '關於串流設備的監管規範不斷更新，我們可能隨時修訂本聲明以符合法律要求，恕不另行通知。',
                'items' => [
                    'line1' => '建議定期回訪本頁面，掌握最新合規說明。',
                    'line2' => '若發出更新通知，繼續使用我們的產品即代表您同意最新條款。',
                ],
            ],
        ],
        'contact' => [
            'title' => '需要合規協助嗎？',
            'copy'  => '禮賓客服可提供硬體安裝建議與合法來源參考，但無法提供法律意見。歡迎與我們聯繫取得更多資訊。',
            'cta'   => '聯絡禮賓客服',
        ],
    ],
    'cart_page' => [
        'title'             => '購物車',
        'continue_shopping' => '繼續選購',
        'empty'             => '您的購物車目前是空的。',
        'empty_cta'         => '前往選購商品',
        'table'             => [
            'thumbnail' => '圖片',
            'product'   => '商品',
            'price'     => '單價',
            'quantity'  => '數量',
            'total'     => '小計',
        ],
        'meta'              => [
            'authorized' => '美國授權經銷商',
            'shipping'   => '48 小時內出貨',
            'warranty'   => '一年美國保固',
        ],
        'remove'            => '移除商品',
        'remove_aria'       => '從購物車移除 %s',
        'update'            => '更新購物車',
        'coupon'            => [
            'label'       => '優惠代碼',
            'placeholder' => '輸入優惠代碼',
            'apply'       => '套用',
            'hint'        => '優惠代碼需區分大小寫，留白也能繼續結帳。',
        ],
        'summary'           => [
            'title' => '訂單摘要',
            'intro'        => '美國內華達州倉庫快速出貨，提供雙語禮賓服務。',
            'empty'        => '加入商品後，這裡會顯示出貨方式、保固與結帳金額。',
            'reassurance'  => '支援 PayPal、Visa、Mastercard 與 American Express 安全結帳。',
        ],
        'benefits'          => [
            'shipping' => [
                'title' => '快速出貨',
                'copy'  => '每個工作天 48 小時內從內華達州倉庫寄出。',
            ],
            'warranty' => [
                'title' => '美國保固',
                'copy'  => '所有小雲電視盒皆享有一年美國保固。',
            ],
            'concierge' => [
                'title' => '雙語禮賓',
                'copy'  => '中英文專員協助安裝、更新與排解問題。',
            ],
        ],
    ],
    'checkout_page' => [
        'badge'    => '安全結帳',
        'title'    => '完成您的小雲電視盒訂單',
        'subtitle' => '內華達州倉庫 48 小時內出貨，附英文與中文禮賓服務。',
        'summary'  => [
            'title'         => '訂單摘要',
            'intro'         => '下單前請確認商品、金額與付款方式。',
            'reassurance'   => 'PayPal、Visa、Mastercard、American Express SSL 加密安全付款。',
            'order_heading' => '檢視訂單',
        ],
        'benefits' => [
            'shipping'  => '48 小時美國出貨',
            'warranty'  => '一年美國保固',
            'concierge' => '英文與中文禮賓支援',
        ],
        'assurance' => [
            'title' => '需要協助嗎？',
            'copy'  => '客服會寄送物流資訊、安裝指南與中英雙語即時支援。',
        ],
        'payment' => [
            'title' => '付款與確認',
            'intro' => '選擇已儲存的信用卡或新增付款方式，然後提交訂單完成安全結帳。',
            'cards_label' => '支援的信用卡：Visa、Mastercard、American Express、Discover。',
        ],
        'coupon' => [
            'toggle_lead' => '有折扣碼嗎？',
            'toggle_cta'  => '點此輸入',
            'toggle_aria' => '輸入折扣碼',
            'intro'       => '輸入折扣碼即可於結帳前套用優惠。',
            'label'       => '折扣碼',
            'placeholder' => '輸入折扣碼',
            'apply'       => '套用',
            'hint'        => '折扣碼需區分大小寫，沒有折扣碼也能完成結帳。',
            'applied_label' => '已套用折扣碼',
            'remove'        => '移除',
        ],
    ],

    'order_tracking' => [
        'badge'              => '訂單查詢',
        'title'              => '追蹤您的訂單',
        'subtitle'           => '請輸入購物車上的訂單編號與結帳時使用的電子信箱。',
        'order_id_label'     => '訂單編號',
        'order_id_placeholder' => '例如 1234 或 WC-1234',
        'email_label'        => '結帳電子信箱',
        'email_placeholder'  => '結帳時填寫的信箱',
        'submit'             => '查詢訂單',
        'submit_value'       => '查詢訂單',
        'help_text'          => '需要即時協助或更新貨運資訊嗎？',
        'help_link'          => '聯繫禮賓客服',
        'support_hours'      => '服務時間：太平洋時間 09:00-18:00（週一至週六）',
        'summary' => [
            'order_label'    => '訂單資訊',
            'shipping_label' => '物流狀態',
            'shipping_note'  => '追蹤更新後我們會立即以 email 通知您。',
            'details_heading' => '訂單明細',
            'details_note'    => '以下列出出貨商品、金額與帳單資訊供您確認。',
            'placed'         => '建立日期：{{order_date}}',
            'total'          => '金額 {{order_total}} · {{payment_method}}',
        ],
        'status_heading'     => '訂單狀態',
        'status'             => '訂單 {{order_number}} 建立於 {{order_date}}，目前狀態為 {{order_status}}。',
        'customer_label'     => '顧客：',
        'updates_heading'    => '最新進度',
        'no_updates'         => '目前沒有最新進度，一有更新將以 email 通知您。',
      ],

    'order_thankyou' => [
        'title'       => '感謝您的訂購 — 已完成確認',
        'email_intro' => '我們已將收據與訂單更新寄送至 {{name}}{{dot}}{{email}}',
        'summary' => [
            'order_label'     => '訂單資訊',
            'next_label'      => '後續步驟',
            'placed_on'       => '建立日期：{{order_date}}',
            'total'           => '金額 {{order_total}}{{dot}}{{payment_method}}',
            'tracking_note'   => '商品出貨後，我們會立即寄送物流追蹤資訊。',
            'details_heading' => '訂單明細',
            'details_note'    => '以下列出出貨商品、金額與帳單資訊供您確認。',
        ],
        'cta' => [
            'track'    => '查詢訂單',
            'continue' => '繼續選購',
        ],
        'failed' => [
            'title'      => '訂單失敗',
            'copy'       => '銀行或金流拒絕了此次交易，請重新嘗試付款。',
            'pay_now'    => '立即付款',
            'my_account' => '我的帳號',
        ],
    ],

    'faq' => [
        'hero' => [
            'badge'        => '客服中心',
            'title'        => '小雲（SviCloud）電視盒常見問題',
            'subtitle'     => '快速了解選購、安裝與使用小雲電視盒的常見問題與解答。',
            'cta_primary'  => '查看安裝指南',
            'cta_secondary'=> '比較 10P+ 與 10S',
        ],
        'sections' => [
            'device_models' => [
                'title' => '裝置與型號',
                'items' => [
                    'model_choice' => [
                        'question' => '要買哪一款小雲電視盒？',
                        'answer'   => '若需要語音搜尋或卡拉 OK，建議選擇 SviCloud 10P+。SviCloud 10S 擁有相同串流內容，但沒有 Karaoke KTV 與麥克風遙控。決定後可先閱讀 <a href="{{setup_guide_url}}">安裝教學</a>，並在 <a href="{{compare_url}}">10P+ 與 10S 比較頁</a>確認規格差異。',
                    ],
                    'international_use' => [
                        'question' => '小雲電視盒在海外可以用嗎？',
                        'answer'   => '可以。只要有穩定的寬頻或 Wi-Fi 網路，就能在世界各地使用。',
                    ],
                    'box_contents' => [
                        'question' => '盒內附帶哪些配件？',
                        'answer'   => '標準配件包含主機、語音遙控器、HDMI 線、電源變壓器與快速入門說明。',
                    ],
                ],
            ],
            'setup_activation' => [
                'title' => '安裝與啟動',
                'items' => [
                    'power_on' => [
                        'question' => '如何啟動小雲電視盒？',
                        'answer'   => '接上 HDMI 與電源後，裝置會自動開機，前方指示燈亮起即可確認。想看操作畫面可參考 <a href="{{setup_guide_url}}">安裝圖解教學</a>。',
                    ],
                    'change_language' => [
                        'question' => '如何切換系統語言？',
                        'answer'   => '首次開機時，用遙控器選擇語言並按 <code>OK</code>、<code>Next</code>。日後可在系統設定中隨時調整。',
                    ],
                    'remote_pairing' => [
                        'question' => '遙控器沒有反應怎麼辦？',
                        'answer'   => '換上新電池後，同時按住 <code>VOL-</code> 與 <code>VOL+</code> 重新配對，配對時請靠近機身。多數遙控問題都能透過此動作解決。',
                    ],
                ],
            ],
            'apps_content' => [
                'title' => 'App 與內容',
                'items' => [
                    'preinstalled' => [
                        'question' => '小雲專屬 App 會預先安裝嗎？',
                        'answer'   => '不會。系統僅提供基礎介面，請透過官方商店或服務網站安裝您已取得授權的串流平台。',
                    ],
                    'third_party' => [
                        'question' => '可以安裝 Netflix 或其他第三方 App 嗎？',
                        'answer'   => '可以。小雲採用 Android 系統，可安裝相容的串流服務。請務必確認來源可信且使用方式符合內容提供者的授權條款。',
                    ],
                    'family_content' => [
                        'question' => '有適合小朋友的內容嗎？',
                        'answer'   => '請選擇擁有家長監護與合法授權聲明的兒童服務，確保內容適齡且來源安全。',
                    ],
                    'adult_content' => [
                        'question' => '有成人內容嗎？',
                        'answer'   => '裝置本身不包含成人內容。您新增的服務需符合法規並完成年齡驗證，請務必遵守所在地法律。',
                    ],
                ],
            ],
            'features_limitations' => [
                'title' => '功能與限制',
                'items' => [
                    'karaoke_support' => [
                        'question' => '所有小雲機種都支援卡拉 OK 嗎？',
                        'answer'   => '僅 SviCloud 10P+ 支援 Karaoke KTV。若卡拉 OK 是必備功能，請選擇 10P+。',
                    ],
                    'voice_control' => [
                        'question' => '語音操作怎麼用？',
                        'answer'   => '先按 <code>VOL-</code> + <code>VOL+</code> 配對遙控器，再按麥克風鍵說出影片名稱。SviCloud 10S 不支援語音功能。',
                    ],
                    'subtitle_speed' => [
                        'question' => '字幕速度可以調整嗎？',
                        'answer'   => '可以，在播放設定中可調整字幕速度與語言。',
                    ],
                ],
            ],
            'troubleshooting_support' => [
                'title' => '疑難排解與客服',
                'items' => [
                    'buffering' => [
                        'question' => '畫面模糊或不斷緩衝怎麼辦？',
                        'answer'   => '請重啟路由器、將盒子靠近 Wi-Fi，或改用網路線。如果家中裝置很多，可以升級網速。<a href="{{setup_guide_url}}">安裝指南</a>也整理了更多疑難排解步驟。',
                    ],
                    'orz_installer' => [
                        'question' => '串流 App 或安裝程式打不開怎麼辦？',
                        'answer'   => '請先確認該服務為合法來源，再檢查網路是否連線，必要時直接聯絡該服務的客服。我們無法協助非官方下載連結。',
                    ],
                    'contact_support' => [
                        'question' => '如何聯繫客服？',
                        'answer'   => '可透過 <a href="{{support_url}}">svicloudtvbox.us 客服聯絡頁</a>，或使用訂單確認信中的 email 與我們聯繫。我們提供英文與繁體中文客服支援。',
                    ],
                ],
            ],
        ],
    ],

    'header' => [
        'nav' => [
            'home'     => '首頁',
            'compare'  => '比較機型',
            'compare_evpad'  => '小雲電視盒 10P+ vs EVPAD 10 Pro',
            'compare_ubox12' => '小雲電視盒 10P+ vs 安博電視盒 12 代',
            'shop'     => '選購',
            'faq'      => '常見問題',
            'ten_p'    => '小雲電視盒 10P+ 旗艦款',
            'ten_s'    => '小雲電視盒 10S 輕巧款',
            'concierge'=> '禮賓客服',
            'legal'    => '法律聲明',
            'guides'   => '使用指南',
            'about'    => '關於我們',
            'return_policy' => '退換貨政策',
            'order_tracking' => '訂單查詢',
            'account'  => '會員中心',
            'my-account' => '會員中心',
            'submenu_expand'   => '展開次選單',
            'submenu_collapse' => '收合次選單',
        ],
    ],


    'footer' => [
        'tagline'   => '美國授權經銷 · 內華達州倉儲配送',
        'summary'   => '內華達州倉庫快速配送，暢看中美熱門節目並享有原廠保固。',
        'badges'    => [
            'ships'     => '美國本地出貨',
            'warranty'  => '一年美國保固',
            'concierge' => '英文與中文客服支援',
        ],
        'benefits'  => [
            'shipping'  => [
                'label'       => '48 小時內美國出貨',
                'description' => '大多數訂單於兩個工作天內由內華達州倉庫出貨。',
            ],
            'dealer'    => [
                'label'       => '官方授權經銷商',
                'description' => '提供原廠保固與在地服務，安心選購。',
            ],
            'concierge' => [
                'label'       => '中英雙語客服',
                'description' => '提供英文與中文真人客服，協助安裝、觀影與疑難排解。',
            ],
        ],
        'copyright' => '© {{year}} SVICLOUDTVBOX.US · 保留一切權利',
    ],
    'frontpage' => [
        'hero' => [
            'badge'   => '美國授權經銷商',
            'title'   => '中文電視盒天花板，全球娛樂任一電視即插即用',
            'title_lead' => '中文電視盒天花板',
            'title_separator' => '，',
            'title_tail' => '全球娛樂任一電視即插即用',
            'copy'    => '小雲電視盒一次打包 4K 體育、亞洲戲劇、卡拉 OK 與兒童內容，美國庫房急速出貨，隨附中英雙語禮賓服務。',
            'bullets' => [
                'shipping' => '美國本地出貨',
                'warranty' => '一年美國保固',
                'fees'     => '無月租費',
            ],
            'cta' => [
                'primary' => '選購 10P+',
                'bundles' => '查看優惠組合',
                'compare' => '比較各機型',
            ],
            'card' => [
                'badge'     => '直播',
                'headline'  => '小雲電視盒 10P+ 硬體一覽',
                'timestamp' => '美國最新批次 · 2024 年 7 月',
                'stat'      => '4GB 記憶體 · 64GB 儲存',
                'specs'     => [
                    'processor'    => [
                        'label' => '處理器',
                        'value' => 'Amlogic S928X · 八核心',
                    ],
                    'connectivity' => [
                        'label' => '連線能力',
                        'value' => 'Wi-Fi 6 雙頻 + Gigabit 有線網路',
                    ],
                    'video'        => [
                        'label' => '高畫質解碼',
                        'value' => 'AV1 解碼 · 4K HDR',
                    ],
                    'extras'       => [
                        'label' => '遙控與配件',
                        'value' => '藍牙語音遙控 · USB 3.0 擴充',
                    ],
                ],
                'footer' => [
                    'shipping' => '美國出貨 · 48 小時內寄送',
                    'support'  => '一年美國保固 · 專人安裝服務',
                ],
            ],
        ],
        'certification' => [
            'badge' => '原廠授權',
            'title' => 'SVI.STUDIO 官方授權證書',
            'lead' => 'SVI.STUDIO 正式授權 168 Media Group LLC（SVICLOUDTVBOX.US）於美洲地區銷售並提供原廠保固。',
            'meta' => [
                'number' => [
                    'label' => '授權編號',
                    'value' => 'US2025092609217',
                ],
                'territory' => [
                    'label' => '授權區域',
                    'value' => '美洲（Americas）',
                ],
                'term' => [
                    'label' => '有效期間',
                    'value' => '2025 年 9 月 26 日 - 2026 年 9 月 26 日',
                ],
            ],
            'footnote' => '此證書由 SVI.STUDIO 核發，內容與原件完全一致。',
            'cta' => '查看完整證書',
            'alt' => 'SVI.STUDIO 授權 168 Media Group LLC 的官方證書',
        ],
        'metrics' => [
            'shipping' => [
                'title' => '48 小時出貨',
                'copy'  => '美國庫存快速配貨',
            ],
            'concierge' => [
                'title' => '專人安裝服務',
                'copy'  => '中英雙語開通協助',
            ],
            'security' => [
                'title' => '安全結帳',
                'copy'  => 'SSL 加密付款與保固保障',
            ],
            'dealer' => [
                'title' => '頂級評價經銷商',
                'copy'  => '自 2019 年起深受美國小雲電視盒用戶信賴',
            ],
        ],
        'feature_grid' => [
            'title'    => '為什麼小雲電視盒勝過一般串流盒',
            'subtitle' => '專為超高清 4K 體育、追劇馬拉松與卡拉 OK 夜晚打造，不卡頓。',
            'cards'    => [
                'entertainment' => [
                    'title' => '全方位娛樂',
                    'copy'  => '涵蓋亞洲與北美的 4K 直播頻道，還有雙語隨選影片、卡拉 OK 與親子專區。',
                ],
                'hardware' => [
                    'title' => '新世代硬體',
                    'copy'  => '最新 Amlogic 晶片、AV1 解碼與 Wi-Fi 6，讓直播與點播在繁忙網路下仍穩定。',
                ],
                'support' => [
                    'title' => '在地專家支援',
                    'copy'  => '美國在地小雲電視盒專家協助安裝、更新與挑選喜愛頻道。',
                ],
            ],
        ],
        'experience' => [
            'badge'      => '真人客服，立即支援。',
            'title'      => '從安裝到觀影夜晚的全程禮賓服務',
            'lead'       => '我們替您設定、排除故障並更新設備，讓您專心享受內容。所有方案皆包含小雲電視盒中英雙語專屬客服。',
            'card_title' => '我們替您處理',
            'services'   => [
                'activation' => '設備設定與韌體檢查',
                'wifi'       => 'Wi-Fi 最佳化建議',
                'karaoke'    => '卡拉 OK 歌單與麥克風配對',
                'kids'       => '兒童安全帳號與觀看時間設定',
            ],
            'cta' => '聯絡專家',
        ],
        'faq' => [
            'badge' => '常見問題',
            'title' => '美加客戶最常詢問的重點',
            'lead'  => '結帳前先了解配送時程、美國保固與繁體禮賓服務，安心挑選最適合的機型。',
            'cta'   => '查看完整客服 FAQ',
            'groups' => [
                'orders' => [
                    'title' => '訂單與配送',
                    'items' => [
                        'fulfillment' => [
                            'question' => '美國／加拿大多久可以收到貨？',
                            'answer'   => '美西時間下午 2 點前成立的訂單，48 小時內由內華達州倉庫以 USPS、UPS 或 FedEx 出貨，寄出後會自動寄送追蹤碼。寄往加拿大通常 5–7 個工作天（含清關時間）。',
                        ],
                        'warranty' => [
                            'question' => '有美國保固與退換貨服務嗎？',
                            'answer'   => '在 SVICLOUDTVBOX.US 購買的裝置皆享一年美國原廠保固與 30 天內退換貨。提供訂單編號給禮賓客服，我們會先協助檢測並安排換貨或退款流程。',
                        ],
                    ],
                ],
                'setup' => [
                    'title' => '裝置與安裝',
                    'items' => [
                        'compatibility' => [
                            'question' => '我的電視與家中網路是否相容？',
                            'answer'   => '只要具備 HDMI 介面的電視即可使用，可透過 2.4G / 5G Wi-Fi 或盒子附的 Gigabit 有線網路連線。包裝內含語音遙控器、HDMI 線與美規變壓器。',
                        ],
                        'concierge' => [
                            'question' => '可以用繁體中文協助我完成安裝嗎？',
                            'answer'   => '可以，禮賓客服提供英文／繁體中文遠端指導，陪你完成介面設定、Kids Mode、安全權限、卡拉 OK 與 App 更新等問題。',
                        ],
                    ],
                ],
            ],
        ],
        'pricing' => [
            'title'    => '選擇適合您的小雲電視盒設備',
            'subtitle' => '挑選最適合家庭的硬體。無需綁約或額外訂閱，純正小雲電視盒設備由美國直送。',
            'sr_sale_announcement' => '特價 %2$s，原價 %1$s',
            'cards'    => [
                '10p' => [
                    'badge'    => '最受歡迎',
                    'title'    => '小雲電視盒 10P+',
                    'interval' => '台',
                    'copy'     => '旗艦 4GB 記憶體 / 64GB 儲存，內建兒童與卡拉 OK 應用。',
                    'features' => [
                        'hdr'    => '4K HDR + AV1 解碼',
                        'apps'   => '內含兒童與卡拉 OK 應用',
                        'wifi'   => 'AI 語音遙控 + 雙頻 Wi-Fi',
                    ],
                    'cta'  => '查看 10P+',
                    'cta_style' => 'primary',
                    'meta' => [
                        'shipping'  => '✔ 美國出貨',
                        'warranty'  => '✔ 一年美國保固',
                        'concierge' => '✔ 中英雙語客服',
                    ],
                ],
                '10s' => [
                    'title'    => '小雲電視盒 10S',
                    'interval' => '台',
                    'copy'     => '超值 2GB 記憶體 / 32GB 儲存，適合臥室或第二台電視。',
                    'features' => [
                        'hdr'    => '4K HDR + AV1 解碼',
                        'remote' => 'AI 語音遙控',
                        'bundle' => '附 HDMI 與電源配件',
                    ],
                    'cta'  => '查看 10S',
                    'cta_style' => 'primary',
                    'meta' => [
                        'shipping' => '✔ 美國出貨',
                        'warranty' => '✔ 一年美國保固',
                        'fees'     => '✔ 無設備月費',
                    ],
                ],
            ],
        ],
        'concierge' => [
            'personalized_walkthrough' => '專屬頻道導覽與最愛設定',
            'remote_updates'           => '遠端推送韌體與應用程式更新',
            'community_access'         => '社群活動、卡拉 OK 歌單與體育賽事包',
        ],
    ],
    'shop' => [
        'hero' => [
            'badge'     => '選購',
            'title'     => '小雲電視盒',
            'subtitle'  => '美國授權經銷，提供快速出貨、一年保固與英文/中文客服。',
            'highlights' => [
                'shipping' => '美國倉庫 2-4 日快速出貨',
                'warranty' => '含一年美國保固',
                'support'  => '中英雙語專人客服',
                'fees'     => '無月費、無隱藏續費',
            ],
        ],
        'cards' => [
            'price_label'    => '一次購買',
            'price_note'     => '價格以美元計價 · 無月費',
            'best_for_label' => '適合族群',
            'assurance'      => [
                'shipping' => '美國內華達州倉庫出貨',
                'warranty' => '提供一年美國保固',
                'support'  => '附中英雙語遠端設定',
            ],
            '10p' => [
                'title'   => '小雲電視盒 10P+',
                'lead'    => '旗艦硬體配上專屬家庭功能與最快速的運算效能。',
                'button'  => '查看 10P+',
                'badge'   => '熱銷旗艦',
                'best_for' => '家庭客廳・體育迷・K 歌派對',
                'features' => [
                    'ram_storage' => '4GB 記憶體 / 64GB 儲存與 AV1 解碼',
                    'apps'        => '內建兒童模式與卡拉 OK 應用',
                    'remote'      => '藍牙 AI 語音遙控 + Wi-Fi 6',
                ],
            ],
            '10s' => [
                'title'   => '小雲電視盒 10S',
                'lead'    => '提供 4K 影音播放的精省配置，適合重視性價比的家庭。',
                'button'  => '查看 10S',
                'badge'   => '超值推薦',
                'best_for' => '臥室・客房・入門影音',
                'features' => [
                    'ram_storage' => '2GB 記憶體 / 32GB 儲存，滿足日常播放',
                    'remote'      => '4K HDR + AV1 解碼，搭配語音遙控',
                    'ports'       => '含 HDMI、USB 3.0 與有線網路端口',
                ],
            ],
        ],
    ],
    'products' => [
        'svicloud-10p-plus' => [
            'short_description' => '旗艦 4K 盒子，4GB 記憶體 / 64GB 儲存，內建兒童模式、卡拉 OK 與雙語禮賓支援。',
            'description' => '<p>小雲電視盒 10P+ 是家庭客廳的旗艦機，結合語音操控、卡拉 OK 與最快的硬體效能。</p><ul><li>4GB 記憶體與 64GB 儲存、AV1 解碼，直播體育賽事與戲劇都能流暢 4K 播放。</li><li>內建兒童模式與卡拉 OK 應用，附英文 / 中文禮賓帶領完成設定。</li><li>標配 Wi-Fi 6、Gigabit 有線網路、藍牙語音遙控、USB 3.0 與擴充埠，滿足多種安裝情境。</li><li>美國內華達州倉庫出貨，30 天安心退換、一年美國保固與真人禮賓客服。</li></ul><p>盒內附 HDMI 線、電源供應器、語音遙控，支援雙無線麥克風卡拉 OK。</p>',
        ],
        'svicloud-10s' => [
            'short_description' => '經濟型 4K 盒子，2GB 記憶體 / 32GB 儲存，附語音遙控與即插即用配件，適合臥室或客房。',
            'description' => '<p>小雲電視盒 10S 是臥室、客房或預算型家庭的理想選擇，仍保有正品小雲體驗。</p><ul><li>2GB 記憶體與 32GB 儲存支援 AV1 解碼，輕鬆播放 4K 直播與隨選內容。</li><li>附上與旗艦款相同的 AI 語音遙控與中英雙語禮賓開箱協助。</li><li>具備 HDMI、乙太網路與 USB 連接埠，可快速硬接或擴充儲存。</li><li>美國倉庫現貨、一年保固、中文客服隨時待命，安心使用無負擔。</li></ul><p>非常適合臥室、宿舍或客房，需要快速即插即用的 SVICLOUD 正版影音體驗。</p>',
        ],
    ],
    'compare' => [
        'meta' => [
            'title'       => '小雲電視盒 10P+ vs 10S 比較｜規格、功能、價格總整理',
            'description' => '比較小雲電視盒 10P+ 與 10S 的硬體規格、影音功能、適用族群與價格，快速找到最適合你家的中文電視盒。',
            'image_alt'   => '小雲電視盒 10P+ 與 10S 並排展示，旁邊放著語音遙控器',
        ],
        'hero' => [
            'badge'    => '機型比較',
            'title'    => '小雲電視盒 10P+ 與 10S 比較',
            'subtitle' => '逐項比較硬體規格、功能與使用情境，幫你挑到最適合家庭的小雲電視盒。',
        ],
        'differences' => [
            'premium_performance' => [
                'title'       => '頂級效能',
                'description' => '雙倍記憶體與儲存空間，多工處理更順暢，可安裝更多應用程式。',
            ],
            'family_entertainment' => [
                'title'       => '家庭娛樂',
                'description' => '獨家兒童模式與卡拉 OK 功能，全家同樂。',
            ],
            'smart_value' => [
                'title'       => '聰明首選',
                'description' => '維持 4K 畫質，同時擁有更親民的價格。',
            ],
        ],
        'products' => [
            '10p' => [
                'lead'    => '旗艦硬體、專屬家庭功能與最快速的運算效能。',
                'bullets' => [
                    'ram_storage' => '4GB 記憶體、64GB 儲存與 AV1 解碼',
                    'apps'        => '內建兒童模式與卡拉 OK 應用',
                    'remote'      => '藍牙 AI 語音遙控 + Wi-Fi 6',
                ],
                'cta'     => '選購 10P+',
            ],
            '10s' => [
                'lead'    => '提供 4K 影音播放的精省配置，適合重視性價比的家庭。',
                'bullets' => [
                    'ram_storage' => '2GB 記憶體、32GB 儲存，滿足日常播放',
                    'remote'      => '4K HDR + AV1 解碼，搭配語音遙控',
                    'ports'       => '內建 HDMI、USB 3.0 與有線網路接口',
                ],
                'cta'     => '選購 10S',
            ],
        ],
        'comparison' => [
            'title' => '功能比較',
            'rows'  => [
                'ram_storage' => [
                    'label' => '記憶體 / 儲存',
                    'p10p'  => '4GB / 64GB',
                    'p10s'  => '2GB / 32GB',
                ],
                'video_quality' => [
                    'label' => '影像品質',
                    'p10p'  => '4K HDR、AV1 解碼',
                    'p10s'  => '4K HDR、AV1 解碼',
                ],
                'voice_remote' => [
                    'label' => '語音遙控器',
                    'p10p'  => '支援',
                    'p10s'  => '支援',
                ],
                'kids_app' => [
                    'label' => '兒童應用',
                    'p10p'  => '10P+ 獨享',
                    'p10s'  => '無',
                ],
                'karaoke_mode' => [
                    'label' => '卡拉 OK 模式',
                    'p10p'  => '10P+ 獨享',
                    'p10s'  => '無',
                ],
                'best_for' => [
                    'label' => '最適合',
                    'p10p'  => '家庭、運動迷、4K 家庭劇院',
                    'p10s'  => '精省用戶 / 次要房間',
                ],
            ],
        ],
        'final_cta' => [
            'badge'   => '選擇您的機型',
            'title'   => '準備好升級家的觀影體驗了嗎？',
            'copy'    => '選擇最符合您家庭需求與預算的小雲電視盒機型。',
            'cta_10p' => '選購小雲電視盒 10P+',
            'cta_10s' => '選購小雲電視盒 10S',
        ],
    ],
    'about' => [
        'hero' => [
            'badge'         => '原廠授權經銷',
            'title'         => 'SVI.STUDIO 官方授權的美國團隊',
            'lead'          => '我們是 168 Media Group LLC，駐點內華達州的中文電視盒顧問團隊，提供美國本地庫存、中英雙語安裝服務與快速售後支援。',
            'cta'           => '預約禮賓客服',
            'secondary_cta' => '比較各款設備',
        ],
        'story' => [
            'title' => '品牌起源',
            'lead'  => '從客廳小聚，到成為原廠認證的美洲夥伴。',
            'body'  => '<p>2019 年，我們從幫親友汰換不穩定的山寨串流盒開始，介紹真正原廠的小雲電視盒。口碑很快擴散：卡拉 OK 派對、體育迷聚會、農曆新年直播都需要同一件事——可信賴、附保固的正版來源。</p><p>我們投入美國在地庫存、中英雙語遠端協助與快速售後，逐步成為西岸小雲電視盒使用者最熟悉的聯絡窗口。也因為這份投入，通過 SVI.STUDIO 原廠審核，取得美洲地區官方授權資格。</p>',
        ],
        'stats' => [
            'orders' => [
                'value' => '6,500+',
                'label' => '2019 年起累積出貨台數',
            ],
            'concierge_hours' => [
                'value' => '9,800+',
                'label' => '禮賓客服支援分鐘數',
            ],
            'rating' => [
                'value' => '4.9/5',
                'label' => '顧客滿意度回饋分數',
            ],
        ],
        'certification' => [
            'badge'  => '官方證明',
            'title'  => 'SVI.STUDIO 授權流程',
            'lead'   => '原廠審核我們的營運、售後服務與物流能力後，正式授權我們負責美洲地區銷售與保固。',
            'points' => [
                'vetting'   => '工廠查核序號管理、保固處理與客戶教育流程，確認符合原廠標準。',
                'inventory' => '維持美國倉庫現貨，48 小時內完成出貨與換貨。',
                'support'   => '配置中英雙語專員，協助開通、續約與更新。',
            ],
        ],
        'timeline' => [
            'badge' => '重要里程碑',
            'title' => '從興趣到官方夥伴的旅程',
            'lead'  => '一路走來的重點節點，見證我們成為 SVI.STUDIO 美國授權經銷商。',
            'items' => [
                '2019' => [
                    'year'  => '2019',
                    'title' => '社群試用起步',
                    'copy'  => '在灣區客廳舉辦小雲電視盒體驗，服務對中文節目有需求的家庭。',
                ],
                '2020' => [
                    'year'  => '2020',
                    'title' => '啟動雙語禮賓支援',
                    'copy'  => '提供中英遠端開通、韌體更新與頻道建議。',
                ],
                '2022' => [
                    'year'  => '2022',
                    'title' => '建立美國物流中心',
                    'copy'  => '內華達州倉庫正式運作，大幅縮短出貨與保固處理時間。',
                ],
                '2024' => [
                    'year'  => '2024',
                    'title' => '擴大全美社群',
                    'copy'  => '全美範圍出貨，並舉辦卡拉 OK、體育賽事與節慶直播活動。',
                ],
                '2025' => [
                    'year'  => '2025',
                    'title' => '取得原廠授權',
                    'copy'  => '通過 SVI.STUDIO 審核，取得 2025-2026 年官方授權證書。',
                ],
            ],
        ],
        'values' => [
            'badge' => '我們的承諾',
            'title' => '真品、禮賓、社群三大核心',
            'lead'  => '每位小雲電視盒用戶都能獲得原廠硬體、即時協助，以及讓全家共享的娛樂體驗。',
            'authentic' => [
                'title' => '保證真品',
                'copy'  => '庫存直接來自原廠，序號可追蹤，享有一年美國保固。',
            ],
            'concierge' => [
                'title' => '全程禮賓服務',
                'copy'  => '中英雙語安裝指導、遠端故障排除與專屬使用建議。',
            ],
            'community' => [
                'title' => '連結社群',
                'copy'  => '定期分享節目推薦、卡拉 OK 歌單與節慶活動，讓盒子成為家庭聚焦點。',
            ],
        ],
        'concierge' => [
            'badge'          => '禮賓支援',
            'title'          => '隨時待命的串流顧問',
            'lead'           => '不論開箱、調整設定還是籌備卡拉 OK 派對，我們的專員都能快速以中英雙語提供協助。',
            'points'         => [
                'setup'    => '視訊/通訊協助開機設定與個人化頻道',
                'renewals' => '主動協助韌體更新與定期保養提醒',
                'events'   => '提供節慶直播、卡拉 OK 歌單與頻道攻略',
            ],
            'cta_primary'   => '聯絡禮賓客服',
        'cta_secondary' => '瀏覽小雲電視盒設備',
        ],
    ],

    'guides' => [
        'hero' => [
            'badge'            => '安裝與支援中心',
            'title'            => '小雲電視盒指南中心',
            'lead'             => '提供中英文安裝清單與疑難排解，快速完成小雲電視盒開箱。',
            'callouts_headline' => '指南重點',
            'pill_headline'    => '分步教學，10P+ / 10S 全涵蓋',
            'pill_copy'        => '依照清單完成設定、調整主要選項，遇到問題也有中英雙語支援。',
            'callouts'         => [
                'remote'  => '配對藍牙語音遙控',
                'network' => '最佳化 Wi-Fi 或網路線連線',
                'apps'    => '了解內容合規守則',
            ],
            'primary_label'   => '開始安裝',
            'secondary_label' => '聯繫禮賓客服',
        ],
        'highlights' => [
            'badge' => '為什麼選這份指南',
            'title' => '10 分鐘完成設定，一次搞定',
            'items' => [
                'install' => [
                    'title' => '10 分鐘搞定',
                    'copy'  => '依照雙語清單完成接線與連網設定，不用猜測。',
                ],
                'models' => [
                    'title' => '支援 10P+ 與 10S',
                    'copy'  => '涵蓋語音遙控配對、KTV 功能與目前所有小雲型號的最佳做法。',
                ],
                'concierge' => [
                    'title' => '隨時有人協助',
                    'copy'  => '需要真人支援嗎？英文與中文禮賓客服可在安裝過程中即時接手。',
                ],
            ],
        ],
        'meta' => [
            'anchor_nav_badge'       => '指南索引',
            'anchor_nav_title'       => '選擇你需要的協助',
            'anchor_nav_description' => '直接跳到安裝步驟、內容合規提示、疑難排解或客服支援。',
        ],
        'nav' => [
            'overview'        => '指南亮點',
            'setup'           => '安裝流程',
            'apps'            => '內容與合規',
            'post_setup'      => '安裝後設定',
            'troubleshooting' => '疑難排解',
            'resources'       => '延伸資源',
            'support'         => '尋求支援',
        ],
        'nav_summaries' => [
            'overview'        => '快速了解重點與禮賓支援。',
            'setup'           => '完成接線、語言選擇與語音遙控配對。',
            'apps'            => '了解如何挑選合法串流服務。',
            'post_setup'      => '個人化設定，讓小雲長期穩定運作。',
            'troubleshooting' => '修復遙控、緩衝與網路等常見問題。',
            'resources'       => '收藏頻道指南、選購建議與最新 10 系列亮點。',
            'support'         => '聯絡禮賓客服或查看常見問題。',
        ],
        'setup' => [
            'badge' => '安裝清單',
            'title' => '7 個步驟完成小雲電視盒設定',
            'lead'  => '逐步完成以下流程，遙控器請隨手放在旁邊。擁有 10P+ 的用戶別忘了語音配對步驟。',
            'steps' => [
                'connect' => [
                    'title' => '連接硬體',
                    'copy'  => '接上 HDMI 與電源，確認機身前方指示燈亮起。',
                ],
                'language' => [
                    'title' => '選擇系統語言',
                    'copy'  => '使用方向鍵選擇語言，按下 OK 後再按 Next 進入下一步。',
                ],
                'disclaimer' => [
                    'title' => '同意免責聲明',
                    'copy'  => '選擇 Continue 確認並繼續。',
                ],
                'remote' => [
                    'title' => '配對語音遙控器',
                    'copy'  => '同時按住 <kbd>VOL-</kbd> 與 <kbd>VOL+</kbd> 直到螢幕顯示配對成功（僅 10P+ 需要）。',
                ],
                'time' => [
                    'title' => '設定時區與時間',
                    'copy'  => '選擇正確時區，電子節目表才會顯示準確播放時間。',
                ],
                'network' => [
                    'title' => '連上 Wi-Fi 或網路線',
                    'copy'  => '選擇 Set up WiFi 輸入密碼，或插上 Ethernet 取得更穩定的串流品質。',
                ],
                'apps' => [
                    'title' => '檢視內容合規提醒',
                    'copy'  => '安裝任何服務前，請先確認來源合法並僅透過官方管道下載，確保遵守所在地的版權與使用條款。',
                ],
            ],
            'note_title' => '即將加入完整截圖教學',
            'note_copy'  => '我們正在拍攝 HDMI 接線、語言選擇、遙控器配對與 Wi-Fi 設定畫面，完成後會於此更新。',
        ],
        'apps' => [
            'badge' => '內容合規',
            'title' => '選擇合法串流服務的提醒',
            'lead'  => '新增服務前，請先確認授權與可用地區。以下說明如何評估常見內容類別。',
            'items' => [
                'live' => [
                    'title' => '直播電視頻道',
                    'copy'  => '訂閱時請確認業者公開授權資訊、使用條款與聯繫方式，確保來源可信。',
                ],
                'kids' => [
                    'title' => '親子與兒童內容',
                    'copy'  => '選擇具備家長監護、分齡標示與合法播映權的服務，保護孩子觀影安全。',
                ],
                'karaoke' => [
                    'title' => '音樂與卡拉 OK',
                    'copy'  => '僅使用已取得詞曲授權的音樂與卡拉 OK 平台，尊重演出與版權規範。',
                ],
                'regional' => [
                    'title' => '區域性內容',
                    'copy'  => '海外觀賞時請再次確認是否具有跨國播映權，並遵守地區限制與法律。',
                ],
                'cherry' => [
                    'title' => '成人內容',
                    'copy'  => '成人節目受法律嚴格管制，僅在合法地區並符合年齡限制下才可啟用。',
                ],
            ],
        ],
        'post_setup' => [
            'badge' => '安裝完成後',
            'title' => '上線後別錯過這些設定',
            'lead'  => '設定喜愛的應用程式、調整畫面與音訊，讓小雲成為全家的娛樂中心。',
            'items' => [
                'explore' => [
                    'title' => '熟悉主畫面',
                    'copy'  => '找到直播、隨選影音與設定列，打造自己的首頁。',
                ],
                'install' => [
                    'title' => '加裝可信賴的 Android App',
                    'copy'  => '如 Netflix、Disney+、YouTube 等常用服務，記得選擇可信來源。',
                ],
                'tune' => [
                    'title' => '調整音訊與顯示',
                    'copy'  => '打開設定調整字幕、顯示模式與網路偏好。',
                ],
            ],
        ],
        'troubleshooting' => [
            'badge' => '疑難排解',
            'title' => '常見問題快速解法',
            'lead'  => '遇到問題先試試以下步驟，通常能解決開箱第一天的狀況。',
            'items' => [
                'remote' => [
                    'title' => '遙控器無法配對',
                    'copy'  => '取出電池重新裝回，靠近機身再按 <kbd>VOL-</kbd> + <kbd>VOL+</kbd>，直到看到配對成功提示。',
                ],
                'streaming' => [
                    'title' => '畫面模糊或緩衝',
                    'copy'  => '重新啟動路由器、靠近 Wi-Fi，或改用網路線提升穩定度。',
                ],
                'orz' => [
                    'title' => '串流 App 無法開啟',
                    'copy'  => '確認該服務為合法來源，檢查網路連線，並直接聯繫該平台客服。非官方安裝來源不在支援範圍內。',
                ],
            ],
        ],
        'resources' => [
            'badge' => '延伸閱讀',
            'title' => '推薦收藏的指南',
            'lead'  => '安裝完成後，這些資源提供頻道覆蓋、選購建議與 10 系列特色的完整說明。',
            'items' => [
                'why' => [
                    'title' => '為什麼要在 SVICLOUDTVBOX.US 購買',
                    'copy'  => '了解內華達州倉庫與禮賓服務如何讓你最快拿到正版 10 系列機種。',
                ],
                'channels' => [
                    'title' => '授權內容檢查清單',
                    'copy'  => '在安裝前先檢視串流服務的授權與地區限制，確保合法使用。',
                ],
                'features' => [
                    'title' => '小雲電視盒 10 系列核心特色',
                    'copy'  => '深入了解 10P+ 與 10S 的硬體規格、編碼能力與家庭娛樂體驗。',
                ],
                'which' => [
                    'title' => '我適合哪款小雲電視盒？',
                    'copy'  => '比較 10P+ 與 10S 的使用情境，第一次就選對機種。',
                ],
            ],
            'articles' => [
                'shared' => [
                    'badge'             => '延伸指南',
                    'back_label'        => '返回延伸資源',
                    'more_guides_title' => '更多延伸指南',
                ],
                'why' => [
                    'title'   => '為什麼要在 SVICLOUDTVBOX.US 購買',
                    'lead'    => '從內華達州倉庫直接出貨，保證正版小雲電視盒 10 系列機種、禮賓安裝與一年美國保固。',
                    'updated' => '更新：2025 年 1 月 15 日',
                    'sections' => [
                        'fulfillment' => [
                            'heading' => '內華達州現貨，48 小時內啟運',
                            'body'    => '<p>所有小雲電視盒 10P+ 與 10S 皆由內華達州倉庫出貨，檢查序號、封條與最新韌體後才寄出；中午 12 點前的訂單通常可在兩個工作天內出貨。</p>
<ul>
  <li>提供 UPS／USPS 需簽收的配送選項，包裹更安全。</li>
  <li>美國現貨免去海關延誤與水貨或翻新機的風險。</li>
  <li>包裝內含原廠語音遙控器、美規電源供應器與完整配件。</li>
</ul>',
                        ],
                        'warranty' => [
                            'heading' => '一年美國保固與簡單退換貨',
                            'body'    => '<p>每台機器皆附一年美國保固，由禮賓團隊協助檢測與維修；正常使用下若發生問題，會在美國本地安排零件或換機。</p>
<ul>
  <li>購買後 30 天內保留未拆封，可申請退換貨。</li>
  <li>維修或更換全程在美國處理，不需跨國寄件等待。</li>
  <li>韌體更新、功能諮詢與故障排除都由禮賓客服跟進。</li>
</ul>',
                        ],
                        'concierge' => [
                            'heading' => '中英文禮賓客服全程陪伴',
                            'body'    => '<p>需要設定語音遙控器或調整網路環境嗎？透過 <a href="{{contact_url}}">禮賓客服表單</a> 留言，我們會在一個工作天內以中英文回覆。</p>
<ul>
  <li>提供視訊截圖與逐步指引，協助完成開箱與連線。</li>
  <li>提醒僅安裝具授權的串流服務，確保合法使用。</li>
  <li>安裝完成後持續協助韌體更新與問題排解。</li>
</ul>',
                        ],
                        'billing' => [
                            'heading' => '安全結帳，價格透明',
                            'body'    => '<p>網站採用 SSL 安全結帳，支援信用卡與 PayPal，依法開立銷售稅。價格透明，結帳畫面即會顯示運費與稅額。</p>
<ul>
  <li>全美免運，沒有額外的處理費或盒裝加價。</li>
  <li>一次購買終身使用，沒有月費或自動續約。</li>
  <li>出貨後立即寄送收據與追蹤碼，方便掌握物流。</li>
</ul>',
                        ],
                        'next_steps' => [
                            'heading' => '下單後的下一步',
                            'body'    => '<p>下單後會先收到訂單確認，再寄出物流追蹤與開箱技巧。建議先閱讀 <a href="{{setup_url}}">10 分鐘安裝指南</a>，若需要協助隨時透過 <a href="{{contact_url}}">禮賓客服表單</a> 聯繫我們。</p>',
                        ],
                    ],
                ],
                'channels' => [
                    'title'   => '授權內容檢查清單',
                    'lead'    => '安裝任何串流服務前，請先確認授權、地區限制與安全性，確保合法安心地使用。',
                    'updated' => '更新：2025 年 1 月 15 日',
                    'sections' => [
                        'rights' => [
                            'heading' => '確認授權來源',
                            'body'    => '<p>僅安裝清楚揭示授權資訊的服務，若無法提供合法權利證明或聯絡方式，請勿使用。</p>
<ul>
  <li>閱讀服務條款、隱私政策與智慧財產權聲明。</li>
  <li>確認業者提供有效客服信箱或公司資料。</li>
  <li>保存訂閱或購買憑證，必要時可提供給執法單位或 ISP。</li>
</ul>',
                        ],
                        'regional' => [
                            'heading' => '檢查地區限制',
                            'body'    => '<p>許多平台僅授權特定國家播放。安裝前請確認是否允許在您所在地使用，並遵守地方法規。</p>
<ul>
  <li>瞭解是否禁止使用 VPN 或代理跨區觀看。</li>
  <li>確認自己符合年齡、居住或身份條件。</li>
  <li>若涉及進口或報關，也應先行了解相關規範。</li>
</ul>',
                        ],
                        'security' => [
                            'heading' => '維護裝置安全',
                            'body'    => '<p>避免從未知來源下載 APK。請透過官方商店或可信平台取得安裝檔，降低惡意軟體風險。</p>
<ul>
  <li>下載前使用防毒軟體掃描安裝檔案。</li>
  <li>啟用強密碼與雙重驗證保護帳號。</li>
  <li>留意平台是否提供定期安全更新與公告。</li>
</ul>',
                        ],
                        'documentation' => [
                            'heading' => '保留合規紀錄',
                            'body'    => '<p>建議將交易記錄、客服往來與授權證明整理存檔，必要時可快速提供證明。</p>
<ul>
  <li>截圖訂閱頁面、收據與授權條款。</li>
  <li>整理客服往來紀錄，方便後續追蹤。</li>
  <li>如有多人共用，請同步相關規範與文件。</li>
</ul>',
                        ],
                        'network' => [
                            'heading' => '確保播放品質',
                            'body'    => '<p>建議使用 Wi-Fi 6 或有線網路提升穩定度。若遇到緩衝，可依 <a href="{{troubleshooting_url}}">疑難排解指南</a> 調整設備或重新啟動路由器。</p>',
                        ],
                    ],
                ],
                'features' => [
                    'title'   => '小雲電視盒 10 系列核心特色',
                    'lead'    => '一次掌握小雲電視盒 10P+ 與 10S 的硬體規格、串流技術與家庭娛樂優勢。',
                    'updated' => '更新：2025 年 1 月 15 日',
                    'sections' => [
                        'hardware' => [
                            'heading' => '旗艦硬體，4K HDR 無痛播放',
                            'body'    => '<p>10P+ 採用 Amlogic S928X 八核心處理器，搭配 4GB RAM 與 64GB 儲存；10S 則配置四核心處理器、4GB RAM 與 32GB 儲存。兩款皆支援 4K HDR、HDMI 2.1、雙頻 Wi-Fi 6 與 Gigabit 有線網路。</p>
<ul>
  <li>USB 3.0／USB 2.0 可外接硬碟、麥克風或周邊配件。</li>
  <li>HDMI eARC 與光纖輸出提供家庭劇院等級的音訊選擇。</li>
  <li>低噪音散熱設計，長時間追劇也能維持穩定。</li>
</ul>',
                        ],
                        'streaming' => [
                            'heading' => 'AV1 編碼與智慧緩衝',
                            'body'    => '<p>兩款機型皆支援 AV1、HEVC、VP9 等高效率編碼，讓畫質更清晰、網路用量更低；自動緩衝與幀率匹配，確保體育與戲劇播放流暢。</p>
<ul>
  <li>根據節目自動切換 24p／30p／60p，減少抖動。</li>
  <li>全球 CDN 加速路徑優化北美連線，海外內容一樣順暢。</li>
  <li>定期韌體更新加入新功能與效能調校。</li>
</ul>',
                        ],
                        'remote' => [
                            'heading' => '語音遙控，一鍵找到節目',
                            'body'    => '<p>藍牙語音遙控支援語音搜尋、數字快捷鍵與媒體控制，只要長按音量鍵即可重新配對，搜尋頻道或 App 更快速。</p>
<ul>
  <li>可學習電視與音響的電源、音量，一支遙控搞定。</li>
  <li>按鍵具背光設計，晚上追劇也看得清楚。</li>
  <li>遙控器韌體可 OTA 更新，維持靈敏度。</li>
</ul>',
                        ],
                        'family' => [
                            'heading' => '全家娛樂一次滿足',
                            'body'    => '<p>小雲電視盒不只有直播，也支援兒童分齡模式、居家卡拉 OK 與密碼鎖定的成人專區，可依需求調整內容權限。</p>
<ul>
  <li>建立兒童模式，僅顯示教育與卡通內容。</li>
  <li>卡拉 OK 功能支援排歌、合唱與歌詞顯示。</li>
  <li>可依需求擴充各地語系的合法串流服務。</li>
</ul>',
                        ],
                        'service' => [
                            'heading' => '終身禮賓服務',
                            'body'    => '<p>每台機器都享有終身禮賓服務。需要安裝教學、韌體公告或節目推薦，可隨時透過 <a href="{{contact_url}}">禮賓客服表單</a> 聯絡我們。</p>',
                        ],
                    ],
                ],
                'models' => [
                    'title'   => '我適合哪款小雲電視盒？',
                    'lead'    => '依照收視習慣、周邊需求與預算，挑選最適合的小雲電視盒 10P+ 或 10S。',
                    'updated' => '更新：2025 年 1 月 15 日',
                    'sections' => [
                        'summary' => [
                            'heading' => '差異一目了然',
                            'body'    => '<table class="guides-article__table">
  <thead>
    <tr>
      <th>項目</th>
      <th>小雲電視盒 10P+</th>
      <th>小雲電視盒 10S</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>處理器與記憶體</td>
      <td>Amlogic S928X · 4GB RAM · 64GB 儲存</td>
      <td>四核心 CPU · 4GB RAM · 32GB 儲存</td>
    </tr>
    <tr>
      <td>連線能力</td>
      <td>Wi-Fi 6 雙頻 · Gigabit LAN · USB 3.0 + USB 2.0</td>
      <td>Wi-Fi 6 雙頻 · Gigabit LAN · 雙 USB 2.0</td>
    </tr>
    <tr>
      <td>音訊輸出</td>
      <td>HDMI 2.1 · 光纖 SPDIF</td>
      <td>HDMI 2.1 · 3.5mm AUX</td>
    </tr>
    <tr>
      <td>最佳使用情境</td>
      <td>家庭劇院、多 App 安裝、KTV 加強</td>
      <td>日常串流、小空間、副電視</td>
    </tr>
  </tbody>
</table>
<p>兩款機型皆支援相同韌體功能，差別在於儲存空間、連接介面與周邊擴充性。</p>',
                        ],
                        'ten_p' => [
                            'heading' => '適合選擇 10P+ 的情境',
                            'body'    => '<p>以下情境建議選擇 10P+：</p>
<ul>
  <li>需要連接擴大機、Soundbar 或光纖音響的家庭劇院。</li>
  <li>需要外接大容量儲存裝置或管理大型媒體資料庫。</li>
  <li>常使用 Karaoke、Party 模式，想要最強的處理效能。</li>
</ul>',
                        ],
                        'ten_s' => [
                            'heading' => '適合選擇 10S 的情境',
                            'body'    => '<p>以下情境適合 10S：</p>
<ul>
  <li>臥室、租屋或小坪數空間，需要精巧機身。</li>
  <li>以直播與隨選內容為主，只需安裝少量額外 App。</li>
  <li>想要禮賓服務與最新韌體，但預算更精簡。</li>
</ul>',
                        ],
                        'next_steps' => [
                            'heading' => '選好機型後別忘了',
                            'body'    => '<p>確定機型後，記得先完成 <a href="{{setup_url}}">安裝指南</a>、閱讀 <a href="{{legal_url}}">法律聲明</a>，並收藏 <a href="{{contact_url}}">禮賓客服表單</a> 以便日後提問。</p>',
                        ],
                    ],
                ],
            ],
        ],
        'detail' => [
            'back_to_hub'  => '返回指南首頁',
            'more_guides'  => '瀏覽其他指南',
            'open_section' => '開啟指南',
        ],
        'support' => [
            'badge' => '需要協助嗎？',
            'title' => '中英文禮賓客服隨時待命',
            'copy'  => '提供訂單編號或安裝問題，我們會在一個工作天內以英文或中文回覆您。',
            'primary_label'   => '聯繫禮賓客服',
            'secondary_label' => '查看常見問題',
        ],
    ],

    'product' => [
        'hero' => [
            'subtitle' => '美國倉庫出貨，提供中英文專人安裝服務。',
            'detail'   => '安全結帳 • 美國免運 • 英/中文客服',
        ],
        'highlights' => [
            'inventory' => '美國現貨與一年保固',
            'concierge' => '英/中文專屬開箱服務',
            'no_fees'   => '無月費與隱藏續費',
        ],
    ],
];
