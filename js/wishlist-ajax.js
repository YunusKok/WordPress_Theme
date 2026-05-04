/**
 * ThessNest — Wishlist AJAX Handler
 *
 * Listens for heart-button clicks on property cards,
 * sends toggle requests to admin-ajax.php, and
 * updates the UI with smooth animations.
 *
 * @package ThessNest
 */

(function () {
  'use strict';

  // Bail early if the localized object is missing.
  if (typeof thessnestWishlist === 'undefined') return;

  const AJAX_URL  = thessnestWishlist.ajaxurl;
  const NONCE     = thessnestWishlist.nonce;
  const LOGGED_IN = thessnestWishlist.loggedIn === '1';

  /**
   * Initialize — attach click listeners to all save buttons.
   */
  function init() {
    document.addEventListener('click', function (e) {
      const btn = e.target.closest('.card-save-btn');
      if (!btn) return;

      e.preventDefault();
      e.stopPropagation();

      // Require login
      if (!LOGGED_IN) {
        // Try to open login modal if it exists
        const loginTrigger = document.querySelector('[data-modal-open="modal-login"]');
        if (loginTrigger) {
          loginTrigger.click();
        } else {
          alert(thessnestWishlist.loginMsg || 'Please sign in to save properties.');
        }
        return;
      }

      // Prevent double-click spam
      if (btn.classList.contains('is-loading')) return;

      const propertyId = btn.getAttribute('data-property-id');
      if (!propertyId) return;

      toggleFavorite(btn, propertyId);
    });
  }

  /**
   * Send AJAX toggle request and update the button state.
   *
   * @param {HTMLElement} btn        The heart button element.
   * @param {string}      propertyId The property post ID.
   */
  function toggleFavorite(btn, propertyId) {
    btn.classList.add('is-loading');

    const formData = new FormData();
    formData.append('action', 'thessnest_toggle_favorite');
    formData.append('security', NONCE);
    formData.append('property_id', propertyId);

    fetch(AJAX_URL, {
      method: 'POST',
      credentials: 'same-origin',
      body: formData,
    })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        btn.classList.remove('is-loading');

        if (data.success) {
          if (data.data.is_saved) {
            btn.classList.add('saved');
            animateHeart(btn);
          } else {
            btn.classList.remove('saved');
          }
        }
      })
      .catch(function () {
        btn.classList.remove('is-loading');
      });
  }

  /**
   * Trigger a scale-bounce animation on the heart icon.
   *
   * @param {HTMLElement} btn The heart button.
   */
  function animateHeart(btn) {
    btn.classList.add('heart-pop');
    btn.addEventListener('animationend', function handler() {
      btn.classList.remove('heart-pop');
      btn.removeEventListener('animationend', handler);
    });
  }

  // Boot on DOMContentLoaded
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
