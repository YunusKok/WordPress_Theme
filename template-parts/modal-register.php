<?php
/**
 * ThessNest — Register Modal
 *
 * AJAX-powered registration modal for new users.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;
?>

<!-- Register Modal Overlay -->
<div class="thessnest-modal-overlay" id="modal-register-overlay" style="display:none;"></div>
<div class="thessnest-modal" id="modal-register" role="dialog" aria-modal="true" aria-labelledby="modal-register-title" style="display:none;">
	<div class="thessnest-modal-inner">
		<button class="thessnest-modal-close" data-modal-close="modal-register" aria-label="<?php esc_attr_e( 'Close', 'thessnest' ); ?>">
			<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
		</button>

		<div class="thessnest-modal-header">
			<h2 id="modal-register-title"><?php esc_html_e( 'Create Account', 'thessnest' ); ?></h2>
			<p><?php esc_html_e( 'Join ThessNest to find or list your ideal accommodation.', 'thessnest' ); ?></p>
		</div>

		<form id="thessnest-register-form" class="thessnest-modal-form">
			<input type="hidden" name="action" value="thessnest_ajax_register">
			<?php wp_nonce_field( 'thessnest-register-nonce', 'register_security' ); ?>

			<div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-3);">
				<div class="thessnest-form-group">
					<label for="reg_first_name"><?php esc_html_e( 'First Name', 'thessnest' ); ?></label>
					<input type="text" id="reg_first_name" name="first_name" required placeholder="<?php esc_attr_e( 'First name', 'thessnest' ); ?>">
				</div>
				<div class="thessnest-form-group">
					<label for="reg_last_name"><?php esc_html_e( 'Last Name', 'thessnest' ); ?></label>
					<input type="text" id="reg_last_name" name="last_name" required placeholder="<?php esc_attr_e( 'Last name', 'thessnest' ); ?>">
				</div>
			</div>

			<div class="thessnest-form-group">
				<label for="reg_email"><?php esc_html_e( 'Email Address', 'thessnest' ); ?></label>
				<input type="email" id="reg_email" name="email" required autocomplete="email" placeholder="<?php esc_attr_e( 'your@email.com', 'thessnest' ); ?>">
			</div>

			<div class="thessnest-form-group">
				<label for="reg_username"><?php esc_html_e( 'Username', 'thessnest' ); ?></label>
				<input type="text" id="reg_username" name="username" required autocomplete="username" placeholder="<?php esc_attr_e( 'Choose a username', 'thessnest' ); ?>">
			</div>

			<div class="thessnest-form-group">
				<label for="reg_password"><?php esc_html_e( 'Password', 'thessnest' ); ?></label>
				<input type="password" id="reg_password" name="password" required autocomplete="new-password" minlength="8" placeholder="<?php esc_attr_e( 'Minimum 8 characters', 'thessnest' ); ?>">
			</div>

			<div class="thessnest-form-group">
				<label for="reg_role"><?php esc_html_e( 'I am a...', 'thessnest' ); ?></label>
				<select id="reg_role" name="role" required>
					<option value="tenant"><?php esc_html_e( 'Tenant (Looking for housing)', 'thessnest' ); ?></option>
					<option value="landlord"><?php esc_html_e( 'Landlord (Listing properties)', 'thessnest' ); ?></option>
				</select>
			</div>

			<div style="margin-bottom:var(--space-4);">
				<label style="display:flex; align-items:flex-start; gap:var(--space-2); cursor:pointer; font-size:var(--font-size-sm); line-height:1.5;">
					<input type="checkbox" name="terms" required style="margin-top:3px;">
					<?php printf(
						esc_html__( 'I agree to the %sTerms of Service%s and %sPrivacy Policy%s.', 'thessnest' ),
						'<a href="' . esc_url( get_privacy_policy_url() ?: '#' ) . '" target="_blank" style="color:var(--color-accent);">',
						'</a>',
						'<a href="' . esc_url( get_privacy_policy_url() ?: '#' ) . '" target="_blank" style="color:var(--color-accent);">',
						'</a>'
					); ?>
				</label>
			</div>

			<div id="register-response" class="thessnest-modal-response" style="display:none;"></div>

			<button type="submit" class="btn btn-primary thessnest-modal-submit" id="register-submit-btn" style="width:100%;">
				<?php esc_html_e( 'Create Account', 'thessnest' ); ?>
			</button>
		</form>

		<div class="thessnest-modal-footer">
			<p>
				<?php esc_html_e( 'Already have an account?', 'thessnest' ); ?>
				<a href="#" class="thessnest-modal-link" data-modal-switch="modal-login"><?php esc_html_e( 'Sign In', 'thessnest' ); ?></a>
			</p>
		</div>
	</div>
</div>
