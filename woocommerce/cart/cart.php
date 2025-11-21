<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.1.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<section class="cart-page py-10 md:py-12 px-4">
	<div class="container mx-auto">
		<div class="cart-header mb-8">
			<h1 class="font-manrope text-[32px] leading-[40px] lg:text-[48px] lg:leading-[58px] font-medium text-black mb-4"><?php _e('Shopping Cart', 'walla'); ?></h1>
			<?php if (WC()->cart->is_empty()): ?>
				<div class="empty-cart-message bg-[#F6F6F6] rounded-[20px] p-8 text-center">
					<p class="font-inter text-[18px] text-black/70 mb-6"><?php _e('Your cart is currently empty.', 'walla'); ?></p>
					<a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="inline-flex bg-p2 rounded-[8px] py-3 px-6 text-black font-inter font-medium hover:opacity-70 transition">
						<?php _e('Continue Shopping', 'walla'); ?>
					</a>
				</div>
			<?php else: ?>
		</div>

		<div class="cart-content bg-white rounded-[20px] md:rounded-[32px] p-4 md:p-8 shadow-sm border border-[#E8E8E8]">

<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>

			<div class="cart-table-wrapper overflow-x-auto">
				<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents w-full" cellspacing="0">
					<thead>
						<tr class="bg-[#F4F4F4]">
							<th class="product-remove text-left py-4 px-4 font-inter font-medium text-[16px] text-black"><span class="screen-reader-text"><?php esc_html_e( 'Remove item', 'woocommerce' ); ?></span></th>
							<th class="product-thumbnail text-left py-4 px-4 font-inter font-medium text-[16px] text-black"><span class="screen-reader-text"><?php esc_html_e( 'Thumbnail image', 'woocommerce' ); ?></span></th>
							<th scope="col" class="product-name text-left py-4 px-4 font-inter font-medium text-[16px] text-black"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
							<th scope="col" class="product-price text-left py-4 px-4 font-inter font-medium text-[16px] text-black"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
							<th scope="col" class="product-quantity text-left py-4 px-4 font-inter font-medium text-[16px] text-black"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
							<th scope="col" class="product-subtotal text-left py-4 px-4 font-inter font-medium text-[16px] text-black"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
						</tr>
					</thead>
		<tbody>
			<?php do_action( 'woocommerce_before_cart_contents' ); ?>

			<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
				/**
				 * Filter the product name.
				 *
				 * @since 2.1.0
				 * @param string $product_name Name of the product in the cart.
				 * @param array $cart_item The product in the cart.
				 * @param string $cart_item_key Key for the product in the cart.
				 */
				$product_name = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>
						<tr class="woocommerce-cart-form__cart-item border-b border-[#EDEDED] hover:bg-[#F9F9F9] transition <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

							<td class="product-remove py-4 px-4">
								<?php
									echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										'woocommerce_cart_item_remove_link',
										sprintf(
											'<a role="button" href="%s" class="remove relative w-8 h-8 flex items-center justify-center bg-red-100 hover:bg-red-200 text-red-600 rounded-full transition" aria-label="%s" data-product_id="%s" data-product_sku="%s">
												<svg class="svg-close" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M12 4L4 12M4 4L12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</a>',
											esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
											/* translators: %s is the product name */
											esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
											esc_attr( $product_id ),
											esc_attr( $_product->get_sku() )
										),
										$cart_item_key
									);
								?>
							</td>

							<td class="product-thumbnail py-4 px-4">
							<?php
							/**
							 * Filter the product thumbnail displayed in the WooCommerce cart.
							 *
							 * This filter allows developers to customize the HTML output of the product
							 * thumbnail. It passes the product image along with cart item data
							 * for potential modifications before being displayed in the cart.
							 *
							 * @param string $thumbnail     The HTML for the product image.
							 * @param array  $cart_item     The cart item data.
							 * @param string $cart_item_key Unique key for the cart item.
							 *
							 * @since 2.1.0
							 */
							$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail'), $cart_item, $cart_item_key );

							if ( ! $product_permalink ) {
								echo '<div class="w-16 h-16 rounded-[8px] overflow-hidden bg-[#F4F4F4] flex items-center justify-center">' . $thumbnail . '</div>'; // PHPCS: XSS ok.
							} else {
								printf( '<a href="%s" class="w-16 h-16 rounded-[8px] overflow-hidden bg-[#F4F4F4] flex items-center justify-center block">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
							}
							?>
							</td>

							<td scope="row" role="rowheader" class="product-name py-4 px-4" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
							<?php
							if ( ! $product_permalink ) {
								echo '<div class="font-inter font-medium text-[16px] text-black">' . wp_kses_post( $product_name . '&nbsp;' ) . '</div>';
							} else {
								/**
								 * This filter is documented above.
								 *
								 * @since 2.1.0
								 */
								echo '<div class="font-inter font-medium text-[16px] text-black">' . wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s" class="text-black hover:text-p3 transition">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) ) . '</div>';
							}

							do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

							// Meta data.
							echo '<div class="mt-2 text-[14px] text-black/70">' . wc_get_formatted_cart_item_data( $cart_item ) . '</div>'; // PHPCS: XSS ok.

							// Backorder notification.
							if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
								echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification text-[14px] text-orange-600 mt-2">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
							}
							?>
							</td>

							<td class="product-price py-4 px-4" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
								<div class="font-inter font-medium text-[16px] text-black">
									<?php
										echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
									?>
								</div>
							</td>

							<td class="product-quantity py-4 px-4" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
							<?php
							if ( $_product->is_sold_individually() ) {
								$min_quantity = 1;
								$max_quantity = 1;
							} else {
								$min_quantity = 0;
								$max_quantity = $_product->get_max_purchase_quantity();
							}

							$product_quantity = woocommerce_quantity_input(
								array(
									'input_name'   => "cart[{$cart_item_key}][qty]",
									'input_value'  => $cart_item['quantity'],
									'max_value'    => $max_quantity,
									'min_value'    => $min_quantity,
									'product_name' => $product_name,
									'classes'      => 'w-20 h-10 text-center border border-[#E8E8E8] rounded-[8px] font-inter font-medium text-[14px] focus:border-p3 focus:outline-none',
								),
								$_product,
								false
							);

							echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
							?>
							</td>

							<td class="product-subtotal py-4 px-4" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>">
								<div class="font-inter font-medium text-[16px] text-black">
									<?php
										echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
									?>
								</div>
							</td>
						</tr>
					<?php
				}
			}
			?>

			<?php do_action( 'woocommerce_cart_contents' ); ?>

						<!-- <tr>
							<td colspan="6" class="actions py-6">
								<div class="flex flex-col md:flex-row gap-4 md:gap-6 items-start md:items-center justify-between">
									<?php if ( wc_coupons_enabled() ) { ?>
										<div class="coupon flex flex-col md:flex-row gap-3 md:gap-4">
											<label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label>
											<input type="text" name="coupon_code" class="input-text w-full md:w-64 h-12 px-4 border border-[#E8E8E8] rounded-[8px] font-inter text-[14px] focus:border-p3 focus:outline-none" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" />
											<button type="submit" class="button bg-black text-white px-6 py-3 rounded-[8px] font-inter font-medium text-[14px] hover:bg-black/80 transition<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>">
												<?php esc_html_e( 'Apply coupon', 'woocommerce' ); ?>
											</button>
											<?php do_action( 'woocommerce_cart_coupon' ); ?>
										</div>
									<?php } ?>

									<?php do_action( 'woocommerce_cart_actions' ); ?>

									<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
								</div>
							</td>
						</tr> -->

			<?php do_action( 'woocommerce_after_cart_contents' ); ?>
					</tbody>
				</table>
				<?php do_action( 'woocommerce_after_cart_table' ); ?>
			</form>

			<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

			<div class="cart-collaterals mt-8">
				<?php
					/**
					 * Cart collaterals hook.
					 *
					 * @hooked woocommerce_cross_sell_display
					 * @hooked woocommerce_cart_totals - 10
					 */
					do_action( 'woocommerce_cart_collaterals' );
				?>
			</div>
		</div>
		<?php endif; ?>
	</div>
</section>

<?php do_action( 'woocommerce_after_cart' ); ?>


