<?php
/**
 * Display the content
 *
 * @package Tutor\Templates
 * @subpackage Single\Lesson
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 1.0.0
 */

use TUTOR\User;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;
global $previous_id;
global $next_id;

// Get the ID of this content and the corresponding course.
$course_content_id = get_the_ID();
$course_id         = tutor_utils()->get_course_id_by_subcontent( $course_content_id );

$_is_preview = get_post_meta( $course_content_id, '_is_preview', true );
$content_id  = tutor_utils()->get_post_id( $course_content_id );
$contents    = tutor_utils()->get_course_prev_next_contents_by_id( $content_id );
$previous_id = $contents->previous_id;
$next_id     = $contents->next_id;

$prev_is_preview = get_post_meta( $previous_id, '_is_preview', true );
$next_is_preview = get_post_meta( $next_id, '_is_preview', true );
$is_enrolled     = tutor_utils()->is_enrolled( $course_id );
$is_public       = get_post_meta( $course_id, '_tutor_is_public_course', true ) == 'yes';
if ( ! $is_enrolled) {
    echo '<script>window.location.href = "' . esc_url(get_permalink( $course_id )) . '";</script>';
    die;
}

$prev_is_locked = ! ( $is_enrolled || $prev_is_preview || $is_public );
$next_is_locked = ! ( $is_enrolled || $next_is_preview || $is_public );

$json_data                                 = array();
$json_data['post_id']                      = get_the_ID();
$json_data['best_watch_time']              = 0;
$json_data['autoload_next_course_content'] = (bool) get_tutor_option( 'autoload_next_course_content' );

$best_watch_time = tutor_utils()->get_lesson_reading_info( get_the_ID(), 0, 'video_best_watched_time' );
if ( $best_watch_time > 0 ) {
	$json_data['best_watch_time'] = $best_watch_time;
}

$is_comment_enabled = tutor_utils()->get_option( 'enable_comment_for_lesson' ) && comments_open() && is_user_logged_in();

?>

<?php do_action( 'tutor_lesson/single/before/content' ); ?>

<?php
tutor_load_template(
	'single.common.header',
	array(
		'course_id'        => $course_id,
		'mark_as_complete' => false,
	)
);
?>
<div class="tutor-course-topic-single-body">
	<!-- Load Lesson Video -->
	<?php
		$video_info = tutor_utils()->get_video_info();
		$source_key = is_object( $video_info ) && 'html5' !== $video_info->source ? 'source_' . $video_info->source : null;
		$has_source = ( is_object( $video_info ) && $video_info->source_video_id ) || ( isset( $source_key ) ? $video_info->$source_key : null );
	?>
	<?php
	if ( $has_source ) :
		$completion_mode                              = tutor_utils()->get_option( 'course_completion_process' );
		$json_data['strict_mode']                     = ( 'strict' === $completion_mode );
		$json_data['control_video_lesson_completion'] = (bool) tutor_utils()->get_option( 'control_video_lesson_completion', false );
		$json_data['required_percentage']             = (int) tutor_utils()->get_option( 'required_percentage_to_complete_video_lesson', 80 );
		$json_data['video_duration']                  = $video_info->duration_sec ?? 0;
		$json_data['lesson_completed']                = tutor_utils()->is_completed_lesson( $content_id, get_current_user_id() ) !== false;
		$json_data['is_enrolled']                     = tutor_utils()->is_enrolled( $course_id, get_current_user_id() ) !== false;
		?>
		<input type="hidden" id="tutor_video_tracking_information" value="<?php echo esc_attr( json_encode( $json_data ) ); ?>">
	<?php endif; ?>
	<div class="tutor-video-player-wrapper">
		<?php echo apply_filters( 'tutor_single_lesson_video', tutor_lesson_video( false ), $video_info, $source_key ); //phpcs:ignore ?>
	</div>

	<?php
	$referer_url        = wp_get_referer();
	$referer_comment_id = explode( '#', filter_input( INPUT_SERVER, 'REQUEST_URI' ) ?? '' );
	$url_components     = parse_url( $referer_url );
	$page_tab           = \TUTOR\Input::get( 'page_tab', 'overview' );

	isset( $url_components['query'] ) ? parse_str( $url_components['query'], $output ) : null;

	/**
	 * If lesson has no content, lesson tab will be hidden.
	 * To enable elementor and SCORM, only admin can see lesson tab.
	 *
	 * @since 2.2.2
	 */
	$has_lesson_content = apply_filters(
		'tutor_has_lesson_content',
		User::is_admin() || ! in_array( trim( get_the_content() ), array( null, '', '&nbsp;' ), true ),
		$course_content_id
	);
	

	$has_lesson_attachment = count( tutor_utils()->get_attachments() ) > 0;
	$has_lesson_comment    = (int) get_comments_number( $course_content_id );
	?>

	<style>
		.complete-button {
			width: fit-content;
		}
		.tutor-actual-comment.viewing {
			box-shadow: 0 0 10px #cdcfd5;
			animation: blinkComment 1s infinite;
		}
		@keyframes blinkComment { 50% { box-shadow:0 0 0px #ffffff; }  }
	</style>

    <style>
		.tutor-video-player-wrapper {
			border-radius: 36px;
			overflow: hidden;
		}
		.tutor-lesson-sidebar {
			margin-left: 22px;
		}
		.tutor-course-topic-single-header, .tutor-course-topic-single-footer {
			display: none !important;
		}
		.tutor-course-single-content-wrapper {
			padding: 22px 0 35px 0;
		}
		@media(max-width:1024px){
			.tutor-video-player-wrapper {border-radius: 20px;}
			.info-block .tutor-ul {gap: 15px;}
			.tutor-course-single-content-wrapper .tutor-nav:not(.tutor-nav-pills):not(.tutor-nav-tabs) {border-radius: 12px;}
			.tutor-course-single-content-wrapper .tutor-nav:not(.tutor-nav-pills):not(.tutor-nav-tabs) li a {height: 50px;font-size: 14px;line-height: 1.2;border-radius: 12px !important;}
			.tutor-course-single-content-wrapper .tutor-container .tutor-row p {font-size: 18px;line-height: 26px;}
		}
	 </style>

	<div class="info-block py-[38px] px-4">
		<div class="author">
		<?php
		$course_id = tutor_utils()->get_course_id_by_subcontent( get_the_ID() );
		$instructors = tutor_utils()->get_instructors_by_course( $course_id );
		if ( ! empty( $instructors ) && is_array( $instructors ) ) {
			$author_display_name = $instructors[0]->display_name;
		} else {
			$author_display_name = get_the_author_meta( 'display_name' );
		}
		?>
		<h6 class="font-jost text-black text-[16px] font-normal leading-[24px]">
			<span class="text-[#555555] text-[16px] font-jost leading-[24px]">by</span>
			<?php echo esc_html( $author_display_name ); ?>
		</h6>
		</div>

		<div class="complete-button">
<?php
    $progress = tutor_utils()->get_course_completed_percent($course_id, get_current_user_id(), true);
    $is_completed_course = tutor_utils()->is_completed_course($course_id, get_current_user_id());
    if (isset($progress['completed_percent']) && $progress['completed_percent'] == 100 && ! $is_completed_course) {
?>
    <form method="post">
        <?php wp_nonce_field(tutor()->nonce_action, tutor()->nonce, false); ?>
        <input type="hidden" value="<?php echo esc_attr($course_id); ?>" name="course_id"/>
        <input type="hidden" value="tutor_complete_course" name="tutor_action"/>
        <button type="submit" class="tutor-btn tutor-btn-primary tutor-btn-block" name="complete_course_btn" value="complete_course">
            <?php esc_html_e('Complete Course', 'tutor'); ?>
        </button>
    </form>
<?php
    }
?>
</div>

		<div class="course-name">
			<h2 class="pt-3 pb-4 font-roboto font-bold text-[32px] leading-[40px] text-black"><?php echo esc_html( get_the_title( $course_id ) ); ?></h2>
		</div>
		<div class="info">
		<?php
		if ( function_exists( 'tutor_load_template' ) ) {
			tutor_load_template( 'single.course.course-entry-box' );
		}
		?>
		</div>
		<?php
			$is_completed_lesson = tutor_utils()->is_completed_lesson();

			$video_info = tutor_utils()->get_video_info();
			$source_key = is_object( $video_info ) && 'html5' !== $video_info->source ? 'source_' . $video_info->source : null;
			$has_video  = ( is_object( $video_info ) && $video_info->source_video_id ) || ( isset( $source_key ) ? $video_info->$source_key : null );

			if ( ! $has_video && ! $is_completed_lesson ) : ?>
				<div class="tutor-topbar-complete-btn mt-4">
					<?php if ( ! $is_completed_lesson ) : ?>
						<form method="post">
							<?php wp_nonce_field( tutor()->nonce_action, tutor()->nonce, false ); ?>
							<input type="hidden" value="<?php echo esc_attr( get_the_ID() ); ?>" name="lesson_id" />
							<input type="hidden" value="tutor_complete_lesson" name="tutor_action" />
							<button type="submit" class="tutor-topbar-mark-btn tutor-btn tutor-btn-primary tutor-ws-nowrap"
									name="complete_lesson_btn" value="complete_lesson">
								<span class="tutor-icon-circle-mark-line tutor-mr-8" area-hidden="true"></span>
								<span><?php esc_html_e( 'Mark as Complete', 'tutor' ); ?></span>
							</button>
						</form>
					<?php else : ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
	</div>

	<div class="tutor-course-spotlight-wrapper">
		<ul class="tutor-nav tutor-course-spotlight-nav tutor-justify-center">
			<?php if ( $has_lesson_content && ( $has_lesson_attachment || $is_comment_enabled ) ) : ?>
			<li class="tutor-nav-item">
				<a href="#" class="tutor-nav-link<?php echo 'overview' == $page_tab ? ' is-active' : ''; ?>" data-tutor-nav-target="tutor-course-spotlight-overview" data-tutor-query-variable="page_tab" data-tutor-query-value="overview">
					<span class="tutor-icon-document-text tutor-mr-8" area-hidden="true"></span>
					<span><?php esc_html_e( 'About', 'tutor' ); ?></span>
				</a>
			</li>
			<?php endif; ?>
			
			<?php if ( $has_lesson_attachment && ( $has_lesson_content || $is_comment_enabled ) ) : ?>
			<li class="tutor-nav-item">
				<a href="#" class="tutor-nav-link<?php echo ( 'files' == $page_tab || false === $has_lesson_content ) ? ' is-active' : ''; ?>" data-tutor-nav-target="tutor-course-spotlight-files" data-tutor-query-variable="page_tab" data-tutor-query-value="files">
					<span class="tutor-icon-paperclip tutor-mr-8" area-hidden="true"></span>
					<span><?php esc_html_e( 'Study Materials', 'tutor' ); ?></span>
				</a>
			</li>
			<?php endif; ?>

			<?php if ( $is_comment_enabled && ( $has_lesson_content || $has_lesson_attachment ) ) : ?>
			<li class="tutor-nav-item">
				<a  href="#" 
					class="tutor-nav-link<?php echo ( 'comments' == $page_tab || ( false === $has_lesson_content && false === $has_lesson_attachment ) ) ? ' is-active' : ''; ?>" 
					data-tutor-nav-target="tutor-course-spotlight-comments" data-tutor-query-variable="page_tab" 
					data-tutor-query-value="comments">
					
					<span class="tutor-icon-comment tutor-mr-8" area-hidden="true"></span>
					<span><?php esc_html_e( 'Q&A', 'tutor' ); ?></span>
				</a>
			</li>
			<?php endif; ?>
			<?php if ( true ) : ?>
			<li class="tutor-nav-item">
				<a  href="#" 
					class="tutor-nav-link<?php echo ( 'reviews' == $page_tab || ( false === $has_lesson_content && false === $has_lesson_attachment ) ) ? ' is-active' : ''; ?>" 
					data-tutor-nav-target="tutor-course-spotlight-reviews" data-tutor-query-variable="page_tab" 
					data-tutor-query-value="reviews">
					
					<span class="tutor-icon-star-line tutor-mr-8" area-hidden="true"></span>
					<span><?php esc_html_e( 'Review', 'tutor' ); ?></span>
				</a>
			</li>
			<?php endif; ?>
		</ul>

		<div class="tutor-tab tutor-course-spotlight-tab">
			<?php if ( $has_lesson_content ) : ?>
			<div id="tutor-course-spotlight-overview" class="tutor-tab-item<?php echo 'overview' == $page_tab ? esc_attr( ' is-active' ) : esc_attr( '' ); ?>">
				<div class="tutor-container">
					<div class="tutor-row tutor-justify-center">
						<div class="tutor-col-xl-8">
							<?php do_action( 'tutor_lesson_before_the_content', $post, $course_id ); ?>
							<div class="tutor-fs-6 tutor-color-secondary tutor-lesson-wrapper">
								<?php the_content(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<?php if ( $has_lesson_attachment ) : ?>
			<div id="tutor-course-spotlight-files" class="tutor-tab-item<?php echo esc_attr( ( 'files' == $page_tab || false === $has_lesson_content ) ? ' is-active' : '' ); ?>">
				<div class="tutor-container">
					<div class="tutor-row tutor-justify-center">
						<div class="tutor-col-xl-8">
							<div class="tutor-fs-5 tutor-fw-medium tutor-color-black"><?php esc_html_e( 'Resources', 'tutor' ); ?></div>
							<?php get_tutor_posts_attachments(); ?>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>
			
			<?php if ( $is_comment_enabled ) : ?>
			<div id="tutor-course-spotlight-comments" class="tutor-tab-item<?php echo esc_attr( ( 'comments' == $page_tab || ( false === $has_lesson_content && false === $has_lesson_attachment ) ) ? ' is-active' : '' ); ?>">
				<div class="tutor-container">
					<div class="tutor-course-spotlight-comments">
						<?php require __DIR__ . '/comment.php'; ?>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<div id="tutor-course-spotlight-reviews" class="tutor-tab-item<?php echo esc_attr( ( 'reviews' == $page_tab ) ? ' is-active' : '' ); ?>">
				<div class="tutor-container">
					<div class="tutor-row tutor-justify-center">
						<div class="tutor-col-xl-8">
							<?php tutor_load_template( 'single.course.reviews' ); ?>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>

<?php tutor_load_template( 'single.common.footer', array( 'course_id' => $course_id ) ); ?>

<?php do_action( 'tutor_lesson/single/after/content' ); ?>

<?php
$can_review    = tutor_utils()->has_enrolled_content_access( 'course', $course_id );
$my_rating     = tutor_utils()->get_reviews_by_user( 0, 0, 1, false, $course_id, array( 'approved', 'hold' ) );
$has_my_review = $my_rating ? true : false;
if ( $can_review && ! $has_my_review ) {
    $course_id_for_modal = $course_id;
    ob_start();
    $course_id = $course_id_for_modal;
    $theme_modal = get_stylesheet_directory() . '/tutor/modal/review.php';
    if ( file_exists( $theme_modal ) ) {
        include $theme_modal;
    } else {
        tutor_load_template( 'modal.review', array( 'course_id' => $course_id_for_modal ) );
    }
    $modal_html = ob_get_clean();
    $modal_html = str_replace( 'tutor-modal tutor-is-active', 'tutor-modal', $modal_html );
    echo '<div id="walla-review-modal" style="display:none">' . $modal_html . '</div>'; // phpcs:ignore
}
?>

<!-- <?php if ( $can_review ) : ?>
<script>
(function(){
	const courseId = <?php echo (int) $course_id; ?>;
	const key = 'wallaReviewLastShown:' + courseId;
	const now = Date.now();
	const last = parseInt(localStorage.getItem(key) || '0', 10);
	const thirtyMinutes = 30;
	const hasReview = <?php echo $has_my_review ? 'true' : 'false'; ?>;
	function showModal(){
		const wrap = document.getElementById('walla-review-modal');
		if(!wrap) return;
		wrap.style.display = 'block';
		const form = wrap.querySelector('.tutor-modal');
		if(form) form.classList.add('tutor-is-active');
		localStorage.setItem(key, String(Date.now()));
		const close = wrap.querySelector('.tutor-modal-close-o, .tutor-review-popup-cancel');
		if(close){
			close.addEventListener('click', function(){
				if(form) form.classList.remove('tutor-is-active');
				wrap.style.display = 'none';
			});
		}
	}
	if(!hasReview && now - last >= thirtyMinutes){
		if(document.readyState === 'complete' || document.readyState === 'interactive'){
			setTimeout(showModal, 800);
		} else {
			document.addEventListener('DOMContentLoaded', function(){ setTimeout(showModal, 800); });
		}
	}
})();
</script>
<?php endif; ?> -->

<!-- Bot -->
 <?php

	$bot_logotype = get_field('bot_logotype', 'option');
	$send_message_icon = get_field('send_message_icon', 'option');
	$bot_collapse_timer_message = get_field('bot_collapse_timer_message', 'option');
	$bot_collapse_message_timer = get_field('bot_collapse_message_timer', 'option');
	$bot_title = get_field('bot_title', 'option');
	$input_placeholder = get_field('input_placeholder', 'option');
	$welcome_message = get_field('welcome_message', 'option');
	$welcome_message_label = get_field('welcome_message_label', 'option');
	$color_palette = get_field('color_palette', 'option');
	// Palette Items:
	$collapse_icon_background = $color_palette['collapse_icon_background'];
	$chat_background = $color_palette['chat_background'];
	$chat_bot_icon_background = $color_palette['chat_bot_icon_background'];
	$message_background = $color_palette['message_background'];
	$message_text_color = $color_palette['message_text_color'];
	$label_color = $color_palette['label_color'];
	$chat_message_block_background = $color_palette['chat_message_block_background'];
	$input_background = $color_palette['input_background'];
	$send_button_background = $color_palette['send_button_background'];
	$input_placeholder_color = $color_palette['input_placeholder_color'];
	$input_text_color = $color_palette['input_text_color'];

 ?>

 <div class="bot relative z-9999">
	<div class="chat-overlay" style="display: none;"></div>
	<div class="collapse-icon w-[84px] h-[84px] bg-[<?php echo $collapse_icon_background ?>] rounded-full fixed bottom-[100px] right-[50px] flex items-center justify-center cursor-pointer">
		<img class="max-w-[50px] max-h-[50px]" src="<?php echo $bot_logotype; ?>" alt="logotype ai">
	</div>
	<div  class="collapse-message fixed bottom-[185px] right-[135px] " style="display: none; opacity: 0;">
		<div class="relative">
			<div style="box-shadow: 0px 4px 44px 0px rgba(0,0,0,0.14);" class="w-full h-full z-20 bg-white p-5 rounded-[10px] rounded-br-[0px]">
				<h6 class="font-inter text-black text-[18px] leading-[18px] font-normal"><?php echo $bot_collapse_timer_message; ?></h6>
			</div>
			<div class="absolute -right-[10px] w-[34px] h-[54px] -bottom-[30px] block" aria-hidden="true">
				<svg width="37" height="36" viewBox="0 0 37 36" fill="none" xmlns="http://www.w3.org/2000/svg" style="filter: drop-shadow(0px 4px 44px rgba(0,0,0,0.14));">
					<path d="M36.0309 32.1077C36.8889 33.7654 35.2095 35.5908 33.4862 34.8735L1.23192 21.448C-0.0499006 20.9144 -0.411414 19.2691 0.528674 18.2474L16.7231 0.646046C17.6632 -0.375708 19.3329 -0.152192 19.9711 1.08085L36.0309 32.1077Z" fill="white"/>
				</svg>
			</div>
		</div>
	</div>

	<div class="chat p-5 bg-[<?php echo $chat_background ?>] w-full md:w-[502px] min-h-[70vh] md:min-h-[614px] rounded-[32px] flex flex-col fixed bottom-[20px] right-[40px]" style="display: none; opacity: 0;">
		<div class="collapsed block mx-auto bg-white h-[4px] w-[64px] min-h-[4px] min-w-[64px] cursor-pointer mb-5"></div>
		<div class="head-bot flex gap-5 mb-8 items-center">
			<div class="w-[54px] h-[54px] flex justify-center items-center rounded-full bg-[<?php echo $chat_bot_icon_background ?>]">
				<img class="max-w-[24px] max-h-[24px]" src="<?php echo $bot_logotype; ?>" alt="logotype ai">
			</div>
			<h4 class="font-inter font-medium text-[28px] text-white leading-[40px]"><?php echo $bot_title; ?></h4>
		</div>
		<div class="body-ai bg-[<?php echo $chat_message_block_background ?>] h-[100%] w-full rounded-[21px] flex flex-col py-6 px-4 gap-[24px]">
		<div class="messages h-[40vh] md:h-[347px] w-full overflow-y-auto flex flex-col gap-4"></div>
		<div class="ai-typing-indicator pl-4 text-[<?php echo $label_color; ?>] text-[12px] leading-[28px] font-inter font-medium" style="display: none;">
			AI typing
			<span class="typing-dots">
				<span class="dot dot-1">.</span>
				<span class="dot dot-2">.</span>
				<span class="dot dot-3">.</span>
			</span>
		</div>
		<form class="send-message-ai flex items-center gap-[10px]">
			<input  class="w-full py-6 px-5 rounded-[48px] font-inter text-[14px] leading-[21px] bg-[<?php echo $input_background; ?>] text-[<?php echo $input_text_color ?>]" type="text" placeholder="<?php echo $input_placeholder ?>">
			<button class="bg-[<?php echo $send_button_background ?>] rounded-full flex justify-center items-center w-[68px] h-[68px] min-w-[68px] min-h-[68px]">
				<img src="<?php echo $send_message_icon ?>" alt="send">
			</button>
		</form>
		</div>
	</div>
 </div>

 <style>
	.send-message-ai input:placeholder {
		color: <?php echo $input_placeholder_color ?>;
	}
	.send-message-ai input {
		outline: none;
		border: none;
	}
	.collapse-message {
		transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out;
		transform: translateY(10px);
	}
	.collapse-message.show {
		opacity: 1 !important;
		transform: translateY(0);
	}
	.collapse-message.hide {
		opacity: 0 !important;
		transform: translateY(10px);
	}
	.chat {
		transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out;
		transform: translateY(20px);
	}
	.chat.show {
		opacity: 1 !important;
		transform: translateY(0);
	}
	.chat.hide {
		opacity: 0 !important;
		transform: translateY(20px);
	}
	.collapse-icon {
		transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out;
	}
	.collapse-icon.hide {
		opacity: 0 !important;
		transform: scale(0.8);
		pointer-events: none;
	}
	.collapse-icon.show {
		opacity: 1 !important;
		transform: scale(1);
		pointer-events: auto;
	}
	.typing-dots {
		display: inline-block;
		margin-left: 4px;
		position: relative;
		height: 18px;
		vertical-align: middle;
	}
	.typing-dots .dot {
		display: inline-block;
		position: relative;
		animation: typing-dot-jump 1.2s infinite cubic-bezier(0.4, 0, 0.2, 1);
	}
	.typing-dots .dot-1 {
		animation-delay: 0s;
	}
	.typing-dots .dot-2 {
		animation-delay: 0.4s;
	}
	.typing-dots .dot-3 {
		animation-delay: 0.8s;
	}
	@keyframes typing-dot-jump {
		0%, 80%, 100% {
			transform: translateY(0);
		}
		40% {
			transform: translateY(-14px);
		}
	}
	.message-text {
		white-space: pre-wrap;
		word-wrap: break-word;
	}
	.body-ai {
		position: relative;
	}
	.ai-typing-indicator {
		position: absolute;
		bottom: 90px;
		left: 16px;
		right: 16px;
		margin-bottom: 0;
	}
	.chat-overlay {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background-color: rgba(0, 0, 0, 0.5);
		z-index: 9998;
		opacity: 0;
		transition: opacity 0.5s ease-in-out;
		pointer-events: none;
	}
	.chat-overlay.show {
		opacity: 1;
		pointer-events: auto;
	}
	@media (max-width: 767px) {
		.chat {
			width: 100% !important;
			max-width: 100% !important;
			height: 70vh !important;
			min-height: 70vh !important;
			max-height: 70vh !important;
			bottom: 0 !important;
			left: 0 !important;
			right: 0 !important;
			border-radius: 32px 32px 0 0 !important;
			z-index: 9999 !important;
			transform: translateZ(0);
			-webkit-transform: translateZ(0);
			will-change: transform;
		}
		.chat-overlay {
			display: block !important;
		}
		.tutor-nav.tutor-course-spotlight-nav.tutor-justify-center {
			flex-wrap: nowrap !important;
			overflow-x: auto !important;
			overflow-y: hidden !important;
		}
	}
 </style>

 <script>
 document.addEventListener("DOMContentLoaded", e => {
	const collapseIcon = document.querySelector('.collapse-icon');
	const collapseMessage = document.querySelector('.collapse-message');
	const chat = document.querySelector('.chat');
	const chatOverlay = document.querySelector('.chat-overlay');
	const collapsed = document.querySelector('.collapsed');
	const messagesContainer = document.querySelector('.messages');
	const typingIndicator = document.querySelector('.ai-typing-indicator');
	const form = document.querySelector('.send-message-ai');
	const input = form ? form.querySelector('input') : null;
	
	function isMobile() {
		return window.innerWidth < 768;
	}
	
	let messageTimer = null;
	let hideTimer = null;
	let isChatOpen = false;
	let isTimerActive = false;
	let dateUpdateInterval = null;
	let chatOpenedTime = null;
	let chatHistory = [];
	let lastRenderedIndex = -1;
	
	const hasTimer = <?php echo $bot_collapse_message_timer ? 'true' : 'false'; ?>;
	let intervalMs = 0;
	const hideDelayMs = 10000;
	
	<?php if ( $bot_collapse_message_timer ) : ?>
	const timerValue = '<?php echo esc_js( $bot_collapse_message_timer ); ?>';
	
	if (timerValue && timerValue.trim()) {
		const parts = timerValue.trim().split(':');
		if (parts.length === 2) {
			const minutes = parseInt(parts[0].trim(), 10) || 0;
			const seconds = parseInt(parts[1].trim(), 10) || 0;
			intervalMs = (minutes * 60 + seconds) * 1000;
		} else if (parts.length === 1) {
			intervalMs = parseInt(parts[0].trim(), 10) * 1000 || 0;
		}
	}
	
	if (intervalMs < 10000) {
		intervalMs = 10000;
	}
	<?php endif; ?>
	
	function stopMessageTimer() {
		if (messageTimer) {
			clearTimeout(messageTimer);
			messageTimer = null;
		}
		if (hideTimer) {
			clearTimeout(hideTimer);
			hideTimer = null;
		}
		isTimerActive = false;
	}
	
	function hideCollapseMessage() {
		if (!collapseMessage) return;
		
		collapseMessage.classList.remove('show');
		collapseMessage.classList.add('hide');
		
		setTimeout(function() {
			if (collapseMessage.classList.contains('hide')) {
				collapseMessage.style.display = 'none';
			}
		}, 500);
	}
	
	function showCollapseMessage() {
		if (!collapseMessage || isChatOpen) return;
		
		collapseMessage.style.display = 'block';
		requestAnimationFrame(function() {
			setTimeout(function() {
				collapseMessage.classList.remove('hide');
				collapseMessage.classList.add('show');
			}, 10);
		});
		
		hideTimer = setTimeout(function() {
			hideCollapseMessage();
			
			if (isTimerActive && !isChatOpen) {
				messageTimer = setTimeout(showCollapseMessage, intervalMs - hideDelayMs);
			}
		}, hideDelayMs);
	}
	
	function startMessageTimer() {
		if (!hasTimer || isChatOpen || isTimerActive) return;
		
		isTimerActive = true;
		messageTimer = setTimeout(showCollapseMessage, intervalMs);
	}
	
	if (hasTimer) {
		startMessageTimer();
	}
	
	function formatTime(date) {
		const now = new Date();
		const diff = Math.floor((now - date) / 1000);
		
		if (diff < 60) {
			return 'Just Now';
		}
		
		const minutes = Math.floor(diff / 60);
		if (minutes < 60) {
			return minutes === 1 ? '1 minute ago' : `${minutes} minutes ago`;
		}
		
		const hours = Math.floor(minutes / 60);
		if (hours < 24) {
			return hours === 1 ? '1 hour ago' : `${hours} hours ago`;
		}
		
		const days = Math.floor(hours / 24);
		if (days < 7) {
			return days === 1 ? '1 day ago' : `${days} days ago`;
		}
		
		const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
		return `${months[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
	}
	
	function updateDate() {
		if (!messagesContainer) return;
		
		const allMessages = messagesContainer.querySelectorAll('.one-message');
		
		allMessages.forEach(function(msgEl) {
			const dateAttr = msgEl.getAttribute('data-time');
			if (!dateAttr) return;
			
			const msgDate = new Date(dateAttr);
			if (isNaN(msgDate.getTime())) return;
			
			const dateEl = msgEl.querySelector('.date-m');
			if (dateEl) {
				const isBotMessage = msgEl.classList.contains('ai-m');
				if (isBotMessage && chatHistory.length > 0 && chatHistory[0].role === 'bot' && chatHistory[0].date && chatHistory[0].date.getTime() === msgDate.getTime()) {
					if (chatOpenedTime) {
						dateEl.textContent = ' ' + formatTime(chatOpenedTime);
					} else {
						dateEl.textContent = ' ' + formatTime(msgDate);
					}
				} else {
					dateEl.textContent = ' ' + formatTime(msgDate);
				}
			}
		});
	}
	
	function startDateUpdater() {
		if (dateUpdateInterval) {
			clearInterval(dateUpdateInterval);
		}
		updateDate();
		dateUpdateInterval = setInterval(updateDate, 5000);
	}
	
	function stopDateUpdater() {
		if (dateUpdateInterval) {
			clearInterval(dateUpdateInterval);
			dateUpdateInterval = null;
		}
	}
	
	function scrollToBottom() {
		if (messagesContainer) {
			requestAnimationFrame(() => {
				messagesContainer.scrollTop = messagesContainer.scrollHeight;
			});
		}
	}
	
	function renderMessages() {
		if (!messagesContainer) return;
		
		for (let i = lastRenderedIndex + 1; i < chatHistory.length; i++) {
			const msg = chatHistory[i];
			const msgEl = document.createElement('div');
			msgEl.className = `one-message ${msg.role === 'bot' ? 'ai-m' : 'user-m'} text-[<?php echo $message_text_color; ?>]`;
			msgEl.setAttribute('data-time', msg.date.toISOString());
			
			const boxDiv = document.createElement('div');
			boxDiv.className = `box p-5 rounded-[21px] ${msg.role === 'bot' ? 'rounded-tl-none' : 'rounded-tr-none'} bg-[<?php echo $message_background; ?>]`;
			
			const msgContent = document.createElement('div');
			msgContent.className = 'message-text font-inter text-white text-[18px] leading-[28px] font-medium';
			msgContent.innerHTML = msg.html;
			boxDiv.appendChild(msgContent);
			msgEl.appendChild(boxDiv);
			
			const labelDiv = document.createElement('div');
			labelDiv.className = 'label-m mt-[6px] pl-4 text-[<?php echo $label_color; ?>] text-[12px] leading-[28px] font-inter font-medium';
			if (msg.role === 'bot') {
				labelDiv.innerHTML = `<?php echo $welcome_message_label; ?> <span class="date-m"></span>`;
			} else {
				labelDiv.innerHTML = 'You ~ <span class="date-m"></span>';
			}
			msgEl.appendChild(labelDiv);
			
			messagesContainer.appendChild(msgEl);
			lastRenderedIndex = i;
			
			if (msg.role === 'bot' && msg.date) {
				const dateEl = msgEl.querySelector('.date-m');
				if (dateEl) {
					if (i === 0 && !chatOpenedTime) {
						dateEl.textContent = ' Just Now';
						chatOpenedTime = msg.date;
						startDateUpdater();
					} else {
						dateEl.textContent = ' ' + formatTime(msg.date);
					}
				}
			} else if (msg.role === 'user' && msg.date) {
				const dateEl = msgEl.querySelector('.date-m');
				if (dateEl) {
					dateEl.textContent = ' ' + formatTime(msg.date);
				}
			}
		}
		
		scrollToBottom();
	}
	
	function typeMessageHTML(html, messageIndex, callback) {
		if (!messagesContainer || messageIndex < 0 || messageIndex >= chatHistory.length) return;
		
		const msgElements = messagesContainer.querySelectorAll('.one-message');
		if (!msgElements[messageIndex]) return;
		
		const element = msgElements[messageIndex].querySelector('.message-text');
		if (!element) return;
		
		let index = 0;
		const typingSpeed = 30;
		let currentHTML = '';
		
		function type() {
			if (index < html.length) {
				currentHTML += html.charAt(index);
				element.innerHTML = currentHTML;
				index++;
				setTimeout(type, typingSpeed);
			} else {
				if (callback) callback();
			}
		}
		
		type();
	}
	
	function showWelcomeMessage() {
		if (!typingIndicator || !messagesContainer) return;
		
		const welcomeHTML = <?php echo json_encode( $welcome_message ); ?>;
		const welcomeDate = new Date();
		
		typingIndicator.style.display = 'block';
		
		setTimeout(function() {
			typingIndicator.style.display = 'none';
			
			chatHistory.push({
				role: 'bot',
				html: '',
				date: welcomeDate
			});
			
			renderMessages();
			
			const messageIndex = chatHistory.length - 1;
			typeMessageHTML(welcomeHTML, messageIndex, function() {
				chatHistory[messageIndex].html = welcomeHTML;
			});
		}, 1500);
	}
	
	function openChat() {
		if (isChatOpen) return;
		
		isChatOpen = true;
		
		if (hasTimer) {
			stopMessageTimer();
			hideCollapseMessage();
		}
		
		if (collapseIcon) {
			collapseIcon.classList.remove('show');
			collapseIcon.classList.add('hide');
		}
		
		if (!chat) return;
		
		if (isMobile() && chatOverlay) {
			chatOverlay.style.display = 'block';
			requestAnimationFrame(function() {
				setTimeout(function() {
					chatOverlay.classList.add('show');
				}, 10);
			});
		}
		
		chat.style.display = 'flex';
		requestAnimationFrame(function() {
			setTimeout(function() {
				chat.classList.remove('hide');
				chat.classList.add('show');
				
				if (chatHistory.length === 0) {
					setTimeout(showWelcomeMessage, 300);
				} else {
					if (messagesContainer) {
						messagesContainer.innerHTML = '';
					}
					lastRenderedIndex = -1;
					renderMessages();
					if (chatOpenedTime) {
						startDateUpdater();
					}
				}
			}, 10);
		});
	}
	
	function closeChat() {
		if (!isChatOpen) return;
		
		isChatOpen = false;
		
		stopDateUpdater();
		
		if (!chat) return;
		
		if (chatOverlay) {
			chatOverlay.classList.remove('show');
			setTimeout(function() {
				if (!chatOverlay.classList.contains('show')) {
					chatOverlay.style.display = 'none';
				}
			}, 500);
		}
		
		chat.classList.remove('show');
		chat.classList.add('hide');
		
		setTimeout(function() {
			if (chat.classList.contains('hide')) {
				chat.style.display = 'none';
			}
			
			if (collapseIcon) {
				collapseIcon.classList.remove('hide');
				collapseIcon.classList.add('show');
			}
			
			if (typingIndicator) {
				typingIndicator.style.display = 'none';
			}
			
			if (hasTimer) {
				startMessageTimer();
			}
		}, 500);
	}
	
	if (collapseIcon) {
		collapseIcon.addEventListener('click', openChat);
	}
	
	if (collapseMessage) {
		collapseMessage.addEventListener('click', openChat);
	}
	
	if (collapsed) {
		collapsed.addEventListener('click', closeChat);
	}
	
	if (chatOverlay) {
		chatOverlay.addEventListener('click', function(e) {
			if (isMobile() && isChatOpen) {
				closeChat();
			}
		});
	}
	
	if (form && input) {
		form.addEventListener('submit', function(e) {
			e.preventDefault();
			const message = input.value.trim();
			if (!message) return;
			
			const userMessage = message.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>');
			
			chatHistory.push({
				role: 'user',
				html: userMessage,
				date: new Date()
			});
			
			input.value = '';
			renderMessages();
		});
		
		input.addEventListener('keydown', function(e) {
			if (e.key === 'Enter' && !e.shiftKey) {
				e.preventDefault();
				form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
			}
		});
	}
	
	document.addEventListener('click', function(e) {
		if (!isChatOpen) return;
		
		const clickedInsideChat = chat && chat.contains(e.target);
		const clickedOnCollapseIcon = collapseIcon && collapseIcon.contains(e.target);
		const clickedOnCollapseMessage = collapseMessage && collapseMessage.contains(e.target);
		const clickedOnOverlay = chatOverlay && chatOverlay.contains(e.target);
		
		if (isMobile() && clickedOnOverlay) {
			return;
		}
		
		if (!clickedInsideChat && !clickedOnCollapseIcon && !clickedOnCollapseMessage) {
			closeChat();
		}
	});
 })
 </script>