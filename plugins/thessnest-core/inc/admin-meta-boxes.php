<?php
/**
 * ThessNest — Admin Meta Boxes
 *
 * Registers custom meta boxes for the Property Custom Post Type,
 * allowing admins to edit Advanced Pricing fields and other metadata
 * directly from the WP Admin Dashboard.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

class ThessNest_Admin_Meta_Boxes {

	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_property_meta_boxes' ) );
		add_action( 'save_post_property', array( $this, 'save_property_meta_boxes' ) );
		
		// Enqueue admin scripts for the repeater
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Enqueue admin scripts/styles for meta boxes.
	 */
	public function enqueue_admin_scripts( $hook ) {
		if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
			return;
		}
		
		global $post;
		if ( ! $post || 'property' !== $post->post_type ) {
			return;
		}

		?>
		<style>
			.thessnest-meta-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
			.thessnest-meta-field { margin-bottom: 10px; }
			.thessnest-meta-field label { display: block; font-weight: 600; margin-bottom: 5px; }
			.thessnest-meta-field input, .thessnest-meta-field select { width: 100%; border-radius: 4px; border: 1px solid #ccc; padding: 5px; }
			.thessnest-season-row { display: flex; gap: 10px; align-items: flex-end; margin-bottom: 10px; background: #f9f9f9; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
			.thessnest-season-row > div { flex: 1; }
			.btn-remove-season { background: #d63638; color: #fff; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; }
			.btn-add-season { background: #2271b1; color: #fff; border: none; padding: 6px 12px; border-radius: 3px; cursor: pointer; }
		</style>
		<script>
		document.addEventListener('DOMContentLoaded', function() {
			const container = document.getElementById('thessnest-seasonal-rates-container');
			const btnAdd = document.getElementById('thessnest-btn-add-season');
			const template = document.getElementById('thessnest-season-template');

			if (btnAdd && container && template) {
				btnAdd.addEventListener('click', function(e) {
					e.preventDefault();
					const clone = template.content.cloneNode(true);
					container.appendChild(clone);
				});

				container.addEventListener('click', function(e) {
					if (e.target.classList.contains('btn-remove-season')) {
						e.target.closest('.thessnest-season-row').remove();
					}
				});
			}
		});
		</script>
		<?php
	}

	/**
	 * Register meta boxes.
	 */
	public function add_property_meta_boxes() {
		add_meta_box(
			'thessnest_property_pricing',
			__( 'Advanced Pricing Engine', 'thessnest' ),
			array( $this, 'render_pricing_meta_box' ),
			'property',
			'normal',
			'high'
		);

		add_meta_box(
			'thessnest_ical_feeds',
			__( 'iCalendar Feeds (Sync)', 'thessnest' ),
			array( $this, 'render_ical_feeds_meta_box' ),
			'property',
			'side',
			'default'
		);
	}

	/**
	 * Render the Pricing meta box.
	 */
	public function render_pricing_meta_box( $post ) {
		wp_nonce_field( 'thessnest_save_pricing_meta', 'thessnest_pricing_meta_nonce' );

		$rent = get_post_meta( $post->ID, '_thessnest_rent', true );
		$utilities = get_post_meta( $post->ID, '_thessnest_utilities', true );
		$deposit = get_post_meta( $post->ID, '_thessnest_deposit', true );

		$min_stay = get_post_meta( $post->ID, '_thessnest_min_stay', true );
		$cleaning_fee = get_post_meta( $post->ID, '_thessnest_cleaning_fee', true );
		$cleaning_fee_type = get_post_meta( $post->ID, '_thessnest_cleaning_fee_type', true );
		$service_fee = get_post_meta( $post->ID, '_thessnest_service_fee', true );

		$weekly = get_post_meta( $post->ID, '_thessnest_weekly_discount', true );
		$monthly = get_post_meta( $post->ID, '_thessnest_monthly_discount', true );
		$quarterly = get_post_meta( $post->ID, '_thessnest_quarterly_discount', true );

		$eb_days = get_post_meta( $post->ID, '_thessnest_early_bird_days', true );
		$eb_discount = get_post_meta( $post->ID, '_thessnest_early_bird_discount', true );
		
		$seasonal_rates = get_post_meta( $post->ID, '_thessnest_seasonal_rates', true );
		if ( ! is_array( $seasonal_rates ) ) {
			$seasonal_rates = array();
		}

		?>
		<div class="thessnest-meta-grid">
			<div class="thessnest-meta-field">
				<label><?php esc_html_e( 'Monthly Rent (€)', 'thessnest' ); ?></label>
				<input type="number" name="thessnest_rent" value="<?php echo esc_attr( $rent ); ?>">
			</div>
			<div class="thessnest-meta-field">
				<label><?php esc_html_e( 'Utilities (€)', 'thessnest' ); ?></label>
				<input type="number" name="thessnest_utilities" value="<?php echo esc_attr( $utilities ); ?>">
			</div>
			<div class="thessnest-meta-field">
				<label><?php esc_html_e( 'Security Deposit (€)', 'thessnest' ); ?></label>
				<input type="number" name="thessnest_deposit" value="<?php echo esc_attr( $deposit ); ?>">
			</div>
			<div class="thessnest-meta-field">
				<label><?php esc_html_e( 'Min Stay (Nights)', 'thessnest' ); ?></label>
				<input type="number" name="thessnest_min_stay" value="<?php echo esc_attr( $min_stay ); ?>">
			</div>
			<div class="thessnest-meta-field">
				<label><?php esc_html_e( 'Cleaning Fee (€)', 'thessnest' ); ?></label>
				<input type="number" name="thessnest_cleaning_fee" value="<?php echo esc_attr( $cleaning_fee ); ?>">
			</div>
			<div class="thessnest-meta-field">
				<label><?php esc_html_e( 'Cleaning Fee Type', 'thessnest' ); ?></label>
				<select name="thessnest_cleaning_fee_type">
					<option value="single" <?php selected($cleaning_fee_type, 'single'); ?>><?php esc_html_e( 'Single', 'thessnest' ); ?></option>
					<option value="monthly" <?php selected($cleaning_fee_type, 'monthly'); ?>><?php esc_html_e( 'Monthly', 'thessnest' ); ?></option>
					<option value="on-demand" <?php selected($cleaning_fee_type, 'on-demand'); ?>><?php esc_html_e( 'On-Demand', 'thessnest' ); ?></option>
				</select>
			</div>
			<div class="thessnest-meta-field">
				<label><?php esc_html_e( 'Service Fee (€)', 'thessnest' ); ?></label>
				<input type="number" name="thessnest_service_fee" value="<?php echo esc_attr( $service_fee ); ?>">
			</div>
		</div>

		<hr>
		<h4><?php esc_html_e( 'Discounts', 'thessnest' ); ?></h4>
		<div class="thessnest-meta-grid">
			<div class="thessnest-meta-field">
				<label><?php esc_html_e( 'Weekly % (7+)', 'thessnest' ); ?></label>
				<input type="number" name="thessnest_weekly_discount" value="<?php echo esc_attr( $weekly ); ?>">
			</div>
			<div class="thessnest-meta-field">
				<label><?php esc_html_e( 'Monthly % (28+)', 'thessnest' ); ?></label>
				<input type="number" name="thessnest_monthly_discount" value="<?php echo esc_attr( $monthly ); ?>">
			</div>
			<div class="thessnest-meta-field">
				<label><?php esc_html_e( 'Quarterly % (90+)', 'thessnest' ); ?></label>
				<input type="number" name="thessnest_quarterly_discount" value="<?php echo esc_attr( $quarterly ); ?>">
			</div>
			<div class="thessnest-meta-field">
				<label><?php esc_html_e( 'Early Bird Days', 'thessnest' ); ?></label>
				<input type="number" name="thessnest_early_bird_days" value="<?php echo esc_attr( $eb_days ); ?>">
			</div>
			<div class="thessnest-meta-field">
				<label><?php esc_html_e( 'Early Bird %', 'thessnest' ); ?></label>
				<input type="number" name="thessnest_early_bird_discount" value="<?php echo esc_attr( $eb_discount ); ?>">
			</div>
		</div>

		<hr>
		<h4><?php esc_html_e( 'Seasonal Rates', 'thessnest' ); ?></h4>
		<div id="thessnest-seasonal-rates-container">
			<?php foreach ( $seasonal_rates as $season ) : ?>
				<div class="thessnest-season-row">
					<div>
						<label><?php esc_html_e('Start Date', 'thessnest'); ?></label>
						<input type="date" name="thessnest_season[start][]" value="<?php echo esc_attr( $season['start'] ); ?>">
					</div>
					<div>
						<label><?php esc_html_e('End Date', 'thessnest'); ?></label>
						<input type="date" name="thessnest_season[end][]" value="<?php echo esc_attr( $season['end'] ); ?>">
					</div>
					<div>
						<label><?php esc_html_e('Rate (€)', 'thessnest'); ?></label>
						<input type="number" name="thessnest_season[rate][]" value="<?php echo esc_attr( $season['rate'] ); ?>">
					</div>
					<button type="button" class="btn-remove-season">✕</button>
				</div>
			<?php endforeach; ?>
		</div>
		<button type="button" id="thessnest-btn-add-season" class="btn-add-season">+ <?php esc_html_e( 'Add Season', 'thessnest' ); ?></button>

		<template id="thessnest-season-template">
			<div class="thessnest-season-row">
				<div>
					<label><?php esc_html_e('Start Date', 'thessnest'); ?></label>
					<input type="date" name="thessnest_season[start][]">
				</div>
				<div>
					<label><?php esc_html_e('End Date', 'thessnest'); ?></label>
					<input type="date" name="thessnest_season[end][]">
				</div>
				<div>
					<label><?php esc_html_e('Rate (€)', 'thessnest'); ?></label>
					<input type="number" name="thessnest_season[rate][]">
				</div>
				<button type="button" class="btn-remove-season">✕</button>
			</div>
		</template>
		<?php
	}

	/**
	 * Render the iCal Feeds meta box.
	 */
	public function render_ical_feeds_meta_box( $post ) {
		wp_nonce_field( 'thessnest_save_ical_feeds', 'thessnest_ical_feeds_nonce' );

		$feeds = get_post_meta( $post->ID, '_thessnest_ical_feeds', true );
		if ( ! is_array( $feeds ) ) {
			$feeds = array();
		}

		// Backward compat: show legacy single URL if no new feeds exist
		if ( empty( $feeds ) ) {
			$legacy_url = get_post_meta( $post->ID, '_thessnest_ical_import_url', true );
			if ( ! empty( $legacy_url ) ) {
				$feeds[] = array( 'name' => 'Airbnb', 'url' => $legacy_url );
			}
		}

		$last_sync = get_post_meta( $post->ID, '_thessnest_ical_last_sync', true );
		$export_url = get_permalink( $post->ID ) . 'ical/';
		?>

		<style>
			.thessnest-ical-feed { background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px; padding: 8px; margin-bottom: 8px; }
			.thessnest-ical-feed input { width: 100%; margin-bottom: 4px; padding: 4px 6px; border: 1px solid #ccc; border-radius: 3px; font-size: 12px; }
			.thessnest-ical-feed .btn-remove-ical { background: #d63638; color: #fff; border: none; padding: 2px 8px; border-radius: 3px; cursor: pointer; font-size: 11px; float: right; }
			.thessnest-ical-export { background: #f0f6fc; border: 1px solid #c3d9ed; border-radius: 4px; padding: 8px; margin-bottom: 10px; font-size: 11px; word-break: break-all; }
			.thessnest-ical-sync-status { font-size: 11px; color: #646970; margin: 6px 0; }
			#thessnest-sync-result { margin-top: 8px; padding: 6px 8px; border-radius: 3px; font-size: 11px; display: none; }
		</style>

		<!-- Export URL -->
		<p style="margin:0 0 6px; font-weight:600; font-size:12px;"><?php esc_html_e( '📤 Export URL', 'thessnest' ); ?></p>
		<div class="thessnest-ical-export">
			<input type="text" value="<?php echo esc_url( $export_url ); ?>" readonly onclick="this.select()" style="width:100%;border:none;background:transparent;font-size:11px;">
			<small><?php esc_html_e( 'Paste this URL into Airbnb / Booking.com to export ThessNest bookings.', 'thessnest' ); ?></small>
		</div>

		<!-- Import Feeds -->
		<p style="margin:0 0 6px; font-weight:600; font-size:12px;"><?php esc_html_e( '📥 Import Feeds', 'thessnest' ); ?></p>
		<div id="thessnest-ical-feeds-container">
			<?php foreach ( $feeds as $index => $feed ) : ?>
				<div class="thessnest-ical-feed">
					<button type="button" class="btn-remove-ical">✕</button>
					<input type="text" name="thessnest_ical_feeds[<?php echo $index; ?>][name]" placeholder="<?php esc_attr_e( 'Feed Name (e.g. Airbnb)', 'thessnest' ); ?>" value="<?php echo esc_attr( $feed['name'] ); ?>">
					<input type="url" name="thessnest_ical_feeds[<?php echo $index; ?>][url]" placeholder="<?php esc_attr_e( 'https://...ical/...', 'thessnest' ); ?>" value="<?php echo esc_url( $feed['url'] ); ?>">
				</div>
			<?php endforeach; ?>
		</div>

		<button type="button" id="thessnest-btn-add-ical-feed" class="button button-small" style="margin-top:4px;">+ <?php esc_html_e( 'Add Feed', 'thessnest' ); ?></button>

		<!-- Last Sync -->
		<div class="thessnest-ical-sync-status">
			<?php esc_html_e( 'Last synced:', 'thessnest' ); ?>
			<strong id="thessnest-last-sync-time">
				<?php
				if ( $last_sync ) {
					echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $last_sync ) );
				} else {
					esc_html_e( 'Never', 'thessnest' );
				}
				?>
			</strong>
		</div>

		<!-- Sync Now Button -->
		<?php if ( $post->post_status === 'publish' ) : ?>
		<button type="button" id="thessnest-btn-sync-now" class="button button-primary button-small" style="margin-top:4px;width:100%;">
			🔄 <?php esc_html_e( 'Sync Now', 'thessnest' ); ?>
		</button>
		<div id="thessnest-sync-result"></div>
		<?php endif; ?>

		<script>
		document.addEventListener('DOMContentLoaded', function() {
			var container = document.getElementById('thessnest-ical-feeds-container');
			var btnAdd = document.getElementById('thessnest-btn-add-ical-feed');
			var feedIndex = <?php echo count( $feeds ); ?>;

			// Add Feed
			if (btnAdd) {
				btnAdd.addEventListener('click', function(e) {
					e.preventDefault();
					var html = '<div class="thessnest-ical-feed">' +
						'<button type="button" class="btn-remove-ical">✕</button>' +
						'<input type="text" name="thessnest_ical_feeds[' + feedIndex + '][name]" placeholder="<?php echo esc_js( __( 'Feed Name (e.g. Airbnb)', 'thessnest' ) ); ?>">' +
						'<input type="url" name="thessnest_ical_feeds[' + feedIndex + '][url]" placeholder="<?php echo esc_js( __( 'https://...ical/...', 'thessnest' ) ); ?>">' +
						'</div>';
					container.insertAdjacentHTML('beforeend', html);
					feedIndex++;
				});
			}

			// Remove Feed
			container.addEventListener('click', function(e) {
				if (e.target.classList.contains('btn-remove-ical')) {
					e.target.closest('.thessnest-ical-feed').remove();
				}
			});

			// Sync Now
			var btnSync = document.getElementById('thessnest-btn-sync-now');
			if (btnSync) {
				btnSync.addEventListener('click', function(e) {
					e.preventDefault();
					var resultEl = document.getElementById('thessnest-sync-result');
					btnSync.disabled = true;
					btnSync.textContent = '⏳ <?php echo esc_js( __( 'Syncing...', 'thessnest' ) ); ?>';
					resultEl.style.display = 'none';

					var formData = new FormData();
					formData.append('action', 'thessnest_ical_sync_now');
					formData.append('property_id', '<?php echo intval( $post->ID ); ?>');
					formData.append('security', '<?php echo wp_create_nonce( 'thessnest_ical_sync_nonce' ); ?>');

					fetch(ajaxurl, { method: 'POST', body: formData })
						.then(function(r) { return r.json(); })
						.then(function(resp) {
							if (resp.success) {
								resultEl.style.display = 'block';
								resultEl.style.background = '#ecfdf5';
								resultEl.style.color = '#065f46';
								resultEl.textContent = resp.data.message;
								document.getElementById('thessnest-last-sync-time').textContent = resp.data.last_sync;
							} else {
								resultEl.style.display = 'block';
								resultEl.style.background = '#fef2f2';
								resultEl.style.color = '#991b1b';
								resultEl.textContent = resp.data.message || 'Error';
							}
						})
						.catch(function() {
							resultEl.style.display = 'block';
							resultEl.style.background = '#fef2f2';
							resultEl.style.color = '#991b1b';
							resultEl.textContent = 'Network error.';
						})
						.finally(function() {
							btnSync.disabled = false;
							btnSync.textContent = '🔄 <?php echo esc_js( __( 'Sync Now', 'thessnest' ) ); ?>';
						});
				});
			}
		});
		</script>
		<?php
	}

	/**
	 * Save meta box data.
	 */
	public function save_property_meta_boxes( $post_id ) {
		if ( ! isset( $_POST['thessnest_pricing_meta_nonce'] ) || ! wp_verify_nonce( $_POST['thessnest_pricing_meta_nonce'], 'thessnest_save_pricing_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$fields = array(
			'thessnest_rent'               => '_thessnest_rent',
			'thessnest_utilities'          => '_thessnest_utilities',
			'thessnest_deposit'            => '_thessnest_deposit',
			'thessnest_min_stay'           => '_thessnest_min_stay',
			'thessnest_cleaning_fee'       => '_thessnest_cleaning_fee',
			'thessnest_service_fee'        => '_thessnest_service_fee',
			'thessnest_weekly_discount'    => '_thessnest_weekly_discount',
			'thessnest_monthly_discount'   => '_thessnest_monthly_discount',
			'thessnest_quarterly_discount' => '_thessnest_quarterly_discount',
			'thessnest_early_bird_days'    => '_thessnest_early_bird_days',
			'thessnest_early_bird_discount'=> '_thessnest_early_bird_discount',
		);

		foreach ( $fields as $post_key => $meta_key ) {
			if ( isset( $_POST[ $post_key ] ) ) {
				update_post_meta( $post_id, $meta_key, floatval( $_POST[ $post_key ] ) );
			}
		}

		if ( isset( $_POST['thessnest_cleaning_fee_type'] ) ) {
			update_post_meta( $post_id, '_thessnest_cleaning_fee_type', sanitize_text_field( $_POST['thessnest_cleaning_fee_type'] ) );
		}

		// Save Seasonal Rates Repeater
		$seasonal_rates = array();
		if ( isset( $_POST['thessnest_season'] ) && is_array( $_POST['thessnest_season'] ) ) {
			$starts = $_POST['thessnest_season']['start'] ?? array();
			$ends   = $_POST['thessnest_season']['end'] ?? array();
			$rates  = $_POST['thessnest_season']['rate'] ?? array();

			foreach ( $starts as $index => $start_date ) {
				if ( ! empty( $start_date ) && ! empty( $ends[ $index ] ) && ! empty( $rates[ $index ] ) ) {
					$seasonal_rates[] = array(
						'start' => sanitize_text_field( $start_date ),
						'end'   => sanitize_text_field( $ends[ $index ] ),
						'rate'  => floatval( $rates[ $index ] ),
					);
				}
			}
		}
		update_post_meta( $post_id, '_thessnest_seasonal_rates', $seasonal_rates );

		// Save iCal Feeds (if nonce present — separate meta box)
		if ( isset( $_POST['thessnest_ical_feeds_nonce'] ) && wp_verify_nonce( $_POST['thessnest_ical_feeds_nonce'], 'thessnest_save_ical_feeds' ) ) {
			$ical_feeds = array();
			if ( isset( $_POST['thessnest_ical_feeds'] ) && is_array( $_POST['thessnest_ical_feeds'] ) ) {
				foreach ( $_POST['thessnest_ical_feeds'] as $feed ) {
					$url  = isset( $feed['url'] ) ? esc_url_raw( $feed['url'] ) : '';
					$name = isset( $feed['name'] ) ? sanitize_text_field( $feed['name'] ) : '';
					if ( ! empty( $url ) ) {
						$ical_feeds[] = array( 'name' => $name, 'url' => $url );
					}
				}
			}
			update_post_meta( $post_id, '_thessnest_ical_feeds', $ical_feeds );

			// Clean up legacy meta if new feeds are saved
			if ( ! empty( $ical_feeds ) ) {
				delete_post_meta( $post_id, '_thessnest_ical_import_url' );
			}
		}
	}
}

new ThessNest_Admin_Meta_Boxes();
