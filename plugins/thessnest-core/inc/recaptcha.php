<?php
/**
 * ThessNest — Google reCAPTCHA v3 Integration
 *
 * Handles the server-side validation of Google reCAPTCHA v3 tokens.
 * Settings are managed via Redux (Section 19: Google reCaptcha).
 *
 * Protected forms:
 * - Registration
 * - Login
 * - Booking Request
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;


/* ==========================================================================
   ENQUEUE reCAPTCHA SCRIPT
   ========================================================================== */

/**
 * Enqueue Google reCAPTCHA v3 script on the frontend when enabled.
 */
function thessnest_enqueue_recaptcha() {
	if ( ! thessnest_opt( 'recaptcha_enabled', false ) ) {
		return;
	}

	$site_key = thessnest_opt( 'recaptcha_site_key', '' );
	if ( empty( $site_key ) ) {
		return;
	}

	wp_enqueue_script(
		'google-recaptcha',
		'https://www.google.com/recaptcha/api.js?render=' . esc_attr( $site_key ),
		array(),
		'3.0',
		true
	);

	// Inline script to generate tokens for each protected form
	$inline_js = "
		document.addEventListener('DOMContentLoaded', function() {
			if (typeof grecaptcha === 'undefined') return;
			
			grecaptcha.ready(function() {
				// Helper: generate token and inject into form
				function attachRecaptcha(formId, action) {
					var form = document.getElementById(formId);
					if (!form) return;
					
					form.addEventListener('submit', function(e) {
						var tokenField = form.querySelector('input[name=\"g-recaptcha-response\"]');
						if (tokenField && tokenField.value) return; // Already have a token
						
						e.preventDefault();
						grecaptcha.execute('" . esc_js( $site_key ) . "', {action: action}).then(function(token) {
							if (!tokenField) {
								tokenField = document.createElement('input');
								tokenField.type = 'hidden';
								tokenField.name = 'g-recaptcha-response';
								form.appendChild(tokenField);
							}
							tokenField.value = token;
							
							// Also add action name for server-side validation
							var actionField = form.querySelector('input[name=\"recaptcha_action\"]');
							if (!actionField) {
								actionField = document.createElement('input');
								actionField.type = 'hidden';
								actionField.name = 'recaptcha_action';
								form.appendChild(actionField);
							}
							actionField.value = action;
							
							// Re-trigger submit
							form.requestSubmit ? form.requestSubmit() : form.submit();
						});
					}, { once: false });
				}
				
				// Attach to known forms
				attachRecaptcha('thessnest-login-form', 'login');
				attachRecaptcha('thessnest-register-form', 'register');
				attachRecaptcha('booking-form', 'booking');
			});
		});
	";

	wp_add_inline_script( 'google-recaptcha', $inline_js );
}
add_action( 'wp_enqueue_scripts', 'thessnest_enqueue_recaptcha' );


/* ==========================================================================
   SERVER-SIDE VERIFICATION
   ========================================================================== */

/**
 * Verify a reCAPTCHA v3 token server-side.
 *
 * @param string $token  The token from the frontend.
 * @param string $action Expected action name.
 * @param float  $threshold Minimum score (0.0 - 1.0). Default 0.5.
 * @return bool|WP_Error True if valid, WP_Error on failure.
 */
function thessnest_verify_recaptcha( $token = '', $action = '', $threshold = 0.5 ) {
	if ( ! thessnest_opt( 'recaptcha_enabled', false ) ) {
		return true; // reCAPTCHA disabled, always pass
	}

	$secret_key = thessnest_opt( 'recaptcha_secret_key', '' );
	if ( empty( $secret_key ) ) {
		return true; // No secret key configured
	}

	if ( empty( $token ) ) {
		return new WP_Error( 'recaptcha_missing', __( 'reCAPTCHA verification failed. Please try again.', 'thessnest' ) );
	}

	$response = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array(
		'body' => array(
			'secret'   => $secret_key,
			'response' => $token,
			'remoteip' => $_SERVER['REMOTE_ADDR'],
		),
		'timeout' => 10,
	) );

	if ( is_wp_error( $response ) ) {
		// Network error — let the user through rather than blocking
		return true;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( empty( $body['success'] ) || ! $body['success'] ) {
		return new WP_Error( 'recaptcha_failed', __( 'reCAPTCHA verification failed. Please try again.', 'thessnest' ) );
	}

	// Verify action matches (if provided)
	if ( ! empty( $action ) && isset( $body['action'] ) && $body['action'] !== $action ) {
		return new WP_Error( 'recaptcha_action_mismatch', __( 'Security validation failed. Please try again.', 'thessnest' ) );
	}

	// Check score threshold
	if ( isset( $body['score'] ) && $body['score'] < $threshold ) {
		return new WP_Error( 'recaptcha_low_score', __( 'Our security system flagged this request. Please try again or contact support.', 'thessnest' ) );
	}

	return true;
}


/* ==========================================================================
   HOOK INTO FORMS
   ========================================================================== */

/**
 * Validate reCAPTCHA on WordPress registration.
 *
 * @param WP_Error $errors Registration errors.
 * @return WP_Error
 */
function thessnest_recaptcha_registration( $errors ) {
	$token  = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';
	$result = thessnest_verify_recaptcha( $token, 'register' );

	if ( is_wp_error( $result ) ) {
		$errors->add( 'recaptcha_error', $result->get_error_message() );
	}

	return $errors;
}
add_filter( 'registration_errors', 'thessnest_recaptcha_registration', 10, 1 );

/**
 * Validate reCAPTCHA on WordPress login.
 *
 * @param WP_User|WP_Error $user     User object or error.
 * @param string           $username Username.
 * @param string           $password Password.
 * @return WP_User|WP_Error
 */
function thessnest_recaptcha_login( $user, $username, $password ) {
	// Skip if no username provided (initial form load)
	if ( empty( $username ) ) {
		return $user;
	}

	$token  = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';
	$result = thessnest_verify_recaptcha( $token, 'login' );

	if ( is_wp_error( $result ) ) {
		return $result;
	}

	return $user;
}
add_filter( 'authenticate', 'thessnest_recaptcha_login', 30, 3 );

/**
 * Validate reCAPTCHA on booking submission (AJAX).
 * This hooks into the booking AJAX handler early.
 */
function thessnest_recaptcha_booking() {
	$token  = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';
	$result = thessnest_verify_recaptcha( $token, 'booking' );

	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ) );
	}
}
add_action( 'thessnest_before_booking_submit', 'thessnest_recaptcha_booking' );
