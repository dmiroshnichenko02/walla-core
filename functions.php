<?php

/**
 * walla functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package walla
 */

if (! defined('_S_VERSION')) {
	define('_S_VERSION', '1.0.0');
}

function walla_setup()
{

	add_theme_support('title-tag');

	add_theme_support('post-thumbnails');


	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	add_theme_support('customize-selective-refresh-widgets');

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action('after_setup_theme', 'walla_setup');

function walla_widgets_init()
{
	register_sidebar(
		array(
			'name'          => esc_html__('Sidebar', 'walla'),
			'id'            => 'sidebar-1',
			'description'   => esc_html__('Add widgets here.', 'walla'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action('widgets_init', 'walla_widgets_init');

function walla_enqueue_scripts()
{

	$template_slug = get_page_template_slug(get_queried_object_id());

	wp_enqueue_style(
		'walla-main-style',
		get_template_directory_uri() . '/dist/css/index.css',
		array(),
		filemtime(get_template_directory() . '/dist/css/index.css')
	);

	wp_enqueue_style( 'slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css' );
    wp_enqueue_style( 'slick-theme-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css' );
    wp_enqueue_script( 'slick-js', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), null, true );

    // CSS
    wp_enqueue_style(
        'magnific-popup',
        'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css',
        array(),
        '1.1.0'
    );

    // JS
    wp_enqueue_script(
        'magnific-popup',
        'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js',
        array('jquery'),
        '1.1.0',
        true
    );
    
	// Script section
	if (is_front_page()) {
		wp_enqueue_script(
			'walla-front',
			get_template_directory_uri() . '/dist/js/main.js',
			array(),
			filemtime(get_template_directory() . '/dist/js/main.js'),
			true
		);
		wp_script_add_data('walla-front', 'type', 'module');
	}
    
    // Enqueue jQuery on checkout page
	if ( function_exists('is_checkout') && is_checkout() ) {
		wp_enqueue_script('jquery');
	}
}
add_action('wp_enqueue_scripts', 'walla_enqueue_scripts');

function add_module_type_attribute($tag, $handle, $src)
{
	$module_scripts = array(
		'walla-front',
	);

	if (in_array($handle, $module_scripts)) {
		$tag = '<script type="module" src="' . esc_url($src) . '"></script>';
	}

	return $tag;
}
add_filter('script_loader_tag', 'add_module_type_attribute', 10, 3);

add_filter( 'nav_menu_link_attributes', function( $atts, $item, $args ) {
    if ( isset($args->menu_class) && $args->menu_class === 'flex flex-col gap-3.5 font-inter text-white/60 text-[15px]' ) {
        $atts['class'] = ( $atts['class'] ?? '' ) . ' transition hover:text-white';
    }
    return $atts;
}, 10, 3 );

// Cart only: force classic shortcode so the theme override applies. Checkout remains unchanged.
add_filter( 'the_content', function( $content ) {
    if ( function_exists( 'is_cart' ) && is_cart() ) {
        return do_shortcode( '[woocommerce_cart]' );
    }
    return $content;
}, 1 );

add_action('wp_ajax_tutor_mark_lesson_complete', 'tutor_mark_lesson_complete_handler');
add_action('wp_ajax_nopriv_tutor_mark_lesson_complete', 'tutor_mark_lesson_complete_handler');

function tutor_mark_lesson_complete_handler() {
    if (!wp_verify_nonce($_POST['nonce'], 'tutor_mark_lesson_complete')) {
        wp_die('Security check failed');
    }

    $lesson_id = intval($_POST['lesson_id']);
    $course_id = intval($_POST['course_id']);
    $user_id = get_current_user_id();

    if (!$user_id) {
        wp_send_json_error('User not logged in');
        return;
    }

    if (!tutor_utils()->is_enrolled($course_id, $user_id)) {
        wp_send_json_error('User not enrolled in course');
        return;
    }

    if (tutor_utils()->is_completed_lesson($lesson_id)) {
        wp_send_json_success('Lesson already completed');
        return;
    }

    $result = tutor_utils()->mark_lesson_complete($lesson_id, $course_id);

    if ($result) {
        tutor_utils()->update_course_progress($course_id);
        
        wp_send_json_success('Lesson marked as completed');
    } else {
        wp_send_json_error('Failed to mark lesson as completed');
    }
}

function walla_set_single_course_as_front_page() {
    $course = get_posts([
        'post_type'      => 'courses', 
        'posts_per_page' => 1,
        'post_status'    => 'publish',
    ]);
    if (!empty($course)) {
        $course_id = $course[0]->ID;
        // Устанавливаем эту страницу как главную
        update_option('show_on_front', 'page');
        update_option('page_on_front', $course_id);
    }
}
add_action('init', 'walla_set_single_course_as_front_page');

// Redirect WooCommerce cart page to checkout
add_action('template_redirect', function() {
    if ( is_admin() || ( function_exists('wp_doing_ajax') && wp_doing_ajax() ) ) {
        return;
    }
    if ( function_exists('is_cart') && is_cart() ) {
        if ( function_exists('wc_get_checkout_url') ) {
            wp_safe_redirect( wc_get_checkout_url() );
            exit;
        }
    }
});

// Redirect WooCommerce checkout page to custom theme checkout template
add_action('template_redirect', function() {
    if ( is_admin() || ( function_exists('wp_doing_ajax') && wp_doing_ajax() ) ) {
        return;
    }
    if ( function_exists('is_checkout') && is_checkout() && !is_order_received_page() ) {
        $custom_checkout = get_theme_file_path('woocommerce/checkout/checkout.php');
        if ( file_exists( $custom_checkout ) ) {
            include $custom_checkout;
            exit;
        }
    }
});

/**
 * Redirect WooCommerce "order received" page to the custom Thank You template.
 */
add_filter('woocommerce_get_checkout_order_received_url', function ($url, $order) {
    if (! class_exists('WC_Order')) {
        return $url;
    }

    if (! $order instanceof WC_Order) {
        return $url;
    }

    $thank_you_page = get_page_by_path('thank-you');

    if (! $thank_you_page) {
        return $url;
    }

    $thank_you_url = get_permalink($thank_you_page);

    if (! $thank_you_url) {
        return $url;
    }

    return add_query_arg(
        array(
            'order_id'  => $order->get_id(),
            'order_key' => $order->get_order_key(),
        ),
        $thank_you_url
    );
}, 10, 2);

// Enqueue script + localize
function my_enqueue_tutor_toggle_script() {
    $handle = 'tutor-lesson-toggle';
    wp_enqueue_script(
        $handle,
        get_stylesheet_directory_uri() . '/js/lesson-toggle.js',
        array(),
        filemtime( get_stylesheet_directory() . '/js/lesson-toggle.js' ),
        true
    );

    wp_localize_script(
        $handle,
        'tutor_ajax_object',
        array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            // используем один и тот же nonce для всех toggle-ов
            'nonce'    => wp_create_nonce( 'tutor_toggle_nonce' ),
        )
    );
}
add_action( 'wp_enqueue_scripts', 'my_enqueue_tutor_toggle_script' );

// Toggle lesson - единый обработчик
function my_toggle_lesson_completion() {
    check_ajax_referer( 'tutor_toggle_nonce', 'nonce' );

    $user_id  = get_current_user_id();
    $lesson_id = intval( $_POST['lesson_id'] ?? 0 );
    $completed = intval( $_POST['completed'] ?? 0 );

    if ( ! $user_id || ! $lesson_id ) {
        wp_send_json_error( array( 'message' => 'Invalid data' ) );
    }

    $meta_key = '_tutor_completed_lesson_id_' . $lesson_id;
    if ( $completed ) {
        update_user_meta( $user_id, $meta_key, time() );
    } else {
        delete_user_meta( $user_id, $meta_key );
    }

    wp_send_json_success( array( 'completed' => $completed ) );
}
add_action( 'wp_ajax_tutor_toggle_lesson', 'my_toggle_lesson_completion' );

// Toggle quiz - единый обработчик
function my_toggle_quiz_completion() {
    check_ajax_referer( 'tutor_toggle_nonce', 'nonce' );

    $user_id = get_current_user_id();
    $quiz_id = intval( $_POST['quiz_id'] ?? 0 );
    $completed = intval( $_POST['completed'] ?? 0 );

    if ( ! $user_id || ! $quiz_id ) {
        wp_send_json_error( array( 'message' => 'Invalid data' ) );
    }

    $meta_key = '_tutor_completed_quiz_id_' . $quiz_id;
    if ( $completed ) {
        update_user_meta( $user_id, $meta_key, time() );
    } else {
        delete_user_meta( $user_id, $meta_key );
    }

    wp_send_json_success( array( 'completed' => $completed ) );
}
add_action( 'wp_ajax_tutor_toggle_quiz', 'my_toggle_quiz_completion' );

add_action('after_setup_theme', function(){
    $path = get_theme_file_path('tutor/ajax-handlers.php');
    if ( file_exists( $path ) ) {
        require_once $path;
    }
});

add_filter( 'woocommerce_billing_fields', 'remove_unwanted_billing_fields' );
function remove_unwanted_billing_fields( $fields ) {
    unset( $fields['billing_company'] );
    unset( $fields['billing_address_1'] );
    unset( $fields['billing_address_2'] );
    unset( $fields['billing_postcode'] );
    unset( $fields['billing_city'] );
    // unset( $fields['billing_phone'] );
    unset( $fields['billing_country'] );
    unset( $fields['billing_state'] );
    // unset( $fields['billing_first_name'] );
    // unset( $fields['billing_last_name'] );
    // unset( $fields['billing_email'] );
    return $fields;
}

add_action( 'woocommerce_checkout_process', 'walla_custom_checkout_process' );
function walla_custom_checkout_process() {
    if ( is_user_logged_in() ) {
        return;
    }
    $billing_phone = isset( $_POST['billing_phone'] ) ? sanitize_text_field( $_POST['billing_phone'] ) : '';
    $billing_email = isset( $_POST['billing_email'] ) ? sanitize_email( $_POST['billing_email'] ) : '';
    $billing_first_name = isset( $_POST['billing_first_name'] ) ? sanitize_text_field( $_POST['billing_first_name'] ) : '';
    $billing_last_name = isset( $_POST['billing_last_name'] ) ? sanitize_text_field( $_POST['billing_last_name'] ) : '';

    if ( empty( $billing_phone ) ) {
        wc_add_notice(
            __( 'Phone number is required.', 'woocommerce' ),
            'error'
        );
        return;
    }

    $existing_user = walla_find_user_by_phone( $billing_phone );
    
    if ( $existing_user ) {
        wc_add_notice(
            __( 'An account with this phone number already exists. Please log in to continue.', 'woocommerce' ),
            'error'
        );
        return;
    }

    if ( empty( $billing_email ) ) {
        wc_add_notice(
            __( 'Email address is required to create an account.', 'woocommerce' ),
            'error'
        );
        return;
    }

    if ( email_exists( $billing_email ) ) {
        wc_add_notice(
            __( 'An account with this email address already exists. Please log in to continue.', 'woocommerce' ),
            'error'
        );
        return;
    }

    $_POST['createaccount'] = 1;
}

function walla_find_user_by_phone( $phone ) {
    if ( empty( $phone ) ) {
        return false;
    }

    $users = get_users( array(
        'meta_key'   => 'billing_phone',
        'meta_value' => $phone,
        'number'     => 1,
        'fields'     => 'ID',
    ) );

    if ( ! empty( $users ) ) {
        return $users[0];
    }

    return false;
}

add_action( 'woocommerce_created_customer', 'walla_save_customer_phone', 10, 3 );
function walla_save_customer_phone( $customer_id, $new_customer_data, $password_generated ) {
    if ( isset( $_POST['billing_phone'] ) && ! empty( $_POST['billing_phone'] ) ) {
        $phone = sanitize_text_field( $_POST['billing_phone'] );
        update_user_meta( $customer_id, 'billing_phone', $phone );
    }
}

$walla_registered_customer_id = 0;

add_action( 'woocommerce_checkout_update_user_meta', 'walla_disable_auto_login_after_registration', 10, 2 );
function walla_disable_auto_login_after_registration( $customer_id, $data ) {
    global $walla_registered_customer_id;
    
    if ( $customer_id && ! empty( $data['createaccount'] ) ) {
        $walla_registered_customer_id = $customer_id;
        
        wp_clear_auth_cookie();
        wp_set_current_user( 0 );
        
        if ( function_exists( 'WC' ) && WC()->session ) {
            WC()->session->set_customer_session_cookie( false );
        }
        
        if ( WC()->session ) {
            WC()->session->__unset( 'reload_checkout' );
        }
    }
}

add_filter( 'woocommerce_checkout_customer_id', 'walla_set_order_customer_id', 10, 1 );
function walla_set_order_customer_id( $customer_id ) {
    global $walla_registered_customer_id;
    
    if ( $walla_registered_customer_id > 0 ) {
        return $walla_registered_customer_id;
    }
    
    return $customer_id;
}

/**
 * Replace "Add to cart" button text for course products
 */
add_filter( 'woocommerce_product_add_to_cart_text', function( $text, $product ) {
    if ( $product && get_post_meta( $product->get_id(), '_tutor_product', true ) === 'yes' ) {
        return __( 'Buy a Course', 'walla' );
    }
    return $text;
}, 10, 2 );

add_filter( 'woocommerce_product_single_add_to_cart_text', function( $text, $product ) {
    if ( $product && get_post_meta( $product->get_id(), '_tutor_product', true ) === 'yes' ) {
        return __( 'Buy a Course', 'walla' );
    }
    return $text;
}, 10, 2 );

// Redirect /shop to homepage
add_action('template_redirect', function() {
    if ( is_admin() || ( function_exists('wp_doing_ajax') && wp_doing_ajax() ) ) {
        return;
    }
    if ( function_exists('is_shop') && is_shop() ) {
        wp_safe_redirect( home_url( '/' ) );
        exit;
    }
});

// Redirect WooCommerce lost password to Tutor retrieve password page
add_action('template_redirect', function() {
    if ( is_admin() || ( function_exists('wp_doing_ajax') && wp_doing_ajax() ) ) {
        return;
    }
    
    // Check if we're on WooCommerce lost password page (but not for newaccount action)
    if ( function_exists('is_account_page') && is_account_page() ) {
        global $wp;
        
        // Check if it's lost-password endpoint
        if ( isset( $wp->query_vars['lost-password'] ) ) {
            // Don't redirect if it's newaccount action (password set for new account)
            if ( ! isset( $_GET['action'] ) || $_GET['action'] !== 'newaccount' ) {
                // Also don't redirect if show-reset-form is set (password reset form)
                if ( ! isset( $_GET['show-reset-form'] ) ) {
                    // Redirect to Tutor retrieve password page
                    wp_safe_redirect( home_url( '/dashboard/retrieve-password/' ) );
                    exit;
                }
            }
        }
    }
}, 5 );
