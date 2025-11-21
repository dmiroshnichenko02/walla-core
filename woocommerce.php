<?php

get_header();

if ( function_exists( 'woocommerce_content' ) ) {
	woocommerce_content();
} else {
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			the_content();
		}
	}
}

get_footer();


