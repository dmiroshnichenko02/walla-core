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
    $course_id = get_field('walla_front_page_course', 'options');
    update_option('show_on_front', 'page');
    update_option('page_on_front', $course_id[0]);
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
    
    $course_callback_path = get_theme_file_path('course-api/courseUpdateCallback.php');
    if ( file_exists( $course_callback_path ) ) {
        require_once $course_callback_path;
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


add_filter('post_link', function($permalink, $post, $leavename) {
    if ( ! function_exists('tutor_utils') ) {
        return $permalink;
    }
    
    $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
    if ( strpos( $request_uri, '/dashboard' ) === false ) {
        return $permalink;
    }
    
    if ( ! isset( $post->post_type ) || $post->post_type !== 'courses' ) {
        return $permalink;
    }
    
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        return $permalink;
    }
    
    if ( ! tutor_utils()->is_enrolled( $post->ID, $user_id ) ) {
        return $permalink;
    }
    
    $first_lesson_url = tutor_utils()->get_course_first_lesson( $post->ID, tutor()->lesson_post_type );
    if ( ! $first_lesson_url ) {
        $first_lesson_url = tutor_utils()->get_course_first_lesson( $post->ID );
    }
    
    if ( $first_lesson_url ) {
        return $first_lesson_url;
    }
    
    return $permalink;
}, 10, 3);

add_filter('the_permalink', function($permalink) {
    if ( ! function_exists('tutor_utils') ) {
        return $permalink;
    }
    
    $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
    if ( strpos( $request_uri, '/dashboard' ) === false ) {
        return $permalink;
    }

    global $post;
    if ( ! $post || ! isset( $post->post_type ) || $post->post_type !== 'courses' ) {
        return $permalink;
    }
    
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        return $permalink;
    }
    
    if ( ! tutor_utils()->is_enrolled( $post->ID, $user_id ) ) {
        return $permalink;
    }

    $first_lesson_url = tutor_utils()->get_course_first_lesson( $post->ID, tutor()->lesson_post_type );
    if ( ! $first_lesson_url ) {
        $first_lesson_url = tutor_utils()->get_course_first_lesson( $post->ID );
    }
    
    if ( $first_lesson_url ) {
        return $first_lesson_url;
    }
    
    return $permalink;
}, 10, 1);

add_action('wp_ajax_check_quiz_entry', 'walla_check_quiz_entry');
add_action('wp_ajax_nopriv_check_quiz_entry', 'walla_check_quiz_entry');

function walla_check_quiz_entry() {
	if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'check_quiz_entry_nonce')) {
		wp_send_json_error(array('message' => 'Invalid nonce'));
		return;
	}

	$form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
	$user_email = isset($_POST['user_email']) ? sanitize_email($_POST['user_email']) : '';

	if (empty($form_id) || empty($user_email)) {
		wp_send_json_error(array('message' => 'Missing required parameters'));
		return;
	}

	if (!class_exists('GFAPI')) {
		wp_send_json_error(array('message' => 'Gravity Forms not available'));
		return;
	}

	$form = GFAPI::get_form($form_id);
	
	if (is_wp_error($form)) {
		wp_send_json_error(array('message' => 'Form not found', 'form_id' => $form_id));
		return;
	}

	$entries = array();
	
	foreach ($form['fields'] as $field) {
		if ($field->type === 'email') {
			$search_criteria = array(
				'field_filters' => array(
					array(
						'key' => $field->id,
						'value' => $user_email,
						'operator' => 'is'
					)
				)
			);
			$entries = GFAPI::get_entries($form_id, $search_criteria, array('key' => 'id', 'direction' => 'DESC'), array('offset' => 0, 'page_size' => 1));
			if (!is_wp_error($entries) && !empty($entries)) {
				break;
			}
		}
	}
	
	if (empty($entries) || is_wp_error($entries)) {
		$search_criteria = array(
			'field_filters' => array(
				array(
					'value' => $user_email
				)
			)
		);
		$entries = GFAPI::get_entries($form_id, $search_criteria, array('key' => 'id', 'direction' => 'DESC'), array('offset' => 0, 'page_size' => 1));
	}
	
	if ((empty($entries) || is_wp_error($entries)) && is_user_logged_in()) {
		$current_user_id = get_current_user_id();
		$search_criteria = array(
			'field_filters' => array(
				array(
					'key' => 'created_by',
					'value' => $current_user_id,
					'operator' => 'is'
				)
			)
		);
		$entries = GFAPI::get_entries($form_id, $search_criteria, array('key' => 'id', 'direction' => 'DESC'), array('offset' => 0, 'page_size' => 1));
	}

	if (is_wp_error($entries)) {
		wp_send_json_error(array('message' => 'Error searching entries', 'error' => $entries->get_error_message()));
		return;
	}

	if (!empty($entries) && is_array($entries) && count($entries) > 0) {
		$entry = $entries[0];
		$entry_has_email = false;
		
		foreach ($entry as $key => $value) {
			if (is_string($value) && strtolower(trim($value)) === strtolower(trim($user_email))) {
				$entry_has_email = true;
				break;
			}
		}
		
		if (!$entry_has_email && is_user_logged_in()) {
			$current_user_id = get_current_user_id();
			if (isset($entry['created_by']) && intval($entry['created_by']) === $current_user_id) {
				$entry_has_email = true;
			}
		}
		
		if ($entry_has_email || is_user_logged_in()) {
			wp_send_json_success(array(
				'has_entry' => true,
				'entry_id' => $entry['id'],
				'debug' => array(
					'entry_email_match' => $entry_has_email,
					'user_logged_in' => is_user_logged_in()
				)
			));
		} else {
			wp_send_json_success(array(
				'has_entry' => false,
				'debug' => 'Entry found but email/user mismatch'
			));
		}
	} else {
		wp_send_json_success(array(
			'has_entry' => false,
			'debug' => 'No entries found'
		));
	}
}

add_action('wp_ajax_get_quiz_results', 'walla_get_quiz_results');
add_action('wp_ajax_nopriv_get_quiz_results', 'walla_get_quiz_results');

function walla_get_quiz_results() {
	if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'get_quiz_results_nonce')) {
		wp_send_json_error(array('message' => 'Invalid nonce'));
		return;
	}

	$form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
	$entry_id = isset($_POST['entry_id']) ? intval($_POST['entry_id']) : 0;

	if (empty($form_id) || empty($entry_id)) {
		wp_send_json_error(array('message' => 'Missing required parameters'));
		return;
	}

	if (!class_exists('GFAPI')) {
		wp_send_json_error(array('message' => 'Gravity Forms not available'));
		return;
	}

	$form = GFAPI::get_form($form_id);
	$entry = GFAPI::get_entry($entry_id);

	if (is_wp_error($form)) {
		wp_send_json_error(array('message' => 'Error loading form: ' . $form->get_error_message()));
		return;
	}

	if (is_wp_error($entry)) {
		wp_send_json_error(array('message' => 'Error loading entry: ' . $entry->get_error_message()));
		return;
	}

	if (empty($form) || empty($entry)) {
		wp_send_json_error(array('message' => 'Form or entry not found'));
		return;
	}

	$confirmation = '<div class="gform_confirmation_wrapper"><div class="gform_confirmation_message">';
	
	if (class_exists('GFQuiz')) {
		$quiz_addon = GFQuiz::get_instance();
		if (method_exists($quiz_addon, 'get_quiz_results')) {
			$results = $quiz_addon->get_quiz_results($form, $entry);
			if (!empty($results) && isset($results['summary'])) {
				$confirmation .= '<div id="gquiz_confirmation_message">';
				$confirmation .= $results['summary'];
				$confirmation .= '</div>';
			}
		}
	}
	
	if (strpos($confirmation, 'gquiz_confirmation_message') === false) {
		if (class_exists('GFFormDisplay') && method_exists('GFFormDisplay', 'update_confirmation')) {
			$form = GFFormDisplay::update_confirmation($form, $entry);
		}
		
		if (class_exists('GFFormDisplay') && method_exists('GFFormDisplay', 'get_confirmation_message')) {
			$confirmation_type = rgar($form['confirmation'], 'type', 'message');
			
			if ($confirmation_type == 'message') {
				$confirmation_msg = GFFormDisplay::get_confirmation_message($form['confirmation'], $form, $entry);
				if (!empty($confirmation_msg)) {
					$confirmation .= $confirmation_msg;
				}
			}
		}
	}
	
	if (strpos($confirmation, 'gquiz-field') === false) {
		$confirmation .= '<h3>Your Quiz Results</h3>';
		
		foreach ($form['fields'] as $field) {
			if ($field->type === 'quiz') {
				$field_value = rgar($entry, $field->id);
				if (!empty($field_value)) {
					$confirmation .= '<div class="gquiz-field">';
					$confirmation .= '<div class="gquiz-field-label">' . esc_html(GFCommon::get_label($field)) . '</div>';
					$confirmation .= '<div class="gquiz-field-choice">' . esc_html($field_value) . '</div>';
					$confirmation .= '</div>';
				}
			}
		}
	}
	
	$quiz_score = gform_get_meta($entry_id, 'gquiz_score');
	$quiz_percent = gform_get_meta($entry_id, 'gquiz_percent');
	$quiz_grade = gform_get_meta($entry_id, 'gquiz_grade');
	$quiz_is_pass = gform_get_meta($entry_id, 'gquiz_is_pass');
	
	$max_score = false;
	if (class_exists('GFQuiz')) {
		$quiz_addon = GFQuiz::get_instance();
		if (method_exists($quiz_addon, 'get_max_score')) {
			$max_score = $quiz_addon->get_max_score($form);
		}
	}
	
	$results_html = '<div class="gquiz-results-summary" style="margin-top: 20px; padding: 20px; background: #f8f9ff; border-radius: 12px;">';
	
	if ($quiz_score !== false) {
		$score_display = $max_score !== false ? $quiz_score . '/' . $max_score : $quiz_score;
		$results_html .= '<div class="gquiz-result-item" style="margin-bottom: 12px; font-size: 18px; line-height: 24px;">';
		$results_html .= '<strong>Total Score:</strong> ' . esc_html($score_display);
		$results_html .= '</div>';
	}
	
	if ($quiz_percent !== false) {
		$results_html .= '<div class="gquiz-result-item" style="margin-bottom: 12px; font-size: 18px; line-height: 24px;">';
		$results_html .= '<strong>Pass Percent:</strong> ' . esc_html($quiz_percent) . '%';
		$results_html .= '</div>';
	}
	
	if ($quiz_grade !== false && !empty($quiz_grade)) {
		$results_html .= '<div class="gquiz-result-item" style="margin-bottom: 12px; font-size: 18px; line-height: 24px;">';
		$results_html .= '<strong>Your Grade:</strong> ' . esc_html($quiz_grade);
		$results_html .= '</div>';
	}
	
	if ($quiz_is_pass !== false) {
		$pass_text = ($quiz_is_pass == '1' || $quiz_is_pass === 1 || $quiz_is_pass === true) ? 'Pass' : 'Fail';
		$results_html .= '<div class="gquiz-result-item" style="margin-bottom: 12px; font-size: 18px; line-height: 24px;">';
		$results_html .= '<strong>Result:</strong> ' . esc_html($pass_text);
		$results_html .= '</div>';
	}
	
	$results_html .= '</div>';
	
	$confirmation .= $results_html;
	
	$confirmation .= '</div></div>';
	
	wp_send_json_success(array(
		'html' => $confirmation
	));
}

add_action('wp_ajax_walla_ask_course_question', 'walla_ask_course_question');
add_action('wp_ajax_nopriv_walla_ask_course_question', 'walla_ask_course_question');

function walla_ask_course_question() {
	if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'walla_ask_question_nonce')) {
		wp_send_json_error(array('message' => 'Invalid nonce'));
		return;
	}

	$course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
	$lesson_id = isset($_POST['lesson_id']) ? intval($_POST['lesson_id']) : 0;
	$question = isset($_POST['question']) ? sanitize_text_field($_POST['question']) : '';

	if (empty($course_id) || empty($question)) {
		wp_send_json_error(array('message' => 'Missing required parameters'));
		return;
	}

	$request_body = array(
		'course_id' => (string) $course_id,
		'lesson_id' => (string) $lesson_id,
		'question' => $question
	);

	$api_url = get_field('ai_api_endpoint', 'option');
	if (empty($api_url)) {
		$api_url = 'https://ai.walla.academy/chat/course';
	}

	$response = wp_remote_post($api_url, array(
		'timeout' => 30,
		'headers' => array(
			'Content-Type' => 'application/json',
		),
		'body' => json_encode($request_body),
	));

	if (is_wp_error($response)) {
		wp_send_json_error(array(
			'message' => 'API request failed: ' . $response->get_error_message()
		));
		return;
	}

	$response_code = wp_remote_retrieve_response_code($response);
	$response_body = wp_remote_retrieve_body($response);
	$response_data = json_decode($response_body, true);

	if ($response_code !== 200) {
		$error_message = 'API request failed';
		
		if (isset($response_data['detail'])) {
			$error_message = $response_data['detail'];
		} elseif (isset($response_data['message'])) {
			$error_message = $response_data['message'];
		} elseif (isset($response_data['error'])) {
			$error_message = is_array($response_data['error']) && isset($response_data['error']['message']) 
				? $response_data['error']['message'] 
				: (is_string($response_data['error']) ? $response_data['error'] : $error_message);
		}
		
		wp_send_json_error(array(
			'message' => $error_message,
			'code' => $response_code,
			'response' => $response_data
		));
		return;
	}

	if (!isset($response_data['answer'])) {
		wp_send_json_error(array(
			'message' => 'Invalid API response format',
			'response' => $response_data
		));
		return;
	}

	wp_send_json_success(array(
		'answer' => $response_data['answer'],
		'source' => isset($response_data['source']) ? $response_data['source'] : 'course',
		'course_id' => isset($response_data['course_id']) ? $response_data['course_id'] : $course_id,
		'lesson_id' => isset($response_data['lesson_id']) ? $response_data['lesson_id'] : $lesson_id
	));
}

// GF

add_filter('gform_form_settings_fields', function ($fields, $form) {
    $tags = is_array($form) ? ($form['tags'] ?? '') : ($form->tags ?? '');
    if (is_array($tags)) {
        $tags = implode(', ', $tags);
    }
    $tags = is_string($tags) ? $tags : '';
    
    $fields['form_tags'] = [
        'title'  => 'Form Tags',
        'fields' => [
            [
                'name'    => 'tags',
                'label'   => 'Tags',
                'type'    => 'text',
                'tooltip' => 'Enter tags separated by commas. These tags can be used to search and filter forms.',
            ],
        ],
    ];
    return $fields;
}, 10, 2);

add_filter('gform_form_settings_initial_values', function ($initial_values, $form) {
    $tags = is_array($form) ? ($form['tags'] ?? '') : ($form->tags ?? '');
    if (is_array($tags)) {
        $tags = implode(', ', $tags);
    }
    $tags = is_string($tags) ? $tags : '';
    $initial_values['tags'] = $tags;
    return $initial_values;
}, 10, 2);

add_filter('gform_pre_form_settings_save', function ($form) {
    if (isset($_POST['_gform_setting_tags'])) {
        $tags = sanitize_text_field($_POST['_gform_setting_tags']);
        $tags = trim($tags);
        $form['tags'] = $tags;
    } elseif (isset($form['tags']) && is_array($form['tags'])) {
        $form['tags'] = implode(', ', array_filter($form['tags']));
    }
    return $form;
});

add_filter('gform_form_list_columns', function ($columns) {
    $columns['tags'] = 'Tags';
    return $columns;
});

add_action('gform_form_list_column_tags', function ($form) {
    $form_id = is_object($form) ? $form->id : (is_array($form) ? $form['id'] : 0);
    
    if (!$form_id) {
        echo '<span style="color: #999;">—</span>';
        return;
    }
    
    $full_form = GFAPI::get_form($form_id);
    if (!$full_form || is_wp_error($full_form)) {
        echo '<span style="color: #999;">—</span>';
        return;
    }
    
    $tags = $full_form['tags'] ?? '';
    
    if (is_array($tags)) {
        $tags = implode(', ', array_filter($tags));
    }
    
    if (!empty($tags)) {
        $tags = trim($tags);
        $tags_array = array_map('trim', explode(',', $tags));
        $tags_array = array_filter($tags_array);
        echo esc_html(implode(', ', $tags_array));
    } else {
        echo '<span style="color: #999;">—</span>';
    }
});

add_filter('gform_form_list_forms', function ($forms, $search_query, $active, $sort_column, $sort_direction, $trash) {
    if (empty($search_query)) {
        return $forms;
    }

    $existing_ids = [];
    foreach ($forms as $form) {
        $existing_ids[] = is_object($form) ? $form->id : (is_array($form) ? $form['id'] : 0);
    }

    $all_forms_db = GFFormsModel::get_forms($active, $sort_column, $sort_direction, $trash);
    $matched_by_tags = [];

    foreach ($all_forms_db as $form_obj) {
        if (in_array($form_obj->id, $existing_ids)) {
            continue;
        }

        $full_form = GFAPI::get_form($form_obj->id);
        if (!$full_form || is_wp_error($full_form)) {
            continue;
        }

        $tags = $full_form['tags'] ?? '';
        
        if (is_array($tags)) {
            $tags = implode(', ', $tags);
        }
        
        if (!empty($tags)) {
            $tags = trim($tags);
            $tags_array = array_map('trim', explode(',', $tags));
            
            foreach ($tags_array as $tag) {
                if (stripos($tag, $search_query) !== false) {
                    $matched_by_tags[] = $form_obj;
                    break;
                }
            }
        }
    }

    if (!empty($matched_by_tags)) {
        $forms = array_merge($forms, $matched_by_tags);
    }

    return $forms;
}, 10, 6);

add_filter('acf-gravityforms-add-on/field_html', function ($field_html, $field, $field_options, $multiple) {
    if (empty($field_html)) {
        return $field_html;
    }
    
    $fieldId = str_replace(['[', ']'], ['-', ''], $field['name']);
    
    $forms = GFAPI::get_forms(true, false, 'title');
    $forms_data = [];
    
    foreach ($forms as $form) {
        $tags = $form['tags'] ?? '';
        if (is_array($tags)) {
            $tags = implode(', ', $tags);
        }
        $tags = trim($tags);
        
        $forms_data[$form['id']] = [
            'title' => $form['title'],
            'tags' => $tags
        ];
    }
    
    $forms_json = wp_json_encode($forms_data);
    
    $field_html = str_replace(
        '<select id="' . $fieldId . '"',
        '<select id="' . $fieldId . '" data-forms-data="' . esc_attr($forms_json) . '" class="acf-gravityforms-select"',
        $field_html
    );
    
    return $field_html;
}, 10, 4);

add_action('acf/input/admin_enqueue_scripts', function() {
    if (function_exists('acf_get_setting') && acf_get_setting('enqueue_select2')) {
        wp_enqueue_script('select2');
        wp_enqueue_style('select2');
    }
    
    wp_add_inline_script('jquery', '
        (function($) {
            function initGravityFormsSelect() {
                $(".acf-gravityforms-select").each(function() {
                    var $select = $(this);
                    if ($select.data("select2")) {
                        return;
                    }
                    
                    if (typeof $.fn.select2 === "undefined") {
                        setTimeout(initGravityFormsSelect, 200);
                        return;
                    }
                    
                    var formsData = {};
                    try {
                        var dataAttr = $select.attr("data-forms-data");
                        if (dataAttr) {
                            formsData = JSON.parse(dataAttr);
                        }
                    } catch(e) {
                        console.error("Error parsing forms data", e);
                    }
                    
                    var selectOptions = {
                        placeholder: "Search forms by title or tags...",
                        allowClear: true,
                        width: "100%",
                        minimumResultsForSearch: 0
                    };
                    
                    if (typeof $.fn.select2.amd !== "undefined") {
                        selectOptions.matcher = function(params, data) {
                            if (!params.term || params.term.trim() === "") {
                                return data;
                            }
                            
                            var term = params.term.toLowerCase();
                            var text = (data.text || "").toLowerCase();
                            var formId = data.id;
                            
                            if (text.indexOf(term) !== -1) {
                                return data;
                            }
                            
                            if (formsData[formId] && formsData[formId].tags) {
                                var tags = formsData[formId].tags.toLowerCase();
                                if (tags.indexOf(term) !== -1) {
                                    return data;
                                }
                            }
                            
                            return null;
                        };
                        
                        selectOptions.templateResult = function(data) {
                            if (!data.id) {
                                return data.text;
                            }
                            
                            var formId = data.id;
                            var $result = $("<span>" + (data.text || "") + "</span>");
                            
                            if (formsData[formId] && formsData[formId].tags) {
                                $result.append("<span style=\"color: #999; font-size: 0.9em; margin-left: 10px;\">(" + formsData[formId].tags + ")</span>");
                            }
                            
                            return $result;
                        };
                    }
                    
                    $select.select2(selectOptions);
                });
            }
            
            $(document).ready(function() {
                setTimeout(initGravityFormsSelect, 200);
            });
            
            $(document).on("acf/setup_fields", function() {
                setTimeout(initGravityFormsSelect, 200);
            });
        })(jQuery);
    ', 'after');
});
