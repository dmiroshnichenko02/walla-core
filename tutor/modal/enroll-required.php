<?php
/**
 * Enroll Required Modal: Show when user tries to access lesson/quiz without enrollment
 *
 * @package Tutor\Views
 * @subpackage Tutor\Modal
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 1.0.0
 */

$course_id = isset($course_id) ? $course_id : 0;
$is_user_logged_in = is_user_logged_in();
$course_permalink = get_permalink($course_id);
$login_url = wp_login_url($course_permalink);
$register_url = wp_registration_url();
if (function_exists('tutor_utils')) {
	$register_url = tutor_utils()->student_register_url();
}

// Получаем информацию о курсе
$course_price = '';
$is_purchasable = false;
$tutor_course_sell_by = '';

if ($course_id) {
	$is_purchasable = tutor_utils()->is_course_purchasable($course_id);
	$tutor_course_sell_by = tutor_utils()->get_option('sell_course_by');
	
	if ($is_purchasable && $tutor_course_sell_by === 'woocommerce') {
		$product_id = tutor_utils()->get_course_product_id($course_id);
		if ($product_id) {
			$product = wc_get_product($product_id);
			if ($product) {
				$course_price = $product->get_price_html();
			}
		}
	}
}

?>
<div class="tutor-modal tutor-enroll-required-modal">
	<div class="tutor-modal-overlay"></div>
	<div class="tutor-modal-window">
		<div class="tutor-modal-content tutor-modal-content-white">
			<button type="button" class="tutor-iconic-btn tutor-modal-close-o tutor-enroll-modal-close">
				<span class="tutor-icon-times" area-hidden="true"></span>
			</button>

			<div class="tutor-modal-body tutor-text-center">
				<div class="tutor-fs-3 tutor-fw-medium tutor-color-black tutor-mt-48 tutor-mb-12">
					<?php esc_html_e('Access Restricted', 'tutor'); ?>
				</div>
				<div class="tutor-fs-6 tutor-color-muted tutor-mb-32">
					<?php if (!$is_user_logged_in): ?>
						<?php esc_html_e('You need to register or purchase this course to access this content.', 'tutor'); ?>
					<?php else: ?>
						<?php esc_html_e('You need to purchase this course to access this content.', 'tutor'); ?>
					<?php endif; ?>
				</div>

				<div class="tutor-d-flex tutor-flex-column tutor-gap-16 tutor-mt-32 gap-4">
					<?php if (!$is_user_logged_in): ?>
						<a href="<?php echo esc_url($login_url); ?>" class="tutor-btn tutor-btn-primary tutor-btn-block">
							<?php esc_html_e('Login', 'tutor'); ?>
						</a>
						<?php if (get_option('users_can_register', false)): ?>
							<a href="<?php echo esc_url($register_url); ?>" class="tutor-btn tutor-btn-outline-primary tutor-btn-block">
								<?php esc_html_e('Create Account', 'tutor'); ?>
							</a>
						<?php endif; ?>
					<?php endif; ?>
					
					<?php if ($is_purchasable && $tutor_course_sell_by === 'woocommerce'): ?>
						<a href="<?php echo esc_url($course_permalink); ?>" class="tutor-btn tutor-btn-primary tutor-btn-block">
							<?php esc_html_e('Purchase Course', 'tutor'); ?>
							<?php if ($course_price): ?>
								<span class="tutor-ml-8"><?php echo wp_kses_post($course_price); ?></span>
							<?php endif; ?>
						</a>
					<?php else: ?>
						<a href="<?php echo esc_url($course_permalink); ?>" class="tutor-btn tutor-btn-primary tutor-btn-block">
							<?php esc_html_e('View Course', 'tutor'); ?>
						</a>
					<?php endif; ?>
				</div>

				<button type="button" class="tutor-btn tutor-btn-ghost tutor-mt-24 tutor-enroll-modal-close">
					<?php esc_html_e('Close', 'tutor'); ?>
				</button>
			</div>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const modal = document.querySelector('.tutor-enroll-required-modal');
	const closeButtons = document.querySelectorAll('.tutor-enroll-modal-close');
	
	if (modal) {
		modal.classList.add('tutor-is-active');
		document.body.style.overflow = 'hidden';
	}
	
	function closeModal() {
		if (modal) {
			modal.classList.remove('tutor-is-active');
			document.body.style.overflow = '';
			setTimeout(function() {
				window.location.href = '<?php echo esc_js($course_permalink); ?>';
			}, 300);
		}
	}
	
	closeButtons.forEach(function(btn) {
		btn.addEventListener('click', closeModal);
	});
	
	if (modal) {
		const overlay = modal.querySelector('.tutor-modal-overlay');
		if (overlay) {
			overlay.addEventListener('click', closeModal);
		}
	}
});
</script>

