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

		<!-- ── Filter Sidebar ──────────────────────────────── -->
		<aside class="filter-sidebar" id="filter-sidebar" role="complementary" aria-label="<?php esc_attr_e( 'Filter properties', 'thessnest' ); ?>">

			<h3><?php esc_html_e( 'Filters', 'thessnest' ); ?></h3>

			<form method="get" action="<?php echo esc_url( get_post_type_archive_link( 'property' ) ); ?>">
				<input type="hidden" name="post_type" value="property">

				<!-- Neighborhood -->
				<div class="filter-group">
					<label class="filter-label" for="filter-neighborhood">
						<?php esc_html_e( 'Neighborhood', 'thessnest' ); ?>
					</label>
					<select id="filter-neighborhood" name="neighborhood">
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

				<!-- Amenities (Checkboxes) -->
				<div class="filter-group">
					<span class="filter-label"><?php esc_html_e( 'Amenities', 'thessnest' ); ?></span>
					<div class="filter-checkbox-group">
						<?php
						$amenities = get_terms( array(
							'taxonomy'   => 'amenity',
							'hide_empty' => true,
						) );

						// Get currently selected amenities from URL.
						$selected_amenities = isset( $_GET['amenity'] ) ? array_map( 'sanitize_text_field', wp_unslash( (array) $_GET['amenity'] ) ) : array();

						if ( ! is_wp_error( $amenities ) ) :
							foreach ( $amenities as $am ) :
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

				<!-- Price Range -->
				<div class="filter-group">
					<span class="filter-label"><?php esc_html_e( 'Price Range (€/month)', 'thessnest' ); ?></span>
					<div class="price-range">
						<input type="number"
						       name="price_min"
						       placeholder="<?php esc_attr_e( 'Min', 'thessnest' ); ?>"
						       min="0"
						       step="50"
						       value="<?php echo isset( $_GET['price_min'] ) ? esc_attr( (int) $_GET['price_min'] ) : ''; ?>"
						       aria-label="<?php esc_attr_e( 'Minimum price', 'thessnest' ); ?>">
						<input type="number"
						       name="price_max"
						       placeholder="<?php esc_attr_e( 'Max', 'thessnest' ); ?>"
						       min="0"
						       step="50"
						       value="<?php echo isset( $_GET['price_max'] ) ? esc_attr( (int) $_GET['price_max'] ) : ''; ?>"
						       aria-label="<?php esc_attr_e( 'Maximum price', 'thessnest' ); ?>">
					</div>
				</div>

				<!-- Apply Filters -->
				<button type="submit" class="btn btn-primary filter-btn">
					<?php esc_html_e( 'Apply Filters', 'thessnest' ); ?>
				</button>

			</form>
		</aside>

		<!-- ── Main Content: Property Grid ─────────────── -->
		<div class="archive-main">

			<!-- Mobile Filter Toggle -->
			<button class="mobile-filter-toggle" id="mobile-filter-toggle" aria-controls="filter-sidebar" aria-expanded="false">
				<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/>
					<line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/>
				</svg>
				<?php esc_html_e( 'Filters', 'thessnest' ); ?>
			</button>

			<?php if ( have_posts() ) : ?>

				<div class="property-grid">
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

		// Default to Thessaloniki coordinates
		var map = L.map('properties-map').setView([40.6401, 22.9444], 13);

		L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
			maxZoom: 19,
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
		}).addTo(map);

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
					$lat = 40.6401 + ( ( rand(0, 100) - 50 ) / 3000 );
					$lng = 22.9444 + ( ( rand(0, 100) - 50 ) / 3000 );
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

		var bounds = L.latLngBounds();
		var hasValidPoints = false;

		propertiesData.forEach(function(prop) {
			if ( prop.lat && prop.lng ) {
				hasValidPoints = true;
				var customIcon = L.divIcon({
					className: 'custom-map-marker',
					html: '<div style="background:var(--color-primary);color:#fff;padding:4px 8px;border-radius:12px;font-weight:bold;font-size:12px;box-shadow:0 2px 4px rgba(0,0,0,0.2);white-space:nowrap;border:1px solid #fff;">' + prop.price + '</div>',
					iconSize: [null, null],
					iconAnchor: [20, 20],
					popupAnchor: [0, -20]
				});

				var marker = L.marker([prop.lat, prop.lng], {icon: customIcon}).addTo(map);
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
			map.fitBounds(bounds, { padding: [50, 50] });
		}
	});
	</script>

</main>

<?php get_footer(); ?>
