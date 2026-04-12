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
			
			if (typeof thessnestAjax === 'undefined' || thessnestAjax.loggedIn === '0') {
				alert('Please sign in to save properties.');
				return;
			}

			const propertyId = btn.getAttribute('data-property-id');
			
			// Optimistic UI update
			btn.classList.toggle('saved');
			
			const formData = new FormData();
			formData.append('action', 'thessnest_toggle_favorite');
			formData.append('security', thessnestAjax.nonce);
			formData.append('property_id', propertyId);

			fetch(thessnestAjax.ajaxurl, {
				method: 'POST',
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				if (!data.success) {
					btn.classList.toggle('saved'); // Revert
					alert(data.data?.message || 'Error saving property.');
				}
			})
			.catch(error => {
				console.error(error);
				btn.classList.toggle('saved'); // Revert
			});
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

	/* ----------------------------------------------------
	 * 6. Property Inquiry Form
	 * ---------------------------------------------------- */
	const inquiryForm = document.getElementById('property-inquiry-form');
	if (inquiryForm) {
		inquiryForm.addEventListener('submit', function(e) {
			e.preventDefault();
			const submitBtn = inquiryForm.querySelector('button[type="submit"]');
			const responseDiv = document.getElementById('inquiry-form-response');
			
			submitBtn.disabled = true;
			submitBtn.innerHTML = 'Sending...';
			responseDiv.style.display = 'none';

			const formData = new FormData(inquiryForm);

			fetch(thessnestAjax.ajaxurl, {
				method: 'POST',
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				responseDiv.style.display = 'block';
				responseDiv.innerHTML = data.data.message;
				if (data.success) {
					responseDiv.style.color = '#38a169'; // Green success
					inquiryForm.reset();
				} else {
					responseDiv.style.color = '#e53e3e'; // Red error
				}
			})
			.catch(error => {
				responseDiv.style.display = 'block';
				responseDiv.style.color = '#e53e3e';
				responseDiv.innerHTML = 'An unexpected error occurred.';
			})
			.finally(() => {
				submitBtn.disabled = false;
				submitBtn.innerHTML = 'Send Message';
			});
		});
	}

	/* ----------------------------------------------------
	 * 7. AJAX Property Filter
	 * ---------------------------------------------------- */
	const filterForm = document.getElementById('property-filter-form');
	const propertyGrid = document.querySelector('.property-grid');
	const archiveCount = document.querySelector('.archive-count');

	if (filterForm && propertyGrid) {
		filterForm.addEventListener('submit', function(e) {
			e.preventDefault();
			const submitBtn = filterForm.querySelector('.filter-btn');
			const originalText = submitBtn.innerHTML;
			
			submitBtn.disabled = true;
			submitBtn.innerHTML = 'Filtering...';
			propertyGrid.style.opacity = '0.5';

			const formData = new FormData(filterForm);
			formData.append('action', 'thessnest_filter_properties');
			formData.append('security', thessnestAjax.nonce);

			fetch(thessnestAjax.ajaxurl, {
				method: 'POST',
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					if (data.data.html) {
						propertyGrid.innerHTML = data.data.html;
					} else {
						propertyGrid.innerHTML = '<div class="text-center" style="grid-column: 1 / -1; padding:var(--space-16);"><h2 style="font-size:var(--font-size-xl);margin-bottom:var(--space-2);">No properties found</h2><p class="text-muted">Try adjusting your filters or check back later for new listings.</p></div>';
					}
					
					if (archiveCount) {
						archiveCount.innerHTML = data.data.count + ' property found' + (data.data.count !== 1 ? 's' : '');
					}

					// Reinitialize tooltips/carousels for newly added HTML
					if (typeof Swiper !== 'undefined') {
						propertyGrid.querySelectorAll('.property-swiper').forEach(swiperEl => {
							new Swiper(swiperEl, {
								loop: true,
								pagination: { el: '.swiper-pagination', clickable: true },
								effect: 'fade', fadeEffect: { crossFade: true }
							});
						});
					}

					// Re-render map markers with new data
					if (window.renderMarkers) {
						window.renderMarkers(data.data.markers);
					}
				}
			})
			.catch(error => console.error(error))
			.finally(() => {
				submitBtn.disabled = false;
				submitBtn.innerHTML = originalText;
				propertyGrid.style.opacity = '1';
			});
		});
	}

	/* ----------------------------------------------------
	 * 8. Add Listing (Property Submission) Form
	 * ---------------------------------------------------- */
	const listingForm = document.getElementById('add-listing-form');
	if (listingForm) {
		listingForm.addEventListener('submit', function(e) {
			e.preventDefault();
			const submitBtn = document.getElementById('listing-submit-btn');
			const responseDiv = document.getElementById('listing-form-response');
			
			submitBtn.disabled = true;
			submitBtn.innerHTML = 'Uploading... Please wait.';
			responseDiv.style.display = 'none';

			const formData = new FormData(listingForm);

			fetch(thessnestAjax.ajaxurl, {
				method: 'POST',
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				responseDiv.style.display = 'block';
				responseDiv.innerHTML = data.data.message;
				if (data.success) {
					responseDiv.style.backgroundColor = 'rgba(56, 161, 105, 0.1)';
					responseDiv.style.color = '#38a169'; // Green success
					responseDiv.style.border = '1px solid #38a169';
					listingForm.reset();
					// Redirect to dashboard after 2 seconds
					setTimeout(() => {
						window.location.href = data.data.redirect || '/dashboard/';
					}, 2000);
				} else {
					responseDiv.style.backgroundColor = 'rgba(229, 62, 62, 0.1)';
					responseDiv.style.color = '#e53e3e'; // Red error
					responseDiv.style.border = '1px solid #e53e3e';
				}
			})
			.catch(error => {
				console.error(error);
				responseDiv.style.display = 'block';
				responseDiv.style.backgroundColor = 'rgba(229, 62, 62, 0.1)';
				responseDiv.style.color = '#e53e3e';
				responseDiv.style.border = '1px solid #e53e3e';
				responseDiv.innerHTML = 'An unexpected error occurred during upload.';
			})
			.finally(() => {
				submitBtn.disabled = false;
				submitBtn.innerHTML = 'Submit Listing';
			});
		});
	}

	/* ----------------------------------------------------
	 * 9. Dashboard Profile Update
	 * ---------------------------------------------------- */
	const profileForm = document.getElementById('dashboard-profile-form');
	if (profileForm) {
		profileForm.addEventListener('submit', function(e) {
			e.preventDefault();
			const submitBtn = document.getElementById('profile-submit-btn');
			const responseDiv = document.getElementById('profile-response');
			
			submitBtn.disabled = true;
			submitBtn.innerHTML = 'Saving...';
			responseDiv.style.display = 'none';

			const formData = new FormData(profileForm);

			fetch(thessnestAjax.ajaxurl, {
				method: 'POST',
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				responseDiv.style.display = 'block';
				responseDiv.innerHTML = data.data.message;
				if (data.success) {
					responseDiv.style.backgroundColor = 'rgba(56, 161, 105, 0.1)';
					responseDiv.style.color = '#38a169'; // Green success
					responseDiv.style.border = '1px solid #38a169';
				} else {
					responseDiv.style.backgroundColor = 'rgba(229, 62, 62, 0.1)';
					responseDiv.style.color = '#e53e3e'; // Red error
					responseDiv.style.border = '1px solid #e53e3e';
				}
			})
			.catch(error => {
				console.error(error);
				responseDiv.style.display = 'block';
				responseDiv.style.backgroundColor = 'rgba(229, 62, 62, 0.1)';
				responseDiv.style.color = '#e53e3e';
				responseDiv.style.border = '1px solid #e53e3e';
				responseDiv.innerHTML = 'An unexpected error occurred.';
			})
			.finally(() => {
				submitBtn.disabled = false;
				submitBtn.innerHTML = 'Save Changes';
			});
		});
	}

	/* ----------------------------------------------------
	 * 10. Dashboard KYC Document Upload
	 * ---------------------------------------------------- */
	const kycForm = document.getElementById('kyc-upload-form');
	if (kycForm) {
		kycForm.addEventListener('submit', function(e) {
			e.preventDefault();
			const submitBtn = document.getElementById('kyc-submit-btn');
			const responseDiv = document.getElementById('kyc-response');
			
			submitBtn.disabled = true;
			submitBtn.innerHTML = 'Uploading...';
			responseDiv.style.display = 'none';

			const formData = new FormData(kycForm);
			formData.append('action', 'thessnest_submit_kyc');
			// Re-using the dashboard nonce
			formData.append('security', document.getElementById('dashboard_nonce') ? document.getElementById('dashboard_nonce').value : thessnestAjax.nonce);

			fetch(thessnestAjax.ajaxurl, {
				method: 'POST',
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				responseDiv.style.display = 'block';
				responseDiv.innerHTML = data.data.message;
				if (data.success) {
					responseDiv.style.backgroundColor = 'rgba(56, 161, 105, 0.1)';
					responseDiv.style.color = '#38a169';
					responseDiv.style.border = '1px solid #38a169';
					kycForm.reset();
					
					// Optional: Reload the page to show the "Pending" state UI
					setTimeout(() => {
						window.location.reload();
					}, 2000);
				} else {
					responseDiv.style.backgroundColor = 'rgba(229, 62, 62, 0.1)';
					responseDiv.style.color = '#e53e3e';
					responseDiv.style.border = '1px solid #e53e3e';
				}
			})
			.catch(error => {
				console.error(error);
				responseDiv.style.display = 'block';
				responseDiv.style.backgroundColor = 'rgba(229, 62, 62, 0.1)';
				responseDiv.style.color = '#e53e3e';
				responseDiv.style.border = '1px solid #e53e3e';
				responseDiv.innerHTML = 'An unexpected error occurred during upload.';
			})
			.finally(() => {
				submitBtn.disabled = false;
				submitBtn.innerHTML = 'Submit for Verification';
			});
		});
	}

	/* ----------------------------------------------------
	 * 10a. Dashboard 'Pay to Publish' (Monetization Paywall)
	 * ---------------------------------------------------- */
	const payPublishBtns = document.querySelectorAll('.btn-pay-publish');
	payPublishBtns.forEach(btn => {
		btn.addEventListener('click', function(e) {
			e.preventDefault();

			const propertyId = this.dataset.propertyId;
			const nonce = this.dataset.nonce;
			const originalHtml = this.innerHTML;

			this.disabled = true;
			this.innerHTML = '<?xml version="1.0" encoding="utf-8"?><svg width="20px" height="20px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" style="display:inline-block; vertical-align:middle; background:transparent;"><circle cx="50" cy="50" fill="none" stroke="#ffffff" stroke-width="8" r="35" stroke-dasharray="164.93361431346415 56.97787143782138"><animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"></animateTransform></circle></svg>';

			const formData = new FormData();
			formData.append('action', 'thessnest_pay_to_publish');
			formData.append('security', nonce);
			formData.append('property_id', propertyId);

			fetch(thessnestAjax.ajaxurl, {
				method: 'POST',
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				if (data.success && data.data.redirect) {
					window.location.href = data.data.redirect;
				} else if (data.success) {
					// Free publish
					window.location.reload();
				} else {
					alert(data.data.message || 'Error processing request.');
					this.disabled = false;
					this.innerHTML = originalHtml;
				}
			})
			.catch(error => {
				console.error(error);
				alert('An unexpected error occurred.');
				this.disabled = false;
				this.innerHTML = originalHtml;
			});
		});
	});

	/* ----------------------------------------------------
	 * 10. Dashboard specific Property Delete (Trash)
	 * ---------------------------------------------------- */
	const deleteBtns = document.querySelectorAll('.btn-delete-property');
	deleteBtns.forEach(btn => {
		btn.addEventListener('click', function(e) {
			e.preventDefault();

			if (!confirm('Are you sure you want to delete this property? You can still recover it from the Admin Panel later if needed.')) {
				return;
			}

			const propertyId = this.dataset.propertyId;
			const nonce = this.dataset.nonce;
			const propertyItem = document.getElementById('my-property-' + propertyId);
			const originalHtml = this.innerHTML;

			this.disabled = true;
			this.innerHTML = '...';

			const formData = new FormData();
			formData.append('action', 'thessnest_delete_property');
			formData.append('security', nonce);
			formData.append('property_id', propertyId);

			fetch(thessnestAjax.ajaxurl, {
				method: 'POST',
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					// Remove the property item from DOM with a fade out effect
					propertyItem.style.transition = 'opacity 0.4s ease';
					propertyItem.style.opacity = '0';
					setTimeout(() => {
						propertyItem.remove();
						
						// If no more items, show the empty message
						const listContainer = document.querySelector('.dashboard-properties-list');
						if (listContainer && listContainer.children.length === 0) {
							listContainer.innerHTML = '<p style="color:var(--color-text-muted); padding:var(--space-8); text-align:center; background:var(--color-surface); border-radius:var(--radius-lg); border:1px dashed var(--color-border);">You have no properties left.</p>';
						}
					}, 400);

				} else {
					alert(data.data.message || 'Error deleting property.');
					this.disabled = false;
					this.innerHTML = originalHtml;
				}
			})
			.catch(error => {
				console.error(error);
				alert('An unexpected error occurred.');
				this.innerHTML = originalHtml;
			});
		});
	});

	/* ----------------------------------------------------
	 * 11. Dashboard Inbox (Messaging)
	 * ---------------------------------------------------- */
	const threadItems = document.querySelectorAll('.thread-item');
	const chatHeader = document.getElementById('chat-header');
	const chatTitle = document.getElementById('chat-property-title');
	const chatMessages = document.getElementById('chat-messages');
	const chatReplyFormContainer = document.getElementById('chat-reply-form');
	const replyForm = document.getElementById('reply-form');
	const replyMessageInput = document.getElementById('reply-message');
	const replyBtn = document.getElementById('reply-btn');
	
	let currentRecipientId = null;
	let currentPropertyId = null;

	if (threadItems.length > 0 && chatMessages) {
		threadItems.forEach(item => {
			item.addEventListener('click', function() {
				// Style active state
				threadItems.forEach(i => i.classList.remove('active'));
				this.classList.add('active');
				this.style.background = 'var(--color-background)';

				currentRecipientId = this.dataset.otherUser;
				currentPropertyId = this.dataset.property;

				// UI Loading state
				chatHeader.style.display = 'block';
				chatTitle.textContent = 'Loading conversation...';
				chatMessages.innerHTML = '<div style="margin:auto; color:var(--color-text-muted);">Fetching messages...</div>';
				chatReplyFormContainer.style.display = 'none';

				const formData = new FormData();
				formData.append('action', 'thessnest_fetch_messages');
				formData.append('security', document.getElementById('inbox_security').value);
				formData.append('other_user_id', currentRecipientId);
				formData.append('property_id', currentPropertyId);

				fetch(thessnestAjax.ajaxurl, {
					method: 'POST',
					body: formData
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						chatTitle.textContent = data.data.property_title;
						chatMessages.innerHTML = '';
						
						if (data.data.messages.length > 0) {
							data.data.messages.forEach(msg => {
								appendMessageToDOM(msg);
							});
						} else {
							chatMessages.innerHTML = '<div style="margin:auto; color:var(--color-text-muted);">No messages found.</div>';
						}

						// Scroll to bottom
						chatMessages.scrollTop = chatMessages.scrollHeight;
						chatReplyFormContainer.style.display = 'block';
						
					} else {
						chatMessages.innerHTML = `<div style="margin:auto; color:#e53e3e;">${data.data.message || 'Error loading thread.'}</div>`;
					}
				})
				.catch(error => {
					console.error(error);
					chatMessages.innerHTML = '<div style="margin:auto; color:#e53e3e;">An unexpected error occurred.</div>';
				});
			});
		});

		// Handle Form Submission for Replies
		if (replyForm) {
			replyForm.addEventListener('submit', function(e) {
				e.preventDefault();

				if (!currentRecipientId || !currentPropertyId) return;

				const messageText = replyMessageInput.value.trim();
				if (!messageText) return;

				replyBtn.disabled = true;
				replyMessageInput.disabled = true;

				const formData = new FormData();
				formData.append('action', 'thessnest_send_message');
				formData.append('security', document.getElementById('inbox_security').value);
				formData.append('recipient_id', currentRecipientId);
				formData.append('property_id', currentPropertyId);
				formData.append('message', messageText);

				fetch(thessnestAjax.ajaxurl, {
					method: 'POST',
					body: formData
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						appendMessageToDOM(data.data.new_message);
						replyMessageInput.value = '';
						chatMessages.scrollTop = chatMessages.scrollHeight;
					} else {
						alert(data.data.message || 'Failed to send message.');
					}
				})
				.catch(error => {
					console.error(error);
					alert('An unexpected error occurred while sending the message.');
				})
				.finally(() => {
					replyBtn.disabled = false;
					replyMessageInput.disabled = false;
					replyMessageInput.focus();
				});
			});
		}

		// Helper to abstract message rendering
		function appendMessageToDOM(msg) {
			const wrapper = document.createElement('div');
			wrapper.style.display = 'flex';
			wrapper.style.flexDirection = 'column';
			wrapper.style.marginBottom = 'var(--space-2)';

			const bubbleClasses = msg.is_mine ? 'msg-bubble msg-mine' : 'msg-bubble msg-theirs';
			
			wrapper.innerHTML = `
				<div class="${bubbleClasses}">
					${msg.content}
				</div>
				<span style="font-size:10px; color:var(--color-text-muted); margin-top:4px; ${msg.is_mine ? 'align-self:flex-end;' : 'align-self:flex-start;'}">
					${msg.date}
				</span>
			`;
			chatMessages.appendChild(wrapper);
		}
	}

	/* ----------------------------------------------------
	 * 12. Booking Engine (Property Page)
	 * ---------------------------------------------------- */
	const bookingForm = document.getElementById('property-booking-form');
	const checkinInput = document.getElementById('booking_checkin');
	const checkoutInput = document.getElementById('booking_checkout');
	
	if ( bookingForm && checkinInput && checkoutInput && typeof flatpickr !== 'undefined' ) {
		const propertyId = document.getElementById('booking_property_id').value;
		const bookingSection = document.getElementById('booking-section');
		const pricePerNight = parseFloat(bookingSection.dataset.pricePerNight) || 0;
		const priceCalcDiv = document.getElementById('booking-price-calc');
		const nightsText = document.getElementById('calc-nights-text');
		const nightsTotal = document.getElementById('calc-nights-total');
		const grandTotal = document.getElementById('calc-grand-total');
		
		let checkoutPicker = null;
		
		// 1. Fetch Blocked Dates
		const formData = new FormData();
		formData.append('action', 'thessnest_fetch_booked_dates');
		formData.append('property_id', propertyId);
		
		fetch(thessnestAjax.ajaxurl, {
			method: 'POST',
			body: formData
		})
		.then(response => response.json())
		.then(data => {
			let disabledDates = [];
			if ( data.success && data.data.blocked_dates ) {
				disabledDates = data.data.blocked_dates;
			}
			
			// Initialize Check-in
			flatpickr(checkinInput, {
				minDate: "today",
				disable: disabledDates,
				onChange: function(selectedDates, dateStr, instance) {
					checkoutInput.disabled = false;
					if (checkoutPicker) {
						let minCheckout = new Date(selectedDates[0]);
						minCheckout.setDate(minCheckout.getDate() + 1);
						checkoutPicker.set('minDate', minCheckout);
					}
					calculatePrice();
				}
			});
			
			// Initialize Check-out
			let minN = new Date();
			minN.setDate(minN.getDate() + 1);
			checkoutPicker = flatpickr(checkoutInput, {
				minDate: minN,
				disable: disabledDates,
				onChange: function() {
					calculatePrice();
				}
			});
		});
		
		// 2. Calculate Price
		function calculatePrice() {
			if (!checkinInput.value || !checkoutInput.value) {
				priceCalcDiv.style.display = 'none';
				return;
			}
			
			const d1 = new Date(checkinInput.value);
			const d2 = new Date(checkoutInput.value);
			const diffTime = Math.abs(d2 - d1);
			const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
			
			if (diffDays > 0) {
				const total = diffDays * pricePerNight;
				nightsText.textContent = `${diffDays} night${diffDays > 1 ? 's' : ''} x €${pricePerNight}`;
				nightsTotal.textContent = `€${total}`;
				grandTotal.textContent = `€${total}`;
				priceCalcDiv.style.display = 'block';
			} else {
				priceCalcDiv.style.display = 'none';
			}
		}
		
		// 3. Submit Booking
		bookingForm.addEventListener('submit', function(e) {
			e.preventDefault();
			const submitBtn = document.getElementById('booking-submit-btn');
			const responseDiv = document.getElementById('booking-form-response');
			
			if (thessnestAjax.loggedIn === '0') {
				responseDiv.innerHTML = '<span style="color:#e53e3e;">You must be logged in to request a booking. Please sign in or register.</span>';
				responseDiv.style.display = 'block';
				responseDiv.style.background = '#fef2f2';
				return;
			}
			
			submitBtn.disabled = true;
			submitBtn.innerHTML = 'Submitting...';
			responseDiv.style.display = 'none';
			
			const fd = new FormData(this);
			
			fetch(thessnestAjax.ajaxurl, {
				method: 'POST',
				body: fd
			})
			.then(response => response.json())
			.then(data => {
				responseDiv.style.display = 'block';
				if (data.success) {
					responseDiv.style.background = '#f0fdf4';
					responseDiv.innerHTML = `<span style="color:#166534;">${data.data.message}</span>`;
					bookingForm.reset();
					priceCalcDiv.style.display = 'none';
				} else {
					responseDiv.style.background = '#fef2f2';
					responseDiv.innerHTML = `<span style="color:#e53e3e;">${data.data.message}</span>`;
				}
			})
			.catch(error => {
				console.error(error);
				responseDiv.style.background = '#fef2f2';
				responseDiv.innerHTML = '<span style="color:#e53e3e;">An unexpected error occurred.</span>';
				responseDiv.style.display = 'block';
			})
			.finally(() => {
				submitBtn.disabled = false;
				submitBtn.innerHTML = 'Request to Book';
			});
		});
	}

	/* ----------------------------------------------------
	 * 13. Dashboard Booking Management (Accept/Reject/Cancel)
	 * ---------------------------------------------------- */
	const manageBookingBtns = document.querySelectorAll('.btn-manage-booking, .btn-cancel-booking');
	manageBookingBtns.forEach(btn => {
		btn.addEventListener('click', function(e) {
			e.preventDefault();
			
			const action = this.dataset.action || 'cancel';
			const bookingId = this.dataset.id;
			const nonce = this.dataset.nonce;
			
			let confirmMsg = '';
			if (action === 'accept') confirmMsg = 'Are you sure you want to accept this booking?';
			else if (action === 'reject') confirmMsg = 'Are you sure you want to reject this booking?';
			else confirmMsg = 'Are you sure you want to cancel this trip?';
			
			if (!confirm(confirmMsg)) return;
			
			const originalText = this.innerHTML;
			this.disabled = true;
			this.innerHTML = '...';
			
			const fd = new FormData();
			fd.append('action', 'thessnest_manage_booking');
			fd.append('security', nonce);
			fd.append('booking_id', bookingId);
			fd.append('booking_action', action);
			
			fetch(thessnestAjax.ajaxurl, {
				method: 'POST',
				body: fd
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					// Update UI badge
					const item = this.closest('.booking-item');
					if (item) {
						const badge = item.querySelector('.booking-status-badge');
						if (badge) {
							badge.textContent = data.data.new_status;
							if (data.data.new_status === 'confirmed') badge.style = 'display:inline-block; padding:var(--space-1) var(--space-3); border-radius:var(--radius-full); font-size:12px; font-weight:600; background:#38a16920; color:#38a169; text-transform:uppercase;';
							if (data.data.new_status === 'rejected') badge.style = 'display:inline-block; padding:var(--space-1) var(--space-3); border-radius:var(--radius-full); font-size:12px; font-weight:600; background:#e53e3e20; color:#e53e3e; text-transform:uppercase;';
							if (data.data.new_status === 'canceled') badge.style = 'display:inline-block; padding:var(--space-1) var(--space-3); border-radius:var(--radius-full); font-size:12px; font-weight:600; background:#71809620; color:#718096; text-transform:uppercase;';
						}
						// Remove action buttons
						const actionArea = item.querySelector('.booking-action-buttons');
						if (actionArea) actionArea.remove();
						
						const cancelBtn = item.querySelector('.btn-cancel-booking');
						if (cancelBtn && data.data.new_status === 'canceled') cancelBtn.remove();
					}
				} else {
					alert(data.data.message || 'Error updating status.');
					this.disabled = false;
					this.innerHTML = originalText;
				}
			})
			.catch(err => {
				console.error(err);
				alert('An unexpected error occurred.');
				this.disabled = false;
				this.innerHTML = originalText;
			});
		});
	});

	/* ----------------------------------------------------
	 * 14. Scroll to Top Button
	 * ---------------------------------------------------- */
	const scrollTopBtn = document.getElementById('scroll-to-top');
	if (scrollTopBtn) {
		window.addEventListener('scroll', function() {
			if (window.scrollY > 400) {
				scrollTopBtn.classList.add('visible');
			} else {
				scrollTopBtn.classList.remove('visible');
			}
		}, { passive: true });

		scrollTopBtn.addEventListener('click', function() {
			window.scrollTo({ top: 0, behavior: 'smooth' });
		});
	}

	/* ----------------------------------------------------
	 * 15. Stats Counter — Animated Count-Up on Scroll
	 * ---------------------------------------------------- */
	const statNumbers = document.querySelectorAll('.stat-number[data-count]');
	if (statNumbers.length > 0 && 'IntersectionObserver' in window) {
		let statsAnimated = false;

		const animateCount = (el) => {
			const target = parseInt(el.getAttribute('data-count'), 10);
			if (isNaN(target)) return;

			const duration = 2000; // ms
			const startTime = performance.now();

			const tick = (now) => {
				const elapsed = now - startTime;
				const progress = Math.min(elapsed / duration, 1);

				// Ease-out cubic
				const eased = 1 - Math.pow(1 - progress, 3);
				const current = Math.round(eased * target);

				el.textContent = current.toLocaleString();

				if (progress < 1) {
					requestAnimationFrame(tick);
				}
			};

			requestAnimationFrame(tick);
		};

		const statsObserver = new IntersectionObserver((entries) => {
			entries.forEach(entry => {
				if (entry.isIntersecting && !statsAnimated) {
					statsAnimated = true;
					statNumbers.forEach(el => animateCount(el));
					statsObserver.disconnect();
				}
			});
		}, { threshold: 0.3 });

		const statsSection = document.querySelector('.stats-counter');
		if (statsSection) {
			statsObserver.observe(statsSection);
		}
	}

	/* ----------------------------------------------------
	 * 16. Homepage Date Range Picker (Booking Style)
	 * ---------------------------------------------------- */
	const homeDateInPicker = document.getElementById('home_date_in_picker');
	const homeDateOutPicker = document.getElementById('home_date_out_picker');

	let fpIn, fpOut;

	if (typeof flatpickr !== 'undefined') {
		if (homeDateInPicker) {
			fpIn = flatpickr(homeDateInPicker, {
				minDate: 'today',
				disableMobile: true,
				onChange: function(selectedDates, dateStr, instance) {
					if (selectedDates.length > 0) {
						homeMoveInVal.textContent = instance.formatDate(selectedDates[0], 'M j, Y');
						homeMoveInInput.value = instance.formatDate(selectedDates[0], 'Y-m-d');
						homeMoveInVal.style.color = 'var(--color-primary)';
						
						// Update Move-out minimum date
						if (fpOut) {
							fpOut.set('minDate', selectedDates[0]);
							setTimeout(() => fpOut.open(), 100);
						}
					}
				}
			});
			if (homeDateTrigger) {
				homeDateTrigger.addEventListener('click', () => fpIn.open());
			}
		}

		if (homeDateOutPicker) {
			fpOut = flatpickr(homeDateOutPicker, {
				minDate: 'today',
				disableMobile: true,
				onChange: function(selectedDates, dateStr, instance) {
					if (selectedDates.length > 0) {
						homeMoveOutVal.textContent = instance.formatDate(selectedDates[0], 'M j, Y');
						homeMoveOutInput.value = instance.formatDate(selectedDates[0], 'Y-m-d');
						homeMoveOutVal.style.color = 'var(--color-primary)';
					}
				}
			});
			if (homeDateTriggerOut) {
				homeDateTriggerOut.addEventListener('click', () => fpOut.open());
			}
		}
	}

	/* ----------------------------------------------------
	 * 17. Guest Selector Modal
	 * ---------------------------------------------------- */
	const guestTrigger = document.getElementById('trigger-guest-modal');
	const guestModal = document.getElementById('guest-selector-modal');
	const guestVal = document.getElementById('val-guests');
	const guestInput = document.getElementById('home_guests');
	const btnDec = document.getElementById('guest-dec');
	const btnInc = document.getElementById('guest-inc');
	const countDisplay = document.getElementById('guest-count-display');

	if (guestTrigger && guestModal) {
		guestTrigger.addEventListener('click', (e) => {
			// Prevent triggering if clicked on inner elements we want to handle separately
			if (e.target.closest('#guest-selector-modal')) return;
			guestModal.style.display = guestModal.style.display === 'none' ? 'block' : 'none';
		});

		// Close modal when clicking outside
		document.addEventListener('click', (e) => {
			if (!guestTrigger.contains(e.target)) {
				guestModal.style.display = 'none';
			}
		});

		let guestCount = 1;
		if (btnInc && btnDec && countDisplay && guestInput && guestVal) {
			btnInc.addEventListener('click', () => {
				if (guestCount < 10) {
					guestCount++;
					updateGuests();
				}
			});
			btnDec.addEventListener('click', () => {
				if (guestCount > 1) {
					guestCount--;
					updateGuests();
				}
			});

			function updateGuests() {
				countDisplay.textContent = guestCount;
				guestInput.value = guestCount;
				guestVal.textContent = guestCount + (guestCount === 1 ? ' Guest' : ' Guests');
				guestVal.style.color = 'var(--color-primary)';
			}
		}
	}

});
