<?php
/**
 * ThessNest — Single Property Template
 *
 * Renders a single property listing page with details, gallery, and meta info.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main-content" role="main">

	<?php
	while ( have_posts() ) :
		the_post();

		$property_id = get_the_ID();
		$rent        = thessnest_get_meta( 'rent', $property_id );
		$utilities   = thessnest_get_meta( 'utilities', $property_id );
		$deposit     = thessnest_get_meta( 'deposit', $property_id );
		$wifi_speed  = thessnest_get_meta( 'wifi_speed', $property_id );
		$max_tenants = (int) thessnest_get_meta( 'max_tenants', $property_id );
		if ( $max_tenants < 1 ) { $max_tenants = 1; }

		// Check if the property author (landlord) is KYC verified
		$author_id   = get_post_field( 'post_author', $property_id );
		$is_verified = get_user_meta( $author_id, '_kyc_status', true ) === 'approved';

		$gallery_ids = thessnest_get_gallery( $property_id );
		$neighborhood = thessnest_get_first_term( 'neighborhood', $property_id );
	?>

	<!-- Property Header -->
	<section style="background:var(--color-surface);padding:var(--space-10) var(--space-4);border-bottom:1px solid var(--color-border);">
		<div class="container">
			<?php
			if ( function_exists( 'thessnest_breadcrumbs' ) ) {
				thessnest_breadcrumbs();
			}
			?>
			<div style="display:flex;align-items:center;gap:var(--space-3);margin-bottom:var(--space-3);flex-wrap:wrap;">
				<?php if ( $is_verified ) :
					$kyc_date = get_user_meta( $author_id, '_kyc_approved_date', true );
					$tooltip  = $kyc_date ? sprintf( __( 'Verified on %s by ThessNest', 'thessnest' ), date_i18n( get_option('date_format'), strtotime( $kyc_date ) ) ) : __( 'Physically verified by ThessNest team', 'thessnest' );
				?>
					<span class="badge badge-verified" data-tooltip="<?php echo esc_attr( $tooltip ); ?>">
						<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
						</svg>
						<?php esc_html_e( 'Verified', 'thessnest' ); ?>
					</span>
				<?php endif; ?>

				<?php if ( $wifi_speed ) : ?>
					<span class="badge badge-wifi">
						<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M5 12.55a11 11 0 0 1 14.08 0"/>
							<path d="M1.42 9a16 16 0 0 1 21.16 0"/>
							<path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
							<line x1="12" y1="20" x2="12.01" y2="20"/>
						</svg>
						<?php echo esc_html( $wifi_speed ); ?> Mbps
					</span>
				<?php endif; ?>
			</div>

			<h1 style="font-size:var(--font-size-3xl);margin-bottom:var(--space-2);color:var(--color-primary);display:flex;align-items:center;gap:var(--space-3);flex-wrap:wrap;">
				<?php the_title(); ?>
				<?php
				$average_rating = get_post_meta( $property_id, '_thessnest_average_rating', true );
				$review_count   = get_post_meta( $property_id, '_thessnest_review_count', true );
				if ( $average_rating ) :
				?>
					<span class="badge badge-rating" style="background:var(--color-surface); color:var(--color-primary); border:1px solid var(--color-border); font-size:var(--font-size-base); padding:var(--space-1) var(--space-3);">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="var(--color-accent)" stroke="var(--color-accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="margin-right:2px;">
							<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
						</svg>
						<?php echo esc_html( $average_rating ); ?> 
						<span style="color:var(--color-text-muted); font-weight:normal; font-size:var(--font-size-sm); margin-left:2px;">(<?php echo esc_html( $review_count ); ?>)</span>
					</span>
				<?php endif; ?>
			</h1>

			<?php if ( $neighborhood ) : ?>
				<p class="card-location" style="font-size:var(--font-size-base);">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
					</svg>
					<?php echo esc_html( $neighborhood ); ?>, Thessaloniki
				</p>
			<?php endif; ?>
		</div>
	</section>

	<div class="container" style="padding:var(--space-10) var(--space-4);">
		<div style="display:grid;grid-template-columns:1fr;gap:var(--space-8);">

			<!-- Gallery -->
			<?php if ( ! empty( $gallery_ids ) || has_post_thumbnail() ) : ?>
				<div class="card-carousel" style="border-radius:var(--radius-lg);overflow:hidden;max-height:500px;">
					<div class="swiper property-swiper">
						<div class="swiper-wrapper">
							<?php
							if ( ! empty( $gallery_ids ) ) :
								foreach ( $gallery_ids as $image_id ) :
									$img_url = wp_get_attachment_image_url( $image_id, 'large' );
									$img_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
									if ( $img_url ) : ?>
										<div class="swiper-slide">
											<img src="<?php echo esc_url( $img_url ); ?>"
											     alt="<?php echo esc_attr( $img_alt ? $img_alt : get_the_title() ); ?>"
											     loading="lazy"
											     style="width:100%;height:100%;object-fit:cover;">
										</div>
									<?php endif;
								endforeach;
							elseif ( has_post_thumbnail() ) : ?>
								<div class="swiper-slide">
									<?php the_post_thumbnail( 'large', array(
										'style' => 'width:100%;height:100%;object-fit:cover;',
									) ); ?>
								</div>
							<?php endif; ?>
						</div>
						<div class="swiper-pagination"></div>
					</div>
				</div>
			<?php endif; ?>

			<!-- Content + Pricing -->
			<div style="display:grid;grid-template-columns:1fr;gap:var(--space-8);">

				<!-- Description -->
				<div class="entry-content" style="line-height:1.8;color:var(--color-text);">
					<?php the_content(); ?>
				</div>

				<!-- Pricing Card -->
				<?php if ( $rent ) : ?>
					<div style="background:var(--color-background);padding:var(--space-6);border-radius:var(--radius-xl);border:1px solid var(--color-border);box-shadow:var(--shadow-card-hover);">
						<h3 style="font-size:var(--font-size-lg);margin-bottom:var(--space-4);color:var(--color-primary);">
							<?php esc_html_e( 'Pricing', 'thessnest' ); ?>
						</h3>

						<div style="display:flex;justify-content:space-between;align-items:center;padding:var(--space-3) 0;border-bottom:1px solid var(--color-border);">
							<span><?php esc_html_e( 'Monthly Rent', 'thessnest' ); ?></span>
							<strong style="color:var(--color-primary);"><?php echo esc_html( thessnest_format_price( $rent ) ); ?></strong>
						</div>

						<?php if ( $utilities ) : ?>
							<div style="display:flex;justify-content:space-between;align-items:center;padding:var(--space-3) 0;border-bottom:1px solid var(--color-border);">
								<span><?php esc_html_e( 'Utilities', 'thessnest' ); ?></span>
								<strong style="color:var(--color-text-muted);"><?php echo esc_html( thessnest_format_price( $utilities ) ); ?></strong>
							</div>
						<?php endif; ?>

						<?php if ( $deposit ) : ?>
							<div style="display:flex;justify-content:space-between;align-items:center;padding:var(--space-3) 0;border-bottom:1px solid var(--color-border);">
								<span><?php esc_html_e( 'Security Deposit (one-time)', 'thessnest' ); ?></span>
								<strong style="color:var(--color-text-muted);"><?php echo esc_html( thessnest_format_price( $deposit ) ); ?></strong>
							</div>
						<?php endif; ?>

						<?php if ( $utilities ) : ?>
							<div style="display:flex;justify-content:space-between;align-items:center;padding:var(--space-4) 0;font-size:var(--font-size-lg);">
								<strong><?php esc_html_e( 'Total / month', 'thessnest' ); ?></strong>
								<strong style="color:var(--color-accent);font-size:var(--font-size-xl);">
									<?php echo esc_html( thessnest_format_price( (int) $rent + (int) $utilities ) ); ?>
								</strong>
							</div>
						<?php endif; ?>

						<!-- No Platform Fees Trust Badge -->
						<div style="display:flex;align-items:center;gap:var(--space-2);padding:var(--space-3);background:rgba(72,187,120,0.08);border-radius:var(--radius-md);margin-top:var(--space-3);">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#48bb78" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>
							</svg>
							<span style="font-size:var(--font-size-sm);font-weight:600;color:#2f855a;">
								<?php esc_html_e( 'No platform fees — what you see is what you pay', 'thessnest' ); ?>
							</span>
						</div>

					<?php if ( $max_tenants > 1 ) : ?>
					<!-- Split Cost Calculator -->
					<div style="margin-top:var(--space-4);padding:var(--space-4);background:rgba(66,153,225,0.06);border:1px solid rgba(66,153,225,0.15);border-radius:var(--radius-md);">
						<div style="display:flex;align-items:center;gap:var(--space-2);margin-bottom:var(--space-3);">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4299e1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
							</svg>
							<strong style="font-size:var(--font-size-sm);color:#2b6cb0;">
								<?php printf( esc_html__( 'Split between %d tenants', 'thessnest' ), $max_tenants ); ?>
							</strong>
						</div>
						<?php
						$total_monthly  = (int) $rent + (int) $utilities;
						$per_person     = ceil( $total_monthly / $max_tenants );
						$per_person_dep = $deposit ? ceil( (int) $deposit / $max_tenants ) : 0;
						?>
						<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--space-2);">
							<span style="font-size:var(--font-size-sm);color:var(--color-text);"><?php esc_html_e( 'Per person / month', 'thessnest' ); ?></span>
							<strong style="color:#2b6cb0;font-size:var(--font-size-lg);"><?php echo esc_html( thessnest_format_price( $per_person ) ); ?></strong>
						</div>
						<?php if ( $per_person_dep > 0 ) : ?>
						<div style="display:flex;justify-content:space-between;align-items:center;">
							<span style="font-size:var(--font-size-sm);color:var(--color-text-muted);"><?php esc_html_e( 'Deposit per person', 'thessnest' ); ?></span>
							<span style="font-size:var(--font-size-sm);color:var(--color-text-muted);"><?php echo esc_html( thessnest_format_price( $per_person_dep ) ); ?></span>
						</div>
						<?php endif; ?>
					</div>
					<?php endif; ?>

						<div id="booking-section" class="property-booking-box" data-price-per-night="<?php echo esc_attr( $rent ? $rent : 0 ); ?>" style="margin-top:var(--space-6); background:var(--glass-bg); padding:var(--space-6); border-radius:var(--radius-xl); border:1px solid var(--color-border); box-shadow:var(--shadow-sm);">
							<?php
							$is_instant_book = get_post_meta( $property_id, '_thessnest_instant_book', true ) === '1';
							?>
							<h3 style="font-size:var(--font-size-lg);margin-bottom:var(--space-4);color:var(--color-primary);">
								<?php if ( $is_instant_book ) : ?>
									⚡ <?php esc_html_e( 'Instant Book', 'thessnest' ); ?>
								<?php else : ?>
									<?php esc_html_e( 'Request to Book', 'thessnest' ); ?>
								<?php endif; ?>
							</h3>
							
							<?php if ( is_user_logged_in() && get_current_user_id() == $property->post_author ) : ?>
								<p style="color:var(--color-text-muted); text-align:center; padding:var(--space-4); background:var(--color-surface); border-radius:var(--radius-md);">
									<?php esc_html_e( 'This is your property. You cannot book it.', 'thessnest' ); ?>
								</p>
							<?php else : ?>
								<form id="property-booking-form" class="booking-form">
									<input type="hidden" name="action" value="thessnest_submit_booking">
									<input type="hidden" name="property_id" id="booking_property_id" value="<?php echo esc_attr( $property_id ); ?>">
									<?php wp_nonce_field( 'thessnest-inquiry-nonce', 'security' ); ?>
									
									<div style="display:flex; gap:var(--space-2); margin-bottom:var(--space-3);">
										<div style="flex:1;">
											<label style="display:block; font-size:var(--font-size-sm); color:var(--color-text-muted); margin-bottom:var(--space-1); font-weight:500;"><?php esc_html_e('Check-in', 'thessnest'); ?></label>
											<input type="text" id="booking_checkin" name="checkin" placeholder="YYYY-MM-DD" required style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-background); color:var(--color-text); cursor:pointer;">
										</div>
										<div style="flex:1;">
											<label style="display:block; font-size:var(--font-size-sm); color:var(--color-text-muted); margin-bottom:var(--space-1); font-weight:500;"><?php esc_html_e('Check-out', 'thessnest'); ?></label>
											<input type="text" id="booking_checkout" name="checkout" placeholder="YYYY-MM-DD" required disabled style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-background); color:var(--color-text); cursor:pointer;">
										</div>
									</div>
									
									<div style="margin-bottom:var(--space-3);">
										<label style="display:block; font-size:var(--font-size-sm); color:var(--color-text-muted); margin-bottom:var(--space-1); font-weight:500;"><?php esc_html_e('Guests', 'thessnest'); ?></label>
										<input type="number" name="guests" min="1" max="10" value="1" required style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-background); color:var(--color-text);">
									</div>

									<div style="margin-bottom:var(--space-4);">
										<textarea name="message" placeholder="<?php esc_attr_e( 'Message to landlord (Optional)...', 'thessnest' ); ?>" rows="2" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-background); color:var(--color-text); resize:vertical;"></textarea>
									</div>
									
									<!-- Dynamic Price Calculation UI -->
									<div id="booking-price-calc" style="display:none; padding-bottom:var(--space-4); margin-bottom:var(--space-4); border-bottom:1px solid var(--color-border);">
										<div style="display:flex; justify-content:space-between; margin-bottom:var(--space-2); color:var(--color-text);">
											<span id="calc-nights-text">0 nights x 0</span>
											<span id="calc-nights-total">0</span>
										</div>
										<div style="display:flex; justify-content:space-between; font-weight:bold; font-size:var(--font-size-lg); color:var(--color-primary);">
											<span><?php esc_html_e('Total', 'thessnest'); ?></span>
											<span id="calc-grand-total">0</span>
										</div>
									</div>

									<button type="submit" id="booking-submit-btn" class="btn btn-primary" style="width:100%; justify-content:center; padding:var(--space-3); font-size:var(--font-size-base);">
										<?php if ( $is_instant_book ) : ?>
											⚡ <?php esc_html_e( 'Book Instantly', 'thessnest' ); ?>
										<?php else : ?>
											<?php esc_html_e( 'Request to Book', 'thessnest' ); ?>
										<?php endif; ?>
									</button>
									<input type="hidden" name="instant_book" value="<?php echo esc_attr( $is_instant_book ? '1' : '0' ); ?>">
									<div id="booking-form-response" style="margin-top:var(--space-3); font-size:var(--font-size-sm); display:none; padding:var(--space-3); border-radius:var(--radius-md);"></div>
								</form>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

				<!-- Amenities -->
				<?php
				$amenities = get_the_terms( $property_id, 'amenity' );
				if ( ! empty( $amenities ) && ! is_wp_error( $amenities ) ) :
				?>
					<div>
						<h3 style="font-size:var(--font-size-lg);margin-bottom:var(--space-4);color:var(--color-primary);">
							<?php esc_html_e( 'Amenities', 'thessnest' ); ?>
						</h3>
						<div style="display:flex;flex-wrap:wrap;gap:var(--space-2);">
							<?php foreach ( $amenities as $amenity ) : ?>
								<span class="category-pill" style="cursor:default;">
									<?php echo esc_html( $amenity->name ); ?>
								</span>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<!-- One-Click Navigation to Maps -->
				<?php
				$prop_lat = get_post_meta( $property_id, '_thessnest_latitude', true );
				$prop_lng = get_post_meta( $property_id, '_thessnest_longitude', true );
				if ( $prop_lat && $prop_lng ) :
				?>
					<div style="margin-top:var(--space-6);">
						<h3 style="font-size:var(--font-size-lg);margin-bottom:var(--space-4);color:var(--color-primary);">
							<?php esc_html_e( 'Location', 'thessnest' ); ?>
						</h3>
						<div style="display:flex;gap:var(--space-3);flex-wrap:wrap;">
							<a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo esc_attr( $prop_lat ); ?>,<?php echo esc_attr( $prop_lng ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:var(--space-2);">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
								<?php esc_html_e( 'Navigate with Google Maps', 'thessnest' ); ?>
							</a>
							<a href="https://maps.apple.com/?daddr=<?php echo esc_attr( $prop_lat ); ?>,<?php echo esc_attr( $prop_lng ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline" style="display:inline-flex;align-items:center;gap:var(--space-2);">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
								<?php esc_html_e( 'Navigate with Apple Maps', 'thessnest' ); ?>
							</a>
						</div>
					</div>
				<?php endif; ?>

			</div>

		</div>

		<!-- Reviews & Ratings Section -->
		<?php
		if ( comments_open() || get_comments_number() ) :
			comments_template();
		endif;
		?>

	</div>

	<?php endwhile; ?>

</main>

<?php get_footer(); ?>
