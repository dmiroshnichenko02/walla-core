<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'save_post_courses', 'walla_course_update_callback', 10, 2 );
add_action( 'save_post_tutor_course', 'walla_course_update_callback', 10, 2 );

function walla_course_update_callback( $post_id, $post ) {
	if ( ! function_exists( 'tutor' ) ) {
		return;
	}

	$course_post_type = tutor()->course_post_type;
	if ( $post->post_type !== $course_post_type ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	$course_data = walla_collect_course_data( $post_id );
	
	$json_data = json_encode( $course_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
	
	error_log( 'Course Update JSON: ' . $json_data );
	
	walla_send_course_data_to_api( $json_data );
}

function walla_collect_course_data( $course_id ) {
	if ( ! function_exists( 'tutor_utils' ) || ! function_exists( 'tutor' ) ) {
		return array();
	}

	$course = get_post( $course_id );
	if ( ! $course ) {
		return array();
	}

	$lesson_ids = tutor_utils()->get_course_content_ids_by( 
		tutor()->lesson_post_type, 
		tutor()->course_post_type, 
		$course_id 
	);

	$lessons = array();
	foreach ( $lesson_ids as $lesson_id ) {
		$lesson_data = walla_collect_lesson_data( $lesson_id );
		if ( ! empty( $lesson_data ) ) {
			$lessons[] = $lesson_data;
		}
	}

	$course_data = array(
		'course_id'   => (string) $course_id,
		'title'       => get_the_title( $course_id ),
		'description' => wp_strip_all_tags( $course->post_content ),
		'lessons'     => $lessons,
	);

	return $course_data;
}


function walla_collect_lesson_data( $lesson_id ) {
	$lesson = get_post( $lesson_id );
	if ( ! $lesson ) {
		return array();
	}

	$video_meta = get_post_meta( $lesson_id, '_video', true );
	$attachments = get_post_meta( $lesson_id, '_tutor_attachments', true );
	
	$video_text = '';
	if ( function_exists( 'get_field' ) ) {
		$video_text = get_field( 'video_text', $lesson_id );
	} else {
		$video_text = get_post_meta( $lesson_id, 'video_text', true );
	}

	$content_parts = array();
	
	$post_content = wp_strip_all_tags( $lesson->post_content );
	if ( ! empty( $post_content ) ) {
		$content_parts[] = trim( $post_content );
	}

	if ( ! empty( $video_meta ) && is_array( $video_meta ) ) {
		$video_parts = array();
		
		if ( ! empty( $video_meta['source_youtube'] ) ) {
			$video_parts[] = 'YouTube: ' . $video_meta['source_youtube'];
		}
		if ( ! empty( $video_meta['runtime'] ) && is_array( $video_meta['runtime'] ) ) {
			$hours = isset( $video_meta['runtime']['hours'] ) ? $video_meta['runtime']['hours'] : '00';
			$minutes = isset( $video_meta['runtime']['minutes'] ) ? $video_meta['runtime']['minutes'] : '00';
			$seconds = isset( $video_meta['runtime']['seconds'] ) ? $video_meta['runtime']['seconds'] : '00';
			$video_parts[] = 'Duration: ' . $hours . ':' . $minutes . ':' . $seconds;
		}
		if ( ! empty( $video_meta['duration_sec'] ) ) {
			$video_parts[] = 'Duration (seconds): ' . $video_meta['duration_sec'];
		}
		if ( ! empty( $video_meta['playtime'] ) ) {
			$video_parts[] = 'Playtime: ' . $video_meta['playtime'];
		}
		
		if ( ! empty( $video_parts ) ) {
			$content_parts[] = 'Video: ' . implode( ', ', $video_parts );
		}
	}

	if ( ! empty( $attachments ) ) {
		if ( is_array( $attachments ) ) {
			$content_parts[] = 'Attachments: ' . implode( ', ', $attachments );
		} else {
			$content_parts[] = 'Attachments: ' . $attachments;
		}
	}

	if ( ! empty( $video_text ) ) {
		$video_text_clean = wp_strip_all_tags( $video_text );
		if ( ! empty( $video_text_clean ) ) {
			$content_parts[] = 'Video Transcription: ' . trim( $video_text_clean );
		}
	}

	$content = implode( "\n\n", $content_parts );

	return array(
		'lesson_id' => (string) $lesson_id,
		'title'     => get_the_title( $lesson_id ),
		'content'   => $content,
	);
}


function walla_send_course_data_to_api( $json_data ) {
	$api_url = 'https://ai.walla.academy/courses/sync';
	
	$response = wp_remote_post( $api_url, array(
		'method'      => 'POST',
		'timeout'     => 30,
		'headers'     => array(
			'Content-Type' => 'application/json',
		),
		'body'        => $json_data,
		'data_format' => 'body',
	) );
	
	if ( is_wp_error( $response ) ) {
		error_log( 'Course API Error: ' . $response->get_error_message() );
		return false;
	}
	
	$response_code = wp_remote_retrieve_response_code( $response );
	$response_body = wp_remote_retrieve_body( $response );
	
	if ( $response_code >= 200 && $response_code < 300 ) {
		error_log( 'Course API Success: ' . $response_body );
		return true;
	} else {
		error_log( 'Course API Error: HTTP ' . $response_code . ' - ' . $response_body );
		return false;
	}
}


add_action( 'save_post_tutor_lesson', 'walla_trigger_course_update_on_lesson_save', 20, 1 );
add_action( 'save_post_lesson', 'walla_trigger_course_update_on_lesson_save', 20, 1 );

function walla_trigger_course_update_on_lesson_save( $lesson_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $lesson_id ) ) {
		return;
	}

	if ( ! function_exists( 'tutor_utils' ) ) {
		return;
	}

	$course_id = tutor_utils()->get_course_id_by( 'lesson', $lesson_id );
	if ( $course_id ) {
		$course = get_post( $course_id );
		if ( $course ) {
			walla_course_update_callback( $course_id, $course );
		}
	}
}

