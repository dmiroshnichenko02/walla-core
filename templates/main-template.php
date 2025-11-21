<?php

if (have_rows('sections')) :
	while (have_rows('sections')) : the_row();
		if (get_row_layout() == 'about_us') :
			get_template_part('components/sections/front/about-us');
		elseif (get_row_layout() == 'hero') :
			get_template_part('components/sections/front/hero');
		endif;
	endwhile;

else :
	echo '<p>No sections found</p>';
endif;
