<?php
/**
 * Cart items block for checkout left column
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$cart_items = WC()->cart ? WC()->cart->get_cart() : array();
if ( empty( $cart_items ) ) {
    return;
}

$items = array_values( $cart_items );
$first = $items[0];
$first_product = isset( $first['data'] ) ? $first['data'] : null;
$first_id = $first_product ? $first_product->get_id() : 0;
?>

<div class="walla-cart-visual mb-4">
    <?php if ( $first_product ) : ?>
        <div class="flex gap-3 items-center p-4 border border-[#EFEFEF] rounded-[12px] bg-white">
            <div class="thumb w-[96px] h-[72px] overflow-hidden rounded-[12px]">
                <?php echo get_the_post_thumbnail( $first_id, 'medium', array( 'class' => 'w-full h-full object-cover' ) ); ?>
            </div>
            <div class="flex-1">
                <div class="text-[12px] text-[#6B7280] font-roboto mb-1">
                    <?php
                    $author_id = get_post_field( 'post_author', $first_id );
                    $author_name = $author_id ? get_the_author_meta( 'display_name', $author_id ) : '';
                    if ( $author_name ) {
                        echo esc_html__( 'by', 'walla' ) . ' ' . esc_html( $author_name );
                    }
                    ?>
                </div>
                <div class="text-[14px] leading-[20px] font-roboto text-[#1D1F1E] font-medium">
                    <?php echo esc_html( $first_product->get_name() ); ?>
                </div>
                <div class="text-[12px] text-[#6B7280] font-roboto mt-1">
                    <?php echo wc_get_formatted_cart_item_data( $first ); ?>
                </div>
            </div>
            <div class="text-[14px] leading-[20px] font-roboto text-[#1D1F1E]">
                <?php echo WC()->cart->get_product_price( $first_product ); ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<!-- 
<div class="walla-cart-items">
    <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
        $product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
        if ( ! $product || ! $product->exists() || $cart_item['quantity'] <= 0 ) {
            continue;
        }
        $product_id = $product->get_id();
        ?>
        <div class="flex gap-3 items-center p-3 border border-[#EFEFEF] rounded-[12px] bg-white mb-3">
            <div class="thumb w-[64px] h-[64px] overflow-hidden rounded-[12px]">
                <?php echo get_the_post_thumbnail( $product_id, 'thumbnail', array( 'class' => 'w-full h-full object-cover' ) ); ?>
            </div>
            <div class="flex-1">
                <div class="text-[14px] leading-[20px] font-roboto text-[#1D1F1E] font-medium"><?php echo esc_html( $product->get_name() ); ?></div>
                <div class="text-[12px] text-[#6B7280] font-roboto">
                    x<?php echo intval( $cart_item['quantity'] ); ?>
                </div>
            </div>
            <div class="text-[14px] leading-[20px] font-roboto text-[#1D1F1E]">
                <?php echo WC()->cart->get_product_price( $product ); ?>
            </div>
        </div>
    <?php endforeach; ?>
</div> -->


