<?php
/**
 * ThessNest — Property Grid Widget for Elementor
 */

defined( 'ABSPATH' ) || exit;

class ThessNest_Property_Grid_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'thessnest_property_grid';
	}

	public function get_title() {
		return esc_html__( 'Property Grid (ThessNest)', 'thessnest' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return [ 'thessnest-elements' ];
	}

	protected function _register_controls() {
		
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Grid Settings', 'thessnest' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Section Title', 'thessnest' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Featured Properties', 'thessnest' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'subtitle',
			[
				'label'   => esc_html__( 'Section Subtitle', 'thessnest' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Verified landlord student apartments & digital nomad flats ready for move-in.', 'thessnest' ),
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label'   => esc_html__( 'Number of Properties', 'thessnest' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 8,
				'min'     => 1,
				'max'     => 40,
			]
		);

		$this->add_control(
			'only_featured',
			[
				'label'        => esc_html__( 'Show Only Featured?', 'thessnest' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
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
		?>
		<section class="section featured-section widget-featured-section" aria-labelledby="featured-heading-<?php echo esc_attr( $this->get_id() ); ?>">
			<div class="container">

				<div class="section-header">
					<h2 class="section-title" id="featured-heading-<?php echo esc_attr( $this->get_id() ); ?>">
						<?php echo esc_html( $settings['title'] ); ?>
					</h2>
					<?php if ( ! empty( $settings['subtitle'] ) ) : ?>
					<p class="section-subtitle">
						<?php echo wp_kses_post( $settings['subtitle'] ); ?>
					</p>
					<?php endif; ?>
				</div>

				<?php
				$args = array(
					'post_type'      => 'property',
					'posts_per_page' => isset( $settings['posts_per_page'] ) ? intval( $settings['posts_per_page'] ) : 8,
					'orderby'        => 'date',
					'order'          => 'DESC',
				);

				if ( 'yes' === $settings['only_featured'] ) {
					$args['meta_key']   = '_thessnest_featured';
					$args['meta_value'] = '1';
				}

				$property_query = new \WP_Query( $args );

				// Fallback to non-featured if strict featured didn't find any.
				if ( ! $property_query->have_posts() && 'yes' === $settings['only_featured'] ) {
					unset( $args['meta_key'] );
					unset( $args['meta_value'] );
					$property_query = new \WP_Query( $args );
				}

				if ( $property_query->have_posts() ) : ?>
					<div class="property-grid">
						<?php
						while ( $property_query->have_posts() ) :
							$property_query->the_post();
							// Load theme's card component
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
		<?php
	}
}
