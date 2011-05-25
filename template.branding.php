<div class="branding_wrap">
	<?php pagelines_main_logo(); ?>
		
	<?php pagelines_register_hook( 'pagelines_before_branding_icons', 'branding' ); // Hook ?>

	<div class="icons" style="bottom: <?php echo intval(pagelines_option('icon_pos_bottom'));?>px; right: <?php echo pagelines_option('icon_pos_right');?>px;">

		<?php if(pagelines('rsslink')):?>
		<a target="_blank" href="<?php echo apply_filters( 'pagelines_branding_rssurl', get_bloginfo('rss2_url') );?>" class="rsslink"></a>
		<?php endif;?>
		
		<?php if(VPRO):?>
			<?php pagelines_register_hook( 'pagelines_branding_icons_start', 'branding' ); // Hook ?>
			<?php if(pagelines_option('twitterlink')):?>
			<a target="_blank" href="<?php echo pagelines_option('twitterlink');?>" class="twitterlink"></a>
			<?php endif;?>
			<?php if(pagelines_option('facebooklink')):?>
			<a target="_blank" href="<?php echo pagelines_option('facebooklink');?>" class="facebooklink"></a>
			<?php endif;?>
			<?php if(pagelines_option('linkedinlink')):?>
			<a target="_blank" href="<?php echo pagelines_option('linkedinlink');?>" class="linkedinlink"></a>
			<?php endif;?>
			<?php if(pagelines_option('youtubelink')):?>
			<a target="_blank" href="<?php echo pagelines_option('youtubelink');?>" class="youtubelink"></a>
			<?php endif;?>
			<?php pagelines_register_hook( 'pagelines_branding_icons_end', 'branding' ); // Hook ?>
		<?php endif;?>
		
	</div>
</div>
<?php pagelines_register_hook( 'pagelines_after_branding_wrap', 'branding' ); // Hook ?>
