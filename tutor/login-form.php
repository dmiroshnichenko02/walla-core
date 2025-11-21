<?php
/**
 * Tutor login form template
 *
 * @package Tutor\Templates
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 2.0.1
 */

use TUTOR\Ajax;

$lost_pass = apply_filters( 'tutor_lostpassword_url', wp_lostpassword_url() );
/**
 * Get login validation errors & print
 *
 * @since 2.1.3
 */
$login_errors = get_transient( Ajax::LOGIN_ERRORS_TRANSIENT_KEY ) ? get_transient( Ajax::LOGIN_ERRORS_TRANSIENT_KEY ) : array();
foreach ( $login_errors as $login_error ) {
	?>
	<div class="tutor-alert tutor-warning tutor-mb-12" style="display:block; grid-gap: 0px 10px;">
		<?php
		echo wp_kses(
			$login_error,
			array(
				'strong' => true,
				'a'      => array(
					'href'  => true,
					'class' => true,
					'id'    => true,
				),
				'p'      => array(
					'class' => true,
					'id'    => true,
				),
				'div'    => array(
					'class' => true,
					'id'    => true,
				),
			)
		);
		?>
	</div>
	<?php
}

do_action( 'tutor_before_login_form' );
?>

<form id="tutor-login-form" method="post">
	<?php if ( is_single_course() ) : ?>
		<input type="hidden" name="tutor_course_enroll_attempt" value="<?php echo esc_attr( get_the_ID() ); ?>">
	<?php endif; ?>
	<?php tutor_nonce_field(); ?>
	<input type="hidden" name="tutor_action" value="tutor_user_login" />
	<input type="hidden" name="redirect_to" value="<?php echo esc_url( apply_filters( 'tutor_after_login_redirect_url', tutor()->current_url ) ); ?>" />

	<div class="tutor-mb-20">
        <label class="lbl" for="log">Email</label>
        <div class="relative">
        <svg class="absolute top-1/2 left-3 transform -translate-y-1/4" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M1.66602 5L7.42687 8.26414C9.55068 9.46751 10.448 9.46751 12.5718 8.26414L18.3327 5" stroke="#141B34" stroke-width="1.5" stroke-linejoin="round"/>
<path d="M1.67915 11.23C1.73363 13.7846 1.76087 15.0619 2.70348 16.0081C3.64609 16.9543 4.95796 16.9873 7.58171 17.0532C9.19878 17.0938 10.7999 17.0938 12.417 17.0532C15.0407 16.9873 16.3526 16.9543 17.2952 16.0081C18.2378 15.0619 18.2651 13.7846 18.3195 11.23C18.3371 10.4086 18.3371 9.59206 18.3195 8.77065C18.2651 6.21604 18.2378 4.93874 17.2952 3.99254C16.3526 3.04635 15.0407 3.01339 12.417 2.94747C10.7999 2.90684 9.19877 2.90683 7.5817 2.94746C4.95796 3.01338 3.64608 3.04634 2.70348 3.99253C1.76087 4.93872 1.73363 6.21603 1.67915 8.77064C1.66164 9.59205 1.66164 10.4086 1.67915 11.23Z" stroke="#141B34" stroke-width="1.5" stroke-linejoin="round"/>
</svg>

		<input type="text" class="tutor-form-control lgi" placeholder="<?php esc_html_e( 'Enter mail addresss', 'tutor' ); ?>" name="log" value="" size="20" required/>
        </div>
	</div>

	<div class="tutor-mb-32">
        <label class="lbl" for="pwd">Password</label>
        <div class="relative">
        <svg class="absolute top-1/2 left-3 transform -translate-y-1/4" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M12.0762 12.917H12.0837M7.91699 12.917H7.92446" stroke="#141B34" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M3.55618 15.704C3.74358 17.0958 4.89645 18.1863 6.29939 18.2508C7.4799 18.305 8.67909 18.3333 9.99967 18.3333C11.3203 18.3333 12.5194 18.305 13.7 18.2508C15.1029 18.1863 16.2558 17.0958 16.4432 15.704C16.5655 14.7956 16.6663 13.8647 16.6663 12.9167C16.6663 11.9686 16.5655 11.0378 16.4432 10.1294C16.2558 8.73752 15.1029 7.64707 13.7 7.58258C12.5194 7.52831 11.3203 7.5 9.99968 7.5C8.67909 7.5 7.4799 7.52831 6.29939 7.58258C4.89645 7.64707 3.74358 8.73752 3.55618 10.1294C3.43388 11.0378 3.33301 11.9686 3.33301 12.9167C3.33301 13.8647 3.43388 14.7956 3.55618 15.704Z" stroke="#141B34" stroke-width="1.5"/>
<path d="M6.25 7.50033V5.41699C6.25 3.34592 7.92893 1.66699 10 1.66699C12.0711 1.66699 13.75 3.34592 13.75 5.41699V7.50033" stroke="#141B34" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
</svg>

		<input type="password" class="tutor-form-control lgi lgi-p" placeholder="<?php esc_html_e( 'Password', 'tutor' ); ?>" name="pwd" value="" size="20" required/>
        <svg class="absolute top-1/2 right-3 transform -translate-y-1/4 pointer shw-p" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M1.75 10C1.75 10 4.75 4 10 4C15.25 4 18.25 10 18.25 10C18.25 10 15.25 16 10 16C4.75 16 1.75 10 1.75 10Z" stroke="#646A69" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M10 12.25C11.2426 12.25 12.25 11.2426 12.25 10C12.25 8.75736 11.2426 7.75 10 7.75C8.75736 7.75 7.75 8.75736 7.75 10C7.75 11.2426 8.75736 12.25 10 12.25Z" stroke="#646A69" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
</svg>

        </div>
	</div>

	<div class="tutor-login-error"></div>
	<?php
		do_action( 'tutor_login_form_middle' );
		do_action( 'login_form' );
		apply_filters( 'login_form_middle', '', '' );
	?>
    <style>
        .fgt a, .lbl{
            color: #1D1F1E !important;
            font-weight: 500 !important;
            font-size: 14px !important;
            line-height: 20px !important;
           text-decoration: underline !important;
           font-family: "Roboto", sans-serif !important;
        }
		.fgt a::after {
			display: none;
		}
        .lgn-btn {
            background: #000 !important;
            color: #fff !important;
            width: 100% !important;
            border-radius: 200px !important;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .lgn-btn:hover {
            background: #1D1F1E !important;
            color: #fff !important;
        }
        .lgi {
            padding: 20px !important;
            padding-left: 40px !important;
            padding-right: 40px !important;
            background-color: #fff !important;
            /*height: 100% !important;*/
            
        }
        .lbl {
            text-decoration: none !important;
            padding-left: 16px !important;
        }
        .crt {
            color: #1D1F1E !important;
            font-size: 16px !important;
            font-family: "Roboto" !important;
            line-height: 24px !important;
            font-weight: 400 !important;
        }
        .crt a {
            font-weight: 500 !important;
        }
        #tutor-pro-social-authentication {
            border: none !important;
            display: none !important;
        }
        .shw-p {
            z-index: 999;
            cursor: pointer !important;
        }
        .shw-p.shw path {
            stroke: #000 !important;
        }
    </style>
	<div class="tutor-d-flex justify-end tutor-align-center tutor-mb-20 fgt ">
		<!-- <div class="tutor-form-check">
			<input id="tutor-login-agmnt-1" type="checkbox" class="tutor-form-check-input tutor-bg-black-40" name="rememberme" value="forever" />
			<label for="tutor-login-agmnt-1" class="tutor-fs-7 tutor-color-muted">
				<?php esc_html_e( 'Keep me signed in', 'tutor' ); ?>
			</label>
		</div> -->
		<a href="<?php echo esc_url( $lost_pass ); ?>" class="tutor-btn tutor-btn-ghost">
			<?php esc_html_e( 'Forgot Password?', 'tutor' ); ?>
		</a>
	</div>

	<?php do_action( 'tutor_login_form_end' ); ?>
	<button type="submit" class="tutor-btn tutor-btn-primary tutor-btn-block lgn-btn">
		<?php esc_html_e( 'Login', 'tutor' ); ?>
	</button>
	
	<?php if ( get_option( 'users_can_register', false ) ) : ?>
		<?php
			$url_arg = array(
				'redirect_to' => tutor()->current_url,
			);
			if ( is_single_course() ) {
				$url_arg['enrol_course_id'] = get_the_ID();
			}
			?>
		<div class="tutor-text-center tutor-fs-6 tutor-color-secondary tutor-mt-20 fgt crt">
			<?php esc_html_e( 'New to Flow?', 'tutor' ); ?>&nbsp;
			<a href="<?php echo esc_url( add_query_arg( $url_arg, tutor_utils()->student_register_url() ) ); ?>" class="tutor-btn tutor-btn-link">
				<?php esc_html_e( 'Create an account', 'tutor' ); ?>
			</a>
		</div>
	<?php endif; ?>
	<?php do_action( 'tutor_after_sign_in_button' ); ?>
</form>
<?php
do_action( 'tutor_after_login_form' );
if ( ! tutor_utils()->is_tutor_frontend_dashboard() ) : ?>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		var { __ } = wp.i18n;
		var loginModal = document.querySelector('.tutor-modal.tutor-login-modal');
		var errors = <?php echo wp_json_encode( $login_errors ); ?>;
		if (loginModal && errors.length) {
			loginModal.classList.add('tutor-is-active');
		}
	});
</script>
<?php endif; ?>
<?php delete_transient( Ajax::LOGIN_ERRORS_TRANSIENT_KEY ); ?>