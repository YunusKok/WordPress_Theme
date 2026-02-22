/**
 * ThessNest — Interactive JS (Vanilla)
 *
 * Handles:
 * - Mobile Navigation Drawer
 * - Mobile Filter Sidebar (Archive)
 * - Save Property Button (UI visual toggle)
 * - Swiper Initialization
 */

document.addEventListener('DOMContentLoaded', () => {

	/* ----------------------------------------------------
	 * 1. Mobile Navigation Drawer
	 * ---------------------------------------------------- */
	const menuToggle = document.getElementById('mobile-menu-toggle');
	const navDrawer  = document.getElementById('mobile-nav-drawer');
	const navClose   = document.getElementById('mobile-nav-close');
	const navOverlay = document.getElementById('mobile-nav-overlay');

	const toggleNav = (open) => {
		if (open) {
			navDrawer.classList.add('is-open');
			navOverlay.classList.add('is-active');
			menuToggle.setAttribute('aria-expanded', 'true');
			document.body.style.overflow = 'hidden'; // Prevent scrolling
		} else {
			navDrawer.classList.remove('is-open');
			navOverlay.classList.remove('is-active');
			menuToggle.setAttribute('aria-expanded', 'false');
			document.body.style.overflow = '';
		}
	};

	if (menuToggle && navDrawer && navOverlay && navClose) {
		menuToggle.addEventListener('click', () => toggleNav(true));
		navClose.addEventListener('click', () => toggleNav(false));
		navOverlay.addEventListener('click', () => toggleNav(false));
	}

	/* ----------------------------------------------------
	 * 2. Mobile Filter Sidebar (Archive Page)
	 * ---------------------------------------------------- */
	const filterToggle = document.getElementById('mobile-filter-toggle');
	const filterSidebar = document.getElementById('filter-sidebar');

	if (filterToggle && filterSidebar) {
		// Create a close button dynamically for the mobile view
		const closeFilterBtn = document.createElement('button');
		closeFilterBtn.className = 'mobile-filter-close';
		closeFilterBtn.innerHTML = '✕ Close Filters';
		closeFilterBtn.style.cssText = 'display:none; width:100%; margin-bottom: 20px; padding: 12px; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-lg); font-weight: 600; cursor: pointer;';
		
		filterSidebar.insertBefore(closeFilterBtn, filterSidebar.firstChild);

		const toggleFilters = (open) => {
			if (open) {
				filterSidebar.classList.add('mobile-open');
				filterToggle.setAttribute('aria-expanded', 'true');
				closeFilterBtn.style.display = 'block';
				document.body.style.overflow = 'hidden';
			} else {
				filterSidebar.classList.remove('mobile-open');
				filterToggle.setAttribute('aria-expanded', 'false');
				closeFilterBtn.style.display = 'none';
				document.body.style.overflow = '';
			}
		};

		filterToggle.addEventListener('click', () => toggleFilters(true));
		closeFilterBtn.addEventListener('click', () => toggleFilters(false));
	}

	/* ----------------------------------------------------
	 * 3. Save Property Button (UI Toggle)
	 * ---------------------------------------------------- */
	const saveBtns = document.querySelectorAll('.card-save-btn');
	saveBtns.forEach(btn => {
		btn.addEventListener('click', (e) => {
			e.preventDefault();
			btn.classList.toggle('saved');
			
			// Optional: Trigger AJAX save functionality here
			// const propertyId = btn.getAttribute('data-property-id');
		});
	});

	/* ----------------------------------------------------
	 * 4. Init Swiper for Property Cards / Heroes
	 * ---------------------------------------------------- */
	if (typeof Swiper !== 'undefined') {
		// Initialize all property card carousels
		document.querySelectorAll('.property-swiper').forEach(swiperEl => {
			new Swiper(swiperEl, {
				loop: true,
				pagination: {
					el: '.swiper-pagination',
					clickable: true,
				},
				navigation: false,
				grabCursor: true,
				effect: 'fade', // Optional: 'fade' for smoother transition or default 'slide'
				fadeEffect: {
					crossFade: true
				},
			});
		});

		// Initialize hero carousel if present
		const heroSwiper = document.querySelector('.hero-swiper');
		if (heroSwiper) {
			new Swiper(heroSwiper, {
				loop: true,
				autoplay: {
					delay: 5000,
					disableOnInteraction: false,
				},
				effect: 'fade',
				fadeEffect: { crossFade: true },
			});
		}
	}
	/* ----------------------------------------------------
	 * 5. Dynamic Island Header on Scroll
	 * ---------------------------------------------------- */
	const siteHeader = document.getElementById('site-header');
	if (siteHeader) {
		const handleScroll = () => {
			if (window.scrollY > 50) {
				siteHeader.classList.add('scrolled');
			} else {
				siteHeader.classList.remove('scrolled');
			}
		};
		// Initial check
		handleScroll();
		window.addEventListener('scroll', handleScroll, { passive: true });
	}

});
