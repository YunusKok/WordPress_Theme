<?php
/**
 * ThessNest — Automated Invoicing Engine
 *
 * Generates a clean, print-ready HTML invoice for completed bookings.
 * Accessed via ?thessnest_invoice=BOOKING_ID
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

class ThessNest_Automated_Invoicing {

	public function __construct() {
		// Intercept template redirect to check for invoice request
		add_action( 'template_redirect', [ $this, 'handle_invoice_request' ] );
		
		// Add the 'Download Invoice' button to the dashboard
		add_action( 'thessnest_after_booking_status', [ $this, 'add_invoice_button_to_dashboard' ] );
	}

	/**
	 * Render the invoice if the query var is present
	 */
	public function handle_invoice_request() {
		if ( isset( $_GET['thessnest_invoice'] ) && is_user_logged_in() ) {
			$booking_id = intval( $_GET['thessnest_invoice'] );
			
			// Security check: Only landlord or tenant or admin can view
			$current_user_id = get_current_user_id();
			$tenant_id       = get_post_field( 'post_author', $booking_id );
			$property_id     = get_post_meta( $booking_id, '_booking_property_id', true );
			$landlord_id     = get_post_field( 'post_author', $property_id );

			if ( $current_user_id !== (int) $tenant_id && $current_user_id !== (int) $landlord_id && ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You do not have permission to view this invoice.', 'thessnest' ) );
			}

			$this->render_html_invoice( $booking_id );
			exit;
		}
	}

	/**
	 * Output the HTML layout for the invoice
	 */
	private function render_html_invoice( $booking_id ) {
		$property_id   = get_post_meta( $booking_id, '_booking_property_id', true );
		$checkin       = get_post_meta( $booking_id, '_booking_checkin', true );
		$checkout      = get_post_meta( $booking_id, '_booking_checkout', true );
		$tenant_id     = get_post_field( 'post_author', $booking_id );
		$landlord_id   = get_post_field( 'post_author', $property_id );
		
		$tenant        = get_userdata( $tenant_id );
		$landlord      = get_userdata( $landlord_id );
		
		$rent_total    = get_post_meta( $booking_id, '_booking_rent_total', true );
		$deposit       = get_post_meta( $property_id, '_thessnest_deposit', true );

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

		$invoice_number = 'INV-' . date( 'Y' ) . '-' . str_pad( $booking_id, 5, '0', STR_PAD_LEFT );
		$invoice_date   = get_the_date( 'Y-m-d', $booking_id );

		?>
		<!DOCTYPE html>
		<html lang="en">
		<head>
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<title><?php echo esc_html( $invoice_number ); ?> - Invoice</title>
			<style>
				body {
					font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
					color: #333;
					background: #f7f9fc;
					margin: 0;
					padding: 40px;
					font-size: 14px;
				}
				.invoice-box {
					max-width: 800px;
					margin: auto;
					padding: 40px;
					border: 1px solid #eee;
					box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
					background: #fff;
				}
				.invoice-box table {
					width: 100%;
					line-height: inherit;
					text-align: left;
					border-collapse: collapse;
				}
				.invoice-box table td {
					padding: 10px;
					vertical-align: top;
				}
				.invoice-box table tr td:nth-child(2) {
					text-align: right;
				}
				.invoice-box table tr.top table td {
					padding-bottom: 20px;
				}
				.invoice-box table tr.top table td.title {
					font-size: 45px;
					line-height: 45px;
					color: #333;
				}
				.invoice-box table tr.information table td {
					padding-bottom: 40px;
				}
				.invoice-box table tr.heading td {
					background: #f8f9fa;
					border-bottom: 2px solid #ddd;
					font-weight: bold;
				}
				.invoice-box table tr.details td {
					padding-bottom: 20px;
				}
				.invoice-box table tr.item td {
					border-bottom: 1px solid #eee;
				}
				.invoice-box table tr.item.last td {
					border-bottom: none;
				}
				.invoice-box table tr.total td:nth-child(2) {
					border-top: 2px solid #eee;
					font-weight: bold;
					font-size: 1.2em;
				}
				.text-muted {
					color: #6c757d;
				}
				.print-btn {
					display: block;
					width: 200px;
					margin: 20px auto;
					text-align: center;
					padding: 12px 20px;
					background: #2563eb;
					color: #fff;
					text-decoration: none;
					border-radius: 6px;
					font-weight: 600;
					cursor: pointer;
					border: none;
				}
				@media print {
					body { background: transparent; padding: 0; }
					.invoice-box { box-shadow: none; border: none; padding: 0; }
					.print-btn { display: none; }
				}
			</style>
		</head>
		<body>
			<button class="print-btn" onclick="window.print();">
				<svg style="vertical-align:middle; margin-right:5px;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
				Print / Save as PDF
			</button>

			<div class="invoice-box">
				<table cellpadding="0" cellspacing="0">
					<tr class="top">
						<td colspan="2">
							<table>
								<tr>
									<td class="title">
										<img src="<?php echo esc_url( $site_logo ); ?>" style="width: 100%; max-width: 200px" alt="Logo" />
									</td>
									<td>
										<strong>Invoice Number:</strong> <?php echo esc_html( $invoice_number ); ?><br />
										<strong>Created:</strong> <?php echo esc_html( $invoice_date ); ?><br />
										<strong>Status:</strong> Paid
									</td>
								</tr>
							</table>
						</td>
					</tr>

					<tr class="information">
						<td colspan="2">
							<table>
								<tr>
									<td>
										<strong>Billed From (Landlord):</strong><br />
										<?php echo esc_html( $landlord->display_name ); ?><br />
										<?php echo esc_html( $landlord->user_email ); ?>
									</td>
									<td>
										<strong>Billed To (Tenant):</strong><br />
										<?php echo esc_html( $tenant->display_name ); ?><br />
										<?php echo esc_html( $tenant->user_email ); ?>
									</td>
								</tr>
							</table>
						</td>
					</tr>

					<tr class="heading">
						<td>Description</td>
						<td>Amount</td>
					</tr>

					<tr class="item">
						<td>
							<strong><?php echo esc_html( get_the_title( $property_id ) ); ?></strong><br>
							<span class="text-muted">Stay: <?php echo esc_html( $checkin ); ?> &mdash; <?php echo esc_html( $checkout ); ?></span>
						</td>
						<td>€<?php echo number_format( (float) $rent_total, 2 ); ?></td>
					</tr>

					<tr class="item last">
						<td>
							<strong>Security Deposit</strong><br>
							<span class="text-muted">Refundable via platform rules</span>
						</td>
						<td>€<?php echo number_format( (float) $deposit, 2 ); ?></td>
					</tr>

					<tr class="total">
						<td></td>
						<td>Total: €<?php echo number_format( (float) $rent_total + (float) $deposit, 2 ); ?></td>
					</tr>
				</table>

				<div style="margin-top: 40px; text-align: center; border-top: 1px solid #eee; padding-top: 20px; font-size: 12px; color: #777;">
					<p>This is a computer-generated invoice and requires no physical signature.</p>
					<p>Thank you for using <?php bloginfo('name'); ?>!</p>
				</div>
			</div>
		</body>
		</html>
		<?php
	}

	/**
	 * Append Invoice Button to User Dashboard using Action Hook
	 */
	public function add_invoice_button_to_dashboard( $booking_id ) {
		$status = get_post_meta( $booking_id, '_booking_status', true );
		// Only show invoice for accepted/completed/paid bookings
		if ( in_array( $status, [ 'accepted', 'completed', 'paid' ] ) ) {
			$invoice_url = add_query_arg( 'thessnest_invoice', $booking_id, home_url( '/' ) );
			echo '<a href="' . esc_url( $invoice_url ) . '" target="_blank" class="button" style="margin-top:10px; display:inline-flex; align-items:center; gap:5px;">';
			echo '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>';
			echo esc_html__( 'Download Invoice', 'thessnest' );
			echo '</a>';
		}
	}
}

new ThessNest_Automated_Invoicing();
