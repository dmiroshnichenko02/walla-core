<?php
/**
 * Registration template
 *
 * @package Tutor\Templates
 * @subpackage Dashboard\Instructor
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 1.4.3
 */

?>

<?php if ( ! get_option( 'users_can_register', false ) ) : ?>

	<?php
		$args = array(
			'image_path'  => tutor()->url . 'assets/images/construction.png',
			'title'       => __( 'Oooh! Access Denied', 'tutor' ),
			'description' => __( 'You do not have access to this area of the application. Please refer to your system  administrator.', 'tutor' ),
			'button'      => array(
				'text'  => __( 'Go to Home', 'tutor' ),
				'url'   => get_home_url(),
				'class' => 'tutor-btn',
			),
		);
		tutor_load_template( 'feature_disabled', $args );
		?>

<?php else : ?>

<div class="px-4">
    <div class="container mx-auto">
    <div class="tutor-page-wrap walla-auth">
        <div class="walla-auth__container">

            <div class="walla-auth__left">
                <div class="tutor-login-form-wrapper">

                    <div class="tutor-fs-5 tutor-color-black tutor-mb-32" style="font-size:32px;font-weight:500;margin-bottom:30px;line-height:40px;font-family:'Figtree';">
                        <?php _e('Create Instructor Account', 'walla'); ?>
                    </div>

					<div id="tutor-registration-wrap">

						<?php do_action( 'tutor_before_instructor_reg_form' ); ?>

						<form method="post" enctype="multipart/form-data" id="tutor-registration-form">

							<?php do_action( 'tutor_instructor_reg_form_start' ); ?>

							<?php wp_nonce_field( tutor()->nonce_action, tutor()->nonce ); ?>
							<input type="hidden" value="tutor_register_instructor" name="tutor_action"/>

							<?php
							$errors = apply_filters( 'tutor_instructor_register_validation_errors', array() );//phpcs:ignore
							if ( is_array( $errors ) && count( $errors ) ) {
								echo '<div class="tutor-alert tutor-warning"><ul class="tutor-required-fields">';
								foreach ( $errors as $error_key => $error_value ) {
									echo wp_kses( "<li>{$error_value}</li>", array( 'li' => array() ) );
								}
								echo '</ul></div>';
							}
							?>

							<div class="tutor-form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
								<div class="tutor-form-col-6">
									<div class="tutor-form-group">
										<label>
											<?php esc_html_e( 'First Name', 'tutor' ); ?>
										</label>
		 								<div class="relative">
		                               		<svg class="absolute top-1/2 left-3 transform -translate-y-1/4" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 16.75V15.25C16 14.4544 15.6839 13.6913 15.1213 13.1287C14.5587 12.5661 13.7956 12.25 13 12.25H7C6.20435 12.25 5.44129 12.5661 4.87868 13.1287C4.31607 13.6913 4 14.4544 4 15.25V16.75" stroke="#1D1F1E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 9.25C11.6569 9.25 13 7.90685 13 6.25C13 4.59315 11.6569 3.25 10 3.25C8.34315 3.25 7 4.59315 7 6.25C7 7.90685 8.34315 9.25 10 9.25Z" stroke="#1D1F1E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
											<input type="text" name="first_name" value="<?php echo esc_attr( tutor_utils()->input_old( 'first_name' ) ); ?>" placeholder="<?php esc_html_e( 'First Name', 'tutor' ); ?>" required autocomplete="given-name">
										</div>
									</div>
								</div>

								<div class="tutor-form-col-6">
									<div class="tutor-form-group">
										<label>
											<?php esc_html_e( 'Last Name', 'tutor' ); ?>
										</label>
										<div class="relative">
		                               		<svg class="absolute top-1/2 left-3 transform -translate-y-1/4" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 16.75V15.25C16 14.4544 15.6839 13.6913 15.1213 13.1287C14.5587 12.5661 13.7956 12.25 13 12.25H7C6.20435 12.25 5.44129 12.5661 4.87868 13.1287C4.31607 13.6913 4 14.4544 4 15.25V16.75" stroke="#1D1F1E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 9.25C11.6569 9.25 13 7.90685 13 6.25C13 4.59315 11.6569 3.25 10 3.25C8.34315 3.25 7 4.59315 7 6.25C7 7.90685 8.34315 9.25 10 9.25Z" stroke="#1D1F1E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
											<input type="text" name="last_name" value="<?php echo esc_attr( tutor_utils()->input_old( 'last_name' ) ); ?>" placeholder="<?php esc_html_e( 'Last Name', 'tutor' ); ?>" required autocomplete="family-name">
										</div>	
									</div>
								</div>


							</div>

							<div class="tutor-form-row">

								<div class="tutor-form-col-6">
									<div class="tutor-form-group">
										<label>
											<?php esc_html_e( 'User Name', 'tutor' ); ?>
										</label>
										<div class="relative">
		                               		<svg class="absolute top-1/2 left-3 transform -translate-y-1/4" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 16.75V15.25C16 14.4544 15.6839 13.6913 15.1213 13.1287C14.5587 12.5661 13.7956 12.25 13 12.25H7C6.20435 12.25 5.44129 12.5661 4.87868 13.1287C4.31607 13.6913 4 14.4544 4 15.25V16.75" stroke="#1D1F1E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 9.25C11.6569 9.25 13 7.90685 13 6.25C13 4.59315 11.6569 3.25 10 3.25C8.34315 3.25 7 4.59315 7 6.25C7 7.90685 8.34315 9.25 10 9.25Z" stroke="#1D1F1E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
											<input type="text" name="user_login" class="tutor_user_name" value="<?php echo esc_attr( tutor_utils()->input_old( 'user_login' ) ); ?>" placeholder="<?php esc_html_e( 'User Name', 'tutor' ); ?>" required autocomplete="username">
										</div>
									</div>
								</div>

								<div class="tutor-form-col-6">
									<div class="tutor-form-group">
										<label>
											<?php esc_html_e( 'E-Mail', 'tutor' ); ?>
										</label>
										<div class="relative">
                               				<svg class="absolute top-1/2 left-3 transform -translate-y-1/4" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.66602 5L7.42687 8.26414C9.55068 9.46751 10.448 9.46751 12.5718 8.26414L18.3327 5" stroke="#141B34" stroke-width="1.5" stroke-linejoin="round"/><path d="M1.67915 11.23C1.73363 13.7846 1.76087 15.0619 2.70348 16.0081C3.64609 16.9543 4.95796 16.9873 7.58171 17.0532C9.19878 17.0938 10.7999 17.0938 12.417 17.0532C15.0407 16.9873 16.3526 16.9543 17.2952 16.0081C18.2378 15.0619 18.2651 13.7846 18.3195 11.23C18.3371 10.4086 18.3371 9.59206 18.3195 8.77065C18.2651 6.21604 18.2378 4.93874 17.2952 3.99254C16.3526 3.04635 15.0407 3.01339 12.417 2.94747C10.7999 2.90684 9.19877 2.90683 7.5817 2.94746C4.95796 3.01338 3.64608 3.04634 2.70348 3.99253C1.76087 4.93872 1.73363 6.21603 1.67915 8.77064C1.66164 9.59205 1.66164 10.4086 1.67915 11.23Z" stroke="#141B34" stroke-width="1.5" stroke-linejoin="round"/></svg>
											<input type="text" name="email" value="<?php echo esc_attr( tutor_utils()->input_old( 'email' ) ); ?>" placeholder="<?php esc_html_e( 'E-Mail', 'tutor' ); ?>" required autocomplete="email">
										</div>
									</div>
								</div>

							</div>

							<div class="tutor-form-row">
								<div class="tutor-form-col-6">
									<div class="tutor-form-group">
										<div class="tutor-password-strength-checker">
											<div class="tutor-password-field">
												<label>
													<?php esc_html_e( 'Password', 'tutor' ); ?>
												</label>
				                                <div class="relative">
				                                	<svg class="absolute top-1/2 left-3 transform -translate-y-1/4" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.0762 12.917H12.0837M7.91699 12.917H7.92446" stroke="#141B34" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M3.55618 15.704C3.74358 17.0958 4.89645 18.1863 6.29939 18.2508C7.4799 18.305 8.67909 18.3333 9.99967 18.3333C11.3203 18.3333 12.5194 18.305 13.7 18.2508C15.1029 18.1863 16.2558 17.0958 16.4432 15.704C16.5655 14.7956 16.6663 13.8647 16.6663 12.9167C16.6663 11.9686 16.5655 11.0378 16.4432 10.1294C16.2558 8.73752 15.1029 7.64707 13.7 7.58258C12.5194 7.52831 11.3203 7.5 9.99968 7.5C8.67909 7.5 7.4799 7.52831 6.29939 7.58258C4.89645 7.64707 3.74358 8.73752 3.55618 10.1294C3.43388 11.0378 3.33301 11.9686 3.33301 12.9167C3.33301 13.8647 3.43388 14.7956 3.55618 15.704Z" stroke="#141B34" stroke-width="1.5"/><path d="M6.25 7.50033V5.41699C6.25 3.34592 7.92893 1.66699 10 1.66699C12.0711 1.66699 13.75 3.34592 13.75 5.41699V7.50033" stroke="#141B34" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
													<input class="password-checker" id="tutor-new-password" type="password" name="password" value="<?php echo esc_attr( tutor_utils()->input_old( 'password' ) ); ?>" placeholder="<?php esc_html_e( 'Password', 'tutor' ); ?>" required autocomplete="new-password">
												</div>
												<span class="show-hide-btn"></span>
											</div>
											<div class="tutor-password-strength-hint">
												<div class="indicator">
													<span class="weak"></span>
													<span class="medium"></span>
													<span class="strong"></span>
												</div>
												<div class="text tutor-fs-7 tutor-color-muted"></div>
											</div>
										</div>
									</div>
								</div>

								<div class="tutor-form-col-6">
									<div class="tutor-form-group">
										<label>
											<?php esc_html_e( 'Password confirmation', 'tutor' ); ?>
										</label>

										<div class="tutor-form-wrap">
											<span class="tutor-validation-icon tutor-icon-mark tutor-color-success tutor-form-icon tutor-form-icon-reverse" style="display: none;"></span>
											<div class="relative w-full">
				                                <svg class="absolute top-1/2 left-3 transform -translate-y-1/4" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.0762 12.917H12.0837M7.91699 12.917H7.92446" stroke="#141B34" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M3.55618 15.704C3.74358 17.0958 4.89645 18.1863 6.29939 18.2508C7.4799 18.305 8.67909 18.3333 9.99967 18.3333C11.3203 18.3333 12.5194 18.305 13.7 18.2508C15.1029 18.1863 16.2558 17.0958 16.4432 15.704C16.5655 14.7956 16.6663 13.8647 16.6663 12.9167C16.6663 11.9686 16.5655 11.0378 16.4432 10.1294C16.2558 8.73752 15.1029 7.64707 13.7 7.58258C12.5194 7.52831 11.3203 7.5 9.99968 7.5C8.67909 7.5 7.4799 7.52831 6.29939 7.58258C4.89645 7.64707 3.74358 8.73752 3.55618 10.1294C3.43388 11.0378 3.33301 11.9686 3.33301 12.9167C3.33301 13.8647 3.43388 14.7956 3.55618 15.704Z" stroke="#141B34" stroke-width="1.5"/><path d="M6.25 7.50033V5.41699C6.25 3.34592 7.92893 1.66699 10 1.66699C12.0711 1.66699 13.75 3.34592 13.75 5.41699V7.50033" stroke="#141B34" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
												<input type="password" name="password_confirmation" value="<?php echo esc_attr( tutor_utils()->input_old( 'password_confirmation' ) ); ?>" placeholder="<?php esc_html_e( 'Password Confirmation', 'tutor' ); ?>" required autocomplete="new-password" style="margin-bottom: 0;">
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="tutor-form-row">
								<div class="tutor-form-col-12">
									<div class="tutor-form-group">
										<?php
											// Providing register_form hook.
											do_action( 'tutor_instructor_reg_form_middle' );
											do_action( 'register_form' );
										?>
									</div>
								</div>
							</div> 

							<?php do_action( 'tutor_instructor_reg_form_end' ); ?>

							<?php
								$tutor_toc_page_link = tutor_utils()->get_toc_page_link();
							?>

							<?php if ( null !== $tutor_toc_page_link ) : ?>
								<div class="tutor-mb-24">
									<?php esc_html_e( 'By signing up, I agree with the website\'s', 'tutor' ); ?> <a target="_blank" href="<?php echo esc_url( $tutor_toc_page_link ); ?>" title="<?php esc_attr_e( 'Terms and Conditions', 'tutor' ); ?>"><?php esc_html_e( 'Terms and Conditions', 'tutor' ); ?></a>
								</div>
							<?php endif; ?>

							<div>
								<button type="submit" name="tutor_register_instructor_btn" value="register" class="tutor-btn tutor-btn-primary tutor-btn-block"><?php esc_html_e( 'Register as instructor', 'tutor' ); ?></button>
							</div>
							<?php do_action( 'tutor_after_register_button' ); ?>

						</form>
						<div class="flex flex-col justify-center items-center">
							<span class="font-roboto text-[#646A69] font-medium my-4">or</span>
							<span class="font-roboto text-[12px] text-[#646A69]">sign up with</span>
							<?php do_action( 'tutor_after_registration_form_wrap' ); ?>
						</div>
					</div>

					<?php do_action( 'tutor_after_instructor_reg_form' ); ?>
				<?php endif; ?>

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
	#tutor-registration-wrap {border-radius: 0;padding: 0;box-shadow: none;max-width: unset;}
	.tutor-login-form-wrapper, #tutor-registration-form {padding: 0 !important;}
    .walla-auth__container{display:grid;grid-template-columns:1.5fr 2fr;gap:40px;align-items:stretch;min-height:80vh;padding:40px 0;box-sizing:border-box}
    .walla-auth__left{background:#F6F6F6;border-radius:20px;padding:60px 42px;display:flex;flex-direction:column;justify-content:center}
    .tutor-form-group {margin-bottom: 0;}
    .tutor-form-group input[type="text"], 
    .tutor-form-group input[type="password"] {height:52px;border-radius:28px;background:#fff;border:1px solid #e6e7eb;padding:0 18px;box-shadow:none;text-indent: 0;}
    .tutor-btn-primary {background:#000;color:#fff;width:100%;border-radius:200px;padding:20px;display:flex;justify-content:center;align-items:center;margin-top: 25px;}
    .tutor-btn-primary:hover{background:#1D1F1E;color:#fff}
    .tutor-form-group label {color:#1D1F1E;font-weight:500;font-size:14px;line-height:20px;text-decoration:none;font-family:"Roboto",sans-serif;padding-left:16px}
    .walla-auth .lgi{padding:20px;padding-left:40px;padding-right:40px;background-color:#fff}
    .walla-auth .shw-p{z-index:2;cursor:pointer}
    .walla-auth input{padding-left: 40px !important; padding-right: 40px !important;}
    .tutor-login-form-wrapper .tutor-password-strength-checker .show-hide-btn,
    #tutor-registration-form .tutor-password-strength-checker .show-hide-btn {top: 42%;}
    .tutor-border-top-light {border-top: none;}
    .shw-p.shw path {stroke: #000 !important;}
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
    #tutor-pro-social-authentication {flex-direction: row !important;gap: 24px !important;}
    #tutor-pro-twitter-login,
    #tutor-pro-social-authentication > div {width: 40px !important;overflow: hidden;height: 40px;border-radius: 50%;position: relative;background: #EBEBEB;}
    #tutor-pro-social-authentication > div:before {content: "";background: #EBEBEB;width: 100%;height: 100%;border-radius: 50%; position: absolute; left: 0; top: 0; z-index: 1;pointer-events: none;}
    #tutor-pro-social-authentication > div > div > div {background: transparent !important;}
    #tutor-pro-social-authentication > div:after {
    	content: "";
    	width: 20px;
    	height: 20px;
    	position: absolute;
    	left: 50%;
    	top: 50%;
    	transform: translate(-50%,-50%);
    	z-index: 2; pointer-events: none;
    	display: block;
    	background-size: contain !important;
    }
	#tutor-pro-social-authentication > div#tutor-pro-google-authentication:after {
		background: url("/wp-content/themes/walla-core/src/img/google.svg") no-repeat;
	}
	#tutor-pro-social-authentication > div#tutor-pro-facebook-authentication:after {
		background: url("/wp-content/themes/walla-core/src/img/facebook-app-symbol.svg") no-repeat;
	}
	#tutor-pro-social-authentication > div#tutor-pro-twitter-authentication:after {
		background: url("/wp-content/themes/walla-core/src/img/X_logo_2023.svg") no-repeat;
	}


    @media(max-width:1024px){.walla-auth__container{grid-template-columns:1fr;min-height:0}.walla-auth__right{display:none} }
    @media(max-width:767px){
    	.walla-auth__left {padding: 24px;}
        .tutor-login-form-wrapper, #tutor-registration-form {padding: 24px 16px;}
        .tutor-form-row {grid-template-columns: 1fr !important;gap: 0 !important;}
    }
</style>
