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
		cursor: pointer !important;
	}
	.site-footer .wpcf7 input[type="submit"]:hover {
		background: #d2de09 !important;
		color: #000 !important;
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
	/* Styles for payment logos */
	.site-footer .pay-logos-row {
		margin-top: 10px;
		display: flex;
		align-items: center;
		gap: 4px;
	}
	.site-footer .pay-logos-row img {
		height: 20px;
		width: auto;
		object-fit: cover;
		display: inline-block;
	}
	.site-footer .pay-logos-label {
		font-family: "Inter", sans-serif;
		font-size: 14px;
		color: #CDD5DF;
		margin-right: 4px;
		white-space: nowrap;
	}
	@media (max-width: 767px) {
		.site-footer .box > p {flex-direction: column;}
		.site-footer .label-intl_tel-729, .site-footer .label-your-email {width: 100% !important;max-width: 100% !important;}
		.site-footer .wpcf7 input[type="submit"] {width: 100% !important;}
		.site-footer .wpcf7 .wpcf7-spinner {position: absolute;right: 15px;}
		.site-footer .pay-logos-row {flex-wrap:wrap;}
	}

	footer .intl-tel-input .country-list, footer .intl-tel-input .selected-flag .iti-arrow {
		display: none !important;
	}
</style>

<footer id="colophon" class="site-footer bg-[#030616] pt-5 md:pt-17.5 px-4">
	<div class="container m-auto">

		<?php $help_box = get_field('help_box_footer','option'); if($help_box) { ?>
		<div class="box-help-footer py-6 px-4 md:p-10 rounded-[24px] md:rounded-[36px] bg-[#F1F1F1] flex flex-wrap md:flex-nowrap gap-[24px] md:gap-[121px] min-h-[408px] mb-17.5">
			<div class="w-full md:w-1/2 flex flex-col">
				<h2 class="capitalize font-manrope text-[30px] md:text-[44px] leading-[40px] md:leading-[54px] font-medium max-w-[510px] mb-4 md:mb-5"><?php echo $help_box['title']; ?></h2>
				<div class="capitalize font-inter text-[16px] md:text-[18px] text-black/80 max-w-[540px]"><?php echo $help_box['text']; ?></div>
			</div>
			<div class="w-full md:w-1/2">
				<?php echo do_shortcode('[contact-form-7 id="08ced4c" title="Contact form 1"]'); ?>
			</div>
		</div>
		<?php } ?>

		<div class="footer-row flex justify-between mb-[65px] md:mb-28">

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
					<?php
					$pay_logos = get_field('pay_logos', 'option');
					if ($pay_logos && is_array($pay_logos)) {
					?>
					<div class="pay-logos-row">
						<span class="pay-logos-label">We accept:</span>
						<?php foreach ($pay_logos as $logo) {
							if (!empty($logo['image'])) { ?>
								<img src="<?php echo esc_url($logo['image']); ?>" alt="Payment Logo" />
							<?php }
						} ?>
					</div>
					<?php } ?>
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
		<div class="footer-row flex flex-col md:flex-row items-center md:items-start justify-between py-[21px]">
			<div class="font-inter text-white text-[13px]"><?php echo $bottom_bar['left_text']; ?></div>
			<ul class="flex flex-wrap justify-center gap-[17px] md:gap-7">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    var rows = document.querySelectorAll('.woocommerce-checkout .form-row');

    if(!rows) return

    function isFilled(el) {
        if (!el) return false;
        if (el.tagName === 'SELECT') {
            return el.value !== '' && el.value !== undefined && el.value !== null;
        }
        return (el.value || '').toString().trim().length > 0;
    }

    function activate(row, active) {
        if (!row) return;
        row.classList.toggle('floating-active', !!active);
    }

    rows.forEach(function(row) {
        var input = row.querySelector('input.input-text, input[type="text"], input[type="email"], input[type="tel"], input[type="password"], input[type="number"], textarea, select');
        var label = row.querySelector(':scope > label');
        if (!input || !label) return;

        // Initialize state
        activate(row, document.activeElement === input || isFilled(input));

        input.addEventListener('focus', function() { activate(row, true); });
        input.addEventListener('blur', function() { activate(row, isFilled(input)); });
        input.addEventListener('input', function() { activate(row, isFilled(input)); });
        input.addEventListener('change', function() { activate(row, isFilled(input)); });
    });

    // Handle Select2 widgets if present
    if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
        jQuery('.woocommerce-checkout .form-row select').on('select2:open', function(e) {
            var row = this.closest('.form-row');
            activate(row, true);
        });
        jQuery('.woocommerce-checkout .form-row select').on('select2:close change', function(e) {
            var row = this.closest('.form-row');
            activate(row, isFilled(this));
        });
    }

    // Explicitly handle address placeholders by ID
    ['billing_address_1', 'billing_address_2'].forEach(function(id) {
        var field = document.getElementById(id);
        if (!field) return;
        var row = field.closest('.form-row');
        var label = row ? row.querySelector(':scope > label') : null;
        var placeholder = field.getAttribute('placeholder') || field.getAttribute('data-placeholder') || '';

        if (label) {
            if (label.classList.contains('screen-reader-text')) {
                label.classList.remove('screen-reader-text');
            }
            if (placeholder && !label.textContent.trim()) {
                label.textContent = placeholder;
            }
        }

        field.removeAttribute('placeholder');
        field.setAttribute('data-placeholder', '');
        activate(row, document.activeElement === field || isFilled(field));
    });

    // Convert placeholders to labels or remove them
    document.querySelectorAll('.woocommerce-checkout .woocommerce-billing-fields .form-row input, .woocommerce-checkout .woocommerce-billing-fields .form-row textarea').forEach(function(field) {
        var row = field.closest('.form-row');
        if (!row) return;
        var label = row.querySelector(':scope > label');
        var placeholder = field.getAttribute('placeholder') || field.getAttribute('data-placeholder') || '';

        // If label exists but is screen-reader only, make it visible and prefer placeholder as text
        if (label) {
            if (label.classList.contains('screen-reader-text')) {
                label.classList.remove('screen-reader-text');
            }
            // If placeholder is non-empty and label text is empty or equals placeholder pattern, set to placeholder
            var labelText = label.textContent.trim();
            if (placeholder && (!labelText || labelText === placeholder)) {
                label.textContent = placeholder;
            }
        } else if (placeholder) {
            // Create a label if missing
            var newLabel = document.createElement('label');
            var id = field.getAttribute('id');
            if (id) newLabel.setAttribute('for', id);
            newLabel.textContent = placeholder;
            row.insertBefore(newLabel, row.firstChild);
            label = newLabel;
        }

        // Remove placeholders so floating label takes over
        field.setAttribute('data-placeholder', '');
        field.removeAttribute('placeholder');

        // Re-evaluate floating state after change
        activate(row, document.activeElement === field || isFilled(field));
    });

    // Hard remove any remaining placeholders across the entire checkout form
    document.querySelectorAll('.woocommerce-checkout .woocommerce-billing-fields input, .woocommerce-checkout .woocommerce-billing-fields textarea, .woocommerce-checkout .woocommerce-billing-fields select').forEach(function(el){
		
        if (el.hasAttribute('placeholder')) {
            el.removeAttribute('placeholder');
        }
        if (el.hasAttribute('data-placeholder')) {
            el.setAttribute('data-placeholder', '');
        }
    });
});
</script>

</html>