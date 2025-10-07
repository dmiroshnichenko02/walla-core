<?php
/**
 * Custom Review Order
 * Shows order summary with product thumbnail and author
 *
 * @package WooCommerce\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="walla-order-summary">
    <h3 class="text-black font-roboto font-medium text-[20px] leading-[28px] mb-4"><?php esc_html_e( 'Order Summary', 'walla' ); ?></h3>
    <div class="border border-[#EFEFEF] rounded-[12px] bg-white p-4 text-[14px] font-roboto">
        <div class="flex justify-between mb-2"><span><?php esc_html_e( 'Subtotal', 'walla' ); ?></span><span><?php wc_cart_totals_subtotal_html(); ?></span></div>
        <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
            <div class="flex justify-between mb-2"><span><?php echo esc_html( $fee->name ); ?></span><span><?php wc_cart_totals_fee_html( $fee ); ?></span></div>
        <?php endforeach; ?>
        <?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
            <?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
                <?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
                    <div class="flex justify-between mb-2"><span><?php echo esc_html( $tax->label ); ?></span><span><?php echo wp_kses_post( $tax->formatted_amount ); ?></span></div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="flex justify-between mb-2"><span><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></span><span><?php wc_cart_totals_taxes_total_html(); ?></span></div>
            <?php endif; ?>
        <?php endif; ?>
        <div class="flex justify-between text-[16px] leading-[24px] font-medium mt-2"><span><?php esc_html_e( 'Total', 'walla' ); ?></span><span><?php wc_cart_totals_order_total_html(); ?></span></div>
    </div>
</div>


