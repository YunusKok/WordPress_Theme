<?php
/**
 * ThessNest — Property Card Template Part (Liquid Glass Edition)
 *
 * Premium card with:
 *   - Swiper carousel with Unsplash fallback images
 *   - Frosted glass save/heart button
 *   - Frosted pill trust badges (Verified, Wi-Fi Speed)
 *   - WYSIWYP Pricing with breakdown pill
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

// ── Gather Data ──────────────────────────────────────────
$property_id   = get_the_ID();
$rent          = thessnest_get_meta( 'rent', $property_id );
$utilities     = thessnest_get_meta( 'utilities', $property_id );
$deposit       = thessnest_get_meta( 'deposit', $property_id );
$wifi_speed    = thessnest_get_meta( 'wifi_speed', $property_id );

// Check if the property author (landlord) is KYC verified
$author_id     = get_post_field( 'post_author', $property_id );
$is_verified   = get_user_meta( $author_id, '_kyc_status', true ) === 'approved';

$gallery_ids   = thessnest_get_gallery( $property_id );
$neighborhood  = thessnest_get_first_term( 'neighborhood', $property_id );
$permalink     = get_the_permalink();

$is_saved = false;
if ( is_user_logged_in() ) {
	$user_favorites = get_user_meta( get_current_user_id(), 'thessnest_favorites', true );
	if ( is_array( $user_favorites ) && in_array( $property_id, $user_favorites ) ) {
		$is_saved = true;
	}
}

// Unsplash placeholder images for cards without galleries.
$placeholder_images = array(
	'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=640&h=480&fit=crop&q=80',
	'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=640&h=480&fit=crop&q=80',
	'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=640&h=480&fit=crop&q=80',
);
?>

<article class="property-card" id="property-<?php echo esc_attr( $property_id ); ?>">

	<!-- ── Image Carousel ──────────────────────────── -->
	<div class="card-carousel">
		<div class="swiper property-swiper">
			<div class="swiper-wrapper">
				<?php
				if ( ! empty( $gallery_ids ) ) :
					foreach ( $gallery_ids as $image_id ) :
						$img_url = wp_get_attachment_image_url( $image_id, 'card-thumb' );
						$img_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
						if ( $img_url ) : ?>
							<div class="swiper-slide">
								<img src="<?php echo esc_url( $img_url ); ?>"
								     alt="<?php echo esc_attr( $img_alt ? $img_alt : get_the_title() ); ?>"
								     loading="lazy"
								     width="640"
								     height="480">
							</div>
						<?php endif;
					endforeach;
				elseif ( has_post_thumbnail() ) : ?>
					<div class="swiper-slide">
						<?php the_post_thumbnail( 'card-thumb', array(
							'loading' => 'lazy',
							'alt'     => get_the_title(),
						) ); ?>
					</div>
				<?php else :
					// Fallback: high-quality Unsplash images.
					foreach ( $placeholder_images as $idx => $img_src ) : ?>
						<div class="swiper-slide">
							<img src="<?php echo esc_url( $img_src ); ?>"
							     alt="<?php echo esc_attr( get_the_title() . ' - Image ' . ( $idx + 1 ) ); ?>"
							     loading="lazy"
							     width="640"
							     height="480">
						</div>
					<?php endforeach;
				endif;
				?>
			</div>

			<?php
			$slide_count = ! empty( $gallery_ids ) ? count( $gallery_ids ) : ( has_post_thumbnail() ? 1 : count( $placeholder_images ) );
			if ( $slide_count > 1 ) : ?>
				<div class="swiper-pagination"></div>
			<?php endif; ?>
		</div>

		<!-- Frosted Glass Save Button -->
		<button class="card-save-btn <?php echo $is_saved ? 'saved' : ''; ?>" aria-label="<?php esc_attr_e( 'Save property', 'thessnest' ); ?>" data-property-id="<?php echo esc_attr( $property_id ); ?>">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
			</svg>
		</button>
	</div>

	<!-- ── Card Body ───────────────────────────────── -->
	<div class="card-body">

		<!-- Frosted Trust Badges -->
		<div class="card-badges">
			<?php if ( $is_verified ) : 
				$kyc_date = get_user_meta( $author_id, '_kyc_approved_date', true );
				$tooltip  = $kyc_date ? sprintf( __( 'Verified on %s by ThessNest', 'thessnest' ), date_i18n( get_option('date_format'), strtotime( $kyc_date ) ) ) : __( 'Physically verified by ThessNest team', 'thessnest' );
			?>
				<span class="badge badge-verified" data-tooltip="<?php echo esc_attr( $tooltip ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
						<polyline points="22 4 12 14.01 9 11.01"/>
					</svg>
					<?php esc_html_e( 'Verified', 'thessnest' ); ?>
				</span>
			<?php endif; ?>

			<?php
			$average_rating = get_post_meta( $property_id, '_thessnest_average_rating', true );
			if ( $average_rating ) :
			?>
				<span class="badge badge-rating" style="background:var(--color-surface); color:var(--color-primary); border:1px solid var(--color-border);">
					<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="var(--color-accent)" stroke="var(--color-accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="margin-right:2px;">
						<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
					</svg>
					<?php echo esc_html( $average_rating ); ?>
				</span>
			<?php endif; ?>

			<?php if ( $wifi_speed ) : ?>
				<span class="badge badge-wifi" data-tooltip="<?php printf( esc_attr__( 'Internet speed: %s Mbps', 'thessnest' ), esc_attr( $wifi_speed ) ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<path d="M5 12.55a11 11 0 0 1 14.08 0"/>
						<path d="M1.42 9a16 16 0 0 1 21.16 0"/>
						<path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
						<line x1="12" y1="20" x2="12.01" y2="20"/>
					</svg>
					<?php echo esc_html( $wifi_speed ); ?> Mbps
				</span>
			<?php endif; ?>
		</div>

		<!-- Title -->
		<h3 class="card-title">
			<a href="<?php echo esc_url( $permalink ); ?>">
				<?php the_title(); ?>
			</a>
		</h3>

		<!-- Location -->
		<?php if ( $neighborhood ) : ?>
			<p class="card-location">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
					<circle cx="12" cy="10" r="3"/>
				</svg>
				<?php echo esc_html( $neighborhood ); ?>, Thessaloniki
			</p>
		<?php endif; ?>

		<!-- WYSIWYP Pricing -->
		<div class="card-pricing">
			<?php if ( $rent ) : ?>
				<span class="price-main"><?php echo esc_html( thessnest_format_price( $rent ) ); ?></span>
				<span class="price-period">/<?php esc_html_e( 'mo', 'thessnest' ); ?></span>
			<?php endif; ?>

			<?php if ( $utilities || $deposit ) : ?>
				<span class="price-breakdown">
					<?php if ( $utilities ) : ?>
						+ <?php echo esc_html( thessnest_format_price( $utilities ) ); ?> <?php esc_html_e( 'utils', 'thessnest' ); ?>
					<?php endif; ?>
					<?php if ( $deposit ) : ?>
						&middot; <?php echo esc_html( thessnest_format_price( $deposit ) ); ?> <?php esc_html_e( 'dep.', 'thessnest' ); ?>
					<?php endif; ?>
				</span>
			<?php endif; ?>
		</div>

	</div>
</article>
