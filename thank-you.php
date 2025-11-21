<?php
/**
 * Template Name: Thank You
 *
 * Custom order confirmation page that receives order data through the query
 * string (order_id + order_key) and renders a summary for the customer.
 */

get_header();

$order         = null;
$order_id      = isset($_GET['order_id']) ? absint($_GET['order_id']) : 0;
$order_key     = isset($_GET['order_key']) ? sanitize_text_field(wp_unslash($_GET['order_key'])) : '';
$can_display   = false;
$error_message = '';

// Получаем ссылку на картинку из опций через ACF поле
$thank_you_image_url = function_exists('get_field') ? get_field('thank_you_image', 'option') : '';

if ($order_id && $order_key && function_exists('wc_get_order') && class_exists('WC_Order')) {
	$order = wc_get_order($order_id);

	if (! ($order instanceof WC_Order) || ! hash_equals($order->get_order_key(), $order_key)) {
		$order         = null;
		$error_message = __('We could not verify your order. Please check the link from your confirmation email.', 'walla');
	} elseif (! is_user_logged_in()) {
		$order         = null;
		$error_message = __('You need to log in to view this order.', 'walla');
	} else {
		$current_user_id = get_current_user_id();

		if ((int) $order->get_user_id() !== (int) $current_user_id) {
			$order         = null;
			$error_message = __('This order does not belong to your account.', 'walla');
		} else {
			$can_display = true;
		}
	}
}
?>

<main class="bg-[#f2f2f5] py-16">
	<div class="container mx-auto max-w-5xl px-6">
		<?php if ($can_display && $order) : ?>
			<section class="rounded-3xl bg-white shadow-xl">
				<div class="rounded-t-3xl bg-white px-8 py-12 text-center sm:px-16">
					<h1 class="font-sora text-4xl font-semibold text-gray-900 sm:text-5xl">
						<?php esc_html_e('Thank You', 'walla'); ?>
					</h1>
					<p class="mt-4 font-inter text-base text-gray-500 sm:text-lg">
						<?php esc_html_e('We received your order. You can continue learning right away.', 'walla'); ?>
					</p>
					<div class="mt-8 flex justify-center">
						<?php if ( !empty($thank_you_image_url) ) : ?>
							<img
								src="<?php echo esc_url($thank_you_image_url); ?>"
								alt="<?php esc_attr_e('Thank you illustration', 'walla'); ?>"
								class="h-32 w-32 sm:h-40 sm:w-40"
							/>
						<?php endif; ?>
					</div>
				</div>

				<div class="border-t border-gray-100 px-6 py-10 sm:px-12">
					<div class="mb-8 flex flex-wrap items-center justify-between gap-4">
						<div>
							<p class="font-inter text-sm uppercase tracking-wide text-gray-400">
								<?php esc_html_e('Order Number', 'walla'); ?>
							</p>
							<p class="font-sora text-lg font-semibold text-gray-900">
								<?php echo esc_html($order->get_order_number()); ?>
							</p>
						</div>
						<div>
							<p class="font-inter text-sm uppercase tracking-wide text-gray-400">
								<?php esc_html_e('Date', 'walla'); ?>
							</p>
							<p class="font-sora text-lg font-semibold text-gray-900">
								<?php echo esc_html(wc_format_datetime($order->get_date_created())); ?>
							</p>
						</div>
						<div>
							<p class="font-inter text-sm uppercase tracking-wide text-gray-400">
								<?php esc_html_e('Email', 'walla'); ?>
							</p>
							<p class="font-sora text-lg font-semibold text-gray-900">
								<?php echo esc_html($order->get_billing_email()); ?>
							</p>
						</div>
					</div>

					<h2 class="font-sora text-xl font-semibold text-gray-900">
						<?php esc_html_e('Order Summary', 'walla'); ?>
					</h2>

					<div class="mt-6 space-y-6">
						<?php foreach ($order->get_items() as $item_id => $item) : ?>
							<?php
							$product      = $item->get_product();
							$product_name = $item->get_name();
							$quantity     = $item->get_quantity();
							$image_html   = '';

							if ($product && is_a($product, 'WC_Product')) {
								$image_id = $product->get_image_id();
								if ($image_id) {
									$image_html = wp_get_attachment_image($image_id, 'medium', false, array('class' => 'h-20 w-20 rounded-2xl object-cover'));
								}
							}

							if (! $image_html) {
								$image_html = '<div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-gray-100 text-sm text-gray-400">'
									. esc_html__('No image', 'walla')
									. '</div>';
							}
							?>
							<div class="flex flex-wrap gap-4 rounded-2xl bg-gray-50 p-5 sm:flex-nowrap sm:p-6">
								<div>
									<?php echo wp_kses_post($image_html); ?>
								</div>
								<div class="flex-1">
									<p class="font-sora text-lg font-semibold text-gray-900">
										<?php echo esc_html($product_name); ?>
									</p>
									<p class="mt-1 font-inter text-sm text-gray-500">
										<?php echo esc_html(sprintf(_n('%d licence', '%d licences', $quantity, 'walla'), $quantity)); ?>
									</p>
									<div class="mt-4 font-sora text-base font-semibold text-gray-900">
										<?php echo wp_kses_post($order->get_formatted_line_subtotal($item)); ?>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>

					<div class="mt-10 rounded-2xl bg-gray-900 px-6 py-6 text-white sm:px-8">
						<?php
						$order_totals = $order->get_order_item_totals();
						if ($order_totals) :
							foreach ($order_totals as $total_key => $total) :
								$is_total = ('order_total' === $total_key);
								?>
								<div class="flex items-center justify-between py-2 <?php echo $is_total ? 'border-t border-white/20 mt-2 pt-4 text-lg font-semibold uppercase tracking-wide' : 'text-sm'; ?>">
									<span><?php echo esc_html(wp_strip_all_tags($total['label'])); ?></span>
									<span><?php echo wp_kses_post($total['value']); ?></span>
								</div>
							<?php
							endforeach;
						endif;
						?>
					</div>

					<div class="mt-10 flex flex-wrap gap-3">
						<a
							class="inline-flex items-center justify-center rounded-full bg-yellow-400 px-6 py-3 font-sora text-sm font-semibold text-gray-900 shadow-sm hover:bg-yellow-300"
							href="<?php echo esc_url(home_url('/dashboard/purchase_history/')); ?>"
						>
							<?php esc_html_e('Download Invoice', 'walla'); ?>
						</a>
						<a
							class="inline-flex items-center justify-center rounded-full border border-gray-200 px-6 py-3 font-sora text-sm font-semibold text-gray-700 hover:border-gray-300 hover:text-gray-900"
							href="<?php echo esc_url(home_url('/dashboard/enrolled-courses/')); ?>"
						>
							<?php esc_html_e('My Courses', 'walla'); ?>
						</a>
					</div>
				</div>
			</section>
		<?php else : ?>
			<section class="rounded-3xl bg-white px-8 py-16 text-center shadow-xl sm:px-16">
				<h1 class="font-sora text-4xl font-semibold text-gray-900 sm:text-5xl">
					<?php esc_html_e('Order unavailable', 'walla'); ?>
				</h1>
				<p class="mt-4 font-inter text-base text-gray-500 sm:text-lg">
					<?php echo esc_html($error_message ?: __('We could not locate your order details. Please check the link from your payment confirmation or contact support.', 'walla')); ?>
				</p>
				<div class="mt-8">
					<a
						class="inline-flex items-center justify-center rounded-full bg-gray-900 px-6 py-3 font-sora text-sm font-semibold text-white hover:bg-gray-800"
						href="<?php echo esc_url(home_url('/')); ?>"
					>
						<?php esc_html_e('Back to Home', 'walla'); ?>
					</a>
				</div>
			</section>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();