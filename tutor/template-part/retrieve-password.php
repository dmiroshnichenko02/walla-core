<?php
/**
 * Password retrieve template
 *
 * @package Tutor\Templates
 * @subpackage Template_Part
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 1.4.3
 */

defined( 'ABSPATH' ) || exit;

use TUTOR\Input; ?>

<div class="px-4">
    <div class="container mx-auto">
	
		<div class="tutor-page-wrap walla-auth">
	        <div class="walla-auth__container">
	            <div class="walla-auth__left">
	                <div class="tutor-login-form-wrapper">
	                    <div class="tutor-fs-5 tutor-color-black tutor-mb-32" style="font-size:32px;font-weight:500;margin-bottom:24px;line-height:52px;font-family:'Figtree';">
	                        <?php _e('Reset password', 'walla'); ?>
	                    </div>

						<?php
						if ( Input::get( 'reset_key' ) && Input::get( 'user_id' ) ) {
							tutor_load_template( 'template-part.form-retrieve-password' );
						} else {
							do_action( 'tutor_before_retrieve_password_form' );
							?>
							<form method="post" class="tutor-forgot-password-form tutor-ResetPassword lost_reset_password">
								<?php
									tutor_alert( null, 'any' );
									tutor_nonce_field();
								?>

								<input type="hidden" name="tutor_action" value="tutor_retrieve_password">
						        <p><?php echo apply_filters( 'tutor_lost_password_message', esc_html__( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'tutor' ) ); ?></p><?php // @codingStandardsIgnoreLine ?>

								<div class="tutor-form-row tutor-mt-16">
									<div class="tutor-form-col-12">
										<div class="tutor-form-group">
											<label><?php esc_html_e( 'Username or email', 'tutor' ); ?></label>
											<input type="text" name="user_login" id="user_login" autocomplete="username">
										</div>
									</div>
								</div>

								<div class="clear"></div>

								<?php do_action( 'tutor_lostpassword_form' ); ?>

								<div class="tutor-form-row">
									<div class="tutor-form-col-12">
										<div class="tutor-form-group">
											<button type="submit" class="tutor-btn tutor-btn-primary" value="<?php esc_attr_e( 'Reset password', 'tutor' ); ?>">
												<?php esc_html_e( 'Reset password', 'tutor' ); ?>
											</button>
										</div>
									</div>
								</div>

							</form>

							<?php
							do_action( 'tutor_after_retrieve_password_form' );
						} ?>

					</div>
            	</div>

	            <div class="walla-auth__right" aria-hidden="true">
	                <div class="min-h-full min-w-full flex justify-center items-center">
	                    <img src="<?php echo get_field('right_image', 'options') ?>" alt="register">
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
    .shw-p.shw path {
            stroke: #000 !important;
        }
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