<?php
/**
 * ThessNest — AJAX Internal Messaging System
 *
 * Handles fetching, sending, and marking messages as read for the
 * frontend Dashboard Inbox tab.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

/**
 * Endpoint: Fetch Message Thread
 */
function thessnest_fetch_messages() {
	check_ajax_referer( 'thessnest_dashboard_nonce', 'security' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( 'Authentication required.', 'thessnest' ) ) );
	}

	$current_user_id = get_current_user_id();
	$other_user_id   = isset( $_POST['other_user_id'] ) ? intval( $_POST['other_user_id'] ) : 0;
	$property_id     = isset( $_POST['property_id'] ) ? intval( $_POST['property_id'] ) : 0;

	if ( ! $other_user_id || ! $property_id ) {
		wp_send_json_error( array( 'message' => __( 'Missing thread parameters.', 'thessnest' ) ) );
	}

	// Fetch all messages where current user is sender and other user is recipient OR vice versa
	// AND related to specific property.
	
	// Because WP_Query meta_query OR relations across multiple keys can be complex and slow,
	// we will run a direct SQL query for peak performance on messaging tables, or a simpler WP_Query.
	// For WordPress safety, we use WP_Query with a carefully constructed meta_query.

	$args = array(
		'post_type'      => 'thessnest_message',
		'post_status'    => 'publish',
		'posts_per_page' => 50,
		'order'          => 'ASC',
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key'   => '_property_id',
				'value' => $property_id,
			),
			array(
				'relation' => 'OR',
				array(
					'relation' => 'AND',
					array( 'key' => '_recipient_id', 'value' => $current_user_id ),
					array( 'author' => $other_user_id ), // WP_Query author argument handles post_author gracefully inside meta_query via hook, but safer to use author arg outside. 
				),
				// We actually can't do author logic inside meta_query OR cleanly without custom SQL in WP core.
			)
		)
	);
	
	// A simpler and faster approach for retrieving direct messaging history in WP without custom tables:
	global $wpdb;
	
	// Fetch all messages for this property where user is either sender or recipient, and other user is the opposite.
	$messages_query = $wpdb->prepare( "
		SELECT p.ID, p.post_content, p.post_author, p.post_date
		FROM {$wpdb->posts} p
		INNER JOIN {$wpdb->postmeta} pm_prop ON p.ID = pm_prop.post_id
		INNER JOIN {$wpdb->postmeta} pm_recip ON p.ID = pm_recip.post_id
		WHERE p.post_type = 'thessnest_message'
		AND p.post_status = 'publish'
		AND pm_prop.meta_key = '_property_id' AND pm_prop.meta_value = %d
		AND pm_recip.meta_key = '_recipient_id'
		AND (
			(p.post_author = %d AND pm_recip.meta_value = %d)
			OR
			(p.post_author = %d AND pm_recip.meta_value = %d)
		)
		ORDER BY p.post_date ASC
		LIMIT 100
	", $property_id, $current_user_id, $other_user_id, $other_user_id, $current_user_id );

	$results = $wpdb->get_results( $messages_query );
	
	$formatted_messages = array();
	$current_user_avatar = get_avatar_url( $current_user_id, array( 'size' => 40 ) );
	$other_user_avatar   = get_avatar_url( $other_user_id, array( 'size' => 40 ) );

	if ( $results ) {
		foreach ( $results as $msg ) {
			
			// If recipient is current user, mark as read
			$recipient_id = get_post_meta( $msg->ID, '_recipient_id', true );
			if ( (int) $recipient_id === $current_user_id ) {
				update_post_meta( $msg->ID, '_is_read', 1 );
			}

			$is_sent_by_me = ( (int) $msg->post_author === $current_user_id );

			$formatted_messages[] = array(
				'content'    => wpautop( esc_html( $msg->post_content ) ), // Escaped then formatted
				'date'       => date_i18n( 'M j, g:i a', strtotime( $msg->post_date ) ),
				'is_mine'    => $is_sent_by_me,
				'avatar_url' => $is_sent_by_me ? $current_user_avatar : $other_user_avatar,
			);
		}
	}

	wp_send_json_success( array(
		'messages' => $formatted_messages,
		'property_title' => get_the_title( $property_id )
	) );
}
add_action( 'wp_ajax_thessnest_fetch_messages', 'thessnest_fetch_messages' );


/**
 * Endpoint: Send a Reply Message
 */
function thessnest_send_message() {
	check_ajax_referer( 'thessnest_dashboard_nonce', 'security' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( 'Authentication required.', 'thessnest' ) ) );
	}

	$sender_id     = get_current_user_id();
	$recipient_id  = isset( $_POST['recipient_id'] ) ? intval( $_POST['recipient_id'] ) : 0;
	$property_id   = isset( $_POST['property_id'] ) ? intval( $_POST['property_id'] ) : 0;
	$message_text  = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( ! $recipient_id || ! $property_id || empty( trim( $message_text ) ) ) {
		wp_send_json_error( array( 'message' => __( 'Missing required information.', 'thessnest' ) ) );
	}

	$property = get_post( $property_id );
	$subject  = sprintf( __( 'Re: Inquiry for %s', 'thessnest' ), $property ? $property->post_title : 'Property' );

	$message_id = wp_insert_post( array(
		'post_title'   => wp_strip_all_tags( $subject ),
		'post_content' => wp_kses_post( $message_text ),
		'post_status'  => 'publish',
		'post_author'  => $sender_id,
		'post_type'    => 'thessnest_message',
	) );

	if ( is_wp_error( $message_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Failed to send message.', 'thessnest' ) ) );
	}

	// Save recipient and read status
	update_post_meta( $message_id, '_recipient_id', $recipient_id );
	update_post_meta( $message_id, '_property_id', $property_id );
	update_post_meta( $message_id, '_is_read', 0 ); // Unread by default

	// Optional: Send an email notification to the recipient
	$recipient = get_userdata( $recipient_id );
	if ( $recipient ) {
		$sender = get_userdata( $sender_id );
		$mail_subject = sprintf( __( 'New message from %s regarding %s', 'thessnest' ), $sender->display_name, $property ? $property->post_title : '' );
		$mail_body    = sprintf( __( "You have a new message on ThessNest:\n\n%s\n\nLog in to your dashboard to reply.", 'thessnest' ), $message_text );
		wp_mail( $recipient->user_email, $mail_subject, $mail_body );
	}

	$formatted_msg = array(
		'content'    => wpautop( esc_html( $message_text ) ),
		'date'       => date_i18n( 'M j, g:i a', current_time( 'timestamp' ) ),
		'is_mine'    => true,
		'avatar_url' => get_avatar_url( $sender_id, array( 'size' => 40 ) ),
	);

	wp_send_json_success( array( 
		'message' => __( 'Reply sent.', 'thessnest' ),
		'new_message' => $formatted_msg
	) );
}
add_action( 'wp_ajax_thessnest_send_message', 'thessnest_send_message' );
