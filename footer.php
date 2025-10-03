<?php

?>

<style>
	.site-footer .wpcf7 input {
		width: 100% !important;
		height: 48px !important;
		padding: 14px 20px !important;
		border-radius: 48px !important;
		border: 1px solid rgba(1, 1, 1, 0.1) !important;
		background: #fff !important;
	}

	.site-footer .intl-tel-input {
		display: flex !important;
		gap: 16px;
	}
	.site-footer .intl-tel-input input {
		padding-left: 76px !important;
	}
	.site-footer .intl-tel-input .flag-container {
		padding: 0 15px !important;
	}
	.site-footer .intl-tel-input .flag-container .selected-flag {
		position: relative;
	}
	.site-footer .intl-tel-input .flag-container .selected-flag:after{
		width: 1px;
		height: 24px;
		background: rgba(1, 1, 1, 0.1);
		position: absolute;
		content: "";
		right: 0;
		top: 50%;
		transform: translateY(-50%);
	}

	.site-footer .label-intl_tel-729, .site-footer .label-your-email {
		width: 50% !important;
		max-width: 50% !important;
		display: block !important;
	}
	.site-footer .box > p {
		display: flex;
		justify-content: space-between;
		gap: 12px;
		width: 100%;
	}
	.site-footer .wpcf7 input[type="submit"] {
		width: 147px !important;
		height: 48px !important;
		border-radius: 48px !important;
		background: #030616 !important;
		color: #000 !important;
		background: #E8F501 !important;
		font-size: 16px !important;
		font-weight: 500 !important;
		padding: 0 !important;
		line-height: 24px !important;
		font-family: "Inter", sans-serif !important;
		margin-bottom: 20px !important;
	}
	.site-footer .text {
		font-family: "Inter", sans-serif !important;
		font-weight: 400;
		font-size: 14px;
		line-height: 24px;
		color: rgba(0, 0, 0, 0.6);
	}
	.site-footer .accpt {
		margin-top: 8px;
	}
	.site-footer .accpt label {
		display: flex;
		align-items: center;
	}
	.site-footer .accpt .wpcf7-list-item {
		margin: 0 !important;
	}
	.site-footer .accpt input{
		width: 16px !important;
		height: 16px !important;
		border-radius: 3px !important;
		border: 1px solid rgba(0, 0, 0, 0.6) !important;
		background: #fff !important;
		margin-right: 10px !important;
		cursor: pointer !important;
	}
	.site-footer .accpt span{
		color: rgba(0, 0, 0, 0.6) !important;
		font-size: 14px !important;
		line-height: 24px !important;
		font-family: "Inter", sans-serif !important;
		font-weight: 400 !important;
	}
</style>

<footer id="colophon" class="site-footer bg-[#030616] pt-5 md:pt-17.5 px-4">
	<div class="container m-auto">

		<?php $help_box = get_field('help_box_footer','option'); if($help_box) { ?>
		<div class="box-help-footer p-10 rounded-[36px] bg-[#F1F1F1] flex gap-[121px] min-h-[408px] mb-17.5">
			<div class="w-full md:w-1/2 flex flex-col">
				<h2 class="capitalize font-manrope text-[44px] leading-[54px] font-medium max-w-[510px] mb-5"><?php echo $help_box['title']; ?></h2>
				<div class="capitalize font-inter text-[18px] text-black/80 max-w-[540px]"><?php echo $help_box['text']; ?></div>
			</div>
			<div class="w-full md:w-1/2">
				<?php echo do_shortcode('[contact-form-7 id="08ced4c" title="Contact form 1"]'); ?>
			</div>
		</div>
		<?php } ?>

		<div class="footer-row flex justify-between mb-28">

			<div class="min-w-[45%]">
				<div class="flex flex-col items-start max-w-[360px] gap-3.5">
					<div class="logo w-[112px] object-cover">
						<a href="<?php echo home_url(); ?>"><img src="<?php the_field('logotype','option'); ?>" alt="" /></a>
					</div>	
					<div class="text-[#CDD5DF] font-inter text-[15px]"><?php the_field('text_f','option'); ?></div>
					<?php $socials = get_field('socials','option');  ?>
					<div class="flex items-center gap-[7px]">
						<?php foreach ($socials as $row) { ?>
							<a class="h-[32px] w-[32px] flex items-center justify-center" href="<?php echo $row['link']; ?>" target="_blank"><img class="h-[32px] w-[32px]" src="<?php echo $row['icon']; ?>" alt=""></a>
						<?php } ?>
					</div>
					<!-- <?php if(get_field('qr-code','option')) { ?>
					<div class="p-[13px] rounded-[22px] bg-white">
						<img class="max-w-[136px] object-cover" src="<?php the_field('qr-code','option'); ?>" alt="" />
					</div>
					<?php } ?> -->
				</div>
			</div>

			<!-- <?php $menus = get_field('menus','option'); if($menus) { ?>
			<?php foreach ($menus as $row) { ?>		
			<div class="flex flex-col items-start gap-3.5">
				<h4 class="font-manrope text-white text-[14px] leading-[22px] font-medium uppercase"><?php echo $row['title']; ?></h4>
				<?php if ( !empty($row['menu']) ) { ?>
				<?php 
					wp_nav_menu( array(
						'menu'            => intval($row['menu']),
						'container'       => false,
						'menu_class'      => 'flex flex-col gap-3.5 font-inter text-white/60 text-[15px]',
						'fallback_cb'     => false,
					) );
					?>
				<?php } ?>
			</div>
			<?php } } ?> -->

		</div>

		<?php $bottom_bar = get_field('bottom_bar','option'); if($bottom_bar) { ?>
		<div class="footer-row flex justify-between py-[21px]">
			<div class="font-inter text-white text-[13px]"><?php echo $bottom_bar['left_text']; ?></div>
			<ul class="flex gap-7">
			<?php foreach ($bottom_bar['links'] as $row) { ?>	
			<li><a class="font-inter text-white text-[13px]" href="<?php echo $row['navigate_link']['link']; ?>"><?php echo $row['navigate_link']['text']; ?></a></li>
			<?php } ?>
			</ul>
		</div>
		<?php } ?>

	</div>
</footer><!-- #colophon -->

</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>