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
            '/wp-content/themes/svicloudtvbox/assets/images/svicloud-hero-product.png',
            '/wp-content/themes/svicloudtvbox/assets/images/svicloud-10p-plus.png',
            '/wp-content/themes/svicloudtvbox/assets/images/svicloud-10s.png'
        ];

        criticalImages.forEach(function(src) {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.href = src;
            document.head.appendChild(link);
        });

        // Throttled scroll handler for performance
        let ticking = false;

        function updateScrollElements() {
            // Add subtle header background on scroll
            const scrollY = window.scrollY;
            const header = $('.site-header');

            if (scrollY > 100) {
                header.addClass('scrolled');
            } else {
                header.removeClass('scrolled');
            }

            ticking = false;
        }

        $(window).on('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(updateScrollElements);
                ticking = true;
            }
        });
    }

    /**
     * Product grid layout toggle
     */
    function initProductGridToggle() {
        const grid = $('.product-grid');
        if (!grid.length) return;

        $('.grid-toggle .grid-btn').on('click', function() {
            const mode = $(this).data('mode');
            $('.grid-toggle .grid-btn').removeClass('active');
            $(this).addClass('active');
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
        // Add loading state to add to cart buttons
        $('.single_add_to_cart_button').on('click', function() {
            const button = $(this);
            const originalText = button.text();

            button.addClass('loading').text('Adding...');

            // Reset after 3 seconds (fallback)
            setTimeout(function() {
                button.removeClass('loading').text(originalText);
            }, 3000);
        });

        // Enhance quantity selector
        $('.quantity input[type="number"]').on('change', function() {
            const qty = parseInt($(this).val());
            const price = parseFloat($('.price .amount').text().replace(/[^0-9.]/g, ''));

            if (!isNaN(qty) && !isNaN(price)) {
                // Update any total displays
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
    /** Mobile menu for classic header **/
    (function(){
        $(document).on('click', '.mobile-menu-toggle', function(){
            const expanded = $(this).attr('aria-expanded') === 'true';
            const willOpen = !expanded;
            $(this).attr({'aria-expanded': willOpen.toString(), 'aria-label': willOpen ? 'Close menu' : 'Open menu'});
            $('body').toggleClass('nav-open', willOpen);
        });
        $(document).on('keydown', function(e){
            if (e.key === 'Escape') { $('body').removeClass('nav-open'); $('.mobile-menu-toggle').attr({'aria-expanded': 'false', 'aria-label': 'Open menu'}); }
        });
        $(document).on('click', function(e){
            if (!$(e.target).closest('.primary-nav, .mobile-menu-toggle').length) {
                $('body').removeClass('nav-open');
                $('.mobile-menu-toggle').attr({'aria-expanded': 'false', 'aria-label': 'Open menu'});
            }
        });
        $(document).on('click', '.primary-nav a', function(){
            $('body').removeClass('nav-open');
            $('.mobile-menu-toggle').attr({'aria-expanded': 'false', 'aria-label': 'Open menu'});
        });
    })();

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
