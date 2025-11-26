<?php
/**
 * Custom Payment Section with Card-style Gateway Selector
 *
 * @package WooCommerce\Templates
 */

defined( 'ABSPATH' ) || exit;

if ( ! $available_gateways = WC()->payment_gateways()->get_available_payment_gateways() ) {
    $available_gateways = array();
}

WC()->payment_gateways()->set_current_gateway( $available_gateways );
?>

<div id="payment" class="woocommerce-checkout-payment">
    <h3 class="text-[#1D1F1E] font-roboto font-medium text-[16px] leading-[24px] mb-3"><?php esc_html_e( '2. Choose Payment Method', 'walla' ); ?></h3>
    <ul class="wc_payment_methods payment_methods methods flex flex-col gap-3">
        <?php if ( ! empty( $available_gateways ) ) : ?>
            <?php foreach ( $available_gateways as $gateway ) : ?>
                <li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?>">
                    <label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>" class="block cursor-pointer">
                        <input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio hidden" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />
                        <div class="rounded-[12px] border border-[#EDEDED] bg-white p-4 text-center transition shadow-[0_0_0_2px_transparent]">
                            <div class="text-[14px] font-roboto text-[#1D1F1E] mb-1"><?php echo wp_kses_post( $gateway->get_title() ); ?></div>
                            <div class="text-[12px] text-[#6B7280] font-roboto"><?php echo wp_kses_post( $gateway->get_description() ); ?></div>
                        </div>
                    </label>
                    <?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
                        <div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>" <?php if ( ! $gateway->chosen ) : ?>style="display:none;"<?php endif; ?>>
                            <?php $gateway->payment_fields(); ?>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        <?php else : ?>
            <li class="wc_payment_method"><?php echo wp_kses_post( apply_filters( 'woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? __( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) : __( 'Please fill in your details above to see available payment methods.', 'woocommerce' ) ) ); ?></li>
        <?php endif; ?>
    </ul>

    <div class="form-row place-order mt-4">
        <noscript>
            <?php
            /* translators: $1 and $2 opening and closing emphasis tags respectively */
            printf( esc_html__( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce' ), '<em>', '</em>' );
            ?>
            <br/>
            <button type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>"><?php esc_html_e( 'Update totals', 'woocommerce' ); ?></button>
        </noscript>

        <?php wc_get_template( 'checkout/terms.php' ); ?>

        <?php do_action( 'woocommerce_review_order_before_submit' ); ?>

        <?php echo apply_filters( 'woocommerce_order_button_html', '<button type="submit" class="tutor-btn tutor-btn-primary tutor-btn-block lgn-btn w-full" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( __( 'Purchase', 'walla' ) ) . '" data-value="' . esc_attr( __( 'Purchase', 'walla' ) ) . '">' . esc_html( __( 'Purchase', 'walla' ) ) . '</button>' ); // @codingStandardsIgnoreLine ?>

        <?php do_action( 'woocommerce_review_order_after_submit' ); ?>

        <?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
    </div>
</div>

<style>
/* Payment method card border when checked */
#payment .wc_payment_methods .wc_payment_method label input[type="radio"]:checked + div {
    border-color: #E8F501;
    box-shadow: 0 0 0 2px #E8F501;
}
.payment_box.payment_method_cod {
    display: none !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function(){
    var list = document.querySelectorAll('#payment .wc_payment_methods .wc_payment_method');
    list.forEach(function(item){
        var input = item.querySelector('input[type="radio"]');
        var cardArray = item.getElementsByClassName('rounded-[12px]');
        var card  = cardArray.length ? cardArray[0] : null;
        if (!input || !card) return;
        function sync(){
            if (input.checked) {
                card.style.boxShadow = '0 0 0 2px #E8F501';
                card.style.borderColor = '#E8F501';
            } else {
                card.style.boxShadow = '0 0 0 2px transparent';
                card.style.borderColor = '#EDEDED';
            }
        }
        sync();
        card.addEventListener('click', function(){ input.checked = true; input.dispatchEvent(new Event('change', { bubbles: true })); sync(); });
        input.addEventListener('change', sync);
    });
    // Inject cart visual + items under payment section
    var paymentRoot = document.getElementById('payment');
    if (paymentRoot) {
        var mount = document.createElement('div');
        mount.className = 'mt-4';
        paymentRoot.appendChild(mount);
    }
});
</script>