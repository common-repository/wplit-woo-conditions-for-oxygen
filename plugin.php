<?php
/*
Plugin Name:	WPlit Woo Conditions for Oxygen
Description:	Build more dynamic e-commerce sites in Oxygen with Woocommerce-focused conditions.
Version:		1.0.0
Author:			WPlit
Author URI:		https://wplit.com/
License:		GPL-2.0+
License URI:	http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain:    woo-conditons-oxy

This plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

This plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with This plugin. If not, see {URI to Plugin License}.
*/


if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WCO_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Start (only if we have the correct plugins activated).
 */
add_action( 'plugins_loaded', 'lit_wco_plugin_init' );
function lit_wco_plugin_init() {
    
      // check if WooCommerce installed and active
      if (!class_exists( 'WooCommerce' ) ) {
        return;
      }

      // check if Oxygen (2.3+) installed and active
      if (!function_exists('oxygen_vsb_register_condition')) {
          return;
      }
     
      // get Woocommerce conditions
      require_once( WCO_PLUGIN_PATH . 'lib/conditions.php' );  
    
}

/**
 * Adds link in Plugin page to settings
 */
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'lit_wco_action_links');
function lit_wco_action_links( $links ) {
    
	$plugin_shortcuts = array(
        '<a href="https://www.buymeacoffee.com/wplit" target="_blank">Donate Coffee</a>'
    );
    return array_merge($links, $plugin_shortcuts);
}
