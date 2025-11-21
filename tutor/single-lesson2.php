<?php

/**
 * Custom single lesson template for Walla theme
 * Based on Tutor LMS plugin
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! function_exists('tutor_utils')) {
	wp_die('Tutor LMS plugin is required for this template.');
}

require_once get_template_directory() . '/tutor/ajax-handlers.php';

global $post;

$lesson_id = get_the_ID();
$course_id = tutor_utils()->get_course_id_by_subcontent($lesson_id);
$course = get_post($course_id);

$video_info = tutor_utils()->get_video_info();
$has_video = is_object($video_info) && ($video_info->source_video_id || $video_info->source_external_url || $video_info->source_embedded);

$instructor_id = get_post_field('post_author', $course_id);
$instructor = get_userdata($instructor_id);

$total_students = tutor_utils()->count_enrolled_users_by_course($course_id);
$total_lessons = tutor_utils()->get_course_contents_by_topic($course_id, -1);

use Tutor\Models\LessonModel;

$total_lessons_count = LessonModel::get_total_lesson([$course_id]);

$course_duration = tutor_utils()->get_course_duration($course_id, false);
$course_level = get_tutor_course_level($course_id);

$topics = tutor_utils()->get_topics($course_id);

$is_enrolled = tutor_utils()->is_enrolled($course_id, get_current_user_id());
$is_public_course = \TUTOR\Course_List::is_public($course_id);

$lesson_content = get_the_content();
$attachments = tutor_utils()->get_attachments();
$has_attachments = is_array($attachments) && !empty($attachments);

$current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'about';

get_header();
?>

<style>
	.lesson-page {
		display: flex;
		min-height: 100vh;
		background: #f8f9fa;
	}

	.lesson-main {
		flex: 1;
		padding: 2rem;
		max-width: calc(100% - 400px);
	}

	.lesson-sidebar {
		width: 400px;
		background: white;
		border-left: 1px solid #e9ecef;
		overflow-y: auto;
		height: 100vh;
		position: sticky;
		top: 0;
	}

	.video-container {
		background: #000;
		border-radius: 12px;
		overflow: hidden;
		margin-bottom: 2rem;
		position: relative;
		aspect-ratio: 16/9;
	}

	.tutor-video-player {
		width: 100%;
		height: 100%;
	}

	.tutor-ratio {
		position: relative;
		width: 100%;
		height: 100%;
	}

	.tutor-ratio-16x9 {
		aspect-ratio: 16/9;
	}

	.tutor-video-player iframe {
		width: 100%;
		height: 100%;
		border: none;
		border-radius: 12px;
	}

	.tutor-video-player video {
		width: 100%;
		height: 100%;
		border-radius: 12px;
	}

	.tutorPlayer {
		width: 100%;
		height: 100%;
		border-radius: 12px;
	}

	.video-placeholder {
		width: 100%;
		height: 100%;
		display: flex;
		align-items: center;
		justify-content: center;
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		position: relative;
	}

	.play-button {
		width: 80px;
		height: 80px;
		background: #ff6b9d;
		border-radius: 50%;
		display: flex;
		align-items: center;
		justify-content: center;
		cursor: pointer;
		transition: transform 0.3s ease;
		box-shadow: 0 8px 32px rgba(255, 107, 157, 0.3);
	}

	.play-button:hover {
		transform: scale(1.1);
	}

	.play-button::after {
		content: '';
		width: 0;
		height: 0;
		border-left: 20px solid white;
		border-top: 12px solid transparent;
		border-bottom: 12px solid transparent;
		margin-left: 8px;
	}

	.course-info {
		margin-bottom: 2rem;
	}

	.course-author {
		color: #6c757d;
		font-size: 0.9rem;
		margin-bottom: 0.5rem;
	}

	.course-title {
		font-size: 2rem;
		font-weight: 700;
		color: #212529;
		margin-bottom: 1rem;
	}

	.course-meta {
		display: flex;
		gap: 2rem;
		flex-wrap: wrap;
		margin-bottom: 2rem;
	}

	.meta-item {
		display: flex;
		align-items: center;
		gap: 0.5rem;
		color: #6c757d;
		font-size: 0.9rem;
	}

	.meta-icon {
		width: 16px;
		height: 16px;
		opacity: 0.7;
	}

	.tabs-nav {
		display: flex;
		border-bottom: 1px solid #e9ecef;
		margin-bottom: 2rem;
	}

	.tab-item {
		padding: 1rem 2rem;
		border: none;
		background: none;
		cursor: pointer;
		color: #6c757d;
		font-weight: 500;
		transition: all 0.3s ease;
		border-bottom: 3px solid transparent;
	}

	.tab-item.active {
		color: #6f42c1;
		border-bottom-color: #6f42c1;
		background: rgba(111, 66, 193, 0.05);
	}

	.tab-content {
		display: none;
	}

	.tab-content.active {
		display: block;
	}

	.lesson-content {
		line-height: 1.7;
		color: #495057;
	}

	.quick-quiz {
		background: white;
		border-radius: 12px;
		padding: 2rem;
		margin-top: 2rem;
		border: 1px solid #e9ecef;
	}

	.quiz-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 1rem;
	}

	.quiz-title {
		font-size: 1.25rem;
		font-weight: 600;
		color: #212529;
	}

	.quiz-progress {
		color: #6c757d;
		font-size: 0.9rem;
	}

	/* Sidebar Styles */
	.sidebar-header {
		padding: 1.5rem;
		border-bottom: 1px solid #e9ecef;
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	.sidebar-title {
		font-weight: 600;
		color: #495057;
	}

	.close-sidebar {
		display: none;
		background: none;
		border: none;
		cursor: pointer;
		color: #6c757d;
	}

	.topic-item {
		border-bottom: 1px solid #f1f3f4;
	}

	.topic-header {
		padding: 1rem 1.5rem;
		cursor: pointer;
		display: flex;
		justify-content: space-between;
		align-items: center;
		transition: background-color 0.3s ease;
	}

	.topic-header:hover {
		background: #f8f9fa;
	}

	.topic-header.active {
		background: #e3f2fd;
	}

	.topic-title {
		font-weight: 500;
		color: #212529;
	}

	.topic-meta {
		color: #6c757d;
		font-size: 0.8rem;
	}

	.topic-arrow {
		transition: transform 0.3s ease;
	}

	.topic-header.active .topic-arrow {
		transform: rotate(180deg);
	}

	.lesson-list {
		display: none;
		background: #f8f9fa;
	}

	.lesson-list.active {
		display: block;
	}

	.lesson-item {
		padding: 0.75rem 1.5rem 0.75rem 3rem;
		display: flex;
		justify-content: space-between;
		align-items: center;
		transition: background-color 0.3s ease;
		border-bottom: 1px solid #e9ecef;
		text-decoration: none;
		color: inherit;
	}

	.lesson-item:hover {
		background: #e9ecef;
	}

	.lesson-item.active {
		background: #6f42c1;
		color: white;
	}

	.lesson-item.active .lesson-duration {
		color: rgba(255, 255, 255, 0.8);
	}

	.lesson-item.completed {
		background: #d4edda;
		border-left: 4px solid #28a745;
	}

	.lesson-item.completed .lesson-title {
		color: #155724;
		font-weight: 600;
	}

	.lesson-item.completed .lesson-duration {
		color: #28a745;
	}

	.completed-icon {
		color: #28a745 !important;
		font-weight: bold;
		margin-left: 8px;
	}

	.lesson-title {
		font-size: 0.9rem;
		font-weight: 500;
	}

	.lesson-duration {
		color: #6c757d;
		font-size: 0.8rem;
	}

	.lesson-icon {
		width: 16px;
		height: 16px;
		margin-right: 0.5rem;
		opacity: 0.7;
	}

	/* Discussion/Q&A Styles */
	.discussion-container {
		background: white;
		border-radius: 12px;
		padding: 2rem;
		border: 1px solid #e9ecef;
	}

	.discussion-header {
		margin-bottom: 2rem;
	}

	.discussion-title {
		font-size: 1.5rem;
		font-weight: 600;
		color: #212529;
		margin: 0;
	}

	.discussion-list {
		margin-bottom: 2rem;
	}

	.discussion-item {
		display: flex;
		gap: 1rem;
		padding: 1.5rem;
		border: 1px solid #e9ecef;
		border-radius: 12px;
		margin-bottom: 1rem;
		background: white;
		transition: box-shadow 0.3s ease;
	}

	.discussion-item:hover {
		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
	}

	.discussion-avatar {
		flex-shrink: 0;
	}

	.discussion-avatar img {
		width: 48px;
		height: 48px;
		border-radius: 50%;
		object-fit: cover;
	}

	.discussion-content {
		flex: 1;
	}

	.discussion-meta {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 0.5rem;
	}

	.discussion-author {
		font-weight: 600;
		color: #212529;
		font-size: 0.95rem;
	}

	.discussion-time {
		color: #6c757d;
		font-size: 0.85rem;
	}

	.discussion-text {
		color: #495057;
		line-height: 1.6;
		margin-bottom: 1rem;
		font-size: 0.95rem;
	}

	.discussion-actions {
		display: flex;
		align-items: center;
	}

	.like-button {
		display: flex;
		align-items: center;
		gap: 0.5rem;
		background: none;
		border: none;
		color: #6c757d;
		cursor: pointer;
		padding: 0.5rem;
		border-radius: 6px;
		transition: all 0.3s ease;
		font-size: 0.9rem;
	}

	.like-button:hover {
		background: #f8f9fa;
		color: #6f42c1;
	}

	.like-button.liked {
		color: #6f42c1;
	}

	.likes-count {
		font-weight: 500;
	}

	.no-discussions {
		text-align: center;
		padding: 3rem 1rem;
		color: #6c757d;
	}

	.question-input-container {
		margin-top: 2rem;
		padding-top: 2rem;
		border-top: 1px solid #e9ecef;
	}

	.question-input-wrapper {
		display: flex;
		gap: 1rem;
		align-items: flex-end;
		background: white;
		border: 1px solid #e9ecef;
		border-radius: 12px;
		padding: 1rem;
	}

	.question-avatar {
		flex-shrink: 0;
	}

	.question-avatar img {
		width: 40px;
		height: 40px;
		border-radius: 50%;
		object-fit: cover;
	}

	.question-input {
		flex: 1;
	}

	.question-textarea {
		width: 100%;
		min-height: 60px;
		border: none;
		resize: vertical;
		font-family: inherit;
		font-size: 0.95rem;
		color: #495057;
		background: transparent;
		outline: none;
	}

	.question-textarea::placeholder {
		color: #6c757d;
	}

	.question-submit {
		flex-shrink: 0;
		width: 40px;
		height: 40px;
		border: none;
		background: #6f42c1;
		color: white;
		border-radius: 50%;
		cursor: pointer;
		display: flex;
		align-items: center;
		justify-content: center;
		transition: all 0.3s ease;
	}

	.question-submit:hover {
		background: #5a32a3;
		transform: scale(1.05);
	}

	.question-submit:disabled {
		background: #6c757d;
		cursor: not-allowed;
		transform: none;
	}

	@media (max-width: 1024px) {
		.lesson-page {
			flex-direction: column;
		}

		.lesson-main {
			max-width: 100%;
		}

		.lesson-sidebar {
			width: 100%;
			height: auto;
			position: relative;
		}

		.close-sidebar {
			display: block;
		}
	}

	@media (max-width: 768px) {
		.lesson-main {
			padding: 1rem;
		}

		.course-meta {
			gap: 1rem;
		}

		.tabs-nav {
			flex-wrap: wrap;
		}

		.tab-item {
			padding: 0.75rem 1rem;
			font-size: 0.9rem;
		}
	}
</style>

<div class="lesson-page">
	<div class="lesson-main">
		<div class="video-container">
			<?php echo apply_filters('tutor_single_lesson_video', tutor_lesson_video(false), $video_info); ?>
		</div>

		<div class="course-info">
			<div class="course-author">
				by <?php echo esc_html($instructor ? $instructor->display_name : 'Unknown Instructor'); ?>
			</div>
			<h1 class="course-title"><?php echo esc_html($course ? $course->post_title : 'Course Title'); ?></h1>

			<div class="course-meta">
				<div class="meta-item">
					<svg class="meta-icon" fill="currentColor" viewBox="0 0 20 20">
						<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
					</svg>
					<?php echo esc_html($course_duration ? $course_duration : '2 Weeks'); ?>
				</div>
				<div class="meta-item">
					<svg class="meta-icon" fill="currentColor" viewBox="0 0 20 20">
						<path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
					</svg>
					<?php echo esc_html($total_students); ?> Students
				</div>
				<div class="meta-item">
					<svg class="meta-icon" fill="currentColor" viewBox="0 0 20 20">
						<path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
					</svg>
					<?php echo esc_html($course_level ? $course_level : 'All levels'); ?>
				</div>
				<div class="meta-item">
					<svg class="meta-icon" fill="currentColor" viewBox="0 0 20 20">
						<path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
					</svg>
					<?php echo esc_html($total_lessons_count); ?> Lessons
				</div>
			</div>
		</div>

		<div class="tabs-nav">
			<button class="tab-item <?php echo $current_tab === 'about' ? 'active' : ''; ?>" data-tab="about">
				About
			</button>
			<?php if ($has_attachments) : ?>
				<button class="tab-item <?php echo $current_tab === 'materials' ? 'active' : ''; ?>" data-tab="materials">
					Study Materials
				</button>
			<?php endif; ?>
			<button class="tab-item <?php echo $current_tab === 'qa' ? 'active' : ''; ?>" data-tab="qa">
				Q&A
			</button>
			<button class="tab-item <?php echo $current_tab === 'review' ? 'active' : ''; ?>" data-tab="review">
				Review
			</button>
		</div>

		<div class="tab-content <?php echo $current_tab === 'about' ? 'active' : ''; ?>" id="about">
			<div class="lesson-content">
				<?php if ($lesson_content) : ?>
					<?php echo wp_kses_post($lesson_content); ?>
				<?php else : ?>
					<p>In this lesson, we'll explore the fundamental architecture of neural networks, including layers, neurons, and activation functions. You'll learn how to design and implement basic neural network architectures using popular frameworks.</p>
				<?php endif; ?>
			</div>
		</div>

		<?php if ($has_attachments) : ?>
			<div class="tab-content <?php echo $current_tab === 'materials' ? 'active' : ''; ?>" id="materials">
				<h3>Study Materials</h3>
				<?php get_tutor_posts_attachments(); ?>
			</div>
		<?php endif; ?>

		<div class="tab-content <?php echo $current_tab === 'qa' ? 'active' : ''; ?>" id="qa">
			<?php
			$disable_qa_for_this_course = get_post_meta($course_id, '_tutor_enable_qa', true) != 'yes';
			$enable_q_and_a_on_course = tutor_utils()->get_option('enable_q_and_a_on_course');
			if (!$enable_q_and_a_on_course || $disable_qa_for_this_course) {
				echo '<div class="no-qa-message">Q&A is disabled for this course.</div>';
			} else {
				$questions = tutor_utils()->get_qa_questions(0, 20, '', null, null, null, null, false, array('course_id' => $course_id));
			?>
				<div class="walla-qna-list">
					<?php if (count($questions)): ?>
						<?php foreach ($questions as $question): ?>
							<div class="walla-qna-item">
								<?php
								tutor_load_template_from_custom_path(
									tutor()->path . '/views/qna/qna-single.php',
									array(
										'question_id' => $question->comment_ID,
										'context' => 'course-single-qna-sidebar',
									),
									false
								);
								?>
							</div>
						<?php endforeach; ?>
					<?php else: ?>
						<div class="no-questions">No questions yet. Be the first to ask!</div>
					<?php endif; ?>
				</div>
				<div class="walla-qna-form">
					<?php
					tutor_load_template_from_custom_path(
						tutor()->path . '/views/qna/qna-new.php',
						array(
							'course_id' => $course_id,
							'context' => 'course-single-qna-sidebar',
						),
						false
					);
					?>
				</div>
			<?php
			}
			?>
		</div>

		<div class="tab-content <?php echo $current_tab === 'review' ? 'active' : ''; ?>" id="review">
			<h3>Course Review</h3>
			<p>Share your thoughts and review this course.</p>
		</div>

		<div class="quick-quiz">
			<div class="quiz-header">
				<h3 class="quiz-title">Quick Quiz</h3>
				<span class="quiz-progress">1/5</span>
			</div>
			<p>Test your knowledge with this quick quiz about neural networks.</p>
		</div>
	</div>

	<div class="lesson-sidebar">
		<div class="sidebar-header">
			<h3 class="sidebar-title">Course Content</h3>
			<button class="close-sidebar" onclick="toggleSidebar()">
				<svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
					<path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
				</svg>
			</button>
		</div>

		<?php if ($topics && $topics->have_posts()) : ?>
			<?php while ($topics->have_posts()) : $topics->the_post(); ?>
				<?php

				$topic_id = get_the_ID();
				$topic_title = get_the_title();
				$topic_lessons = tutor_utils()->get_course_contents_by_topic($topic_id, -1);
				$is_topic_active = false;

				if ($topic_lessons && $topic_lessons->have_posts()) {
					while ($topic_lessons->have_posts()) {
						$topic_lessons->the_post();
						if (get_the_ID() == $lesson_id) {
							$is_topic_active = true;
							break;
						}
					}
					$topic_lessons->rewind_posts();
				}
				?>

				<div class="topic-item">
					<div class="topic-header <?php echo $is_topic_active ? 'active' : ''; ?>" onclick="toggleTopic(this)">
						<div>
							<div class="topic-title"><?php echo esc_html($topic_title); ?></div>
							<div class="topic-meta">
								<?php
								echo ($topic_lessons && $topic_lessons->have_posts()) ? $topic_lessons->found_posts : 0;
								?> lectures •
								<?php
								$topic_duration = 0;

								if ($topic_lessons && $topic_lessons->have_posts()) {
									while ($topic_lessons->have_posts()) {
										$topic_lessons->the_post();
										$video = tutor_utils()->get_video_info();
										if ($video) {
											if (isset($video->duration_sec) && $video->duration_sec > 0) {
												$topic_duration += (int)$video->duration_sec;
											} elseif (isset($video->playtime) && $video->playtime) {
												$parts = explode(':', $video->playtime);
												if (count($parts) == 2) {
													$minutes = (int)$parts[0];
													$seconds = (int)$parts[1];
													$topic_duration += $minutes * 60 + $seconds;
												} elseif (count($parts) == 3) {
													$hours = (int)$parts[0];
													$minutes = (int)$parts[1];
													$seconds = (int)$parts[2];
													$topic_duration += $hours * 3600 + $minutes * 60 + $seconds;
												}
											}
										}
									}
									$topic_lessons->rewind_posts();
								}

								if (!function_exists('format_seconds_to_time')) {
									function format_seconds_to_time($seconds)
									{
										$seconds = (int)$seconds;
										$h = floor($seconds / 3600);
										$m = floor(($seconds % 3600) / 60);
										$s = $seconds % 60;
										if ($h > 0) {
											return sprintf('%d:%02d:%02d', $h, $m, $s);
										} else {
											return sprintf('%d:%02d', $m, $s);
										}
									}
								}
								if (!$topic_lessons || !$topic_lessons->have_posts()) {
									echo '0:00';
								} else {
									if ($topic_duration > 0) {
										echo esc_html(format_seconds_to_time($topic_duration));
									} else {
										echo '0:00';
									}
								}
								?>
							</div>
						</div>
						<svg class="topic-arrow" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
							<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
						</svg>
					</div>

					<div class="lesson-list <?php echo $is_topic_active ? 'active' : ''; ?>">
						<?php if ($topic_lessons && $topic_lessons->have_posts()) : ?>
							<?php while ($topic_lessons->have_posts()) : $topic_lessons->the_post(); ?>
								<?php
								$current_lesson_id = get_the_ID();
								$is_current_lesson = $current_lesson_id == $lesson_id;
								$video = tutor_utils()->get_video_info();
								$duration = $video ? $video->playtime : '00:00';
								$is_completed = tutor_utils()->is_completed_lesson($current_lesson_id);
								?>

								<a href="<?php echo esc_url(get_permalink($current_lesson_id)); ?>" class="lesson-item <?php echo $is_current_lesson ? 'active' : ''; ?> <?php echo $is_completed ? 'completed' : ''; ?>">
									<div class="lesson-info">
										<svg class="lesson-icon" fill="currentColor" viewBox="0 0 20 20">
											<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
										</svg>
										<span class="lesson-title"><?php the_title(); ?></span>
										<?php if ($is_completed): ?>
											<span class="completed-icon">✓</span>
										<?php endif; ?>
									</div>
									<span class="lesson-duration"><?php echo esc_html($duration); ?></span>
								</a>
							<?php endwhile; ?>
							<?php $topic_lessons->rewind_posts(); ?>
						<?php endif; ?>
					</div>
				</div>
			<?php endwhile; ?>
			<?php $topics->reset_postdata(); ?>
		<?php endif; ?>
	</div>
</div>

<script>
	document.querySelectorAll('.tab-item').forEach(tab => {
		tab.addEventListener('click', function() {
			document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
			document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

			this.classList.add('active');

			const tabId = this.getAttribute('data-tab');
			document.getElementById(tabId).classList.add('active');
		});
	});

	function toggleTopic(element) {
		const topicItem = element.closest('.topic-item');
		const lessonList = topicItem.querySelector('.lesson-list');
		const isActive = element.classList.contains('active');

		document.querySelectorAll('.topic-header').forEach(header => {
			header.classList.remove('active');
			header.closest('.topic-item').querySelector('.lesson-list').classList.remove('active');
		});

		if (!isActive) {
			element.classList.add('active');
			lessonList.classList.add('active');
		}
	}

	function toggleSidebar() {
		const sidebar = document.querySelector('.lesson-sidebar');
		sidebar.style.display = sidebar.style.display === 'none' ? 'block' : 'none';
	}

	document.addEventListener('DOMContentLoaded', function() {
		const currentLessonItem = document.querySelector('.lesson-item.active');
		if (currentLessonItem) {
			const topicItem = currentLessonItem.closest('.topic-item');
			const topicHeader = topicItem.querySelector('.topic-header');
			const lessonList = topicItem.querySelector('.lesson-list');

			topicHeader.classList.add('active');
			lessonList.classList.add('active');
		}

		// Автоматическое завершение урока
		initLessonCompletion();
	});

	// Функция для автоматического завершения урока
	function initLessonCompletion() {
		const lessonId = <?php echo $lesson_id; ?>;
		const courseId = <?php echo $course_id; ?>;
		let startTime = Date.now();
		let isCompleted = false;
		let videoWatched = false;
		let timeWatched = 0;
		const requiredTime = 30; // Минимальное время просмотра в секундах
		const videoThreshold = 0.8; // 80% видео должно быть просмотрено

		// Отслеживание времени на странице
		const timeTracker = setInterval(function() {
			timeWatched = Math.floor((Date.now() - startTime) / 1000);
			
			// Если прошло достаточно времени, отмечаем урок как завершенный
			if (timeWatched >= requiredTime && !isCompleted) {
				markLessonAsCompleted(lessonId, courseId);
				isCompleted = true;
				clearInterval(timeTracker);
			}
		}, 1000);

		// Отслеживание видео
		const videoElement = document.querySelector('video');
		const iframeElement = document.querySelector('iframe[src*="youtube"], iframe[src*="vimeo"]');
		
		if (videoElement) {
			// Для обычного HTML5 видео
			videoElement.addEventListener('timeupdate', function() {
				if (videoElement.duration > 0) {
					const progress = videoElement.currentTime / videoElement.duration;
					if (progress >= videoThreshold && !isCompleted) {
						markLessonAsCompleted(lessonId, courseId);
						isCompleted = true;
						clearInterval(timeTracker);
					}
				}
			});
		} else if (iframeElement) {
			// Для YouTube/Vimeo видео - отслеживаем время на странице
			// Если пользователь находится на странице достаточно долго, считаем что видео просмотрено
			setTimeout(function() {
				if (!isCompleted) {
					markLessonAsCompleted(lessonId, courseId);
					isCompleted = true;
					clearInterval(timeTracker);
				}
			}, 60000); // 1 минута для iframe видео
		}

		// Отслеживание скролла и активности
		let lastActivity = Date.now();
		document.addEventListener('scroll', function() {
			lastActivity = Date.now();
		});

		document.addEventListener('click', function() {
			lastActivity = Date.now();
		});

		// Проверка активности каждые 10 секунд
		setInterval(function() {
			const timeSinceActivity = Date.now() - lastActivity;
			if (timeSinceActivity > 300000) { // 5 минут неактивности
				startTime = Date.now(); // Сбрасываем таймер
			}
		}, 10000);
	}

	// Функция для отметки урока как завершенного
	function markLessonAsCompleted(lessonId, courseId) {
		// Проверяем, не завершен ли уже урок
		if (document.querySelector('.lesson-completed')) {
			return;
		}

		// AJAX запрос для завершения урока
		fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
			},
			body: new URLSearchParams({
				action: 'tutor_mark_lesson_complete',
				lesson_id: lessonId,
				course_id: courseId,
				nonce: '<?php echo wp_create_nonce('tutor_mark_lesson_complete'); ?>'
			})
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				// Обновляем UI
				const currentLesson = document.querySelector('.lesson-item.active');
				if (currentLesson) {
					currentLesson.classList.add('completed');
					
					// Добавляем иконку завершения
					const lessonInfo = currentLesson.querySelector('.lesson-info');
					if (lessonInfo && !lessonInfo.querySelector('.completed-icon')) {
						const completedIcon = document.createElement('span');
						completedIcon.className = 'completed-icon';
						completedIcon.innerHTML = '✓';
						completedIcon.style.cssText = 'color: #28a745; margin-left: 8px; font-weight: bold;';
						lessonInfo.appendChild(completedIcon);
					}
				}

				// Показываем уведомление
				showCompletionNotification();
			}
		})
		.catch(error => {
			console.error('Error marking lesson as completed:', error);
		});
	}

	// Функция для показа уведомления о завершении
	function showCompletionNotification() {
		const notification = document.createElement('div');
		notification.style.cssText = `
			position: fixed;
			top: 20px;
			right: 20px;
			background: #28a745;
			color: white;
			padding: 15px 20px;
			border-radius: 8px;
			box-shadow: 0 4px 12px rgba(0,0,0,0.15);
			z-index: 1000;
			font-weight: 500;
		`;
		notification.textContent = 'Lesson completed! Great job!';
		document.body.appendChild(notification);

		// Убираем уведомление через 3 секунды
		setTimeout(() => {
			notification.remove();
		}, 3000);
	}
</script>

<?php
get_footer();
?>