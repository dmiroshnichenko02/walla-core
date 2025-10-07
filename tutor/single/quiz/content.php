<?php
/**
 * Quiz content
 *
 * @package Tutor\Templates
 * @subpackage Single\Quiz
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 1.4.3
 */

global $post;

$currentPost = $post; //phpcs:ignore
$quiz_id           = get_the_ID();
$previous_attempts = tutor_utils()->quiz_attempts();
$attempted_count   = is_array( $previous_attempts ) ? count( $previous_attempts ) : 0;
$attempts_allowed  = tutor_utils()->get_quiz_option( $quiz_id, 'attempts_allowed', 0 );
$attempt_remaining = $attempts_allowed - $attempted_count;

// Получаем course_id из квиза
$course_id = tutor_utils()->get_course_id_by( 'quiz', $quiz_id );

if ( 0 !== $attempted_count ) {
	?>
<div id="tutor-quiz-content" class="tutor-quiz-content tutor-quiz-content-<?php the_ID(); ?>">
	<?php
	do_action( 'tutor_quiz/content/before', $quiz_id );

	do_action( 'tutor_quiz/content/after', $quiz_id );
	?>
	<?php tutor_load_template( 'single.common.footer', array( 'course_id' => $course_id ) ); ?>
</div>
<?php } ?>
