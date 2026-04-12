<?php
/**
 * ThessNest — Accommodation Proof Endpoint
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

add_action( 'template_redirect', 'thessnest_handle_proof_download' );

function thessnest_handle_proof_download() {
	if ( isset( $_GET['download_proof'] ) && is_user_logged_in() ) {
		$booking_id = intval( $_GET['download_proof'] );
		$booking = get_post( $booking_id );
		
		if ( ! $booking || $booking->post_type !== 'thessnest_booking' ) {
			wp_die( 'Invalid booking.' );
		}
		
		// Ensure only the tenant or landlord or admin can download
		$tenant_id = $booking->post_author;
		$landlord_id = get_post_meta( $booking_id, '_booking_landlord_id', true );
		$current_user_id = get_current_user_id();
		
		if ( $current_user_id != $tenant_id && $current_user_id != $landlord_id && ! current_user_can('administrator') ) {
			wp_die( 'Unauthorized.' );
		}
		
		// Only approved bookings (confirmed)
		if ( get_post_meta( $booking_id, '_booking_status', true ) !== 'confirmed' ) {
			wp_die( 'Booking is not confirmed yet.' );
		}
		
		$property_id = get_post_meta( $booking_id, '_booking_property_id', true );
		$checkin     = get_post_meta( $booking_id, '_booking_checkin', true );
		$checkout    = get_post_meta( $booking_id, '_booking_checkout', true );
		$price       = get_post_meta( $booking_id, '_booking_total_price', true );
		$guests      = get_post_meta( $booking_id, '_booking_guests', true );
		
		// Extra data
		$tenant      = get_userdata( $tenant_id );
		$landlord    = get_userdata( $landlord_id );
		$t_passport  = function_exists('thessnest_get_passport_number') ? thessnest_get_passport_number($tenant_id) : 'N/A';
		$t_nat       = get_user_meta( $tenant_id, '_thessnest_nationality', true ) ?: 'N/A';
		$t_uni       = get_user_meta( $tenant_id, '_thessnest_receiving_university', true ) ?: 'N/A';
		$h_tax_id    = get_user_meta( $landlord_id, '_thessnest_tax_id', true ) ?: 'N/A';
		$p_address   = get_post_meta( $property_id, '_thessnest_location_address', true ) ?: esc_html( thessnest_get_first_term( 'neighborhood', $property_id ) );

		// Logo
		$site_logo = '';
		if ( class_exists( 'Redux' ) ) {
			global $thessnest_opt;
			if ( ! empty( $thessnest_opt['opt-media-logo']['url'] ) ) {
				$site_logo = $thessnest_opt['opt-media-logo']['url'];
			}
		}
		if ( empty( $site_logo ) ) {
			$site_logo = get_theme_file_uri( 'assets/images/logo-dark.svg' );
		}

		// Output the HTML
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta charset="<?php bloginfo( 'charset' ); ?>">
			<title><?php esc_html_e( 'Official Proof of Accommodation', 'thessnest' ); ?></title>
			<style>
				body { font-family: 'Times New Roman', Times, serif; padding: 40px; color: #111; max-width: 800px; margin: 0 auto; background: #fff; line-height: 1.5; font-size: 14pt; }
				.header { border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 30px; text-align: center; }
				.header img { max-height: 60px; margin-bottom: 10px; }
				.header h1 { color: #000; margin: 0; font-size: 20pt; text-transform: uppercase; }
				.header h2 { margin: 5px 0 0 0; font-size: 14pt; font-weight: normal; }
				.doc-meta { display: flex; justify-content: space-between; margin-bottom: 30px; font-weight: bold; }
				.section { margin-bottom: 30px; }
				.section h3 { font-size: 14pt; border-bottom: 1px solid #000; padding-bottom: 5px; color: #000; text-transform: uppercase; }
				table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
				table, th, td { border: 1px solid #000; }
				th, td { padding: 8px 12px; text-align: left; }
				th { background: #f0f0f0; width: 35%; font-weight: bold; }
				.summary { border: 1px solid #000; padding: 15px; background: #fafafa; margin-bottom: 30px; }
				.verification { margin-top: 50px; text-align: center; border: 2px solid #000; padding: 20px; border-radius: 8px; position: relative; }
				.verification h4 { margin: 0 0 10px 0; color: #0056b3; text-transform: uppercase; }
				.verification p { margin: 0 0 5px 0; font-size: 12pt; }
				.stamp { position: absolute; right: 20px; top: 10px; opacity: 0.2; width: 100px; height: 100px; border: 4px solid #0056b3; border-radius: 50%; display: flex; align-items: center; justify-content: center; transform: rotate(-15deg); color: #0056b3; font-weight: bold; font-size: 16pt; text-align: center; line-height: 1.2; }
				.footer { margin-top: 50px; text-align: center; color: #666; font-size: 10pt; border-top: 1px solid #ccc; padding-top: 10px; }
				@media print { body { padding: 0; } .print-btn { display: none; } }
				.print-btn { padding: 10px 20px; background: #0056b3; color: white; border: none; border-radius: 4px; cursor: pointer; margin-top: 20px; font-size: 14pt; display: block; margin-left: auto; margin-right: auto; }
			</style>
		</head>
		<body>
			<button class="print-btn" onclick="window.print()"><?php esc_html_e( 'Print / Save as PDF', 'thessnest' ); ?></button>
			
			<div class="header">
				<?php if ( $site_logo ) : ?>
					<img src="<?php echo esc_url( $site_logo ); ?>" alt="Logo">
				<?php endif; ?>
				<h1><?php esc_html_e( 'Official Proof of Accommodation', 'thessnest' ); ?></h1>
				<h2><?php esc_html_e( 'For Visa Application Purposes', 'thessnest' ); ?></h2>
			</div>

			<div class="doc-meta">
				<div><?php esc_html_e( 'Reference No:', 'thessnest' ); ?> THN-<?php echo esc_html( date('Y') ); ?>-<?php echo str_pad( $booking_id, 6, '0', STR_PAD_LEFT ); ?></div>
				<div><?php esc_html_e( 'Date of Issue:', 'thessnest' ); ?> <?php echo date_i18n( 'F j, Y' ); ?></div>
			</div>
			
			<div class="section">
				<h3><?php esc_html_e( '1. Tenant Information', 'thessnest' ); ?></h3>
				<table>
					<tr>
						<th><?php esc_html_e( 'Full Name', 'thessnest' ); ?></th>
						<td><?php echo esc_html( $tenant ? $tenant->display_name : 'N/A' ); ?></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Passport Number', 'thessnest' ); ?></th>
						<td><strong><?php echo esc_html( $t_passport ); ?></strong></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Nationality', 'thessnest' ); ?></th>
						<td><?php echo esc_html( $t_nat ); ?></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Receiving Institution', 'thessnest' ); ?></th>
						<td><?php echo esc_html( $t_uni ); ?></td>
					</tr>
				</table>
			</div>
			
			<div class="section">
				<h3><?php esc_html_e( '2. Accommodation Details', 'thessnest' ); ?></h3>
				<table>
					<tr>
						<th><?php esc_html_e( 'Property Name', 'thessnest' ); ?></th>
						<td><?php echo esc_html( get_the_title( $property_id ) ); ?></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Full Address', 'thessnest' ); ?></th>
						<td><?php echo esc_html( $p_address ); ?></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'GPS Coordinates', 'thessnest' ); ?></th>
						<td><?php echo esc_html( get_post_meta($property_id, '_thessnest_latitude', true) ); ?>, <?php echo esc_html( get_post_meta($property_id, '_thessnest_longitude', true) ); ?></td>
					</tr>
				</table>
			</div>

			<div class="section">
				<h3><?php esc_html_e( '3. Lease Duration', 'thessnest' ); ?></h3>
				<table>
					<tr>
						<th><?php esc_html_e( 'Check-In Date', 'thessnest' ); ?></th>
						<td><strong><?php echo date_i18n( 'F j, Y', strtotime($checkin) ); ?></strong></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Check-Out Date', 'thessnest' ); ?></th>
						<td><strong><?php echo date_i18n( 'F j, Y', strtotime($checkout) ); ?></strong></td>
					</tr>
				</table>
			</div>

			<div class="section">
				<h3><?php esc_html_e( '4. Host Information', 'thessnest' ); ?></h3>
				<table>
					<tr>
						<th><?php esc_html_e( 'Host/Company Name', 'thessnest' ); ?></th>
						<td><?php echo esc_html( $landlord ? $landlord->display_name : 'N/A' ); ?></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Tax ID / AFM', 'thessnest' ); ?></th>
						<td><?php echo esc_html( $h_tax_id ); ?></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Email', 'thessnest' ); ?></th>
						<td><?php echo esc_html( $landlord ? $landlord->user_email : 'N/A' ); ?></td>
					</tr>
				</table>
			</div>

			<div class="summary">
				<strong><?php esc_html_e( 'Tenancy Agreement Summary:', 'thessnest' ); ?></strong>
				<p>
					<?php esc_html_e( 'This document certifies that a formal lease agreement has been concluded between the Tenant and the Host via the ThessNest platform for the duration stated above.', 'thessnest' ); ?><br><br>
					<?php esc_html_e( 'Financial obligations (including security deposit and initial rent) have been secured through our escrow system, confirming the validity and financial commitment of this reservation.', 'thessnest' ); ?>
				</p>
			</div>

			<div class="verification">
				<div class="stamp">VERIFIED<br>BOOKING</div>
				<h4><?php esc_html_e( 'Digital Verification Footprint', 'thessnest' ); ?></h4>
				<p><?php esc_html_e( 'This certificate is legally binding and generated by the ThessNest Automated Booking System.', 'thessnest' ); ?></p>
				<p><strong><?php esc_html_e( 'Status:', 'thessnest' ); ?></strong> <?php esc_html_e( 'FULLY CONFIRMED & SECURED', 'thessnest' ); ?></p>
				<p><em><?php esc_html_e( 'Platform Operator:', 'thessnest' ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?></em></p>
			</div>
			
			<div class="footer">
				<p><?php esc_html_e( 'This document is generated automatically by ThessNest for visa and registry purposes.', 'thessnest' ); ?></p>
			</div>
		</body>
		</html>
		<?php
		exit;
	}
}
