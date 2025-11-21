<?php
/**
 * Display single login
 *
 * @package Tutor\Templates
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! tutor_utils()->get_option( 'enable_tutor_native_login', null, true, true ) ) {
	// Redirect to wp native login page.
	header( 'Location: ' . wp_login_url( tutor_utils()->get_current_url() ) );
	exit;
}

tutor_utils()->tutor_custom_header();
$login_url = tutor_utils()->get_option( 'enable_tutor_native_login', null, true, true ) ? '' : wp_login_url( tutor()->current_url );
?>

<?php
//phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
do_action( 'tutor/template/login/before/wrap' );
?>
<style>
	.walla-auth__container{display:grid;grid-template-columns:1.5fr 2fr;gap:40px;align-items:stretch;min-height:80vh;padding:40px 0;box-sizing:border-box}
	.walla-auth__left{background:#F6F6F6;border-radius:20px;padding:125px 42px;display:flex;flex-direction:column;justify-content:center}
	/* .walla-auth__right{background:#ff2e2e;border-radius:16px} */
	.walla-auth .tutor-login-form-wrapper .tutor-fs-5{font-size:28px;font-weight:700;margin-bottom:24px; padding: 0 !important;}
	.walla-auth input.tutor-form-control{height:52px;border-radius:28px;background:#f7f7f8;border:1px solid #e6e7eb;padding:0 18px;box-shadow:none}
	.walla-auth .tutor-btn-primary{background:#ecff00;color:#000;border:none;height:52px;border-radius:28px;font-weight:600}
	.walla-auth .tutor-btn-primary:hover{filter:brightness(.98)}
	.walla-auth .tutor-btn-ghost,.walla-auth .tutor-btn-link{color:#6b7280}
    .walla-auth .tutor-login-form-wrapper {padding: 0 !important;}
	@media(max-width:1024px){.walla-auth__container{grid-template-columns:1fr;min-height:0}.walla-auth__right{display:none}.walla-auth__left{padding: 24px 16px;}}
</style>
<div class="px-4">
	<div class="container mx-auto"> 
		<div <?php tutor_post_class( 'tutor-page-wrap walla-auth' ); ?>>
			<div class="walla-auth__container">
				<div class="walla-auth__left">
					<div class="tutor-login-form-wrapper">
						<div class="tutor-fs-5 tutor-color-black tutor-mb-32">
							<?php esc_html_e( 'Login', 'tutor' ); ?>
						</div>
						<?php
							// load form template.
							$login_form = __DIR__ . '/login-form.php';
							tutor_load_template_from_custom_path(
								$login_form,
								false
							);
						?>
					</div>
					<?php do_action( 'tutor_after_login_form_wrapper' ); ?>
				</div>
				<div class="walla-auth__right" aria-hidden="true">
		            <div class="min-h-full min-w-full flex justify-center items-center">
		                <img src="<?php echo get_field('right_image', 'options') ?>" alt="login">
		            </div>
		        </div>
			</div>
		</div>
	</div>
</div>
<?php
	//phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
	do_action( 'tutor/template/login/after/wrap' );
	tutor_utils()->tutor_custom_footer();
?>
