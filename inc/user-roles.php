<?php
/**
 * ThessNest — User Roles & Capabilities
 *
 * Roles are only added on theme activation to avoid calling
 * add_role() on every page load (which is a DB write if the
 * role doesn't exist yet).
 *
 * Also handles:
 * - Assigning the correct role on registration (based on Redux toggle)
 * - Setting KYC verification status for new hosts
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register custom user roles (Landlord and Tenant).
 * Hooked to after_switch_theme so it runs only once on activation.
 */
function thessnest_add_roles() {
	// Only add if not already registered
	if ( ! get_role( 'landlord' ) ) {
		add_role( 'landlord', __( 'Landlord', 'thessnest' ), array(
			'read'           => true,
			'upload_files'   => true,  // Required for KYC document uploads
			'edit_posts'     => false,
			'delete_posts'   => false,
		) );
	}

	if ( ! get_role( 'tenant' ) ) {
		add_role( 'tenant', __( 'Tenant', 'thessnest' ), array(
			'read'         => true,
			'edit_posts'   => false,
			'delete_posts' => false,
		) );
	}
}
add_action( 'after_switch_theme', 'thessnest_add_roles' );


/* ==========================================================================
   ROLE ASSIGNMENT ON REGISTRATION
   ========================================================================== */

/**
 * Assign selected role to a newly registered user.
 *
 * When "User roles on the register form" is enabled in Redux,
 * reads the thessnest_user_role POST value (landlord or tenant).
 * Otherwise falls back to the Redux default_user_role setting.
 *
 * Also sets KYC status to 'unverified' for new hosts if
 * Host Verification toggle is active.
 */
function thessnest_assign_role_on_register( $user_id ) {
	$role_selection_enabled = function_exists( 'thessnest_opt' )
		? thessnest_opt( 'enable_role_selection', '0' )
		: '0';

	$allowed_roles = array( 'landlord', 'tenant' );

	if ( $role_selection_enabled === '1' && ! empty( $_POST['thessnest_user_role'] ) ) {
		// Read from form submission
		$selected_role = sanitize_text_field( $_POST['thessnest_user_role'] );
	} else {
		// Fall back to Redux default
		$selected_role = function_exists( 'thessnest_opt' )
			? thessnest_opt( 'default_user_role', 'tenant' )
			: 'tenant';
	}

	// Security: only allow valid roles
	if ( ! in_array( $selected_role, $allowed_roles, true ) ) {
		$selected_role = 'tenant';
	}

	// Set the role
	$user = new WP_User( $user_id );
	$user->set_role( $selected_role );

	// If host verification is enabled and user registered as landlord,
	// mark them as unverified so they cannot list until admin approves.
	if ( $selected_role === 'landlord' ) {
		$verification_enabled = function_exists( 'thessnest_opt' )
			? thessnest_opt( 'enable_host_verification', false )
			: false;

		if ( $verification_enabled ) {
			update_user_meta( $user_id, '_kyc_status', 'unverified' );
		}
	}
}
add_action( 'user_register', 'thessnest_assign_role_on_register' );


/* ==========================================================================
   HOST CAN LIST HELPER
   ========================================================================== */

/**
 * Check if the current host is allowed to create listings.
 *
 * Returns true if:
 * - Host verification is disabled in Redux, OR
 * - The landlord's KYC status is 'approved'
 *
 * @param int|null $user_id User ID. Defaults to current user.
 * @return bool
 */
function thessnest_host_can_list( $user_id = null ) {
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	// If host verification is disabled globally, everyone can list
	$verification_enabled = function_exists( 'thessnest_opt' )
		? thessnest_opt( 'enable_host_verification', false )
		: false;

	if ( ! $verification_enabled ) {
		return true;
	}

	// Admins can always list
	if ( user_can( $user_id, 'manage_options' ) ) {
		return true;
	}

	$status = get_user_meta( $user_id, '_kyc_status', true );
	return ( $status === 'approved' );
}


/* ==========================================================================
   PASSWORD HANDLING ON REGISTRATION
   ========================================================================== */

/**
 * Handle password on registration:
 * - 'custom' mode: read user-defined password from POST and set it
 * - 'auto' mode: generate random password, store it temporarily for welcome email
 */
function thessnest_handle_password_on_register( $user_id ) {
	$password_mode = function_exists( 'thessnest_opt' )
		? thessnest_opt( 'password_mode', 'auto' )
		: 'auto';

	if ( $password_mode === 'custom' && ! empty( $_POST['thessnest_password'] ) ) {
		$password         = $_POST['thessnest_password'];
		$password_confirm = isset( $_POST['thessnest_password_confirm'] ) ? $_POST['thessnest_password_confirm'] : '';

		// Validate password match
		if ( $password !== $password_confirm ) {
			// Can't easily throw error in user_register hook,
			// so just use the password as-is (form has frontend validation)
		}

		// Minimum 6 characters
		if ( strlen( $password ) >= 6 ) {
			wp_set_password( $password, $user_id );
			// Store plain text temporarily for welcome email (will be cleared after send)
			update_user_meta( $user_id, '_thessnest_temp_password', $password );
		}
	} else {
		// Auto-generate password
		$password = wp_generate_password( 12, true, false );
		wp_set_password( $password, $user_id );
		update_user_meta( $user_id, '_thessnest_temp_password', $password );
	}
}
add_action( 'user_register', 'thessnest_handle_password_on_register', 20 );


/**
 * Validate custom password during registration (registration_errors hook).
 */
function thessnest_validate_registration_password( $errors, $sanitized_user_login, $user_email ) {
	$password_mode = function_exists( 'thessnest_opt' )
		? thessnest_opt( 'password_mode', 'auto' )
		: 'auto';

	if ( $password_mode === 'custom' ) {
		$password         = isset( $_POST['thessnest_password'] ) ? $_POST['thessnest_password'] : '';
		$password_confirm = isset( $_POST['thessnest_password_confirm'] ) ? $_POST['thessnest_password_confirm'] : '';

		if ( empty( $password ) ) {
			$errors->add( 'thessnest_password_error', __( '<strong>Error</strong>: Please enter a password.', 'thessnest' ) );
		} elseif ( strlen( $password ) < 6 ) {
			$errors->add( 'thessnest_password_error', __( '<strong>Error</strong>: Password must be at least 6 characters.', 'thessnest' ) );
		} elseif ( $password !== $password_confirm ) {
			$errors->add( 'thessnest_password_error', __( '<strong>Error</strong>: Passwords do not match.', 'thessnest' ) );
		}
	}

	return $errors;
}
add_filter( 'registration_errors', 'thessnest_validate_registration_password', 10, 3 );


/* ==========================================================================
   WELCOME EMAIL
   ========================================================================== */

/**
 * Send a branded welcome email to newly registered users.
 *
 * Contains: username, password, login URL, and role info.
 * Uses the global HTML email wrapper from email-templates.php.
 */
function thessnest_send_welcome_email( $user_id ) {
	$welcome_enabled = function_exists( 'thessnest_opt' )
		? thessnest_opt( 'enable_welcome_email', true )
		: true;

	if ( ! $welcome_enabled ) {
		return;
	}

	$user     = get_userdata( $user_id );
	$password = get_user_meta( $user_id, '_thessnest_temp_password', true );

	if ( ! $user || ! $user->user_email ) {
		return;
	}

	$site_name = get_bloginfo( 'name' );
	$login_url = wp_login_url();
	$subject   = function_exists( 'thessnest_opt' )
		? thessnest_opt( 'welcome_email_subject', 'Welcome to ThessNest! 🏠' )
		: 'Welcome to ThessNest! 🏠';

	// Replace placeholder if site name is in subject
	$subject = str_replace( 'ThessNest', $site_name, $subject );

	// Determine role label
	$role_label = '';
	if ( in_array( 'landlord', (array) $user->roles ) ) {
		$role_label = __( 'Host (Landlord)', 'thessnest' );
	} elseif ( in_array( 'tenant', (array) $user->roles ) ) {
		$role_label = __( 'Renter (Tenant)', 'thessnest' );
	}

	// Build email body (plain text — will be wrapped by email-templates.php)
	$message  = sprintf( __( 'Hi %s,', 'thessnest' ), esc_html( $user->display_name ) ) . "\n\n";
	$message .= sprintf( __( 'Welcome to %s! Your account has been created successfully.', 'thessnest' ), esc_html( $site_name ) ) . "\n\n";
	$message .= __( 'Here are your login details:', 'thessnest' ) . "\n\n";
	$message .= sprintf( __( 'Username: %s', 'thessnest' ), $user->user_login ) . "\n";

	if ( ! empty( $password ) ) {
		$message .= sprintf( __( 'Password: %s', 'thessnest' ), $password ) . "\n";
	} else {
		$message .= __( 'Password: (the one you chose during registration)', 'thessnest' ) . "\n";
	}

	if ( ! empty( $role_label ) ) {
		$message .= sprintf( __( 'Account Type: %s', 'thessnest' ), $role_label ) . "\n";
	}

	$message .= "\n" . sprintf( __( 'Login here: %s', 'thessnest' ), $login_url ) . "\n\n";

	// KYC notice for hosts
	$verification_enabled = function_exists( 'thessnest_opt' )
		? thessnest_opt( 'enable_host_verification', false )
		: false;

	if ( $verification_enabled && in_array( 'landlord', (array) $user->roles ) ) {
		$message .= __( '⚠️ Important: As a host, you need to complete ID verification before you can list properties. Please log in and go to your Dashboard to submit your verification documents.', 'thessnest' ) . "\n\n";
	}

	$message .= sprintf( __( 'Thanks for joining %s!', 'thessnest' ), esc_html( $site_name ) ) . "\n";
	$message .= __( 'The ThessNest Team', 'thessnest' );

	// Send the email
	wp_mail( $user->user_email, $subject, $message );

	// Clean up temp password (security)
	delete_user_meta( $user_id, '_thessnest_temp_password' );
}
add_action( 'user_register', 'thessnest_send_welcome_email', 30 );


/**
 * Suppress default WordPress new user notification email
 * when our custom welcome email is enabled, to avoid duplicate emails.
 */
if ( ! function_exists( 'wp_new_user_notification' ) ) {
	function wp_new_user_notification( $user_id, $deprecated = null, $notify = '' ) {
		$welcome_enabled = function_exists( 'thessnest_opt' )
			? thessnest_opt( 'enable_welcome_email', true )
			: true;

		if ( $welcome_enabled ) {
			// Only send admin notification, skip user notification (we handle it)
			if ( $notify === 'admin' || $notify === 'both' || $notify === '' ) {
				$user = get_userdata( $user_id );
				$admin_email = get_option( 'admin_email' );
				$site_name   = get_bloginfo( 'name' );
				$subject     = sprintf( __( '[%s] New User Registration', 'thessnest' ), $site_name );
				$message     = sprintf( __( 'New user registration on %s:', 'thessnest' ), $site_name ) . "\n\n";
				$message    .= sprintf( __( 'Username: %s', 'thessnest' ), $user->user_login ) . "\n";
				$message    .= sprintf( __( 'Email: %s', 'thessnest' ), $user->user_email ) . "\n";
				wp_mail( $admin_email, $subject, $message );
			}
			return; // Skip default user email
		}

		// If welcome email is disabled, fall back to WordPress default behavior
		// Replicate core wp_new_user_notification for 'user' notify
		if ( $notify !== 'admin' ) {
			$user = get_userdata( $user_id );
			$key  = get_password_reset_key( $user );
			if ( ! is_wp_error( $key ) ) {
				$login_url = network_site_url( 'wp-login.php', 'login' );
				$message   = sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
				$message  .= __( 'To set your password, visit the following address:' ) . "\r\n\r\n";
				$message  .= $login_url . "?action=rp&key=$key&login=" . rawurlencode( $user->user_login ) . "\r\n";
				wp_mail( $user->user_email, sprintf( __( '[%s] Login Details' ), get_bloginfo( 'name' ) ), $message );
			}
		}
	}
}

