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
