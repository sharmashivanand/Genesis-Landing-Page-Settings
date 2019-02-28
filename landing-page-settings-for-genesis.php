<?php

/**
 *
 * Plugin Name: Landing Page Settings for Genesis
 * Description: Easily show / hide elements on a page, post or any custom post type on your Genesis powered website.
 * Version: 1.1
 * Author: Shivanand Sharma
 * Author URI: https://www.converticacommerce.com.com/
 * License: GPL-2.0+
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 * Text Domain: genesis-landing-page-settings
 *
 */

/* Bail if accessing directly */
if ( !defined( 'ABSPATH' ) ) {
	wp_die( "Sorry, you are not allowed to access this page directly." );
}
 
define( 'GLPS_SETTINGS_FIELD','genesis-landing-page-settings' );
define( 'GLPS_PLUGIN_NAME', 'Landing Page Settings for Genesis' );
define( 'GLPS_PLUGIN_URL', plugin_dir_url(__FILE__) );
define( 'GLPS_PLUGIN_DIR', plugin_dir_path(__FILE__) );

define( 'GLPS_SETTINGS_VER', '1.0' );


register_activation_hook( __FILE__, 'glps_activation' );

function glps_activation() {

	global $wp_version;
	$current_theme = wp_get_theme();
	
	if ( 'genesis' != basename( TEMPLATEPATH ) )
		glps_deactivate_template_err();
	
	if(  !defined( 'PARENT_THEME_VERSION' ) || !version_compare( PARENT_THEME_VERSION, '2.1.0', '>=' ) || !version_compare( $wp_version, '3.9', '>=' ) )
		glps_deactivate_version_err( '2.1.0', '3.9' );
	
	if( $current_theme->get( 'TextDomain' ) === 'lander' && $current_theme->get( 'Author' ) === 'Shivanand Sharma' ) {
		glps_deactivate_lander_installed();
	}
	
}


/**
 * Deactivate the plugin if Genesis is not 'active'
 * Useful when users switch themes
 */
add_action( 'admin_init', 'gme_deactivate_self_no_genesis' );

function gme_deactivate_self_no_genesis() {
    
	if ( !function_exists('genesis_pre') ) {
		deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate plugin
		glps_deactivate_genesis_inactive();
    }
	
}

/**
 * Deactivate plugin if Lander or derived theme(s) is installed and activated
 * Alternatively, also check for the theme support that lander themes include and deactivate the plugin, if found any
 */

add_action( 'admin_init', 'glps_activation_validate_theme' );

function glps_activation_validate_theme() {

	$current_theme = wp_get_theme();
	
	if( ( $current_theme->get( 'TextDomain' ) === 'lander' && $current_theme->get( 'Author' ) === 'Shivanand Sharma' ) ) {
		glps_deactivate_lander_installed();
	}
	
}
 

/**
 * Check if the parent theme Genesis is installed, else deactivate
 */
 
function glps_deactivate_template_err() {
	
	deactivate_plugins( plugin_basename( __FILE__ ) );
	
	$notice =  sprintf( __( '%s%s plugin requires %sGenesis Framework%s to be installed and activated. Please install Genesis as the parent theme to use %s.%sIf Genesis Framework / Genesis child theme is already installed, go to the %sThemes page%s and activate it.%s&larr; Return to Plugins page%sGo to Themes page &rarr;%s', 'genesis-landing-page-settings' ), '<p>', GLPS_PLUGIN_NAME, '<a href="http://www.binaryturf.com/genesis">', '</a>', GLPS_PLUGIN_NAME, '</p><p>', '<a href="' . self_admin_url( 'themes.php' ) . '">', '</a>', '</p><p><a class="glps-button" href="' . self_admin_url( 'plugins.php' ) . '" style="background-color: #f2f2f2; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.25); display: inline-block; margin: 0 auto; padding: 10px 12px; float: left;" target="_parent">', '</a><a href="' . self_admin_url( 'themes.php' ) . '" style="background-color: #f2f2f2; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.25); display: inline-block; margin: 0 auto; padding: 10px 12px; float: right;">','</a><span style="display: block; clear: both;"></span></p>' );
	
	wp_die( $notice );

}

/**
 * Check the active theme to be Genesis
 * Deactivate the plugin, if Genesis not active
 */

function glps_deactivate_genesis_inactive() {
	
	deactivate_plugins( plugin_basename( __FILE__ ) );
	
	$notice =  sprintf( __( '%s plugin requires %sGenesis Framework%s to be installed and activated. Please install Genesis as the parent theme to use %s.%sThe plugin will now be deactivated.%s&larr; Return to Plugins page%sGo to Themes page &rarr;%s', 'genesis-landing-page-settings' ), '<p>' . GLPS_PLUGIN_NAME, '<a href="http://www.binaryturf.com/genesis">', '</a>', GLPS_PLUGIN_NAME, '</p><p>', '</p><p><a class="glps-button" href="' . self_admin_url( 'plugins.php' ) . '" style="background-color: #f2f2f2; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.25); display: inline-block; margin: 0 auto; padding: 10px 12px; float: left;" target="_parent">', '</a><a href="' . self_admin_url( 'themes.php' ) . '" style="background-color: #f2f2f2; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.25); display: inline-block; margin: 0 auto; padding: 10px 12px; float: right;">','</a><span style="display: block; clear: both;"></span></p>' );
	
	wp_die( $notice );
	
}

/**
 * Check the WordPress and Genesis version
 * WordPress to be 3.9 and Genesis to be 2.1.0 to use the plugin, else deactivate
 */

function glps_deactivate_version_err( $genesis_version, $wp_version ) {
	
	deactivate_plugins( plugin_basename( __FILE__ ) );
	
	$notice = sprintf( __( '%s%s requires WordPress version %s and Genesis version %s or greater. Please update to the latest version and try again.%s&larr; Return to Plugins page%sGo to Themes page &rarr;%s', 'genesis-landing-page-settings' ), '<p>', GLPS_PLUGIN_NAME, '<strong>' . $wp_version . '</strong>', '<strong>' . $genesis_version . '</strong>', '</p><p><a class="glps-button" href="' . self_admin_url( 'plugins.php' ) . '" style="background-color: #f2f2f2; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.25); display: inline-block; margin: 0 auto; padding: 10px 12px; float: left;" target="_parent">', '</a><a href="' . self_admin_url( 'themes.php' ) . '" style="background-color: #f2f2f2; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.25); display: inline-block; margin: 0 auto; padding: 10px 12px; float: right;">','</a><span style="display: block; clear: both;"></span></p>' );
	
	wp_die( $notice );

}

function glps_deactivate_lander_installed() {
	
	deactivate_plugins( plugin_basename( __FILE__ ) );
	
	$theme = wp_get_theme();
	
	/* Activation / Deactivation Notices */
	
	$notice = sprintf( __( '%1$s is already built into %3$s%2$sYou\'re using %3$s theme on your site. %3$s already has built-in support for SILO menus.%4$s&larr; Return to Plugins page%5$s%6$sGo to Themes Page &rarr;%7$s', 'genesis-landing-page-settings' ), '<h3>' . GLPS_PLUGIN_NAME, '</h3><p>', $theme->get( 'Name' ), '</p><p><a class="glps-button" href="' . self_admin_url( 'plugins.php' ) . '" style="background-color: #f2f2f2; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.25); display: inline-block; margin: 0 auto; padding: 10px 12px; float: left;" target="_parent">', '</a>', '<a href="' . self_admin_url( 'themes.php' ) . '" style="background-color: #f2f2f2; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.25); display: inline-block; margin: 0 auto; padding: 10px 12px; float: right;">', '</a><span style="display: block; clear: both;"></span></p>' );
	
	wp_die( $notice );
	
}


/**
 * Add the Support and Author links to the plugin in the admin area on the plugins page
 */
 
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'glps_add_action_links' );

function glps_add_action_links ( $links ) {
	
	$link = array(
		'<a href="'. admin_url( 'admin.php?page=' . GLPS_SETTINGS_FIELD ) .'">' . __( 'Settings', 'genesis-landing-page-settings' ) . '</a>',
		'<a href="https://wordpress.org/support/plugin/genesis-landing-page-settings">' . __( 'Support', 'genesis-landing-page-settings' ) . '</a>'
	);
	
	return array_merge( $links, $link );
	
}

/*
 * Safe load plugin textdomain, during init for translations
 * @refer http://geertdedeckere.be/article/loading-wordpress-language-files-the-right-way
 */
 
add_action( 'init', 'glps_load_textdomain' );

/**
 * Makes the plugin translation ready
 * Let's make the plugin also look in the WordPress languages directory for translations
 * Translation files should follow the syntax: genesis-landing-page-settings-{your-domain}
 *
 * Example: To set the pluign language to German(de_DE), you can drop in your translation files named as: genesis-landing-page-settings-de_DE.mo / genesis-landing-page-settings-de_DE.po
 */

function glps_load_textdomain() {
		
	$domain = 'genesis-landing-page-settings';
	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	/* Users can create a directory 'genesis-landing-page-settings' under '../wp-content/languages/' and drop in translation files with the similar filename syntax as described above */
	load_textdomain( $domain, WP_LANG_DIR . '/genesis-landing-page-settings/' . $domain . '-' . $locale . '.mo' );
	
	load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) .'/languages/' );

}


/**
 * Include all the plugin core function files
 * Init all the plugin core functionality and features
 */

add_action( 'genesis_init', 'glps_init_plugin', 20 );

function glps_init_plugin() {
	
	if ( is_admin() ) {
		// Include the admin file that builds the plugin admin page
		require_once( GLPS_PLUGIN_DIR . 'admin/glps-admin.php' );
		// Include the file that builds landing page settings for regular viewport
		require_once( GLPS_PLUGIN_DIR . 'admin/glps-lp-settings-regular.php' );
		// Include the file that builds landing page settings for mobile viewport
		require_once( GLPS_PLUGIN_DIR . 'admin/glps-lp-settings-mobile.php' );
	}
	
	// Include the core functions file to execute the plugin functionality
	require_once( GLPS_PLUGIN_DIR . 'functions/glps-functions.php' );
	
	// Include the mobile detection library
	if( !class_exists( 'Mobile_Detect' ) ) {
		require_once( GLPS_PLUGIN_DIR . 'functions/glps-mobile-detection.php' );
	}
	
	add_action( 'admin_enqueue_scripts', 'glps_admin_scripts' );
	
	// Add the Landing Page feature to supported post types
	add_action( 'init', 'glps_init_feature_post_types' );
	
}