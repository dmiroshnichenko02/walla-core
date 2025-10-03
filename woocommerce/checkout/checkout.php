<?php
/**
 * Checkout Wrapper
 *
 * Keep this file minimal; load the actual WooCommerce checkout form template.
 */

defined( 'ABSPATH' ) || exit;

get_header();

wc_get_template( 'checkout/form-checkout.php', array( 'checkout' => WC()->checkout() ) );

get_footer();