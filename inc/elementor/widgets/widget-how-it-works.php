<?php
/**
 * ThessNest — How It Works Widget for Elementor
 */

defined( 'ABSPATH' ) || exit;

class ThessNest_How_It_Works_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'thessnest_how_it_works';
	}

	public function get_title() {
		return esc_html__( 'How It Works (ThessNest)', 'thessnest' );
	}

	public function get_icon() {
		return 'eicon-info-box';
	}

	public function get_categories() {
		return [ 'thessnest-elements' ];
	}

	protected function _register_controls() {
		
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Steps Content', 'thessnest' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Section Title', 'thessnest' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'How It Works', 'thessnest' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'subtitle',
			[
				'label'   => esc_html__( 'Section Subtitle', 'thessnest' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Secure your Thessaloniki home in three simple steps.', 'thessnest' ),
			]
		);

		// Step 1
		$this->add_control(
			'step_1_title',
			[
				'label'       => esc_html__( 'Step 1 Title', 'thessnest' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Choose Safe Rooms', 'thessnest' ),
				'separator'   => 'before',
			]
		);
		$this->add_control(
			'step_1_desc',
			[
				'label'       => esc_html__( 'Step 1 Description', 'thessnest' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Browse verified listings near Aristotle University or grocery stores. Filter by high-speed Wi-Fi, budget, and amenities.', 'thessnest' ),
			]
		);

		// Step 2
		$this->add_control(
			'step_2_title',
			[
				'label'       => esc_html__( 'Step 2 Title', 'thessnest' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Book', 'thessnest' ),
				'separator'   => 'before',
			]
		);
		$this->add_control(
			'step_2_desc',
			[
				'label'       => esc_html__( 'Step 2 Description', 'thessnest' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Reserve your place online with transparent pricing. No hidden fees, no surprises.', 'thessnest' ),
			]
		);

		// Step 3
		$this->add_control(
			'step_3_title',
			[
				'label'       => esc_html__( 'Step 3 Title', 'thessnest' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Move In', 'thessnest' ),
				'separator'   => 'before',
			]
		);
		$this->add_control(
			'step_3_desc',
			[
				'label'       => esc_html__( 'Step 3 Description', 'thessnest' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Arrive in Thessaloniki and settle into your new home. Welcome to the city!', 'thessnest' ),
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<section class="section how-it-works widget-how-it-works" aria-labelledby="hiw-heading-<?php echo esc_attr( $this->get_id() ); ?>">
			<div class="container">

				<div class="section-header">
					<h2 class="section-title" id="hiw-heading-<?php echo esc_attr( $this->get_id() ); ?>">
						<?php echo esc_html( $settings['title'] ); ?>
					</h2>
					<?php if ( ! empty( $settings['subtitle'] ) ) : ?>
					<p class="section-subtitle">
						<?php echo esc_html( $settings['subtitle'] ); ?>
					</p>
					<?php endif; ?>
				</div>

				<div class="steps-grid">

					<div class="step-card">
						<div class="step-icon">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
							</svg>
						</div>
						<span class="step-number">1</span>
						<h3 class="step-title"><?php echo esc_html( $settings['step_1_title'] ); ?></h3>
						<p class="step-desc"><?php echo wp_kses_post( $settings['step_1_desc'] ); ?></p>
					</div>

					<div class="step-card">
						<div class="step-icon">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
								<path d="M9 16l2 2 4-4"/>
							</svg>
						</div>
						<span class="step-number">2</span>
						<h3 class="step-title"><?php echo esc_html( $settings['step_2_title'] ); ?></h3>
						<p class="step-desc"><?php echo wp_kses_post( $settings['step_2_desc'] ); ?></p>
					</div>

					<div class="step-card">
						<div class="step-icon">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
								<polyline points="9 22 9 12 15 12 15 22"/>
							</svg>
						</div>
						<span class="step-number">3</span>
						<h3 class="step-title"><?php echo esc_html( $settings['step_3_title'] ); ?></h3>
						<p class="step-desc"><?php echo wp_kses_post( $settings['step_3_desc'] ); ?></p>
					</div>

				</div>
			</div>
		</section>
		<?php
	}
}
