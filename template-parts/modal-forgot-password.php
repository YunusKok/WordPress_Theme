<?php
/**
 * ThessNest — Forgot Password Modal
 *
 * AJAX-powered password reset request modal.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;
?>

<!-- Forgot Password Modal Overlay -->
<div class="thessnest-modal-overlay" id="modal-forgot-password-overlay" style="display:none;"></div>
<div class="thessnest-modal" id="modal-forgot-password" role="dialog" aria-modal="true" aria-labelledby="modal-forgot-title" style="display:none;">
	<div class="thessnest-modal-inner">
		<button class="thessnest-modal-close" data-modal-close="modal-forgot-password" aria-label="<?php esc_attr_e( 'Close', 'thessnest' ); ?>">
			<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
		</button>

		<div class="thessnest-modal-header">
			<h2 id="modal-forgot-title"><?php esc_html_e( 'Reset Password', 'thessnest' ); ?></h2>
			<p><?php esc_html_e( 'Enter your email address and we\'ll send you a link to reset your password.', 'thessnest' ); ?></p>
		</div>

		<form id="thessnest-forgot-form" class="thessnest-modal-form">
			<input type="hidden" name="action" value="thessnest_ajax_forgot_password">
			<?php wp_nonce_field( 'thessnest-forgot-nonce', 'forgot_security' ); ?>

			<div class="thessnest-form-group">
				<label for="forgot_email"><?php esc_html_e( 'Email Address', 'thessnest' ); ?></label>
				<input type="email" id="forgot_email" name="email" required autocomplete="email" placeholder="<?php esc_attr_e( 'your@email.com', 'thessnest' ); ?>">
			</div>

			<div id="forgot-response" class="thessnest-modal-response" style="display:none;"></div>

			<button type="submit" class="btn btn-primary thessnest-modal-submit" id="forgot-submit-btn" style="width:100%;">
				<?php esc_html_e( 'Send Reset Link', 'thessnest' ); ?>
			</button>
		</form>

		<div class="thessnest-modal-footer">
			<p>
				<?php esc_html_e( 'Remember your password?', 'thessnest' ); ?>
				<a href="#" class="thessnest-modal-link" data-modal-switch="modal-login"><?php esc_html_e( 'Sign In', 'thessnest' ); ?></a>
			</p>
		</div>
	</div>
</div>
