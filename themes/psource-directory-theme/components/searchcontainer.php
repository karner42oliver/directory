<div id="searchbox">
	<form method="get" id="searchform" action="<?php echo home_url( '/' ); ?>">
		<div>
			<label class="screen-reader-text" for="s"><?php _e( 'Was kann ich heute für Dich finden?', THEME_TEXT_DOMAIN ); ?></label>
			<input type="text" value="" name="s" id="s" size="32"/>
			<input type="submit" id="searchsubmit" value="<?php _e('Finde es jetzt!', THEME_TEXT_DOMAIN); ?> " />
		</div>
	</form>
</div>
