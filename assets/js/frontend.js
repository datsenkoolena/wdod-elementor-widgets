/**
 * WDOD Elementor Widgets – front-end script.
 *
 * Initialises the testimonial slider with Swiper when it is available
 * (Elementor's bundled copy through `elementorFrontend.utils.swiper`, or a
 * global `Swiper`) and otherwise wires the arrows / dots to a scroll-snap
 * fallback so the markup stays usable without any library.
 */
( function () {
	'use strict';

	var SELECTOR = '.wdod-ew-testimonials';
	var config = window.wdodEwConfig || {};
	var i18n = config.i18n || {};

	/**
	 * Reads the JSON settings from the element.
	 *
	 * @param {HTMLElement} el Slider element.
	 * @return {Object} Settings.
	 */
	function readSettings( el ) {
		var raw = el.getAttribute( 'data-settings' );

		if ( ! raw ) {
			return {};
		}

		try {
			return JSON.parse( raw ) || {};
		} catch ( e ) {
			return {};
		}
	}

	/**
	 * Returns Elementor's active breakpoints (min-width for tablet / desktop).
	 *
	 * @return {{tablet: number, desktop: number}} Breakpoints.
	 */
	function getBreakpoints() {
		var points = {
			tablet: ( config.breakpoints && config.breakpoints.tablet ) || 768,
			desktop: ( config.breakpoints && config.breakpoints.desktop ) || 1025
		};

		try {
			var active = window.elementorFrontend && elementorFrontend.config.responsive.activeBreakpoints;

			if ( active ) {
				if ( active.mobile && active.mobile.value ) {
					points.tablet = active.mobile.value + 1;
				}
				if ( active.tablet && active.tablet.value ) {
					points.desktop = active.tablet.value + 1;
				}
			}
		} catch ( e ) {
			// Keep defaults.
		}

		return points;
	}

	/**
	 * Picks a per-device value for the current viewport.
	 *
	 * @param {Object} values   {desktop, tablet, mobile}.
	 * @param {*}      fallback Fallback when nothing matches.
	 * @return {*} Value.
	 */
	function deviceValue( values, fallback ) {
		var bp = getBreakpoints();
		var width = window.innerWidth;

		if ( ! values || typeof values !== 'object' ) {
			return fallback;
		}

		if ( width >= bp.desktop ) {
			return values.desktop || values.tablet || values.mobile || fallback;
		}

		if ( width >= bp.tablet ) {
			return values.tablet || values.desktop || values.mobile || fallback;
		}

		return values.mobile || values.tablet || values.desktop || fallback;
	}

	/**
	 * Builds the Swiper options object from the element settings.
	 *
	 * @param {HTMLElement} el       Slider element.
	 * @param {Object}      settings Settings.
	 * @return {Object} Swiper options.
	 */
	function buildSwiperOptions( el, settings ) {
		var bp = getBreakpoints();
		var spv = settings.slidesPerView || {};
		var gap = settings.spaceBetween || {};
		var slideCount = el.querySelectorAll( '.swiper-slide' ).length;
		var options = {
			slidesPerView: Number( spv.mobile ) || 1,
			spaceBetween: Number( gap.mobile ) || 16,
			speed: Number( settings.speed ) || 500,
			loop: !! settings.loop && slideCount > Math.max( Number( spv.desktop ) || 1, Number( spv.tablet ) || 1 ),
			grabCursor: true,
			watchOverflow: true,
			a11y: {
				prevSlideMessage: i18n.prev || 'Previous slide',
				nextSlideMessage: i18n.next || 'Next slide'
			},
			breakpoints: {}
		};

		options.breakpoints[ bp.tablet ] = {
			slidesPerView: Number( spv.tablet ) || options.slidesPerView,
			spaceBetween: Number( gap.tablet ) || options.spaceBetween
		};

		options.breakpoints[ bp.desktop ] = {
			slidesPerView: Number( spv.desktop ) || options.slidesPerView,
			spaceBetween: Number( gap.desktop ) || options.spaceBetween
		};

		if ( settings.autoplay ) {
			options.autoplay = {
				delay: Number( settings.autoplaySpeed ) || 5000,
				disableOnInteraction: false,
				pauseOnMouseEnter: !! settings.pauseOnHover
			};
		}

		if ( settings.arrows ) {
			options.navigation = {
				nextEl: el.querySelector( '.wdod-ew-testimonials__arrow--next' ),
				prevEl: el.querySelector( '.wdod-ew-testimonials__arrow--prev' )
			};
		}

		if ( settings.dots ) {
			options.pagination = {
				el: el.querySelector( '.swiper-pagination' ),
				clickable: true
			};
		}

		return options;
	}

	/**
	 * Tries to create a Swiper instance through Elementor's utility, then a
	 * global Swiper. Resolves to null when nothing is available.
	 *
	 * @param {HTMLElement} el      Slider element.
	 * @param {Object}      options Swiper options.
	 * @return {Promise<Object|null>} Swiper instance or null.
	 */
	function createSwiper( el, options ) {
		var container = el;

		if ( window.elementorFrontend && elementorFrontend.utils && elementorFrontend.utils.swiper ) {
			try {
				// Elementor's util expects a jQuery object in most versions.
				if ( window.jQuery ) {
					container = window.jQuery( el );
				}

				var result = new elementorFrontend.utils.swiper( container, options );

				// Newer Elementor returns a Promise, older versions the instance.
				if ( result && typeof result.then === 'function' ) {
					return result.then( function ( instance ) {
						return instance || null;
					} ).catch( function () {
						return createGlobalSwiper( el, options );
					} );
				}

				return Promise.resolve( result || null );
			} catch ( e ) {
				// Fall through to the global Swiper.
			}
		}

		return Promise.resolve( createGlobalSwiper( el, options ) );
	}

	/**
	 * Creates a Swiper instance from the global constructor, if present.
	 *
	 * @param {HTMLElement} el      Slider element.
	 * @param {Object}      options Swiper options.
	 * @return {Object|null} Swiper instance or null.
	 */
	function createGlobalSwiper( el, options ) {
		if ( typeof window.Swiper !== 'function' ) {
			return null;
		}

		try {
			return new window.Swiper( el, options );
		} catch ( e ) {
			return null;
		}
	}

	/**
	 * Vanilla fallback: scroll-snap wrapper driven by the arrows and dots.
	 *
	 * @param {HTMLElement} el       Slider element.
	 * @param {Object}      settings Settings.
	 */
	function initFallback( el, settings ) {
		var wrapper = el.querySelector( '.swiper-wrapper' );
		var slides = el.querySelectorAll( '.swiper-slide' );
		var prev = el.querySelector( '.wdod-ew-testimonials__arrow--prev' );
		var next = el.querySelector( '.wdod-ew-testimonials__arrow--next' );
		var pagination = el.querySelector( '.swiper-pagination' );
		var timer = null;
		var bullets = [];

		if ( ! wrapper || ! slides.length ) {
			return;
		}

		el.classList.add( 'wdod-ew-testimonials--fallback' );

		function applyLayout() {
			var spv = Number( deviceValue( settings.slidesPerView, 1 ) ) || 1;
			var gap = Number( deviceValue( settings.spaceBetween, 24 ) );

			el.style.setProperty( '--wdod-ew-spv', String( Math.min( spv, slides.length ) ) );
			el.style.setProperty( '--wdod-ew-gap', ( isNaN( gap ) ? 24 : gap ) + 'px' );
		}

		function slideWidth() {
			return slides[ 0 ].getBoundingClientRect().width + ( parseFloat( getComputedStyle( wrapper ).columnGap ) || 0 );
		}

		function currentIndex() {
			return Math.round( wrapper.scrollLeft / slideWidth() );
		}

		function maxIndex() {
			return Math.max( 0, Math.round( ( wrapper.scrollWidth - wrapper.clientWidth ) / slideWidth() ) );
		}

		function goTo( index ) {
			var max = maxIndex();

			if ( index < 0 ) {
				index = settings.loop ? max : 0;
			} else if ( index > max ) {
				index = settings.loop ? 0 : max;
			}

			wrapper.scrollTo( { left: index * slideWidth(), behavior: 'smooth' } );
		}

		function updateUi() {
			var index = currentIndex();
			var max = maxIndex();

			if ( ! settings.loop ) {
				if ( prev ) {
					prev.disabled = index <= 0;
				}
				if ( next ) {
					next.disabled = index >= max;
				}
			}

			bullets.forEach( function ( bullet, i ) {
				bullet.classList.toggle( 'swiper-pagination-bullet-active', i === index );
				bullet.setAttribute( 'aria-current', i === index ? 'true' : 'false' );
			} );
		}

		function buildDots() {
			if ( ! pagination || ! settings.dots ) {
				return;
			}

			pagination.innerHTML = '';
			bullets = [];

			var count = maxIndex() + 1;

			for ( var i = 0; i < count; i++ ) {
				var bullet = document.createElement( 'button' );

				bullet.type = 'button';
				bullet.className = 'swiper-pagination-bullet';
				bullet.setAttribute( 'aria-label', ( i18n.goTo || 'Go to slide' ) + ' ' + ( i + 1 ) );
				bullet.addEventListener( 'click', goTo.bind( null, i ) );
				pagination.appendChild( bullet );
				bullets.push( bullet );
			}
		}

		function startAutoplay() {
			stopAutoplay();

			if ( ! settings.autoplay ) {
				return;
			}

			timer = window.setInterval( function () {
				goTo( currentIndex() + 1 );
			}, Number( settings.autoplaySpeed ) || 5000 );
		}

		function stopAutoplay() {
			if ( timer ) {
				window.clearInterval( timer );
				timer = null;
			}
		}

		if ( prev ) {
			prev.addEventListener( 'click', function () {
				goTo( currentIndex() - 1 );
			} );
		}

		if ( next ) {
			next.addEventListener( 'click', function () {
				goTo( currentIndex() + 1 );
			} );
		}

		if ( settings.pauseOnHover ) {
			el.addEventListener( 'mouseenter', stopAutoplay );
			el.addEventListener( 'mouseleave', startAutoplay );
		}

		el.addEventListener( 'focusin', stopAutoplay );
		el.addEventListener( 'focusout', startAutoplay );

		var scrollTimeout = null;

		wrapper.addEventListener( 'scroll', function () {
			window.clearTimeout( scrollTimeout );
			scrollTimeout = window.setTimeout( updateUi, 80 );
		}, { passive: true } );

		var resizeTimeout = null;

		window.addEventListener( 'resize', function () {
			window.clearTimeout( resizeTimeout );
			resizeTimeout = window.setTimeout( function () {
				applyLayout();
				buildDots();
				updateUi();
			}, 150 );
		} );

		applyLayout();
		buildDots();
		updateUi();
		startAutoplay();
	}

	/**
	 * Initialises one slider element (idempotent).
	 *
	 * @param {HTMLElement} el Slider element.
	 */
	function initSlider( el ) {
		if ( ! el || el.wdodEwInitialised ) {
			return;
		}

		el.wdodEwInitialised = true;

		var settings = readSettings( el );
		var options = buildSwiperOptions( el, settings );

		createSwiper( el, options ).then( function ( instance ) {
			if ( instance ) {
				el.wdodEwSwiper = instance;
				return;
			}

			initFallback( el, settings );
		} ).catch( function () {
			initFallback( el, settings );
		} );
	}

	/**
	 * Initialises every slider inside a root element.
	 *
	 * @param {Document|HTMLElement} root Root element.
	 */
	function initAll( root ) {
		var nodes = ( root || document ).querySelectorAll( SELECTOR );

		Array.prototype.forEach.call( nodes, initSlider );
	}

	/**
	 * Destroys a slider instance (used when the editor re-renders a widget).
	 *
	 * @param {HTMLElement} el Slider element.
	 */
	function destroySlider( el ) {
		if ( el && el.wdodEwSwiper && typeof el.wdodEwSwiper.destroy === 'function' ) {
			try {
				el.wdodEwSwiper.destroy( true, true );
			} catch ( e ) {
				// Ignore.
			}
		}
	}

	// Elementor: hook into the widget lifecycle so the editor preview works.
	function registerElementorHandler() {
		if ( ! window.elementorFrontend || ! elementorFrontend.hooks ) {
			return false;
		}

		elementorFrontend.hooks.addAction( 'frontend/element_ready/wdod-testimonial-slider.default', function ( $scope ) {
			var scope = $scope && $scope[ 0 ] ? $scope[ 0 ] : $scope;

			if ( ! scope || ! scope.querySelectorAll ) {
				return;
			}

			Array.prototype.forEach.call( scope.querySelectorAll( SELECTOR ), function ( el ) {
				destroySlider( el );
				el.wdodEwInitialised = false;
				initSlider( el );
			} );
		} );

		return true;
	}

	function boot() {
		if ( ! registerElementorHandler() ) {
			window.addEventListener( 'elementor/frontend/init', function () {
				registerElementorHandler();
			} );
		}

		// Sliders rendered by shortcodes (outside Elementor) or on pages where
		// Elementor is not active.
		initAll( document );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}

	window.wdodEwInitSliders = initAll;
} )();
