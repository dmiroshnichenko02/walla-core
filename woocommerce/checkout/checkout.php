<?php
/**
 * Checkout Wrapper
 *
 * Keep this file minimal; load the actual WooCommerce checkout form template.
 */

defined( 'ABSPATH' ) || exit;

$checkout = WC()->checkout();

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

if ( isset( $_SESSION['walla_redirect_after_login'] ) ) {
	$target_url = $_SESSION['walla_redirect_after_login'];
	unset( $_SESSION['walla_redirect_after_login'] );
	if ( $target_url && esc_url_raw($target_url) !== esc_url_raw( wc_get_checkout_url() ) ) {
		wp_redirect( $target_url );
		exit;
	}
}

get_header();

wc_get_template( 'checkout/form-checkout.php', array( 'checkout' => $checkout ) );

get_footer();