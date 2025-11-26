<?php
/**
 * Lost password form / Password set form (for newaccount action)
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.2.0
 */

defined( 'ABSPATH' ) || exit;

// Check if this is password set form for new account
$is_new_account = isset( $_GET['action'] ) && $_GET['action'] === 'newaccount';
$key = isset( $_GET['key'] ) ? sanitize_text_field( $_GET['key'] ) : '';
$login = isset( $_GET['login'] ) ? sanitize_text_field( $_GET['login'] ) : '';

// If it's newaccount, show password set form
if ( $is_new_account && $key && $login ) {
	// Verify the reset key
	$user = get_user_by( 'login', $login );
	if ( $user ) {
		$valid_key = check_password_reset_key( $key, $login );
		
		if ( ! is_wp_error( $valid_key ) ) {
			// Show password set form
			do_action( 'woocommerce_before_reset_password_form' );
			?>
			
			<div class="px-4">
				<div class="container mx-auto">
					<div class="tutor-page-wrap walla-auth">
						<div class="walla-auth__container">
							<div class="walla-auth__left">
								<div class="tutor-login-form-wrapper">
									<div class="tutor-fs-5 tutor-color-black tutor-mb-32" style="font-size:32px;font-weight:500;margin-bottom:24px;line-height:52px;font-family:'Figtree';">
										<?php esc_html_e( 'Set Password', 'woocommerce' ); ?>
									</div>

									<p class="ttrb"><?php echo apply_filters( 'woocommerce_reset_password_message', esc_html__( 'Enter a new password below.', 'woocommerce' ) ); ?></p>

									<form method="post" class="tutor-reset-password-form tutor-ResetPassword lost_reset_password">
										<?php wp_nonce_field( 'reset_password', 'woocommerce-reset-password-nonce' ); ?>
										
										<input type="hidden" name="wc_reset_password" value="true" />
										<input type="hidden" name="reset_key" value="<?php echo esc_attr( $key ); ?>" />
										<input type="hidden" name="reset_login" value="<?php echo esc_attr( $login ); ?>" />

										<div class="tutor-form-row tutor-mt-16">
											<div class="tutor-form-col-12">
												<div class="tutor-form-group">
													<label><?php esc_html_e( 'New password', 'woocommerce' ); ?></label>
													<input type="password" name="password_1" id="password_1" autocomplete="new-password" required>
												</div>
											</div>
										</div>

										<div class="tutor-form-row">
											<div class="tutor-form-col-12">
												<div class="tutor-form-group">
													<label><?php esc_html_e( 'Re-enter new password', 'woocommerce' ); ?></label>
													<input type="password" name="password_2" id="password_2" autocomplete="new-password" required>
												</div>
											</div>
										</div>

										<div class="clear"></div>

										<?php do_action( 'woocommerce_resetpassword_form' ); ?>

										<div class="tutor-form-row">
											<div class="tutor-form-col-12">
												<div class="tutor-form-group">
													<button type="submit" class="tutor-btn tutor-btn-primary" value="<?php esc_attr_e( 'Save', 'woocommerce' ); ?>">
														<?php esc_html_e( 'Save', 'woocommerce' ); ?>
													</button>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>

							<div class="walla-auth__right" aria-hidden="true">
								<div class="min-h-full min-w-full flex justify-center items-center">
									<?php if ( function_exists('get_field') && get_field('right_image', 'option') ) : ?>
										<img src="<?php echo esc_url( get_field('right_image', 'option') ); ?>" alt="register">
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<style>
				.walla-auth__container{display:grid;grid-template-columns:1.5fr 2fr;gap:40px;align-items:stretch;min-height:80vh;padding:40px 0;box-sizing:border-box}
				.walla-auth__left{background:#F6F6F6;border-radius:20px;padding:125px 42px;display:flex;flex-direction:column;justify-content:center}
				.walla-auth .tutor-form-group input {height:52px;border-radius:28px;background:#fff;border:1px solid #e6e7eb;padding:0 18px;box-shadow:none}
				.walla-auth .tutor-btn{background:#000;color:#fff;width:100%;border-radius:200px;padding:20px;display:flex;justify-content:center;align-items:center}
				.walla-auth .tutor-btn:hover{background:#1D1F1E;color:#fff}
				.walla-auth input{padding-left: 40px !important; padding-right: 40px !important;}
				.walla-auth .tutor-forgot-password-form, .walla-auth .tutor-reset-password-form {padding: 0 !important;margin: 0;}
				.walla-auth .tutor-form-group {margin-bottom: 0;}
				.ttrb {
					font-family: "Roboto" !important;
					font-size: 16px !important;
					font-weight: 400;
					line-height: 24px;
					color: #1D1F1E !important;
				}
				.ttrb a {
					font-weight: 500 !important;
					text-decoration: underline !important;
				}
				@media(max-width:1024px){.walla-auth__container{grid-template-columns:1fr;min-height:0}.walla-auth__right{display:none}.walla-auth__left{padding:0;}}
				@media(max-width:767px){
					.tutor-login-form-wrapper .tutor-grid {grid-template-columns: 1fr !important;}
				}
			</style>

			<?php
			do_action( 'woocommerce_after_reset_password_form' );
			return;
		}
	}
}

// Default lost password form (should not be shown due to redirect, but keeping for fallback)
do_action( 'woocommerce_before_lost_password_form' );
?>

<div class="px-4">
	<div class="container mx-auto">
		<div class="tutor-page-wrap walla-auth">
			<div class="walla-auth__container">
				<div class="walla-auth__left">
					<div class="tutor-login-form-wrapper">
						<div class="tutor-fs-5 tutor-color-black tutor-mb-32" style="font-size:32px;font-weight:500;margin-bottom:24px;line-height:52px;font-family:'Figtree';">
							<?php esc_html_e( 'Reset password', 'woocommerce' ); ?>
						</div>

						<p class="ttrb"><?php echo apply_filters( 'woocommerce_lost_password_message', esc_html__( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'woocommerce' ) ); ?></p>

						<form method="post" class="tutor-forgot-password-form tutor-ResetPassword lost_reset_password">
							<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>
							
							<input type="hidden" name="wc_reset_password" value="true" />

							<div class="tutor-form-row tutor-mt-16">
								<div class="tutor-form-col-12">
									<div class="tutor-form-group">
										<label><?php esc_html_e( 'Username or email', 'woocommerce' ); ?></label>
										<input type="text" name="user_login" id="user_login" autocomplete="username" required>
									</div>
								</div>
							</div>

							<div class="clear"></div>

							<?php do_action( 'woocommerce_lostpassword_form' ); ?>

							<div class="tutor-form-row">
								<div class="tutor-form-col-12">
									<div class="tutor-form-group">
										<button type="submit" class="tutor-btn tutor-btn-primary" value="<?php esc_attr_e( 'Reset password', 'woocommerce' ); ?>">
											<?php esc_html_e( 'Reset password', 'woocommerce' ); ?>
										</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>

				<div class="walla-auth__right" aria-hidden="true">
					<div class="min-h-full min-w-full flex justify-center items-center">
						<?php if ( function_exists('get_field') && get_field('right_image', 'option') ) : ?>
							<img src="<?php echo esc_url( get_field('right_image', 'option') ); ?>" alt="register">
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
	.walla-auth__container{display:grid;grid-template-columns:1.5fr 2fr;gap:40px;align-items:stretch;min-height:80vh;padding:40px 0;box-sizing:border-box}
	.walla-auth__left{background:#F6F6F6;border-radius:20px;padding:125px 42px;display:flex;flex-direction:column;justify-content:center}
	.walla-auth .tutor-form-group input {height:52px;border-radius:28px;background:#fff;border:1px solid #e6e7eb;padding:0 18px;box-shadow:none}
	.walla-auth .tutor-btn{background:#000;color:#fff;width:100%;border-radius:200px;padding:20px;display:flex;justify-content:center;align-items:center}
	.walla-auth .tutor-btn:hover{background:#1D1F1E;color:#fff}
	.walla-auth input{padding-left: 40px !important; padding-right: 40px !important;}
	.walla-auth .tutor-forgot-password-form, .walla-auth .tutor-reset-password-form {padding: 0 !important;margin: 0;}
	.walla-auth .tutor-form-group {margin-bottom: 0;}
	.ttrb {
		font-family: "Roboto" !important;
		font-size: 16px !important;
		font-weight: 400;
		line-height: 24px;
		color: #1D1F1E !important;
	}
	.ttrb a {
		font-weight: 500 !important;
		text-decoration: underline !important;
	}
	@media(max-width:1024px){.walla-auth__container{grid-template-columns:1fr;min-height:0}.walla-auth__right{display:none}.walla-auth__left{padding:0;}}
	@media(max-width:767px){
		.tutor-login-form-wrapper .tutor-grid {grid-template-columns: 1fr !important;}
	}
</style>

<?php
do_action( 'woocommerce_after_lost_password_form' );

