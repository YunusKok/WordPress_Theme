<?php
/**
 * Template Name: Add Listing
 *
 * Frontend property submission form.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

// Redirect if not logged in
if ( ! is_user_logged_in() ) {
	wp_redirect( wp_login_url( home_url( '/add-listing/' ) ) );
	exit;
}

get_header();
?>

<main id="main-content" role="main" style="background:var(--color-surface); min-height:80vh; padding:var(--space-10) 0;">
	<div class="container" style="max-width: 800px; margin: 0 auto;">
		
		<header style="margin-bottom:var(--space-8); text-align:center;">
			<h1 style="color:var(--color-primary); font-size:var(--font-size-3xl); margin-bottom:var(--space-2);">
				<?php esc_html_e( 'Add New Property', 'thessnest' ); ?>
			</h1>
			<p style="color:var(--color-text-muted);">
				<?php esc_html_e( 'Fill out the form below to list your property. It will be reviewed by our team before going live.', 'thessnest' ); ?>
			</p>
		</header>

		<div style="background:var(--color-background); border:1px solid var(--color-border); border-radius:var(--radius-xl); padding:var(--space-8); box-shadow:var(--shadow-sm);">
			<form id="add-listing-form" enctype="multipart/form-data">
				<input type="hidden" name="action" value="thessnest_submit_listing">
				<?php wp_nonce_field( 'thessnest_add_listing_nonce', 'security' ); ?>

				<!-- Title -->
				<div class="form-group" style="margin-bottom:var(--space-6);">
					<label for="listing_title" style="display:block; margin-bottom:var(--space-2); font-weight:600; color:var(--color-text);">
						<?php esc_html_e( 'Property Title *', 'thessnest' ); ?>
					</label>
					<input type="text" id="listing_title" name="listing_title" required style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
				</div>

				<!-- Description -->
				<div class="form-group" style="margin-bottom:var(--space-6);">
					<label for="listing_description" style="display:block; margin-bottom:var(--space-2); font-weight:600; color:var(--color-text);">
						<?php esc_html_e( 'Description *', 'thessnest' ); ?>
					</label>
					<textarea id="listing_description" name="listing_description" rows="5" required style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text); resize:vertical;"></textarea>
				</div>

				<!-- Pricing & Basic Info Grid -->
				<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:var(--space-6); margin-bottom:var(--space-6);">
					
					<!-- Rent -->
					<div class="form-group">
						<label for="listing_rent" style="display:block; margin-bottom:var(--space-2); font-weight:600; color:var(--color-text);">
							<?php esc_html_e( 'Monthly Rent (€) *', 'thessnest' ); ?>
						</label>
						<input type="number" id="listing_rent" name="listing_rent" min="0" required style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
					</div>

					<!-- Utilities -->
					<div class="form-group">
						<label for="listing_utilities" style="display:block; margin-bottom:var(--space-2); font-weight:600; color:var(--color-text);">
							<?php esc_html_e( 'Utilities Cost (€)', 'thessnest' ); ?>
						</label>
						<input type="number" id="listing_utilities" name="listing_utilities" min="0" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
					</div>

					<!-- Deposit -->
					<div class="form-group">
						<label for="listing_deposit" style="display:block; margin-bottom:var(--space-2); font-weight:600; color:var(--color-text);">
							<?php esc_html_e( 'Security Deposit (€)', 'thessnest' ); ?>
						</label>
						<input type="number" id="listing_deposit" name="listing_deposit" min="0" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
					</div>

					<!-- Wi-Fi Speed -->
					<div class="form-group">
						<label for="listing_wifi_speed" style="display:block; margin-bottom:var(--space-2); font-weight:600; color:var(--color-text);">
							<?php esc_html_e( 'Wi-Fi Speed (Mbps)', 'thessnest' ); ?>
						</label>
						<input type="number" id="listing_wifi_speed" name="listing_wifi_speed" min="0" placeholder="e.g. 50" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
					</div>

				<!-- Max Tenants (for Split Payment) -->
				<div class="form-group">
					<label for="listing_max_tenants" style="display:block; margin-bottom:var(--space-2); font-weight:600; color:var(--color-text);">
						<?php esc_html_e( 'Max Tenants', 'thessnest' ); ?>
					</label>
					<input type="number" id="listing_max_tenants" name="listing_max_tenants" min="1" max="10" value="1" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
				</div>

				<!-- Instant Book Toggle -->
				<div class="form-group">
					<label style="display:block; margin-bottom:var(--space-2); font-weight:600; color:var(--color-text);">
						<?php esc_html_e( 'Booking Type', 'thessnest' ); ?>
					</label>
					<label style="display:flex; align-items:center; gap:var(--space-2); cursor:pointer; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface);">
						<input type="checkbox" name="listing_instant_book" value="1">
						<span style="font-size:var(--font-size-sm);">⚡ <?php esc_html_e( 'Enable Instant Book', 'thessnest' ); ?></span>
					</label>
					<p style="font-size:12px;color:var(--color-text-muted);margin-top:var(--space-1);">
						<?php esc_html_e( 'If unchecked, tenants will send a booking request that you approve manually.', 'thessnest' ); ?>
					</p>
				</div>

				<!-- iCal External Calendar Import -->
				<div class="form-group">
					<label for="listing_ical_url" style="display:block; margin-bottom:var(--space-2); font-weight:600; color:var(--color-text);">
						<?php esc_html_e( 'External iCal Import URL (.ics) - Optional', 'thessnest' ); ?>
					</label>
					<input type="url" id="listing_ical_url" name="listing_ical_url" placeholder="https://www.airbnb.com/calendar/ical/..." style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
					<p style="font-size:12px;color:var(--color-text-muted);margin-top:var(--space-1);">
						<?php esc_html_e( 'Paste your Airbnb or Booking.com calendar sync link here to prevent double bookings.', 'thessnest' ); ?>
					</p>
				</div>

				<!-- Neighborhood -->
					<div class="form-group">
						<label for="listing_neighborhood" style="display:block; margin-bottom:var(--space-2); font-weight:600; color:var(--color-text);">
							<?php esc_html_e( 'Neighborhood *', 'thessnest' ); ?>
						</label>
						<select id="listing_neighborhood" name="listing_neighborhood" required style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
							<option value=""><?php esc_html_e( 'Select Neighborhood', 'thessnest' ); ?></option>
							<?php
							$neighborhoods = get_terms( array( 'taxonomy' => 'neighborhood', 'hide_empty' => false ) );
							if ( ! is_wp_error( $neighborhoods ) ) :
								foreach ( $neighborhoods as $term ) :
									echo '<option value="' . esc_attr( $term->term_id ) . '">' . esc_html( $term->name ) . '</option>';
								endforeach;
							endif;
							?>
						</select>
					</div>

				</div>

				<!-- ========================================== -->
				<!-- ADVANCED PRICING ENGINE FIELDS             -->
				<!-- ========================================== -->
				<div style="background:var(--color-surface); border:1px solid var(--color-border); border-radius:var(--radius-md); padding:var(--space-6); margin-bottom:var(--space-6);">
					<h3 style="margin-top:0; margin-bottom:var(--space-4); font-size:var(--font-size-xl); color:var(--color-text);">
						<?php esc_html_e( 'Advanced Pricing Rules', 'thessnest' ); ?>
					</h3>
					
					<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:var(--space-4); margin-bottom:var(--space-6);">
						<!-- Minimum Stay -->
						<div class="form-group">
							<label for="thessnest_min_stay" style="display:block; margin-bottom:var(--space-2); font-weight:600;">
								<?php esc_html_e( 'Minimum Stay (Nights)', 'thessnest' ); ?>
							</label>
							<input type="number" id="thessnest_min_stay" name="thessnest_min_stay" min="1" value="1" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md);">
						</div>

						<!-- Cleaning Fee -->
						<div class="form-group">
							<label for="thessnest_cleaning_fee" style="display:block; margin-bottom:var(--space-2); font-weight:600;">
								<?php esc_html_e( 'Cleaning Fee (€)', 'thessnest' ); ?>
							</label>
							<input type="number" id="thessnest_cleaning_fee" name="thessnest_cleaning_fee" min="0" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md);">
						</div>

						<!-- Cleaning Fee Type -->
						<div class="form-group">
							<label for="thessnest_cleaning_fee_type" style="display:block; margin-bottom:var(--space-2); font-weight:600;">
								<?php esc_html_e( 'Cleaning Fee Type', 'thessnest' ); ?>
							</label>
							<select id="thessnest_cleaning_fee_type" name="thessnest_cleaning_fee_type" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md);">
								<option value="single"><?php esc_html_e( 'Single (Once per stay)', 'thessnest' ); ?></option>
								<option value="monthly"><?php esc_html_e( 'Monthly (Charged per month)', 'thessnest' ); ?></option>
								<option value="on-demand"><?php esc_html_e( 'On-Demand (Per request, not in total)', 'thessnest' ); ?></option>
							</select>
						</div>

						<!-- Service Fee -->
						<div class="form-group">
							<label for="thessnest_service_fee" style="display:block; margin-bottom:var(--space-2); font-weight:600;">
								<?php esc_html_e( 'Additional Service Fee (€)', 'thessnest' ); ?>
							</label>
							<input type="number" id="thessnest_service_fee" name="thessnest_service_fee" min="0" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md);">
						</div>
					</div>

					<hr style="border:0; border-top:1px solid var(--color-border); margin:var(--space-6) 0;">

					<h4 style="margin-top:0; margin-bottom:var(--space-4); color:var(--color-text);"><?php esc_html_e( 'Discounts', 'thessnest' ); ?></h4>
					<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:var(--space-4); margin-bottom:var(--space-6);">
						<!-- Weekly Discount -->
						<div class="form-group">
							<label style="display:block; margin-bottom:var(--space-2); font-size:var(--font-size-sm);">
								<?php esc_html_e( 'Weekly Discount (%) - 7+ days', 'thessnest' ); ?>
							</label>
							<input type="number" name="thessnest_weekly_discount" min="0" max="100" style="width:100%; padding:var(--space-2); border:1px solid var(--color-border); border-radius:var(--radius-md);">
						</div>
						<!-- Monthly Discount -->
						<div class="form-group">
							<label style="display:block; margin-bottom:var(--space-2); font-size:var(--font-size-sm);">
								<?php esc_html_e( 'Monthly Discount (%) - 28+ days', 'thessnest' ); ?>
							</label>
							<input type="number" name="thessnest_monthly_discount" min="0" max="100" style="width:100%; padding:var(--space-2); border:1px solid var(--color-border); border-radius:var(--radius-md);">
						</div>
						<!-- Quarterly Discount -->
						<div class="form-group">
							<label style="display:block; margin-bottom:var(--space-2); font-size:var(--font-size-sm);">
								<?php esc_html_e( 'Quarterly Discount (%) - 90+ days', 'thessnest' ); ?>
							</label>
							<input type="number" name="thessnest_quarterly_discount" min="0" max="100" style="width:100%; padding:var(--space-2); border:1px solid var(--color-border); border-radius:var(--radius-md);">
						</div>
					</div>

					<!-- Early Bird Discount -->
					<div style="display:flex; gap:var(--space-4); margin-bottom:var(--space-6);">
						<div class="form-group" style="flex:1;">
							<label style="display:block; margin-bottom:var(--space-2); font-size:var(--font-size-sm);">
								<?php esc_html_e( 'Early Bird: Days in Advance', 'thessnest' ); ?>
							</label>
							<input type="number" name="thessnest_early_bird_days" min="0" placeholder="e.g. 60" style="width:100%; padding:var(--space-2); border:1px solid var(--color-border); border-radius:var(--radius-md);">
						</div>
						<div class="form-group" style="flex:1;">
							<label style="display:block; margin-bottom:var(--space-2); font-size:var(--font-size-sm);">
								<?php esc_html_e( 'Early Bird: Discount (%)', 'thessnest' ); ?>
							</label>
							<input type="number" name="thessnest_early_bird_discount" min="0" max="100" style="width:100%; padding:var(--space-2); border:1px solid var(--color-border); border-radius:var(--radius-md);">
						</div>
					</div>

					<hr style="border:0; border-top:1px solid var(--color-border); margin:var(--space-6) 0;">

					<!-- Seasonal Rates Repeater -->
					<h4 style="margin-top:0; margin-bottom:var(--space-2); color:var(--color-text);"><?php esc_html_e( 'Seasonal Rates', 'thessnest' ); ?></h4>
					<p style="font-size:var(--font-size-sm); color:var(--color-text-muted); margin-bottom:var(--space-4);">
						<?php esc_html_e( 'Define specific nightly rates for certain periods (e.g. High Season, Holidays). These override the base rate.', 'thessnest' ); ?>
					</p>

					<div id="seasonal-rates-container">
						<!-- Rows injected by JS -->
					</div>
					
					<button type="button" id="btn-add-season" class="btn btn-outline" style="margin-top:var(--space-2); padding:var(--space-2) var(--space-4); font-size:var(--font-size-sm);">
						+ <?php esc_html_e( 'Add Seasonal Rate', 'thessnest' ); ?>
					</button>

					<!-- Season Template (Hidden) -->
					<template id="season-template">
						<div class="season-row" style="display:flex; gap:var(--space-3); align-items:flex-end; margin-bottom:var(--space-3); background:var(--color-background); padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-sm);">
							<div style="flex:1;">
								<label style="display:block; font-size:12px; margin-bottom:4px;"><?php esc_html_e('Start Date', 'thessnest'); ?></label>
								<input type="date" name="thessnest_season[start][]" required style="width:100%; padding:var(--space-2); border:1px solid var(--color-border); border-radius:var(--radius-sm);">
							</div>
							<div style="flex:1;">
								<label style="display:block; font-size:12px; margin-bottom:4px;"><?php esc_html_e('End Date', 'thessnest'); ?></label>
								<input type="date" name="thessnest_season[end][]" required style="width:100%; padding:var(--space-2); border:1px solid var(--color-border); border-radius:var(--radius-sm);">
							</div>
							<div style="flex:1;">
								<label style="display:block; font-size:12px; margin-bottom:4px;"><?php esc_html_e('Rate (€)', 'thessnest'); ?></label>
								<input type="number" name="thessnest_season[rate][]" min="0" required style="width:100%; padding:var(--space-2); border:1px solid var(--color-border); border-radius:var(--radius-sm);">
							</div>
							<button type="button" class="btn-remove-season" style="background:#ef4444; color:white; border:none; border-radius:var(--radius-sm); padding:var(--space-2) 12px; cursor:pointer;" title="<?php esc_html_e('Remove', 'thessnest'); ?>">✕</button>
						</div>
					</template>
					
					<script>
					document.addEventListener('DOMContentLoaded', function() {
						const container = document.getElementById('seasonal-rates-container');
						const btnAdd = document.getElementById('btn-add-season');
						const template = document.getElementById('season-template');

						btnAdd.addEventListener('click', function() {
							const clone = template.content.cloneNode(true);
							container.appendChild(clone);
						});

						container.addEventListener('click', function(e) {
							if (e.target.classList.contains('btn-remove-season')) {
								e.target.closest('.season-row').remove();
							}
						});
					});
					</script>
				</div>
				<!-- ========================================== -->

				<!-- Amenities -->
				<div class="form-group" style="margin-bottom:var(--space-6);">
					<label style="display:block; margin-bottom:var(--space-2); font-weight:600; color:var(--color-text);">
						<?php esc_html_e( 'Amenities', 'thessnest' ); ?>
					</label>
					<div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap:var(--space-3);">
						<?php
						$amenities = get_terms( array( 'taxonomy' => 'amenity', 'hide_empty' => false ) );
						if ( ! is_wp_error( $amenities ) ) :
							foreach ( $amenities as $term ) :
								?>
								<label style="display:flex; align-items:center; gap:var(--space-2); cursor:pointer;">
									<input type="checkbox" name="listing_amenities[]" value="<?php echo esc_attr( $term->term_id ); ?>">
									<span><?php echo esc_html( $term->name ); ?></span>
								</label>
								<?php
							endforeach;
						endif;
						?>
					</div>
				</div>

				<!-- Target Group -->
				<div class="form-group" style="margin-bottom:var(--space-6);">
					<label style="display:block; margin-bottom:var(--space-2); font-weight:600; color:var(--color-text);">
						<?php esc_html_e( 'Target Group (Optional)', 'thessnest' ); ?>
					</label>
					<div style="display:flex; gap:var(--space-4); flex-wrap:wrap;">
						<?php
						$groups = get_terms( array( 'taxonomy' => 'target_group', 'hide_empty' => false ) );
						if ( ! is_wp_error( $groups ) ) :
							foreach ( $groups as $term ) :
								?>
								<label style="display:flex; align-items:center; gap:var(--space-2); cursor:pointer;">
									<input type="checkbox" name="listing_target_groups[]" value="<?php echo esc_attr( $term->term_id ); ?>">
									<span><?php echo esc_html( $term->name ); ?></span>
								</label>
								<?php
							endforeach;
						endif;
						?>
					</div>
				</div>

				<!-- Image Upload -->
				<div class="form-group" style="margin-bottom:var(--space-8);">
					<label for="listing_images" style="display:block; margin-bottom:var(--space-2); font-weight:600; color:var(--color-text);">
						<?php esc_html_e( 'Property Images (First image will be cover) *', 'thessnest' ); ?>
					</label>
					<input type="file" id="listing_images" name="listing_images[]" accept="image/*" multiple required style="width:100%; padding:var(--space-3); border:1px dashed var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text); cursor:pointer;">
					<p style="font-size:var(--font-size-sm); color:var(--color-text-muted); margin-top:var(--space-2);">
						<?php esc_html_e( 'You can select multiple images (Max 5MB each).', 'thessnest' ); ?>
					</p>
				</div>

				<!-- Feedback Message Container -->
				<div id="listing-form-response" style="margin-bottom:var(--space-4); padding:var(--space-4); border-radius:var(--radius-md); display:none; font-weight:500;"></div>

				<!-- Submit -->
				<button type="submit" id="listing-submit-btn" class="btn btn-primary" style="width:100%; justify-content:center; padding:var(--space-4); font-size:var(--font-size-lg);">
					<?php esc_html_e( 'Submit Listing', 'thessnest' ); ?>
				</button>
			</form>
		</div>

	</div>
</main>

<?php get_footer(); ?>
