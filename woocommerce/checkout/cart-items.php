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

if ( isset($_POST['walla_remove_key']) ) {
    $key = sanitize_text_field($_POST['walla_remove_key']);
    WC()->cart->remove_cart_item($key);
    echo '<script>window.location.href="' . esc_url( wc_get_checkout_url() ) . '";</script>';
}
?>

<div class="walla-cart-items mt-5">
    <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
        $product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
        if ( ! $product || ! $product->exists() || $cart_item['quantity'] <= 0 ) {
            continue;
        }
        $product_id = $product->get_id();
        ?>
        <div class="flex gap-3 items-center p-4 border border-[#EFEFEF] rounded-[12px] bg-white relative mb-3">
            <div style="position:absolute;top:-10px;left:-10px;z-index:10">
            <form method="post"  onsubmit="event.stopPropagation();">
                <input type="hidden" name="walla_remove_key" value="<?php echo esc_attr( $cart_item_key ); ?>">
                <button type="submit" style="border:none;background:rgba(255,255,255,0.9);border-radius:50%;width:24px;height:24px;display:flex;align-items:center;justify-content:center;font-size:18px;line-height:1;cursor:pointer;color:#666;box-shadow:0 2px 4px rgba(0,0,0,0.1);" title="Remove" onmouseover="this.style.background='rgba(255,255,255,1)';this.style.color='#000';" onmouseout="this.style.background='rgba(255,255,255,0.9)';this.style.color='#666';" onclick="event.preventDefault(); event.stopPropagation(); this.form.submit();">&times;</button>
            </form>
            </div>
            <div class="thumb w-[96px] h-[72px] overflow-hidden rounded-[12px]">
                <?php echo get_the_post_thumbnail( $product_id, 'medium', array( 'class' => 'w-full h-full object-cover' ) ); ?>
            </div>
            <div class="flex-1">
                <div class="text-[12px] text-[#6B7280] font-roboto mb-1">
                    <?php
                    $author_id = get_post_field( 'post_author', $product_id );
                    $author_name = $author_id ? get_the_author_meta( 'display_name', $author_id ) : '';
                    if ( $author_name ) {
                        echo esc_html__( 'by', 'walla' ) . ' ' . esc_html( $author_name );
                    }
                    ?>
                </div>
                <div class="text-[14px] leading-[20px] font-roboto text-[#1D1F1E] font-medium">
                    <?php echo esc_html( $product->get_name() ); ?>
                </div>
                <div class="text-[12px] text-[#6B7280] font-roboto mt-1">
                    x<?php echo intval( $cart_item['quantity'] ); ?>
                </div>
            </div>
            <div class="text-[14px] leading-[20px] font-roboto text-[#1D1F1E]">
                <?php echo WC()->cart->get_product_price( $product ); ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>


