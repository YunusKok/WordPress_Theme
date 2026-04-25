<?php
/**
 * ThessNest — Login Modal
 *
 * AJAX-powered login modal that appears when clicking "Sign In"
 * instead of redirecting to wp-login.php.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;
?>

<!-- Login Modal Overlay -->
<div class="thessnest-modal-overlay" id="modal-login-overlay" style="display:none;"></div>
<div class="thessnest-modal" id="modal-login" role="dialog" aria-modal="true" aria-labelledby="modal-login-title" style="display:none;">
	<div class="thessnest-modal-inner">
		<button class="thessnest-modal-close" data-modal-close="modal-login" aria-label="<?php esc_attr_e( 'Close', 'thessnest' ); ?>">
			<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
		</button>

		<div class="thessnest-modal-header">
			<h2 id="modal-login-title"><?php esc_html_e( 'Welcome Back', 'thessnest' ); ?></h2>
			<p><?php esc_html_e( 'Sign in to your ThessNest account.', 'thessnest' ); ?></p>
		</div>

		<form id="thessnest-login-form" class="thessnest-modal-form">
			<input type="hidden" name="action" value="thessnest_ajax_login">
			<?php wp_nonce_field( 'thessnest-login-nonce', 'login_security' ); ?>

			<div class="thessnest-form-group">
				<label for="login_username"><?php esc_html_e( 'Email or Username', 'thessnest' ); ?></label>
				<input type="text" id="login_username" name="username" required autocomplete="username" placeholder="<?php esc_attr_e( 'Enter your email or username', 'thessnest' ); ?>">
			</div>

			<div class="thessnest-form-group">
				<label for="login_password"><?php esc_html_e( 'Password', 'thessnest' ); ?></label>
				<input type="password" id="login_password" name="password" required autocomplete="current-password" placeholder="<?php esc_attr_e( 'Enter your password', 'thessnest' ); ?>">
			</div>

			<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-4);">
				<label style="display:flex; align-items:center; gap:var(--space-2); cursor:pointer; font-size:var(--font-size-sm);">
					<input type="checkbox" name="remember" value="1">
					<?php esc_html_e( 'Remember me', 'thessnest' ); ?>
				</label>
				<a href="#" class="thessnest-modal-link" data-modal-switch="modal-forgot-password" style="font-size:var(--font-size-sm);"><?php esc_html_e( 'Forgot Password?', 'thessnest' ); ?></a>
			</div>

			<div id="login-response" class="thessnest-modal-response" style="display:none;"></div>

			<button type="submit" class="btn btn-primary thessnest-modal-submit" id="login-submit-btn" style="width:100%;">
				<?php esc_html_e( 'Sign In', 'thessnest' ); ?>
			</button>
		</form>

		<div class="thessnest-modal-footer">
			<p>
				<?php esc_html_e( "Don't have an account?", 'thessnest' ); ?>
				<a href="#" class="thessnest-modal-link" data-modal-switch="modal-register"><?php esc_html_e( 'Create Account', 'thessnest' ); ?></a>
			</p>
		</div>
	</div>
</div>
