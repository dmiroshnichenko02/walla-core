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

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

?>

<style>
    body {
        background: #F3F3F3;
    }
    .woocommerce-form-coupon-toggle {
        display: none;
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
    padding-left: 40px !important;
    padding-right: 40px !important;
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
/* Floating labels */
.woocommerce-checkout .form-row { position: relative; }
.woocommerce-checkout .form-row { margin-bottom: 16px; }
.woocommerce-checkout .form-row:last-child { margin-bottom: 0; }
.woocommerce-checkout .woocommerce-billing-fields__field-wrapper > .form-row,
.woocommerce-checkout .woocommerce-shipping-fields__field-wrapper > .form-row,
.woocommerce-checkout .woocommerce-additional-fields__field-wrapper > .form-row { margin-bottom: 16px; }
.woocommerce-checkout .woocommerce-billing-fields__field-wrapper > .form-row:last-child,
.woocommerce-checkout .woocommerce-shipping-fields__field-wrapper > .form-row:last-child,
.woocommerce-checkout .woocommerce-additional-fields__field-wrapper > .form-row:last-child { margin-bottom: 0; }
.woocommerce-checkout .form-row > label {
    position: absolute;
    left: 40px;
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
/* Textarea label baseline */
.woocommerce-checkout .form-row textarea + label,
.woocommerce-checkout .form-row.floating-active textarea + label { top: 16px; transform: translateY(0); }
html {
    overflow-x: hidden;
}
/* Hide Additional Information block */
.woocommerce-checkout .woocommerce-additional-fields { display: none; }
</style>
<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'woocommerce' ); ?>">

<?php if ( $checkout->get_checkout_fields() ) : ?>

    <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

    <div class="col2-set" id="customer_details">
        <div class="col-1 bg-white rounded-[20px] py-10 px-8">
            <h2 class="text-black font-roboto font-medium text-[44px] leading-[60px]">Checkout</h2>
            <?php do_action( 'woocommerce_checkout_billing' ); ?>
            <?php do_action( 'woocommerce_checkout_payment' ); ?>
            <?php wc_get_template( 'checkout/cart-items.php' ); ?>
            <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
                <div id="order_review" class="woocommerce-checkout-review-order">
                    <?php do_action( 'woocommerce_checkout_order_review' ); ?>
                </div>
                <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
        </div>

        <div class="col-2">
            <div class="sticky top-6">
                <?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
                <h3 id="order_review_heading" class="sr-only"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h3>
                
            </div>
        </div>
    </div>

    <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

<?php endif; ?>

<?php /* Order review moved to right column above */ ?>

</form>
</div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

