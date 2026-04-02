<?php
/**
* Uninstall Directory plugin
* @package Verzeichnis
* @version 1.0.0
* @copyright Incsub 2007-2011 {@link https://github.com/cp-psource}
* @author Arnold Bailey (Incsub)
* @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
*/

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
exit();

flush_rewrite_rules();
