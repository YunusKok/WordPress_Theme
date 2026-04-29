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
							<a href="<?php echo esc_url( add_query_arg( 'tab', 'earnings', $dashboard_url ) ); ?>" style="display:block; padding:var(--space-2); border-radius:var(--radius-md); <?php echo ( 'earnings' === $active_tab ) ? 'background:var(--color-primary); color:white; font-weight:600;' : 'color:var(--color-text);'; ?>">
								<?php esc_html_e( 'Earnings & Payouts', 'thessnest' ); ?>
							</a>
						</li>
						<li style="margin-bottom:var(--space-2);">
							<a href="<?php echo esc_url( add_query_arg( 'tab', 'pricing_calendar', $dashboard_url ) ); ?>" style="display:block; padding:var(--space-2); border-radius:var(--radius-md); <?php echo ( 'pricing_calendar' === $active_tab ) ? 'background:var(--color-primary); color:white; font-weight:600;' : 'color:var(--color-text);'; ?>">
								<?php esc_html_e( 'Pricing Calendar', 'thessnest' ); ?>
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

					<!-- Roommate Finder -->
					<?php if ( thessnest_opt( 'enable_roommate_matching', true ) ) : ?>
					<li style="margin-bottom:var(--space-2);">
						<a href="<?php echo esc_url( add_query_arg( 'tab', 'roommates', $dashboard_url ) ); ?>" style="display:block; padding:var(--space-2); border-radius:var(--radius-md); <?php echo ( 'roommates' === $active_tab ) ? 'background:var(--color-primary); color:white; font-weight:600;' : 'color:var(--color-text);'; ?>">
							🔥 <?php esc_html_e( 'Roommate Finder', 'thessnest' ); ?>
						</a>
					</li>
					<?php endif; ?>
					
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

					<?php
					// ── Extended Student/Tenant Fields ──
					if ( in_array( 'tenant', (array) $user->roles, true ) ) :
						$u_nationality   = get_user_meta( $user->ID, '_thessnest_nationality', true );
						$u_sending_uni   = get_user_meta( $user->ID, '_thessnest_sending_university', true );
						$u_receiving_uni = get_user_meta( $user->ID, '_thessnest_receiving_university', true );
						$u_funding       = get_user_meta( $user->ID, '_thessnest_funding_source', true );
						$u_age           = get_user_meta( $user->ID, '_thessnest_student_age', true );
						$u_emergency     = get_user_meta( $user->ID, '_thessnest_emergency_contact', true );
						$u_passport      = function_exists( 'thessnest_get_passport_number' ) ? thessnest_get_passport_number( $user->ID ) : '';
					?>
					<div style="margin-top:var(--space-4); padding-top:var(--space-4); border-top:1px solid var(--color-border);">
						<h3 style="font-size:var(--font-size-base); margin-bottom:var(--space-4); color:var(--color-primary);"><?php esc_html_e( 'Student Information', 'thessnest' ); ?></h3>
						<div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-4); margin-bottom:var(--space-4);">
							<div>
								<label style="display:block; margin-bottom:4px; font-weight:600; font-size:var(--font-size-sm);"><?php esc_html_e( 'Nationality', 'thessnest' ); ?></label>
								<input type="text" name="thessnest_nationality" value="<?php echo esc_attr( $u_nationality ); ?>" placeholder="e.g. Turkish" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
							</div>
							<div>
								<label style="display:block; margin-bottom:4px; font-weight:600; font-size:var(--font-size-sm);"><?php esc_html_e( 'Age', 'thessnest' ); ?></label>
								<input type="number" name="thessnest_student_age" value="<?php echo esc_attr( $u_age ); ?>" min="16" max="99" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
							</div>
						</div>
						<div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-4); margin-bottom:var(--space-4);">
							<div>
								<label style="display:block; margin-bottom:4px; font-weight:600; font-size:var(--font-size-sm);"><?php esc_html_e( 'Sending University', 'thessnest' ); ?></label>
								<input type="text" name="thessnest_sending_university" value="<?php echo esc_attr( $u_sending_uni ); ?>" placeholder="Your home university" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
							</div>
							<div>
								<label style="display:block; margin-bottom:4px; font-weight:600; font-size:var(--font-size-sm);"><?php esc_html_e( 'Receiving University', 'thessnest' ); ?></label>
								<input type="text" name="thessnest_receiving_university" value="<?php echo esc_attr( $u_receiving_uni ); ?>" placeholder="University in host country" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
							</div>
						</div>
						<div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-4); margin-bottom:var(--space-4);">
							<div>
								<label style="display:block; margin-bottom:4px; font-weight:600; font-size:var(--font-size-sm);"><?php esc_html_e( 'Funding Source', 'thessnest' ); ?></label>
								<select name="thessnest_funding_source" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
									<option value=""><?php esc_html_e( '— Select —', 'thessnest' ); ?></option>
									<option value="erasmus+" <?php selected( $u_funding, 'erasmus+' ); ?>>Erasmus+</option>
									<option value="scholarship" <?php selected( $u_funding, 'scholarship' ); ?>><?php esc_html_e( 'Scholarship', 'thessnest' ); ?></option>
									<option value="self_funded" <?php selected( $u_funding, 'self_funded' ); ?>><?php esc_html_e( 'Self-Funded', 'thessnest' ); ?></option>
									<option value="other" <?php selected( $u_funding, 'other' ); ?>><?php esc_html_e( 'Other', 'thessnest' ); ?></option>
								</select>
							</div>
							<div>
								<label style="display:block; margin-bottom:4px; font-weight:600; font-size:var(--font-size-sm);"><?php esc_html_e( 'Emergency Contact', 'thessnest' ); ?></label>
								<input type="text" name="thessnest_emergency_contact" value="<?php echo esc_attr( $u_emergency ); ?>" placeholder="Name & phone number" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
							</div>
						</div>
						<div style="margin-bottom:var(--space-4);">
							<label style="display:block; margin-bottom:4px; font-weight:600; font-size:var(--font-size-sm);">
								<?php esc_html_e( 'Passport Number', 'thessnest' ); ?>
								<span style="font-weight:400; color:var(--color-text-muted); font-size:12px;">(<?php esc_html_e( 'Encrypted & required for visa documents', 'thessnest' ); ?>)</span>
							</label>
							<input type="text" name="thessnest_passport_number" value="<?php echo esc_attr( $u_passport ); ?>" placeholder="e.g. U12345678" style="width:100%; max-width:300px; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text); font-family:monospace;">
						</div>
					</div>
					<?php endif; ?>

					<?php
					// ── Extended Host/Landlord Fields ──
					if ( in_array( 'landlord', (array) $user->roles, true ) || in_array( 'administrator', (array) $user->roles, true ) ) :
						$u_tax_id  = get_user_meta( $user->ID, '_thessnest_tax_id', true );
						$u_id_card = get_user_meta( $user->ID, '_thessnest_id_card_number', true );
					?>
					<div style="margin-top:var(--space-4); padding-top:var(--space-4); border-top:1px solid var(--color-border);">
						<h3 style="font-size:var(--font-size-base); margin-bottom:var(--space-4); color:var(--color-primary);"><?php esc_html_e( 'Host Identification', 'thessnest' ); ?></h3>
						<div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-4); margin-bottom:var(--space-4);">
							<div>
								<label style="display:block; margin-bottom:4px; font-weight:600; font-size:var(--font-size-sm);"><?php esc_html_e( 'Tax ID (AFM)', 'thessnest' ); ?></label>
								<input type="text" name="thessnest_tax_id" value="<?php echo esc_attr( $u_tax_id ); ?>" placeholder="e.g. 123456789" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
							</div>
							<div>
								<label style="display:block; margin-bottom:4px; font-weight:600; font-size:var(--font-size-sm);"><?php esc_html_e( 'ID Card Number', 'thessnest' ); ?></label>
								<input type="text" name="thessnest_id_card_number" value="<?php echo esc_attr( $u_id_card ); ?>" placeholder="National ID number" style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);">
							</div>
						</div>
					</div>
					<?php endif; ?>

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
				   TAB: PRICING CALENDAR (LANDLORD ONLY)
				   ========================================================================== */
				elseif ( 'pricing_calendar' === $active_tab && $is_landlord ) :
				?>
					<h2 style="font-size:var(--font-size-xl); margin-bottom:var(--space-6); color:var(--color-primary);">
						<?php esc_html_e( 'Custom Period Pricing', 'thessnest' ); ?>
					</h2>
					<p style="color:var(--color-text-muted); margin-bottom:var(--space-6);">
						<?php esc_html_e( 'Select a property and define seasonal or custom date prices. These rates will override the base nightly rent.', 'thessnest' ); ?>
					</p>

					<div style="background:var(--color-surface); border:1px solid var(--color-border); border-radius:var(--radius-lg); padding:var(--space-6); margin-bottom:var(--space-8);">
						<form id="custom-pricing-form">
							<div style="margin-bottom:var(--space-4);">
								<label style="display:block; margin-bottom:var(--space-2); font-weight:600; font-size:var(--font-size-sm);"><?php esc_html_e('Select Property', 'thessnest'); ?></label>
								<select id="pricing_property_id" name="property_id" required style="width:100%; max-width:400px; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md);">
									<option value=""><?php esc_html_e('-- Select a Property --', 'thessnest'); ?></option>
									<?php
									$landlord_props = get_posts(array('post_type'=>'property', 'author'=>$user->ID, 'posts_per_page'=>-1, 'post_status'=>'publish'));
									foreach($landlord_props as $prop) {
										echo '<option value="' . esc_attr($prop->ID) . '">' . esc_html($prop->post_title) . '</option>';
									}
									?>
								</select>
							</div>

							<div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:var(--space-4); margin-bottom:var(--space-4); max-width:600px;">
								<div>
									<label style="display:block; margin-bottom:var(--space-2); font-weight:600; font-size:var(--font-size-sm);"><?php esc_html_e('Start Date', 'thessnest'); ?></label>
									<input type="date" id="pricing_start_date" name="start_date" required style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md);">
								</div>
								<div>
									<label style="display:block; margin-bottom:var(--space-2); font-weight:600; font-size:var(--font-size-sm);"><?php esc_html_e('End Date', 'thessnest'); ?></label>
									<input type="date" id="pricing_end_date" name="end_date" required style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md);">
								</div>
								<div>
									<label style="display:block; margin-bottom:var(--space-2); font-weight:600; font-size:var(--font-size-sm);"><?php esc_html_e('Daily Rate (€)', 'thessnest'); ?></label>
									<input type="number" step="0.01" id="pricing_rate" name="rate" required style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md);">
								</div>
							</div>
							
							<div id="pricing-response" style="margin-bottom:var(--space-4); display:none; padding:var(--space-2); border-radius:var(--radius-sm); font-size:14px;"></div>
							<button type="submit" id="btn-save-pricing" class="btn btn-primary"><?php esc_html_e('Add Custom Price Period', 'thessnest'); ?></button>
						</form>
					</div>

					<script>
					document.addEventListener('DOMContentLoaded', function() {
						const form = document.getElementById('custom-pricing-form');
						if(form) {
							form.addEventListener('submit', function(e) {
								e.preventDefault();
								const btn = document.getElementById('btn-save-pricing');
								const resp = document.getElementById('pricing-response');
								
								btn.disabled = true; btn.textContent = 'Saving...';
								
								const formData = new FormData(form);
								formData.append('action', 'thessnest_save_custom_pricing');
								formData.append('security', '<?php echo esc_js( wp_create_nonce("thessnest_dashboard_nonce") ); ?>');
								
								fetch('<?php echo esc_url(admin_url("admin-ajax.php")); ?>', {
									method: 'POST',
									body: formData
								}).then(r => r.json()).then(data => {
									resp.style.display = 'block';
									if(data.success) {
										resp.style.background = '#f0fdf4'; resp.style.color = '#166534';
										resp.textContent = data.data.message || 'Saved successfully!';
										form.reset();
									} else {
										resp.style.background = '#fef2f2'; resp.style.color = '#991b1b';
										resp.textContent = data.data.message || 'Error saving price.';
									}
									btn.disabled = false; btn.textContent = 'Add Custom Price Period';
								}).catch(() => {
									resp.style.display = 'block'; resp.textContent = 'Server Error.';
									btn.disabled = false; btn.textContent = 'Add Custom Price Period';
								});
							});
						}
					});
					</script>


				<!-- iCal SYNC SECTION -->
				<div style="margin-top:var(--space-8); padding-top:var(--space-8); border-top:2px solid var(--color-border);">
					<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-4);">
						<h2 style="font-size:var(--font-size-xl); margin:0; color:var(--color-primary);"><?php esc_html_e( 'iCal Sync', 'thessnest' ); ?></h2>
						<span style="background:linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); color:white; font-size:11px; font-weight:700; padding:4px 12px; border-radius:20px;"><?php esc_html_e( 'TWO-WAY', 'thessnest' ); ?></span>
					</div>
					<p style="color:var(--color-text-muted); margin-bottom:var(--space-6); font-size:var(--font-size-sm);"><?php esc_html_e( 'Synchronize your ThessNest calendar with Airbnb, Booking.com to prevent double bookings.', 'thessnest' ); ?></p>
					<div style="margin-bottom:var(--space-6);"><label style="display:block; margin-bottom:var(--space-2); font-weight:600;"><?php esc_html_e( 'Select Property', 'thessnest' ); ?></label>
						<select id="ical_property_id" style="width:100%; max-width:400px; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md);"><option value=""><?php esc_html_e('-- Choose --','thessnest'); ?></option><?php if(!empty($landlord_props)){foreach($landlord_props as $p){$fc=0;$fd=get_post_meta($p->ID,'_thessnest_ical_feeds',true);if(is_array($fd)){$fc=count($fd);}elseif(get_post_meta($p->ID,'_thessnest_ical_import_url',true)){$fc=1;}$bg=$fc>0?' ('.$fc.' feed'.($fc>1?'s':'').')':'';echo '<option value="'.esc_attr($p->ID).'" data-slug="'.esc_attr($p->post_name).'">'.esc_html($p->post_title.$bg).'</option>';}} ?></select>
					</div>
					<div id="ical-sync-panel" style="display:none;">
						<div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-6); margin-bottom:var(--space-6);">
							<div style="background:linear-gradient(135deg,#f0f9ff,#e0f2fe); border:1px solid #bae6fd; border-radius:var(--radius-lg); padding:var(--space-5);"><div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;"><span style="font-size:20px;">📤</span><h3 style="margin:0;color:#0369a1;"><?php esc_html_e('Export Calendar','thessnest'); ?></h3></div><p style="font-size:12px;color:#0c4a6e;margin-bottom:12px;"><?php esc_html_e('Copy this URL into Airbnb or Booking.com to push bookings outward.','thessnest'); ?></p><div style="display:flex;gap:8px;"><input type="text" id="ical-export-url" readonly style="flex:1;padding:6px;border:1px solid #7dd3fc;border-radius:4px;background:#fff;font-size:11px;color:#0369a1;font-family:monospace;"><button type="button" id="btn-copy-export" style="padding:6px 12px;background:#0284c7;color:#fff;border:none;border-radius:4px;font-size:12px;cursor:pointer;">Copy</button></div></div>
							<div style="background:linear-gradient(135deg,#fefce8,#fef9c3); border:1px solid #fde68a; border-radius:var(--radius-lg); padding:var(--space-5);"><div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;"><span style="font-size:20px;">📥</span><h3 style="margin:0;color:#92400e;"><?php esc_html_e('Import Feeds','thessnest'); ?></h3></div><p style="font-size:12px;color:#78350f;margin-bottom:12px;"><?php esc_html_e('Add external .ics URLs to block booked dates.','thessnest'); ?></p><button type="button" id="btn-open-import-modal" style="padding:8px 16px;background:#d97706;color:#fff;border:none;border-radius:4px;font-weight:600;cursor:pointer;"><?php esc_html_e('Manage Feeds','thessnest'); ?></button><span id="ical-feed-count" style="margin-left:8px;font-size:12px;color:#92400e;font-weight:600;"></span></div>
						</div>
						<div style="display:flex;align-items:center;gap:var(--space-4);padding:var(--space-4);background:var(--color-surface);border:1px solid var(--color-border);border-radius:var(--radius-lg);">
							<button type="button" id="btn-frontend-sync-now" class="btn btn-primary" style="padding:8px 20px;font-weight:600;">🔄 <?php esc_html_e('Sync Now','thessnest'); ?></button>
							<div style="flex:1;"><span style="font-size:var(--font-size-sm);color:var(--color-text-muted);"><?php esc_html_e('Last synced:','thessnest'); ?> <strong id="frontend-last-sync">&mdash;</strong></span><br><span style="font-size:11px;color:var(--color-text-muted);"><?php printf(esc_html__('Auto-sync every %d hours.','thessnest'),intval(function_exists('thessnest_get_ical_sync_hours')?thessnest_get_ical_sync_hours():4)); ?></span></div>
							<div id="frontend-sync-result" style="display:none;padding:6px 12px;border-radius:4px;font-size:12px;font-weight:600;"></div>
						</div>
					</div>
				</div>
				<div id="ical-import-modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999;background:rgba(0,0,0,.5);backdrop-filter:blur(4px);"><div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:var(--color-background);border-radius:16px;padding:32px;width:90%;max-width:520px;box-shadow:0 25px 50px rgba(0,0,0,.2);border:1px solid var(--color-border);"><div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;"><h3 style="margin:0;color:var(--color-primary);">📥 <?php esc_html_e('Import iCal','thessnest'); ?></h3><button type="button" id="btn-close-import-modal" style="background:none;border:none;font-size:24px;cursor:pointer;color:var(--color-text-muted);">&times;</button></div><div id="ical-modal-feeds-container"></div><button type="button" id="btn-modal-add-feed" class="btn btn-outline" style="margin:12px 0;padding:8px 16px;width:100%;">+ <?php esc_html_e('Add Feed','thessnest'); ?></button><hr style="border:0;border-top:1px solid var(--color-border);margin:16px 0;"><div style="display:flex;gap:12px;"><button type="button" id="btn-save-feeds" class="btn btn-primary" style="flex:1;padding:12px;font-weight:600;"><?php esc_html_e('Save Feeds','thessnest'); ?></button><button type="button" id="btn-cancel-modal" class="btn btn-outline" style="padding:12px 20px;"><?php esc_html_e('Cancel','thessnest'); ?></button></div><div id="modal-feed-response" style="display:none;margin-top:12px;padding:8px;border-radius:4px;font-size:13px;text-align:center;"></div></div></div>
				<?php $ical_jf=$ical_js=array();if(!empty($landlord_props)){foreach($landlord_props as $p){$fr=get_post_meta($p->ID,'_thessnest_ical_feeds',true);if(!is_array($fr)){$l=get_post_meta($p->ID,'_thessnest_ical_import_url',true);$fr=$l?array(array('name'=>'Airbnb','url'=>$l)):array();}$ical_jf[$p->ID]=$fr;$ls=get_post_meta($p->ID,'_thessnest_ical_last_sync',true);$ical_js[$p->ID]=$ls?date_i18n(get_option('date_format').' '.get_option('time_format'),$ls):'—';}} ?>
				<script>(function(){var A='<?php echo esc_url(admin_url("admin-ajax.php"));?>',N='<?php echo esc_js(wp_create_nonce("thessnest_dashboard_nonce"));?>',IN='<?php echo esc_js(wp_create_nonce("thessnest_ical_sync_nonce"));?>',S='<?php echo esc_url(home_url("/"));?>',pF=<?php echo wp_json_encode((object)$ical_jf);?>,pS=<?php echo wp_json_encode((object)$ical_js);?>;var sel=document.getElementById('ical_property_id'),pan=document.getElementById('ical-sync-panel'),exp=document.getElementById('ical-export-url'),fc=document.getElementById('ical-feed-count'),ls=document.getElementById('frontend-last-sync'),cP=0;if(!sel)return;sel.addEventListener('change',function(){cP=parseInt(this.value);if(!cP){pan.style.display='none';return;}pan.style.display='block';exp.value=S+'property/'+this.options[this.selectedIndex].dataset.slug+'/ical/';var f=pF[cP]||[];fc.textContent=f.length?f.length+' feed(s)':'No feeds';ls.textContent=pS[cP]||'\u2014';});document.getElementById('btn-copy-export').addEventListener('click',function(){exp.select();document.execCommand('copy');var b=this;b.textContent='Copied!';setTimeout(function(){b.textContent='Copy';},2e3);});var mo=document.getElementById('ical-import-modal'),mc=document.getElementById('ical-modal-feeds-container');function aR(n,u){var d=document.createElement('div');d.style.cssText='background:var(--color-surface);border:1px solid var(--color-border);border-radius:8px;padding:12px;margin-bottom:8px;position:relative;';d.innerHTML='<button type="button" class="rmf" style="position:absolute;top:6px;right:6px;background:#ef4444;color:#fff;border:none;width:22px;height:22px;border-radius:50%;cursor:pointer;font-size:14px;line-height:1;">&times;</button><label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Feed Name</label><input type="text" class="fn" value="'+(n||'').replace(/"/g,'&quot;')+'" placeholder="e.g. Airbnb" style="width:100%;padding:6px;border:1px solid var(--color-border);border-radius:4px;margin-bottom:8px;font-size:13px;"><label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Feed URL</label><input type="url" class="fu" value="'+(u||'').replace(/"/g,'&quot;')+'" placeholder="https://..." style="width:100%;padding:6px;border:1px solid var(--color-border);border-radius:4px;font-size:13px;">';mc.appendChild(d);}function oM(){mc.innerHTML='';var f=pF[cP]||[];if(!f.length)aR('','');else f.forEach(function(x){aR(x.name,x.url);});mo.style.display='block';}function cM(){mo.style.display='none';document.getElementById('modal-feed-response').style.display='none';}document.getElementById('btn-open-import-modal').addEventListener('click',oM);document.getElementById('btn-close-import-modal').addEventListener('click',cM);document.getElementById('btn-cancel-modal').addEventListener('click',cM);mo.addEventListener('click',function(e){if(e.target===mo)cM();});document.getElementById('btn-modal-add-feed').addEventListener('click',function(){aR('','');});mc.addEventListener('click',function(e){if(e.target.classList.contains('rmf'))e.target.parentElement.remove();});document.getElementById('btn-save-feeds').addEventListener('click',function(){var b=this,r=document.getElementById('modal-feed-response'),ns=mc.querySelectorAll('.fn'),us=mc.querySelectorAll('.fu'),fp=[];ns.forEach(function(el,i){var u=us[i].value.trim();if(u)fp.push({name:el.value.trim(),url:u});});b.disabled=true;b.textContent='Saving...';var fd=new FormData();fd.append('action','thessnest_save_ical_feeds_frontend');fd.append('security',N);fd.append('property_id',cP);fp.forEach(function(f,i){fd.append('feeds['+i+'][name]',f.name);fd.append('feeds['+i+'][url]',f.url);});fetch(A,{method:'POST',body:fd}).then(function(x){return x.json();}).then(function(d){r.style.display='block';if(d.success){r.style.background='#f0fdf4';r.style.color='#166534';r.textContent=d.data.message;pF[cP]=fp;fc.textContent=fp.length?fp.length+' feed(s)':'No feeds';setTimeout(cM,1200);}else{r.style.background='#fef2f2';r.style.color='#991b1b';r.textContent=d.data.message||'Error';}}).catch(function(){r.style.display='block';r.style.background='#fef2f2';r.textContent='Network error.';}).finally(function(){b.disabled=false;b.textContent='Save Feeds';});});document.getElementById('btn-frontend-sync-now').addEventListener('click',function(){if(!cP)return;var b=this,r=document.getElementById('frontend-sync-result');b.disabled=true;b.textContent='\u23f3 Syncing...';r.style.display='none';var fd=new FormData();fd.append('action','thessnest_ical_sync_now');fd.append('security',IN);fd.append('property_id',cP);fetch(A,{method:'POST',body:fd}).then(function(x){return x.json();}).then(function(d){r.style.display='inline-block';if(d.success){r.style.background='#f0fdf4';r.style.color='#166534';r.textContent=d.data.message;ls.textContent=d.data.last_sync;pS[cP]=d.data.last_sync;}else{r.style.background='#fef2f2';r.style.color='#991b1b';r.textContent=d.data.message||'Error';}}).catch(function(){r.style.display='inline-block';r.style.background='#fef2f2';r.textContent='Network error.';}).finally(function(){b.disabled=false;b.textContent='\ud83d\udd04 Sync Now';});});})();</script>


				<?php
				/* ==========================================================================
				   TAB: EARNINGS & PAYOUTS (LANDLORD ONLY)
				   ========================================================================== */
				elseif ( 'earnings' === $active_tab && $is_landlord ) :
					$payout_engine = null;
					$available_balance = 0;
					$lifetime_earnings = 0;
					$min_payout        = function_exists('thessnest_opt') ? floatval( thessnest_opt('min_payout_threshold', 100) ) : 100;

					if ( class_exists('ThessNest_Host_Payout_Engine') ) {
						$payout_engine = ThessNest_Host_Payout_Engine::get_instance();
						$available_balance = $payout_engine->get_host_balance( $user->ID );
						$lifetime_earnings = $payout_engine->get_lifetime_earnings( $user->ID );
					}
				?>
					<h2 style="font-size:var(--font-size-xl); margin-bottom:var(--space-6); color:var(--color-primary);">
						<?php esc_html_e( 'Earnings & Payouts', 'thessnest' ); ?>
					</h2>

					<!-- At a Glance Cards -->
					<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:var(--space-6); margin-bottom:var(--space-8);">
						<div style="background:var(--color-surface); border:1px solid var(--color-border); border-radius:var(--radius-lg); padding:var(--space-6); text-align:center;">
							<h4 style="margin:0 0 var(--space-2) 0; font-size:var(--font-size-sm); color:var(--color-text-muted); text-transform:uppercase; letter-spacing:1px;"><?php esc_html_e('Available Balance', 'thessnest'); ?></h4>
							<div style="font-size:32px; font-weight:700; color:#38a169;">
								<?php echo esc_html( thessnest_format_price($available_balance) ); ?>
							</div>
						</div>
						<div style="background:var(--color-surface); border:1px solid var(--color-border); border-radius:var(--radius-lg); padding:var(--space-6); text-align:center;">
							<h4 style="margin:0 0 var(--space-2) 0; font-size:var(--font-size-sm); color:var(--color-text-muted); text-transform:uppercase; letter-spacing:1px;"><?php esc_html_e('Lifetime Earnings', 'thessnest'); ?></h4>
							<div style="font-size:32px; font-weight:700; color:var(--color-text);">
								<?php echo esc_html( thessnest_format_price($lifetime_earnings) ); ?>
							</div>
						</div>
					</div>

					<!-- Request Payout Form -->
					<div style="background:var(--color-surface); border:1px solid var(--color-border); border-radius:var(--radius-lg); padding:var(--space-6); margin-bottom:var(--space-8);">
						<h3 style="font-size:var(--font-size-lg); border-bottom:1px solid var(--color-border); padding-bottom:var(--space-2); margin-top:0; margin-bottom:var(--space-4);">
							<?php esc_html_e( 'Request Payout', 'thessnest' ); ?>
						</h3>
						<?php if ( $available_balance >= $min_payout ) : ?>
							<p style="color:var(--color-text-muted); margin-bottom:var(--space-4); font-size:var(--font-size-sm);">
								<?php esc_html_e('You can request a payout for your available balance. Please provide your bank or PayPal details below.', 'thessnest'); ?>
							</p>
							<form id="payout-request-form" style="max-width:500px;">
								<input type="hidden" name="action" value="thessnest_request_payout">
								<?php wp_nonce_field( 'thessnest_payout_nonce', 'payout_security' ); ?>
								
								<div class="form-group" style="margin-bottom:var(--space-4);">
									<label style="display:block; margin-bottom:var(--space-2); font-weight:600; font-size:var(--font-size-sm);"><?php esc_html_e('Payout Method', 'thessnest'); ?></label>
									<select name="payout_method" required style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md);">
										<option value="bank_transfer"><?php esc_html_e('Bank Transfer (IBAN)', 'thessnest'); ?></option>
										<option value="paypal"><?php esc_html_e('PayPal', 'thessnest'); ?></option>
									</select>
								</div>

								<div class="form-group" style="margin-bottom:var(--space-4);">
									<label style="display:block; margin-bottom:var(--space-2); font-weight:600; font-size:var(--font-size-sm);"><?php esc_html_e('Payment Details (IBAN or PayPal Email)', 'thessnest'); ?></label>
									<input type="text" name="payout_details" required style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md);">
								</div>

								<div class="form-group" style="margin-bottom:var(--space-6);">
									<label style="display:block; margin-bottom:var(--space-2); font-weight:600; font-size:var(--font-size-sm);"><?php esc_html_e('Amount to Withdraw', 'thessnest'); ?></label>
									<input type="number" name="payout_amount" max="<?php echo esc_attr($available_balance); ?>" step="0.01" value="<?php echo esc_attr($available_balance); ?>" required style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md);">
								</div>

								<div id="payout-response" style="margin-bottom:var(--space-4); display:none; padding:var(--space-2); border-radius:var(--radius-sm); font-size:14px;"></div>
								
								<button type="submit" id="btn-request-payout" class="btn btn-primary">
									<?php esc_html_e('Submit Request', 'thessnest'); ?>
								</button>
							</form>

							<script>
							document.addEventListener('DOMContentLoaded', function() {
								const form = document.getElementById('payout-request-form');
								if (form) {
									form.addEventListener('submit', function(e) {
										e.preventDefault();
										const btn = document.getElementById('btn-request-payout');
										const responseDiv = document.getElementById('payout-response');
										const formData = new FormData(form);
										
										btn.disabled = true;
										btn.textContent = 'Processing...';
										
										fetch('<?php echo esc_url(admin_url("admin-ajax.php")); ?>', {
											method: 'POST',
											body: formData
										}).then(res => res.json()).then(data => {
											responseDiv.style.display = 'block';
											if (data.success) {
												responseDiv.style.background = '#f0fdf4';
												responseDiv.style.color = '#166534';
												responseDiv.textContent = data.data.message;
												setTimeout(() => location.reload(), 2000);
											} else {
												responseDiv.style.background = '#fef2f2';
												responseDiv.style.color = '#991b1b';
												responseDiv.textContent = data.data.message || 'Error processing request.';
												btn.disabled = false;
												btn.textContent = 'Submit Request';
											}
										}).catch(() => {
											responseDiv.style.display = 'block';
											responseDiv.textContent = 'Server error. Please try again.';
											btn.disabled = false;
											btn.textContent = 'Submit Request';
										});
									});
								}
							});
							</script>

						<?php else : ?>
							<div style="background:#fffbeb; border:1px solid #fde68a; color:#b45309; padding:var(--space-4); border-radius:var(--radius-md); font-weight:600; display:flex; gap:var(--space-2); align-items:center;">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
								<?php printf( 
									esc_html__('You must reach a minimum balance of %s to request a payout.', 'thessnest'), 
									thessnest_format_price($min_payout) 
								); ?>
							</div>
						<?php endif; ?>
					</div>

					<!-- Payout History -->
					<h3 style="font-size:var(--font-size-lg); border-bottom:1px solid var(--color-border); padding-bottom:var(--space-2); margin-bottom:var(--space-4);">
						<?php esc_html_e( 'Payout History', 'thessnest' ); ?>
					</h3>
					<?php
					$payout_query = new WP_Query( array(
						'post_type'      => 'thessnest_payout',
						'author'         => $user->ID,
						'posts_per_page' => 20,
						'post_status'    => array('publish', 'pending', 'draft')
					) );

					if ( $payout_query->have_posts() ) : ?>
						<div style="overflow-x:auto;">
							<table style="width:100%; border-collapse:collapse; text-align:left; font-size:var(--font-size-sm);">
								<thead>
									<tr style="border-bottom:2px solid var(--color-border);">
										<th style="padding:var(--space-3) 0; color:var(--color-text-muted);"><?php esc_html_e('Date', 'thessnest'); ?></th>
										<th style="padding:var(--space-3) 0; color:var(--color-text-muted);"><?php esc_html_e('Amount', 'thessnest'); ?></th>
										<th style="padding:var(--space-3) 0; color:var(--color-text-muted);"><?php esc_html_e('Method', 'thessnest'); ?></th>
										<th style="padding:var(--space-3) 0; color:var(--color-text-muted);"><?php esc_html_e('Status', 'thessnest'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php while ( $payout_query->have_posts() ) : $payout_query->the_post(); 
										$p_amount = get_post_meta( get_the_ID(), '_payout_amount', true );
										$p_method = get_post_meta( get_the_ID(), '_payout_method', true );
										$p_status = get_post_meta( get_the_ID(), '_payout_status', true );
										
										$badge_bg = '#e2e8f0'; $badge_col = '#4a5568';
										if ( 'completed' === $p_status ) { $badge_bg = '#c6f6d5'; $badge_col = '#22543d'; }
										elseif ( 'pending' === $p_status ) { $badge_bg = '#feebc8'; $badge_col = '#7b341e'; }
										elseif ( 'rejected' === $p_status ) { $badge_bg = '#fed7d7'; $badge_col = '#742a2a'; }
									?>
									<tr style="border-bottom:1px solid var(--color-border);">
										<td style="padding:var(--space-3) 0;"><?php echo get_the_date(); ?></td>
										<td style="padding:var(--space-3) 0; font-weight:600;"><?php echo esc_html(thessnest_format_price($p_amount)); ?></td>
										<td style="padding:var(--space-3) 0; text-transform:capitalize;"><?php echo esc_html(str_replace('_', ' ', $p_method)); ?></td>
										<td style="padding:var(--space-3) 0;">
											<span style="background:<?php echo esc_attr($badge_bg); ?>; color:<?php echo esc_attr($badge_col); ?>; padding:2px 8px; border-radius:12px; font-size:12px; font-weight:600; text-transform:uppercase;">
												<?php echo esc_html($p_status); ?>
											</span>
										</td>
									</tr>
									<?php endwhile; ?>
								</tbody>
							</table>
						</div>
					<?php else : ?>
						<p style="color:var(--color-text-muted);"><?php esc_html_e('No payout requests found.', 'thessnest'); ?></p>
					<?php endif; wp_reset_postdata(); ?>

				<?php
				/* ==========================================================================
				   TAB: ROOMMATE FINDER
				   ========================================================================== */
				elseif ( 'roommates' === $active_tab && thessnest_opt( 'enable_roommate_matching', true ) ) : 
					$r_active = get_user_meta( $user->ID, '_thessnest_roommate_active', true );
					$r_prefs  = get_user_meta( $user->ID, '_thessnest_roommate_prefs', true );
					if ( ! is_array( $r_prefs ) ) {
						$r_prefs = array();
					}
					// Default values for form:
					$p_bmin = isset($r_prefs['budget_min']) ? $r_prefs['budget_min'] : '';
					$p_bmax = isset($r_prefs['budget_max']) ? $r_prefs['budget_max'] : '';
					$p_neigh = isset($r_prefs['neighborhood']) ? $r_prefs['neighborhood'] : '';
					$p_move = isset($r_prefs['move_in_date']) ? $r_prefs['move_in_date'] : '';
					$p_dur = isset($r_prefs['stay_duration']) ? $r_prefs['stay_duration'] : '';
					$p_age = isset($r_prefs['age_range']) ? $r_prefs['age_range'] : '';
					$p_gen = isset($r_prefs['gender_pref']) ? $r_prefs['gender_pref'] : 'any';
					$p_smk = isset($r_prefs['smoker']) ? $r_prefs['smoker'] : 'no';
					$p_pet = isset($r_prefs['pets']) ? $r_prefs['pets'] : 'no';
					$p_stu = !empty($r_prefs['student']) ? 'checked' : '';
					$p_qui = !empty($r_prefs['quiet_hours']) ? 'checked' : '';
					$p_lan = isset($r_prefs['languages']) ? $r_prefs['languages'] : '';
					$p_bio = isset($r_prefs['bio']) ? $r_prefs['bio'] : '';
				?>
					<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-6);">
						<h2 style="font-size:var(--font-size-xl); margin:0; color:var(--color-primary);">
							🔥 <?php esc_html_e( 'Roommate Finder', 'thessnest' ); ?>
						</h2>
						<div class="toggle-switch-container" style="display:flex; align-items:center; gap:var(--space-2);">
							<span style="font-size:var(--font-size-sm); color:var(--color-text-muted); font-weight:600;"><?php esc_html_e('Search Status:', 'thessnest'); ?></span>
							<label class="thessnest-toggle" style="position:relative; display:inline-block; width:40px; height:20px;">
								<input type="checkbox" id="roommate-status-toggle" <?php checked($r_active, '1'); ?> style="opacity:0; width:0; height:0;">
								<span class="thessnest-slider" style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:#ccc; border-radius:34px; transition:.4s;"></span>
							</label>
							<span id="roommate-status-label" style="font-size:var(--font-size-sm); font-weight:bold; color: <?php echo $r_active ? '#38a169' : '#718096'; ?>;">
								<?php echo $r_active ? esc_html__('Active', 'thessnest') : esc_html__('Inactive', 'thessnest'); ?>
							</span>
						</div>
					</div>
					
					<style>
						.thessnest-toggle input:checked + .thessnest-slider { background-color: #38a169; }
						.thessnest-slider:before { position:absolute; content:""; height:16px; width:16px; left:2px; bottom:2px; background-color:white; border-radius:50%; transition:.4s; }
						.thessnest-toggle input:checked + .thessnest-slider:before { transform: translateX(20px); }
					</style>

					<!-- Roommate Matches Section -->
					<div style="margin-bottom:var(--space-8); padding-bottom:var(--space-8); border-bottom:1px dashed var(--color-border);">
						<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-4);">
							<h3 style="margin:0; font-size:var(--font-size-lg);"><?php esc_html_e('Top Matches', 'thessnest'); ?></h3>
							<button id="btn-roommate-refresh" class="btn btn-primary" style="padding:var(--space-2) var(--space-4);">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle; margin-right:4px;"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
								<?php esc_html_e('Find Matches', 'thessnest'); ?>
							</button>
						</div>

						<div id="roommate-matches-container" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:var(--space-4);">
							<!-- AJAX Results injected here -->
							<div style="grid-column:1/-1; text-align:center; padding:var(--space-8); color:var(--color-text-muted); background:var(--color-surface); border-radius:var(--radius-lg); border:1px solid var(--color-border);">
								<?php esc_html_e('Click "Find Matches" to see who shares your lifestyle preferences.', 'thessnest'); ?>
							</div>
						</div>
					</div>

					<!-- My Match Profile Form -->
					<h3 style="font-size:var(--font-size-lg); margin-bottom:var(--space-4);"><?php esc_html_e('My Match Preferences', 'thessnest'); ?></h3>
					<form id="roommate-profile-form" style="background:var(--color-surface); padding:var(--space-6); border-radius:var(--radius-lg); border:1px solid var(--color-border);">
						<input type="hidden" name="action" value="thessnest_save_roommate_profile">
						<?php wp_nonce_field( 'thessnest_dashboard_nonce', 'security' ); ?>

						<div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-4); margin-bottom:var(--space-4);">
							<div>
								<label style="display:block; font-size:13px; font-weight:600; margin-bottom:4px;"><?php esc_html_e('Min Budget (€)', 'thessnest'); ?></label>
								<input type="number" name="budget_min" value="<?php echo esc_attr($p_bmin); ?>" placeholder="0" style="width:100%; padding:var(--space-2); border-radius:var(--radius-sm); border:1px solid var(--color-border);">
							</div>
							<div>
								<label style="display:block; font-size:13px; font-weight:600; margin-bottom:4px;"><?php esc_html_e('Max Budget (€)', 'thessnest'); ?></label>
								<input type="number" name="budget_max" value="<?php echo esc_attr($p_bmax); ?>" placeholder="500" style="width:100%; padding:var(--space-2); border-radius:var(--radius-sm); border:1px solid var(--color-border);">
							</div>
						</div>

						<div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-4); margin-bottom:var(--space-4);">
							<div>
								<label style="display:block; font-size:13px; font-weight:600; margin-bottom:4px;"><?php esc_html_e('Preferred Neighborhood', 'thessnest'); ?></label>
								<input type="text" name="neighborhood" value="<?php echo esc_attr($p_neigh); ?>" placeholder="e.g. Ano Poli" style="width:100%; padding:var(--space-2); border-radius:var(--radius-sm); border:1px solid var(--color-border);">
							</div>
							<div>
								<label style="display:block; font-size:13px; font-weight:600; margin-bottom:4px;"><?php esc_html_e('Move-in Date', 'thessnest'); ?></label>
								<input type="date" name="move_in_date" value="<?php echo esc_attr($p_move); ?>" style="width:100%; padding:var(--space-2); border-radius:var(--radius-sm); border:1px solid var(--color-border);">
							</div>
						</div>

						<div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:var(--space-4); margin-bottom:var(--space-4);">
							<div>
								<label style="display:block; font-size:13px; font-weight:600; margin-bottom:4px;"><?php esc_html_e('Stay Duration', 'thessnest'); ?></label>
								<select name="stay_duration" style="width:100%; padding:var(--space-2); border-radius:var(--radius-sm); border:1px solid var(--color-border);">
									<option value="1-3m" <?php selected($p_dur, '1-3m'); ?>>1-3 Months</option>
									<option value="3-6m" <?php selected($p_dur, '3-6m'); ?>>3-6 Months</option>
									<option value="6m+" <?php selected($p_dur, '6m+'); ?>>6+ Months</option>
								</select>
							</div>
							<div>
								<label style="display:block; font-size:13px; font-weight:600; margin-bottom:4px;"><?php esc_html_e('Age Range', 'thessnest'); ?></label>
								<select name="age_range" style="width:100%; padding:var(--space-2); border-radius:var(--radius-sm); border:1px solid var(--color-border);">
									<option value="18-25" <?php selected($p_age, '18-25'); ?>>18 - 25</option>
									<option value="25-35" <?php selected($p_age, '25-35'); ?>>25 - 35</option>
									<option value="35+" <?php selected($p_age, '35+'); ?>>35+</option>
								</select>
							</div>
							<div>
								<label style="display:block; font-size:13px; font-weight:600; margin-bottom:4px;"><?php esc_html_e('Preferred Gender', 'thessnest'); ?></label>
								<select name="gender_pref" style="width:100%; padding:var(--space-2); border-radius:var(--radius-sm); border:1px solid var(--color-border);">
									<option value="any" <?php selected($p_gen, 'any'); ?>>Any</option>
									<option value="male" <?php selected($p_gen, 'male'); ?>>Male</option>
									<option value="female" <?php selected($p_gen, 'female'); ?>>Female</option>
								</select>
							</div>
						</div>

						<div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-4); margin-bottom:var(--space-4);">
							<div>
								<label style="display:block; font-size:13px; font-weight:600; margin-bottom:4px;"><?php esc_html_e('Are you a Smoker?', 'thessnest'); ?></label>
								<select name="smoker" style="width:100%; padding:var(--space-2); border-radius:var(--radius-sm); border:1px solid var(--color-border);">
									<option value="no" <?php selected($p_smk, 'no'); ?>>No</option>
									<option value="yes" <?php selected($p_smk, 'yes'); ?>>Yes</option>
								</select>
							</div>
							<div>
								<label style="display:block; font-size:13px; font-weight:600; margin-bottom:4px;"><?php esc_html_e('Do you have Pets?', 'thessnest'); ?></label>
								<select name="pets" style="width:100%; padding:var(--space-2); border-radius:var(--radius-sm); border:1px solid var(--color-border);">
									<option value="no" <?php selected($p_pet, 'no'); ?>>No</option>
									<option value="yes" <?php selected($p_pet, 'yes'); ?>>Yes</option>
								</select>
							</div>
						</div>

						<div style="margin-bottom:var(--space-4); display:flex; gap:var(--space-4);">
							<label style="display:flex; align-items:center; gap:8px; font-size:14px;">
								<input type="checkbox" name="student" value="1" <?php echo $p_stu; ?>>
								<?php esc_html_e('I am a student', 'thessnest'); ?>
							</label>
							<label style="display:flex; align-items:center; gap:8px; font-size:14px;">
								<input type="checkbox" name="quiet_hours" value="1" <?php echo $p_qui; ?>>
								<?php esc_html_e('I need quiet hours (Strict)', 'thessnest'); ?>
							</label>
						</div>

						<div style="margin-bottom:var(--space-4);">
							<label style="display:block; font-size:13px; font-weight:600; margin-bottom:4px;"><?php esc_html_e('Languages Spoken', 'thessnest'); ?></label>
							<input type="text" name="languages" value="<?php echo esc_attr($p_lan); ?>" placeholder="e.g. English, Greek, Spanish" style="width:100%; padding:var(--space-2); border-radius:var(--radius-sm); border:1px solid var(--color-border);">
						</div>

						<div style="margin-bottom:var(--space-4);">
							<label style="display:block; font-size:13px; font-weight:600; margin-bottom:4px;"><?php esc_html_e('Short Bio', 'thessnest'); ?></label>
							<textarea name="bio" rows="3" placeholder="<?php esc_attr_e('Describe yourself...', 'thessnest'); ?>" style="width:100%; padding:var(--space-2); border-radius:var(--radius-sm); border:1px solid var(--color-border); resize:vertical;"><?php echo esc_textarea($p_bio); ?></textarea>
						</div>

						<div id="roommate-form-response" style="margin-bottom:var(--space-4); display:none; padding:var(--space-2); border-radius:var(--radius-sm); font-size:14px;"></div>

						<button type="submit" id="btn-save-roommate" class="btn btn-primary">
							<?php esc_html_e('Save Match Profile', 'thessnest'); ?>
						</button>
					</form>

					<script>
					document.addEventListener('DOMContentLoaded', function() {
						
						// 1. Toggle Active Search Status
						const toggle = document.getElementById('roommate-status-toggle');
						const label = document.getElementById('roommate-status-label');
						if (toggle) {
							toggle.addEventListener('change', function() {
								const formData = new FormData();
								formData.append('action', 'thessnest_toggle_roommate_search');
								formData.append('security', '<?php echo esc_js( wp_create_nonce("thessnest_dashboard_nonce") ); ?>');
								
								fetch(thessnestAjax.ajaxurl, { method: 'POST', body: formData })
								.then(r => r.json())
								.then(data => {
									if (data.success) {
										if (data.data.active) {
											label.textContent = 'Active';
											label.style.color = '#38a169';
										} else {
											label.textContent = 'Inactive';
											label.style.color = '#718096';
										}
									}
								});
							});
						}

						// 2. Save Preferences Form
						const form = document.getElementById('roommate-profile-form');
						if (form) {
							form.addEventListener('submit', function(e) {
								e.preventDefault();
								const btn = document.getElementById('btn-save-roommate');
								const resp = document.getElementById('roommate-form-response');
								btn.disabled = true; btn.textContent = 'Saving...';
								
								fetch(thessnestAjax.ajaxurl, { method: 'POST', body: new FormData(form) })
								.then(r => r.json())
								.then(data => {
									resp.style.display = 'block';
									resp.textContent = data.data.message || 'Saved successfully!';
									resp.style.background = data.success ? '#f0fdf4' : '#fef2f2';
									resp.style.color = data.success ? '#166534' : '#991b1b';
									btn.disabled = false; btn.textContent = 'Save Match Profile';

									// Turn toggle to true if it isn't
									if(data.success && toggle && !toggle.checked) {
										toggle.checked = true;
										label.textContent = 'Active'; label.style.color = '#38a169';
									}
								});
							});
						}

						// 3. Find Matches AI Fetcher
						const btnMatch = document.getElementById('btn-roommate-refresh');
						const matchCont = document.getElementById('roommate-matches-container');
						if (btnMatch) {
							btnMatch.addEventListener('click', function() {
								btnMatch.disabled = true;
								btnMatch.innerHTML = '<svg class="thessnest-spinner" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite; margin-right:4px;"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg> Loading...';
								matchCont.style.opacity = '0.5';
								
								const data = new FormData();
								data.append('action', 'thessnest_get_roommate_matches');
								data.append('security', '<?php echo esc_js( wp_create_nonce("thessnest_dashboard_nonce") ); ?>');
								
								fetch(thessnestAjax.ajaxurl, { method: 'POST', body: data })
								.then(r => r.json())
								.then(res => {
									matchCont.style.opacity = '1';
									btnMatch.disabled = false;
									btnMatch.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle; margin-right:4px;"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg> Find Matches';
									
									matchCont.innerHTML = '';
									if (!res.success) {
										matchCont.innerHTML = `<div style="grid-column:1/-1; padding:20px; text-align:center; color:#e53e3e; background:#fff5f5; border-radius:8px;">${res.data.message}</div>`;
										return;
									}

									if (res.data.matches.length === 0) {
										matchCont.innerHTML = `<div style="grid-column:1/-1; padding:20px; text-align:center; color:var(--color-text-muted);">No exact matches found yet. Try broadening your criteria!</div>`;
										return;
									}

									res.data.matches.forEach(m => {
										// Color mapping for score
										let scoreCol = '#38a169'; // Green > 70
										if(m.match_score < 40) scoreCol = '#e53e3e'; // Red
										else if (m.match_score < 70) scoreCol = '#d97706'; // Orange

										const html = `
											<div style="background:var(--color-surface); border:1px solid var(--color-border); border-radius:var(--radius-lg); padding:var(--space-4); display:flex; flex-direction:column; gap:var(--space-3);">
												<div style="display:flex; justify-content:space-between; align-items:flex-start;">
													<div style="display:flex; gap:var(--space-3); align-items:center;">
														<img src="${m.avatar}" style="width:50px; height:50px; border-radius:50%; object-fit:cover;">
														<div>
															<h4 style="margin:0; font-size:16px;">${m.name}</h4>
															${m.student ? '<span style="font-size:11px; background:#ebf8ff; color:#3182ce; padding:2px 6px; border-radius:4px;">🎓 Student</span>' : ''}
														</div>
													</div>
													<div style="text-align:center;">
														<div style="font-size:18px; font-weight:700; color:${scoreCol};">${m.match_score}%</div>
														<div style="font-size:10px; color:var(--color-text-muted); text-transform:uppercase;">Match</div>
													</div>
												</div>
												<div style="font-size:13px; color:var(--color-text-muted); display:flex; flex-direction:column; gap:4px; margin-top:var(--space-2);">
													<span><strong>📍 Prefers:</strong> ${m.neighborhood || 'Any'}</span>
													<span><strong>💰 Budget:</strong> ${m.budget_range}</span>
													<span><strong>📅 Move in:</strong> ${m.move_in || 'Flexible'} (${m.duration})</span>
												</div>
												<p style="font-size:13px; margin:0; padding-top:var(--space-3); border-top:1px solid var(--color-border);">
													${m.bio || '<i>No bio provided.</i>'}
												</p>
												<button class="btn btn-outline" style="width:100%; margin-top:auto;" onclick="alert('In a real app, this would open the message composer to say Hi!')">Send Message</button>
											</div>
										`;
										matchCont.insertAdjacentHTML('beforeend', html);
									});
								});
							});
						}

					});
					</script>

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
