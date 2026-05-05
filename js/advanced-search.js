/**
 * ThessNest — Advanced Search Frontend Controller
 *
 * Handles:
 *  - Dynamic filter changes (taxonomy dropdowns, amenity checkboxes)
 *  - noUiSlider price range
 *  - Date pickers (Flatpickr)
 *  - Radius search via Leaflet map click
 *  - AJAX requests to thessnest_live_search
 *  - URL pushState for shareable search links
 *  - Skeleton loading animation
 *
 * @package ThessNest
 */

(function () {
  'use strict';

  if (typeof thessnestSearch === 'undefined') return;

  const AJAX_URL = thessnestSearch.ajaxurl;
  const NONCE    = thessnestSearch.nonce;
  const DEFAULTS = thessnestSearch.defaults || {};

  // DOM refs
  const form        = document.getElementById('advanced-search-form');
  const resultsWrap = document.getElementById('search-results');
  const countEl     = document.getElementById('search-results-count');
  const sortSelect  = document.getElementById('search-sort');
  const loadMoreBtn = document.getElementById('search-load-more');

  if (!form || !resultsWrap) return;

  let currentPage  = 1;
  let totalPages   = 1;
  let debounceTimer = null;

  /* ─────────────────────────────────────────────────
     1. noUiSlider Price Range
     ───────────────────────────────────────────────── */
  const sliderEl = document.getElementById('price-slider');
  if (sliderEl && typeof noUiSlider !== 'undefined') {
    const minPrice = parseInt(DEFAULTS.price_min || 100);
    const maxPrice = parseInt(DEFAULTS.price_max || 5000);
    const step     = parseInt(DEFAULTS.price_step || 50);

    noUiSlider.create(sliderEl, {
      start: [minPrice, maxPrice],
      connect: true,
      step: step,
      range: { min: minPrice, max: maxPrice },
      format: {
        to: function (v) { return Math.round(v); },
        from: function (v) { return Number(v); }
      }
    });

    const minLabel = document.getElementById('price-min-label');
    const maxLabel = document.getElementById('price-max-label');

    sliderEl.noUiSlider.on('update', function (values) {
      if (minLabel) minLabel.textContent = DEFAULTS.currency + values[0];
      if (maxLabel) maxLabel.textContent = DEFAULTS.currency + values[1];
    });

    sliderEl.noUiSlider.on('change', function () {
      debouncedSearch();
    });
  }

  /* ─────────────────────────────────────────────────
     2. Flatpickr Date Pickers
     ───────────────────────────────────────────────── */
  const checkinInput  = document.getElementById('search-checkin');
  const checkoutInput = document.getElementById('search-checkout');

  if (checkinInput && typeof flatpickr !== 'undefined') {
    flatpickr(checkinInput, {
      dateFormat: 'Y-m-d',
      minDate: 'today',
      onChange: function (dates, dateStr) {
        if (checkoutInput && checkoutInput._flatpickr) {
          checkoutInput._flatpickr.set('minDate', dateStr);
        }
      }
    });
  }
  if (checkoutInput && typeof flatpickr !== 'undefined') {
    flatpickr(checkoutInput, {
      dateFormat: 'Y-m-d',
      minDate: 'today'
    });
  }

  /* ─────────────────────────────────────────────────
     3. Event Listeners (filters)
     ───────────────────────────────────────────────── */
  // Taxonomy selects & checkboxes
  form.addEventListener('change', function (e) {
    const tag = e.target.tagName;
    if (tag === 'SELECT' || tag === 'INPUT') {
      debouncedSearch();
    }
  });

  // Sort
  if (sortSelect) {
    sortSelect.addEventListener('change', function () {
      currentPage = 1;
      runSearch(false);
    });
  }

  // Load more
  if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', function () {
      currentPage++;
      runSearch(true);
    });
  }

  // Date fields
  if (checkinInput) checkinInput.addEventListener('change', function () { debouncedSearch(); });
  if (checkoutInput) checkoutInput.addEventListener('change', function () { debouncedSearch(); });

  /* ─────────────────────────────────────────────────
     4. Collect Filters
     ───────────────────────────────────────────────── */
  function getFilters() {
    var data = {
      action:   'thessnest_live_search',
      security: NONCE,
      page:     currentPage,
      per_page: parseInt(DEFAULTS.per_page || 12),
    };

    // Taxonomy selects
    var neighborhood = form.querySelector('[name="neighborhood"]');
    if (neighborhood && neighborhood.value) data.neighborhood = neighborhood.value;

    var targetGroup = form.querySelector('[name="target_group"]');
    if (targetGroup && targetGroup.value) data.target_group = targetGroup.value;

    // Amenity checkboxes
    var amenityChecks = form.querySelectorAll('input[name="amenities[]"]:checked');
    if (amenityChecks.length) {
      data.amenities = Array.from(amenityChecks).map(function (cb) { return cb.value; });
    }

    // Price slider
    if (sliderEl && sliderEl.noUiSlider) {
      var prices = sliderEl.noUiSlider.get();
      data.price_min = prices[0];
      data.price_max = prices[1];
    }

    // Dates
    if (checkinInput && checkinInput.value) data.checkin = checkinInput.value;
    if (checkoutInput && checkoutInput.value) data.checkout = checkoutInput.value;

    // Guests
    var guestsInput = form.querySelector('[name="guests"]');
    if (guestsInput && guestsInput.value) data.guests = guestsInput.value;

    // Instant Book
    var instantCb = form.querySelector('[name="instant_book"]');
    if (instantCb && instantCb.checked) data.instant_book = '1';

    // WiFi
    var wifiInput = form.querySelector('[name="wifi_min"]');
    if (wifiInput && wifiInput.value) data.wifi_min = wifiInput.value;

    // Sort
    if (sortSelect) data.sort = sortSelect.value;

    // Radius (from hidden fields set by map click)
    var latInput = form.querySelector('[name="lat"]');
    var lngInput = form.querySelector('[name="lng"]');
    var radiusInput = form.querySelector('[name="radius"]');
    if (latInput && latInput.value) data.lat = latInput.value;
    if (lngInput && lngInput.value) data.lng = lngInput.value;
    if (radiusInput && radiusInput.value) data.radius = radiusInput.value;

    return data;
  }

  /* ─────────────────────────────────────────────────
     5. Run Search (AJAX)
     ───────────────────────────────────────────────── */
  function runSearch(append) {
    if (!append) {
      showSkeleton();
    }

    var filters = getFilters();

    var formData = new FormData();
    Object.keys(filters).forEach(function (key) {
      var val = filters[key];
      if (Array.isArray(val)) {
        val.forEach(function (v) { formData.append(key, v); });
      } else {
        formData.append(key, val);
      }
    });

    fetch(AJAX_URL, { method: 'POST', credentials: 'same-origin', body: formData })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (!data.success) return;

        totalPages = data.data.total_pages || 1;

        if (countEl) {
          countEl.textContent = data.data.total + ' ' + (data.data.total === 1 ? 'result' : 'results');
        }

        // Build cards HTML from JSON (or use server-rendered HTML)
        var html = '';
        if (data.data.results && data.data.results.length) {
          data.data.results.forEach(function (item) {
            html += buildCardHTML(item);
          });
        } else if (!append) {
          html = '<div class="search-empty"><p>' + (thessnestSearch.emptyMsg || 'No properties found.') + '</p></div>';
        }

        if (append) {
          resultsWrap.insertAdjacentHTML('beforeend', html);
        } else {
          resultsWrap.innerHTML = html;
        }

        // Load more button visibility
        if (loadMoreBtn) {
          loadMoreBtn.style.display = currentPage < totalPages ? '' : 'none';
        }

        // Push URL state
        pushFiltersToURL(filters);

        // Re-init swiper on new cards
        initCardSwipers();
      })
      .catch(function (err) {
        console.error('Search error:', err);
        resultsWrap.innerHTML = '<div class="search-empty"><p>Something went wrong. Please try again.</p></div>';
      });
  }

  /* ─────────────────────────────────────────────────
     6. Debounce
     ───────────────────────────────────────────────── */
  function debouncedSearch() {
    currentPage = 1;
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(function () { runSearch(false); }, 350);
  }

  /* ─────────────────────────────────────────────────
     7. Skeleton Loading
     ───────────────────────────────────────────────── */
  function showSkeleton() {
    var skeletons = '';
    for (var i = 0; i < 6; i++) {
      skeletons += '<div class="property-card skeleton-card"><div class="skeleton-img"></div><div class="skeleton-body"><div class="skeleton-line w80"></div><div class="skeleton-line w60"></div><div class="skeleton-line w40"></div></div></div>';
    }
    resultsWrap.innerHTML = skeletons;
  }

  /* ─────────────────────────────────────────────────
     8. URL pushState
     ───────────────────────────────────────────────── */
  function pushFiltersToURL(filters) {
    var params = new URLSearchParams();
    var skip = ['action', 'security', 'per_page'];

    Object.keys(filters).forEach(function (key) {
      if (skip.indexOf(key) !== -1) return;
      var val = filters[key];
      if (val && val !== '' && val !== '0' && val !== 0) {
        if (Array.isArray(val)) {
          val.forEach(function (v) { params.append(key, v); });
        } else {
          params.set(key, val);
        }
      }
    });

    var qs = params.toString();
    var newUrl = window.location.pathname + (qs ? '?' + qs : '');
    history.pushState(null, '', newUrl);
  }

  /* ─────────────────────────────────────────────────
     9. Build Card HTML (client-side from JSON)
     ───────────────────────────────────────────────── */
  function buildCardHTML(item) {
    var currency = DEFAULTS.currency || '€';
    var img = item.thumbnail || 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=640&h=480&fit=crop&q=80';

    return '<article class="property-card">' +
      '<div class="card-carousel" style="position:relative;">' +
        '<a href="' + item.url + '"><img src="' + img + '" alt="' + item.title + '" loading="lazy" width="640" height="480" style="width:100%;height:240px;object-fit:cover;border-radius:var(--radius-lg) var(--radius-lg) 0 0;"></a>' +
      '</div>' +
      '<div class="card-body">' +
        '<h3 class="card-title"><a href="' + item.url + '">' + item.title + '</a></h3>' +
        '<div class="card-pricing"><span class="price-main">' + currency + item.rent + '</span></div>' +
      '</div>' +
    '</article>';
  }

  /* ─────────────────────────────────────────────────
     10. Re-init Swiper on dynamic cards
     ───────────────────────────────────────────────── */
  function initCardSwipers() {
    if (typeof Swiper === 'undefined') return;
    document.querySelectorAll('.property-card .property-swiper:not(.swiper-initialized)').forEach(function (el) {
      new Swiper(el, {
        slidesPerView: 1,
        pagination: { el: el.querySelector('.swiper-pagination'), clickable: true },
        loop: false,
        grabCursor: true,
      });
    });
  }

  /* ─────────────────────────────────────────────────
     11. Init: load URL params into form & run
     ───────────────────────────────────────────────── */
  function loadFromURL() {
    var params = new URLSearchParams(window.location.search);

    params.forEach(function (val, key) {
      var input = form.querySelector('[name="' + key + '"]');
      if (input) {
        if (input.type === 'checkbox') {
          input.checked = val === '1';
        } else {
          input.value = val;
        }
      }
    });

    // Set slider from URL
    if (sliderEl && sliderEl.noUiSlider) {
      var urlMin = params.get('price_min');
      var urlMax = params.get('price_max');
      if (urlMin && urlMax) {
        sliderEl.noUiSlider.set([parseInt(urlMin), parseInt(urlMax)]);
      }
    }
  }

  // ── Boot ──
  loadFromURL();
  runSearch(false);

  // Handle back/forward navigation
  window.addEventListener('popstate', function () {
    loadFromURL();
    runSearch(false);
  });

})();
