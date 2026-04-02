<?php
/*
Plugin Name: Verzeichnis
Plugin URI: https://github.com/cp-psource/docs/verzeichnis
Description: Verzeichnis - Erstelle eine vollständige Verzeichnis-Seite.
Version: 1.0.0
Author: PSOURCE
Author URI: https://github.com/cp-psource
Text Domain: dr_text_domain
Domain Path: /languages
License: GNU General Public License (Version 2 - GPLv2)
*/
$plugin_header_translate = array(
        __('Verzeichnis - Erstelle eine vollständige Verzeichnis-Seite.', 'dr_text_domain'),
        __('PSOURCE', 'dr_text_domain'),
        __('https://github.com/cp-psource', 'dr_text_domain'),
        __('Verzeichnis', 'dr_text_domain'),
        );
/*

Authors - DerN3rd
Copyright 2021-2026 PSOURCE, (https://github.com/cp-psource)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


// Define plugin version
define( 'DR_VERSION', '1.0.0' );
// define the plugin folder url
define( 'DR_PLUGIN_URL', plugin_dir_url(__FILE__) );
// define the plugin folder dir
define( 'DR_PLUGIN_DIR', plugin_dir_path(__FILE__) );
// The text domain for strings localization
define( 'DR_TEXT_DOMAIN', 'dr_text_domain' );
// The key for the options array
define( 'DR_OPTIONS_NAME', 'dr_options' );
// The key for the captcha transient
define( 'DR_CAPTCHA', 'dr_captcha_' );

// include core files

register_deactivation_hook( __FILE__, function() {
        unregister_post_type( 'directory_listing' );
        unregister_taxonomy( 'listing_tag' );
        unregister_taxonomy( 'listing_category' );
        flush_rewrite_rules();

} );

include_once 'core/core.php';
include_once 'core/functions.php';
include_once 'core/template-tags.php';
include_once 'core/payments.php';
include_once 'core/ratings.php';
