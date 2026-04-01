<?php
/**
 * ThessNest — Property Location Map Meta Box
 *
 * Adds a modern, interactive Leaflet map to the "Property" edit screen
 * allowing admins to simply click/drag to set latitude and longitude.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the Map Meta Box.
 */
function thessnest_add_location_meta_box() {
	add_meta_box(
		'thessnest_property_location', // ID
		__( 'Property Location (Map)', 'thessnest' ), // Title
		'thessnest_property_location_callback', // Callback function
		'property', // Post type
		'normal', // Context
		'high' // Priority
	);
}
add_action( 'add_meta_boxes', 'thessnest_add_location_meta_box' );

/**
 * Output the Meta Box content.
 *
 * @param WP_Post $post Current post object.
 */
function thessnest_property_location_callback( $post ) {
	// Add a nonce field for security checks.
	wp_nonce_field( 'thessnest_save_location_data', 'thessnest_location_nonce' );

	$lat = get_post_meta( $post->ID, '_thessnest_latitude', true );
	$lng = get_post_meta( $post->ID, '_thessnest_longitude', true );

	// Default to general center if empty.
	$default_lat = thessnest_opt( 'default_map_lat', '51.5074' );
	$default_lng = thessnest_opt( 'default_map_lng', '-0.1278' );

	if ( ! $lat ) { $lat = $default_lat; }
	if ( ! $lng ) { $lng = $default_lng; }
	?>
	<div style="margin-bottom: 1em;">
		<p class="description"><?php esc_html_e( 'Drag the marker or click anywhere on the map to set the property\'s exact location. The coordinates will automatically update.', 'thessnest' ); ?></p>
		
		<div style="display: flex; gap: 15px; margin-bottom: 15px;">
			<div>
				<label for="thessnest_latitude"><strong><?php esc_html_e( 'Latitude', 'thessnest' ); ?></strong></label><br>
				<input type="text" id="thessnest_latitude" name="thessnest_latitude" value="<?php echo esc_attr( $lat ); ?>" class="regular-text" readonly style="background:#f0f0f1; cursor:not-allowed;" />
			</div>
			<div>
				<label for="thessnest_longitude"><strong><?php esc_html_e( 'Longitude', 'thessnest' ); ?></strong></label><br>
				<input type="text" id="thessnest_longitude" name="thessnest_longitude" value="<?php echo esc_attr( $lng ); ?>" class="regular-text" readonly style="background:#f0f0f1; cursor:not-allowed;" />
			</div>
		</div>

		<!-- Map Container -->
		<div id="thessnest-admin-map" style="width: 100%; height: 400px; border: 1px solid #c3c4c7; border-radius: 4px;"></div>
	</div>

	<!-- Leaflet Setup Scripts -->
	<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Initialize the map
		var defaultLat = parseFloat('<?php echo esc_js( $default_lat ); ?>') || 51.5074;
		var defaultLng = parseFloat('<?php echo esc_js( $default_lng ); ?>') || -0.1278;

		var adminLat = parseFloat(document.getElementById('thessnest_latitude').value) || defaultLat;
		var adminLng = parseFloat(document.getElementById('thessnest_longitude').value) || defaultLng;

		var map = L.map('thessnest-admin-map').setView([adminLat, adminLng], 14);

		L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
			maxZoom: 19,
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>'
		}).addTo(map);

		// Initialize draggable marker
		var marker = L.marker([adminLat, adminLng], {
			draggable: true
		}).addTo(map);

		// Function to update input fields
		function updateCoordinates(lat, lng) {
			document.getElementById('thessnest_latitude').value = lat.toFixed(6);
			document.getElementById('thessnest_longitude').value = lng.toFixed(6);
		}

		// Update inputs when marker is dragged
		marker.on('dragend', function(e) {
			var position = marker.getLatLng();
			updateCoordinates(position.lat, position.lng);
		});

		// Move marker and update inputs when map is clicked
		map.on('click', function(e) {
			marker.setLatLng(e.latlng);
			updateCoordinates(e.latlng.lat, e.latlng.lng);
		});

		// Fix Leaflet sizing issue in WP hidden metaboxes
		setTimeout(function() {
			map.invalidateSize();
		}, 500);
	});
	</script>
	<?php
}

/**
 * Save the Meta Box data.
 *
 * @param int $post_id Post ID.
 */
function thessnest_save_location_meta_box_data( $post_id ) {
	// Check if nonce is set
	if ( ! isset( $_POST['thessnest_location_nonce'] ) ) {
		return;
	}

	// Verify the nonce
	if ( ! wp_verify_nonce( $_POST['thessnest_location_nonce'], 'thessnest_save_location_data' ) ) {
		return;
	}

	// Stop WP from clearing custom fields on autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check user permissions
	if ( isset( $_POST['post_type'] ) && 'property' === $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	// Sanitize & Save Coordinates
	if ( isset( $_POST['thessnest_latitude'] ) ) {
		// Use sanitize_text_field as it's a coordinate string
		update_post_meta( $post_id, '_thessnest_latitude', sanitize_text_field( $_POST['thessnest_latitude'] ) );
	}
	
	if ( isset( $_POST['thessnest_longitude'] ) ) {
		update_post_meta( $post_id, '_thessnest_longitude', sanitize_text_field( $_POST['thessnest_longitude'] ) );
	}
}
add_action( 'save_post', 'thessnest_save_location_meta_box_data' );

/**
 * Enqueue Leaflet for the Admin screen only on 'property' post type.
 *
 * @param string $hook The current admin page.
 */
function thessnest_enqueue_admin_map_scripts( $hook ) {
	global $post;

	if ( 'post.php' === $hook || 'post-new.php' === $hook ) {
		if ( 'property' === $post->post_type ) {
			wp_enqueue_style( 'leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4' );
			wp_enqueue_script( 'leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', false );
		}
	}
}
add_action( 'admin_enqueue_scripts', 'thessnest_enqueue_admin_map_scripts' );
