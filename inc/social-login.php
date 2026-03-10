<?php
/**
 * ThessNest — Single-Click Social Login
 *
 * Injects Google and Facebook login buttons into the default WordPress login/register forms.
 * Provides the callback endpoints for the OAuth flow.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

class ThessNest_Social_Login {

	public function __construct() {
		// Inject buttons into the WP Login & Register forms
		add_action( 'login_form', [ $this, 'render_social_buttons' ] );
		add_action( 'register_form', [ $this, 'render_social_buttons' ] );

		// Custom styling for the wp-login.php page to make it match the premium theme
		add_action( 'login_enqueue_scripts', [ $this, 'custom_login_styles' ] );

		// Handle the OAuth Redirection Endpoints
		add_action( 'init', [ $this, 'handle_oauth_callbacks' ] );
	}

	/**
	 * Append Social Login Buttons
	 */
	public function render_social_buttons() {
		$google_url = site_url( '?thessnest_oauth=google' );
		$facebook_url = site_url( '?thessnest_oauth=facebook' );
		?>
		<div class="thessnest-social-login" style="margin-top:20px;text-align:center;">
			<div class="social-separator" style="display:flex;align-items:center;text-align:center;color:#94a3b8;font-size:12px;margin:20px 0;">
				<hr style="flex:1;border:none;border-top:1px solid #e2e8f0;">
				<span style="padding:0 10px;"><?php esc_html_e( 'Or continue with', 'thessnest' ); ?></span>
				<hr style="flex:1;border:none;border-top:1px solid #e2e8f0;">
			</div>
			
			<div style="display:flex;gap:10px;flex-direction:column;">
				<!-- Google Button -->
				<a href="<?php echo esc_url( $google_url ); ?>" class="btn-social-login" style="display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:10px;background:#fff;color:#334155;border:1px solid #cbd5e1;border-radius:6px;text-decoration:none;font-weight:600;transition:all 0.2s;">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20px" height="20px"><path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/><path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/><path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/><path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/></svg>
					<?php esc_html_e( 'Continue with Google', 'thessnest' ); ?>
				</a>
				
				<!-- Facebook Button -->
				<a href="<?php echo esc_url( $facebook_url ); ?>" class="btn-social-login" style="display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:10px;background:#1877F2;color:#fff;border:1px solid #1877F2;border-radius:6px;text-decoration:none;font-weight:600;transition:all 0.2s;">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20px" height="20px" fill="#fff"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
					<?php esc_html_e( 'Continue with Facebook', 'thessnest' ); ?>
				</a>
			</div>
			
			<script>
				document.querySelectorAll('.btn-social-login').forEach(btn => {
					btn.addEventListener('mouseover', () => btn.style.transform = 'translateY(-1px)');
					btn.addEventListener('mouseout', () => btn.style.transform = 'translateY(0)');
				});
			</script>
		</div>
		<?php
	}

	/**
	 * Inject custom CSS to make the standard WP Login look like ThessNest Premium
	 */
	public function custom_login_styles() {
		echo '<style type="text/css">
			body.login { background: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
			body.login h1 a { background-image: none, url("' . esc_url( get_theme_file_uri( 'assets/images/logo-dark.svg' ) ) . '"); background-size: contain; width: 100%; height: 50px; }
			#login { padding: 8% 0 0; }
			.login form { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); padding: 32px; }
			.login label { color: #334155; font-weight: 600; font-size: 14px; }
			.login input[type="text"], .login input[type="password"] { border-radius: 6px; border: 1px solid #cbd5e1; padding: 10px; font-size: 16px; width: 100%; box-shadow: none; }
			.login input[type="text"]:focus, .login input[type="password"]:focus { border-color: #2563eb; outline: none; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
			.wp-core-ui .button-primary { background: #2563eb !important; border-color: #2563eb !important; color: #fff !important; text-shadow: none !important; box-shadow: none !important; border-radius: 6px !important; padding: 6px 20px !important; font-size: 15px !important; height: auto !important; line-height: 1.5 !important; }
			.wp-core-ui .button-primary:hover { background: #1d4ed8 !important; border-color: #1d4ed8 !important; }
			.login #nav a, .login #backtoblog a { color: #64748b; }
			.login #nav a:hover, .login #backtoblog a:hover { color: #2563eb; }
		</style>';
	}

	/**
	 * Endpoint Interceptor for OAuth Callbacks
	 */
	public function handle_oauth_callbacks() {
		if ( isset( $_GET['thessnest_oauth'] ) && ! is_user_logged_in() ) {
			$provider = sanitize_text_field( $_GET['thessnest_oauth'] );
			
			// For Phase 3, this is an architectural mock indicating where 
			// the Social Login library (like Nextend Social Login or HybridAuth) hooks in.
			if ( in_array( $provider, ['google', 'facebook'] ) ) {
				$msg = urlencode( __( 'Social Login API Keys not configured yet. Please configure them in Theme Settings to enable single-click authentication.', 'thessnest' ) );
				wp_safe_redirect( wp_login_url() . '?login_error=' . $msg );
				exit;
			}
		}

		// Inject custom error message into login screen if redirected from above
		if ( isset( $_GET['login_error'] ) && ! empty( $_GET['login_error'] ) ) {
			add_filter( 'login_message', function( $message ) {
				return '<div id="login_error" style="border-left-color: #f59e0b;">' . esc_html( wp_unslash( sanitize_text_field( $_GET['login_error'] ) ) ) . '</div>';
			});
		}
	}
}

new ThessNest_Social_Login();
