<?php

/**
 * AJAX handlers for Tutor LMS custom functionality
 * 
 * @package Walla Theme
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

function walla_tutor_comment_like()
{
	if (!tutor_utils()->is_nonce_verified()) {
		wp_send_json_error('Invalid nonce');
		return;
	}

	$comment_id = intval($_POST['comment_id']);
	$user_id = get_current_user_id();

	if (!$comment_id || !$user_id) {
		wp_send_json_error('Invalid parameters');
		return;
	}

	$liked_comments = get_user_meta($user_id, '_tutor_liked_comments', true);
	if (!is_array($liked_comments)) {
		$liked_comments = array();
	}

	$is_liked = in_array($comment_id, $liked_comments);
	$current_likes = intval(get_comment_meta($comment_id, 'likes_count', true));

	if ($is_liked) {
		$liked_comments = array_diff($liked_comments, array($comment_id));
		$new_likes = max(0, $current_likes - 1);
	} else {
		$liked_comments[] = $comment_id;
		$new_likes = $current_likes + 1;
	}

	update_user_meta($user_id, '_tutor_liked_comments', $liked_comments);

	update_comment_meta($comment_id, 'likes_count', $new_likes);

	wp_send_json_success(array(
		'likes_count' => $new_likes,
		'is_liked' => !$is_liked
	));
}

add_action('wp_ajax_tutor_comment_like', 'walla_tutor_comment_like');
add_action('wp_ajax_nopriv_tutor_comment_like', 'walla_tutor_comment_like');

function walla_student_registration()
{
	if ( ! isset( $_POST['student_registration_nonce'] ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid request', 'walla' ) ) );
	}
	if ( ! check_ajax_referer( 'student_registration_nonce', 'student_registration_nonce', false ) ) {
		wp_send_json_error( array( 'message' => __( 'Security check failed', 'walla' ) ) );
	}
	$first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '';
	$last_name = isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '';
	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$password = isset( $_POST['password'] ) ? (string) $_POST['password'] : '';
	$confirm_password = isset( $_POST['confirm_password'] ) ? (string) $_POST['confirm_password'] : '';
	if ( empty( $first_name ) || empty( $last_name ) || empty( $email ) || empty( $password ) || empty( $confirm_password ) ) {
		wp_send_json_error( array( 'message' => __( 'All fields are required', 'walla' ) ) );
	}
	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid email address', 'walla' ) ) );
	}
	if ( email_exists( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Email already exists', 'walla' ) ) );
	}
	if ( $password !== $confirm_password ) {
		wp_send_json_error( array( 'message' => __( 'Passwords do not match', 'walla' ) ) );
	}
	$base_username = sanitize_user( current( explode( '@', $email ) ), true );
	if ( '' === $base_username ) {
		$base_username = sanitize_user( $first_name . '.' . $last_name, true );
	}
	if ( '' === $base_username ) {
		$base_username = 'user';
	}
	$username = $base_username;
	$counter = 1;
	while ( username_exists( $username ) ) {
		$username = $base_username . $counter;
		$counter++;
	}
	$user_id = wp_insert_user( array(
		'user_login' => $username,
		'user_email' => $email,
		'first_name' => $first_name,
		'last_name' => $last_name,
		'user_pass' => $password,
		'role' => 'subscriber',
	) );
	if ( is_wp_error( $user_id ) ) {
		wp_send_json_error( array( 'message' => $user_id->get_error_message() ) );
	}
	update_user_meta( $user_id, '_is_tutor_student', 1 );
	do_action( 'tutor_after_student_signup', $user_id );
	wp_set_current_user( $user_id );
	wp_set_auth_cookie( $user_id );
	wp_send_json_success( array( 'message' => __( 'Account created successfully', 'walla' ) ) );
}

add_action('wp_ajax_student_registration', 'walla_student_registration');
add_action('wp_ajax_nopriv_student_registration', 'walla_student_registration');

function walla_get_comment_likes_count($comment_id)
{
	$likes_count = get_comment_meta($comment_id, 'likes_count', true);
	return $likes_count ? intval($likes_count) : 0;
}

function walla_user_liked_comment($comment_id)
{
	$user_id = get_current_user_id();
	if (!$user_id) {
		return false;
	}

	$liked_comments = get_user_meta($user_id, '_tutor_liked_comments', true);
	if (!is_array($liked_comments)) {
		return false;
	}

	return in_array($comment_id, $liked_comments);
}
