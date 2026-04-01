<?php
/**
 * Template Name: Native Checkout
 *
 * A lightweight, WooCommerce-independent checkout page for collecting 
 * booking deposits via Stripe or PayPal native APIs.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

// Redirect if not logged in
if ( ! is_user_logged_in() ) {
	auth_redirect();
}

$booking_id = isset( $_GET['booking_id'] ) ? intval( $_GET['booking_id'] ) : 0;

if ( ! $booking_id ) {
	wp_redirect( home_url('/dashboard/') );
	exit;
}

// Verify Ownership & Status
$tenant_id = get_post_field( 'post_author', $booking_id );
if ( $tenant_id != get_current_user_id() ) {
	wp_die( esc_html__( 'You do not have permission to view this checkout page.', 'thessnest' ) );
}

$status = get_post_meta( $booking_id, '_booking_status', true );
if ( $status !== 'awaiting_payment' ) {
	wp_die( esc_html__( 'This booking is not awaiting payment. It may have already been paid or cancelled.', 'thessnest' ) );
}

$property_id = get_post_meta( $booking_id, '_booking_property_id', true );
$deposit     = (float) get_post_meta( $property_id, '_thessnest_deposit', true );

if ( $deposit <= 0 ) {
	wp_die( esc_html__( 'No deposit required for this booking.', 'thessnest' ) );
}

$currency = function_exists('thessnest_opt') ? thessnest_opt('payment_currency', 'EUR') : 'EUR';

global $thessnest_opt;
$stripe_enabled = isset($thessnest_opt['enable_stripe']) && $thessnest_opt['enable_stripe'];
$paypal_enabled = isset($thessnest_opt['enable_paypal']) && $thessnest_opt['enable_paypal'];

if ( ! $stripe_enabled && ! $paypal_enabled ) {
	wp_die( esc_html__( 'No native payment gateways are enabled. Please contact support.', 'thessnest' ) );
}

get_header();
?>

<main id="main-content" style="background:var(--color-surface); min-height:80vh; padding:var(--space-10) 0;">
	<div class="container" style="max-width:800px;">
		
		<div style="background:var(--color-background); border:1px solid var(--color-border); border-radius:var(--radius-xl); overflow:hidden; box-shadow:var(--shadow-md);">
			
			<!-- Checkout Header -->
			<div style="background:var(--color-primary); color:#fff; padding:var(--space-6); text-align:center;">
				<h1 style="margin:0; font-size:var(--font-size-2xl);"><?php esc_html_e('Complete Your Booking', 'thessnest'); ?></h1>
				<p style="margin:var(--space-2) 0 0 0; opacity:0.9;"><?php esc_html_e('Secure Payment Gateway', 'thessnest'); ?></p>
			</div>

			<!-- Order Summary -->
			<div style="padding:var(--space-6); border-bottom:1px solid var(--color-border);">
				<h2 style="font-size:var(--font-size-lg); margin-top:0; margin-bottom:var(--space-4);"><?php esc_html_e('Order Summary', 'thessnest'); ?></h2>
				
				<div style="display:flex; justify-content:space-between; margin-bottom:var(--space-2);">
					<span style="color:var(--color-text-muted);"><?php esc_html_e('Property:', 'thessnest'); ?></span>
					<strong><?php echo esc_html( get_the_title( $property_id ) ); ?></strong>
				</div>
				<div style="display:flex; justify-content:space-between; margin-bottom:var(--space-2);">
					<span style="color:var(--color-text-muted);"><?php esc_html_e('Check-in:', 'thessnest'); ?></span>
					<span><?php echo esc_html( get_post_meta( $booking_id, '_booking_checkin', true ) ); ?></span>
				</div>
				<div style="display:flex; justify-content:space-between; margin-bottom:var(--space-2);">
					<span style="color:var(--color-text-muted);"><?php esc_html_e('Check-out:', 'thessnest'); ?></span>
					<span><?php echo esc_html( get_post_meta( $booking_id, '_booking_checkout', true ) ); ?></span>
				</div>
				
				<hr style="border:none; border-top:1px dashed var(--color-border); margin:var(--space-4) 0;">
				
				<div style="display:flex; justify-content:space-between; font-size:var(--font-size-xl); font-weight:700;">
					<span><?php esc_html_e('Deposit to Pay:', 'thessnest'); ?></span>
					<span style="color:#2563eb;"><?php echo esc_html( $currency ) . ' ' . number_format( $deposit, 2 ); ?></span>
				</div>
			</div>

			<!-- Payment Methods -->
			<div style="padding:var(--space-6); background:#f8fafc;">
				<h2 style="font-size:var(--font-size-lg); margin-top:0; margin-bottom:var(--space-4);"><?php esc_html_e('Select Payment Method', 'thessnest'); ?></h2>

				<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:var(--space-4);">
					
					<?php if ( $stripe_enabled ) : ?>
					<!-- Stripe Button -->
					<div class="payment-card" style="background:#fff; border:2px solid #e2e8f0; border-radius:var(--radius-lg); padding:var(--space-4); text-align:center; cursor:pointer; transition:all 0.2s;">
						<div style="font-size:24px; font-weight:800; color:#635bff; margin-bottom:var(--space-2);">stripe</div>
						<p style="font-size:var(--font-size-sm); color:var(--color-text-muted); margin-bottom:var(--space-4);">Pay securely with Credit / Debit Card, Apple Pay, or Google Pay.</p>
						<button class="btn btn-primary btn-process-payment" data-gateway="stripe" style="width:100%; background:#635bff; border-color:#635bff;">
							<?php esc_html_e('Pay via Stripe', 'thessnest'); ?>
						</button>
					</div>
					<?php endif; ?>

					<?php if ( $paypal_enabled ) : ?>
					<!-- PayPal Button -->
					<div class="payment-card" style="background:#fff; border:2px solid #e2e8f0; border-radius:var(--radius-lg); padding:var(--space-4); text-align:center; cursor:pointer; transition:all 0.2s;">
						<div style="font-size:24px; font-weight:800; color:#003087; margin-bottom:var(--space-2);"><i>PayPal</i></div>
						<p style="font-size:var(--font-size-sm); color:var(--color-text-muted); margin-bottom:var(--space-4);">Pay quickly using your PayPal balance or linked cards.</p>
						<button class="btn btn-primary btn-process-payment" data-gateway="paypal" style="width:100%; background:#ffc439; color:#000; border-color:#ffc439;">
							<?php esc_html_e('Pay via PayPal', 'thessnest'); ?>
						</button>
					</div>
					<?php endif; ?>

				</div>

				<div id="checkout-error" style="display:none; margin-top:var(--space-4); padding:var(--space-3); background:#fef2f2; color:#991b1b; border-radius:var(--radius-md); font-weight:500;"></div>
			</div>

		</div>

		<p style="text-align:center; color:var(--color-text-muted); font-size:12px; margin-top:var(--space-6);">
			<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
			<?php esc_html_e('Your payment data is fully encrypted and processed directly by the gateway. We do not store your card details.', 'thessnest'); ?>
		</p>
	</div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const buttons = document.querySelectorAll('.btn-process-payment');
	const errorDiv = document.getElementById('checkout-error');

	buttons.forEach(btn => {
		btn.addEventListener('click', function(e) {
			e.preventDefault();
			const gateway = this.dataset.gateway;
			
			// Disable all buttons and show loading
			buttons.forEach(b => {
				b.disabled = true;
				b.style.opacity = '0.5';
			});
			this.innerHTML = '<svg class="thessnest-spinner" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite; vertical-align:middle;"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg> Redirecting...';
			
			const formData = new FormData();
			formData.append('action', 'thessnest_process_native_payment');
			formData.append('security', '<?php echo esc_js( wp_create_nonce("thessnest_checkout_nonce") ); ?>');
			formData.append('booking_id', '<?php echo esc_js( $booking_id ); ?>');
			formData.append('gateway', gateway);

			fetch('<?php echo esc_url(admin_url("admin-ajax.php")); ?>', {
				method: 'POST',
				body: formData
			})
			.then(r => r.json())
			.then(data => {
				if (data.success && data.data.redirect_url) {
					window.location.href = data.data.redirect_url;
				} else {
					errorDiv.style.display = 'block';
					errorDiv.textContent = data.data.message || 'Payment provider error. Please try again.';
					
					// Re-enable buttons
					buttons.forEach(b => {
						b.disabled = false;
						b.style.opacity = '1';
					});
					this.innerHTML = 'Pay via ' + (gateway === 'stripe' ? 'Stripe' : 'PayPal');
				}
			})
			.catch(err => {
				errorDiv.style.display = 'block';
				errorDiv.textContent = 'Server connection failed.';
				buttons.forEach(b => {
					b.disabled = false;
					b.style.opacity = '1';
				});
				this.innerHTML = 'Pay via ' + (gateway === 'stripe' ? 'Stripe' : 'PayPal');
			});
		});
	});
});
</script>

<?php get_footer(); ?>
