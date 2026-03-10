<?php
/**
 * ThessNest — Hero Search Widget for Elementor
 */

defined( 'ABSPATH' ) || exit;

class ThessNest_Hero_Search_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'thessnest_hero_search';
	}

	public function get_title() {
		return esc_html__( 'Hero Search (ThessNest)', 'thessnest' );
	}

	public function get_icon() {
		return 'eicon-search';
	}

	public function get_categories() {
		return [ 'thessnest-elements' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Hero Content', 'thessnest' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'thessnest' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Find Your Home in Thessaloniki', 'thessnest' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'subtitle',
			[
				'label'       => esc_html__( 'Subtitle', 'thessnest' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Find student accommodation no agency fee and digital nomad apartments in Thessaloniki. Verified landlords, instant booking.', 'thessnest' ),
			]
		);

		$this->add_control(
			'bg_image',
			[
				'label'   => esc_html__( 'Background Image', 'thessnest' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => get_theme_file_uri( 'assets/images/Thessaloniki_Resized.jpg' ),
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$bg_url   = ! empty( $settings['bg_image']['url'] ) ? $settings['bg_image']['url'] : '';
		?>
		<section class="hero-section hero-widget" style="background-image:url('<?php echo esc_url( $bg_url ); ?>');" aria-label="<?php esc_attr_e( 'Search for housing', 'thessnest' ); ?>">

			<div class="hero-orb hero-orb--1" aria-hidden="true"></div>
			<div class="hero-orb hero-orb--2" aria-hidden="true"></div>
			<div class="hero-orb hero-orb--3" aria-hidden="true"></div>

			<div class="hero-content">

				<h1 class="hero-title">
					<?php echo esc_html( $settings['title'] ); ?>
				</h1>
				<p class="hero-subtitle">
					<?php echo wp_kses_post( $settings['subtitle'] ); ?>
				</p>

				<form class="search-bar" role="search" method="get" action="<?php echo esc_url( get_post_type_archive_link( 'property' ) ); ?>">

					<div class="search-field">
						<svg class="field-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
						</svg>
						<input type="text" name="s" placeholder="<?php esc_attr_e( 'What are you looking for?', 'thessnest' ); ?>" aria-label="<?php esc_attr_e( 'Search keywords', 'thessnest' ); ?>">
						<input type="hidden" name="post_type" value="property">
					</div>

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

					<div class="search-field">
						<svg class="field-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
						</svg>
						<input type="date" name="move_in_date" aria-label="<?php esc_attr_e( 'Move-in date', 'thessnest' ); ?>">
					</div>

					<button type="submit" class="btn-search">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
						</svg>
						<?php esc_html_e( 'Search', 'thessnest' ); ?>
					</button>
				</form>

			</div>
		</section>
		<?php
	}
}
