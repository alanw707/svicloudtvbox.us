/**
 * SVICLOUD TV Box Theme JavaScript
 * Enhanced functionality for neon-tech theme
 */

(function($) {
    'use strict';

    // DOM Ready
    $(document).ready(function() {
        initTheme();
    });

    /**
     * Initialize theme functionality
     */
    function initTheme() {
        initLanguageSwitcher();
        initSmoothScrolling();
        initProductImageEnhancements();
        initAnimationOnScroll();
        initPerformanceOptimizations();
        initLumenNavigation();
        initProductHeroGallery();
    }

    function getStickyScrollOffset() {
        let offset = 0;
        const header = document.querySelector('[data-lumen-header]');
        if (header) {
            offset += header.getBoundingClientRect().height || header.offsetHeight || 0;
        }
        return offset + 24;
    }

    /**
     * Language switcher functionality
     */
    function initLanguageSwitcher() {
        $('.language-toggle .lang-link').on('click', function(e) {
            e.preventDefault();

            const lang = $(this).data('lang');
            const currentUrl = window.location.href;

            // Remove active class from all links
            $('.lang-link').removeClass('active');

            // Add active class to clicked link
            $(this).addClass('active');

            // Handle language switching logic
            if (lang === 'zh') {
                // Add Chinese language support
                $('body').addClass('lang-zh');

                // If TranslatePress is available, trigger it
                if (typeof window.trp_translate_uri !== 'undefined') {
                    window.location.href = window.trp_translate_uri(currentUrl, 'zh');
                    return;
                }

                // Fallback: redirect to /zh/ path
                if (!currentUrl.includes('/zh/')) {
                    const newUrl = currentUrl.replace(window.location.origin, window.location.origin + '/zh');
                    window.location.href = newUrl;
                }
            } else {
                // Switch to English
                $('body').removeClass('lang-zh');

                if (currentUrl.includes('/zh/')) {
                    const newUrl = currentUrl.replace('/zh/', '/');
                    window.location.href = newUrl;
                }
            }
        });
    }

    /**
     * Smooth scrolling for anchor links
     */

    function initSmoothScrolling() {
        $('a[href*="#"]:not([href="#"])').on('click', function(e) {
            const target = $(this.hash);

            if (target.length) {
                e.preventDefault();
                const offset = getStickyScrollOffset();

                $('html, body').animate({
                    scrollTop: target.offset().top - offset
                }, 600, 'easeInOutCubic');
            }
        });
    }


    /**
     * Product image enhancements
     */
    function initProductImageEnhancements() {
        // Lazy loading for product images
        $('.product-image img').each(function() {
            const img = $(this);

            // Add loading class
            img.addClass('loading');

            // Remove loading class when image loads
            img.on('load', function() {
                $(this).removeClass('loading').addClass('loaded');
            });
        });

        // Hover effects for product cards
        $('.product-card').on('mouseenter', function() {
            $(this).addClass('hovered');
        }).on('mouseleave', function() {
            $(this).removeClass('hovered');
        });
    }

    /**
     * Animation on scroll
     */
    function initAnimationOnScroll() {
        // Create intersection observer for fade-in animations
        if ('IntersectionObserver' in window) {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Observe elements for animation
            $('.trust-badge, .product-card, .feature-card').each(function() {
                $(this).addClass('animate-on-scroll');
                observer.observe(this);
            });
        }
    }

    /**
     * Performance optimizations
     */
    function initPerformanceOptimizations() {
        // Preload critical images
        const themeBaseUrl = (() => {
            if (window.svicTheme && window.svicTheme.themeUrl) {
                return window.svicTheme.themeUrl.replace(/\/$/, '');
            }
            const scriptEl = document.getElementById('svicloudtvbox-script-js') || document.currentScript;
            if (scriptEl && scriptEl.src) {
                return scriptEl.src.replace(/\/assets\/js\/theme\.js(?:\?.*)?$/, '');
            }
            return '';
        })();

        const assetFromTheme = (relativePath) => {
            const trimmed = relativePath.replace(/^\/+/, '');
            if (themeBaseUrl) {
                return themeBaseUrl + '/' + trimmed;
            }
            return '/wp-content/themes/svicloudtvbox/' + trimmed;
        };

        const criticalImages = [
            assetFromTheme('assets/images/svicloud-hero-product.png'),
            assetFromTheme('assets/images/svicloud-10p-plus.png'),
            assetFromTheme('assets/images/svicloud-10s.png')
        ];

        criticalImages.forEach((src) => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.href = src;
            document.head.appendChild(link);
        });

        const $header = $('[data-lumen-header]');
        let ticking = false;

        function updateScrollElements() {
            const scrollY = window.scrollY;
            if ($header.length) {
                const shouldCompact = scrollY > 80;
                $header.toggleClass('lumen-header--scrolled', shouldCompact);
                $header.toggleClass('lumen-header--transparent', !shouldCompact);
            }
            ticking = false;
        }

        updateScrollElements();

        $(window).on('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(updateScrollElements);
                ticking = true;
            }
        });
    }

    function initProductHeroGallery() {
        const $stageImage = $('.product-hero-image');
        if (!$stageImage.length) return;

        $(document).on('click', '.product-thumb', function(e) {
            e.preventDefault();
            const $button = $(this);
            const source = $button.data('image');
            const srcset = $button.data('srcset');
            if (!source) return;

            $('.product-thumb').removeClass('active').attr('aria-pressed', 'false');
            $button.addClass('active').attr('aria-pressed', 'true');

            $stageImage.stop(true).fadeTo(150, 0.1, function() {
                $stageImage.attr('src', source);
                if (srcset) {
                    $stageImage.attr('srcset', srcset);
                } else {
                    $stageImage.removeAttr('srcset');
                }
                $stageImage.fadeTo(200, 1);
            });
        });
    }

    /**
     * Lumen navigation (desktop/mobile)
     */
    function initLumenNavigation() {
        const $toggle = $('[data-lumen-toggle]');
        const $mobileNav = $('#lumen-mobile-nav');
        const $header = $('[data-lumen-header]');
        if (!$toggle.length || !$mobileNav.length) {
            return;
        }

        const bodyClass = 'lumen-nav-open';
        const submenuExpandLabel = $mobileNav.data('submenu-expand') || 'Expand submenu';
        const submenuCollapseLabel = $mobileNav.data('submenu-collapse') || 'Collapse submenu';

        const updateSubmenuToggle = ($toggle, expanded) => {
            $toggle.attr('aria-expanded', expanded ? 'true' : 'false');
            const $srText = $toggle.find('.screen-reader-text');
            const label = expanded ? submenuCollapseLabel : submenuExpandLabel;
            if ($srText.length) {
                $srText.text(label);
            } else {
                $toggle.attr('aria-label', label);
            }
        };

        const closeAllSubmenus = () => {
            $mobileNav.find('.menu-item-has-children').removeClass('is-open');
            $mobileNav.find('.lumen-mobile-nav__submenu').attr('hidden', 'hidden');
            $mobileNav.find('.lumen-mobile-nav__submenu-toggle').each(function() {
                updateSubmenuToggle($(this), false);
            });
        };

        const enhanceMobileSubmenus = () => {
            $mobileNav.find('.menu-item-has-children').each(function() {
                const $item = $(this);
                if ($item.data('submenuEnhanced')) {
                    return;
                }

                const $submenu = $item.children('.sub-menu');
                if (!$submenu.length) {
                    return;
                }

                $item.data('submenuEnhanced', true);
                $submenu.addClass('lumen-mobile-nav__submenu').attr('hidden', 'hidden');

                const $toggle = $('<button type="button" class="lumen-mobile-nav__submenu-toggle" aria-expanded="false"><span class="screen-reader-text"></span></button>');
                updateSubmenuToggle($toggle, false);
                $item.children('a').after($toggle);

                $toggle.on('click', function(event) {
                    event.preventDefault();
                    const isOpen = $item.hasClass('is-open');
                    if (isOpen) {
                        $item.removeClass('is-open');
                        $submenu.attr('hidden', 'hidden');
                    } else {
                        $item.addClass('is-open');
                        $submenu.removeAttr('hidden');
                    }
                    updateSubmenuToggle($toggle, !isOpen);
                });
            });
        };

        enhanceMobileSubmenus();

        function setNavState(open) {
            $toggle.attr('aria-expanded', open ? 'true' : 'false');
            if (open) {
                $mobileNav.addClass('is-open').removeAttr('hidden');
                enhanceMobileSubmenus();
            } else {
                $mobileNav.removeClass('is-open').attr('hidden', 'hidden');
                closeAllSubmenus();
            }
            $('body').toggleClass(bodyClass, open);
        }

        $toggle.on('click', function() {
            const isOpen = $(this).attr('aria-expanded') === 'true';
            setNavState(!isOpen);
        });

        $(document).on('click', function(event) {
            if (!$(event.target).closest('#lumen-mobile-nav, [data-lumen-toggle]').length) {
                setNavState(false);
            }
        });

        $(document).on('keydown', function(event) {
            if (event.key === 'Escape') {
                setNavState(false);
            }
        });

        $mobileNav.find('a').on('click', function() {
            setNavState(false);
        });

        if ($header.length) {
            $header.addClass('lumen-header--transparent');
        }
    }

    /**
     * Product grid layout toggle
     */
    function initProductGridToggle() {
        const grid = $('.product-grid');
        if (!grid.length) return;

        $('.grid-toggle .grid-btn').on('click', function() {
            const $button = $(this);
            const mode = $button.data('mode');

            $('.grid-toggle .grid-btn').removeClass('active').attr('aria-pressed', 'false');
            $button.addClass('active').attr('aria-pressed', 'true');

            if (mode === 'list') grid.removeClass('grid-2 grid-4').addClass('grid-1');
            if (mode === '2col') grid.removeClass('grid-1 grid-4').addClass('grid-2');
            if (mode === '4col') grid.removeClass('grid-1 grid-2').addClass('grid-4');
        });
    }

    // Initialize grid toggle on DOM ready
    $(document).ready(initProductGridToggle);

    /**
     * Product card carousel (per card, no external libs)
     */
    function initProductCardCarousel() {
        $('.pcard-carousel').each(function(){
            const $wrap = $(this);
            const $slides = $wrap.find('.pcard-slide');
            const $dots = $wrap.find('.pcard-dot');
            let idx = 0;

            function show(i){
                idx = i;
                $slides.removeClass('active').eq(idx).addClass('active');
                $dots.removeClass('active').eq(idx).addClass('active');
            }

            $wrap.find('.pcard-prev').on('click', function(e){
                e.preventDefault();
                const next = (idx - 1 + $slides.length) % $slides.length;
                show(next);
            });
            $wrap.find('.pcard-next').on('click', function(e){
                e.preventDefault();
                const next = (idx + 1) % $slides.length;
                show(next);
            });
            $dots.on('click', function(){
                const i = $(this).data('i');
                show(i);
            });

            // Init
            show(0);
        });
    }

    $(document).ready(initProductCardCarousel);

    /**
     * Enhanced WooCommerce functionality
     */
    function initWooCommerceEnhancements() {
        if (!(window.svicTheme && svicTheme.isWoo)) return;

        const $body = $(document.body);
        const defaultLabel = (svicTheme.i18n && svicTheme.i18n.addingToCart) ? svicTheme.i18n.addingToCart : 'Adding…';
        const raf = window.requestAnimationFrame ? window.requestAnimationFrame.bind(window) : function(callback) {
            return window.setTimeout(callback, 16);
        };
        let cartFeedbackTimer = null;
        let $cartFeedback = null;
        let hasShownInitialNotice = false;

        function getLoadingButtons() {
            return $('.add_to_cart_button.is-loading, .single_add_to_cart_button.is-loading');
        }

        function markPendingToast($button) {
            if (!$button || !$button.length || !$button.hasClass('single_add_to_cart_button')) {
                return;
            }
            try {
                const payload = { ts: Date.now() };
                sessionStorage.setItem('svicCartToastPending', JSON.stringify(payload));
            } catch (error) {
                // Ignore storage errors (private mode, etc.).
            }
        }

        function consumePendingToast() {
            let raw = null;
            try {
                raw = sessionStorage.getItem('svicCartToastPending');
                if (raw) {
                    sessionStorage.removeItem('svicCartToastPending');
                }
            } catch (error) {
                return null;
            }
            if (!raw) {
                return null;
            }
            try {
                const payload = JSON.parse(raw);
                if (!payload || !payload.ts) {
                    return null;
                }
                if (Date.now() - payload.ts > 600000) {
                    return null;
                }
                return payload;
            } catch (error) {
                return null;
            }
        }

        function setButtonLoading($button) {
            if (!$button || !$button.length) {
                return;
            }
            $button.each(function() {
                const $btn = $(this);
                if ($btn.hasClass('is-loading')) {
                    return;
                }
                $btn.data('original-html', $btn.html());
                $btn.addClass('is-loading').attr('aria-busy', 'true').prop('disabled', true);
                $btn.html('<span class="loading-spinner" aria-hidden="true"></span><span class="loading-text">' + defaultLabel + '</span>');
                const timeoutId = window.setTimeout(function() {
                    clearButtonLoading($btn);
                }, 4000);
                $btn.data('loading-timeout', timeoutId);
                markPendingToast($btn);
            });
        }

        function clearButtonLoading($button) {
            const $targets = ($button && $button.length) ? $button : getLoadingButtons();
            if (!$targets.length) {
                return;
            }
            $targets.each(function() {
                const $btn = $(this);
                const timeoutId = $btn.data('loading-timeout');
                if (timeoutId) {
                    window.clearTimeout(timeoutId);
                }
                $btn.removeData('loading-timeout');
                const original = $btn.data('original-html');
                if (typeof original !== 'undefined') {
                    $btn.html(original);
                    $btn.removeData('original-html');
                }
                $btn.removeClass('is-loading').attr('aria-busy', 'false').prop('disabled', false);
            });
        }

        function ensureCartFeedback() {
            if ($cartFeedback && $cartFeedback.length) {
                return $cartFeedback;
            }
            $cartFeedback = $('<div class="svic-cart-feedback" role="status" aria-live="polite"></div>');
            $('body').append($cartFeedback);
            return $cartFeedback;
        }

        function stripMessage(raw) {
            if (typeof raw !== 'string') {
                return '';
            }
            return $('<div/>').html(raw).text().trim();
        }

        function resolveAddedMessage(fragments) {
            if (fragments && typeof fragments === 'object') {
                const noticeKeys = ['div.woocommerce-notices-wrapper', '.woocommerce-notices-wrapper', '.woocommerce-message'];
                for (let i = 0; i < noticeKeys.length; i += 1) {
                    const key = noticeKeys[i];
                    if (Object.prototype.hasOwnProperty.call(fragments, key)) {
                        const html = fragments[key];
                        if (typeof html === 'string' && html.trim()) {
                            const cleaned = stripMessage(html);
                            if (cleaned) {
                                return cleaned;
                            }
                        }
                    }
                }
            }
            if (window.svicTheme && svicTheme.i18n && svicTheme.i18n.addedToCart) {
                const translated = stripMessage(svicTheme.i18n.addedToCart);
                if (translated) {
                    return translated;
                }
            }
            return 'Added to cart';
        }

        function fallbackErrorMessage() {
            if (window.svicTheme && svicTheme.i18n && svicTheme.i18n.cartError) {
                const translated = stripMessage(svicTheme.i18n.cartError);
                if (translated) {
                    return translated;
                }
            }
            return 'Unable to add to cart. Please try again.';
        }

        function displayInitialNotices() {
            if (hasShownInitialNotice) {
                return;
            }
            const $wrapper = $('.woocommerce-notices-wrapper');
            const $notice = $wrapper.find('.woocommerce-error, .woocommerce-message, .woocommerce-info').filter(function() {
                return $(this).text().trim().length > 0;
            }).first();
            const pending = consumePendingToast();
            if (!$notice.length && !pending) {
                return;
            }
            let message = '';
            let variant = 'success';
            if ($notice.length) {
                message = stripMessage($notice.html());
                variant = $notice.hasClass('woocommerce-error') ? 'error' : 'success';
            } else if (pending) {
                message = (svicTheme.i18n && svicTheme.i18n.addedToCart) ? svicTheme.i18n.addedToCart : 'Added to cart!';
            }
            if (!message) {
                return;
            }
            hasShownInitialNotice = true;
            window.setTimeout(function() {
                showCartFeedback(message, variant);
            }, 120);
        }

        function showCartFeedback(message, variant = 'success') {
            const cleaned = stripMessage(message);
            if (!cleaned) {
                return;
            }
            const $toast = ensureCartFeedback();
            $toast.removeClass('is-visible is-error');
            if (variant === 'error') {
                $toast.addClass('is-error');
            }
            $toast.text(cleaned);
            window.clearTimeout(cartFeedbackTimer);
            cartFeedbackTimer = window.setTimeout(function() {
                $toast.removeClass('is-visible');
            }, 4200);
            raf(function() {
                $toast.addClass('is-visible');
            });
        }

        function flashCartCount() {
            raf(function() {
                const $count = $('[data-cart-count]').first();
                if (!$count.length || $count.hasClass('is-empty')) {
                    return;
                }
                const existing = $count.data('svicCartPulse');
                if (existing) {
                    window.clearTimeout(existing);
                }
                $count.addClass('is-updated');
                const timeoutId = window.setTimeout(function() {
                    $count.removeClass('is-updated');
                    $count.removeData('svicCartPulse');
                }, 800);
                $count.data('svicCartPulse', timeoutId);
            });
        }

        function isWooAddToCartRequest(settings) {
            if (!settings || !settings.url) {
                return false;
            }
            if (settings.url.indexOf('wc-ajax=add_to_cart') !== -1) {
                return true;
            }
            if (settings.url.indexOf('admin-ajax.php') !== -1 && typeof settings.data === 'string' && settings.data.indexOf('wc-ajax=add_to_cart') !== -1) {
                return true;
            }
            return false;
        }

        $(document).on('click', '.add_to_cart_button', function() {
            const $btn = $(this);
            const schedule = typeof requestAnimationFrame === 'function' ? requestAnimationFrame : window.setTimeout;
            schedule(function() {
                setButtonLoading($btn);
            });
        });

        $(document).on('submit', 'form.cart', function() {
            const $form = $(this);
            const $btn = $form.find('.single_add_to_cart_button');
            if (!$btn.length) {
                return;
            }
            window.setTimeout(function() {
                setButtonLoading($btn);
            }, 0);
        });

        $body.on('added_to_cart', function(event, fragments, cart_hash, $button) {
            const $target = ($button && $button.length) ? $button : getLoadingButtons();
            clearButtonLoading($target);
            consumePendingToast();
            flashCartCount();
            showCartFeedback(resolveAddedMessage(fragments));
        });

        $body.on('wc_cart_button_updated', function(event, $button) {
            clearButtonLoading($button);
        });

        $body.on('removed_from_cart', function() {
            clearButtonLoading();
            flashCartCount();
        });

        $(document).on('ajaxError', function(event, jqxhr, settings) {
            if (!isWooAddToCartRequest(settings)) {
                return;
            }
            clearButtonLoading();
            consumePendingToast();
            const response = jqxhr && jqxhr.responseJSON ? jqxhr.responseJSON : null;
            let message = '';
            if (response) {
                if (typeof response.error === 'string') {
                    message = response.error;
                } else if (response.data) {
                    if (typeof response.data === 'string') {
                        message = response.data;
                    } else if (Array.isArray(response.data) && response.data.length) {
                        const notice = response.data[0];
                        if (notice && typeof notice.notice === 'string') {
                            message = notice.notice;
                        }
                    }
                }
            }
            showCartFeedback(message || fallbackErrorMessage(), 'error');
        });

        $('.quantity input[type="number"]').on('change', function() {
            const qty = parseInt($(this).val(), 10);
            const price = parseFloat($('.price .amount').first().text().replace(/[^0-9.]/g, ''));

            if (!Number.isNaN(qty) && !Number.isNaN(price)) {
                $('.calculated-total').text('$' + (qty * price).toFixed(2));
            }
        });

        displayInitialNotices();
        window.setTimeout(displayInitialNotices, 250);

    }

    // Initialize WooCommerce enhancements when DOM is ready
    $(document).ready(function() {
        const $body = $('body');
        if ($body.hasClass('woocommerce') || $body.hasClass('woocommerce-page') || $body.hasClass('single-product')) {
            initWooCommerceEnhancements();
        }
    });

    /**
     * Mobile menu enhancements
     */
    function initMobileMenu() {
        // Enhanced mobile navigation
        $('.wp-block-navigation__responsive-container-open').on('click', function() {
            $('body').addClass('mobile-menu-open');
        });

        $('.wp-block-navigation__responsive-container-close').on('click', function() {
            $('body').removeClass('mobile-menu-open');
        });

        // Close mobile menu on outside click
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.wp-block-navigation').length) {
                $('body').removeClass('mobile-menu-open');
            }
        });
    }

    // Initialize mobile menu
    $(document).ready(initMobileMenu);

    /**
     * Contact form enhancements
     */
    function initContactEnhancements() {
        // Add click tracking for contact buttons
        $('.contact-btn').on('click', function() {
            const type = $(this).hasClass('whatsapp-btn') ? 'WhatsApp' : 'Phone';

            // Track contact interaction (if analytics available)
            if (typeof gtag !== 'undefined') {
                gtag('event', 'contact_click', {
                    'method': type,
                    'event_category': 'engagement'
                });
            }
        });
    }

    // Initialize contact enhancements
    $(document).ready(initContactEnhancements);

})(jQuery);

/**
 * CSS Custom Properties for animations
 */
document.documentElement.style.setProperty('--animation-duration', '0.6s');
document.documentElement.style.setProperty('--animation-easing', 'cubic-bezier(0.4, 0, 0.2, 1)');

/**
 * Add loading states for better UX
 */
window.addEventListener('load', function() {
    document.body.classList.add('loaded');

    // Trigger any post-load animations
    setTimeout(function() {
        document.body.classList.add('animations-ready');
    }, 100);
});
