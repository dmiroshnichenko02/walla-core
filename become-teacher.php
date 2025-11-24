<?php get_header(); /* Template Name: Become a teacher */ ?>

<section class="py-[27px] md:py-15 px-4">
	<div class="container m-auto">

		<div class="flex flex-wrap md:flex-nowrap justify-between rounded-[22px] md:rounded-[36px] bg-[#E3FFE5]">

			<div class="py-5 md:py-12 px-5 md:px-10 max-w-full md:max-w-[600px]">
				<h1 class="font-manrope text-[32px] leading-[40px] lg:text-[48px] lg:leading-[58px] font-medium mb-4"><?php echo get_field('title_hero'); ?></h1>
				<div class="font-inter font-medium text-[16px] text-black/70 mb-4 md:mb-12"><?php echo get_field('text_hero'); ?></div>
				<?php 
				$link = get_field('button_hero');
				if( $link ): 
				    $link_url = $link['url'];
				    $link_title = $link['title'];
				    $link_target = $link['target'] ? $link['target'] : '_self';
				    ?>
				    <a class="w-full md:w-auto justify-center inline-flex bg-black rounded-[8px] py-[17px] md:py-3 px-6 text-white font-inter font-medium cursor-pointer hover:opacity-70 transition" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
				<?php endif; ?>
			</div>

			<div class="flex items-end px-10">
				<img class="w-full" src="<?php echo get_field('image_hero'); ?>" alt="">
			</div>

		</div>

	</div>
</section>

<?php
$blocks_b = get_field('blocks_b');
if ($blocks_b): ?>
<section class="py-[27px] md:py-14 px-4">
	<div class="container m-auto">

		<h2 class="font-manrope text-center text-[32px] leading-[40px] lg:text-[48px] lg:leading-[58px] font-medium mb-4"><?php echo get_field('title_b'); ?></h2>
		<div class="font-inter text-center font-medium text-[18px] text-black/60 mb-12"><?php echo get_field('text_b'); ?></div>

		<div class="flex flex-wrap md:flex-nowrap gap-5">
		<?php foreach ($blocks_b as $row): ?>
			<div class="w-full w-1/3 text-center py-10 px-3 md:p-3 rounded-[20px] md:rounded-[22px] bg-[#F7F7F7]">
				<img class="h-[146px] m-auto mb-12" src="<?php echo $row['img']; ?>" alt="">
				<h3 class="text-black font-inter font-medium text-[20px] leading-5 lg:text-[24px] lg:leading-6 mb-2"><?php echo $row['title']; ?></h3>
				<div class="font-inter font-medium text-[16px] text-black/70"><?php echo $row['text']; ?></div>
			</div>
		<?php endforeach; ?>
		</div>

	</div>
</section>
<?php endif; ?>

<?php
$blocks_r = get_field('blocks_r');
if ($blocks_r): ?>
<section class="pt-[43px] pb-[32px] md:py-[187px] px-4">
	<div class="container m-auto">

		<h2 class="font-roboto max-w-[750px] text-[32px] leading-[40px] lg:text-[48px] lg:leading-[58px] font-medium capitalize mb-8 lg:mb-0"><?php echo get_field('title_r'); ?></h2>

		<div class="flex flex-wrap lg:flex-nowrap items-end gap-5">
		<?php $i = 0; foreach ($blocks_r as $row): 
			$i++;
			$min_height = ($i === 2) ? 'min-h-[320px]' : 'min-h-[260px]'; ?>
			<div class="w-full lg:w-1/3 text-center p-5 md:pt-[66px] md:pb-[32px] rounded-[20px] md:rounded-[22px] bg-[#F7F7F7] border-1 border-solid border-[rgba(0,0,0,0.08)] <?php echo $min_height; ?> flex flex-col justify-between">
				<h3 class="text-black font-roboto font-medium text-[44px] leading-8 mb-4"><?php echo $row['price']; ?></h3>
				<?php if($row['img']) { ?><img class="h-[280px] m-auto mb-4" src="<?php echo $row['img']; ?>" alt=""><?php } ?>
				<div class="flex items-center justify-center gap-1">
					<div class="font-inter font-medium text-[16px] text-black/70 rounded-[70px] bg-white flex justify-center items-center h-10 p-4 w-full max-w-[236px]"><?php echo $row['discont']; ?></div>
					<div class="font-inter font-medium text-[16px] text-black/70 flex justify-center items-center h-10 p-4 w-full max-w-[150px]">Get a discount</div>
				</div>
			</div>
		<?php endforeach; ?>
		</div>

	</div>
</section>
<?php endif; ?>

<?php
$blocks_w = get_field('blocks_w');
if ($blocks_w): ?>
<section class="py-[36px] md:pt-[120px] md:pb-[100px] px-4 bg-[#F6F6F6]">
	<div class="container m-auto">

		<h2 class="font-roboto text-center text-[32px] leading-[40px] lg:text-[48px] lg:leading-[58px] font-medium mb-4"><?php echo get_field('title_w'); ?></h2>
		<div class="font-inter text-center font-medium text-[18px] text-black/60 mb-12"><?php echo get_field('text_w'); ?></div>

		<div class="flex flex-wrap md:flex-nowrap gap-[42px] md:gap-5">
		<?php foreach ($blocks_w as $row): ?>
			<div class="w-full w-1/3 text-center px-3 md:px-3">
				<img class="h-[80px] m-auto mb-12" src="<?php echo $row['img']; ?>" alt="">
				<h3 class="text-black font-inter font-medium text-[20px] leading-5 lg:text-[24px] lg:leading-6 mb-2"><?php echo $row['title']; ?></h3>
				<div class="font-inter font-medium text-[16px] text-black/70 mb-5"><?php echo $row['text']; ?></div>
				<?php 
				$link = $row['link'];
				if( $link ): 
				    $link_url = $link['url'];
				    $link_title = $link['title'];
				    $link_target = $link['target'] ? $link['target'] : '_self';
				    ?>
				    <a class="text-[#5C77FF] text-[16px] font-inter font-medium cursor-pointer hover:opacity-70 transition" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
		</div>

	</div>
</section>
<?php endif; ?>

<?php
$teacher_slider = get_field('teacher_slider');
if ($teacher_slider): ?>
<section class="py-10 md:py-25 px-4">
	<div class="container m-auto">

		<h2 class="font-roboto text-left md:text-center text-[32px] leading-[40px] lg:text-[48px] lg:leading-[58px] font-medium max-w-[650px] m-auto mb-5 md:mb-12"><?php echo get_field('title_t'); ?></h2>

		<div class="teachers-slider-wrap">
		<div class="teachers-slider flex flex-col">
		<?php foreach ($teacher_slider as $row): ?>
		<?php 
		$user = $row['teacher'];
		if (!$user) continue;
		$author_id = $user->ID;
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
		<?php endif; ?>
		<?php endforeach; ?>
		</div>
		<div class="slider-arrows overflow-x-hidden flex justify-center gap-5 mt-8 md:mt-12"></div>
		</div>

	</div>
</section>
<script>
	jQuery(document).ready(function($) {
		$('.teachers-slider').slick({
			slidesToShow: 1,
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
</button>`
		});
	});
</script>
<style>
	.teachers-slider-wrap .slick-slide {
		margin: 0 8px;
		/*min-height: 386px;*/
		display: flex;
	}

	.teachers-slider-wrap .slick-list {
		margin: 0 -8px;
	}

	.teachers-slider-wrap .slick-arrow {
		position: static;
		margin: 0;
		transform: none;
		width: unset;
		height: unset;
	}

	.teachers-slider-wrap .slick-arrow:hover {
		opacity: 0.7;
	}

	.teachers-slider-wrap .slick-arrow::before,
	.teachers-slider-wrap .slick-arrow::before {
		content: none;
	}

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
		const toggles = document.querySelectorAll(".toggle-author");
		
		toggles.forEach(function(toggle) {
			const authorRight = toggle.closest(".course-author-right");
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
	});
</script>
<?php endif; ?>

<section class="py-[52px] md:pt-12 md:pb-[120px] px-4">
	<div class="container m-auto">

		<div class="flex items-center flex-wrap md:flex-nowrap gap-4">
			<div class="w-full md:w-1/2">	
				<h2 class="font-roboto text-[32px] leading-[40px] lg:text-[48px] lg:leading-[58px] font-medium max-w-[600px] mb-6"><?php echo get_field('title_h'); ?></h2>
				<div class="font-inter font-medium text-[12px] md:text-[18px] text-black/70 mb-0 md:mb-8 max-w-[500px] capitalize"><?php echo get_field('text_h'); ?></div>
				<?php 
				$link = get_field('button_h');
				if( $link ): 
				    $link_url = $link['url'];
				    $link_title = $link['title'];
				    $link_target = $link['target'] ? $link['target'] : '_self';
				    ?>
				    <a class="w-full md:w-auto justify-center hidden md:inline-flex bg-p2 rounded-[48px] py-[17px] md:py-3 px-6 text-black font-inter font-medium cursor-pointer hover:opacity-70 transition" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
				<?php endif; ?>
			</div>
			<div class="w-full md:w-1/2 text-right">	
				<img class="w-full max-w-[616px] ml-auto" src="<?php echo get_field('img_h'); ?>" alt="">
				<?php 
				$link = get_field('button_h');
				if( $link ): 
				    $link_url = $link['url'];
				    $link_title = $link['title'];
				    $link_target = $link['target'] ? $link['target'] : '_self';
				    ?>
				    <a class="justify-center hidden inline-flex md:hidden bg-p2 rounded-[48px] py-[17px] md:py-3 px-6 text-[12px] text-black font-inter font-medium cursor-pointer hover:opacity-70 transition mt-5" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
				<?php endif; ?>
			</div>				
		</div>

	</div>
</section>

<section class="pt-0 pb-[52px] md:py-20 px-4">
	<div class="container m-auto">

		<div class="text-center py-12 px-8 rounded-[22px] md:rounded-[36px] bg-[#FF596C]">

			<h2 class="font-roboto text-white text-[32px] leading-[40px] lg:text-[48px] lg:leading-[58px] capitalize font-medium max-w-[305px] md:max-w-full m-auto mb-6"><?php echo get_field('title_banner'); ?></h2>
			<div class="font-inter font-medium text-[18px] text-white capitalize max-w-[464px] m-auto mb-8"><?php echo get_field('text_banner'); ?></div>
			<?php 
			$link = get_field('button_banner');
			if( $link ): 
			    $link_url = $link['url'];
			    $link_title = $link['title'];
			    $link_target = $link['target'] ? $link['target'] : '_self';
			    ?>
			    <a class="md:w-auto justify-center inline-flex bg-p2 rounded-[48px] py-3 px-6 text-black font-inter font-medium cursor-pointer hover:opacity-70 transition" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
			<?php endif; ?>

		</div>

	</div>
</section>

<?php get_footer(); ?>