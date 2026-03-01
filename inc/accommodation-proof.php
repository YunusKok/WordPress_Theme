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
		
		// Output the HTML
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta charset="<?php bloginfo( 'charset' ); ?>">
			<title><?php esc_html_e( 'Proof of Accommodation', 'thessnest' ); ?></title>
			<style>
				body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; padding: 40px; color: #333; max-width: 800px; margin: 0 auto; background: #fff; }
				.header { border-bottom: 2px solid #ddd; padding-bottom: 20px; margin-bottom: 30px; text-align: center; }
				.header h1 { color: #0056b3; margin: 0; }
				.section { margin-bottom: 30px; }
				.section h2 { font-size: 18px; border-bottom: 1px solid #eee; padding-bottom: 10px; color: #555; }
				.grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
				.label { font-weight: bold; color: #666; font-size: 14px; }
				.value { font-size: 16px; margin-bottom: 10px; }
				.footer { margin-top: 50px; text-align: center; color: #999; font-size: 12px; border-top: 1px solid #eee; padding-top: 20px; }
				@media print { body { padding: 0; } .print-btn { display: none; } }
				.print-btn { padding: 10px 20px; background: #0056b3; color: white; border: none; border-radius: 4px; cursor: pointer; margin-top: 20px; font-size: 16px; }
			</style>
		</head>
		<body>
			<div class="header">
				<h1><?php esc_html_e( 'Proof of Accommodation', 'thessnest' ); ?></h1>
				<p><?php esc_html_e( 'Official Booking Confirmation - ThessNest Platform', 'thessnest' ); ?></p>
			</div>
			
			<div class="section">
				<h2><?php esc_html_e( 'Booking Reference: #', 'thessnest' ); ?><?php echo esc_html($booking_id); ?></h2>
				<div class="grid">
					<div>
						<div class="label"><?php esc_html_e( 'Date of Issue', 'thessnest' ); ?></div>
						<div class="value"><?php echo date_i18n( get_option('date_format') ); ?></div>
						
						<div class="label"><?php esc_html_e( 'Booking Status', 'thessnest' ); ?></div>
						<div class="value" style="color: green; font-weight: bold;"><?php esc_html_e( 'CONFIRMED', 'thessnest' ); ?></div>
					</div>
					<div>
						<div class="label"><?php esc_html_e( 'Total Price', 'thessnest' ); ?></div>
						<div class="value"><?php echo esc_html( thessnest_format_price($price) ); ?></div>
					</div>
				</div>
			</div>
			
			<div class="section grid">
				<div>
					<h2><?php esc_html_e( 'Tenant Details', 'thessnest' ); ?></h2>
					<div class="label"><?php esc_html_e( 'Name', 'thessnest' ); ?></div>
					<div class="value"><?php $t = get_userdata($tenant_id); echo esc_html($t ? $t->display_name : 'N/A'); ?></div>
					<div class="label"><?php esc_html_e( 'Guests', 'thessnest' ); ?></div>
					<div class="value"><?php echo esc_html($guests); ?></div>
				</div>
				<div>
					<h2><?php esc_html_e( 'Host Details', 'thessnest' ); ?></h2>
					<div class="label"><?php esc_html_e( 'Name', 'thessnest' ); ?></div>
					<div class="value"><?php $h = get_userdata($landlord_id); echo esc_html($h ? $h->display_name : 'N/A'); ?></div>
					<?php 
					$kyc = get_user_meta( $landlord_id, '_kyc_status', true );
					if ( $kyc === 'approved' ) : ?>
						<div class="label"><?php esc_html_e( 'Verification', 'thessnest' ); ?></div>
						<div class="value" style="color: green;"><?php esc_html_e( 'Verified Host', 'thessnest' ); ?></div>
					<?php endif; ?>
				</div>
			</div>
			
			<div class="section">
				<h2><?php esc_html_e( 'Property Details', 'thessnest' ); ?></h2>
				<div class="label"><?php esc_html_e( 'Property Name', 'thessnest' ); ?></div>
				<div class="value"><?php echo esc_html( get_the_title($property_id) ); ?></div>
				
				<div class="label"><?php esc_html_e( 'Location', 'thessnest' ); ?></div>
				<div class="value">
					<?php echo esc_html( thessnest_get_first_term( 'neighborhood', $property_id ) ); ?>, <?php esc_html_e( 'Thessaloniki', 'thessnest' ); ?>
				</div>
				<p><em><?php esc_html_e( 'GPS Coordinates:', 'thessnest' ); ?> <?php echo esc_html( get_post_meta($property_id, '_thessnest_latitude', true) ); ?>, <?php echo esc_html( get_post_meta($property_id, '_thessnest_longitude', true) ); ?></em></p>
			</div>
			
			<div class="section">
				<h2><?php esc_html_e( 'Stay Duration', 'thessnest' ); ?></h2>
				<div class="grid">
					<div>
						<div class="label"><?php esc_html_e( 'Check-In', 'thessnest' ); ?></div>
						<div class="value"><?php echo date_i18n( get_option('date_format'), strtotime($checkin) ); ?></div>
					</div>
					<div>
						<div class="label"><?php esc_html_e( 'Check-Out', 'thessnest' ); ?></div>
						<div class="value"><?php echo date_i18n( get_option('date_format'), strtotime($checkout) ); ?></div>
					</div>
				</div>
			</div>
			
			<div class="footer">
				<p><?php esc_html_e( 'This document is generated automatically by the ThessNest platform for visa and registry purposes.', 'thessnest' ); ?></p>
				<p>&copy; <?php echo date('Y'); ?> <?php esc_html_e( 'ThessNest. All rights reserved.', 'thessnest' ); ?></p>
				<button class="print-btn" onclick="window.print()"><?php esc_html_e( 'Print Document', 'thessnest' ); ?></button>
			</div>
		</body>
		</html>
		<?php
		exit;
	}
}
