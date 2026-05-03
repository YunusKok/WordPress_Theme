<?php
/**
 * ThessNest — Elementor Blog Posts Widget
 *
 * @package ThessNest
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ThessNest_Blog_Posts_Widget extends Widget_Base {

	public function get_name() {
		return 'thessnest_blog_posts';
	}

	public function get_title() {
		return esc_html__( 'Blog Posts Grid', 'thessnest' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return [ 'thessnest-elements' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'thessnest' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		// Get all categories for the dropdown
		$categories = get_terms( [
			'taxonomy'   => 'category',
			'hide_empty' => false,
		] );

		$category_options = [ '' => esc_html__( 'All Categories', 'thessnest' ) ];
		if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
			foreach ( $categories as $category ) {
				$category_options[ $category->term_id ] = $category->name;
			}
		}

		$this->add_control(
			'category_id',
			[
				'label'   => esc_html__( 'Category', 'thessnest' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $category_options,
				'default' => '',
			]
		);

		$this->add_control(
			'posts_limit',
			[
				'label'   => esc_html__( 'Number of posts to show', 'thessnest' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 9,
				'min'     => 1,
				'max'     => 100,
			]
		);

		$this->add_control(
			'offset',
			[
				'label'   => esc_html__( 'Offset', 'thessnest' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
				'min'     => 0,
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$args = array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => ! empty( $settings['posts_limit'] ) ? absint( $settings['posts_limit'] ) : 9,
			'offset'              => ! empty( $settings['offset'] ) ? absint( $settings['offset'] ) : 0,
		);

		if ( ! empty( $settings['category_id'] ) ) {
			$args['cat'] = absint( $settings['category_id'] );
		}

		$the_query = new \WP_Query( $args );

		if ( $the_query->have_posts() ) :
			?>
			<div class="thessnest-elementor-blog-posts">
				<div class="blog-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: var(--space-8);">
					<?php
					while ( $the_query->have_posts() ) :
						$the_query->the_post();
						?>
						<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="background:var(--color-surface);border-radius:var(--radius-xl);overflow:hidden;box-shadow:var(--shadow-card);transition:transform 0.2s ease;">
							<?php if ( has_post_thumbnail() ) : ?>
								<a href="<?php the_permalink(); ?>" class="post-thumbnail-link" style="display:block;height:220px;overflow:hidden;">
									<?php the_post_thumbnail( 'card-thumb', array( 'style' => 'width:100%;height:100%;object-fit:cover;' ) ); ?>
								</a>
							<?php endif; ?>
							
							<div class="post-content-wrap" style="padding: var(--space-6);">
								<header class="entry-header">
									<div class="entry-meta" style="font-size:var(--font-size-xs);color:var(--color-text-muted);margin-bottom:var(--space-2);text-transform:uppercase;letter-spacing:0.05em;">
										<?php echo get_the_date(); ?> • <?php the_category( ', ' ); ?>
									</div>
									<h2 class="card-title" style="margin-top:0;font-size:var(--font-size-lg);line-height:1.4;">
										<a href="<?php the_permalink(); ?>" style="color:inherit;text-decoration:none;"><?php the_title(); ?></a>
									</h2>
								</header>
								
								<div class="entry-summary" style="margin-top: var(--space-3); color: var(--color-text-muted); font-size: var(--font-size-sm); line-height:1.6;">
									<?php the_excerpt(); ?>
								</div>
								
								<div style="margin-top: var(--space-4);">
									<a href="<?php the_permalink(); ?>" style="color:var(--color-primary);font-weight:600;font-size:var(--font-size-sm);text-decoration:underline;"><?php esc_html_e( 'Read More', 'thessnest' ); ?></a>
								</div>
							</div>
						</article>
					<?php endwhile; ?>
				</div>
			</div>
			<?php
			wp_reset_postdata();
		else :
			?>
			<div class="text-center">
				<p class="text-muted"><?php esc_html_e( 'No content found.', 'thessnest' ); ?></p>
			</div>
			<?php
		endif;
	}
}
