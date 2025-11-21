<?php

$background_image = get_field('background_image', 'option');
$center_text_block = get_field('center_text_block', 'option');
$timer = get_field('timer', 'option');
$select_course = get_field('select_course', 'option');

$logo = get_field('logo', 'option');
$category_menu = get_field('category_menu', 'option');
$teach = get_field('teach', 'option');
$box = get_field('box', 'option');
$coins = get_field('coins', 'option');

$timer_duration = !empty($timer['time']) ? $timer['time'] : '';
$timer_duration_seconds = 0;
if ($timer_duration) {
	// Парсим время в формате H:i:s (например, "20:51:17")
	$time_parts = explode(':', $timer_duration);
	if (count($time_parts) === 3) {
		$hours = intval($time_parts[0]);
		$minutes = intval($time_parts[1]);
		$seconds = intval($time_parts[2]);
		$timer_duration_seconds = $hours * 3600 + $minutes * 60 + $seconds;
	}
}

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Manrope:wght@200..800&family=Roboto:ital,wght@0,100..900;1,100..900&family=Figtree:ital,wght@0,300..900;1,300..900&family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
	    <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
	<style type="text/tailwindcss">
		@theme {
			--color-clifford: #da373d;
			--color-p1: #00F6ED;
			--color-p2: #E8F501;
			--color-p3: #FF00A3;
			--color-black: #000000;
			--color-gradient: linear-gradient(0deg, #F9FCBB 0%, #F7E64B 100%);
			--color-grey-15: #262626;
			--text-base: 1rem;
			--leading-base: auto;
			--text-small: 0.875rem;
			--leading-small: auto;
			--font-manrope: 'Manrope', sans-serif;
			--font-inter: 'Inter', sans-serif;
			--font-roboto: 'Roboto', sans-serif;
			--font-figtree: 'Figtree', sans-serif;
			--font-jost: 'Jost', sans-serif;
		}
		@layer utilities {
    	.container {
      max-width: 82.5rem;
   	 }
		}
	</style>
	<style>
		header .custom-search-form {
			position: relative;
			width: 520px;
			height: 40px;
			margin-left: 20px;
			display: flex;
			align-items: center;
		}

		header .custom-search-form label,
		header .custom-search-form label input {
			width: 100%;
			height: 100%;
			display: block;
		}

		header .custom-search-form .search-submit {
			background: none;
			border: none;
			position: absolute;
			left: 16px;
			top: 50%;
			transform: translateY(-50%);
			padding: 0;
			cursor: pointer;
		}

		.woocommerce-form-coupon-toggle {
			display: none !important;
			}

			.return-to-shop {
				display: none;
			}
	</style>
	<?php wp_head(); ?>
	<script>
		document.addEventListener('DOMContentLoaded', e => {
			const showBtn = document.querySelector('.shw-p');
        if (showBtn) {
            showBtn.addEventListener('click', e => {
                const inputField = document.querySelector('.lgi-p');
                if (inputField.type === 'password') {
                    inputField.type = 'text';
                    showBtn.classList.add('shw')
                } else {
                    inputField.type = 'password';
                    showBtn.classList.remove('shw')
                }
            });
        }
		})
	</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggles = document.querySelectorAll('.shw-p');
    const inputs = document.querySelectorAll('.ps');

    toggles.forEach(toggle => {
        toggle.addEventListener('click', () => {
            if (!inputs.length) return;

            const shouldShow = inputs[1].type === 'password';

            inputs.forEach(input => {
                input.type = shouldShow ? 'text' : 'password';
            });

            toggles.forEach(btn => {
                btn.classList.toggle('shw', shouldShow);
            });
        });
    });
});
</script>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div id="page" class="site">
		<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'walla'); ?></a>

		<header id="masthead" class="site-header border border-[#F4F4F4]">
			<div class="h-[49px] px-4 bg-p2 w-full flex justify-between md:justify-center items-center gap-2 md:gap-[73px] bg-no-repeat bg-top bg-cover" style='background-image: url("<?php echo $background_image['url'] ?>");'>
				<div class="flex gap-3 items-center">
					<img class="hidden md:flex" src="<?php echo $center_text_block['icon']['url'] ?>" alt="<?php echo $center_text_block['icon']['alt'] ?>">
					<div class='flex gap-0 md:gap-[6px] items-center'>
						<h6 class='text-xs md:text-base leading-[1.2] md:leading-base text-black font-medium font-manrope max-w-[160px] md:max-w-full mr-2 md:mr-0'>
							<?php echo $center_text_block['text'] ?>
						</h6>
						<img src="<?php echo $timer['icon']['url'] ?>" alt="<?php echo $timer['icon']['alt'] ?>">
						<h6 class='text-small leading-small text-black font-medium font-manrope'>
							<?php if ($timer_duration_seconds > 0): ?>
								<span id="countdown-timer" data-duration="<?php echo esc_attr($timer_duration_seconds); ?>"></span>
							<?php else: ?>
								<?php echo esc_html($timer['time']); ?>
							<?php endif; ?>
						</h6>
					</div>
				</div>
				<?php /*
				if (!empty($select_course['link']) && !empty($select_course['text'])) :
				?>
					<a href="<?php echo esc_url($select_course['link']); ?>" class="px-4 py-[12px] text-[12px] leading-[1.2] md:leading-base text-white font-manrope font-medium bg-black cursor-pointer rounded-[5px] text-center max-w-[114px] md:max-w-[142px]">
						<?php echo esc_html($select_course['text']); ?>
					</a>
				<?php
				elseif (!empty($select_course['text'])) :
				?>
					<div class="px-4 py-[8px] text-[12px] leading-[1.2] md:leading-base text-white font-manrope font-medium bg-black cursor-default rounded-[5px] text-center max-w-[114px] md:max-w-[142px]">
						<?php echo esc_html($select_course['text']); ?>
					</div>
				<?php
				endif;
				*/ ?>
			</div>
			<div class="px-4">
				<div class="container mx-auto py-[15px] flex items-center justify-between">
					<a class='max-w-[114px] md:max-w-[150px] max-h-[50px] block mr-1 md:mr-10' href="<?php echo esc_url(home_url('/')); ?>">
						<img class="w-full h-full" src="<?php echo $logo['url'] ?>" alt="<?php echo $logo['alt'] ?>">
					</a>
					<?php /*
					<div class="cat flex gap-1 items-center">
						<span>Categories</span> <span><svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M8 11.25C7.75391 11.25 7.53516 11.168 7.37109 11.0039L2.99609 6.62891C2.64063 6.30078 2.64063 5.72656 2.99609 5.39844C3.32422 5.04297 3.89844 5.04297 4.22656 5.39844L8 9.14453L11.7461 5.39844C12.0742 5.04297 12.6484 5.04297 12.9766 5.39844C13.332 5.72656 13.332 6.30078 12.9766 6.62891L8.60156 11.0039C8.4375 11.168 8.21875 11.25 8 11.25Z" fill="#3E3232" fill-opacity="0.5" />
							</svg>
						</span>
					</div>
					<form role="search" method="get" class="search-form custom-search-form mr-2" action="<?php echo esc_url(home_url('/')); ?>">
						<label>
							<span class="screen-reader-text"><?php echo _x('Search for:', 'label', 'walla'); ?></span>
							<input type="search" class="search-field w-full h-full border border-solid border-[rgba(0,0,0,0.07)] rounded-[48px] pl-[48px]"
								placeholder="<?php echo esc_attr_x('Search anything', 'placeholder', 'walla'); ?>"
								value="<?php echo get_search_query(); ?>" name="s" />
						</label>
						<button type="submit" class="search-submit absolute" aria-label="<?php esc_attr_e('Search', 'walla'); ?>">
							<img src="<?php echo esc_url(home_url('/')) . '/wp-content/uploads/2025/08/search-refraction.svg' ?>" alt="Search" style="width:20px;height:20px;vertical-align:middle;">
						</button>
					</form>
					*/ ?>
					<div class="flex items-center">
						<div class="teach hidden md:block">
							<?php if (!empty($teach['link']) && !empty($teach['text'])) : ?>
								<a class='font-inter text-[14px] leading-[20px] font-medium text-grey-15 hover:text-[#FF00A3]' href="<?php echo esc_url($teach['link']); ?>"><?php echo esc_html($teach['text']); ?></a>
							<?php elseif (!empty($teach['text'])) : ?>
								<div class='font-inter text-[14px] leading-[20px] font-medium text-grey-15'><?php echo esc_html($teach['text']); ?></div>
							<?php endif; ?>
						</div>
						<?php /* <div class="w-[1px] h-[10px] bg-[#D1D1D1] rounded-full mx-[6.5px]"></div>
						<div class="rounded-[31px] border border-solid border-black px-3 py-2 flex gap-[2px] items-center">
							<img src="<?php echo esc_url(home_url('/')) . '/wp-content/uploads/2025/08/image-122.svg' ?>" alt="Search" style="width:12px;height:12px;border-radius: 50%;">
							<span class='text-gray-15 font-inter text-[14px] leading-base font-medium'>USD</span>
						</div> */ ?>
						<div class="hidden md:block w-[1px] h-[10px] bg-[#D1D1D1] rounded-full mx-[6.5px]"></div>

						<?php /*
						<div class="gift">
							<?php if (!empty($box['link']['url']) && !empty($box['image'])) : ?>
								<a href="<?php echo esc_url($box['link']['url']); ?>">
									<img src="<?php echo esc_html($box['image']['url']); ?>" alt="<?php echo esc_html($box['image']['alt']); ?>">
								</a>
							<?php elseif (!empty($box['image'])) : ?>
								<div class=''>
									<img src="<?php echo esc_html($box['image']['url']); ?>" alt="<?php echo esc_html($box['image']['alt']); ?>">
								</div>
							<?php endif; ?>
						</div>
						*/ ?>
						<?php /* <div class="w-[1px] h-[10px] bg-[#D1D1D1] rounded-full mx-[6.5px]"></div> */ ?>
						<?php
						// Получаем URL-ы страниц Tutor LMS по ID
						$tutor_login_url = '';
						$tutor_register_url = '';
						$tutor_dashboard_url = '';
						$tutor_is_active = false;
						
						// Проверяем, активен ли Tutor LMS
						if (function_exists('tutor_utils') && class_exists('TUTOR\Tutor')) {
							$tutor_is_active = true;
							
							// Используем конкретные ID страниц
							$tutor_login_url = get_permalink(22); // tutor-login-2
							$tutor_dashboard_url = get_permalink(24); // dashboard
							
							// Для регистрации по умолчанию показываем студенческую регистрацию
							$tutor_register_url = get_permalink(23); // student-registration
						}
						
						// Если Tutor LMS не активен, используем стандартные URL-ы
						if (!$tutor_login_url) {
							$tutor_login_url = wp_login_url();
						}
						if (!$tutor_register_url) {
							$tutor_register_url = wp_registration_url();
						}
						if (!$tutor_dashboard_url) {
							$tutor_dashboard_url = admin_url();
						}
						
						$is_user_logged_in = is_user_logged_in();
						?>
						
						<div class="btns flex gap-2 items-center">
							<?php if ($is_user_logged_in): ?>
								<!-- Пользователь авторизован - показываем дашборд -->
								<?php
								$current_user = wp_get_current_user();
								$user_name = $current_user->display_name ?: $current_user->user_login;
								$user_avatar = get_avatar($current_user->ID, 32, '', '', array('class' => 'w-8 h-8 rounded-full'));
								?>
								<div class="flex items-center gap-3 mr-2">
									<a href="<?php echo esc_url(get_permalink(22)); ?>" class="flex items-center gap-3 hover:opacity-80 transition">
										<?php echo $user_avatar; ?>
										<div class="flex flex-col">
											<span class="font-inter text-[14px] text-grey-15 font-medium"><?php echo esc_html($user_name); ?></span>
											<?php if ($tutor_is_active && function_exists('tutor_utils') && tutor_utils()->is_instructor()): ?>
												<span class="font-inter text-[12px] text-p3 font-medium"><?php _e('Instructor', 'walla'); ?></span>
											<?php endif; ?>
										</div>
									</a>
								</div>
								<a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="p-3 md:px-6 md:py-3 rounded-[8px] border border-solid border-p2 text-black font-inter text-[14px] font-medium hover:bg-p2 transition">
									<?php _e('Logout', 'walla'); ?>
								</a>
							<?php else: ?>
								<!-- Пользователь не авторизован - показываем логин и регистрацию -->
								<a href="<?php echo esc_url($tutor_login_url); ?>" class="p-3 md:px-6 md:py-3 rounded-[8px] border border-solid border-p2 text-black font-inter text-[14px] font-medium hover:bg-p2 transition">
								<?php _e('Login', 'walla'); ?>
							</a>
								<a href="<?php echo esc_url($tutor_register_url); ?>" class="p-3 md:px-6 md:py-3 rounded-[8px] bg-p2 border border-solid border-p2 text-black font-inter text-[14px] font-medium hover:bg-transparent transition">
								<?php _e('Sign Up', 'walla'); ?>
							</a>
							<?php endif; ?>
						</div>
						<?php /*
						<div class="coins">
							<?php if (!empty($coins['link']['url']) && !empty($coins['image'])) : ?>
								<a href="<?php echo esc_url($coins['link']['url']); ?>">
									<img class='w-full h-full object-cover' src="<?php echo esc_html($coins['image']['url']); ?>" alt="<?php echo esc_html($coins['image']['alt']); ?>">
								</a>
							<?php elseif (!empty($coins['image'])) : ?>
								<div>
									<img class='w-full h-full object-cover' src="<?php echo esc_html($coins['image']['url']); ?>" alt="<?php echo esc_html($coins['image']['alt']); ?>">
								</div>
							<?php endif; ?>
						</div>
						*/ ?>
					</div>
				</div>
			</div>
	</div>
	</header>
	<?php if ($timer_duration_seconds > 0): ?>
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				function pad(n) {
					return n < 10 ? '0' + n : n;
				}
				
				var timerElem = document.getElementById('countdown-timer');
				if (!timerElem) return;
				
				var duration = parseInt(timerElem.getAttribute('data-duration')); // Длительность в секундах
				var cookieName = 'timer_start_time';
				var startTime;
				
				// Получаем время начала из куки или создаем новое
				var startTimeCookie = getCookie(cookieName);
				if (startTimeCookie) {
					startTime = parseInt(startTimeCookie);
				} else {
					startTime = Math.floor(Date.now() / 1000);
					setCookie(cookieName, startTime.toString(), 7); // Сохраняем на 7 дней
				}
				
				function updateCountdown() {
					var now = Math.floor(Date.now() / 1000);
					var elapsed = now - startTime;
					var remaining = duration - (elapsed % duration);
					
					// Если таймер закончился, сбрасываем время начала
					if (elapsed >= duration && elapsed % duration === 0) {
						startTime = now;
						setCookie(cookieName, startTime.toString(), 7);
						remaining = duration;
					}
					
					var hours = Math.floor(remaining / 3600);
					var minutes = Math.floor((remaining % 3600) / 60);
					var seconds = remaining % 60;
					
					timerElem.textContent = pad(hours) + ':' + pad(minutes) + ':' + pad(seconds);
					//timerElem.textContent = pad(hours) + ':' + pad(minutes);
				}
				
				// Функции для работы с куки
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