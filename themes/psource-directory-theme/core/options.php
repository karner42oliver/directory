<?php

/**
* Directory_Theme_Options
*
* @copyright PSOURCE 2015-2024 {@link https://github.com/cp-psource}
* @author DerN3rd (PSOURCE)
* @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
*/
class DR_Theme_Options {

	/**
	* Class constructor.
	*/
	function __construct() {
		add_action( 'admin_init', array( $this, 'register_option_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_page' ) );

		// Hook located in header.php
		add_action( 'dir_theme_options', array( $this, 'output_theme_options' ) );
	}

	/**
	* Init plugin options to white list our options.
	*/
	function register_option_settings() {
		register_setting( 'dir_options_group', 'dir_options' );
	}

	/**
	* Load up the menu page.
	*/
	function add_page() {
		add_theme_page( __('Optionen', THEME_TEXT_DOMAIN), __('Optionen', THEME_TEXT_DOMAIN), 'edit_theme_options', 'options', array( &$this, 'output_admin_page' ) );
	}

	/**
	* Output the options page
	*/
	function output_admin_page() {
		if ( isset( $_REQUEST['updated'] ) )
		$msg = __( 'Optionen gespeichert!', THEME_TEXT_DOMAIN ); ?>

		<div class="wrap">
			<h2><?php echo wp_get_theme()->Name . ' ' . __('Optionen', THEME_TEXT_DOMAIN) ?></h2>

			<?php if ( isset( $msg ) ) : ?>
			<div class="updated fade"><p><strong><?php echo $msg; ?></strong></p></div>
			<?php endif; ?>

			<form method="post" action="options.php">
				<?php settings_fields('dir_options_group'); ?>
				<?php $options = get_option('dir_options'); ?>
				<input type="hidden" name="dir_colors[enable]" value="1" />
				<h3><?php _e( 'Präsentation', THEME_TEXT_DOMAIN  ); ?></h3>
				<table class="form-table">
					<tr>
						<th><label for="text_shadows"><?php _e('Textschatten', THEME_TEXT_DOMAIN); ?></label></th>
						<td>
							<input id="text_shadows" type="checkbox" name="dir_options[text_shadows]" value="1" <?php if ( !empty( $options['text_shadows'] ) ) echo 'checked="checked"'; ?> />
							<span class="description"><?php _e('Schalte Textschatten aus', THEME_TEXT_DOMAIN); ?></span>
						</td>
					</tr>
				</table>
				<h3><?php _e( 'Layout', THEME_TEXT_DOMAIN  ); ?></h3>
				<table class="form-table">
					<tr>
						<th><label for="layout"><?php _e('Layout', THEME_TEXT_DOMAIN); ?></label></th>
						<td>
							<select id="layout" name="dir_options[layout]">;
								<option <?php if ( isset( $options['layout'] ) && $options['layout'] == 'grid' ) echo 'selected="selected"'; ?> value="grid"><?php _e( 'Grid Layout', THEME_TEXT_DOMAIN ); ?></option>;
								<option <?php if ( isset( $options['layout'] ) && $options['layout'] == 'row' ) echo 'selected="selected"'; ?> value="row"><?php _e( 'Rows Layout', THEME_TEXT_DOMAIN ); ?></option>
							</select>
							<span class="description"><?php _e('Theme-Layout wechseln', THEME_TEXT_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<th><label for="style"><?php _e('Styles', THEME_TEXT_DOMAIN); ?></label></th>
						<td>
							<select id="style" name="dir_options[style]">
								<option <?php if ( isset( $options['style'] ) && $options['style'] == 'modern' ) echo 'selected="selected"'; ?> value="modern"><?php _e( 'Style Modern', THEME_TEXT_DOMAIN ); ?></option>
								<option <?php if ( isset( $options['style'] ) && $options['style'] == 'minimal' ) echo 'selected="selected"'; ?> value="minimal"><?php _e( 'Style Minimal', THEME_TEXT_DOMAIN ); ?></option>
								<option <?php if ( isset( $options['style'] ) && $options['style'] == 'boxed' ) echo 'selected="selected"'; ?> value="boxed"><?php _e( 'Style Boxed', THEME_TEXT_DOMAIN ); ?></option>
								<option <?php if ( isset( $options['style'] ) && $options['style'] == 'gloss' ) echo 'selected="selected"'; ?> value="gloss"><?php _e( 'Style Gloss', THEME_TEXT_DOMAIN ); ?></option>
								<option <?php if ( isset( $options['style'] ) && $options['style'] == 'skinny' ) echo 'selected="selected"'; ?> value="skinny"><?php _e( 'Style Skinny', THEME_TEXT_DOMAIN ); ?></option>
								<option <?php if ( isset( $options['style'] ) && $options['style'] == 'wide' ) echo 'selected="selected"'; ?> value="wide"><?php _e( 'Style Wide', THEME_TEXT_DOMAIN ); ?></option>
							</select>
							<span class="description"><?php _e('Theme-Style wechseln', THEME_TEXT_DOMAIN); ?></span>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" class="button-primary" name="save_colors" value="<?php _e('Änderungen speichern', THEME_TEXT_DOMAIN); ?>" />
				</p>
			</form>
		</div> <?php
	}

	/**
	* Load theme styles based on user options
	*
	* @return void
	*/
	function output_theme_options() {
		$options = get_option('dir_options');
		// load main styles
		if ( isset( $options['style'] ) ) { ?>
			<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/<?php echo $options['style']; ?>.css" type="text/css" media="all" />
			<?php
		} else { ?>
			<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/modern.css" type="text/css" media="all" />
			<?php
		}
		// load layout styles
		if ( isset( $options['layout'] ) ) { ?>
			<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/home-<?php echo $options['layout']; ?>.css" type="text/css" media="all" />
			<?php
		} else { ?>
			<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/home-grid.css" type="text/css" media="all" />
			<?php
		}
		// load no text shadows styles
		if ( !empty( $options['text_shadows'] ) ) { ?>
			<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/notextshadows.css" type="text/css" media="all" />
			<?php
		}
	}
}

new DR_Theme_Options();
