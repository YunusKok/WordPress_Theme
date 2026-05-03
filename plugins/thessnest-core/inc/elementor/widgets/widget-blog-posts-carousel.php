<?php
/**
 * ThessNest — Elementor Blog Posts Carousel Widget
 *
 * @package ThessNest
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ThessNest_Blog_Posts_Carousel_Widget extends Widget_Base {

	public function get_name() {
		return 'thessnest_blog_posts_carousel';
	}

	public function get_title() {
		return esc_html__( 'Blog Posts Carousel', 'thessnest' );
	}

	public function get_icon() {
		return 'eicon-posts-carousel';
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

		$this->start_controls_section(
			'carousel_settings_section',
			[
				'label' => esc_html__( 'Carousel Settings', 'thessnest' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'slides_to_show',
			[
				'label'   => esc_html__( 'Slides to Show', 'thessnest' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 3,
				'min'     => 1,
				'max'     => 6,
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'        => esc_html__( 'Autoplay', 'thessnest' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'thessnest' ),
				'label_off'    => esc_html__( 'No', 'thessnest' ),
				'return_value' => 'yes',
				'default'      => 'yes',
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

		$slides_to_show = ! empty( $settings['slides_to_show'] ) ? absint( $settings['slides_to_show'] ) : 3;
		$autoplay = $settings['autoplay'] === 'yes' ? 'true' : 'false';

		$the_query = new \WP_Query( $args );

		if ( $the_query->have_posts() ) :
			$widget_id = 'blog-carousel-' . $this->get_id();
			?>
			<div class="thessnest-elementor-blog-posts-carousel" style="position:relative; width: 100%; overflow: hidden;">
				<div class="swiper blog-swiper" id="<?php echo esc_attr( $widget_id ); ?>" style="padding-bottom: 40px;">
					<div class="swiper-wrapper">
						<?php
						while ( $the_query->have_posts() ) :
							$the_query->the_post();
							?>
							<div class="swiper-slide" style="height: auto;">
								<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="background:var(--color-surface);border-radius:var(--radius-xl);overflow:hidden;box-shadow:var(--shadow-card);transition:transform 0.2s ease;height: 100%;">
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
							</div>
						<?php endwhile; ?>
					</div>
					<!-- Pagination -->
					<div class="swiper-pagination"></div>
				</div>
			</div>

			<script>
				document.addEventListener('DOMContentLoaded', function() {
					if (typeof Swiper !== 'undefined') {
						new Swiper('#<?php echo esc_js( $widget_id ); ?>', {
							slidesPerView: 1,
							spaceBetween: 24,
							loop: true,
							autoplay: <?php echo $autoplay === 'true' ? '{ delay: 5000, disableOnInteraction: false }' : 'false'; ?>,
							pagination: {
								el: '#<?php echo esc_js( $widget_id ); ?> .swiper-pagination',
								clickable: true,
							},
							breakpoints: {
								640: {
									slidesPerView: 2,
								},
								1024: {
									slidesPerView: <?php echo esc_js( $slides_to_show ); ?>,
								}
							}
						});
					}
				});
			</script>
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
