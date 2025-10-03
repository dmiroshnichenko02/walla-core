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

/**
 * Handle comment likes
 */
function walla_tutor_comment_like()
{
	// Verify nonce
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

	// Check if user already liked this comment
	$liked_comments = get_user_meta($user_id, '_tutor_liked_comments', true);
	if (!is_array($liked_comments)) {
		$liked_comments = array();
	}

	$is_liked = in_array($comment_id, $liked_comments);
	$current_likes = intval(get_comment_meta($comment_id, 'likes_count', true));

	if ($is_liked) {
		// Unlike
		$liked_comments = array_diff($liked_comments, array($comment_id));
		$new_likes = max(0, $current_likes - 1);
	} else {
		// Like
		$liked_comments[] = $comment_id;
		$new_likes = $current_likes + 1;
	}

	// Update user's liked comments
	update_user_meta($user_id, '_tutor_liked_comments', $liked_comments);

	// Update comment likes count
	update_comment_meta($comment_id, 'likes_count', $new_likes);

	wp_send_json_success(array(
		'likes_count' => $new_likes,
		'is_liked' => !$is_liked
	));
}

// Register AJAX actions
add_action('wp_ajax_tutor_comment_like', 'walla_tutor_comment_like');
add_action('wp_ajax_nopriv_tutor_comment_like', 'walla_tutor_comment_like');

/**
 * Get real likes count for a comment
 */
function walla_get_comment_likes_count($comment_id)
{
	$likes_count = get_comment_meta($comment_id, 'likes_count', true);
	return $likes_count ? intval($likes_count) : 0;
}

/**
 * Check if current user liked a comment
 */
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
