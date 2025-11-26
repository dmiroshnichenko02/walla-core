<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// If checkout registration is disabled and not logged in, the user cannot checkout.
?>

<style>
	body {
		background: #F3F3F3;
	}
	.woocommerce-form-coupon-toggle {
		display: none !important;
	}
	header {
		background: #fff;
	}
</style>

<div class="container mx-auto py-[50px]">
	<style>
        
		/* Match registration input styles on WooCommerce checkout */
		.woocommerce-checkout input[type="text"],
		.woocommerce-checkout input[type="email"],
		.woocommerce-checkout input[type="tel"],
		.woocommerce-checkout input[type="password"],
		.woocommerce-checkout input[type="number"],
		.woocommerce-checkout input.input-text,
		.woocommerce-checkout select,
		.woocommerce-checkout textarea {
			height: 52px;
			border-radius: 28px;
			background: #fff;
			border: 1px solid #e6e7eb;
			padding: 0 18px;
			box-shadow: none;
		}
		.woocommerce-checkout input[type="text"],
		.woocommerce-checkout input[type="email"],
		.woocommerce-checkout input[type="tel"],
		.woocommerce-checkout input[type="password"],
		.woocommerce-checkout input[type="number"],
		.woocommerce-checkout input.input-text {
			padding-left: 30px !important;
			padding-right: 30px !important;
		}
		.woocommerce-checkout textarea {
			height: auto;
			min-height: 120px;
			padding: 12px 18px;
			border-radius: 16px;
		}
		/* Select2 styling to match inputs */
		.woocommerce-checkout .select2-container .select2-selection--single {
			height: 52px;
			border-radius: 28px;
			border: 1px solid #e6e7eb;
		}
		.woocommerce-checkout .select2-container .select2-selection--single .select2-selection__rendered {
			line-height: 52px;
			padding: 0 18px;
		}
		.woocommerce-checkout .select2-container .select2-selection--single .select2-selection__arrow {
			height: 52px;
		}
		.woocommerce-checkout #select2-billing_country-container {
			padding-left: 30px !important;
		}
		/* Floating labels */
		.woocommerce-checkout .form-row {
			position: relative;
		}
		.woocommerce-checkout .form-row {
			margin-bottom: 16px;
		}
		.woocommerce-checkout .form-row:last-child {
			margin-bottom: 0;
		}
		.woocommerce-checkout .woocommerce-billing-fields__field-wrapper > .form-row,
		.woocommerce-checkout .woocommerce-shipping-fields__field-wrapper > .form-row,
		.woocommerce-checkout .woocommerce-additional-fields__field-wrapper > .form-row {
			margin-bottom: 24px;
		}
		.woocommerce-checkout .woocommerce-billing-fields__field-wrapper > .form-row:last-child,
		.woocommerce-checkout .woocommerce-shipping-fields__field-wrapper > .form-row:last-child,
		.woocommerce-checkout .woocommerce-additional-fields__field-wrapper > .form-row:last-child {
			margin-bottom: 0;
		}
		.woocommerce-checkout .form-row > label {
			position: absolute;
			left: 30px;
			top: 50%;
			transform: translateY(-50%);
			color: #6b7280;
			background: transparent;
			padding: 0 8px;
			margin: 0;
			pointer-events: none;
			transition: all 150ms ease;
			font-size: 14px;
			line-height: 20px;
			z-index: 20;
		}
		.woocommerce-checkout .form-row.floating-active > label {
			top: -10px;
			transform: translateY(0);
			background: #fff;
			left: 24px;
			font-size: 12px;
			line-height: 16px;
			color: #1D1F1E;
		}
		.woocommerce-billing-fields__field-wrapper {
			margin-top: 30px;
		}
		/* Textarea label baseline */
		.woocommerce-checkout .form-row textarea + label,
		.woocommerce-checkout .form-row.floating-active textarea + label {
			top: 16px;
			transform: translateY(0);
		}
		html {
			overflow-x: hidden;
		}
		/* Hide Additional Information block */
		.woocommerce-checkout .woocommerce-additional-fields {
			display: none;
		}
		.woocommerce-checkout .create-account {
			margin-top: 10px !important;
		}
		.woocommerce-checkout .create-account label, .woocommerce-checkout .woocommerce-form-login .woocommerce-form-login__rememberme {
			position: relative;
			top: unset !important;
			transform: unset !important;
			pointer-events: all;
			left: unset !important;
            gap: 10px !important;
            display: flex !important;
            align-items: center;
			cursor: pointer;
		}
		.woocommerce-checkout .woocommerce-terms-and-conditions-wrapper {
			margin-bottom: 20px;
		}
        .woocommerce-checkout .woocommerce-billing-fields h3{
            color: #000;
            font-size: 20px;
            line-height: 28px;
            font-weight: 500;
            font-family: 'Roboto', sans-serif;
        }

        .woocommerce-checkout .woocommerce-form-login-toggle .woocommerce-info {
            margin-bottom: 0 !important;
        }
        .woocommerce-checkout .woocommerce-info,
        .woocommerce-checkout .woocommerce-form-login {
            background: white;
            border: 2px solid #EFEFEF;
            border-radius: 12px;
        }
        .woocommerce-checkout .woocommerce-info.form-login-open {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            border-bottom: none;
        }
        .woocommerce-checkout .woocommerce-form-login.form-login-open {
            border-top-left-radius: 0;
            border-top-right-radius: 0;
            border-top: none;
            margin-top: -2px;
        }

        .woocommerce-checkout .woocommerce-form-login,
        .woocommerce-checkout .woocommerce-info {
            padding: 16px;
        }
        .woocommerce-checkout .woocommerce-info {
            padding-left: 48px;
        }

        .woocommerce-checkout .woocommerce-info a {
            text-decoration: underline;
        }

        .woocommerce-checkout .woocommerce-form-login > p {
            margin-bottom: 20px;
        }
		.woocommerce-checkout .woocommerce-checkout-payment {
			padding: 20px !important;
			border-radius: 12px !important;
			border: 2px solid #EFEFEF !important;
			background: #fff !important;
		}
		.woocommerce-checkout .woocommerce-privacy-policy-text {
			font-size: 12px;
			font-family: 'Roboto', sans-serif;
		}

		.woocommerce-checkout .woocommerce-form-login__submit {
			margin: 0 auto !important;
			display: block;
			width: 384px;
			height: 52px;
			border-radius: 28px;
			background: #000;
			color: #fff;
			font-size: 16px;
			font-family: 'Roboto', sans-serif;
			font-weight: 500;
		}
		.woocommerce-checkout .woocommerce-form-login__submit:hover {
			background: #1D1F1E !important;
			color: #fff !important;
		}

		.woocommerce-checkout .lost_password {
			margin: 0px auto !important;
			display: block;
			max-width: 384px;
			text-align: right;
		}
		.woocommerce-checkout .lost_password:hover {
			text-decoration: underline !important;
		}

		.woocommerce-checkout .woocommerce-form-login-toggle {
			border: none !important;
			background: none !important;
			display: none !important;
		}
		.woocommerce-checkout .woocommerce-form-login  {
			display: block !important;
			background: #F6F6F6 !important;
			border-radius: 20px !important;
			padding: 40px 24px !important;
			border: none !important;
		}

		.woocommerce-checkout .woocommerce-form-login p:first-child {
			display: none;
		}
		.woocommerce-checkout .woocommerce-form-login .form-row-first, .woocommerce-checkout .woocommerce-form-login .form-row-last {
			width: 384px !important;
			margin: 0 auto !important;
			float: unset !important;
		}
		.woocommerce-checkout .woocommerce-form-login .form-row-first {
			margin-bottom: 26px !important;
		}
		.woocommerce-checkout .woocommerce-form-login .form-row-last {
			margin-bottom: 26px !important;
		}
		.woocommerce-checkout .woocommerce-form-login .woocommerce-form-login__rememberme {
			display: none !important;
		}
		.woocommerce-checkout .woocommerce-error::before, .woocommerce-checkout .woocommerce-info::before, .woocommerce-checkout .woocommerce-message::before {
			display: none !important;
		}

		.woocommerce-checkout .woocommerce-error {
			border: 2px solid #b81c23;
			border-radius: 24px;
			padding: 20px 24px !important;
			background: #F6F6F6 !important;
		}

		.woocommerce-checkout .woocommerce-error li {
			font-size: 18px;
			line-height: 28px;
			font-family: 'Roboto', sans-serif;
			font-weight: 400;
			color: #1D1F1E;
		}

		.woocommerce-checkout .woocommerce-error li a {
			font-weight: 500;
		}
		.woocommerce-checkout .woocommerce-error li a:hover {
			text-decoration: underline !important;
		}

		.woocommerce-checkout .woocommerce-message[role="alert"], .woocommerce-checkout .woocommerce-message[role="alert"]:focus, .woocommerce-checkout .woocommerce-message[role="alert"]:active {
			border: 2px solid #8fae1b !important;
			border-color: #8fae1b !important;
			border-radius: 24px !important;
			padding: 20px 24px !important;
			background: #F6F6F6 !important;
			display: none !important;
		}
		.woocommerce-checkout .woocommerce-message[role="alert"] {
			font-size: 18px;
			line-height: 28px;
			font-family: 'Roboto', sans-serif;
			font-weight: 400;
			color: #1D1F1E;
		}
		.woocommerce-checkout .woocommerce-message[role="alert"] a {
			font-weight: 500;
		}
		.woocommerce-checkout .woocommerce-message[role="alert"] a:hover {
			text-decoration: underline !important;
		}

		.woocommerce-checkout #list_payment_method_abapay_khqr, .woocommerce-checkout #list_payment_method_abapay_khqr label {
			display: flex !important;
		}

		/* Hide login form completely */
		.woocommerce-checkout .woocommerce-form-login {
			display: none !important;
		}
		/* Hide create account checkbox */
		.woocommerce-checkout .woocommerce-account-fields,
		.woocommerce-checkout .create-account {
			display: none !important;
		}

		.woocommerce-checkout .woocommerce-billing-fields {
			padding: 20px 10px;
			border: 1px solid #EFEFEF;
			border-radius: 12px;
		}

		.woocommerce-checkout .woocommerce-billing-fields h3 {
			position: relative;
			display: inline-block;
			padding-left: 20px;
		}
		.woocommerce-checkout .woocommerce-billing-fields h3::before {
			content: '1.';
			position: absolute;
			left: 0;
			bottom: 0;
			width: 24px;
			height: 24px;
			font-size: 16px;
			line-height: 24px;
			font-weight: 500;
			font-family: 'Roboto', sans-serif;
			color: #000;
			text-align: center;
		}

		.woocommerce-checkout .woocommerce-billing-fields__field-wrapper {
			padding: 0 20px;
		}
	</style>
	<div class="flex justify-between gap-8 w-full">
		<div class="w-full bg-white rounded-[20px] py-10 px-8">
            <h2 class="text-black font-roboto font-medium text-[44px] leading-[60px] mb-6">Checkout</h2>
			<div class="w-full h-[1px] bg-[rgba(0,0,0,0.1)] mb-[44px]"></div>
			<?php if ( ! is_user_logged_in() ) : ?>
				<h2 class="text-black font-roboto font-medium text-[24px] leading-[32px] mb-3">Log in or create an account using billing form below</h2>
			<?php endif; ?>
            <div class="flex flex-col w-full gap-5">
				<?php do_action( 'woocommerce_before_checkout_form', $checkout ); ?>
			</div>
			<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'woocommerce' ); ?>">

				<?php if ( $checkout->get_checkout_fields() ) : ?>

					<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

					<div class="" id="customer_details">
						<div class="">
							<?php wc_get_template( 'checkout/cart-items.php' ); ?>
							<?php do_action( 'woocommerce_checkout_billing' ); ?>
							<?php do_action( 'woocommerce_checkout_payment' ); ?>
							
							<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
							<div id="order_review" class="woocommerce-checkout-review-order">
								<?php do_action( 'woocommerce_checkout_order_review' ); ?>
							</div>
							<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
						</div>
					</div>

				<?php endif; ?>

				<?php /* Order review moved to right column above */ ?>

			</form>
			<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
			<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
		</div>
		<div class="w-full bg-white rounded-[20px] py-10 px-8 h-[min-content] sticky top-6 max-w-[550px]">
			<div class="">
				<div class="walla-order-summary">
					<h3 class="text-black font-roboto font-medium text-[20px] leading-[28px] mb-4"><?php esc_html_e( 'Order Summary', 'walla' ); ?></h3>
					<div class="border border-[#EFEFEF] rounded-[12px] bg-white p-4 text-[14px] font-roboto">
						<?php
						$cart_items = WC()->cart ? WC()->cart->get_cart() : array();
						$regular_total = 0;
						$sale_total = 0;
						$has_sale = false;
						foreach ( $cart_items as $item ) {
							$product = $item['data'];
							if ( ! $product ) {
								continue;
							}
							$qty = $item['quantity'];
							$regular_price = $product->get_regular_price();
							$price = $product->get_price();
							$regular_total += $regular_price * $qty;
							$sale_total += $price * $qty;
							if ( $regular_price > $price ) {
								$has_sale = true;
							}
						}
						?>
						<?php if ( $has_sale ) : ?>
							<div class="flex justify-between mb-2">
								<span><?php esc_html_e( 'Subtotal', 'walla' ); ?></span>
								<span><span style="text-decoration:line-through; color:#AAA;"><?php echo wc_price( $regular_total ); ?></span></span>
							</div>
							<div class="flex justify-between mb-2">
								<span><?php esc_html_e( 'Discount', 'walla' ); ?></span>
								<span style="color:#10B981;">-<?php echo wc_price( $regular_total - $sale_total ); ?></span>
							</div>
						<?php else : ?>
							<div class="flex justify-between mb-2">
								<span><?php esc_html_e( 'Subtotal', 'walla' ); ?></span>
								<span><?php echo wc_price( $regular_total ); ?></span>
							</div>
						<?php endif; ?>
						<?php if ( WC()->cart->get_coupons() ) : ?>
							<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
								<div class="flex justify-between mb-2">
									<span><?php echo esc_html( WC()->cart->get_coupon_label( $coupon ) ); ?></span>
									<span>-<?php wc_cart_totals_coupon_html( $coupon ); ?></span>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
							<div class="flex justify-between mb-2">
								<span><?php echo esc_html( $fee->name ); ?></span>
								<span><?php wc_cart_totals_fee_html( $fee ); ?></span>
							</div>
						<?php endforeach; ?>
						<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
							<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
								<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
									<div class="flex justify-between mb-2">
										<span><?php echo esc_html( $tax->label ); ?></span>
										<span><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
									</div>
								<?php endforeach; ?>
							<?php else : ?>
								<div class="flex justify-between mb-2">
									<span><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></span>
									<span><?php wc_cart_totals_taxes_total_html(); ?></span>
								</div>
							<?php endif; ?>
						<?php endif; ?>
						<div class="flex justify-between text-[16px] leading-[24px] font-medium mt-2">
							<span><?php esc_html_e( 'Total', 'walla' ); ?></span>
							<span><?php echo wc_price( $sale_total ); ?></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>