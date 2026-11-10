/* =============================================================================
   Main JavaScript
   ========================================================================== */

// @prepros-prepend plugins/splide.min.js
// @prepros-prepend plugins/blazy.js
// @prepros-prepend plugins/parvus.min.js


document.addEventListener('DOMContentLoaded', function () {


    /* Animated header */
    /*******************/
    var changeHeader = 50;
    var hideHeaderThreshold = 20;
    var previousScroll = 0;
    var scrollTicking = false;
    var header = document.querySelector('header');
    var hamburgerBtn = document.querySelector('.hamburger-btn');

    function getCurrentScroll() {
        return Math.max(0, window.pageYOffset || document.documentElement.scrollTop);
    }

    // One-time fix: force iOS Safari to recompute the fixed header's
    // compositing layer against the real viewport. Without this, the header
    // can render with a transparent background behind Safari's toolbar.
    if (header) {
        header.style.position = 'sticky';
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                header.style.position = 'fixed';
            });
        });
    }

    function checkScroll() {
        if (!header) return;
        var currentScroll = getCurrentScroll();

        // Dead zone: ignore sub-5px scroll noise so the classes can't
        // flip back and forth.
        if (Math.abs(currentScroll - previousScroll) <= 5) return;

        // 'scrolled' (opaque background) persists past the threshold
        // regardless of direction, and only clears near the top again.
        if (currentScroll > changeHeader) {
            header.classList.add('scrolled');
            if (hamburgerBtn) hamburgerBtn.classList.add('scrolled');
        } else if (currentScroll < hideHeaderThreshold) {
            header.classList.remove('scrolled');
            if (hamburgerBtn) hamburgerBtn.classList.remove('scrolled');
        }

        // 'scroll-up' (reveal) toggles freely with direction.
        if (currentScroll < previousScroll && currentScroll > 0) {
            header.classList.add('scroll-up');
            if (hamburgerBtn) hamburgerBtn.classList.add('scroll-up');
        } else {
            header.classList.remove('scroll-up');
            if (hamburgerBtn) hamburgerBtn.classList.remove('scroll-up');
        }

        previousScroll = currentScroll;
    }

    // Run check on page load
    checkScroll();

    // Run check on scroll, throttled to once per paint frame
    window.addEventListener('scroll', function () {
        if (!scrollTicking) {
            requestAnimationFrame(function () {
                checkScroll();
                scrollTicking = false;
            });
            scrollTicking = true;
        }
    }, { passive: true });



    /* Animated footer reveal */
    /***************************/
    var footer = document.querySelector('.footer');

    if (footer) {

        // Keep --footer-height in sync with the fixed footer's real
        // rendered height, so .wrapper's margin-bottom (which reserves
        // scroll space for it) always matches regardless of screen width.
        if ('ResizeObserver' in window) {
            var setFooterHeightVar = function () {
                document.documentElement.style.setProperty('--footer-height', footer.offsetHeight + 'px');
            };
            new ResizeObserver(setFooterHeightVar).observe(footer);
        }

        window.addEventListener('scroll', function () {
            var scrollBottom = window.pageYOffset + window.innerHeight;
            var docHeight = document.documentElement.scrollHeight;

            if (scrollBottom >= docHeight - 20) {
                footer.classList.add('reveal-footer');
            } else {
                footer.classList.remove('reveal-footer');
            }
        }, { passive: true });
    }



    /* Navigation panel */
    /*********************/
    var body     = document.body;
    var menuBtn  = document.querySelector('.menu-button');
    var navPanel = document.getElementById('wrapper_nav');

    function openNav() {
        body.classList.add('nav-open');
        menuBtn.setAttribute('aria-expanded', 'true');
        menuBtn.setAttribute('aria-label', 'Close navigation menu');
        navPanel.setAttribute('aria-hidden', 'false');
        var first = navPanel.querySelector('a, button');
        if (first) first.focus();
    }

    function closeNav() {
        body.classList.add('nav-closing');
        body.classList.remove('nav-open');
        menuBtn.setAttribute('aria-expanded', 'false');
        menuBtn.setAttribute('aria-label', 'Open navigation menu');
        navPanel.setAttribute('aria-hidden', 'true');
        document.querySelectorAll('.submenu-toggle').forEach(function (btn) {
            btn.setAttribute('aria-expanded', 'false');
        });
        menuBtn.focus();

        // Two rAFs guarantee the no-transition frame has painted
        // before restoring normal header transitions.
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                body.classList.remove('nav-closing');
            });
        });
    }

    if (menuBtn) {
        menuBtn.addEventListener('click', function () {
            if (body.classList.contains('nav-open')) {
                closeNav();
            } else {
                openNav();
            }
        });
    }

    // Close on Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && body.classList.contains('nav-open')) {
            closeNav();
        }
    });

    /* Mobile accordion — only fires on mobile since buttons are display:none on desktop */
    document.querySelectorAll('.submenu-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var isOpen = this.getAttribute('aria-expanded') === 'true';

            // Collapse any other open submenus (accordion behaviour)
            document.querySelectorAll('.submenu-toggle[aria-expanded="true"]').forEach(function (other) {
                if (other !== btn) other.setAttribute('aria-expanded', 'false');
            });

            this.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        });
    });




    /*!
    * Splide.js
    * Version  : 4.1.4
    * License  : MIT
    * Copyright: 2022 Naotoshi Fujita
    */
    var splideElement = document.querySelector('#home-slider');

    if (splideElement) {
        var splide = new Splide(splideElement, {
            type  : 'fade',
            arrows: true,
            rewind: true,
            width : '100vw',
            arrowPath: 'M39.54 20.51l-7.03 7.03c-.14.14-.33.21-.51.21s-.36-.08-.51-.21a.7.7 0 0 1 0-1.01l5.8-5.8H.98a.73.73 0 0 1 0-1.45h36.31l-5.8-5.8c-.29-.29-.29-.74 0-1.01s.74-.29 1.01 0l7.03 7.03c.29.29.29.74 0 1.01Z',
        });

        // Custom pagination
        splide.on('pagination:mounted', function (data) {
            data.list.classList.add('splide__pagination--glor');

            data.items.forEach(function (item, index) {
                item.button.textContent = index + 1;
            });
        });

        splide.mount();

        var pagination = splideElement.querySelector('.splide__pagination');
        var controls = splideElement.querySelector('.splide__controls');
        if (pagination && controls) {
            controls.insertBefore(pagination, controls.firstChild);
        }
    }

    // Initialize all news sliders (supports multiple instances)
	document.querySelectorAll('.news-slider').forEach(function(newsSliderElem) {
		var newsSlider = new Splide(newsSliderElem, {
			perPage: 4,
			perMove: 1,
			gap: '2rem',
			arrows: true,
			pagination: true,
			breakpoints: {
				1280: { perPage: 3 },
				1024: { gap: '1.5rem' },
				768: { perPage: 2 },
				480: { gap: '1.8rem' } /* Alex Amends (08/09/26) */
			},
			arrowPath: 'M14.67 36.45L12.84 34.62L27.5 20L12.84 5.38L14.67 3.55L31.16 20L14.67 36.45Z',
			classes: {
				arrows: 'splide__arrows splide__arrows-top',
			}
		});

		// Remove empty last slide on small screens
		function removeEmptySlideOnMobile() {
			var lastSlide = newsSliderElem.querySelector('.splide__slide:last-of-type');
			if (window.innerWidth <= 600 && lastSlide && lastSlide.children.length === 0) {
				lastSlide.remove();
				newsSlider.refresh();
			}
		}

		// Mount the news slider
		newsSlider.mount();

		// Move Splide-injected arrows into the controls wrapper (alongside pagination)
		var controlsElem = newsSliderElem.querySelector('.splide__controls');
		var arrowsElem = newsSliderElem.querySelector('.splide__arrows');
		if (controlsElem && arrowsElem) {
			controlsElem.appendChild(arrowsElem);
		}

		// Check on initial load
		removeEmptySlideOnMobile();
	});

    // Initialize all events (unofficial) sliders (supports multiple instances)
    document.querySelectorAll('.events-slider').forEach(function(eventsSliderElem) {
        var eventsSlider = new Splide(eventsSliderElem, {
            perPage: 3,
            perMove: 1,
            gap: '2rem',
            arrows: true,
            pagination: true,
            breakpoints: {
                1024: { gap: '1.5rem' },
                768: { perPage: 2 },
                480: { perPage: 1, gap: '0.8rem' }
            },
            arrowPath: 'M14.67 36.45L12.84 34.62L27.5 20L12.84 5.38L14.67 3.55L31.16 20L14.67 36.45Z',
            classes: {
                arrows: 'splide__arrows splide__arrows-top',
            }
        });

        eventsSlider.mount();

        // Move Splide-injected arrows into the controls wrapper (alongside pagination)
        var controlsElem = eventsSliderElem.querySelector('.splide__controls');
        var arrowsElem = eventsSliderElem.querySelector('.splide__arrows');
        if (controlsElem && arrowsElem) {
            controlsElem.appendChild(arrowsElem);
        }
    });

    // Initialize all view-next sliders (supports multiple instances)
    document.querySelectorAll('.view-next-slider').forEach(function(viewNextSliderElem) {
        var isMobileArrow = window.matchMedia('(max-width: 480px)').matches;

        var viewNextSlider = new Splide(viewNextSliderElem, {
            type      : 'slide',
            arrows    : true,
            pagination: true,
            arrowPath: isMobileArrow
                ? 'M14.67 36.45L12.84 34.62L27.5 20L12.84 5.38L14.67 3.55L31.16 20L14.67 36.45Z'
                : 'M10.18 39.87a.21.21 0 0 0 .16-.07L29.98 20.16c.04-.04.07-.1.07-.16s-.02-.12-.07-.16L10.34.2c-.09-.09-.23-.09-.32 0s-.09.23 0 .32L29.5 20l-19.49 19.49c-.09.09-.09.23 0 .32.05.05.1.07.16.07Z',
            classes : {
                arrows: 'splide__arrows splide__arrows-top',
            }
        });

        // Splide's autoHeight option only affects vertical (ttb) carousels, so for
        // this horizontal one we size the track to the active slide's real content
        // height by hand (the slide itself is a flex item stretched by
        // .splide__list, so measure its child instead).
        var viewNextTrack = viewNextSliderElem.querySelector('.splide__track');

        function updateViewNextHeight(index) {
            var slides = viewNextSliderElem.querySelectorAll('.splide__slide');
            var slide  = slides[index] || slides[0];
            if (viewNextTrack && slide && slide.firstElementChild) {
                viewNextTrack.style.height = slide.firstElementChild.offsetHeight + 'px';
            }
        }

        viewNextSlider.on('mounted', function () {
            updateViewNextHeight(viewNextSlider.index);
        });
        viewNextSlider.on('move', function (newIndex) {
            updateViewNextHeight(newIndex);
        });
        viewNextSlider.on('resized', function () {
            updateViewNextHeight(viewNextSlider.index);
        });

        viewNextSlider.mount();
    });

    // Initialize all quote sliders (supports multiple instances)
    document.querySelectorAll('.quote-slider').forEach(function(quoteSliderElem) {
        var slideCount = quoteSliderElem.querySelectorAll('.splide__slide').length;
        var isMobileArrow = window.matchMedia('(max-width: 480px)').matches;

        var quoteSlider = new Splide(quoteSliderElem, {
            type      : 'slide',
            arrows    : slideCount > 1,
            pagination: slideCount > 1,
            arrowPath: isMobileArrow
                ? 'M14.67 36.45L12.84 34.62L27.5 20L12.84 5.38L14.67 3.55L31.16 20L14.67 36.45Z'
                : 'M10.18 39.87a.21.21 0 0 0 .16-.07L29.98 20.16c.04-.04.07-.1.07-.16s-.02-.12-.07-.16L10.34.2c-.09-.09-.23-.09-.32 0s-.09.23 0 .32L29.5 20l-19.49 19.49c-.09.09-.09.23 0 .32.05.05.1.07.16.07Z',
            classes : {
                arrows: 'splide__arrows splide__arrows-top',
            }
        });

        // Splide's autoHeight option only affects vertical (ttb) carousels, so for
        // this horizontal one we size the track to the active slide's real content
        // height by hand (the slide itself is a flex item stretched by
        // .splide__list, so measure its child instead).
        var quoteTrack = quoteSliderElem.querySelector('.splide__track');

        function updateQuoteHeight(index) {
            var slides = quoteSliderElem.querySelectorAll('.splide__slide');
            var slide  = slides[index] || slides[0];
            if (quoteTrack && slide && slide.firstElementChild) {
                quoteTrack.style.height = slide.firstElementChild.offsetHeight + 'px';
            }
        }

        quoteSlider.on('mounted', function () {
            updateQuoteHeight(quoteSlider.index);
        });
        quoteSlider.on('move', function (newIndex) {
            updateQuoteHeight(newIndex);
        });
        quoteSlider.on('resized', function () {
            updateQuoteHeight(quoteSlider.index);
        });

        quoteSlider.mount();
    });




    /*!
      hey, [be]Lazy.js - v1.8.2 - 2016.10.25
      A fast, small and dependency free lazy load script (https://github.com/dinbror/blazy)
      (c) Bjoern Klinggaard - @bklinggaard - http://dinbror.dk/blazy
    */
    new Blazy({
        // options
    });




    /*
	 * Accordion
	 * https://codepen.io/radoslav-valchev/pen/QWNEjvX
     * Customised for accessibility and WCAG AA compliance
     * Converted to vanilla JavaScript
	 */
    var accordion = (function(){
        var settings = {
            speed: 200,
            oneOpen: false
        };

        // Check for reduced motion preference
        function getAnimationDuration() {
            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            return prefersReducedMotion ? 0 : settings.speed;
        }

        // Helper function for smooth height animation
        function slideToggle(element, duration) {
            const isHidden = element.hasAttribute('hidden') || element.style.display === 'none' || !element.style.display;
            if (isHidden) {
                slideDown(element, duration);
            } else {
                slideUp(element, duration);
            }
        }

        function slideDown(element, duration) {
            element.style.height = '0px';
            element.style.overflow = 'hidden';
            element.style.display = 'block';
            element.removeAttribute('hidden');
            const height = element.scrollHeight;

            if (duration > 0) {
                element.style.transition = `height ${duration}ms ease`;
            }

            requestAnimationFrame(() => {
                element.style.height = height + 'px';
            });

            setTimeout(() => {
                element.style.height = '';
                element.style.overflow = '';
                element.style.transition = '';
            }, duration);
        }

        function slideUp(element, duration) {
            const height = element.scrollHeight;
            element.style.height = height + 'px';
            element.style.overflow = 'hidden';

            if (duration > 0) {
                element.style.transition = `height ${duration}ms ease`;
            }

            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    element.style.height = '0px';
                });
            });

            setTimeout(() => {
                element.setAttribute('hidden', '');
                element.style.display = 'none';
                element.style.height = '';
                element.style.overflow = '';
                element.style.transition = '';
            }, duration);
        }

        // Helper function to find closest parent matching selector
        function closest(element, selector) {
            while (element && element !== document) {
                if (element.matches(selector)) {
                    return element;
                }
                element = element.parentElement;
            }
            return null;
        }

        return {
            init: function(userSettings) {
                const accordionHeaders = document.querySelectorAll('.js-accordion-header');

                accordionHeaders.forEach((header, index) => {
                    // Set up ARIA relationships
                    const body = header.nextElementSibling;

                    if (body) {
                        // Set up IDs and ARIA attributes if not present
                        if (!header.getAttribute('aria-controls')) {
                            const panelId = body.id || `accordion-panel-${index}`;
                            body.id = panelId;
                            header.setAttribute('aria-controls', panelId);
                        }

                        if (!body.hasAttribute('role')) {
                            body.setAttribute('role', 'region');
                        }

                        if (!body.getAttribute('aria-labelledby')) {
                            const headerId = header.id || `accordion-header-${index}`;
                            header.id = headerId;
                            body.setAttribute('aria-labelledby', headerId);
                        }

                        // Set initial hidden state
                        const isActive = closest(header, '.js-accordion-item')?.classList.contains('active');
                        if (!isActive) {
                            body.setAttribute('hidden', '');
                        }
                    }

                    // Initialize aria-expanded if not set
                    if (!header.hasAttribute('aria-expanded')) {
                        const isActive = closest(header, '.js-accordion-item')?.classList.contains('active');
                        header.setAttribute('aria-expanded', isActive ? 'true' : 'false');
                    }

                    // Click handler
                    header.addEventListener('click', function() {
                        accordion.toggle(this);

                        // // Animate scroll after 400ms
                        // setTimeout(() => {
                        //     const elementPosition = this.getBoundingClientRect().top;
                        //     const offsetPosition = elementPosition + window.pageYOffset - 140;

                        //     window.scrollTo({
                        //         top: offsetPosition,
                        //         behavior: 'smooth'
                        //     });
                        // }, 400);
                    });

                    // Keyboard navigation
                    header.addEventListener('keydown', function(e) {
                        const headers = Array.from(document.querySelectorAll('.js-accordion-header'));
                        const currentIndex = headers.indexOf(this);

                        switch(e.key) {
                            case 'ArrowDown':
                                e.preventDefault();
                                const nextIndex = (currentIndex + 1) % headers.length;
                                headers[nextIndex].focus();
                                break;

                            case 'ArrowUp':
                                e.preventDefault();
                                const prevIndex = (currentIndex - 1 + headers.length) % headers.length;
                                headers[prevIndex].focus();
                                break;

                            case 'Home':
                                e.preventDefault();
                                headers[0].focus();
                                break;

                            case 'End':
                                e.preventDefault();
                                headers[headers.length - 1].focus();
                                break;
                        }
                    });
                });

                // Merge user settings with defaults
                Object.assign(settings, userSettings);

                // Close other accordion items if oneOpen is true
                const activeItems = document.querySelectorAll('.js-accordion-item.active');
                if (settings.oneOpen && activeItems.length > 1) {
                    activeItems.forEach((item, index) => {
                        if (index > 0) {
                            item.classList.remove('active');
                        }
                    });
                }

                // Show the active accordion bodies
                activeItems.forEach(item => {
                    const body = item.querySelector('.js-accordion-body');
                    if (body) {
                        body.style.display = 'block';
                        body.removeAttribute('hidden');
                    }
                });
            },
            toggle: function(headerElement) {
                const accordionItem = closest(headerElement, '.js-accordion-item');
                const accordionParent = closest(headerElement, '.js-accordion');
                const accordionBody = headerElement.nextElementSibling;
                const duration = getAnimationDuration();

                // Close other items if oneOpen is true
                if (settings.oneOpen) {
                    const currentActiveHeader = accordionParent.querySelector('.js-accordion-item.active > .js-accordion-header');

                    if (currentActiveHeader && currentActiveHeader !== headerElement) {
                        const itemsToClose = accordionParent.querySelectorAll('.js-accordion-item');
                        itemsToClose.forEach(item => {
                            item.classList.remove('active');
                            const header = item.querySelector('.js-accordion-header');
                            const body = item.querySelector('.js-accordion-body');

                            if (header) {
                                header.setAttribute('aria-expanded', 'false');
                            }

                            if (body && !body.hasAttribute('hidden')) {
                                slideUp(body, duration);
                            }
                        });
                    }
                }

                // Toggle the clicked accordion item
                const isExpanding = !accordionItem.classList.contains('active');
                accordionItem.classList.toggle('active');

                // Toggle aria-expanded attribute
                headerElement.setAttribute('aria-expanded', isExpanding ? 'true' : 'false');

                // Slide toggle the body
                slideToggle(accordionBody, duration);
            }
        };
    })();

    accordion.init({ speed: 200, oneOpen: true });




    /*
	* Page navigation
	* Smooth scroll + active link highlighting
	*/
    var pageNavLinks = document.querySelectorAll('.page-nav-link');

    if (pageNavLinks.length) {
        var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // Measure --header-height via a probe element so it stays correct
        // across the responsive breakpoint (11rem desktop / 7rem mobile)
        function getPageNavScrollOffset() {
            var probe = document.createElement('div');
            probe.style.position = 'absolute';
            probe.style.visibility = 'hidden';
            probe.style.height = 'calc(var(--header-height) + 80px)';
            document.body.appendChild(probe);
            var offset = probe.offsetHeight;
            document.body.removeChild(probe);
            return offset;
        }

        pageNavLinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                var targetId = this.getAttribute('href');
                if (!targetId || targetId.charAt(0) !== '#') return;

                var target = document.querySelector(targetId);
                if (!target) return;

                e.preventDefault();

                var offsetPosition = target.getBoundingClientRect().top + window.pageYOffset - getPageNavScrollOffset();

                window.scrollTo({
                    top: offsetPosition,
                    behavior: prefersReducedMotion ? 'auto' : 'smooth'
                });

                // Move focus to the target for keyboard/screen reader users
                target.setAttribute('tabindex', '-1');
                target.focus({ preventScroll: true });
            });
        });

        // Highlight whichever nav link matches the section currently in view
        if ('IntersectionObserver' in window) {
            var pageNavSections = [];

            pageNavLinks.forEach(function (link) {
                var targetId = link.getAttribute('href');
                var target = targetId ? document.querySelector(targetId) : null;
                if (target) {
                    pageNavSections.push({ link: link, target: target });
                }
            });

            if (pageNavSections.length) {
                var setActivePageNavLink = function (activeLink) {
                    pageNavLinks.forEach(function (link) {
                        link.classList.toggle('active', link === activeLink);
                    });
                };

                var pageNavObserver = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (!entry.isIntersecting) return;
                        var match = pageNavSections.find(function (section) {
                            return section.target === entry.target;
                        });
                        if (match) setActivePageNavLink(match.link);
                    });
                }, {
                    // Activation line sits just below the sticky header/nav; the -70%
                    // bottom margin keeps only the upper part of the viewport "active"
                    rootMargin: '-' + getPageNavScrollOffset() + 'px 0px -70% 0px',
                    threshold: 0
                });

                pageNavSections.forEach(function (section) {
                    pageNavObserver.observe(section.target);
                });
            }
        }
    }



    // Card News — tap to reveal CTA, tap again to follow (touch only)
    // Disabled per designer request: .card-news is now static/stacked on
    // touch, no hover effect to reveal. Left here commented out in case
    // that decision changes again.
    // ------------------------------------------------------------------
    // var newsCardOverlays = document.querySelectorAll('.card-news-overlay');
    // var noHoverQuery = window.matchMedia('(hover: none)');
    //
    // if (newsCardOverlays.length) {
    //     newsCardOverlays.forEach(function (overlay) {
    //         overlay.addEventListener('click', function (e) {
    //             if (!noHoverQuery.matches) return; // hover-capable devices: click follows normally
    //
    //             var card = overlay.closest('.card-news');
    //             if (!card || card.classList.contains('is-active')) return;
    //
    //             e.preventDefault();
    //
    //             document.querySelectorAll('.card-news.is-active').forEach(function (openCard) {
    //                 if (openCard !== card) openCard.classList.remove('is-active');
    //             });
    //
    //             card.classList.add('is-active');
    //         });
    //     });
    //
    //     // Tapping outside an open card closes it again
    //     document.addEventListener('click', function (e) {
    //         if (!noHoverQuery.matches) return;
    //
    //         document.querySelectorAll('.card-news.is-active').forEach(function (openCard) {
    //             if (!openCard.contains(e.target)) {
    //                 openCard.classList.remove('is-active');
    //             }
    //         });
    //     });
    // }



    /**
     * Parvus
     * Benjamin de Oostfrees
     * version 2.4.0
     * url https://github.com/deoostfrees/parvus
     * MIT license
     */
    const prvs = new Parvus({
        gallerySelector: '.gallery',
    });


});
