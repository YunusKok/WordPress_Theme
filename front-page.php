<?php
/**
 * ThessNest — Front Page Template (Professional Edition)
 *
 * Competitor-quality landing page with:
 *   1. Hero section with animated gradient orbs + search bar
 *   2. Dynamic Trust Bar (partner logos from Customizer)
 *   3. Quick category pills
 *   4. Featured Properties grid
 *   5. Popular Destinations (neighborhood taxonomy)
 *   6. Stats Counter Bar (animated)
 *   7. How It Works (3-step cards)
 *   8. Testimonials
 *   9. Why Choose Us (split layout)
 *  10. CTA Banner
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( have_posts() ) {
	the_post();
}
?>

<main id="main-content" role="main">

	<!-- ================================================================
	     HERO — Fluid Gradient Orbs + Search
	     ================================================================ -->
	<?php
	$hero_bg_default = get_theme_file_uri( 'assets/images/hero-bg-default.png' );
	$hero_bg_image   = get_theme_mod( 'hero_bg_image', $hero_bg_default );
	?>
	<section class="hero-section" style="background-image:url('<?php echo esc_url( $hero_bg_image ); ?>');" aria-label="<?php esc_attr_e( 'Search for housing', 'thessnest' ); ?>">

		<!-- Animated Gradient Orbs -->
		<div class="hero-orb hero-orb--1" aria-hidden="true"></div>
		<div class="hero-orb hero-orb--2" aria-hidden="true"></div>
		<div class="hero-orb hero-orb--3" aria-hidden="true"></div>

		<div class="hero-content">

			<h1 class="hero-title">
				<?php echo esc_html( get_theme_mod( 'hero_title', __( 'Find Your Perfect Home', 'thessnest' ) ) ); ?>
			</h1>
			<p class="hero-subtitle">
				<?php echo wp_kses_post( get_theme_mod( 'hero_subtitle', __( 'Browse verified listings with transparent pricing. No hidden fees, instant booking.', 'thessnest' ) ) ); ?>
			</p>

			<!-- ── Search Bar (Booking Style) ───────────────── -->
			<form class="search-bar search-bar--booking-style" role="search" method="get" action="<?php echo esc_url( get_post_type_archive_link( 'property' ) ); ?>">

				<!-- Real form inputs -->
				<input type="hidden" name="move_in_date" id="home_move_in">
				<input type="hidden" name="move_out_date" id="home_move_out">
				<input type="hidden" name="guests" id="home_guests" value="1">

				<!-- Move-in Trigger Wrapper -->
				<div class="search-field date-trigger" id="home-dates-trigger">
					<svg class="field-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
					</svg>
					<div class="field-content">
						<span class="field-label"><?php esc_html_e( 'Move in', 'thessnest' ); ?></span>
						<span class="field-value" id="val-move-in"><?php esc_html_e( 'Add dates', 'thessnest' ); ?></span>
					</div>
					<input type="text" id="home_date_in_picker" class="invisible-date-picker" placeholder="">
				</div>

				<!-- Move-out Trigger Wrapper -->
				<div class="search-field date-trigger date-trigger-out">
					<svg class="field-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
					</svg>
					<div class="field-content">
						<span class="field-label"><?php esc_html_e( 'Move out', 'thessnest' ); ?></span>
						<span class="field-value" id="val-move-out"><?php esc_html_e( 'Add dates', 'thessnest' ); ?></span>
					</div>
					<input type="text" id="home_date_out_picker" class="invisible-date-picker" placeholder="">
				</div>

				<!-- Guests Modal Trigger -->
				<div class="search-field" id="trigger-guest-modal" style="cursor: pointer; position: relative;">
					<svg class="field-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
					</svg>
					<div class="field-content">
						<span class="field-label"><?php esc_html_e( 'Guests', 'thessnest' ); ?></span>
						<span class="field-value" id="val-guests">1 <?php esc_html_e( 'Guest', 'thessnest' ); ?></span>
					</div>

					<!-- Glassmorphism Guest Selector Modal -->
					<div id="guest-selector-modal" style="display:none; position:absolute; top:calc(100% + 15px); right:0; width:280px; background:rgba(255,255,255,0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.3); border-radius:12px; padding:20px; box-shadow:0 10px 30px rgba(0,0,0,0.1); z-index:100; cursor:default;">
						<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
							<div>
								<h4 style="margin:0; font-size:16px; color:#333;"><?php esc_html_e( 'Adults', 'thessnest' ); ?></h4>
								<span style="font-size:12px; color:#777;"><?php esc_html_e( 'Age 18+', 'thessnest' ); ?></span>
							</div>
							<div style="display:flex; align-items:center; gap:10px;">
								<button type="button" id="guest-dec" style="width:32px; height:32px; border-radius:50%; border:1px solid #ccc; background:transparent; font-size:18px; line-height:1; display:flex; align-items:center; justify-content:center; cursor:pointer;">-</button>
								<span id="guest-count-display" style="font-weight:600; width:15px; text-align:center; color:#333;">1</span>
								<button type="button" id="guest-inc" style="width:32px; height:32px; border-radius:50%; border:1px solid #aaa; color:#333; background:transparent; font-size:18px; line-height:1; display:flex; align-items:center; justify-content:center; cursor:pointer;">+</button>
							</div>
						</div>
					</div>
				</div>

				<!-- Search CTA -->
				<button type="submit" class="btn-search">
					<?php esc_html_e( 'Search', 'thessnest' ); ?>
				</button>
			</form>

		</div>
	</section>


	<!-- ================================================================
	     TRUST BAR — Dynamic Partner Logos (from Customizer)
	     ================================================================ -->
	<?php
	// Collect all logos from customizer
	$trust_logos = array();
	for ( $i = 1; $i <= 6; $i++ ) {
		$logo_url = get_theme_mod( 'trustbar_logo_' . $i, '' );
		$logo_alt = get_theme_mod( 'trustbar_logo_' . $i . '_alt', '' );
		if ( $logo_url ) {
			$trust_logos[] = array( 'url' => $logo_url, 'alt' => $logo_alt );
		}
	}

	if ( ! empty( $trust_logos ) ) :
		$trustbar_label = get_theme_mod( 'trustbar_label', __( 'Trusted by leading organizations', 'thessnest' ) );
	?>
	<section class="trust-bar" aria-label="<?php esc_attr_e( 'Trusted partners', 'thessnest' ); ?>">
		<div class="container">
			<?php if ( $trustbar_label ) : ?>
				<p class="trust-bar-label"><?php echo esc_html( $trustbar_label ); ?></p>
			<?php endif; ?>
			<div class="trust-bar-logos">
				<?php foreach ( $trust_logos as $logo ) : ?>
					<img src="<?php echo esc_url( $logo['url'] ); ?>"
					     alt="<?php echo esc_attr( $logo['alt'] ); ?>"
					     width="120" height="40" loading="lazy">
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>


	<!-- ================================================================
	     QUICK CATEGORIES — Frosted Glass Pills
	     ================================================================ -->
	<?php
	$archive_url = get_post_type_archive_link( 'property' );
	?>
	<section class="quick-categories" aria-label="<?php esc_attr_e( 'Quick categories', 'thessnest' ); ?>">
		<div class="quick-categories-inner">

			<a href="<?php echo esc_url( add_query_arg( 'instant_book', '1', $archive_url ) ); ?>" class="category-pill">
				<svg class="cat-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
				</svg>
				<?php esc_html_e( 'Instant Book', 'thessnest' ); ?>
			</a>

			<a href="<?php echo esc_url( add_query_arg( 'no_agency_fee', '1', $archive_url ) ); ?>" class="category-pill">
				<svg class="cat-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
				</svg>
				<?php esc_html_e( 'No Agency Fee', 'thessnest' ); ?>
			</a>

			<a href="<?php echo esc_url( add_query_arg( 'target_group', 'student', $archive_url ) ); ?>" class="category-pill">
				<svg class="cat-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 1.1 2.7 2 6 2s6-.9 6-2v-5"/>
				</svg>
				<?php esc_html_e( 'Student Housing', 'thessnest' ); ?>
			</a>

			<a href="<?php echo esc_url( add_query_arg( 'target_group', 'digital-nomad', $archive_url ) ); ?>" class="category-pill">
				<svg class="cat-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
				</svg>
				<?php esc_html_e( 'Nomad Workspace', 'thessnest' ); ?>
			</a>

			<a href="<?php echo esc_url( add_query_arg( 'verified', '1', $archive_url ) ); ?>" class="category-pill">
				<svg class="cat-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
					<path d="M9 12l2 2 4-4"/>
				</svg>
				<?php esc_html_e( 'Verified Only', 'thessnest' ); ?>
			</a>

		</div>
	</section>


	<!-- ================================================================
	     FEATURED PROPERTIES
	     ================================================================ -->
	<section class="section featured-section" aria-labelledby="featured-heading">
		<div class="container">

			<div class="section-header">
				<h2 class="section-title" id="featured-heading">
					<?php echo esc_html( get_theme_mod( 'featured_title', __( 'Featured Properties', 'thessnest' ) ) ); ?>
				</h2>
				<p class="section-subtitle">
					<?php echo esc_html( get_theme_mod( 'featured_subtitle', __( 'Hand-picked properties with verified landlords, ready for move-in.', 'thessnest' ) ) ); ?>
				</p>
			</div>

			<?php
			$featured_query = new WP_Query( array(
				'post_type'      => 'property',
				'posts_per_page' => 8,
				'meta_key'       => '_thessnest_featured',
				'meta_value'     => '1',
				'orderby'        => 'date',
				'order'          => 'DESC',
			) );

			if ( ! $featured_query->have_posts() ) {
				$featured_query = new WP_Query( array(
					'post_type'      => 'property',
					'posts_per_page' => 8,
					'orderby'        => 'date',
					'order'          => 'DESC',
				) );
			}

			if ( $featured_query->have_posts() ) : ?>
				<div class="property-grid">
					<?php
					while ( $featured_query->have_posts() ) :
						$featured_query->the_post();
						get_template_part( 'template-parts/property-card' );
					endwhile;
					?>
				</div>

				<div class="text-center mt-8">
					<a href="<?php echo esc_url( get_post_type_archive_link( 'property' ) ); ?>" class="btn btn-outline">
						<?php esc_html_e( 'View All Properties', 'thessnest' ); ?>
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
						</svg>
					</a>
				</div>

			<?php else : ?>
				<div class="text-center" style="padding:var(--space-16);">
					<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="1" aria-hidden="true" style="margin:0 auto var(--space-4);">
						<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
					</svg>
					<p class="text-muted"><?php esc_html_e( 'Properties coming soon. Check back shortly!', 'thessnest' ); ?></p>
				</div>
			<?php endif;
			wp_reset_postdata();
			?>

		</div>
	</section>


	<!-- ================================================================
	     POPULAR DESTINATIONS — Neighborhood Taxonomy Grid
	     ================================================================ -->
	<?php
	$show_destinations = get_theme_mod( 'destinations_show', true );
	if ( $show_destinations ) :
		$dest_count = (int) get_theme_mod( 'destinations_count', 6 );
		$dest_terms = get_terms( array(
			'taxonomy'   => 'neighborhood',
			'hide_empty' => false,
			'number'     => $dest_count,
			'orderby'    => 'count',
			'order'      => 'DESC',
		) );

		if ( ! is_wp_error( $dest_terms ) && ! empty( $dest_terms ) ) :
	?>
	<section class="section popular-destinations" aria-labelledby="destinations-heading">
		<div class="container">
			<div class="section-header">
				<h2 class="section-title" id="destinations-heading">
					<?php echo esc_html( get_theme_mod( 'destinations_title', __( 'Popular Destinations', 'thessnest' ) ) ); ?>
				</h2>
				<p class="section-subtitle">
					<?php echo esc_html( get_theme_mod( 'destinations_subtitle', __( 'Explore the most sought-after neighborhoods and areas.', 'thessnest' ) ) ); ?>
				</p>
			</div>

			<div class="destinations-grid">
				<?php foreach ( $dest_terms as $idx => $term ) :
					// Try to get taxonomy image (Redux or term meta)
					$term_image = get_term_meta( $term->term_id, 'neighborhood_image', true );
					if ( empty( $term_image ) && function_exists( 'thessnest_opt' ) ) {
						$term_image = '';
					}
					// Fallback gradient if no image
					$has_image = ! empty( $term_image );
					$card_class = 'dest-card';
					if ( $idx === 0 ) $card_class .= ' dest-card--featured';

					$term_link = get_term_link( $term );
					if ( is_wp_error( $term_link ) ) continue;
				?>
				<a href="<?php echo esc_url( $term_link ); ?>" class="<?php echo esc_attr( $card_class ); ?>">
					<?php if ( $has_image ) : ?>
						<img src="<?php echo esc_url( $term_image ); ?>"
						     alt="<?php echo esc_attr( $term->name ); ?>"
						     loading="lazy" class="dest-card-bg">
					<?php else : ?>
						<div class="dest-card-bg dest-card-bg--gradient" aria-hidden="true"></div>
					<?php endif; ?>
					<div class="dest-card-overlay"></div>
					<div class="dest-card-content">
						<h3 class="dest-card-title"><?php echo esc_html( $term->name ); ?></h3>
						<span class="dest-card-count">
							<?php printf( esc_html( _n( '%s property', '%s properties', $term->count, 'thessnest' ) ), number_format_i18n( $term->count ) ); ?>
						</span>
					</div>
				</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
		endif;
	endif;
	?>


	<!-- ================================================================
	     STATS COUNTER BAR — Animated Numbers
	     ================================================================ -->
	<?php if ( get_theme_mod( 'stats_show', true ) ) : ?>
	<section class="stats-counter" aria-label="<?php esc_attr_e( 'Platform statistics', 'thessnest' ); ?>">
		<div class="container">
			<div class="stats-grid">
				<?php
				$stat_defaults = array(
					1 => array( 'number' => '500',  'suffix' => '+', 'label' => __( 'Properties Listed', 'thessnest' ) ),
					2 => array( 'number' => '1200', 'suffix' => '+', 'label' => __( 'Happy Tenants', 'thessnest' ) ),
					3 => array( 'number' => '50',   'suffix' => '+', 'label' => __( 'Neighborhoods', 'thessnest' ) ),
					4 => array( 'number' => '98',   'suffix' => '%', 'label' => __( 'Satisfaction Rate', 'thessnest' ) ),
				);
				for ( $i = 1; $i <= 4; $i++ ) :
					$number = get_theme_mod( 'stat_' . $i . '_number', $stat_defaults[ $i ]['number'] );
					$suffix = get_theme_mod( 'stat_' . $i . '_suffix', $stat_defaults[ $i ]['suffix'] );
					$label  = get_theme_mod( 'stat_' . $i . '_label',  $stat_defaults[ $i ]['label'] );
					if ( ! $number ) continue;
				?>
				<div class="stat-item">
					<span class="stat-number" data-count="<?php echo esc_attr( $number ); ?>">0</span><span class="stat-suffix"><?php echo esc_html( $suffix ); ?></span>
					<span class="stat-label"><?php echo esc_html( $label ); ?></span>
				</div>
				<?php endfor; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>


	<!-- ================================================================
	     HOW IT WORKS — Elevated Glass Step Cards
	     ================================================================ -->
	<section class="section how-it-works" aria-labelledby="hiw-heading">
		<div class="container">

			<div class="section-header">
				<h2 class="section-title" id="hiw-heading">
					<?php echo esc_html( get_theme_mod( 'hiw_title', __( 'How It Works', 'thessnest' ) ) ); ?>
				</h2>
				<p class="section-subtitle">
					<?php echo esc_html( get_theme_mod( 'hiw_subtitle', __( 'Secure your home in three simple steps.', 'thessnest' ) ) ); ?>
				</p>
			</div>

			<div class="steps-grid">

				<div class="step-card">
					<div class="step-icon">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
						</svg>
					</div>
					<span class="step-number">1</span>
					<h3 class="step-title"><?php echo esc_html( get_theme_mod( 'hiw_step_1_title', __( 'Search & Discover', 'thessnest' ) ) ); ?></h3>
					<p class="step-desc"><?php echo wp_kses_post( get_theme_mod( 'hiw_step_1_desc', __( 'Browse verified listings filtered by location, budget, amenities, and more.', 'thessnest' ) ) ); ?></p>
				</div>

				<div class="step-card">
					<div class="step-icon">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
							<path d="M9 16l2 2 4-4"/>
						</svg>
					</div>
					<span class="step-number">2</span>
					<h3 class="step-title"><?php echo esc_html( get_theme_mod( 'hiw_step_2_title', __( 'Book Securely', 'thessnest' ) ) ); ?></h3>
					<p class="step-desc"><?php echo wp_kses_post( get_theme_mod( 'hiw_step_2_desc', __( 'Reserve your place online with transparent pricing. No hidden fees, no surprises.', 'thessnest' ) ) ); ?></p>
				</div>

				<div class="step-card">
					<div class="step-icon">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
							<polyline points="9 22 9 12 15 12 15 22"/>
						</svg>
					</div>
					<span class="step-number">3</span>
					<h3 class="step-title"><?php echo esc_html( get_theme_mod( 'hiw_step_3_title', __( 'Move In', 'thessnest' ) ) ); ?></h3>
					<p class="step-desc"><?php echo wp_kses_post( get_theme_mod( 'hiw_step_3_desc', __( 'Arrive at your destination and settle into your new home. Welcome!', 'thessnest' ) ) ); ?></p>
				</div>

			</div>
		</div>
	</section>


	<!-- ================================================================
	     TESTIMONIALS — Customer Reviews
	     ================================================================ -->
	<?php
	$show_testimonials = get_theme_mod( 'testimonials_show', true );
	// Check if at least one testimonial has text
	$has_testimonials = false;
	for ( $i = 1; $i <= 3; $i++ ) {
		if ( get_theme_mod( 'testimonial_' . $i . '_text', '' ) ) {
			$has_testimonials = true;
			break;
		}
	}

	if ( $show_testimonials && $has_testimonials ) :
	?>
	<section class="section testimonials-section" aria-labelledby="testimonials-heading">
		<div class="container">
			<div class="section-header">
				<h2 class="section-title" id="testimonials-heading">
					<?php echo esc_html( get_theme_mod( 'testimonials_title', __( 'What Our Users Say', 'thessnest' ) ) ); ?>
				</h2>
				<p class="section-subtitle">
					<?php echo esc_html( get_theme_mod( 'testimonials_subtitle', __( 'Real experiences from real people who found their home through our platform.', 'thessnest' ) ) ); ?>
				</p>
			</div>

			<div class="testimonials-grid">
				<?php for ( $i = 1; $i <= 3; $i++ ) :
					$t_text   = get_theme_mod( 'testimonial_' . $i . '_text', '' );
					$t_name   = get_theme_mod( 'testimonial_' . $i . '_name', '' );
					$t_role   = get_theme_mod( 'testimonial_' . $i . '_role', '' );
					$t_image  = get_theme_mod( 'testimonial_' . $i . '_image', '' );
					$t_rating = (int) get_theme_mod( 'testimonial_' . $i . '_rating', 5 );

					if ( ! $t_text ) continue;
				?>
				<div class="testimonial-card">
					<!-- Stars -->
					<div class="testimonial-stars" aria-label="<?php printf( esc_attr__( '%d out of 5 stars', 'thessnest' ), $t_rating ); ?>">
						<?php for ( $s = 1; $s <= 5; $s++ ) : ?>
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
							     fill="<?php echo $s <= $t_rating ? 'var(--color-accent)' : 'var(--color-border)'; ?>"
							     stroke="none" aria-hidden="true">
								<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
							</svg>
						<?php endfor; ?>
					</div>

					<!-- Quote -->
					<blockquote class="testimonial-text">
						<?php echo wp_kses_post( $t_text ); ?>
					</blockquote>

					<!-- Author -->
					<div class="testimonial-author">
						<?php if ( $t_image ) : ?>
							<img src="<?php echo esc_url( $t_image ); ?>"
							     alt="<?php echo esc_attr( $t_name ); ?>"
							     class="testimonial-avatar" width="48" height="48" loading="lazy">
						<?php else : ?>
							<div class="testimonial-avatar testimonial-avatar--placeholder" aria-hidden="true">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
							</div>
						<?php endif; ?>
						<div>
							<?php if ( $t_name ) : ?>
								<span class="testimonial-name"><?php echo esc_html( $t_name ); ?></span>
							<?php endif; ?>
							<?php if ( $t_role ) : ?>
								<span class="testimonial-role"><?php echo esc_html( $t_role ); ?></span>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<?php endfor; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>


	<!-- ================================================================
	     WHY CHOOSE US — Split Layout (Fully Customizable)
	     ================================================================ -->
	<section class="section why-choose-us" aria-labelledby="wcu-heading">
		<div class="container">
			<div class="wtu-layout">
				<div class="wtu-image">
					<?php
					$wcu_image = get_theme_mod( 'whychoose_image', '' );
					if ( $wcu_image ) : ?>
						<img src="<?php echo esc_url( $wcu_image ); ?>"
						     alt="<?php echo esc_attr( get_theme_mod( 'whychoose_title', __( 'Why Choose Us', 'thessnest' ) ) ); ?>"
						     loading="lazy" width="600" height="450">
					<?php else : ?>
						<div class="wtu-image-placeholder" aria-hidden="true">
							<svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
								<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
							</svg>
							<p><?php esc_html_e( 'Upload an image in Customizer → Why Choose Us', 'thessnest' ); ?></p>
						</div>
					<?php endif; ?>
				</div>
				<div class="wtu-content">
					<h2 class="section-title" id="wcu-heading">
						<?php echo esc_html( get_theme_mod( 'whychoose_title', __( 'Why Choose Us', 'thessnest' ) ) ); ?>
					</h2>
					<ul class="wtu-features">
						<?php
						$wcu_icons = array(
							1 => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--color-success)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
							2 => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--color-success)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>',
							3 => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--color-success)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
						);

						$feature_defaults = array(
							1 => array(
								'title' => __( 'Verified Landlords', 'thessnest' ),
								'desc'  => __( 'Every landlord undergoes identity and property ownership verification before listing.', 'thessnest' ),
							),
							2 => array(
								'title' => __( 'No Hidden Fees', 'thessnest' ),
								'desc'  => __( 'Transparent pricing — rent, utilities, and deposit displayed upfront.', 'thessnest' ),
							),
							3 => array(
								'title' => __( 'Global Community', 'thessnest' ),
								'desc'  => __( 'Multilingual support, instant booking, and map navigation for international tenants.', 'thessnest' ),
							),
						);

						for ( $i = 1; $i <= 3; $i++ ) :
							$ft = get_theme_mod( 'whychoose_feature_' . $i . '_title', $feature_defaults[ $i ]['title'] );
							$fd = get_theme_mod( 'whychoose_feature_' . $i . '_desc',  $feature_defaults[ $i ]['desc'] );
						?>
						<li>
							<?php echo $wcu_icons[ $i ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG ?>
							<div>
								<strong><?php echo esc_html( $ft ); ?></strong>
								<p><?php echo esc_html( $fd ); ?></p>
							</div>
						</li>
						<?php endfor; ?>
					</ul>
				</div>
			</div>
		</div>
	</section>


	<!-- ================================================================
	     CTA BANNER
	     ================================================================ -->
	<section class="cta-banner" aria-label="<?php esc_attr_e( 'List your property', 'thessnest' ); ?>">
		<h2 class="cta-title"><?php echo esc_html( get_theme_mod( 'cta_title', __( 'Own a Property?', 'thessnest' ) ) ); ?></h2>
		<p class="cta-subtitle"><?php echo wp_kses_post( get_theme_mod( 'cta_subtitle', __( 'List your space and reach thousands of tenants looking for their next home.', 'thessnest' ) ) ); ?></p>
		<a href="<?php echo esc_url( get_theme_mod( 'cta_btn_link', '#' ) ); ?>" class="btn btn-primary">
			<?php echo esc_html( get_theme_mod( 'cta_btn_text', __( 'List Your Property', 'thessnest' ) ) ); ?>
			<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
			</svg>
		</a>
	</section>

</main>


<?php get_footer(); ?>
