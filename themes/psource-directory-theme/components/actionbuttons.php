<div id="action-bar">
	<span><?php _e( 'Willkommen, was möchtest Du tun?', THEME_TEXT_DOMAIN ); ?></span>

	<?php if ( !is_user_logged_in()): ?>
	<div id="submit-site"><?php echo do_shortcode('[dr_signup_btn view="always" text="' . __('Registrieren',THEME_TEXT_DOMAIN ) . '"]'); ?></div>
	<div id="submit-site"><?php echo do_shortcode('[dr_signin_btn view="always" text="' . __('Anmelden',THEME_TEXT_DOMAIN ) . '"]'); ?></div>
	<?php else: ?>
	<form id="log-out" action="#" method="post">
		<input type="submit" name="directory_logout" value="<?php _e('Abmelden', THEME_TEXT_DOMAIN); ?>" />
	</form>
	<?php 
	if ( current_user_can('create_listings') ): 
	?>
	<form id="add-listing" action="#" method="post">
		<input type="submit" name="redirect_listing" value="<?php _e('Eintrag hinzufügen', THEME_TEXT_DOMAIN); ?>" />
	</form>
	<?php else: ?>
	<div id="submit-site"><?php echo do_shortcode('[dr_signup_btn view="always" text="' . __('Registrieren',THEME_TEXT_DOMAIN ) . '"]'); ?></div>
	<?php endif; ?>

	<form id="go-to-profile" action="#" method="post">
		<input type="submit" name="redirect_profile" value="<?php _e('Profil', THEME_TEXT_DOMAIN); ?>" />
	</form>
	<?php endif; ?>

	<div class="clear"></div>
</div>
