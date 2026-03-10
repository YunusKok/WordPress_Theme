<?php
/**
 * Template Name: Frontend Dashboard
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

// Redirect to login if user is not authenticated
if ( ! is_user_logged_in() ) {
	auth_redirect();
}

$user = wp_get_current_user();
$is_landlord = in_array( 'landlord', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles );

// Determine Active Tab
$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'saved';

// Ensure landlords default to 'properties', tenants to 'saved' if no tab is provided and we want different defaults.
// For now, we'll keep 'saved' as default for everyone unless specified, or change logic here:
if ( ! isset( $_GET['tab'] ) && $is_landlord ) {
	$active_tab = 'properties';
}

$dashboard_url = get_permalink();

get_header();
?>

<main id="main-content" role="main" style="background:var(--color-surface); min-height:80vh; padding:var(--space-10) 0;">
	<div class="container">
		
		<header style="margin-bottom:var(--space-8); border-bottom:1px solid var(--color-border); padding-bottom:var(--space-4);">
			<h1 style="color:var(--color-primary); font-size:var(--font-size-3xl);">
				<?php esc_html_e( 'My Dashboard', 'thessnest' ); ?>
			</h1>
			<p style="color:var(--color-text-muted);">
				<?php printf( esc_html__( 'Welcome back, %s!', 'thessnest' ), esc_html( $user->display_name ) ); ?>
			</p>
		</header>

		<div style="display:flex; flex-direction:column; gap:var(--space-8); @media(min-width: 768px){ flex-direction:row; }">
			
			<!-- Sidebar Navigation -->
			<aside style="flex:1; min-width: 250px;">
				<ul style="background:var(--glass-bg); padding:var(--space-4); border-radius:var(--radius-lg); border:1px solid var(--color-border); list-style:none; margin:0;">
					
					<!-- Profile Tab -->
					<li style="margin-bottom:var(--space-2);">
						<a href="<?php echo esc_url( add_query_arg( 'tab', 'profile', $dashboard_url ) ); ?>" style="display:block; padding:var(--space-2); border-radius:var(--radius-md); <?php echo ( 'profile' === $active_tab ) ? 'background:var(--color-primary); color:white; font-weight:600;' : 'color:var(--color-text);'; ?>">
							<?php esc_html_e( 'My Profile', 'thessnest' ); ?>
						</a>
					</li>
					
					<!-- Landlord Specific Tabs -->
					<?php if ( $is_landlord ) : ?>
						<li style="margin-bottom:var(--space-2);">
							<a href="<?php echo esc_url( add_query_arg( 'tab', 'properties', $dashboard_url ) ); ?>" style="display:block; padding:var(--space-2); border-radius:var(--radius-md); <?php echo ( 'properties' === $active_tab ) ? 'background:var(--color-primary); color:white; font-weight:600;' : 'color:var(--color-text);'; ?>">
								<?php esc_html_e( 'My Properties', 'thessnest' ); ?>
							</a>
						</li>
						<li style="margin-bottom:var(--space-2);">
							<a href="<?php echo esc_url( home_url( '/add-listing/' ) ); ?>" style="display:block; padding:var(--space-2); border-radius:var(--radius-md); color:var(--color-accent); font-weight:600;">
								<?php esc_html_e( '+ Add New Property', 'thessnest' ); ?>
							</a>
						</li>
					<?php endif; ?>

					<!-- Saved Properties -->
					<li style="margin-bottom:var(--space-2);">
						<a href="<?php echo esc_url( add_query_arg( 'tab', 'saved', $dashboard_url ) ); ?>" style="display:block; padding:var(--space-2); border-radius:var(--radius-md); <?php echo ( 'saved' === $active_tab ) ? 'background:var(--color-primary); color:white; font-weight:600;' : 'color:var(--color-text);'; ?>">
							<?php esc_html_e( 'Saved Properties', 'thessnest' ); ?>
						</a>
					</li>
					
					<!-- Inbox (Messages) -->
					<li style="margin-bottom:var(--space-2);">
						<a href="<?php echo esc_url( add_query_arg( 'tab', 'inbox', $dashboard_url ) ); ?>" style="display:flex; justify-content:space-between; align-items:center; padding:var(--space-2); border-radius:var(--radius-md); <?php echo ( 'inbox' === $active_tab ) ? 'background:var(--color-primary); color:white; font-weight:600;' : 'color:var(--color-text);'; ?>">
							<span><?php esc_html_e( 'Inbox', 'thessnest' ); ?></span>
							<?php
							// Simple unread count check
							global $wpdb;
							$unread = $wpdb->get_var( $wpdb->prepare( "
								SELECT COUNT(*) FROM {$wpdb->postmeta} pm
								INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
								WHERE p.post_type = 'thessnest_message' AND pm.meta_key = '_recipient_id' AND pm.meta_value = %d
								AND p.ID IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_is_read' AND meta_value = '0')
							", $user->ID ) );
							if ( $unread > 0 ) {
								echo '<span style="background:#e53e3e; color:white; font-size:10px; padding:2px 6px; border-radius:10px; font-weight:bold;">' . esc_html( $unread ) . '</span>';
							}
							?>
						</a>
					</li>

					<!-- Bookings / Trips -->
					<li style="margin-bottom:var(--space-2);">
						<a href="<?php echo esc_url( add_query_arg( 'tab', 'bookings', $dashboard_url ) ); ?>" style="display:block; padding:var(--space-2); border-radius:var(--radius-md); <?php echo ( 'bookings' === $active_tab ) ? 'background:var(--color-primary); color:white; font-weight:600;' : 'color:var(--color-text);'; ?>">
							<?php esc_html_e( 'Bookings', 'thessnest' ); ?>
						</a>
					</li>

					<!-- Log Out -->
					<li style="margin-top:var(--space-4); border-top:1px solid var(--color-border); padding-top:var(--space-4);">
						<a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" style="display:block; padding:var(--space-2); color:#e53e3e; font-weight:600;">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px; vertical-align:middle;">
								<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line>
							</svg>
							<?php esc_html_e( 'Log Out', 'thessnest' ); ?>
						</a>
					</li>
				</ul>
			</aside>

			<!-- Main Content Area -->
			<section style="flex:3; background:var(--color-background); border:1px solid var(--color-border); border-radius:var(--radius-xl); padding:var(--space-6); box-shadow:var(--shadow-sm);">
				
				<?php
				/* ==========================================================================
				   TAB: MY PROFILE
				   ========================================================================== */
				if ( 'profile' === $active_tab ) :
				?>
					<h2 style="font-size:var(--font-size-xl); margin-bottom:var(--space-4); color:var(--color-primary);">
						<?php esc_html_e( 'My Profile', 'thessnest' ); ?>
					</h2>
					<p style="color:var(--color-text-muted); margin-bottom:var(--space-6);">
						<?php esc_html_e( 'Update your personal information below.', 'thessnest' ); ?>
					</p>

					<form id="dashboard-profile-form" style="max-width:500px;">
						<?php wp_nonce_field( 'thessnest_dashboard_nonce', 'security' ); ?>
						<input type="hidden" name="action" value="thessnest_update_profile">

						<div class="form-group" style="margin-bottom:var(--space-4);">
							<label for="first_name" style="display:block; margin-bottom:var(--space-2); font-weight:600; color:var(--color-text);"><?php esc_html_e( 'First Name', 'thessnest' ); ?></label>
							<input type="text" id="first_name" name="first_name" value="<?php echo esc_attr( $user->first_name ); ?>" required style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
						</div>

						<div class="form-group" style="margin-bottom:var(--space-4);">
							<label for="last_name" style="display:block; margin-bottom:var(--space-2); font-weight:600; color:var(--color-text);"><?php esc_html_e( 'Last Name', 'thessnest' ); ?></label>
							<input type="text" id="last_name" name="last_name" value="<?php echo esc_attr( $user->last_name ); ?>" required style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
						</div>

						<div class="form-group" style="margin-bottom:var(--space-6);">
							<label for="user_email" style="display:block; margin-bottom:var(--space-2); font-weight:600; color:var(--color-text);"><?php esc_html_e( 'Email Address', 'thessnest' ); ?></label>
							<input type="email" id="user_email" name="user_email" value="<?php echo esc_attr( $user->user_email ); ?>" required style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
						</div>

						<div id="profile-response" style="margin-bottom:var(--space-4); padding:var(--space-3); border-radius:var(--radius-md); display:none; font-weight:500; font-size:var(--font-size-sm);"></div>

						<button type="submit" id="profile-submit-btn" class="btn btn-primary" style="padding:var(--space-3) var(--space-6);">
							<?php esc_html_e( 'Save Changes', 'thessnest' ); ?>
						</button>
					</form>

					<?php 
					// KYC Section for Landlords
					if ( $is_landlord ) : 
						$kyc_status = get_user_meta( $user->ID, '_kyc_status', true );
						if ( ! $kyc_status ) $kyc_status = 'unverified';
					?>
						<div style="margin-top:var(--space-10); padding-top:var(--space-6); border-top:1px solid var(--color-border);">
							<h3 style="font-size:var(--font-size-lg); margin-bottom:var(--space-2); color:var(--color-primary);"><?php esc_html_e( 'Trust & Safety: ID Verification', 'thessnest' ); ?></h3>
							<p style="color:var(--color-text-muted); margin-bottom:var(--space-4); font-size:var(--font-size-sm);">
								<?php esc_html_e( 'Verify your identity to get the "Verified Landlord" badge and build trust with tenants.', 'thessnest' ); ?>
							</p>

							<?php if ( 'approved' === $kyc_status ) : ?>
								<div style="display:inline-flex; align-items:center; gap:var(--space-2); padding:var(--space-3) var(--space-4); background:#f0fdf4; border:1px solid #bbf7d0; border-radius:var(--radius-md); color:#166534; font-weight:600;">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
									<?php esc_html_e( 'You are a Verified Landlord.', 'thessnest' ); ?>
								</div>
							<?php elseif ( 'pending' === $kyc_status ) : ?>
								<div style="display:inline-flex; align-items:center; gap:var(--space-2); padding:var(--space-3) var(--space-4); background:#fffbeb; border:1px solid #fde68a; border-radius:var(--radius-md); color:#b45309; font-weight:600;">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
									<?php esc_html_e( 'Your document is under review by admins.', 'thessnest' ); ?>
								</div>
							<?php else : ?>
								<form id="kyc-upload-form" enctype="multipart/form-data" style="background:var(--color-surface); padding:var(--space-4); border-radius:var(--radius-lg); border:1px solid var(--color-border); max-width:500px;">
									<div style="margin-bottom:var(--space-3);">
										<label style="display:block; font-size:var(--font-size-sm); font-weight:500; margin-bottom:var(--space-1);"><?php esc_html_e('Upload valid ID (Passport/National ID)', 'thessnest'); ?></label>
										<input type="file" name="kyc_document" id="kyc_document" accept=".jpg,.jpeg,.png,.pdf" required style="display:block; width:100%; font-size:var(--font-size-sm);">
										<small style="color:var(--color-text-muted); display:block; margin-top:4px;">Max 5MB. PDF, JPG, PNG.</small>
									</div>
									<div id="kyc-response" style="margin-bottom:var(--space-3); font-size:13px; display:none; padding:var(--space-2); border-radius:var(--radius-md);"></div>
									<button type="submit" id="kyc-submit-btn" class="btn btn-primary" style="padding:var(--space-2) var(--space-4); font-size:var(--font-size-sm);">
										<?php esc_html_e('Submit for Verification', 'thessnest'); ?>
									</button>
								</form>
							<?php endif; ?>
						</div>
					<?php endif; ?>

				<?php
				/* ==========================================================================
				   TAB: MY PROPERTIES (LANDLORD ONLY)
				   ========================================================================== */
				elseif ( 'properties' === $active_tab && $is_landlord ) :
				?>
					<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-6);">
						<h2 style="font-size:var(--font-size-xl); margin:0; color:var(--color-primary);">
							<?php esc_html_e( 'My Properties & Analytics', 'thessnest' ); ?>
						</h2>
						<a href="<?php echo esc_url( home_url( '/add-listing/' ) ); ?>" class="btn btn-outline" style="padding:var(--space-2) var(--space-4); font-size:var(--font-size-sm);">
							<?php esc_html_e( 'Create New', 'thessnest' ); ?>
						</a>
					</div>

					<!-- Performance Overview Chart -->
					<div style="background:var(--color-surface); border:1px solid var(--color-border); border-radius:var(--radius-lg); padding:var(--space-6); margin-bottom:var(--space-8);">
						<h3 style="margin-top:0; font-size:var(--font-size-lg); color:var(--color-text); margin-bottom:var(--space-4);">
							<span class="dashicons dashicons-chart-area" style="vertical-align:text-bottom;"></span> <?php esc_html_e( 'Performance Overview', 'thessnest' ); ?>
						</h3>
						<div class="chart-container" style="position:relative; height:300px; width:100%;">
							<canvas id="landlordAnalyticsChart"></canvas>
						</div>
					</div>

					<?php
					$chart_json = function_exists( 'thessnest_get_landlord_chart_data' ) ? thessnest_get_landlord_chart_data( $user->ID ) : '{}';
					?>
					<script>
					document.addEventListener("DOMContentLoaded", function() {
						if (typeof Chart !== 'undefined') {
							var chartData = <?php echo $chart_json; ?>;
							if (chartData && chartData.labels && chartData.labels.length > 0) {
								var ctx = document.getElementById('landlordAnalyticsChart').getContext('2d');
								new Chart(ctx, {
									type: 'bar',
									data: {
										labels: chartData.labels,
										datasets: [
											{
												label: 'Total Views',
												data: chartData.views,
												backgroundColor: 'rgba(37, 99, 235, 0.7)', // Primary blue
												borderColor: 'rgba(37, 99, 235, 1)',
												borderWidth: 1,
												yAxisID: 'y'
											},
											{
												label: 'Monthly Rent (€)',
												data: chartData.prices,
												type: 'line',
												backgroundColor: 'rgba(56, 161, 105, 0.2)', // Green
												borderColor: 'rgba(56, 161, 105, 1)',
												borderWidth: 2,
												pointBackgroundColor: 'rgba(56, 161, 105, 1)',
												tension: 0.4,
												yAxisID: 'y1'
											}
										]
									},
									options: {
										responsive: true,
										maintainAspectRatio: false,
										interaction: {
											mode: 'index',
											intersect: false,
										},
										scales: {
											y: {
												type: 'linear',
												display: true,
												position: 'left',
												title: { display: true, text: 'Views' }
											},
											y1: {
												type: 'linear',
												display: true,
												position: 'right',
												title: { display: true, text: 'Rent (€)' },
												grid: { drawOnChartArea: false }
											}
										}
									}
								});
							} else {
								document.getElementById('landlordAnalyticsChart').parentElement.innerHTML = 
									'<p style="text-align:center; color:gray; padding-top:100px;"><?php esc_html_e("Not enough data to display analytics.", "thessnest"); ?></p>';
							}
						}
					});
					</script>

					<h3 style="font-size:var(--font-size-lg); border-bottom:1px solid var(--color-border); padding-bottom:var(--space-2); margin-bottom:var(--space-4);">
						<?php esc_html_e( 'Active Listings', 'thessnest' ); ?>
					</h3>

					<?php
					$landlord_query = new WP_Query( array(
						'post_type'      => 'property',
						'author'         => $user->ID,
						'post_status'    => array( 'publish', 'pending', 'draft' ),
						'posts_per_page' => -1,
					) );

					if ( $landlord_query->have_posts() ) : ?>
						<div class="dashboard-properties-list" style="display:flex; flex-direction:column; gap:var(--space-4);">
							<?php while ( $landlord_query->have_posts() ) : $landlord_query->the_post(); 
								$status = get_post_status();
								$status_label = '';
								$status_color = '';
								switch ( $status ) {
									case 'publish':
										$status_label = __( 'Published', 'thessnest' );
										$status_color = '#38a169'; // Green
										break;
									case 'pending':
										$status_label = __( 'Pending Review (Awaiting Payment)', 'thessnest' );
										$status_color = '#d97706'; // Orange
										break;
									default:
										$status_label = __( 'Draft', 'thessnest' );
										$status_color = '#718096'; // Gray
								}
							?>
								<div class="dashboard-property-item" id="my-property-<?php the_ID(); ?>" style="display:flex; align-items:center; gap:var(--space-4); background:var(--color-surface); border:1px solid var(--color-border); padding:var(--space-3); border-radius:var(--radius-lg);">
									
									<div style="width:80px; height:80px; border-radius:var(--radius-md); overflow:hidden; flex-shrink:0;">
										<?php if ( has_post_thumbnail() ) : ?>
											<?php the_post_thumbnail( 'thumbnail', array( 'style' => 'width:100%; height:100%; object-fit:cover;' ) ); ?>
										<?php else : ?>
											<div style="width:100%; height:100%; background:var(--color-border);"></div>
										<?php endif; ?>
									</div>
									
									<div style="flex:1;">
										<h3 style="font-size:var(--font-size-base); margin:0 0 var(--space-1) 0; color:var(--color-text);">
											<?php if ( 'publish' === $status ) : ?>
												<a href="<?php the_permalink(); ?>" target="_blank" style="color:inherit;"><?php the_title(); ?></a>
											<?php else : ?>
												<?php the_title(); ?>
											<?php endif; ?>
										</h3>
										<div style="font-size:var(--font-size-sm); color:var(--color-text-muted); display:flex; gap:var(--space-3);">
											<span style="display:inline-flex; align-items:center; gap:4px; color:<?php echo esc_attr($status_color); ?>;">
												<span style="width:8px; height:8px; border-radius:50%; background:currentColor;"></span>
												<?php echo esc_html( $status_label ); ?>
											</span>
											<span><?php echo esc_html( thessnest_format_price( get_post_meta( get_the_ID(), '_thessnest_rent', true ) ) ); ?></span>
										</div>
									</div>

									<div style="display:flex; gap:var(--space-2);">
										<?php if ( 'pending' === $status && class_exists('WooCommerce') ) : ?>
											<button class="btn btn-primary btn-pay-publish" data-property-id="<?php the_ID(); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'thessnest_dashboard_nonce' ) ); ?>" style="padding:var(--space-2) var(--space-4); background-color:#38a169; border-color:#38a169;" title="<?php esc_attr_e('Pay Listing Fee to Publish', 'thessnest'); ?>">
												<?php esc_html_e( 'Pay to Publish', 'thessnest' ); ?>
											</button>
										<?php endif; ?>
										<button class="btn btn-outline btn-delete-property" data-property-id="<?php the_ID(); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'thessnest_dashboard_nonce' ) ); ?>" style="padding:var(--space-2); color:#e53e3e; border-color:#fc8181;" title="<?php esc_attr_e('Move to Trash', 'thessnest'); ?>">
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
											</svg>
										</button>
									</div>
								</div>
							<?php endwhile; ?>
						</div>
						<?php wp_reset_postdata(); ?>
					<?php else : ?>
						<p style="color:var(--color-text-muted); padding:var(--space-8); text-align:center; background:var(--color-surface); border-radius:var(--radius-lg); border:1px dashed var(--color-border);">
							<?php esc_html_e( 'You have not submitted any properties yet.', 'thessnest' ); ?>
						</p>
					<?php endif; ?>

				<?php
				/* ==========================================================================
				   TAB: SAVED PROPERTIES
				   ========================================================================== */
				elseif ( 'saved' === $active_tab ) : 
				?>
					<h2 style="font-size:var(--font-size-xl); margin-bottom:var(--space-6); color:var(--color-primary);">
						<?php esc_html_e( 'Saved Properties', 'thessnest' ); ?>
					</h2>

					<?php
					$favorites = get_user_meta( $user->ID, 'thessnest_favorites', true );
					if ( ! empty( $favorites ) && is_array( $favorites ) ) :
						$fav_query = new WP_Query( array(
							'post_type'      => 'property',
							'post__in'       => $favorites,
							'posts_per_page' => -1,
						) );

						if ( $fav_query->have_posts() ) : ?>
							<div class="property-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: var(--space-6);">
								<?php while ( $fav_query->have_posts() ) : $fav_query->the_post(); ?>
									<?php get_template_part( 'template-parts/property-card' ); ?>
								<?php endwhile; ?>
							</div>
							<?php wp_reset_postdata(); ?>
						<?php else : ?>
							<p style="color:var(--color-text-muted);"><?php esc_html_e( 'No saved properties found. Start exploring!', 'thessnest' ); ?></p>
						<?php endif;
					else : ?>
						<p style="color:var(--color-text-muted);"><?php esc_html_e( 'You have no saved properties yet.', 'thessnest' ); ?></p>
					<?php endif; ?>

				<?php
				/* ==========================================================================
				   TAB: INBOX (INTERNAL MESSAGING)
				   ========================================================================== */
				elseif ( 'inbox' === $active_tab ) :
				?>
					<h2 style="font-size:var(--font-size-xl); margin-bottom:var(--space-6); color:var(--color-primary);">
						<?php esc_html_e( 'Inbox', 'thessnest' ); ?>
					</h2>

					<div class="inbox-container" style="display:flex; height:600px; border:1px solid var(--color-border); border-radius:var(--radius-lg); overflow:hidden; background:var(--color-surface); flex-direction:column; @media(min-width: 768px){ flex-direction:row; }">
						
						<!-- Threads List -->
						<div class="inbox-sidebar" style="flex:1; border-right:1px solid var(--color-border); border-bottom:1px solid var(--color-border); overflow-y:auto; background:var(--glass-bg);">
							<?php
							// Fetch unique threads: (Property ID + Other User ID)
							global $wpdb;

							$threads_query = $wpdb->prepare( "
								SELECT p.ID, p.post_author, p.post_title, p.post_date,
								       MAX(pm_prop.meta_value) as property_id,
								       MAX(pm_recip.meta_value) as recipient_id
								FROM {$wpdb->posts} p
								INNER JOIN {$wpdb->postmeta} pm_prop ON p.ID = pm_prop.post_id AND pm_prop.meta_key = '_property_id'
								INNER JOIN {$wpdb->postmeta} pm_recip ON p.ID = pm_recip.post_id AND pm_recip.meta_key = '_recipient_id'
								WHERE p.post_type = 'thessnest_message' AND p.post_status = 'publish'
								AND (p.post_author = %d OR pm_recip.meta_value = %d)
								GROUP BY property_id,
								         IF(p.post_author = %d, pm_recip.meta_value, p.post_author)
								ORDER BY MAX(p.post_date) DESC
							", $user->ID, $user->ID, $user->ID );

							$threads = $wpdb->get_results( $threads_query );

							if ( $threads ) :
								foreach ( $threads as $thread ) :
									$other_user_id = ( (int) $thread->post_author === $user->ID ) ? (int) $thread->recipient_id : (int) $thread->post_author;
									$other_user    = get_userdata( $other_user_id );
									$prop_title    = get_the_title( $thread->property_id );
									?>
									<div class="thread-item" data-other-user="<?php echo esc_attr( $other_user_id ); ?>" data-property="<?php echo esc_attr( $thread->property_id ); ?>" style="padding:var(--space-4); border-bottom:1px solid var(--color-border); cursor:pointer; transition:background 0.2s;">
										<div style="display:flex; align-items:center; gap:var(--space-3);">
											<?php echo get_avatar( $other_user_id, 40, '', '', array( 'style' => 'border-radius:50%;' ) ); ?>
											<div style="flex:1; overflow:hidden;">
												<h4 style="margin:0; font-size:var(--font-size-base); color:var(--color-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
													<?php echo esc_html( $other_user ? $other_user->display_name : 'Unknown User' ); ?>
												</h4>
												<p style="margin:0; font-size:var(--font-size-sm); color:var(--color-text-muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
													<?php echo esc_html( $prop_title ); ?>
												</p>
											</div>
										</div>
									</div>
								<?php
								endforeach;
							else : ?>
								<div style="padding:var(--space-6); text-align:center; color:var(--color-text-muted);">
									<?php esc_html_e( 'No messages yet.', 'thessnest' ); ?>
								</div>
							<?php endif; ?>
						</div>

						<!-- Chat Area -->
						<div class="inbox-chat-area" style="flex:2; display:flex; flex-direction:column; background:var(--color-background);">
							
							<div id="chat-header" style="padding:var(--space-4); border-bottom:1px solid var(--color-border); background:var(--color-surface); display:none;">
								<h3 id="chat-property-title" style="margin:0; font-size:var(--font-size-lg); color:var(--color-primary);"></h3>
							</div>

							<div id="chat-messages" style="flex:1; padding:var(--space-4); overflow-y:auto; display:flex; flex-direction:column; gap:var(--space-4);">
								<div style="margin:auto; color:var(--color-text-muted); text-align:center;">
									<?php esc_html_e( 'Select a conversation to start messaging.', 'thessnest' ); ?>
								</div>
							</div>

							<div id="chat-reply-form" style="padding:var(--space-4); border-top:1px solid var(--color-border); background:var(--color-surface); display:none;">
								<form id="reply-form" style="display:flex; gap:var(--space-2);">
									<?php wp_nonce_field( 'thessnest_dashboard_nonce', 'inbox_security' ); ?>
									<textarea id="reply-message" required rows="2" placeholder="<?php esc_attr_e( 'Type your message...', 'thessnest' ); ?>" style="flex:1; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-background); font-family:inherit; resize:none;"></textarea>
									<button type="submit" id="reply-btn" class="btn btn-primary" style="height:auto; align-self:stretch;">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
										</svg>
									</button>
								</form>
							</div>

						</div>

					</div>
					
					<style>
						.thread-item:hover, .thread-item.active { background:var(--color-background) !important; }
						.msg-bubble { max-width:70%; padding:var(--space-3); border-radius:var(--radius-lg); font-size:var(--font-size-sm); line-height:1.5; }
						.msg-mine { align-self:flex-end; background:var(--color-primary); color:white; border-bottom-right-radius:2px; }
						.msg-theirs { align-self:flex-start; background:var(--glass-bg); color:var(--color-text); border:1px solid var(--color-border); border-bottom-left-radius:2px; }
					</style>

				<?php
				/* ==========================================================================
				   TAB: BOOKINGS (TRIPS & RESERVATIONS)
				   ========================================================================== */
				elseif ( 'bookings' === $active_tab ) :
				?>
					<h2 style="font-size:var(--font-size-xl); margin-bottom:var(--space-6); color:var(--color-primary);">
						<?php esc_html_e( 'Bookings & Trips', 'thessnest' ); ?>
					</h2>
					<p style="color:var(--color-text-muted); margin-bottom:var(--space-6);">
						<?php esc_html_e( 'Manage your upcoming trips and property reservations.', 'thessnest' ); ?>
					</p>

					<!-- My Trips (As Tenant) -->
					<h3 style="font-size:var(--font-size-lg); border-bottom:1px solid var(--color-border); padding-bottom:var(--space-2); margin-bottom:var(--space-4);"><?php esc_html_e( 'My Trips', 'thessnest' ); ?></h3>
					<?php
					$trips_query = new WP_Query( array(
						'post_type'      => 'thessnest_booking',
						'author'         => $user->ID,
						'posts_per_page' => -1,
						'post_status'    => 'publish'
					) );

					if ( $trips_query->have_posts() ) : ?>
						<div class="booking-list" style="display:flex; flex-direction:column; gap:var(--space-4); margin-bottom:var(--space-8);">
							<?php while ( $trips_query->have_posts() ) : $trips_query->the_post(); 
								$prop_id = get_post_meta( get_the_ID(), '_booking_property_id', true );
								$status  = get_post_meta( get_the_ID(), '_booking_status', true );
								$checkin = get_post_meta( get_the_ID(), '_booking_checkin', true );
								$checkout= get_post_meta( get_the_ID(), '_booking_checkout', true );
								$price   = get_post_meta( get_the_ID(), '_booking_total_price', true );
								
								$status_colors = array(
									'pending'   => '#d97706', // Orange
									'confirmed' => '#38a169', // Green
									'rejected'  => '#e53e3e', // Red
									'canceled'  => '#718096'  // Gray
								);
								$color = isset($status_colors[$status]) ? $status_colors[$status] : '#718096';
							?>
								<div class="booking-item" style="background:var(--color-surface); border:1px solid var(--color-border); border-radius:var(--radius-lg); padding:var(--space-4); display:flex; justify-content:space-between; align-items:center;">
									<div>
										<h4 style="margin:0 0 var(--space-2) 0; font-size:var(--font-size-base);">
											<a href="<?php echo esc_url( get_permalink( $prop_id ) ); ?>" target="_blank" style="color:var(--color-primary);"><?php echo esc_html( get_the_title() ); ?></a>
										</h4>
										<div style="font-size:var(--font-size-sm); color:var(--color-text-muted);">
											<?php echo esc_html( date_i18n('M j, Y', strtotime($checkin)) . ' - ' . date_i18n('M j, Y', strtotime($checkout)) ); ?>
											<span style="margin:0 var(--space-2);">|</span>
											<?php echo esc_html( thessnest_format_price($price) ); ?>
										</div>
									</div>
									<div style="text-align:right;">
										<span class="booking-status-badge" style="display:inline-block; padding:var(--space-1) var(--space-3); border-radius:var(--radius-full); font-size:12px; font-weight:600; background:<?php echo esc_attr($color); ?>20; color:<?php echo esc_attr($color); ?>; text-transform:uppercase;">
											<?php echo esc_html( $status ); ?>
										</span>
										<?php if ( $status === 'pending' || $status === 'confirmed' ) : ?>
											<button class="btn-manage-booking" data-action="cancel" data-id="<?php the_ID(); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce('thessnest_dashboard_nonce') ); ?>" style="display:block; margin-top:var(--space-2); margin-left:auto; font-size:12px; color:#e53e3e; background:none; border:none; cursor:pointer; text-decoration:underline;">
												<?php esc_html_e( 'Cancel Trip', 'thessnest' ); ?>
											</button>
										<?php endif; ?>
										<?php if ( $status === 'confirmed' ) : ?>
											<a href="<?php echo esc_url( add_query_arg( 'download_proof', get_the_ID(), home_url('/') ) ); ?>" target="_blank" class="btn btn-outline" style="display:block; margin-top:var(--space-2); margin-left:auto; font-size:12px; text-decoration:none; padding: 4px 8px; text-align:center;">
												📄 <?php esc_html_e( 'Accommodation Proof', 'thessnest' ); ?>
											</a>
											<button class="btn-extend-stay" data-id="<?php the_ID(); ?>" style="display:block; margin-top:var(--space-2); margin-left:auto; font-size:12px; background:none; border:none; cursor:pointer; color:var(--color-primary); text-decoration:underline;">
												<?php esc_html_e( 'Extend Stay', 'thessnest' ); ?>
											</button>
										<?php endif; ?>
									</div>
								</div>
							<?php endwhile; ?>
						</div>
					<?php else : ?>
						<p style="color:var(--color-text-muted); margin-bottom:var(--space-8);"><?php esc_html_e( 'You have no upcoming trips.', 'thessnest' ); ?></p>
					<?php endif; wp_reset_postdata(); ?>

					<!-- Reservations (If Landlord) -->
					<?php if ( $is_landlord ) : ?>
						<h3 style="font-size:var(--font-size-lg); border-bottom:1px solid var(--color-border); padding-bottom:var(--space-2); margin-bottom:var(--space-4);"><?php esc_html_e( 'Reservations on your properties', 'thessnest' ); ?></h3>
						<?php
						$res_query = new WP_Query( array(
							'post_type'      => 'thessnest_booking',
							'meta_key'       => '_booking_landlord_id',
							'meta_value'     => $user->ID,
							'posts_per_page' => -1,
							'post_status'    => 'publish'
						) );

						if ( $res_query->have_posts() ) : ?>
							<div class="booking-list" style="display:flex; flex-direction:column; gap:var(--space-4);">
								<?php while ( $res_query->have_posts() ) : $res_query->the_post(); 
									$prop_id = get_post_meta( get_the_ID(), '_booking_property_id', true );
									$status  = get_post_meta( get_the_ID(), '_booking_status', true );
									$checkin = get_post_meta( get_the_ID(), '_booking_checkin', true );
									$checkout= get_post_meta( get_the_ID(), '_booking_checkout', true );
									$price   = get_post_meta( get_the_ID(), '_booking_total_price', true );
									$guests  = get_post_meta( get_the_ID(), '_booking_guests', true );
									
									$status_colors = array(
										'pending'   => '#d97706',
										'confirmed' => '#38a169',
										'rejected'  => '#e53e3e',
										'canceled'  => '#718096'
									);
									$color = isset($status_colors[$status]) ? $status_colors[$status] : '#718096';
								?>
									<div class="booking-item" style="background:var(--color-surface); border:1px solid var(--color-border); border-radius:var(--radius-lg); padding:var(--space-4); display:flex; justify-content:space-between; align-items:center;">
										<div>
											<h4 style="margin:0 0 var(--space-2) 0; font-size:var(--font-size-base);">
												<a href="<?php echo esc_url( get_permalink( $prop_id ) ); ?>" target="_blank" style="color:var(--color-primary);"><?php echo esc_html( get_the_title() ); ?></a>
											</h4>
											<div style="font-size:var(--font-size-sm); color:var(--color-text-muted);">
												<strong>Guest:</strong> <?php echo esc_html( get_the_author() ); ?> (<?php echo esc_html($guests); ?> Guests) <br>
												<?php echo esc_html( date_i18n('M j, Y', strtotime($checkin)) . ' - ' . date_i18n('M j, Y', strtotime($checkout)) ); ?>
												<span style="margin:0 var(--space-2);">|</span>
												<strong><?php echo esc_html( thessnest_format_price($price) ); ?></strong>
											</div>
										</div>
										<div style="text-align:right;">
											<span class="booking-status-badge" style="display:inline-block; margin-bottom:var(--space-2); padding:var(--space-1) var(--space-3); border-radius:var(--radius-full); font-size:12px; font-weight:600; background:<?php echo esc_attr($color); ?>20; color:<?php echo esc_attr($color); ?>; text-transform:uppercase;">
												<?php echo esc_html( $status ); ?>
											</span>
											
											<?php if ( $status === 'pending' ) : ?>
												<div class="booking-action-buttons" style="display:flex; gap:var(--space-2); justify-content:flex-end;">
													<button class="btn-manage-booking" data-action="accept" data-id="<?php the_ID(); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce('thessnest_dashboard_nonce') ); ?>" style="padding:var(--space-1) var(--space-2); font-size:12px; cursor:pointer; background:#38a169; color:white; border:none; border-radius:var(--radius-sm);"><?php esc_html_e('Accept', 'thessnest'); ?></button>
													<button class="btn-manage-booking" data-action="reject" data-id="<?php the_ID(); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce('thessnest_dashboard_nonce') ); ?>" style="padding:var(--space-1) var(--space-2); font-size:12px; cursor:pointer; background:none; color:#e53e3e; border:1px solid #fc8181; border-radius:var(--radius-sm);"><?php esc_html_e('Reject', 'thessnest'); ?></button>
												</div>
											<?php endif; ?>
										</div>
									</div>
								<?php endwhile; ?>
							</div>
						<?php else : ?>
							<p style="color:var(--color-text-muted);"><?php esc_html_e( 'You have no reservations on your properties.', 'thessnest' ); ?></p>
						<?php endif; wp_reset_postdata(); ?>
					<?php endif; ?>

				<?php endif; ?>

			</section>
		</div>

	</div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function() {
	var extendBtns = document.querySelectorAll('.btn-extend-stay');
	extendBtns.forEach(function(btn) {
		btn.addEventListener('click', function(e) {
			e.preventDefault();
			var newDate = prompt("<?php esc_html_e( 'Enter new checkout date (YYYY-MM-DD):', 'thessnest' ); ?>");
			if (newDate) {
				var formData = new FormData();
				formData.append('action', 'thessnest_extend_booking');
				formData.append('booking_id', this.dataset.id);
				formData.append('new_checkout', newDate);
				formData.append('security', '<?php echo esc_js( wp_create_nonce('thessnest_dashboard_nonce') ); ?>');

				fetch(thessnestAjax.ajaxurl, {
					method: 'POST',
					body: formData
				})
				.then(response => response.json())
				.then(data => {
					alert(data.data.message);
					if (data.success) {
						location.reload();
					}
				});
			}
		});
	});
});
</script>

<?php get_footer(); ?>
