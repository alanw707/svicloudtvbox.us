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

                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 800, 'easeInOutCubic');
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
        const criticalImages = [
            '/wp-content/themes/svicloudtvbox-lumen/assets/images/svicloud-hero-product.png',
            '/wp-content/themes/svicloudtvbox-lumen/assets/images/svicloud-10p-plus.png',
            '/wp-content/themes/svicloudtvbox-lumen/assets/images/svicloud-10s.png'
        ];

        criticalImages.forEach(function(src) {
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

        function setNavState(open) {
            $toggle.attr('aria-expanded', open ? 'true' : 'false');
            if (open) {
                $mobileNav.addClass('is-open').removeAttr('hidden');
            } else {
                $mobileNav.removeClass('is-open').attr('hidden', 'hidden');
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

        function setButtonLoading($button) {
            if (!$button || !$button.length || $button.hasClass('is-loading')) return;
            $button.data('original-html', $button.html());
            $button.addClass('is-loading').attr('aria-busy', 'true').prop('disabled', true);
            $button.html('<span class="loading-spinner" aria-hidden="true"></span><span class="loading-text">' + defaultLabel + '</span>');
            const timeoutId = window.setTimeout(function() {
                clearButtonLoading($button);
            }, 3500);
            $button.data('loading-timeout', timeoutId);
        }

        function clearButtonLoading($button) {
            if (!$button || !$button.length) return;
            const timeoutId = $button.data('loading-timeout');
            if (timeoutId) {
                window.clearTimeout(timeoutId);
            }
            $button.removeData('loading-timeout');

            const original = $button.data('original-html');
            if (original === undefined) return;
            $button.html(original);
            $button.removeClass('is-loading').attr('aria-busy', 'false').prop('disabled', false);
            $button.removeData('original-html');
        }

        $(document).on('click', '.single_add_to_cart_button, .add_to_cart_button', function() {
            setButtonLoading($(this));
        });

        $body.on('added_to_cart wc_cart_button_updated', function(event, arg1) {
            const $button = arg1 && arg1.jquery ? arg1 : $('.add_to_cart_button.is-loading, .single_add_to_cart_button.is-loading');
            clearButtonLoading($button);
        });

        $body.on('removed_from_cart', function() {
            clearButtonLoading($('.add_to_cart_button.is-loading, .single_add_to_cart_button.is-loading'));
        });

        $(document).on('ajaxError', function(_, jqxhr) {
            if (jqxhr && jqxhr.responseJSON && jqxhr.responseJSON.error) {
                clearButtonLoading($('.add_to_cart_button.is-loading, .single_add_to_cart_button.is-loading'));
            }
        });

        $('.quantity input[type="number"]').on('change', function() {
            const qty = parseInt($(this).val(), 10);
            const price = parseFloat($('.price .amount').first().text().replace(/[^0-9.]/g, ''));

            if (!Number.isNaN(qty) && !Number.isNaN(price)) {
                $('.calculated-total').text('$' + (qty * price).toFixed(2));
            }
        });
    }

    // Initialize WooCommerce enhancements when DOM is ready
    $(document).ready(function() {
        if ($('body').hasClass('woocommerce')) {
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
