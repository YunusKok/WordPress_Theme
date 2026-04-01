<?php
/**
 * ThessNest — Archive Template: Properties
 *
 * Displays the property listing archive with:
 *   - Sticky sidebar filter panel (Neighborhood, Amenities, Target Group, Price Range)
 *   - Main content area with property card grid
 *   - Pagination
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

get_header();

global $wp_query;
?>

<main id="main-content" role="main">

	<div class="properties-split-layout">

		<!-- ── MAP SECTION (Left on Desktop) ───────────────────── -->
		<div class="properties-map-wrapper">
			<div id="properties-map" aria-label="<?php esc_attr_e( 'Properties Map', 'thessnest' ); ?>"></div>
		</div>

		<!-- ── LISTINGS SECTION (Right on Desktop) ─────────────── -->
		<div class="properties-content-wrapper">

			<!-- Archive Page Header -->
			<section style="background:var(--color-surface);padding:var(--space-10) var(--space-4);border-bottom:1px solid var(--color-border);">
				<div class="container">
					<?php
					if ( function_exists( 'thessnest_breadcrumbs' ) ) {
						thessnest_breadcrumbs();
					}
					?>
					<h1 class="archive-title">
						<?php
						if ( is_tax( 'neighborhood' ) ) {
							printf(
								/* translators: %s: neighborhood name */
								esc_html__( 'Properties in %s', 'thessnest' ),
								single_term_title( '', false )
							);
						} elseif ( is_tax( 'amenity' ) ) {
							printf(
								/* translators: %s: amenity name */
								esc_html__( 'Properties with %s', 'thessnest' ),
								single_term_title( '', false )
							);
						} elseif ( is_tax( 'target_group' ) ) {
							printf(
								/* translators: %s: target group name */
								esc_html__( '%s Properties', 'thessnest' ),
								single_term_title( '', false )
							);
						} else {
							esc_html_e( 'All Properties', 'thessnest' );
						}
						?>
					</h1>
					<p class="archive-count">
						<?php
						printf(
							/* translators: %d: number of properties */
							esc_html( _n( '%d property found', '%d properties found', $wp_query->found_posts, 'thessnest' ) ),
							(int) $wp_query->found_posts
						);
						?>
					</p>
				</div>
			</section>

			<div class="archive-layout container">

		<?php if ( thessnest_opt( 'show_sidebar_filters', true ) ) : ?>
		<!-- ── Filter Sidebar ──────────────────────────────── -->
		<aside class="filter-sidebar" id="filter-sidebar" role="complementary" aria-label="<?php esc_attr_e( 'Filter properties', 'thessnest' ); ?>">

			<h3><?php esc_html_e( 'Filters', 'thessnest' ); ?></h3>

			<form id="property-filter-form" method="get" action="<?php echo esc_url( get_post_type_archive_link( 'property' ) ); ?>">
				<input type="hidden" name="post_type" value="property">
				<input type="hidden" name="action" value="thessnest_live_search">

				<?php if ( is_user_logged_in() ) : ?>
					<div style="margin-bottom:var(--space-4);text-align:right;">
						<button type="button" id="btn-save-search" class="btn btn-outline" style="font-size:12px;padding:4px 8px;border-color:var(--color-primary);color:var(--color-primary);">
							<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
							<?php esc_html_e('Save Search', 'thessnest'); ?>
						</button>
					</div>
				<?php endif; ?>

				<!-- Date Availability -->
				<div class="filter-group">
					<span class="filter-label"><?php esc_html_e( 'Availability', 'thessnest' ); ?></span>
					<div style="display:flex; gap:var(--space-2);">
						<input type="date" name="checkin" class="live-search-input" style="width:50%;font-size:12px;padding:var(--space-2);" title="<?php esc_attr_e('Check-in', 'thessnest'); ?>">
						<input type="date" name="checkout" class="live-search-input" style="width:50%;font-size:12px;padding:var(--space-2);" title="<?php esc_attr_e('Check-out', 'thessnest'); ?>">
					</div>
				</div>

				<!-- Radius Search -->
				<div class="filter-group">
					<span class="filter-label"><?php esc_html_e( 'Location Radius', 'thessnest' ); ?></span>
					<!-- Using hardcoded coordinates for demo. In production, connect to Google Places API -> Lat/Lng -->
					<input type="hidden" name="lat" id="search-lat" value="<?php echo esc_attr( thessnest_opt( 'default_map_lat', '51.5074' ) ); ?>">
					<input type="hidden" name="lng" id="search-lng" value="<?php echo esc_attr( thessnest_opt( 'default_map_lng', '-0.1278' ) ); ?>">
					
					<select name="radius" class="live-search-input" style="width:100%; margin-top:var(--space-2);">
						<option value="0"><?php esc_html_e( 'Any Distance', 'thessnest' ); ?></option>
						<option value="1"><?php esc_html_e( '+ 1 km', 'thessnest' ); ?></option>
						<option value="5"><?php esc_html_e( '+ 5 km', 'thessnest' ); ?></option>
						<option value="10"><?php esc_html_e( '+ 10 km', 'thessnest' ); ?></option>
					</select>
				</div>

				<!-- Guests & WiFi -->
				<div class="filter-group" style="display:flex; gap:var(--space-4);">
					<div style="flex:1;">
						<span class="filter-label"><?php esc_html_e( 'Guests', 'thessnest' ); ?></span>
						<input type="number" name="guests" class="live-search-input" min="1" placeholder="1" style="width:100%;">
					</div>
					<div style="flex:1;">
						<span class="filter-label"><?php esc_html_e( 'Min WiFi (Mbps)', 'thessnest' ); ?></span>
						<input type="number" name="wifi_min" class="live-search-input" min="0" step="10" placeholder="50" style="width:100%;">
					</div>
				</div>

				<?php if ( thessnest_opt( 'search_show_neighborhood', true ) ) : ?>
				<!-- Neighborhood -->
				<div class="filter-group">
					<label class="filter-label" for="filter-neighborhood">
						<?php esc_html_e( 'Neighborhood', 'thessnest' ); ?>
					</label>
					<select id="filter-neighborhood" name="neighborhood" class="live-search-input">
						<option value=""><?php esc_html_e( 'All Neighborhoods', 'thessnest' ); ?></option>
						<?php
						$neighborhoods = get_terms( array(
							'taxonomy'   => 'neighborhood',
							'hide_empty' => true,
						) );
						if ( ! is_wp_error( $neighborhoods ) ) :
							foreach ( $neighborhoods as $nb ) :
								$selected = ( isset( $_GET['neighborhood'] ) && sanitize_text_field( wp_unslash( $_GET['neighborhood'] ) ) === $nb->slug ) ? 'selected' : '';
								?>
								<option value="<?php echo esc_attr( $nb->slug ); ?>" <?php echo esc_attr( $selected ); ?>>
									<?php echo esc_html( $nb->name ); ?> (<?php echo esc_html( $nb->count ); ?>)
								</option>
							<?php endforeach;
						endif;
						?>
					</select>
				</div>
				<?php endif; ?>

				<?php if ( thessnest_opt( 'search_show_target_group', true ) ) : ?>
				<!-- Target Group -->
				<div class="filter-group">
					<label class="filter-label" for="filter-target-group">
						<?php esc_html_e( 'Target Group', 'thessnest' ); ?>
					</label>
					<select id="filter-target-group" name="target_group">
						<option value=""><?php esc_html_e( 'All Groups', 'thessnest' ); ?></option>
						<?php
						$target_groups = get_terms( array(
							'taxonomy'   => 'target_group',
							'hide_empty' => true,
						) );
						if ( ! is_wp_error( $target_groups ) ) :
							foreach ( $target_groups as $tg ) :
								$selected = ( isset( $_GET['target_group'] ) && sanitize_text_field( wp_unslash( $_GET['target_group'] ) ) === $tg->slug ) ? 'selected' : '';
								?>
								<option value="<?php echo esc_attr( $tg->slug ); ?>" <?php echo esc_attr( $selected ); ?>>
									<?php echo esc_html( $tg->name ); ?>
								</option>
							<?php endforeach;
						endif;
						?>
					</select>
				</div>

				<!-- Instant Book -->
				<div class="filter-group">
					<label class="filter-label" style="display:flex; align-items:center; gap:var(--space-2); cursor:pointer;">
						<input type="checkbox" name="instant_book" value="1" class="live-search-input" <?php echo isset( $_GET['instant_book'] ) ? 'checked' : ''; ?>>
						<span>⚡ <?php esc_html_e( 'Instant Book Only', 'thessnest' ); ?></span>
					</label>
				</div>

				<?php if ( thessnest_opt( 'search_show_amenities', true ) ) : ?>
				<!-- Appliances (Specific Amenities) -->
				<div class="filter-group">
					<span class="filter-label"><?php esc_html_e( 'Appliances', 'thessnest' ); ?></span>
					<div class="filter-checkbox-group">
						<?php
						$amenities = get_terms( array(
							'taxonomy'   => 'amenity',
							'hide_empty' => false,
						) );

						$selected_amenities = isset( $_GET['amenity'] ) ? array_map( 'sanitize_text_field', wp_unslash( (array) $_GET['amenity'] ) ) : array();
						$appliance_slugs = array( 'washing-machine', 'oven', 'dishwasher' );
						$regular_amenities = array();

						if ( ! is_wp_error( $amenities ) ) :
							foreach ( $amenities as $am ) :
								if ( in_array( $am->slug, $appliance_slugs, true ) ) {
									$checked = in_array( $am->slug, $selected_amenities, true ) ? 'checked' : '';
									?>
									<label>
										<input type="checkbox" name="amenity[]" value="<?php echo esc_attr( $am->slug ); ?>" class="live-search-input" <?php echo esc_attr( $checked ); ?>>
										<?php echo esc_html( $am->name ); ?>
									</label>
									<?php
								} else {
									$regular_amenities[] = $am; // save the rest for later
								}
							endforeach;
						endif;
						?>
					</div>
				</div>

				<!-- Amenities (Checkboxes) -->
				<div class="filter-group">
					<span class="filter-label"><?php esc_html_e( 'Amenities', 'thessnest' ); ?></span>
					<div class="filter-checkbox-group">
						<?php
						if ( ! empty( $regular_amenities ) ) :
							foreach ( $regular_amenities as $am ) :
								$checked = in_array( $am->slug, $selected_amenities, true ) ? 'checked' : '';
								?>
								<label>
									<input type="checkbox" name="amenity[]" value="<?php echo esc_attr( $am->slug ); ?>" <?php echo esc_attr( $checked ); ?>>
									<?php echo esc_html( $am->name ); ?>
								</label>
							<?php endforeach;
						endif;
						?>
					</div>
				</div>
				<?php endif; ?>

				<?php if ( thessnest_opt( 'search_show_price_range', true ) ) : ?>
				<!-- Price Range -->
				<div class="filter-group">
					<span class="filter-label"><?php esc_html_e( 'Price Range (€/month)', 'thessnest' ); ?></span>
					<div class="price-range">
						<input type="number"
						       name="price_min"
						       class="live-search-input"
						       placeholder="<?php esc_attr_e( 'Min', 'thessnest' ); ?>"
						       min="<?php echo esc_attr( thessnest_opt('search_price_min', 100) ); ?>"
						       step="50"
						       value="<?php echo isset( $_GET['price_min'] ) ? esc_attr( (int) $_GET['price_min'] ) : ''; ?>"
						       aria-label="<?php esc_attr_e( 'Minimum price', 'thessnest' ); ?>">
						<input type="number"
						       name="price_max"
						       class="live-search-input"
						       placeholder="<?php esc_attr_e( 'Max', 'thessnest' ); ?>"
						       max="<?php echo esc_attr( thessnest_opt('search_price_max', 2000) ); ?>"
						       step="50"
						       value="<?php echo isset( $_GET['price_max'] ) ? esc_attr( (int) $_GET['price_max'] ) : ''; ?>"
						       aria-label="<?php esc_attr_e( 'Maximum price', 'thessnest' ); ?>">
					</div>
				</div>
				<?php endif; ?>

				<!-- Apply Filters (Disabled via JS if live search is active, but kept for fallback) -->
				<button type="submit" class="btn btn-primary filter-btn" id="btn-fallback-apply">
					<?php esc_html_e( 'Apply Filters', 'thessnest' ); ?>
				</button>
				
				<!-- Live Search Loading Overlay -->
				<div id="live-search-loading" style="display:none; text-align:center; padding:10px; color:var(--color-primary);">
					<svg class="thessnest-spinner" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="animation: spin 1s linear infinite;"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg>
					<span style="font-size:14px; margin-left:8px; font-weight:bold;"><?php esc_html_e('Updating...', 'thessnest'); ?></span>
				</div>
				<style> @keyframes spin { 100% { transform: rotate(360deg); } } </style>


			</form>
		</aside>
		<?php endif; ?>

		<!-- ── Main Content: Property Grid ─────────────── -->
		<div class="archive-main">

			<?php if ( thessnest_opt( 'show_sidebar_filters', true ) ) : ?>
			<!-- Mobile Filter Toggle -->
			<button class="mobile-filter-toggle" id="mobile-filter-toggle" aria-controls="filter-sidebar" aria-expanded="false">
				<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/>
					<line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/>
				</svg>
				<?php esc_html_e( 'Filters', 'thessnest' ); ?>
			</button>
			<?php endif; ?>

			<?php if ( have_posts() ) : ?>

				<?php
				$layout = thessnest_opt( 'listings_layout', 'grid' );
				$cols   = thessnest_opt( 'listings_columns', '3' );
				?>
				<div class="property-grid <?php echo $layout === 'list' ? 'property-list' : ''; ?>" data-columns="<?php echo esc_attr( $cols ); ?>">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/property-card' );
					endwhile;
					?>
				</div>

				<!-- Pagination -->
				<div class="pagination">
					<?php
					the_posts_pagination( array(
						'mid_size'  => 2,
						'prev_text' => '&larr;',
						'next_text' => '&rarr;',
					) );
					?>
				</div>

			<?php else : ?>

				<!-- Empty State -->
				<div class="text-center" style="padding:var(--space-16);">
					<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="1" aria-hidden="true" style="margin:0 auto var(--space-4);">
						<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
						<line x1="8" y1="8" x2="14" y2="14"/><line x1="14" y1="8" x2="8" y2="14"/>
					</svg>
					<h2 style="font-size:var(--font-size-xl);margin-bottom:var(--space-2);">
						<?php esc_html_e( 'No properties found', 'thessnest' ); ?>
					</h2>
					<p class="text-muted">
						<?php esc_html_e( 'Try adjusting your filters or check back later for new listings.', 'thessnest' ); ?>
					</p>
					<a href="<?php echo esc_url( get_post_type_archive_link( 'property' ) ); ?>" class="btn btn-outline mt-4">
						<?php esc_html_e( 'Clear Filters', 'thessnest' ); ?>
					</a>
				</div>

			<?php endif; ?>

		</div>

			</div>
		</div>
	</div>

	<!-- ── MAP INITIALIZATION SCRIPT ───────────────────────── -->
	<script>
	document.addEventListener('DOMContentLoaded', function() {
		if ( typeof L === 'undefined' ) return;

		// Default to general coordinates
		window.thessnestMap = L.map('properties-map').setView([<?php echo esc_js( thessnest_opt( 'default_map_lat', '51.5074' ) ); ?>, <?php echo esc_js( thessnest_opt( 'default_map_lng', '-0.1278' ) ); ?>], 13);

		L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
			maxZoom: 19,
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
		}).addTo(window.thessnestMap);

		window.thessnestMapMarkers = L.layerGroup().addTo(window.thessnestMap);

		// Properties data
		var propertiesData = [];
		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				$prop_id = get_the_ID();
				
				// In a real scenario, use actual lat/lng custom fields or geo coder.
				// For the demo/theme fallback, slightly randomize around Thessaloniki center.
				$lat = get_post_meta( $prop_id, '_thessnest_latitude', true );
				$lng = get_post_meta( $prop_id, '_thessnest_longitude', true );

				if ( ! $lat || ! $lng ) {
					// Fallback pseudo-random for demo
					$lat = floatval( thessnest_opt( 'default_map_lat', '51.5074' ) ) + ( ( rand(0, 100) - 50 ) / 3000 );
					$lng = floatval( thessnest_opt( 'default_map_lng', '-0.1278' ) ) + ( ( rand(0, 100) - 50 ) / 3000 );
				}

				$img_url = has_post_thumbnail() ? get_the_post_thumbnail_url( $prop_id, 'medium' ) : '';
				$price   = thessnest_format_price( thessnest_get_meta( 'rent', $prop_id ) );

				echo "propertiesData.push({
					id: {$prop_id},
					title: '" . esc_js( get_the_title() ) . "',
					url: '" . esc_url( get_the_permalink() ) . "',
					lat: {$lat},
					lng: {$lng},
					img: '{$img_url}',
					price: '{$price}'
				});\n";
			}
			wp_reset_postdata(); // Reset the loop pointer so normal grid works
		}
		?>

		window.renderMarkers = function(data) {
			window.thessnestMapMarkers.clearLayers();
			var bounds = L.latLngBounds();
			var hasValidPoints = false;

			data.forEach(function(prop) {
				if ( prop.lat && prop.lng ) {
					hasValidPoints = true;
					var customIcon = L.divIcon({
						className: 'custom-map-marker',
						html: '<div style="background:var(--color-primary);color:#fff;padding:4px 8px;border-radius:12px;font-weight:bold;font-size:12px;box-shadow:0 2px 4px rgba(0,0,0,0.2);white-space:nowrap;border:1px solid #fff;">' + prop.price + '</div>',
						iconSize: [null, null],
						iconAnchor: [20, 20],
						popupAnchor: [0, -20]
					});

					var marker = L.marker([prop.lat, prop.lng], {icon: customIcon}).addTo(window.thessnestMapMarkers);
					bounds.extend([prop.lat, prop.lng]);

					var popupHtml = '<div class="map-popup-card">' +
						(prop.img ? '<img src="' + prop.img + '" alt="' + prop.title + '">' : '') +
						'<div class="map-popup-content">' +
							'<h4><a href="' + prop.url + '">' + prop.title + '</a></h4>' +
							'<div class="price">' + prop.price + ' / month</div>' +
						'</div>' +
					'</div>';

					marker.bindPopup(popupHtml);
				}
			});

			if ( hasValidPoints ) {
				window.thessnestMap.fitBounds(bounds, { padding: [50, 50] });
			}
		};

		window.renderMarkers(propertiesData);
	});
	</script>

	<!-- ── AJAX LIVE SEARCH SCRIPT ───────────────────────── -->
	<script>
	document.addEventListener('DOMContentLoaded', function() {
		const form = document.getElementById('property-filter-form');
		const gridContainer = document.querySelector('.archive-main');
		const loadingIndicator = document.getElementById('live-search-loading');
		const headerCount = document.querySelector('.archive-count');
		let searchTimeout;

		if (!form || !gridContainer) return;

		// Utility to serialize form logic for AJAX
		function triggerLiveSearch() {
			clearTimeout(searchTimeout);
			searchTimeout = setTimeout(() => {
				loadingIndicator.style.display = 'block';
				document.getElementById('btn-fallback-apply').style.display = 'none';

				// Ensure map is slightly faded while loading to show activity
				document.getElementById('properties-map').style.opacity = '0.5';

				const formData = new FormData(form);
				
				fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
					method: 'POST',
					body: formData
				})
				.then(response => response.json())
				.then(data => {
					loadingIndicator.style.display = 'none';
					document.getElementById('properties-map').style.opacity = '1';

					if (data.success) {
						// 1. Update Grid Content
						// We replace everything except the mobile filter toggle
						const mobileToggle = document.getElementById('mobile-filter-toggle');
						gridContainer.innerHTML = '';
						if (mobileToggle) gridContainer.appendChild(mobileToggle);

						if (data.data.html) {
							// Found results
							gridContainer.insertAdjacentHTML('beforeend', data.data.html);
						} else {
							// Error or no results
							gridContainer.insertAdjacentHTML('beforeend', '<div class="text-center" style="padding:var(--space-16);"><h3>No properties found mathcing your criteria.</h3></div>');
						}

						// 2. Update Map Markers
						if (window.renderMarkers && data.data.map_data) {
							window.renderMarkers(data.data.map_data);
						}

						// 3. Update Count String
						if (headerCount) {
							const found = data.data.found_posts || 0;
							headerCount.textContent = found === 1 ? '1 property found' : found + ' properties found';
						}
					}
				})
				.catch(err => {
					console.error('AJAX Search Error:', err);
					loadingIndicator.style.display = 'none';
					document.getElementById('properties-map').style.opacity = '1';
				});
			}, 400); // 400ms debounce
		}

		// Prevent Form Submission Default -> Turn it into AJAX 
		form.addEventListener('submit', function(e) {
			e.preventDefault();
			triggerLiveSearch();
		});

		// Listen to all inputs with class live-search-input
		const inputs = form.querySelectorAll('.live-search-input');
		inputs.forEach(input => {
			input.addEventListener('change', triggerLiveSearch);
			// For text/number inputs, also listen on keyup with debounce
			if (input.type === 'number' || input.type === 'text') {
				input.addEventListener('keyup', triggerLiveSearch);
			}
		});

		// Save Search Button Handler
		const btnSaveSearch = document.getElementById('btn-save-search');
		if (btnSaveSearch) {
			btnSaveSearch.addEventListener('click', function(e) {
				e.preventDefault();
				const origText = btnSaveSearch.innerHTML;
				btnSaveSearch.innerHTML = 'Saving...';
				btnSaveSearch.disabled = true;

				// Append specific action for saving
				const formData = new FormData(form);
				formData.set('action', 'thessnest_save_search');

				fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
					method: 'POST',
					body: formData
				}).then(res => res.json()).then(data => {
					if (data.success) {
						btnSaveSearch.innerHTML = '✅ Saved';
						setTimeout(() => { btnSaveSearch.innerHTML = origText; btnSaveSearch.disabled = false; }, 2000);
					}
				});
			});
		}

	});
	</script>


</main>

<?php get_footer(); ?>
