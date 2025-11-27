<?php get_header(); 

//$timer = get_field('timer');
//$timer_duration = !empty($timer['time']) ? $timer['time'] : '';
$timer_duration = get_field('timer');
$timer_duration_seconds = 0;
if ($timer_duration) {
	$time_parts = explode(':', $timer_duration);
	if (count($time_parts) === 3) {
		$hours = intval($time_parts[0]);
		$minutes = intval($time_parts[1]);
		$seconds = intval($time_parts[2]);
		$timer_duration_seconds = $hours * 3600 + $minutes * 60 + $seconds;
	}
}
?>

<section class="hero-course py-10 md:py-[22px] px-4">
	<div class="container m-auto">

		<div class="hero-course-box bg-[#C1D8FF] rounded-[20px] md:rounded-[36px] p-4 md:py-8 md:px-11 flex flex-col gap-[20px] md:gap-8">

			<div class="flex gap-12">

				<div class="w-full lg:w-1/2">
					<div class="flex flex-col gap-[26px] md:gap-5 mb-[26px] md:mb-4">
						<?php
						$course_id = get_the_ID();
						$tags = get_the_terms($course_id, 'course-category');

						if ($tags && ! is_wp_error($tags)) {
							echo '<div class="tutor-course-tags hidden lg:flex gap-2">';
							foreach ($tags as $tag) {
								echo '<span class="tutor-course-tag bg-[#FF00A3] rounded-[8px] py-[6px] px-3 text-white font-inter">' . esc_html($tag->name) . '</span> ';
							}
							echo '</div>';
						}
						?>
						<h1 class="font-manrope text-[32px] leading-[40px] lg:text-[48px] lg:leading-[58px] font-medium"><?php the_title(); ?></h1>
						<a class="relative rounded-[9px] h-[197px] md:rounded-[16px] md:h-[346px] w-full flex justify-center items-center overflow-hidden cursor-pointer bg-cover bg-center lg:hidden" style="background-image: url(<?php echo get_the_post_thumbnail_url(); ?>);">
							<span class="inset-0 absolute bg-[rgba(0,0,0,0.2)] z-10"></span>
							<svg class="relative z-20 w-[36px] md:w-[64px]" width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
								<rect width="64" height="64" rx="32" fill="#FF00A3" />
								<path d="M24.4668 43.3021V20.6986C24.4668 20.0273 24.6946 19.4763 25.1502 19.0457C25.6057 18.6155 26.1374 18.4004 26.7451 18.4004C26.9349 18.4004 27.134 18.4282 27.3423 18.4837C27.5506 18.5388 27.7495 18.6218 27.9389 18.7329L45.5314 30.0588C45.8767 30.297 46.1355 30.5822 46.3078 30.9145C46.4805 31.2472 46.5668 31.6092 46.5668 32.0004C46.5668 32.3916 46.4805 32.7535 46.3078 33.0862C46.1355 33.4185 45.8767 33.7038 45.5314 33.942L27.9389 45.2679C27.749 45.3789 27.5495 45.462 27.3404 45.5171C27.1317 45.5726 26.9324 45.6004 26.7426 45.6004C26.1344 45.6004 25.6032 45.3853 25.1489 44.9551C24.6942 44.5245 24.4668 43.9735 24.4668 43.3021Z" fill="white" />
							</svg>
						</a>
					</div>
					<div class="flex flex-col items-start gap-[10px]">
						<div class="font-inter mb-[11px] text-[rgba(0,0,0,0.7)]">
							<?php
							$my_excerpt = get_the_excerpt();
							if (has_excerpt()) {
								echo wpautop($my_excerpt);
							} ?>
						</div>
						<?php
						$price_html = tutor_utils()->get_course_price();
						// if ($price_html) {
						// 	echo '<div class="course-price text-black font-inter font-medium text-[22px]">' . $price_html . '</div>';
						// }
						?>
						<div class="flex flex-col items-start gap-[10px] btn-section">
							<div class="w-full md:w-auto">
								<?php
								use Tutor\Models\CourseModel;
								global $is_enrolled;

								$course_id = get_the_ID();
								$is_enrolled = tutor_utils()->is_enrolled($course_id, get_current_user_id());
								$is_privileged_user   = tutor_utils()->has_user_course_content_access();
								$tutor_course_sell_by = apply_filters( 'tutor_course_sell_by', null );
								$is_public            = get_post_meta( $course_id, '_tutor_is_public_course', true ) == 'yes';
								$monetize_by          = tutor_utils()->get_option( 'monetize_by' );
								$is_purchasable       = tutor_utils()->is_course_purchasable();
								$price                = apply_filters( 'get_tutor_course_price', null, $course_id );
								$lesson_url           = tutor_utils()->get_course_first_lesson();

								if ( current_user_can('manage_options') ) {
								    $debug_product_id = function_exists('tutor_utils') ? tutor_utils()->get_course_product_id() : 0;
								    $debug_product    = function_exists('wc_get_product') ? wc_get_product( $debug_product_id ) : null;
								}

								if ( $is_enrolled || $is_privileged_user ) {
								    $user_id             = get_current_user_id();
								    $completion_mode     = CourseModel::MODE_FLEXIBLE;
								    $is_completed_course = tutor_utils()->is_completed_course();
								    $retake_course       = tutor_utils()->can_user_retake_course();
								    $course_progress     = tutor_utils()->get_course_completed_percent( $course_id, 0, true );
								    $completed_percent   = $course_progress['completed_percent'];

								    if ( $lesson_url ) {
								        if ( $retake_course && ( CourseModel::MODE_FLEXIBLE === $completion_mode || $is_completed_course ) ) {
								            ?>
								            <button type="button" class="tutor-btn tutor-btn-block tutor-btn-outline-primary start-continue-retake-button tutor-course-retake-button" href="<?php echo esc_url( $lesson_url ); ?>" data-course_id="<?php echo esc_attr( $course_id ); ?>">
								                <?php esc_html_e( 'Retake This Course', 'tutor' ); ?>
								            </button>
								            <?php
								        }
								        $link_text = '';
								        if ( !$is_completed_course) {
								            if ( 0 === (int) $completed_percent ) {
								                $link_text = __( 'Start Learning', 'tutor' );
								            }
								            if ( $completed_percent > 0 && $completed_percent < 100 ) {
								                $link_text = __( 'Continue Learning', 'tutor' );
								            }
								        }
								        if ( strlen( $link_text ) > 0 ) {
								            ?>
								            <a href="<?php echo esc_url( $lesson_url ); ?>" class="tutor-btn tutor-btn-block tutor-btn-primary tutor-mt-20">
								                <?php echo esc_html( $link_text ); ?>
								            </a>
								            <?php
								        }
								    }
								}
								elseif ( $is_public && $is_enrolled) {
									
								    $first_lesson_url = tutor_utils()->get_course_first_lesson( $course_id, tutor()->lesson_post_type );
								    if ( ! $first_lesson_url ) {
								        $first_lesson_url = tutor_utils()->get_course_first_lesson( $course_id );
								    }
								    ?>
								    <a href="<?php echo esc_url( $first_lesson_url ); ?>" class="tutor-btn tutor-btn-primary tutor-btn-lg tutor-btn-block">
								        <?php esc_html_e( 'Start Learning', 'tutor' ); ?>
								    </a>
								    <?php
								}
								elseif ( $is_purchasable && $price && $tutor_course_sell_by ) {
								    tutor_load_template( 'single.course.add-to-cart-' . $tutor_course_sell_by );
								}
								elseif (!is_user_logged_in()) { ?>
									<a href="/dashboard" class="tutor-btn tutor-btn-primary tutor-btn-lg tutor-btn-block tutor-mt-24 tutor-enroll-course-button">
										<?php esc_html_e('Enroll Now', 'tutor'); ?>
									</a>
								<?php } else { ?>
									<form class="tutor-enrol-course-form" method="post">
										<?php wp_nonce_field( tutor()->nonce_action, tutor()->nonce ); ?>
										<input type="hidden" name="tutor_course_id" value="<?php echo esc_attr( $course_id ); ?>">
										<input type="hidden" name="tutor_course_action" value="_tutor_course_enroll_now">
										<button type="submit" class="tutor-btn tutor-btn-primary tutor-btn-lg tutor-btn-block tutor-mt-24 tutor-enroll-course-button tutor-static-loader">
											<?php esc_html_e('Enroll Now', 'tutor'); ?>
										</button>
									</form>
								<?php } ?>
							</div>
						</div>

					</div>
				</div>

				<div class="w-1/2 hidden lg:flex">
					<?php

					$video_info = tutor_utils()->get_video_info();
					$video_url  = tutor_utils()->avalue_dot('source_vimeo', $video_info);

					?>
					<a class="popup-video relative rounded-[16px] h-[346px] w-full flex justify-center items-center overflow-hidden cursor-pointer bg-cover bg-center" href="<?php echo $video_url; ?>" style="background-image: url(<?php echo get_the_post_thumbnail_url(); ?>);">
						<span class="inset-0 absolute bg-[rgba(0,0,0,0.2)] z-10"></span>
						<svg class="relative z-20" width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
							<rect width="64" height="64" rx="32" fill="#FF00A3" />
							<path d="M24.4668 43.3021V20.6986C24.4668 20.0273 24.6946 19.4763 25.1502 19.0457C25.6057 18.6155 26.1374 18.4004 26.7451 18.4004C26.9349 18.4004 27.134 18.4282 27.3423 18.4837C27.5506 18.5388 27.7495 18.6218 27.9389 18.7329L45.5314 30.0588C45.8767 30.297 46.1355 30.5822 46.3078 30.9145C46.4805 31.2472 46.5668 31.6092 46.5668 32.0004C46.5668 32.3916 46.4805 32.7535 46.3078 33.0862C46.1355 33.4185 45.8767 33.7038 45.5314 33.942L27.9389 45.2679C27.749 45.3789 27.5495 45.462 27.3404 45.5171C27.1317 45.5726 26.9324 45.6004 26.7426 45.6004C26.1344 45.6004 25.6032 45.3853 25.1489 44.9551C24.6942 44.5245 24.4668 43.9735 24.4668 43.3021Z" fill="white" />
						</svg>
					</a>
					<script>
						jQuery(document).ready(function($) {
							$('.popup-video').magnificPopup({
								type: 'iframe',
								mainClass: 'mfp-fade',
								removalDelay: 160,
								preloader: false,

								fixedContentPos: false
							});
						});
					</script>
				</div>

			</div>

			<?php
			$blocks_smb = get_field('blocks_smb');
			if ($blocks_smb): ?>
				<div class="flex -mx-[6px] gap-y-2 md:gap-y-3">
					<?php
					$i = 0;
					$colors = [
						'bg-white',
						'bg-white',
						'bg-p2'
					];
					$course_rating = function_exists('tutor_utils') ? tutor_utils()->get_course_rating($course_id) : false;
					$price_info   = function_exists('tutor_utils') ? tutor_utils()->get_raw_course_price($course_id) : null;
					$regular_price = $price_info ? (float) ($price_info->regular_price ?? 0) : 0;
					$sale_price    = $price_info ? (float) ($price_info->sale_price ?? 0) : 0;
					$has_discount  = ($regular_price > 0 && $sale_price > 0 && $sale_price < $regular_price);
					$discount_percent = $has_discount ? round((($regular_price - $sale_price) / $regular_price) * 100) : 0;
					$is_free_course = function_exists('tutor_utils') ? ! tutor_utils()->is_course_purchasable($course_id) : false;
					if ($course_rating && $course_rating->rating_count > 0) :
					    $avg = number_format($course_rating->rating_avg, 1);
					    $count = number_format_i18n($course_rating->rating_count);
					?>
						<div class="grow px-[6px] min-w-[25%]">
							<div class="<?php echo $colors[0]; ?> h-full rounded-[16px] p-[18px] flex flex-col min-h-unset md:min-h-[126px] gap-[14px] justify-center">
								<h3 class="text-black font-inter font-medium text-[20px] leading-5 lg:text-[24px] lg:leading-6">
									★ <?php echo esc_html($avg); ?> out of 5
								</h3>
								<p class="font-inter text-[rgba(0,0,0,0.7)] font-medium">Course rating based on <?php echo esc_html($count); ?> ratings</p>
							</div>
						</div>
					<?php
					endif;
					foreach ($blocks_smb as $row) {
						if (empty($row['title']) && empty($row['description'])) continue;
					?>

						<?php if (($i % count($colors)) === 3 && $is_free_course) { $i++; continue; } ?>
						<div class="grow px-[6px] min-w-[25%]">
							<div class="<?php echo $colors[$i % count($colors)]; ?> h-full rounded-[16px] p-[18px] flex flex-col min-h-unset md:min-h-[126px] gap-[14px] justify-center">
								<h3 class="text-black font-inter font-medium text-[20px] leading-5 lg:text-[24px] lg:leading-6">
									<?php if (($i % count($colors)) === 3) { ?>
										<?php echo $has_discount ? esc_html('-' . $discount_percent . '%') : (isset($row['title']) ? $row['title'] : ''); ?>
									<?php } else { ?>
										<?php echo $row['title']; ?>
									<?php } ?>
								</h3>
								<p class="font-inter text-[rgba(0,0,0,0.7)] font-medium">
									<?php echo $row['description']; ?>
									<?php /*if ($i % count($colors) === 2 && $timer_duration_seconds > 0): ?>
										<span class="timer-container mt-2">
											<span id="course-timer-<?php echo $i; ?>" data-duration="<?php echo esc_attr($timer_duration_seconds); ?>" class="font-inter font-bold text-[18px] text-black"></span>
										</span>
									<?php endif; */?>
								</p>
								
							</div>
						</div>

					<?php $i++;
					} ?>

						<?php if(get_field('discount')){ ?>
						<div class="grow px-[6px] min-w-[25%]">
							<div class="bg-p2 h-full rounded-[16px] p-[18px] flex flex-col min-h-unset md:min-h-[126px] gap-[14px] justify-center">
								<h3 class="text-black font-inter font-medium text-[20px] leading-5 lg:text-[24px] lg:leading-6">
									<?php echo get_field('discount'); ?>
								</h3>
								<p class="font-inter text-[rgba(0,0,0,0.7)] font-medium">
									Discount valid for
									<?php if ($timer_duration_seconds > 0): ?>
										<span class="timer-container mt-2">
											<span id="course-timer-<?php echo $i; ?>" data-duration="<?php echo esc_attr($timer_duration_seconds); ?>" class="font-inter font-bold text-[18px] text-black"></span>
										</span>
									<?php endif; ?>
								</p>
								
							</div>
						</div>
						<?php } ?>

				</div>
			<?php endif; ?>

		</div>

	</div>
</section>

<?php
$what_learn_smb = get_field('what_learn_smb');
if ($what_learn_smb): ?>
	<section class="s-what px-4 pt-10 lg:pt-0">
		<div class="container m-auto">

			<div class="rounded-[20px] md:rounded-[32px] py-[24px] px-[16px] md:p-[32px] border border-solid border-[#E8E8E8]">
				<div class="flex justify-between items-center">
					<?php $toggle_text = get_field('more__less_text');
					$toggle_labels = explode('/', $toggle_text, 2);
					$show_text = isset($toggle_labels[0]) ? $toggle_labels[0] : 'Показать ещё';
					$hide_text = isset($toggle_labels[1]) ? $toggle_labels[1] : 'Скрыть';
					?>
					<h2 class="font-manrope text-[32px] leading-[40px] font-medium"><?php echo get_field('what_learn_title'); ?></h2>
					<span class="toggle flex gap-2 text-[#5C77FF] text-[18px] font-semibold cursor-pointer group"><span class="hidden md:flex toggle-label"><?php echo esc_html($show_text); ?></span><svg class="group-[.active]:rotate-180" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M24 12L16 20L8 12" stroke="black" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
						</svg></span>
				</div>
				<ul class="toggle-content flex flex-wrap gap-x-10 -mb-[16px] md:-mb-[22px] mt-[8px] md:mt-[18px]">
					<?php
					$count = count($what_learn_smb);
					$i = 0;
					foreach ($what_learn_smb as $row):
						$i++;
						$is_penultimate = ($count % 2 === 0 && $i === $count - 1);
						$hidden_class = $i > 4 ? ' hidden' : '';
					?>
						<li class="w-full md:w-[calc(50%-20px)] relative pl-9 py-4 md:py-[22px] border-unset md:border-b border-solid border-[#EDEDED] <?php echo $is_penultimate ? 'md:nth-last-2:border-b-0' : ''; ?> last:border-b-0<?php echo $hidden_class; ?>">
							<svg class="absolute left-0 top-[25px]" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M16.6663 5L7.49967 14.1667L3.33301 10" stroke="#5C77FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
							<p class="font-inter text-[rgba(0,0,0,0.7)]"><?php echo $row['what_learn']; ?></p>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

		</div>
	</section>
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			const toggle = document.querySelector(".toggle");
			const content = document.querySelector(".toggle-content");
			const label = document.querySelector(".toggle-label");
			if (toggle && content) {
				toggle.addEventListener("click", function() {
					const items = content.querySelectorAll('li');
					const expanded = toggle.classList.toggle('active');
					for(let i=4;i<items.length;i++){
						items[i].classList.toggle('hidden', !expanded);
					}
					if(label) {
						label.textContent = expanded ? "<?php echo esc_js($hide_text); ?>" : "<?php echo esc_js($show_text); ?>";
					}
				});
			}
		});
	</script>
<?php endif; ?>

<section class="s-include pt-[22px] pb-[40px] px-4">
	<div class="container m-auto py-5 px-4 md:p-6">
		<h2 class="font-manrope text-[32px] leading-[40px] font-medium mb-[32px] md:mb-10">This course includes:</h2>
		<?php
		if ( function_exists( 'tutor_load_template' ) ) {
			tutor_load_template( 'single.course.course-entry-box' );
		}
		?>
	</div>
</section>

<?php if(get_field('ai_title_aib', 'option') && get_field('ai_image_aib', 'option')) { ?>
<section class="course-ai py-10 md:py-0 px-4">
	<div class="container m-auto">

		<div class="course-ai-box bg-black rounded-[20px] md:rounded-[32px] py-5 px-4 md:py-10 md:px-10 flex flex-wrap lg:flex-nowrap gap-10 xl:gap-[200px] items-center">

			<?php if(get_field('ai_image_aib', 'option')) { ?>
			<div class="w-full md:min-w-[539px]">
				<img class="rounded-[12px] w-full min-h-[364px] max-h-[444px] object-cover" src="<?php echo get_field('ai_image_aib', 'option'); ?>" alt="">
			</div>
			<?php } ?>

			<div class="min-w-[35%]">
				<?php if(get_field('ai_title_aib', 'option')) { ?>
				<h2 class="font-manrope capitalize text-white text-[32px] leading-[40px] font-medium mb-8"><?php echo get_field('ai_title_aib', 'option'); ?></h2>
				<?php } ?>
				<?php
				$ai_list_aib = get_field('ai_list_aib', 'option');
				if ($ai_list_aib): ?>
					<ul class="flex flex-col gap-[20px] mb-8 md:mb-10">
						<?php foreach ($ai_list_aib as $row): ?>
							<li class="flex items-center gap-4">
								<img class="w-[20px] md:w-[24px]" src="<?php echo get_stylesheet_directory_uri(); ?>/src/img/check-white.svg" alt="">
								<p class="font-inter text-[18px] md:text-[20px] text-white/70"><?php echo $row['list']; ?></p>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php if(get_field('ai_text_button_aib', 'option')) { ?>
				<a class="w-full md:w-auto justify-center inline-flex bg-p2 rounded-[8px] py-3 px-6 text-black font-inter font-medium cursor-pointer hover:opacity-70 transition"><?php echo get_field('ai_text_button_aib', 'option'); ?></a>
				<?php } ?>
			</div>

		</div>

	</div>
</section>
<?php } ?>

<section class="content-course py-10 md:py-12 px-4">
	<div class="container m-auto">

		<div class="content-course-box bg-[#FBFBFB] rounded-[20px] md:rounded-[32px] py-5 px-4 md:p-10">
			<h2 class="font-manrope text-[32px] leading-[40px] font-medium mb-5 md:mb-10">Course content</h2>

			<?php
			$course_id = get_the_ID();
			$topics = function_exists('tutor_utils') ? tutor_utils()->get_topics($course_id) : false;
			$course_duration = tutor_utils()->get_course_duration($course_id, false);
			$course_duration_clean = str_replace(' sec', '', $course_duration);

			$total_topics   = 0;
			$total_lectures = 0;
			$total_duration = 0;

			if ($topics && $topics->have_posts()) {
				while ($topics->have_posts()) {
					$topics->the_post();
					$total_topics++;

					$topic_id = get_the_ID();
					$topic_lessons = tutor_utils()->get_course_contents_by_topic($topic_id, -1);

					if ($topic_lessons && $topic_lessons->have_posts()) {
						$total_lectures += $topic_lessons->found_posts;

						while ($topic_lessons->have_posts()) {
							$topic_lessons->the_post();
							$video = tutor_utils()->get_video_info();
							if ($video && isset($video->duration_sec)) {
								$total_duration += $video->duration_sec;
							}
						}
						$topic_lessons->rewind_posts();
					}
				}
				wp_reset_postdata();
			}
			?>

			<div class="flex flex-col md:flex-row items-left md:items-center justify-between gap-4 mb-8">
				<?php if ($total_topics > 0): ?>
					<div class="course-meta font-inter font-medium text-black/70 capitalize text-[14px] md:text-[20px] flex gap-3">
						<img class="w-[20px] md:w-[24px]" src="<?php echo get_stylesheet_directory_uri(); ?>/src/img/check-black.svg" alt="">
						<?php echo $total_topics; ?> sections •
						<?php echo $total_lectures; ?> lectures •
						<?php echo esc_html($course_duration_clean ? $course_duration_clean : 'No Data'); ?> total length
					</div>
				<?php endif; ?>
				<span class="expland-all cursor-pointer ont-inter font-semibold underline md:no-underline text-black md:text-[#5C77FF] capitalize text-[18px]">Expand All service</span>
			</div>

			<div class="topic-item-all">
<?php if ($topics && $topics->have_posts()) : ?>
    <?php while ($topics->have_posts()) : $topics->the_post(); ?>
        <?php
        $topic_id = get_the_ID();
        $topic_title = get_the_title();
        $topic_lessons = function_exists('tutor_utils') ? tutor_utils()->get_course_contents_by_topic($topic_id, -1) : false;
        if ( ! $topic_lessons ) {
            $post_types = array(
                function_exists('tutor') ? tutor()->lesson_post_type : 'lesson',
                'tutor_quiz',
                'tutor_assignments',
                'tutor_zoom_meeting',
                'tutor-google-meet'
            );
            $topic_lessons = new WP_Query(array(
                'post_type'      => $post_types,
                'post_parent'    => $topic_id,
                'orderby'        => 'menu_order',
                'order'          => 'ASC',
                'posts_per_page' => -1,
                'post_status'    => array('publish','private','acf-disabled'),
                'no_found_rows'  => true,
            ));
        }
        $is_topic_active = false;
        ?>

						<div class="topic-item overflow-hidden first:rounded-tl-[24px] first:rounded-tr-[24px] last:rounded-bl-[24px] last:rounded-br-[24px]">
							<div class="topic-header <?php echo $is_topic_active ? 'active' : ''; ?> bg-[#F4F4F4] p-4 md:py-5 md:px-[34px] cursor-pointer group">
								<div class="flex items-center justify-between">
									<div class="topic-title flex items-center gap-2 md:gap-3 font-inter font-medium text-[16px] md:text-[20px]"><svg class="group-[.active]:rotate-180 transition w-[24px] md:w-[32px]" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M8 12L16 20L24 12" stroke="black" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
										</svg><?php echo esc_html($topic_title); ?></div>
									<div class="topic-meta font-inter font-medium">
										<?php echo $topic_lessons ? $topic_lessons->found_posts : 0; ?> lectures •
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
											echo '';
										} else {
											if ($topic_duration > 0) {
												echo esc_html(format_seconds_to_time($topic_duration));
											} else {
												echo '';
											}
										}
										?>
									</div>
								</div>
							</div>

							<div class="lesson-list <?php echo $is_topic_active ? 'active' : ''; ?> hidden overflow-hidden [.active]:block">
								<?php if ($topic_lessons && $topic_lessons->have_posts()) : ?>
									<?php while ($topic_lessons->have_posts()) : $topic_lessons->the_post(); ?>
										<?php
										$current_lesson_id = get_the_ID();
										$is_current_lesson = false;
										$post_type = get_post_type($current_lesson_id);
										$video = function_exists('tutor_utils') ? tutor_utils()->get_video_info() : false;
										$has_video = $video && !empty($video->playtime) && $video->playtime !== '00:00';
										$duration = $has_video ? $video->playtime : '';
										$is_preview = (bool) get_post_meta($current_lesson_id, '_is_preview', true);
										$is_completed = false;
										$is_user_logged_in = is_user_logged_in();
										
										if ($is_user_logged_in && function_exists('tutor_utils')) {
											if ($post_type === 'tutor_quiz') {
												if (class_exists('Tutor\Models\QuizModel')) {
													$is_completed = \Tutor\Models\QuizModel::is_quiz_passed($current_lesson_id, get_current_user_id());
												}
											} else {
												$is_completed = tutor_utils()->is_completed_lesson($current_lesson_id);
											}
										}
										
										$lesson_title_class = 'lesson-title font-inter font-medium';
										$icon_color = '#666666';
										
										if ($is_completed && $is_user_logged_in) {
											$lesson_title_class .= ' text-green-500';
											$icon_color = '#22c55e';
										} elseif ($is_preview) {
											$lesson_title_class .= ' text-[#5C77FF]';
											$icon_color = '#5C77FF';
										}
										
										$icon_type = 'video';
										if ($post_type === 'tutor_quiz') {
											$icon_type = 'quiz';
										} elseif (!$has_video) {
											$icon_type = 'document';
										}
										?>

										<a href="<?php echo esc_url(get_permalink($current_lesson_id)); ?>" class="lesson-item <?php echo $is_current_lesson ? 'active' : ''; ?> flex items-center justify-between bg-white px-3 py-5 md:px-[34px]">
											<div class="lesson-info flex items-center gap-2 md:gap-4">
												<?php if ($icon_type === 'quiz'): ?>
													<svg class="lesson-icon w-[18px] md:w-[20px]" fill="<?php echo esc_attr($icon_color); ?>" viewBox="0 0 20 20">
														<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
													</svg>
												<?php elseif ($icon_type === 'document'): ?>
													<svg class="lesson-icon w-[18px] md:w-[20px]" fill="<?php echo esc_attr($icon_color); ?>" viewBox="0 0 20 20">
														<path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
													</svg>
												<?php else: ?>
													<svg class="lesson-icon w-[18px] md:w-[20px]" fill="<?php echo esc_attr($icon_color); ?>" viewBox="0 0 20 20">
														<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
													</svg>
												<?php endif; ?>
												<span class="<?php echo esc_attr($lesson_title_class); ?>"><?php the_title(); ?></span>
											</div>
											<?php if ($duration !== ''): ?>
											<span class="lesson-duration font-inter font-medium"><?php echo esc_html($duration); ?></span>
											<?php endif; ?>
										</a>
									<?php endwhile; ?>
									<?php wp_reset_postdata(); ?>
								<?php endif; ?>
							</div>
						</div>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
				<?php endif; ?>
			</div>

		</div>

	</div>
</section>
<script>
	document.addEventListener("DOMContentLoaded", function() {
		const headers = document.querySelectorAll(".topic-header");

		headers.forEach(header => {
			header.addEventListener("click", function() {
				const parent = header.closest(".topic-item");
				const list = parent.querySelector(".lesson-list");

				document.querySelectorAll(".lesson-list.active").forEach(openList => {
					if (openList !== list) {
						openList.classList.remove("active");
						openList.style.maxHeight = null;
						openList.previousElementSibling.classList.remove("active");
						openList.classList.add("hidden");
					}
				});

				if (list.classList.contains("active")) {
					list.classList.remove("active");
					list.style.maxHeight = null;
					header.classList.remove("active");
					list.classList.add("hidden");
				} else {
					list.classList.add("active");
					list.classList.remove("hidden");
					list.style.maxHeight = 'none';
					header.classList.add("active");
				}
			});
		});
		const firstHeader = document.querySelector(".topic-item:first-child .topic-header");
		if (firstHeader) {
			firstHeader.click();
		}

		const expandAllBtn = document.querySelector(".expland-all");

if (expandAllBtn) {
    expandAllBtn.addEventListener("click", function() {
        const topics = document.querySelectorAll(".topic-item");
        const allOpen = Array.from(topics).every(topic => {
            const header = topic.querySelector(".topic-header");
            const lessonList = topic.querySelector(".lesson-list");
            return header?.classList.contains("active") && lessonList?.classList.contains("active");
        });

        topics.forEach(topic => {
            const header = topic.querySelector(".topic-header");
            const lessonList = topic.querySelector(".lesson-list");

            if (header && lessonList) {
                if (allOpen) {
                    header.classList.remove("active");
                    lessonList.classList.remove("active");
                    lessonList.style.maxHeight = null;
                    lessonList.classList.add("hidden");
                } else {
                    header.classList.add("active");
                    lessonList.classList.add("active");
                    lessonList.classList.remove("hidden");
                    lessonList.style.maxHeight = 'none';
                }
            }
        });
    });
}

	});
</script>

<?php if(get_field('title_smc') && get_field('image_smc')) { ?>
<section class="course-half py-10 md:py-12 px-4">
	<div class="container m-auto">

		<div class="course-half-box flex text-center justify-center lg:text-left flex-wrap lg:flex-nowrap gap-10 md:gap-20 items-center">

			<div class="lg:p-10">
				<h2 class="font-manrope text-[32px] leading-[40px] font-medium mb-4"><?php echo get_field('title_smc'); ?></h2>
				<div class="font-inter font-medium text-[16px] md:text-[20px] text-black/80"><?php echo get_field('content_smc'); ?></div>
			</div>

			<div class="md:min-w-[500px]">
				<img class="w-full" src="<?php echo get_field('image_smc'); ?>" alt="">
			</div>

		</div>

	</div>
</section>
<?php } ?>

<?php if(get_field('title_certificate_aib', 'option') && get_field('image_certificate_aib', 'option')) { ?>
<section class="course-certificate py-10 md:py-12 px-4">
	<div class="container m-auto">

		<div class="course-certificate-box-wrap flex rounded-[32px] xl:p-10 xl:bg-[#F5F5F5]">
			<div class="course-certificate-box flex flex-col-reverse lg:flex-row rounded-[22px] md:rounded-[32px] p-4 md:p-10 gap-[54px] xl:gap-[146px] flex-wrap lg:flex-nowrap items-center bg-[#5C77FF] relative">
				<span class="block inset-0 absolute z-10 bg-cover w-full h-full" style="background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/src/img/bg-cert.svg); content: '';"></span>

				<div class="md:min-w-[510px] relative z-20">
					<img class="rounded-[16px]" src="<?php echo get_field('image_certificate_aib', 'option'); ?>" alt="">
				</div>

				<div class="relative z-20">
					<h2 class="max-w-[200px] md:max-w-full font-manrope text-[32px] text-white leading-[40px] font-medium mb-4"><?php echo get_field('title_certificate_aib', 'option'); ?></h2>
					<div class="font-inter font-medium text-[16px] md:text-[20px] text-white/70"><?php echo get_field('description_certificate_aib', 'option'); ?></div>
				</div>

			</div>
		</div>

	</div>
</section>
<?php } ?>

<?php
$course_id = get_the_ID();
$author_id = (int) get_post_field('post_author', $course_id);
$author_avatar = get_avatar($author_id, 230, '', '', array('class' => 'w-full max-h-[228px] object-cover'));
$author_name   = get_the_author_meta('display_name', $author_id);

$author_bio = get_user_meta($author_id, '_tutor_profile_bio', true);
$job_title = get_user_meta($author_id, '_tutor_profile_job_title', true);

if (empty($author_bio)) {
	$author_bio = get_the_author_meta('description', $author_id);
}

$tags = get_field('tags_aurt', 'user_' . $author_id);
$socials = get_field('social_aut', 'user_' . $author_id);
$joined = get_field('joined', 'user_' . $author_id);

if ($author_id) : ?>
	<section class="course-author py-10 md:py-12 px-4">
		<div class="container m-auto">
			<div class="course-author-box flex rounded-[20px] p-4 md:p-[20px] gap-[20px] flex-wrap lg:flex-nowrap items-start bg-[#F7F7F7]">

				<div class="min-w-full md:min-w-[229px] rounded-[12px] overflow-hidden">
					<?php echo $author_avatar; ?>
				</div>

				<div class="course-author-right w-full">

					<div class="flex items-center justify-between w-full flex-wrap gap-2 mb-4 md:mb-3">
						<h2 class="font-manrope text-[32px] text-black leading-[40px] font-medium">
							<?php echo esc_html($author_name); ?>
						</h2>
						<span class="toggle-author flex md:hidden items-center gap-2 font-inter font-bold text-[18px] text-[#5C77FF]"><span>Show More</span> <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M18 15.5459L12 9.5459L6 15.5459" stroke="black" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
							</svg></span>
					</div>

					<div class="flex items-center gap-3 mb-3">
						<?php if ($tags) { ?>
							<ul class="flex items-center gap-2">
								<?php foreach ($tags as $row) { ?>
									<li class="bg-[#E8E6FF] py-[3px] px-4 rounded-[8px] font-inter font-medium text-[12px]"><?php echo $row['tags']; ?></li>
								<?php } ?>
							</ul>
						<?php } ?>
						<?php if (! empty($job_title)) : ?>
							<div class="tutor-job-title font-inter text-[14px] text-black/70 capitalize">
								<?php echo wp_kses_post(wpautop($job_title)); ?>
							</div>
						<?php endif; ?>
					</div>

					<div class="flex items-center gap-1 mb-5">
						<?php if ($tags) { ?>
							<a class="flex gap-1 bg-[#443EDE] rounded-[8px] py-[4.5px] px-4 text-white font-inter font-medium text-[12px]" href="#"><svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M9 4.25V14.75M3.75 9.5H14.25" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
								</svg>Follow</a>
							<div class="flex items-center gap-2">
								<?php foreach ($socials as $row) { ?>
									<a class="bg-[#E8E6FF] rounded-[8px] h-[27px] w-[27px] flex items-center justify-center" href="<?php echo $row['link']; ?>"><?php echo $row['icon']; ?></a>
								<?php } ?>
							</div>
						<?php } ?>
					</div>

					<?php if (! empty($joined)) : ?>
						<div class="tutor-author-joined font-inter text-[14px] md:text-[16px] text-black/70 mb-5 md:mb-6">
							<?php echo wpautop(wp_kses_post($joined)); ?>
						</div>
					<?php endif; ?>

					<?php if (! empty($author_bio)) : ?>
						<div class="tutor-author-bio">
							<div class="font-inter text-[16px] text-black/70 capitalize">
								<?php echo wp_kses_post(wpautop($author_bio)); ?>
							</div>
						</div>
					<?php endif; ?>

				</div>

			</div>
		</div>
	</section>
	<style>
		.tutor-author-bio p:not(:last-child) {
			margin-bottom: 16px;
		}

		.course-author-right .toggle-author svg {
			transform: rotate(180deg);
		}

		.course-author-right.show .toggle-author svg {
			transform: rotate(0deg);
		}

		.course-author-right.show>div:not(:first-child) {
			display: flex;
		}

		@media (max-width: 768px) {
			.course-author-right>div:not(:first-child) {
				display: none;
			}
		}
	</style>
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			const toggle = document.querySelector(".toggle-author");
			const authorRight = document.querySelector(".course-author-right");
			const span = toggle.querySelector("span");

			toggle.addEventListener("click", function() {
				authorRight.classList.toggle("show");

				if (authorRight.classList.contains("show")) {
					span.textContent = "Show Less";
				} else {
					span.textContent = "Show More";
				}
			});
		});
	</script>
<?php endif; ?>

<?php if(get_field('title_wit') && get_field('image_wit')) { ?>
<section class="course-half py-10 md:py-12 px-4">
	<div class="container m-auto">

		<div class="course-half-box flex flex-col-reverse lg:flex-row gap-4 lg:gap-28 flex-wrap lg:flex-nowrap items-left lg:items-center">

			<div class="">
				<h2 class="hidden lg:flex font-manrope text-[32px] leading-[40px] font-medium mb-4"><?php echo get_field('title_wit'); ?></h2>
				<div class="font-inter text-center md:text-left font-medium text-[16px] md:text-[20px] text-black/80 mb-6 md:mb-8"><?php echo get_field('content_wit'); ?></div>
				<?php if(get_field('text_button_wit')) { ?>
				<a class="w-full md:w-auto justify-center inline-flex bg-p2 rounded-[8px] py-3 px-6 text-black font-inter font-medium cursor-pointer hover:opacity-70 transition" href="<?php echo get_field('link_button_wit'); ?>"><?php echo get_field('text_button_wit'); ?></a>
				<?php } ?>
			</div>

			<div class="min-w-full lg:min-w-[664px]">
				<h2 class="flex lg:hidden font-manrope text-center md:text-left text-[32px] leading-[40px] font-medium mb-4"><?php echo get_field('title_wit'); ?></h2>
				<img src="<?php echo get_field('image_wit'); ?>" alt="">
			</div>

		</div>

	</div>
</section>
<?php } ?>

<?php
$immediately_blocks = get_field('immediately_blocks', 'option');
if ($immediately_blocks): ?>
	<section class="course-immediately py-10 md:py-12 px-4">
		<div class="container m-auto">

			<div class="course-immediately-box flex flex-col rounded-[12px] md:rounded-[32px] py-5 px-4 md:py-10 md:px-[34px] gap-8 md:gap-14 bg-black">

				<h2 class="font-manrope text-[32px] text-white leading-[40px] font-medium max-w-[540px]"><?php echo get_field('immediately_title', 'option'); ?></h2>

				<div class="flex gap-2 md:gap-4 flex-wrap md:flex-nowrap">
					<?php
					foreach ($immediately_blocks as $row): ?>
						<div class="flex flex-row md:flex-col items-start md:items-center p-4 md:p-5 gap-3 md:gap-4 text-center bg-[#272727] rounded-[24px]">
							<img class="mb-4 max-h-[72px] md:max-h-[110px] lg:max-h-[196px]" src="<?php echo $row['icon']; ?>" alt="">
							<div class="flex flex-col items-start md:items-center gap-4 text-left md:text-center">
								<h3 class="font-manrope text-[20px] lg:text-[24px] leading-[32px] text-white font-medium max-w-full md:max-w-[310px] m-0 md:m-auto"><?php echo $row['title']; ?></h3>
								<p class="text-white/70 text-[12px] md:text-[16px] font-inter font-normal"><?php echo $row['description']; ?></p>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

			</div>

		</div>
	</section>
<?php endif; ?>

<!-- <section class="list-course py-10 md:py-12 px-4">
	<div class="container m-auto">

		<h2 class="font-manrope text-[32px] leading-[40px] font-medium mb-10 max-w-[530px] m-auto text-left md:text-center">Immediately after purchasing the course you will receive</h2>

	</div>
</section> -->

<?php
$feedbacks = get_field('feedbacks', 'option');
if ($feedbacks): ?>
	<section class="feedback-course p-0 md:p-5 overflow-x-hidden">
		<div class="feedback-course-wrap bg-[#F6F6F6] rounded-[20px] md:rounded-[36px] py-10 md:py-25 pl-4 md:pl-10 overflow-x-hidden">

			<h2 class="pr-4 md:pr-10 font-manrope text-[32px] leading-[40px] xl:text-[56px] xl:leading-[69px] font-medium mb-4 max-w-[665px] m-auto text-left md:text-center"><?php echo get_field('title_gfb', 'option'); ?></h2>
			<div class="pr-4 md:pr-10 text-black/60 font-inter font-medium mb-8 md:mb-12.5 max-w-[665px] m-auto text-left md:text-center"><?php echo get_field('description_gfb', 'option'); ?></div>

			<div class="feedback-slider-wrap overflow-x-hidden">
				<div class="feedback-slider">
					<?php foreach ($feedbacks as $row): ?>
						<div class="py-[33px] px-[26px] rounded-[24px] bg-white flex flex-col gap-8 justify-between">
							<div class="flex flex-col gap-8">
								<img class="rounded-full w-[60px] h-[60px]" src="<?php echo $row['photo_gfb']; ?>" alt="">
								<p class="text-[#4D4D4D] font-inter font-medium"><?php echo $row['feedback']; ?></p>
							</div>
							<div class="flex flex-col gap-1">
								<h2 class="font-manrope text-black text-[20px] leading-[24px] font-medium"><?php echo $row['name']; ?></h2>
								<p class="text-black/70 font-inter"><?php echo $row['position']; ?></p>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="slider-arrows overflow-x-hidden flex justify-center gap-5 mt-8 md:mt-12.5"></div>
			</div>

		</div>
	</section>
	<script>
		jQuery(document).ready(function($) {
			$('.feedback-slider').slick({
				slidesToShow: 3.8,
				slidesToScroll: 1,
				infinite: false,
				arrows: true,
				appendArrows: $('.slider-arrows'),
				prevArrow: `
    <button type="button" class="slick-prev">
      <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="-0.5" y="0.5" width="47" height="47" rx="7.5" transform="matrix(-1 0 0 1 47 0)" fill="black"/>
        <rect x="-0.5" y="0.5" width="47" height="47" rx="7.5" transform="matrix(-1 0 0 1 47 0)" stroke="black"/>
        <path d="M32 24H16M16 24L22 18M16 24L22 30" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>`,
				nextArrow: `
    <button type="button" class="slick-next">
      <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="0.5" y="0.5" width="47" height="47" rx="7.5" fill="black"/>
        <rect x="0.5" y="0.5" width="47" height="47" rx="7.5" stroke="black"/>
        <path d="M16 24H32M32 24L26 18M32 24L26 30" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>`,
				responsive: [{
						breakpoint: 1200,
						settings: {
							slidesToShow: 3.3,
						}
					},
					{
						breakpoint: 992,
						settings: {
							slidesToShow: 2.3,
						}
					},
					{
						breakpoint: 768,
						settings: {
							slidesToShow: 1.3,
						}
					},
					{
						breakpoint: 480,
						settings: {
							slidesToShow: 1.2,
						}
					}
				]
			});
		});
	</script>
	<style>
		.feedback-slider-wrap .slick-slide {
			margin: 0 8px;
			height: fit-content;
			display: flex;
		}

		.feedback-slider-wrap .slick-list {
			margin: 0 -8px;
		}

		.feedback-slider-wrap .slick-arrow {
			position: static;
			margin: 0;
			transform: none;
			width: unset;
			height: unset;
		}

		.feedback-slider-wrap .slick-arrow:hover {
			opacity: 0.7;
		}

		.feedback-slider-wrap .slick-arrow::before,
		.feedback-slider-wrap .slick-arrow::before {
			content: none;
		}
	</style>
<?php endif; ?>

<?php
$faqs = get_field('faqs', 'option');
if ($faqs): ?>
	<section class="faqs-course py-10 md:py-12 px-4">
		<div class="container m-auto">

			<h2 class="font-manrope text-[32px] leading-[40px] font-medium mb-8 md:mb-12.5 max-w-[530px] m-auto text-center"><?php echo get_field('title_gfq', 'option'); ?></h2>

			<ul class="faqs-wrap flex flex-wrap gap-x-4.5 gap-y-[11px] -mb-[10px] mt-[11px]">
				<?php foreach ($faqs as $row): ?>
					<li class="w-full md:w-[calc(50%-10px)]">
						<div class="faqs-one rounded-[16px] overflow-hidden bg-[#F6F6F6] group">
							<div class="faqs-one-q px-6 py-[18px] gap-4 flex items-center justify-between cursor-pointer">
								<h3 class="font-inter text-[18px] leading-[24px] font-semibold max-w-[400px]"><?php echo $row['question']; ?></h3>
								<svg class="group-[.active]:-rotate-90 transform-gpu min-w-[17px]" width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M9.7412 8.08225L6.0842 4.42525C6.01111 4.3587 5.95212 4.27815 5.91072 4.18839C5.86931 4.09863 5.84634 4.00147 5.84316 3.90267C5.83997 3.80387 5.85664 3.70543 5.89218 3.61319C5.92772 3.52095 5.9814 3.43677 6.05005 3.36565C6.1187 3.29452 6.20092 3.23789 6.29185 3.19911C6.38277 3.16033 6.48056 3.14018 6.57941 3.13987C6.67826 3.13955 6.77617 3.15907 6.86734 3.19727C6.95851 3.23547 7.0411 3.29157 7.1102 3.36225L7.1282 3.38025L11.3082 7.55925C11.4468 7.69784 11.5246 7.88578 11.5246 8.08175C11.5246 8.27772 11.4468 8.46567 11.3082 8.60425L7.1292 12.7833C7.06177 12.8531 6.98125 12.9089 6.89224 12.9476C6.80322 12.9862 6.70747 13.007 6.61043 13.0087C6.51339 13.0104 6.41698 12.9929 6.32669 12.9573C6.23639 12.9217 6.154 12.8687 6.0842 12.8013C6.0144 12.7338 5.95857 12.6533 5.91989 12.5643C5.88121 12.4753 5.86045 12.3795 5.85877 12.2825C5.8571 12.1854 5.87456 12.089 5.91015 11.9987C5.94574 11.9084 5.99877 11.8261 6.0662 11.7563L6.0842 11.7383L9.7412 8.08225Z" fill="#6F1DF4" stroke="#6F1DF4" stroke-width="0.5" />
								</svg>
							</div>
							<div class="faqs-one-a px-6 pb-[30px] font-inter text-[rgba(0,0,0,0.7)] hidden"><?php echo $row['answer']; ?></div>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>

		</div>
	</section>

	<?php
	
	$left_side = get_field('left_side', 'option');
	$right_side = get_field('right_side', 'option');
	
	$title_left = $left_side['title'];
	$discount_text = $left_side['discount_text'];
	$header_color = $left_side['header_color'];
	$body_discount_color = $left_side['body_discount_color'];
	$discount_percent_color = $left_side['discount_percent_color'];
	$header_title = $left_side['header_title'];
	$after_price_description = $left_side['after_price_description'];
	$label = $left_side['label'];

	$title_right = $right_side['title'];
	$form_shortcode = $right_side['form_shortcode'];
	$form_description = $right_side['form_description'];
	?>

	<section class="mb-5">
		<div class="px-5">
			<div class="flex gap-5 justify-between bg-[#F6F6F6] py-[100px] px-[91px] rounded-[36px] items-start">
				<div class="bg-white border border-solid border-[rgba(0,0,0,0.1)] rounded-[24px] p-10 w-1/2">
					<div class="flex justify-between gap-10">
						<h3 class="font-roboto text-[32px] leading-[40px] font-bold text-black capitalize"><?php echo $title_left; ?></h3>
						<div class="course-timer flex flex-col gap-[4px]">
							<h5 class="font-inter text-[16px] leading-[20px] font-medium text-black"><?php echo $discount_text; ?></h5>
							<div class="timer-course">
								<?php if ($timer_duration_seconds > 0): ?>
									<span class="timer-container flex justify-end">
										<span id="course-timer-bottom" data-duration="<?php echo esc_attr($timer_duration_seconds); ?>" class="font-inter font-bold text-[18px] text-black"></span>
									</span>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<div class="w-full flex flex-col rounded-[12px] overflow-hidden mt-8">
						<div class="py-[10px] bg-[<?php echo $header_color; ?>] flex justify-center items-center">
							<h4 class="font-roboto text-[18px] leading-[26px] font-medium text-black capitalize"><?php echo $header_title; ?></h4>
						</div>
						<div class="p-5 pb-[47px] bg-[<?php echo $body_discount_color; ?>] flex justify-between items-start">
							<?php
							$course_id = get_the_ID();
							$price_info = function_exists('tutor_utils') ? tutor_utils()->get_raw_course_price($course_id) : null;
							$regular_price = $price_info ? (float) ($price_info->regular_price ?? 0) : 0;
							$sale_price = $price_info ? (float) ($price_info->sale_price ?? 0) : 0;
							$has_discount = ($regular_price > 0 && $sale_price > 0 && $sale_price < $regular_price);
							$discount_percent = $has_discount ? round((($regular_price - $sale_price) / $regular_price) * 100) : 0;
							
							if (function_exists('wc_price')) {
								$formatted_regular_price = $regular_price > 0 ? wc_price($regular_price) : '';
								$formatted_sale_price = $sale_price > 0 ? wc_price($sale_price) : '';
							} else {
								$currency_symbol = function_exists('get_woocommerce_currency_symbol') ? get_woocommerce_currency_symbol() : '$';
								$formatted_regular_price = $regular_price > 0 ? $currency_symbol . number_format($regular_price, 2) : '';
								$formatted_sale_price = $sale_price > 0 ? $currency_symbol . number_format($sale_price, 2) : '';
							}
							?>
							<div class="flex flex-col gap-1">
								<?php if ($regular_price > 0 && $has_discount): ?>
									<style>
										.amt span{
											display: inline-flex;
											flex-direction: row-reverse;
										}
									</style>
									<div class="font-inter text-[16px] leading-[20px] font-semibold text-black/70 amt">
										<?php echo function_exists('wc_price') ? wp_kses_post($formatted_regular_price) : esc_html($formatted_regular_price); ?>
									</div>
								<?php elseif ($regular_price > 0 && !$has_discount && $sale_price == 0): ?>
									<div class="font-inter text-[16px] leading-[20px] font-semibold text-black/70 amt">
										<?php echo function_exists('wc_price') ? wp_kses_post($formatted_regular_price) : esc_html($formatted_regular_price); ?>
									</div>
								<?php endif; ?>
								<?php if ($sale_price > 0): ?>
									<div class="font-roboto text-[24px] leading-[32px] font-bold text-black amt">
										<?php echo function_exists('wc_price') ? wp_kses_post($formatted_sale_price) : esc_html($formatted_sale_price); ?>
									</div>
								<?php endif; ?>
								<div class="text-[rgba(0,0,0,0.8)] font-inter text-[12px] leading-[14px] font-normal">
									<?php echo $after_price_description; ?>
								</div>
							</div>
							<?php if ($has_discount && $discount_percent > 0): ?>
								<div class="flex items-center">
									<span class="font-inter text-[14px] rounded-[8px] py-[4px] px-4 leading-[21px] font-medium text-black bg-[<?php echo $discount_percent_color; ?>]">
										-<?php echo esc_html($discount_percent); ?>%
									</span>
								</div>
							<?php endif; ?>
						</div>
					</div>
					
					<style>
							.lis ul {
								list-style-type: disc;
								padding-left: 20px;
							}
						</style>
						<div class="text-black/60 lis mt-8">
							<?php echo $label; ?>
					</div>
				</div>

				<div class="bg-white border border-solid border-[rgba(0,0,0,0.1)] rounded-[24px] p-10 w-1/2">
					<h3 class="font-roboto text-[32px] leading-[40px] font-bold text-black capitalize"><?php echo $title_right; ?></h3>
					<style>
						.form-wrap .wpcf7 input[type="text"],
						.form-wrap .wpcf7 input[type="email"],
						.form-wrap .wpcf7 input[type="tel"],
						.form-wrap .wpcf7 input[type="password"],
						.form-wrap .wpcf7 input[type="number"],
						.form-wrap .wpcf7 input[type="url"],
						.form-wrap .wpcf7 textarea,
						.form-wrap .wpcf7 select {
							width: 100%;
							height: 52px;
							border-radius: 28px;
							background: #fff;
							border: 1px solid #e6e7eb;
							padding: 0 30px;
							box-shadow: none;
							font-size: 14px;
							line-height: 20px;
							color: #1D1F1E;
							font-family: 'Roboto', sans-serif;
							transition: all 150ms ease;
						}
						.form-wrap .wpcf7 input[type="text"]:focus,
						.form-wrap .wpcf7 input[type="email"]:focus,
						.form-wrap .wpcf7 input[type="tel"]:focus,
						.form-wrap .wpcf7 input[type="password"]:focus,
						.form-wrap .wpcf7 input[type="number"]:focus,
						.form-wrap .wpcf7 input[type="url"]:focus,
						.form-wrap .wpcf7 textarea:focus,
						.form-wrap .wpcf7 select:focus {
							outline: none;
							border-color: #000;
						}
						.form-wrap .wpcf7 textarea {
							height: auto;
							min-height: 120px;
							padding: 12px 30px;
							border-radius: 16px;
							resize: vertical;
						}
						.form-wrap .wpcf7 p {
							position: relative;
							margin-bottom: 24px;
						}
						.form-wrap .wpcf7 p:last-of-type {
							margin-bottom: 0;
						}
						
						.form-wrap .wpcf7 input::placeholder,
						.form-wrap .wpcf7 textarea::placeholder {
							color: #6b7280;
							opacity: 1;
						}
						.form-wrap .wpcf7-submit {
							height: 48px;
							border-radius: 48px;
							background: #E8F501;
							color: #000;
							font-size: 16px;
							font-family: 'Roboto', sans-serif;
							font-weight: 500;
							border: none;
							cursor: pointer;
							transition: all 150ms ease;
							margin-top: 0;
							padding: 14px 32px;
						}
						.form-wrap .wpcf7-submit:hover {
							background: #d2de09;
							color: #000;
						}
						.form-wrap .wpcf7-submit:focus {
							outline: none;
						}
						.form-wrap .wpcf7-response-output {
							margin-top: 20px;
							padding: 20px 24px;
							border-radius: 24px;
							font-size: 18px;
							line-height: 28px;
							font-family: 'Roboto', sans-serif;
							font-weight: 400;
						}
						.form-wrap .wpcf7-mail-sent-ok {
							border: 2px solid #8fae1b;
							background: #F6F6F6;
							color: #1D1F1E;
						}
						.form-wrap .wpcf7-validation-errors,
						.form-wrap .wpcf7-mail-sent-ng {
							border: 2px solid #b81c23;
							background: #F6F6F6;
							color: #1D1F1E;
						}
						.form-wrap .wpcf7-spinner {
							display: none;
						}
						.form-wrap .wpcf7 .wpcf7-form-control-wrap {
							display: block;
						}

						.form-wrap .label-tel {
							position: relative;
						}

						.form-wrap .label-tel input {
							padding-left: 60px !important;
						}

						.form-wrap .label-tel svg {
							position: absolute;
							content: "";
							left: 16px;
							top: 35%;
							transform: translateY(-50%);
							z-index: 555;
						}
					</style>
					<div class="form-wrap mt-8">
						<?php echo do_shortcode($form_shortcode); ?>
					</div>
					<div class="text-black/60 font-inter text-[14px] leading-[20px] font-normal mt-3">
						<?php echo $form_description; ?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<script>
		document.addEventListener("DOMContentLoaded", function() {
			const faqs = document.querySelectorAll(".faqs-one");

			faqs.forEach((faq) => {
				const question = faq.querySelector(".faqs-one-q");
				const answer = faq.querySelector(".faqs-one-a");

				question.addEventListener("click", function() {
					answer.classList.toggle("hidden");
					faq.classList.toggle("active");
					faqs.forEach((otherFaq) => {
						if (otherFaq !== faq) {
							otherFaq.classList.remove("active");
							otherFaq.querySelector(".faqs-one-a").classList.add("hidden");
						}
					});
				});
			});

		});
	</script>
<?php endif; ?>

<?php if ($timer_duration_seconds > 0): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			function pad(n) {
				return n < 10 ? '0' + n : n;
			}
			
			var courseTimers = document.querySelectorAll('[id^="course-timer-"], #course-timer-bottom');
			if (courseTimers.length === 0) return;
			
			var duration = <?php echo $timer_duration_seconds; ?>;
			var cookieName = 'timer_start_time';
			var startTime;
			
			var startTimeCookie = getCookie(cookieName);
			if (startTimeCookie) {
				startTime = parseInt(startTimeCookie);
			} else {
				startTime = Math.floor(Date.now() / 1000);
				setCookie(cookieName, startTime.toString(), 7);
			}
			
			function updateCountdown() {
				var now = Math.floor(Date.now() / 1000);
				var elapsed = now - startTime;
				var remaining = duration - (elapsed % duration);

				if (elapsed >= duration && elapsed % duration === 0) {
					startTime = now;
					setCookie(cookieName, startTime.toString(), 7);
					remaining = duration;
				}
				
				var hours = Math.floor(remaining / 3600);
				var minutes = Math.floor((remaining % 3600) / 60);
				var seconds = remaining % 60;
				
				var timeString = pad(hours) + ':' + pad(minutes) + ':' + pad(seconds);
				
				courseTimers.forEach(function(timer) {
					timer.textContent = timeString;
				});
			}

			function setCookie(name, value, days) {
				var expires = "";
				if (days) {
					var date = new Date();
					date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
					expires = "; expires=" + date.toUTCString();
				}
				document.cookie = name + "=" + (value || "") + expires + "; path=/";
			}
			
			function getCookie(name) {
				var nameEQ = name + "=";
				var ca = document.cookie.split(';');
				for (var i = 0; i < ca.length; i++) {
					var c = ca[i];
					while (c.charAt(0) == ' ') c = c.substring(1, c.length);
					if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
				}
				return null;
			}
			
			updateCountdown();
			setInterval(updateCountdown, 1000);
		});
	</script>
<?php endif; ?>

<script>
	document.addEventListener("DOMContentLoaded", function() {
		const btnSection = document.querySelector(".btn-section");
		if (btnSection) {
			function validateBtnTextValue() {
			const btnTextValueWhileTrim = btnSection.querySelector(".tutor-btn").textContent.trim().toLocaleLowerCase();
			if (btnTextValueWhileTrim === "view cart") {
				btnSection.querySelector("a").textContent = "View Checkout";
				}
			}
			validateBtnTextValue();
			setInterval(validateBtnTextValue, 15000);
			
		}
	});
</script>



<?php get_footer(); ?>