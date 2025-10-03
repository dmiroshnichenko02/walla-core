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
