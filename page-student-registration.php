<?php
/**
 * Template for student registration page
 *
 * @package walla
 */

get_header(); ?>

<style>
    .walla-auth__container{display:grid;grid-template-columns:1.5fr 2fr;gap:40px;align-items:stretch;min-height:80vh;padding:40px 0;box-sizing:border-box}
    .walla-auth__left{background:#F6F6F6;border-radius:20px;padding:60px 42px;display:flex;flex-direction:column;justify-content:center}
    .walla-auth input.tutor-form-control{height:52px;border-radius:28px;background:#fff;border:1px solid #e6e7eb;padding:0 18px;box-shadow:none}
    .walla-auth .lgn-btn{background:#000;color:#fff;width:100%;border-radius:200px;padding:20px;display:flex;justify-content:center;align-items:center}
    .walla-auth .lgn-btn:hover{background:#1D1F1E;color:#fff}
    .walla-auth .lbl{color:#1D1F1E;font-weight:500;font-size:14px;line-height:20px;text-decoration:none;font-family:"Roboto",sans-serif;padding-left:16px}
    .walla-auth .lgi{padding:20px;padding-left:40px;padding-right:40px;background-color:#fff}
    .walla-auth .shw-p{z-index:2;cursor:pointer}
    .walla-auth input{padding-left: 40px !important; padding-right: 40px !important;}
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
        .tutor-login-form-wrapper, #tutor-registration-form {padding: 24px 16px;}
        .tutor-login-form-wrapper .tutor-grid {grid-template-columns: 1fr !important;}
    }
</style>

<div class="px-4">
    <div class="container mx-auto">
    <div class="tutor-page-wrap walla-auth">
        <div class="walla-auth__container">
            <div class="walla-auth__left">
                <div class="tutor-login-form-wrapper">
                    <div class="tutor-fs-5 tutor-color-black tutor-mb-32" style="font-size:32px;font-weight:500;margin-bottom:24px;line-height:52px;font-family:'Figtree';">
                        <?php _e('Create Account', 'walla'); ?>
                    </div>
                    <form id="student-registration-form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
                        <?php wp_nonce_field('student_registration_nonce', 'student_registration_nonce'); ?>
                        <input type="hidden" name="action" value="student_registration">

                        <div class="tutor-grid tutor-grid-cols-2 tutor-gap-16 tutor-mb-20" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div>
                                <label class="lbl" for="first_name"><?php _e('First Name', 'walla'); ?></label>
                                <div class="relative">
                                <svg class="absolute top-1/2 left-3 transform -translate-y-1/4" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                  <path d="M16 16.75V15.25C16 14.4544 15.6839 13.6913 15.1213 13.1287C14.5587 12.5661 13.7956 12.25 13 12.25H7C6.20435 12.25 5.44129 12.5661 4.87868 13.1287C4.31607 13.6913 4 14.4544 4 15.25V16.75" stroke="#1D1F1E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                  <path d="M10 9.25C11.6569 9.25 13 7.90685 13 6.25C13 4.59315 11.6569 3.25 10 3.25C8.34315 3.25 7 4.59315 7 6.25C7 7.90685 8.34315 9.25 10 9.25Z" stroke="#1D1F1E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                              </svg>
                                    <input type="text" id="first_name" name="first_name" required class="tutor-form-control lgi" placeholder="<?php esc_attr_e('Enter name here', 'walla'); ?>" />
                                </div>
                            </div>
                            <div>
                                <label class="lbl" for="last_name"><?php _e('Last Name', 'walla'); ?></label>
                                <div class="relative">
                                <svg class="absolute top-1/2 left-3 transform -translate-y-1/4" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M16 16.75V15.25C16 14.4544 15.6839 13.6913 15.1213 13.1287C14.5587 12.5661 13.7956 12.25 13 12.25H7C6.20435 12.25 5.44129 12.5661 4.87868 13.1287C4.31607 13.6913 4 14.4544 4 15.25V16.75" stroke="#1D1F1E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M10 9.25C11.6569 9.25 13 7.90685 13 6.25C13 4.59315 11.6569 3.25 10 3.25C8.34315 3.25 7 4.59315 7 6.25C7 7.90685 8.34315 9.25 10 9.25Z" stroke="#1D1F1E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
                                    <input type="text" id="last_name" name="last_name" required class="tutor-form-control lgi" placeholder="<?php esc_attr_e('Enter name here', 'walla'); ?>" />
                                </div>
                            </div>
                        </div>

                        <div class="tutor-mb-20">
                            <label class="lbl" for="email"><?php _e('Email', 'walla'); ?></label>
                            <div class="relative">
                                <svg class="absolute top-1/2 left-3 transform -translate-y-1/4" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.66602 5L7.42687 8.26414C9.55068 9.46751 10.448 9.46751 12.5718 8.26414L18.3327 5" stroke="#141B34" stroke-width="1.5" stroke-linejoin="round"/><path d="M1.67915 11.23C1.73363 13.7846 1.76087 15.0619 2.70348 16.0081C3.64609 16.9543 4.95796 16.9873 7.58171 17.0532C9.19878 17.0938 10.7999 17.0938 12.417 17.0532C15.0407 16.9873 16.3526 16.9543 17.2952 16.0081C18.2378 15.0619 18.2651 13.7846 18.3195 11.23C18.3371 10.4086 18.3371 9.59206 18.3195 8.77065C18.2651 6.21604 18.2378 4.93874 17.2952 3.99254C16.3526 3.04635 15.0407 3.01339 12.417 2.94747C10.7999 2.90684 9.19877 2.90683 7.5817 2.94746C4.95796 3.01338 3.64608 3.04634 2.70348 3.99253C1.76087 4.93872 1.73363 6.21603 1.67915 8.77064C1.66164 9.59205 1.66164 10.4086 1.67915 11.23Z" stroke="#141B34" stroke-width="1.5" stroke-linejoin="round"/></svg>
                                <input type="email" id="email" name="email" required class="tutor-form-control lgi" placeholder="<?php esc_attr_e('Enter mail addresss', 'walla'); ?>" />
                            </div>
                        </div>

                        <div class="tutor-mb-20">
                            <label class="lbl" for="password"><?php _e('Password', 'walla'); ?></label>
                            <div class="relative">
                                <svg class="absolute top-1/2 left-3 transform -translate-y-1/4" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.0762 12.917H12.0837M7.91699 12.917H7.92446" stroke="#141B34" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M3.55618 15.704C3.74358 17.0958 4.89645 18.1863 6.29939 18.2508C7.4799 18.305 8.67909 18.3333 9.99967 18.3333C11.3203 18.3333 12.5194 18.305 13.7 18.2508C15.1029 18.1863 16.2558 17.0958 16.4432 15.704C16.5655 14.7956 16.6663 13.8647 16.6663 12.9167C16.6663 11.9686 16.5655 11.0378 16.4432 10.1294C16.2558 8.73752 15.1029 7.64707 13.7 7.58258C12.5194 7.52831 11.3203 7.5 9.99968 7.5C8.67909 7.5 7.4799 7.52831 6.29939 7.58258C4.89645 7.64707 3.74358 8.73752 3.55618 10.1294C3.43388 11.0378 3.33301 11.9686 3.33301 12.9167C3.33301 13.8647 3.43388 14.7956 3.55618 15.704Z" stroke="#141B34" stroke-width="1.5"/><path d="M6.25 7.50033V5.41699C6.25 3.34592 7.92893 1.66699 10 1.66699C12.0711 1.66699 13.75 3.34592 13.75 5.41699V7.50033" stroke="#141B34" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <input type="password" id="password" name="password" required class="tutor-form-control ps lgi lgi-p" placeholder="<?php esc_attr_e('Password', 'walla'); ?>" />
                                <svg class="absolute top-1/2 right-3 transform -translate-y-1/4 pointer shw-p" data-target="#password" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.75 10C1.75 10 4.75 4 10 4C15.25 4 18.25 10 18.25 10C18.25 10 15.25 16 10 16C4.75 16 1.75 10 1.75 10Z" stroke="#646A69" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 12.25C11.2426 12.25 12.25 11.2426 12.25 10C12.25 8.75736 11.2426 7.75 10 7.75C8.75736 7.75 7.75 8.75736 7.75 10C7.75 11.2426 8.75736 12.25 10 12.25Z" stroke="#646A69" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                        </div>

                        <div class="tutor-mb-20">
                            <label class="lbl" for="confirm_password"><?php _e('Confirm Password', 'walla'); ?></label>
                            <div class="relative">
                                <svg class="absolute top-1/2 left-3 transform -translate-y-1/4" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.0762 12.917H12.0837M7.91699 12.917H7.92446" stroke="#141B34" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M3.55618 15.704C3.74358 17.0958 4.89645 18.1863 6.29939 18.2508C7.4799 18.305 8.67909 18.3333 9.99967 18.3333C11.3203 18.3333 12.5194 18.305 13.7 18.2508C15.1029 18.1863 16.2558 17.0958 16.4432 15.704C16.5655 14.7956 16.6663 13.8647 16.6663 12.9167C16.6663 11.9686 16.5655 11.0378 16.4432 10.1294C16.2558 8.73752 15.1029 7.64707 13.7 7.58258C12.5194 7.52831 11.3203 7.5 9.99968 7.5C8.67909 7.5 7.4799 7.52831 6.29939 7.58258C4.89645 7.64707 3.74358 8.73752 3.55618 10.1294C3.43388 11.0378 3.33301 11.9686 3.33301 12.9167C3.33301 13.8647 3.43388 14.7956 3.55618 15.704Z" stroke="#141B34" stroke-width="1.5"/><path d="M6.25 7.50033V5.41699C6.25 3.34592 7.92893 1.66699 10 1.66699C12.0711 1.66699 13.75 3.34592 13.75 5.41699V7.50033" stroke="#141B34" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <input type="password" id="confirm_password" name="confirm_password" required class="tutor-form-control ps lgi lgi-p" placeholder="<?php esc_attr_e('Password', 'walla'); ?>" />
                                <svg class="absolute top-1/2 right-3 transform -translate-y-1/4 pointer shw-p" data-target="#confirm_password" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.75 10C1.75 10 4.75 4 10 4C15.25 4 18.25 10 18.25 10C18.25 10 15.25 16 10 16C4.75 16 1.75 10 1.75 10Z" stroke="#646A69" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 12.25C11.2426 12.25 12.25 11.2426 12.25 10C12.25 8.75736 11.2426 7.75 10 7.75C8.75736 7.75 7.75 8.75736 7.75 10C7.75 11.2426 8.75736 12.25 10 12.25Z" stroke="#646A69" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                        </div>

                        <div class="tutor-mb-12" id="form-messages" style="display:none;"></div>

                        <button type="submit" id="submit-btn" class="tutor-btn tutor-btn-primary tutor-btn-block lgn-btn">
                            <span class="submit-text"><?php _e('Sign Up', 'walla'); ?></span>
                            <span class="loading-text" style="display:none;"><?php _e('Creating Account...', 'walla'); ?></span>
                        </button>

                        <div class="tutor-text-center tutor-fs-6 tutor-color-secondary tutor-mt-20 ttrb" style="text-align:center;margin-top:20px;">
                            <span class="crt"><?php _e('Already have an account?', 'walla'); ?> <a href="<?php echo esc_url(get_permalink(22)); ?>"><?php _e('Log In', 'walla'); ?></a></span>
                        </div>
                    </form>
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


<script>
jQuery(document).ready(function($){

    $('#student-registration-form').on('submit', function(e){
        e.preventDefault();
        var $form = $(this);
        var $submitBtn = $('#submit-btn');
        var $messages = $('#form-messages');

        var password = $('#password').val();
        var confirmPassword = $('#confirm_password').val();
        if(password !== confirmPassword){
            showMessage('error', '<?php _e('Passwords do not match', 'walla'); ?>');
            return;
        }

        $submitBtn.prop('disabled', true);
        $('.submit-text').hide();
        $('.loading-text').show();

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            success: function(response){
                if(response.success){
                    showMessage('success', response.data.message);
                    $form[0].reset();
                    setTimeout(function(){ window.location.href = '<?php echo esc_url(get_permalink(22)); ?>'; }, 2000);
                }else{
                    showMessage('error', (response.data && response.data.message) ? response.data.message : '<?php _e('Registration failed. Please try again.', 'walla'); ?>');
                }
            },
            error: function(){
                showMessage('error', '<?php _e('An error occurred. Please try again.', 'walla'); ?>');
            },
            complete: function(){
                $submitBtn.prop('disabled', false);
                $('.submit-text').show();
                $('.loading-text').hide();
            }
        });
    });

    function showMessage(type, message){
        var $messages = $('#form-messages');
        $messages.show().attr('class','tutor-mb-12').html('<div class="tutor-alert '+(type==='success'?'tutor-success':'tutor-warning')+'" style="display:block"><p>'+message+'</p></div>');
        setTimeout(function(){ $messages.fadeOut(); }, 5000);
    }
});
</script>

<?php get_footer(); ?>
