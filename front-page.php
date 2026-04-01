<?php
/**
 * ThessNest — Front Page Template (iOS Liquid Glass Edition)
 *
 * Listdo-inspired landing page with:
 *   1. Hero section with animated gradient orbs + liquid glass search bar
 *   2. Quick category frosted pills
 *   3. Featured Properties grid (premium cards)
 *   4. How It Works (3-step elevated cards)
 *   5. CTA Banner with ambient orb
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Setup main post data so 'Edit Page' and other page-specific features work.
if ( have_posts() ) {
	the_post();
}
?>

<main id="main-content" role="main">

	<!-- ================================================================
	     HERO — Fluid Gradient Orbs + Liquid Glass Search
	     ================================================================ -->
	<?php
	$hero_bg_default = get_theme_file_uri( 'assets/images/hero-bg-default.png' );
	$hero_bg_image   = get_theme_mod( 'hero_bg_image', $hero_bg_default );
	?>
	<section class="hero-section" style="background-image:url('<?php echo esc_url( $hero_bg_image ); ?>');" aria-label="<?php esc_attr_e( 'Search for housing', 'thessnest' ); ?>">

		<!-- Animated Gradient Orbs (refract through glass search bar) -->
		<div class="hero-orb hero-orb--1" aria-hidden="true"></div>
		<div class="hero-orb hero-orb--2" aria-hidden="true"></div>
		<div class="hero-orb hero-orb--3" aria-hidden="true"></div>

		<div class="hero-content">

			<h1 class="hero-title">
				<?php echo esc_html( get_theme_mod( 'hero_title', __( 'Find Your Home Anywhere', 'thessnest' ) ) ); ?>
			</h1>
			<p class="hero-subtitle">
				<?php echo wp_kses_post( get_theme_mod( 'hero_subtitle', __( 'Find student accommodation no agency fee and digital nomad apartments. Verified landlords, instant booking.', 'thessnest' ) ) ); ?>
			</p>

			<!-- ── Liquid Glass Search Bar ───────────────── -->
			<form class="search-bar" role="search" method="get" action="<?php echo esc_url( get_post_type_archive_link( 'property' ) ); ?>">

				<!-- Keyword -->
				<div class="search-field">
					<svg class="field-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
					</svg>
					<input type="text" name="s" placeholder="<?php esc_attr_e( 'What are you looking for?', 'thessnest' ); ?>" aria-label="<?php esc_attr_e( 'Search keywords', 'thessnest' ); ?>">
					<input type="hidden" name="post_type" value="property">
				</div>

				<!-- Location -->
				<div class="search-field">
					<svg class="field-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
					</svg>
					<select name="neighborhood" aria-label="<?php esc_attr_e( 'Select neighborhood', 'thessnest' ); ?>">
						<option value=""><?php esc_html_e( 'Location', 'thessnest' ); ?></option>
						<?php
						$neighborhoods = get_terms( array( 'taxonomy' => 'neighborhood', 'hide_empty' => false ) );
						if ( ! is_wp_error( $neighborhoods ) && ! empty( $neighborhoods ) ) :
							foreach ( $neighborhoods as $nb ) : ?>
								<option value="<?php echo esc_attr( $nb->slug ); ?>"><?php echo esc_html( $nb->name ); ?></option>
							<?php endforeach;
						endif;
						?>
					</select>
				</div>

				<!-- Move-in Date -->
				<div class="search-field">
					<svg class="field-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
					</svg>
					<input type="date" name="move_in_date" aria-label="<?php esc_attr_e( 'Move-in date', 'thessnest' ); ?>">
				</div>

				<!-- Search CTA -->
				<button type="submit" class="btn-search">
					<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
					</svg>
					<?php esc_html_e( 'Search', 'thessnest' ); ?>
				</button>
			</form>

		</div>
	</section>


	<!-- ================================================================
	     TRUST BAR — Partner Logos
	     ================================================================ -->
	<section class="trust-bar" aria-label="<?php esc_attr_e( 'Trusted partners', 'thessnest' ); ?>">
		<div class="container">
			<p class="trust-bar-label"><?php esc_html_e( 'Trusted by students from', 'thessnest' ); ?></p>
			<div class="trust-bar-logos">
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/partners/esn-logo.svg' ) ); ?>" alt="ESN" width="120" height="40" loading="lazy">
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/partners/auth-logo.svg' ) ); ?>" alt="Aristotle University" width="120" height="40" loading="lazy">
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/partners/uom-logo.svg' ) ); ?>" alt="University of Macedonia" width="120" height="40" loading="lazy">
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/partners/ihu-logo.svg' ) ); ?>" alt="IHU" width="120" height="40" loading="lazy">
			</div>
		</div>
	</section>


	<!-- ================================================================
	     QUICK CATEGORIES — Frosted Glass Pills
	     ================================================================ -->
	<section class="quick-categories" aria-label="<?php esc_attr_e( 'Quick categories', 'thessnest' ); ?>">
		<div class="quick-categories-inner">

			<a href="#" class="category-pill">
				<svg class="cat-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
				</svg>
				<?php esc_html_e( 'Instant Book', 'thessnest' ); ?>
			</a>

			<a href="#" class="category-pill">
				<svg class="cat-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
				</svg>
				<?php esc_html_e( 'No Agency Fee', 'thessnest' ); ?>
			</a>

			<a href="#" class="category-pill">
				<svg class="cat-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 1.1 2.7 2 6 2s6-.9 6-2v-5"/>
				</svg>
				<?php esc_html_e( 'Safe Student Rooms', 'thessnest' ); ?>
			</a>

			<a href="#" class="category-pill">
				<svg class="cat-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
				</svg>
				<?php esc_html_e( 'Nomad Workspace', 'thessnest' ); ?>
			</a>

			<a href="#" class="category-pill">
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
					<?php echo esc_html( get_theme_mod( 'featured_subtitle', __( 'Verified landlord student apartments & digital nomad flats ready for move-in.', 'thessnest' ) ) ); ?>
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
					<h3 class="step-title"><?php echo esc_html( get_theme_mod( 'hiw_step_1_title', __( 'Choose Safe Rooms', 'thessnest' ) ) ); ?></h3>
					<p class="step-desc"><?php echo wp_kses_post( get_theme_mod( 'hiw_step_1_desc', __( 'Browse verified listings near Aristotle University or grocery stores. Filter by high-speed Wi-Fi, budget, and amenities.', 'thessnest' ) ) ); ?></p>
				</div>

				<div class="step-card">
					<div class="step-icon">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
							<path d="M9 16l2 2 4-4"/>
						</svg>
					</div>
					<span class="step-number">2</span>
					<h3 class="step-title"><?php echo esc_html( get_theme_mod( 'hiw_step_2_title', __( 'Book', 'thessnest' ) ) ); ?></h3>
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
					<p class="step-desc"><?php echo wp_kses_post( get_theme_mod( 'hiw_step_3_desc', __( 'Arrive in your destination and settle into your new home. Welcome!', 'thessnest' ) ) ); ?></p>
				</div>

			</div>
		</div>
	</section>


	<!-- ================================================================
	     WHY TRUST US — Split Layout
	     ================================================================ -->
	<section class="section why-trust-us" aria-labelledby="wtu-heading">
		<div class="container">
			<div class="wtu-layout">
				<div class="wtu-image">
					<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/trust-photo.jpg' ) ); ?>"
					     alt="<?php esc_attr_e( 'Verified ThessNest apartment', 'thessnest' ); ?>"
					     loading="lazy" width="600" height="450">
				</div>
				<div class="wtu-content">
					<h2 class="section-title" id="wtu-heading">
						<?php esc_html_e( 'Why Trust Us', 'thessnest' ); ?>
					</h2>
					<ul class="wtu-features">
						<li>
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--color-success)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
							<div>
								<strong><?php esc_html_e( 'KYC-Verified Landlords', 'thessnest' ); ?></strong>
								<p><?php esc_html_e( 'Every landlord undergoes ID and property ownership verification before listing.', 'thessnest' ); ?></p>
							</div>
						</li>
						<li>
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--color-success)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
							<div>
								<strong><?php esc_html_e( 'No Hidden Fees', 'thessnest' ); ?></strong>
								<p><?php esc_html_e( 'Transparent WYSIWYP pricing — rent, utilities, and deposit shown upfront.', 'thessnest' ); ?></p>
							</div>
						</li>
						<li>
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--color-success)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
							<div>
								<strong><?php esc_html_e( 'Built for International Tenants', 'thessnest' ); ?></strong>
								<p><?php esc_html_e( 'Multilingual support, one-click map navigation, and instant booking for Erasmus students and digital nomads.', 'thessnest' ); ?></p>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</section>


	<!-- ================================================================
	     CTA BANNER
	     ================================================================ -->
	<section class="cta-banner" aria-label="<?php esc_attr_e( 'List your property', 'thessnest' ); ?>">
		<h2 class="cta-title"><?php echo esc_html( get_theme_mod( 'cta_title', __( 'Own a Property Here?', 'thessnest' ) ) ); ?></h2>
		<p class="cta-subtitle"><?php echo wp_kses_post( get_theme_mod( 'cta_subtitle', __( 'List your space and reach thousands of Erasmus students and digital nomads looking for their next home.', 'thessnest' ) ) ); ?></p>
		<a href="<?php echo esc_url( get_theme_mod( 'cta_btn_link', '#' ) ); ?>" class="btn btn-primary">
			<?php echo esc_html( get_theme_mod( 'cta_btn_text', __( 'List Your Property', 'thessnest' ) ) ); ?>
			<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
			</svg>
		</a>
	</section>

</main>


<?php get_footer(); ?>
