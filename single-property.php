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
		$wifi_speed  = thessnest_get_meta( 'wifi_speed', $property_id );
		$is_verified = get_post_meta( $property_id, '_thessnest_verified', true ) === '1';
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
				<?php if ( $is_verified ) : ?>
					<span class="badge badge-verified">
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

			<h1 style="font-size:var(--font-size-3xl);margin-bottom:var(--space-2);color:var(--color-primary);">
				<?php the_title(); ?>
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

							<div style="display:flex;justify-content:space-between;align-items:center;padding:var(--space-4) 0;font-size:var(--font-size-lg);">
								<strong><?php esc_html_e( 'Total / month', 'thessnest' ); ?></strong>
								<strong style="color:var(--color-accent);font-size:var(--font-size-xl);">
									<?php echo esc_html( thessnest_format_price( (int) $rent + (int) $utilities ) ); ?>
								</strong>
							</div>
						<?php endif; ?>

						<a href="#" class="btn btn-primary" style="width:100%;margin-top:var(--space-4);padding:var(--space-4);text-align:center;border-radius:var(--radius-lg);font-size:var(--font-size-lg);">
							<?php esc_html_e( 'Request Booking', 'thessnest' ); ?>
						</a>
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

			</div>

		</div>
	</div>

	<?php endwhile; ?>

</main>

<?php get_footer(); ?>
