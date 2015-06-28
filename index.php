<?php
/**
 * Plugin Name: Responsive Posts Carousel
 * Plugin URI: http://webcodingplace.com/responsive-posts-carousel
 * Description: Displays Posts as Carousel using Shortcodes
 * Version: 1.0
 * Author: Rameez
 * Author URI: http://webcodingplace.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wcp-carousel
 */

/*

  Copyright (C) 2015  Rameez  rameez.iqbal@live.com
*/
require_once('plugin.class.php');

if( class_exists('WCP_Posts_Carousel')){
	
	$just_initialize = new WCP_Posts_Carousel;
}
?>