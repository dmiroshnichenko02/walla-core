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

if ( ! $_is_preview && ! $is_enrolled ) {
    $theme_modal = get_stylesheet_directory() . '/tutor/modal/enroll-required.php';
    if ( file_exists( $theme_modal ) ) {
        include $theme_modal;
    }
    return;
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

$is_comment_enabled = tutor_utils()->get_option( 'enable_comment_for_lesson' ) && comments_open() && ( is_user_logged_in() || $_is_preview );

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
		if ( function_exists( 'get_field' ) && $course_id ) {
			$course_includes = get_field( 'course_includes', $course_id );
			
			if ( $course_includes && is_array( $course_includes ) ) {
				?>
				<div class="course-includes-info" style="display: flex; flex-wrap: wrap; gap: 20px; align-items: center;">
					<?php foreach ( $course_includes as $index => $item ) : 
						$icon = isset( $item['icon'] ) ? $item['icon'] : '';
						$text = isset( $item['text'] ) ? $item['text'] : '';
						
						if ( ! empty( $icon ) || ! empty( $text ) ) :
					?>
						<div class="course-includes-item" style="display: flex; align-items: center; gap: 8px;">
							<?php if ( ! empty( $icon ) ) : ?>
								<img src="<?php echo esc_url( $icon ); ?>" alt="" style="width: 20px; height: 20px; object-fit: contain; flex-shrink: 0;" />
							<?php endif; ?>
							<?php if ( ! empty( $text ) ) : ?>
								<span style="font-size: 16px; line-height: 24px; color: #555555; font-family: 'Jost', sans-serif;"><?php echo esc_html( $text ); ?></span>
							<?php endif; ?>
						</div>
					<?php 
						endif;
					endforeach; 
					?>
				</div>
				<?php
			}
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
			<li class="tutor-nav-item">
				<a href="#" class="tutor-nav-link<?php echo 'overview' == $page_tab || empty( $page_tab ) ? ' is-active' : ''; ?>" data-tutor-nav-target="tutor-course-spotlight-overview" data-tutor-query-variable="page_tab" data-tutor-query-value="overview">
					<span class="tutor-icon-document-text tutor-mr-8" area-hidden="true"></span>
					<span><?php esc_html_e( 'About', 'tutor' ); ?></span>
				</a>
			</li>
			
			<li class="tutor-nav-item">
				<a href="#" class="tutor-nav-link<?php echo 'files' == $page_tab ? ' is-active' : ''; ?>" data-tutor-nav-target="tutor-course-spotlight-files" data-tutor-query-variable="page_tab" data-tutor-query-value="files">
					<span class="tutor-icon-paperclip tutor-mr-8" area-hidden="true"></span>
					<span><?php esc_html_e( 'Study Materials', 'tutor' ); ?></span>
				</a>
			</li>

			<?php if ( $is_comment_enabled ) : ?>
			<li class="tutor-nav-item">
				<a  href="#" 
					class="tutor-nav-link<?php echo 'comments' == $page_tab ? ' is-active' : ''; ?>" 
					data-tutor-nav-target="tutor-course-spotlight-comments" data-tutor-query-variable="page_tab" 
					data-tutor-query-value="comments">
					
					<span class="tutor-icon-comment tutor-mr-8" area-hidden="true"></span>
					<span><?php esc_html_e( 'Q&A', 'tutor' ); ?></span>
				</a>
			</li>
			<?php endif; ?>
			
			<li class="tutor-nav-item">
				<a  href="#" 
					class="tutor-nav-link<?php echo 'reviews' == $page_tab ? ' is-active' : ''; ?>" 
					data-tutor-nav-target="tutor-course-spotlight-reviews" data-tutor-query-variable="page_tab" 
					data-tutor-query-value="reviews">
					
					<span class="tutor-icon-star-line tutor-mr-8" area-hidden="true"></span>
					<span><?php esc_html_e( 'Review', 'tutor' ); ?></span>
				</a>
			</li>
		</ul>

		<div class="tutor-tab tutor-course-spotlight-tab">
			<div id="tutor-course-spotlight-overview" class="tutor-tab-item<?php echo 'overview' == $page_tab || empty( $page_tab ) ? esc_attr( ' is-active' ) : esc_attr( '' ); ?>">
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

			<div id="tutor-course-spotlight-files" class="tutor-tab-item<?php echo esc_attr( 'files' == $page_tab ? ' is-active' : '' ); ?>">
				<div class="tutor-container">
					<div class="tutor-row tutor-justify-center">
						<div class="tutor-col-xl-8">
							<div class="tutor-fs-5 tutor-fw-medium tutor-color-black"><?php esc_html_e( 'Resources', 'tutor' ); ?></div>
							<?php get_tutor_posts_attachments(); ?>
						</div>
					</div>
				</div>
			</div>
			
			<?php if ( $is_comment_enabled ) : ?>
			<div id="tutor-course-spotlight-comments" class="tutor-tab-item<?php echo esc_attr( 'comments' == $page_tab ? ' is-active' : '' ); ?>">
				<div class="tutor-container">
					<div class="tutor-course-spotlight-comments">
						<?php require __DIR__ . '/comment.php'; ?>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<div id="tutor-course-spotlight-reviews" class="tutor-tab-item<?php echo esc_attr( 'reviews' == $page_tab ? ' is-active' : '' ); ?>">
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
	$bot_name = get_field('bot_name', 'option');
	$bot_subtitle = get_field('bot_subtitle', 'option');
	$welcome_message = get_field('welcome_message', 'option');
	$welcome_message_label = get_field('welcome_message_label', 'option');
	$input_placeholder = get_field('input_placeholder', 'option');
	$send_message_icon = get_field('send_message_icon', 'option');
	$color_palette = get_field('color_palette', 'option');
	
	// Color Palette Items:
	$collapse_icon_background = $color_palette['collapse_icon_background'] ?? '#1F2937';
	$chat_background = $color_palette['chat_background'] ?? '#1F2937';
	$header_background = $color_palette['header_background'] ?? '#111827';
	$header_text_color = $color_palette['header_text_color'] ?? '#FFFFFF';
	$header_subtitle_color = $color_palette['header_subtitle_color'] ?? '#9CA3AF';
	$message_background = $color_palette['message_background'] ?? '#374151';
	$user_message_background = $color_palette['user_message_background'] ?? '#4F46E5';
	$message_text_color = $color_palette['message_text_color'] ?? '#FFFFFF';
	$label_color = $color_palette['label_color'] ?? '#9CA3AF';
	$input_background = $color_palette['input_background'] ?? '#374151';
	$input_text_color = $color_palette['input_text_color'] ?? '#FFFFFF';
	$input_placeholder_color = $color_palette['input_placeholder_color'] ?? '#9CA3AF';
	$send_button_background = $color_palette['send_button_background'] ?? '#4F46E5';
	$close_button_color = $color_palette['close_button_color'] ?? '#9CA3AF';
	$back_button_color = $color_palette['back_button_color'] ?? '#9CA3AF';
?>

<div class="walla-bot-wrapper relative z-[99999]">
	<div class="walla-collapse-icon fixed bottom-[20px] right-[20px] w-[60px] h-[60px] rounded-full flex items-center justify-center cursor-pointer transition-all duration-300 hover:scale-110" style="background-color: <?php echo esc_attr($collapse_icon_background); ?>; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
		<img class="walla-collapse-icon-img max-w-[32px] max-h-[32px]" src="<?php echo esc_url($bot_logotype); ?>" alt="Bot">
		<svg class="walla-collapse-icon-arrow hidden max-w-[32px] max-h-[32px]" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: black;">
			<path d="M8 12L16 20L24 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>
	</div>

	<div class="walla-chat fixed bottom-[130px] right-[20px] w-full max-w-[420px] rounded-[20px] overflow-hidden transition-all duration-300 opacity-0 translate-y-5 pointer-events-none" style="background-color: <?php echo esc_attr($chat_background); ?>; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
		<div class="walla-chat-header flex items-center justify-between px-4 py-3" style="background-color: <?php echo esc_attr($header_background); ?>;">
			<div class="flex items-center gap-3 flex-1 min-w-0">
				<button class="walla-back-btn flex-shrink-0 p-1 cursor-pointer transition-opacity hover:opacity-70" style="color: <?php echo esc_attr($back_button_color); ?>;">
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M12.5 15L7.5 10L12.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>
				<div class="flex items-center gap-2 flex-1 min-w-0">
					<div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: <?php echo esc_attr($collapse_icon_background); ?>;">
						<img class="max-w-[18px] max-h-[18px]" src="<?php echo esc_url($bot_logotype); ?>" alt="Bot">
					</div>
					<div class="flex-1 min-w-0">
						<div class="font-semibold text-sm truncate" style="color: <?php echo esc_attr($header_text_color); ?>;"><?php echo esc_html($bot_name ?: 'Fin'); ?></div>
						<?php if ($bot_subtitle) : ?>
							<div class="text-xs truncate" style="color: <?php echo esc_attr($header_subtitle_color); ?>;"><?php echo esc_html($bot_subtitle); ?></div>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<div class="flex items-center gap-2 flex-shrink-0">
				<button class="walla-close-btn p-1 cursor-pointer transition-opacity hover:opacity-70" style="color: <?php echo esc_attr($close_button_color); ?>;">
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M15 5L5 15M5 5L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>
			</div>
		</div>

		<div class="walla-chat-body-wrapper flex flex-col" style="max-height: 500px;">
			<div class="walla-chat-body p-4 flex-1 overflow-y-auto">
				<div class="walla-messages">
					<div class="walla-message walla-bot-message mb-4">
						<div class="inline-block px-4 py-3 rounded-[18px] rounded-tl-none max-w-[85%]" style="background-color: <?php echo esc_attr($message_background); ?>;">
							<div class="walla-welcome-message-text text-sm leading-relaxed" style="color: <?php echo esc_attr($message_text_color); ?>;" data-full-text="<?php echo esc_attr(wp_strip_all_tags($welcome_message)); ?>"></div>
						</div>
						<div class="mt-1 text-xs px-1" style="color: <?php echo esc_attr($label_color); ?>;">
							<?php echo esc_html($welcome_message_label ?: 'AI Agent'); ?> • <span class="walla-time">Just now</span>
						</div>
					</div>
				</div>
			</div>

			<div class="walla-chat-input-wrapper px-4 pb-4 pt-2 border-t" style="border-color: rgba(255, 255, 255, 0.1);">
				<form class="walla-send-form flex items-center gap-2">
					<input 
						type="text" 
						class="walla-input flex-1 px-4 py-3 rounded-full text-sm outline-none border-none" 
						placeholder="<?php echo esc_attr($input_placeholder ?: 'Type a message...'); ?>"
						style="background-color: <?php echo esc_attr($input_background); ?>; color: <?php echo esc_attr($input_text_color); ?>;"
					>
					<button 
						type="submit" 
						class="walla-send-btn flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center cursor-pointer transition-opacity hover:opacity-80"
						style="background-color: <?php echo esc_attr($send_button_background); ?>;"
					>
						<?php if ($send_message_icon) : ?>
							<img src="<?php echo esc_url($send_message_icon); ?>" alt="Send" class="max-w-[20px] max-h-[20px]">
						<?php else : ?>
							<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M18 2L9 11M18 2L12 18L9 11M18 2L2 8L9 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						<?php endif; ?>
					</button>
				</form>
			</div>
		</div>
	</div>
</div>

<style>
	.walla-bot-wrapper {
		font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
	}

	.walla-collapse-icon {
		z-index: 99999;
	}

	.walla-chat {
		z-index: 99998;
		max-height: 600px;
	}

	.walla-chat.show {
		opacity: 1 !important;
		transform: translateY(0) !important;
		pointer-events: auto !important;
	}

	.walla-chat-header {
		border-bottom: 1px solid rgba(255, 255, 255, 0.1);
	}

	.walla-chat-body {
		scrollbar-width: thin;
		scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
	}

	.walla-chat-body::-webkit-scrollbar {
		width: 6px;
	}

	.walla-chat-body::-webkit-scrollbar-track {
		background: transparent;
	}

	.walla-chat-body::-webkit-scrollbar-thumb {
		background-color: rgba(255, 255, 255, 0.2);
		border-radius: 3px;
	}

	.walla-chat-body::-webkit-scrollbar-thumb:hover {
		background-color: rgba(255, 255, 255, 0.3);
	}

	.walla-chat-body-wrapper {
		display: flex;
		flex-direction: column;
	}

	.walla-messages {
		min-height: 100%;
	}

	.walla-user-message {
		display: flex;
		flex-direction: column;
		align-items: flex-end;
		justify-content: flex-end;
		margin-bottom: 1rem;
	}

	.walla-user-message .walla-message-content {
		display: inline-block;
		padding: 12px 16px;
		border-radius: 18px;
		border-top-right-radius: 0;
		max-width: 85%;
		text-align: right;
	}

	.walla-user-message .walla-message-label {
		text-align: right;
	}

	.walla-chat-input-wrapper input::placeholder {
		color: <?php echo esc_attr($input_placeholder_color); ?>;
	}

	.walla-chat-input-wrapper input {
		caret-color: <?php echo esc_attr($input_text_color); ?>;
		caret-shape: block;
	}

	.walla-send-btn {
		color: white;
	}

	.walla-send-btn svg {
		color: white;
	}

	.walla-collapse-icon-arrow {
		display: none;
	}

	.walla-collapse-icon.chat-open .walla-collapse-icon-img {
		display: none;
	}

	.walla-collapse-icon.chat-open .walla-collapse-icon-arrow {
		display: block;
	}

	@media (max-width: 767px) {
		.walla-chat {
			bottom: 0 !important;
			right: 0 !important;
			left: 0 !important;
			max-width: 100% !important;
			border-radius: 20px 20px 0 0 !important;
			max-height: 85vh !important;
		}

		.walla-collapse-icon {
			bottom: 20px !important;
			right: 20px !important;
		}
	}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
	const collapseIcon = document.querySelector('.walla-collapse-icon');
	const chat = document.querySelector('.walla-chat');
	const backBtn = document.querySelector('.walla-back-btn');
	const closeBtn = document.querySelector('.walla-close-btn');
	const timeElement = document.querySelector('.walla-time');
	const messagesContainer = document.querySelector('.walla-messages');
	const sendForm = document.querySelector('.walla-send-form');
	const input = sendForm ? sendForm.querySelector('.walla-input') : null;
	
	let isChatOpen = false;
	let dateUpdateInterval = null;
	const chatOpenedTime = new Date();
	
	function formatTime(date) {
		const now = new Date();
		const diff = Math.floor((now - date) / 1000);
		
		if (diff < 60) {
			return 'Just now';
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
	
	function updateTime() {
		if (timeElement) {
			timeElement.textContent = formatTime(chatOpenedTime);
		}
	}
	
	function startTimeUpdater() {
		if (dateUpdateInterval) {
			clearInterval(dateUpdateInterval);
		}
		updateTime();
		dateUpdateInterval = setInterval(updateTime, 60000);
	}
	
	function stopTimeUpdater() {
		if (dateUpdateInterval) {
			clearInterval(dateUpdateInterval);
			dateUpdateInterval = null;
		}
	}
	
	function scrollToBottom() {
		if (messagesContainer) {
			const chatBody = messagesContainer.closest('.walla-chat-body');
			if (chatBody) {
				requestAnimationFrame(() => {
					chatBody.scrollTop = chatBody.scrollHeight;
				});
			}
		}
	}
	
	function typeText(element, text, speed, callback) {
		if (!element || !text) return;
		
		let index = 0;
		element.textContent = '';
		
		function type() {
			if (index < text.length) {
				const char = text.charAt(index);
				element.textContent += char;
				index++;
				
				let nextDelay = speed;
				if (char === ' ' || char === '\n') {
					nextDelay = speed * 0.3;
				} else if (char === '.' || char === '!' || char === '?') {
					nextDelay = speed * 3.5;
				} else if (char === ',' || char === ';' || char === ':') {
					nextDelay = speed * 2.5;
				} else if (/[a-zA-Z]/.test(char)) {
					nextDelay = speed + Math.random() * 10;
				} else {
					nextDelay = speed * 1.2;
				}
				
				setTimeout(type, nextDelay);
				scrollToBottom();
			} else {
				if (callback) callback();
			}
		}
		
		type();
	}
	
	function addUserMessage(text) {
		if (!messagesContainer) return;
		
		const userMessageBg = '<?php echo esc_js($user_message_background); ?>';
		const messageTextColor = '<?php echo esc_js($message_text_color); ?>';
		const labelColor = '<?php echo esc_js($label_color); ?>';
		
		const messageDiv = document.createElement('div');
		messageDiv.className = 'walla-user-message';
		
		const contentDiv = document.createElement('div');
		contentDiv.className = 'walla-message-content';
		contentDiv.style.cssText = 'background-color: ' + userMessageBg + '; color: ' + messageTextColor + '; padding: 12px 16px; border-radius: 18px; border-top-right-radius: 0; max-width: 85%; display: inline-block; text-align: right;';
		
		const labelDiv = document.createElement('div');
		labelDiv.className = 'walla-message-label mt-1 text-xs px-1';
		labelDiv.style.cssText = 'color: ' + labelColor + '; text-align: right;';
		labelDiv.innerHTML = 'You • <span class="user-time">Just now</span>';
		
		messageDiv.appendChild(contentDiv);
		messageDiv.appendChild(labelDiv);
		messagesContainer.appendChild(messageDiv);
		
		typeText(contentDiv, text, 35, function() {
			const userTimeEl = messageDiv.querySelector('.user-time');
			if (userTimeEl) {
				const msgTime = new Date();
				userTimeEl.textContent = formatTime(msgTime);
			}
		});
		
		scrollToBottom();
	}
	
	function openChat() {
		if (isChatOpen || !chat) return;
		
		isChatOpen = true;
		
		if (collapseIcon) {
			collapseIcon.classList.add('chat-open');
		}
		
		chat.style.display = 'block';
		requestAnimationFrame(function() {
			setTimeout(function() {
				chat.classList.add('show');
				startTimeUpdater();
				
				const welcomeMessageEl = document.querySelector('.walla-welcome-message-text');
				if (welcomeMessageEl) {
					const fullText = welcomeMessageEl.getAttribute('data-full-text') || '';
					if (fullText) {
						setTimeout(function() {
							typeText(welcomeMessageEl, fullText, 35);
						}, 300);
					}
				}
				
				scrollToBottom();
			}, 10);
		});
	}
	
	function closeChat() {
		if (!isChatOpen || !chat) return;
		
		isChatOpen = false;
		stopTimeUpdater();
		
		if (collapseIcon) {
			collapseIcon.classList.remove('chat-open');
		}
		
		chat.classList.remove('show');
		
		setTimeout(function() {
			if (!chat.classList.contains('show')) {
				chat.style.display = 'none';
			}
		}, 300);
	}
	
	if (collapseIcon) {
		collapseIcon.addEventListener('click', function() {
			if (isChatOpen) {
				closeChat();
			} else {
				openChat();
			}
		});
	}
	
	if (backBtn) {
		backBtn.addEventListener('click', closeChat);
	}
	
	if (closeBtn) {
		closeBtn.addEventListener('click', closeChat);
	}
	
	if (sendForm && input) {
		sendForm.addEventListener('submit', function(e) {
			e.preventDefault();
			const message = input.value.trim();
			if (!message) return;
			
			addUserMessage(message);
			input.value = '';
		});
		
		input.addEventListener('keydown', function(e) {
			if (e.key === 'Enter' && !e.shiftKey) {
				e.preventDefault();
				sendForm.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
			}
		});
	}
	
	if (timeElement) {
		updateTime();
	}
});
</script>