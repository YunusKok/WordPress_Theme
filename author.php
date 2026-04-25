<?php
/**
 * ThessNest — Author Template
 *
 * Displays landlord/tenant profiles and their published listings.
 *
 * @package ThessNest
 */

get_header();

$author = get_queried_object();
$author_id = $author->ID;

// Author meta
$first_name  = get_user_meta( $author_id, 'first_name', true );
$last_name   = get_user_meta( $author_id, 'last_name', true );
$display_name = ! empty( $first_name ) ? $first_name . ' ' . $last_name : $author->display_name;
$description = get_user_meta( $author_id, 'description', true );
$is_verified = get_user_meta( $author_id, 'thessnest_kyc_verified', true );
?>

<main id="main-content" class="site-main" style="padding:var(--space-12) 0; background:var(--color-surface); min-height:80vh;">
	<div class="container">
		
		<!-- Author Profile Header -->
		<div class="author-profile-header" style="background:var(--color-background); border-radius:var(--radius-2xl); padding:var(--space-8); box-shadow:var(--shadow-md); margin-bottom:var(--space-8); border:1px solid var(--color-border); display:flex; gap:var(--space-6); align-items:center;">
			<div class="author-avatar" style="position:relative; flex-shrink:0;">
				<?php echo get_avatar( $author_id, 120, '', '', array( 'class' => 'avatar' ) ); ?>
				<style>
					.author-avatar img { border-radius:var(--radius-full); object-fit:cover; border:4px solid #fff; box-shadow:var(--shadow-sm); }
				</style>
				<?php if ( $is_verified === 'yes' ) : ?>
					<div class="verified-badge" style="position:absolute; bottom:0; right:0; background:var(--color-success); color:#fff; width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:3px solid #fff; box-shadow:var(--shadow-sm);" title="<?php esc_attr_e( 'Verified User', 'thessnest' ); ?>">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
					</div>
				<?php endif; ?>
			</div>

			<div class="author-info">
				<h1 style="font-size:var(--font-size-3xl); margin-bottom:var(--space-2);"><?php echo esc_html( $display_name ); ?></h1>
				<p style="color:var(--color-text-muted); font-size:var(--font-size-sm); margin-bottom:var(--space-4);">
					<?php printf( esc_html__( 'Member since %s', 'thessnest' ), date_i18n( 'F Y', strtotime( $author->user_registered ) ) ); ?>
				</p>
				<?php if ( ! empty( $description ) ) : ?>
					<p style="color:var(--color-text); line-height:1.6; max-width:800px;"><?php echo wp_kses_post( $description ); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<!-- Author Listings -->
		<div class="author-listings">
			<h2 style="font-size:var(--font-size-2xl); margin-bottom:var(--space-6);">
				<?php printf( esc_html__( 'Properties by %s', 'thessnest' ), explode( ' ', $display_name )[0] ); ?>
			</h2>

			<?php
			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
			$args = array(
				'post_type'      => 'property',
				'post_status'    => 'publish',
				'author'         => $author_id,
				'posts_per_page' => 9,
				'paged'          => $paged
			);

			$author_query = new WP_Query( $args );

			if ( $author_query->have_posts() ) : ?>
				<div class="properties-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:var(--space-6);">
					<?php while ( $author_query->have_posts() ) : $author_query->the_post(); ?>
						<?php get_template_part( 'template-parts/property-card' ); ?>
					<?php endwhile; ?>
				</div>

				<!-- Pagination -->
				<div class="thessnest-pagination" style="margin-top:var(--space-8); display:flex; justify-content:center; gap:var(--space-2);">
					<?php
					echo paginate_links( array(
						'total'     => $author_query->max_num_pages,
						'current'   => $paged,
						'prev_text' => '&laquo;',
						'next_text' => '&raquo;',
						'type'      => 'list'
					) );
					?>
				</div>
				<style>
					.thessnest-pagination ul { display:flex; gap:var(--space-2); list-style:none; padding:0; margin:0; }
					.thessnest-pagination a, .thessnest-pagination span { display:flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:var(--radius-md); background:var(--color-background); border:1px solid var(--color-border); font-weight:600; transition:all var(--transition-fast); }
					.thessnest-pagination a:hover { border-color:var(--color-accent); color:var(--color-accent); }
					.thessnest-pagination .current { background:var(--color-accent); color:#fff; border-color:var(--color-accent); }
				</style>

				<?php wp_reset_postdata(); ?>

			<?php else : ?>
				<div class="no-results" style="background:var(--color-background); padding:var(--space-12); text-align:center; border-radius:var(--radius-2xl); border:1px dashed var(--color-border);">
					<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--color-text-muted); margin-bottom:var(--space-4);"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
					<h3 style="font-size:var(--font-size-xl); margin-bottom:var(--space-2);"><?php esc_html_e( 'No properties found', 'thessnest' ); ?></h3>
					<p style="color:var(--color-text-muted);"><?php esc_html_e( 'This user hasn\'t listed any properties yet.', 'thessnest' ); ?></p>
				</div>
			<?php endif; ?>
		</div>

	</div>
</main>

<?php get_footer(); ?>
