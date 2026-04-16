/**
 * SVICLOUD TV Box Theme JavaScript
 * Enhanced functionality for neon-tech theme
 */

// Register easeInOutCubic easing for smooth scroll (jQuery UI not loaded)
jQuery.easing.easeInOutCubic = function(x, t, b, c, d) {
  t /= d / 2;
  if (t < 1) return c / 2 * t * t * t + b;
  t -= 2;
  return c / 2 * (t * t * t + 2) + b;
};

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
        initConversionTracking();
        initProductImageEnhancements();
        initAnimationOnScroll();
        initPerformanceOptimizations();
        initLumenNavigation();
        initProductHeroGallery();
        initStripeSavedCardPills();
        initCartQuantityControls();
        $(document.body).on('updated_wc_div cart_page_refreshed updated_cart_totals', initCartQuantityControls);
        relocateCheckoutCoupon();
        $(document.body).on('updated_checkout applied_coupon_in_checkout removed_coupon_in_checkout', relocateCheckoutCoupon);
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
        const $body = $('body');
        const htmlLang = (document.documentElement.getAttribute('lang') || '').toLowerCase();
        if (htmlLang.indexOf('zh') === 0) {
            $body.addClass('lang-zh');
        } else {
            $body.removeClass('lang-zh');
        }

        const $links = $('.lumen-lang-toggle__link');
        if (!$links.length) {
            return;
        }

        $links.on('click', function(event) {
            const locale = ($(this).data('locale') || '').toString().toLowerCase();
            const href = $(this).attr('href');

            if (locale.indexOf('zh') === 0) {
                $body.addClass('lang-zh');
            } else {
                $body.removeClass('lang-zh');
            }

            // Persist language selection so subsequent requests keep the locale.
            try {
                const maxAge = 60 * 60 * 24 * 30; // 30 days
                const secure = window.location.protocol === 'https:' ? ';secure' : '';
                document.cookie = 'svic_lang=' + locale + ';path=/;samesite=lax;max-age=' + maxAge + secure;
            } catch (e) {
                // no-op
            }

            // Force navigation in the same tab to avoid blocked clicks.
            if (href) {
                event.preventDefault();
                window.location.assign(href);
            }
        });
    }

    /**
     * Smooth scrolling for anchor links
     */

    function initSmoothScrolling() {
        $('a[href*="#"]:not([href="#"])').on('click', function(e) {
            const hash = this.hash;
            const target = hash ? document.querySelector(hash) : null;

            if (target) {
                e.preventDefault();
                const offset = getStickyScrollOffset();
                const top = Math.max(0, window.scrollY + target.getBoundingClientRect().top - offset);

                window.scrollTo({
                    top,
                    behavior: 'smooth'
                });

                if (window.history && typeof window.history.replaceState === 'function') {
                    window.history.replaceState(null, '', hash);
                } else {
                    window.location.hash = hash;
                }
            }
        });
    }


    function initConversionTracking() {
        const trackedSelector = '[data-svic-event]';
        if (!document.querySelector(trackedSelector) && !document.querySelector('form.cart')) {
            return;
        }

        const pushTrackingEvent = (payload) => {
            if (!payload || !payload.event) {
                return;
            }

            if (!Array.isArray(window.dataLayer)) {
                window.dataLayer = [];
            }

            const dataLayerPayload = Object.assign({}, payload, {
                svic_location: payload.location || '',
                svic_label: payload.label || '',
                svic_model: payload.model || '',
                svic_href: payload.href || '',
            });

            window.dataLayer.push(dataLayerPayload);

            if (typeof window.gtag === 'function') {
                window.gtag('event', payload.event, {
                    event_category: 'conversion_path',
                    event_label: payload.label || '',
                    location: payload.location || '',
                    model: payload.model || '',
                    href: payload.href || '',
                });
            }

            try {
                window.dispatchEvent(new CustomEvent('svic:track', { detail: payload }));
            } catch (e) {
                // no-op
            }
        };

        $(document).on('click', trackedSelector, function(event) {
            const element = event.currentTarget;
            if (!(element instanceof HTMLElement)) {
                return;
            }

            const dataset = element.dataset || {};
            pushTrackingEvent({
                event: dataset.svicEvent || 'svic_cta_click',
                location: dataset.svicLocation || '',
                label: dataset.svicLabel || element.textContent.trim(),
                model: dataset.svicModel || '',
                href: element.getAttribute('href') || '',
                interaction: 'click'
            });
        });

        $(document).on('submit', 'form.cart', function() {
            const $form = $(this);
            const $button = $form.find('.single_add_to_cart_button');
            const label = ($button.text() || '').trim();
            const productTitle = ($('.product-hero-title').first().text() || $('.product_title').first().text() || '').trim();
            const pathParts = window.location.pathname.split('/').filter(Boolean);
            const model = pathParts.length ? pathParts[pathParts.length - 1] : '';
            pushTrackingEvent({
                event: 'svic_cta_add_to_cart',
                location: 'pdp_hero',
                label: label || productTitle || 'add_to_cart',
                model,
                href: window.location.href,
                interaction: 'submit'
            });
        });

        $(document).on('click', '.lumen-cart-update', function() {
            pushTrackingEvent({
                event: 'svic_cart_update',
                location: 'cart_actions',
                label: 'update_cart',
                href: window.location.href,
                interaction: 'click'
            });
        });

        $(document).on('click', '.wc-proceed-to-checkout .checkout-button', function() {
            pushTrackingEvent({
                event: 'svic_begin_checkout',
                location: 'cart_totals',
                label: 'checkout_button',
                href: this.getAttribute('href') || window.location.href,
                interaction: 'click'
            });
        });

        $(document).on('click', '#place_order', function() {
            const paymentMethod = ($('input[name="payment_method"]:checked').val() || '').toString();
            pushTrackingEvent({
                event: 'svic_place_order_attempt',
                location: 'checkout_payment',
                label: 'place_order',
                model: paymentMethod,
                href: window.location.href,
                interaction: 'click'
            });
        });

        $(document).on('submit', 'form.checkout.woocommerce-checkout', function() {
            const paymentMethod = ($('input[name="payment_method"]:checked').val() || '').toString();
            pushTrackingEvent({
                event: 'svic_checkout_submit',
                location: 'checkout_form',
                label: 'checkout_submit',
                model: paymentMethod,
                href: window.location.href,
                interaction: 'submit'
            });
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
        // Only preload hero assets on the homepage to avoid console warnings on inner pages
        const bodyHasClass = (cls) => document.body && document.body.classList.contains(cls);
        const isHomeLike = bodyHasClass('home') || bodyHasClass('front-page') || bodyHasClass('page-template-front-page');

        if (!isHomeLike) {
            return;
        }

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
            assetFromTheme('assets/images/hero-voice-assistant.webp'),
            assetFromTheme('assets/images/svicloud-hero-product.webp'),
            assetFromTheme('assets/images/svicloud-10p-plus.png'),
            assetFromTheme('assets/images/svicloud-tvbox-10s.jpg')
        ];

        criticalImages.forEach((src) => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.href = src;
            document.head.appendChild(link);
        });

        // Lazy load background sections with data-bg marker
        const lazyBgSections = document.querySelectorAll('[data-bg]');
        if ('IntersectionObserver' in window && lazyBgSections.length) {
            const bgObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) {
                        return;
                    }
                    const el = entry.target;
                    const bg = el.getAttribute('data-bg-src');
                    if (bg) {
                        el.style.backgroundImage = `url(${bg})`;
                    }
                    el.removeAttribute('data-bg-src');
                    observer.unobserve(el);
                });
            }, { rootMargin: '200px 0px' });

            lazyBgSections.forEach((section) => bgObserver.observe(section));
        }

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

    /**
     * Cart quantity steppers
     */
    function initCartQuantityControls() {
        const $cart = $('[data-cart-page]');
        if (!$cart.length) {
            return;
        }

        const $updateButton = $cart.find('button[name="update_cart"]');
        const markCartDirty = () => {
            if (!$updateButton.length) return;
            if ($updateButton.prop('disabled') !== false) {
                $updateButton.prop('disabled', false);
            }
            $updateButton.attr('aria-disabled', 'false');
            $updateButton.addClass('is-dirty');
        };

        $cart.find('.lumen-cart-qty__inner[data-qty]').each(function() {
            const $wrapper = $(this);
            if ($wrapper.data('qtyBound')) {
                return;
            }

            const $input = $wrapper.find('.lumen-cart-qty__field');
            if (!$input.length) {
                return;
            }

            const $decrease = $wrapper.find('[data-qty-control="decrease"]');
            const $increase = $wrapper.find('[data-qty-control="increase"]');

            const stepAttr = parseFloat($input.attr('step'));
            const step = Number.isFinite(stepAttr) && stepAttr > 0 ? stepAttr : 1;
            const precision = (step.toString().split('.')[1] || '').length;
            const minAttr = parseFloat($input.attr('min'));
            const min = Number.isFinite(minAttr) ? minAttr : 0;
            const maxAttr = $input.attr('max');
            const parsedMax = typeof maxAttr === 'string' && maxAttr.length ? parseFloat(maxAttr) : Infinity;
            const max = Number.isFinite(parsedMax) ? parsedMax : Infinity;

            const clampToBounds = (value) => {
                let next = Number.isFinite(value) ? value : min;
                next = Math.max(min, Math.min(max, next));
                if (precision > 0) {
                    next = parseFloat(next.toFixed(precision));
                } else {
                    next = Math.round(next);
                }
                return next;
            };

            const updateValue = (delta) => {
                const current = parseFloat($input.val());
                const normalized = Number.isFinite(current) ? current : min;
                const next = clampToBounds(normalized + delta * step);
                if (next === normalized) {
                    return;
                }
                $input.val(next);
                $input.trigger('input');
                $input.trigger('change');
                markCartDirty();
            };

            if ($decrease.length) {
                $decrease.on('click', function(event) {
                    event.preventDefault();
                    updateValue(-1);
                });
            }

            if ($increase.length) {
                $increase.on('click', function(event) {
                    event.preventDefault();
                    updateValue(1);
                });
            }

            $input.on('blur', function() {
                const current = parseFloat($input.val());
                const next = clampToBounds(current);
                if (!Number.isFinite(current) || next !== current) {
                    $input.val(next);
                    $input.trigger('input');
                    $input.trigger('change');
                    markCartDirty();
                }
            });

            $input.on('input', function() {
                const raw = $input.val();
                const sanitized = raw.replace(/[^0-9.]/g, '');
                if (raw !== sanitized) {
                    $input.val(sanitized);
                }
            });

            $input.on('change', markCartDirty);

            $wrapper.data('qtyBound', true);
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

    function initStripeSavedCardPills() {
        const $containers = $('.wc-stripe-saved-methods-container');
        if (!$containers.length) {
            return;
        }

        $containers.each(function(index) {
            const $container = $(this);
            const $select = $container.find('select').first();
            if (!$select.length) {
                return;
            }

            const options = $select.find('option');
            if (!options.length) {
                $container.removeClass('wcs-hidden').removeAttr('aria-hidden');
                $container.next('.wcs-saved-card-list').remove();
                return;
            }

            if (typeof $select.select2 === 'function' && ($select.data('select2') || $select.hasClass('select2-hidden-accessible'))) {
                try {
                    $select.select2('destroy');
                } catch (err) {
                    // Ignore if select2 not initialised yet
                }
            }

            const $existingList = $container.next('.wcs-saved-card-list');
            if ($existingList.length) {
                $existingList.remove();
            }

            const $list = $('<div class="wcs-saved-card-list" role="radiogroup" aria-label="Saved cards"></div>');

            options.each(function() {
                const $option = $(this);
                const value = ($option.val() || '').trim();
                const text = ($option.text() || '').trim();

                if (!text || !value || value === 'add_new' || value === 'new') {
                    return;
                }
                const brandClass = ($option.attr('class') || '').split(/\s+/).find(cls => cls && cls !== 'wc-stripe-saved-method') || '';
                const [brandWord, ...restWords] = text.split(' ');
                const labelText = restWords.join(' ') || '';
                const normalizedLabel = (labelText || text).replace(/\s+/g, ' ').trim();
                const endingMatch = normalizedLabel.match(/ending in\s+(\d{2,4})/i);
                const expiryMatch = normalizedLabel.match(/\(([^)]+)\)/);
                let descriptorText = normalizedLabel.replace(/\(([^)]+)\)/, '').trim();
                if (endingMatch) {
                    descriptorText = descriptorText.replace(/ending in\s+\d{2,4}/i, '').trim();
                }
                const expiryText = expiryMatch ? expiryMatch[1].replace(/expires?\s*/i, '').trim() : '';

                const brandLabel = brandClass ? brandClass.replace(/[-_]/g, ' ') : brandWord;
                const brandDisplay = (brandLabel || brandWord || 'Card').replace(/\b\w/g, char => char.toUpperCase());

                const $pill = $('<button type="button" class="wcs-saved-card-pill"></button>');
                $pill.attr({
                    'data-method-value': value,
                    'role': 'radio',
                    'aria-checked': $option.is(':selected') ? 'true' : 'false',
                    'tabindex': $option.is(':selected') ? '0' : '-1'
                });
                $pill.data('method-value', value);
                if ($option.is(':selected')) {
                    $pill.addClass('is-selected');
                }

                const $brand = $('<span class="wcs-saved-card-pill__brand"></span>').text(brandDisplay || 'Card');
                if (brandClass) {
                    $brand.addClass(`brand-${brandClass}`);
                }

                const $label = $('<span class="wcs-saved-card-pill__label"></span>');
                const numberText = endingMatch ? `\u2022\u2022\u2022\u2022 ${endingMatch[1]}` : (descriptorText || normalizedLabel || 'Card');
                const $number = $('<span class="wcs-saved-card-pill__number"></span>').text(numberText);
                $label.append($number);

                const metaParts = [];
                if (descriptorText && (!endingMatch || descriptorText.toLowerCase() !== (brandWord || '').toLowerCase())) {
                    metaParts.push(descriptorText);
                }
                if (expiryText) {
                    metaParts.push(/^exp/i.test(expiryText) ? expiryText : `Exp. ${expiryText}`);
                }

                if (metaParts.length) {
                    $label.append($('<span class="wcs-saved-card-pill__meta"></span>').text(metaParts.join(' · ')));
                }

                const ariaParts = [
                    brandDisplay || 'Card',
                    endingMatch ? `ending in ${endingMatch[1]}` : ''
                ].filter(Boolean);
                if (expiryText) {
                    ariaParts.push(`expires ${expiryText}`);
                }

                if (ariaParts.length) {
                    $pill.attr('aria-label', ariaParts.join(' · '));
                }

                $pill.append($brand).append($label);

                $pill.on('click', function(event) {
                    event.preventDefault();
                    if ($pill.hasClass('is-selected')) {
                        return;
                    }
                    $select.val(value).trigger('change');
                    $list.find('.wcs-saved-card-pill')
                        .removeClass('is-selected')
                        .attr({'aria-checked': 'false', 'tabindex': '-1'});
                    $pill.addClass('is-selected').attr({'aria-checked': 'true', 'tabindex': '0'}).focus();
                });

                $pill.on('keydown', function(event) {
                    const keys = ['ArrowUp', 'ArrowLeft', 'ArrowDown', 'ArrowRight', 'Home', 'End'];
                    if (!keys.includes(event.key)) {
                        return;
                    }
                    event.preventDefault();
                    const $pills = $list.find('.wcs-saved-card-pill');
                    const currentIndex = $pills.index($pill);
                    let targetIndex = currentIndex;

                    if (event.key === 'ArrowUp' || event.key === 'ArrowLeft') {
                        targetIndex = currentIndex > 0 ? currentIndex - 1 : $pills.length - 1;
                    } else if (event.key === 'ArrowDown' || event.key === 'ArrowRight') {
                        targetIndex = currentIndex < $pills.length - 1 ? currentIndex + 1 : 0;
                    } else if (event.key === 'Home') {
                        targetIndex = 0;
                    } else if (event.key === 'End') {
                        targetIndex = $pills.length - 1;
                    }

                    const $target = $pills.eq(targetIndex);
                    if ($target.length) {
                        $target.trigger('click');
                    }
                });

            $list.append($pill);
        });

        if (!$list.children().length) {
            $container.removeClass('wcs-hidden').removeAttr('aria-hidden');
            return;
        }

        $container.addClass('wcs-hidden').attr('aria-hidden', 'true').after($list);

        $select.off('change.wcsSavedCardSync').on('change.wcsSavedCardSync', function() {
            const value = String($(this).val() || '');
            $list.find('.wcs-saved-card-pill').each(function() {
                const $pill = $(this);
                    const isActive = $pill.data('method-value') === value;
                    $pill
                        .toggleClass('is-selected', isActive)
                        .attr({
                            'aria-checked': isActive ? 'true' : 'false',
                            'tabindex': isActive ? '0' : '-1'
                        });
                    if (isActive) {
                        $pill.focus();
                    }
                });
            });
        });

        document.querySelectorAll('.wcs-saved-card-list').forEach(list => {
            const previous = list.previousElementSibling;
            if (previous && previous.classList && previous.classList.contains('wc-stripe-saved-methods-container')) {
                previous.classList.add('wcs-hidden');
                previous.setAttribute('aria-hidden', 'true');
            }
        });
    }

    function relocateCheckoutCoupon() {
        if (!document.body || !document.body.classList.contains('woocommerce-checkout')) {
            return;
        }

        const originalBlock = document.querySelector('.lumen-checkout-coupon[data-lumen-coupon-original="true"]');
        const summaryCard = document.querySelector('.lumen-checkout-summary__card');
        if (!originalBlock || !summaryCard) {
            return;
        }

        const target = summaryCard.querySelector('#order_review_heading') || summaryCard.querySelector('.woocommerce-checkout-review-order');
        if (!target) {
            return;
        }

        let displayBlock = summaryCard.querySelector('.lumen-checkout-coupon-display');
        if (displayBlock) {
            displayBlock.remove();
        }

        displayBlock = originalBlock.cloneNode(true);
        displayBlock.classList.add('lumen-checkout-coupon-display');
        displayBlock.removeAttribute('data-lumen-coupon-original');
        displayBlock.removeAttribute('hidden');
        summaryCard.insertBefore(displayBlock, target);

        originalBlock.setAttribute('hidden', 'hidden');

        const hiddenForm = originalBlock.querySelector('form');
        const summaryForm = displayBlock.querySelector('form');
        let summaryFormWrapper = summaryForm;

        if (summaryForm) {
            const wrapper = document.createElement('div');
            wrapper.className = summaryForm.className + ' lumen-checkout-coupon__form-display';
            wrapper.innerHTML = summaryForm.innerHTML;
            summaryForm.replaceWith(wrapper);
            summaryFormWrapper = wrapper;
        }

        if (summaryFormWrapper && hiddenForm) {
            const summaryInput = summaryFormWrapper.querySelector('input[name="coupon_code"]');
            const summaryButton = summaryFormWrapper.querySelector('button[name="apply_coupon"]');
            if (summaryButton) {
                summaryButton.addEventListener('click', (event) => {
                    event.preventDefault();
                    const hiddenInput = hiddenForm.querySelector('input[name="coupon_code"]');
                    if (hiddenInput) {
                        hiddenInput.value = summaryInput ? summaryInput.value : '';
                        $(hiddenForm).trigger('submit');
                    }
                });
            }
        }

        displayBlock.querySelectorAll('.lumen-checkout-coupon__chip-remove').forEach((link) => {
            link.addEventListener('click', (event) => {
                event.preventDefault();
                const couponCode = link.getAttribute('data-coupon');
                if (!couponCode) {
                    return;
                }
                const hiddenLink = originalBlock.querySelector(`.lumen-checkout-coupon__chip-remove[data-coupon="${couponCode}"]`);
                if (hiddenLink) {
                    hiddenLink.dispatchEvent(new Event('click', { bubbles: true, cancelable: true }));
                }
            });
        });
    }

    window.svicInitSavedCardPills = initStripeSavedCardPills;

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
        const raf = window.requestAnimationFrame ? window.requestAnimationFrame.bind(window) : function(callback) {
            return window.setTimeout(callback, 16);
        };
        const defaultLabel = (svicTheme.i18n && svicTheme.i18n.addingToCart) ? svicTheme.i18n.addingToCart : 'Adding…';
        let cartFeedbackTimer = null;
        let $cartFeedback = null;
        let hasShownInitialNotice = false;

        const scheduleSavedCardRefresh = () => {
            raf(function() {
                initStripeSavedCardPills();
            });
        };

        scheduleSavedCardRefresh();

        $body.on('updated_checkout wc-stripe-checkout-order-pay-init wc-stripe-display-tokenized-methods payment_method_selected', scheduleSavedCardRefresh);

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
