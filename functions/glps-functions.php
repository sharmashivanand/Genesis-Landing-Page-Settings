<?php

/**
 *
 * Genesis Landing Page Settings — Primary functions file
 * Description: This file includes all the functions that execute the basic functionality of the plugin.
 *
 * @package Genesis Landing Page Settings
 * @author Shivanand Sharma
 * @since 1.0
 *
 */
 

/* Bail if accessing directly */
if ( !defined( 'ABSPATH' ) ) {
	wp_die( "Sorry, you are not allowed to access this page directly." );
}


/**
 * Helper function that can be used by devs to debug any random variable / function output or anything, in human-readable format ;)
 */

function glps_log( $text, $echo = true ) {
	if ( $echo == true ) {
		echo '<pre>';
		print_r( $text );
		echo '</pre>';
	}
	if ( $echo == false ) {
		return print_r( $text, true );
	}
}


/**
 * Enqueue scripts on admin pages
 *
 * @return none 
 * @since 1.0
 */
 
function glps_admin_scripts() {
	wp_enqueue_script( 'glps-admin-scripts', GLPS_PLUGIN_URL . 'admin/js/glps-scripts.js', array('jquery'), false, false );
}


/**
 * Set-up the default settings for the plugin
 * Allows a filter glps_settings_default() to allow filtering the default options
 *
 * @return array $defaults
 * @since 1.0
 */
 
function glps_settings_defaults() {

    $all_post_types = glps_get_public_post_types();
    
	if( empty( $all_post_types ) )
		$defaults = array();
	else {
		$defaults = array(
			'glps_post_type_page' => 1,
			'glps_post_type_post' => 1,
		);
	}
    
    return apply_filters( 'glps_settings_defaults', $defaults );

}


/**
 * Helper function to fetch the settings and values from the plugin settings field in the database
 *
 * @uses genesis_get_option()
 * @param string; $key to search in the settings field
 * @return field value; type mixed
 * @since 1.0
 */
 
function glps_get_option( $key ) {
	return genesis_get_option( $key, GLPS_SETTINGS_FIELD, false );
}


/**
 * Helper function to fetch the available public post types
 *
 * @return @none
 * @since 1.0
 */

function glps_get_public_post_types() {

    $args = array(
        'public' => true,
		'show_ui' => true,
    );

    $available_post_types = get_post_types( $args, 'objects' );

    return $available_post_types;

}


/**
 * Fetch the post types for which the landing page settings have been enabled by the user
 *
 * @return array $enbaled_post_types
 * @since 1.0
 */

function glps_enabled_post_types() {
	
	$glps_settings = get_option( 'genesis-landing-page-settings' );
	$pt_str = 'glps_post_type_';
	$enabled_post_types = false;
	
	if( empty( $glps_settings ) )
		return false;
	
	foreach( $glps_settings as $option => $value  ) {
		$post_type_options = strpos( $option, $pt_str );
		if( $post_type_options  !== false ) {
			$enabled_post_types[] = str_replace( $pt_str, '', $option  );
		}
	}
	
	return $enabled_post_types;
	
}


/**
 * Add post type supports for landing page settings on user selected post types
 *
 * @return none
 * @since 1.0
 */

function glps_init_feature_post_types() {
	
	// Add post type support for the feature on enabled post types
	$supported_post_types = glps_enabled_post_types();
	if( !$supported_post_types )
		return;

	foreach( $supported_post_types as $glps_post_type ) {
		add_post_type_support( $glps_post_type, 'glps-landing-page-settings' );
		add_post_type_support( $glps_post_type, 'glps-mobile-landing-page-settings' );
	}
	
}


/**
 * Detect the hook for breadcrumb display (can be used for any callback; just pass the callback as the argument to this function)
 * Since different Genesis themes may use different hooks to display the breadcrumbs, we'll need to find out the active hook for Genesis breadcrumbs
 *
 * @param $callback; Default: genesis_do_breadcrumbs
 * @return array
 * @since 1.0
 */

function glps_detect_cb_hook( $target_cb = 'genesis_do_breadcrumbs' ) {
	
	global $wp_filter;
	
	foreach ( $wp_filter as $hook_location => $hook ) {
		foreach ( $hook as $priority => $actions ) {
			foreach ( array_keys( $actions ) as $action_cb ) {
				if ( $target_cb === $action_cb ) {
					return array( 'hook' => $hook_location, 'priority' => $priority );
				}
			}
		}
	}
	
}


/**
 * Wrapper function for woocommerce 'is_shop()' function
 *
 * @return none
 * @since 1.0
 */

function glps_is_woo_shop() {
	
	if( in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) ) ) {
		if( is_shop() ) {
			return true;
		}
	}
	
}


/**
 * Add a new class to entry markup when user hides the page title for a post type
 * To ensure that this option does not hurt the SEO of the post type, we'll hide it via CSS using this class
 *
 * @param $classes
 * @return array $classes
 * @since 1.0
 */
 
function glps_hide_post_type_title( $classes ) {
	
	if( in_the_loop() ) {
		$new_class = 'glps-hidden-title';
		$classes[] = esc_attr( sanitize_html_class( $new_class ) );
	}
	
	return $classes;
	
}


/* Helper function to add necessary styles to the <head> so that the page title is hidden for the desired post type */

add_action( 'wp_head', 'glps_custom_head_styles' );

function glps_custom_head_styles() {
	echo '
		<style type="text/css">
			.glps-hidden-title .entry-header,
			.glps-hidden-title .entry-title {
				text-indent: -9999px;
				height: 0;
				margin: 0;
				padding: 0;
				line-height: 0;
			}
		</style>
	';
}
 
 
/* Hide / show elements on the page based on the user settings  */

add_action( 'genesis_before', 'glps_regular_show_hide_elements', 99 );
add_action( 'genesis_before', 'glps_mobile_show_hide_elements', 99 );

/**
 * Show / hide the elements on the basis of user settings for desktop viewport
 *
 * @param none
 * @return none
 * @since 1.0
 */

function glps_regular_show_hide_elements() {
	
	// Bail, if not a desktop viewport
	if( !glps_is_notdevice() )
		return;
	
	if( !is_singular() && !glps_is_woo_shop() )
		return;
	
	$page_id = get_the_ID();
	
	if( get_option( 'show_on_front' ) == 'page' && is_home() ) {
		$page_id = get_option( 'page_for_posts' );
	}
	
	if( glps_is_woo_shop() && function_exists( 'wc_get_page_id' ) ) {
		$page_id = wc_get_page_id( 'shop' );
	}
	
	// Check the options set by the user
	
	$glps_settings_regular = get_post_meta( $page_id, '_glps_lp_settings_regular', true );
	
	$hide_header = isset( $glps_settings_regular['glps-hide-header'] ) ? $glps_settings_regular['glps-hide-header'] : false;

	$hide_breadcrumbs = isset( $glps_settings_regular['glps-hide-breadcrumbs'] ) ? $glps_settings_regular['glps-hide-breadcrumbs'] : false;

	$hide_page_title = isset( $glps_settings_regular['glps-hide-title'] ) ? $glps_settings_regular['glps-hide-title'] : false;

	$hide_after_entry_widget = isset( $glps_settings_regular['glps-hide-after-entry-widget'] ) ? $glps_settings_regular['glps-hide-after-entry-widget'] : false;

	$hide_footer_widgets = isset( $glps_settings_regular['glps-hide-footer-widgets'] ) ? $glps_settings_regular['glps-hide-footer-widgets'] : false;

	$hide_footer = isset( $glps_settings_regular['glps-hide-footer'] ) ? $glps_settings_regular['glps-hide-footer'] : false;
	
	// Hide the elements based on the user settings
	
	if ( $hide_header ) {
		remove_action( 'genesis_header', 'genesis_header_markup_open', 5 );
		remove_action( 'genesis_header', 'genesis_header_markup_close', 15 );
		remove_action( 'genesis_header', 'genesis_do_header' );
	}
	
	if( $hide_breadcrumbs ) {
		$current_hook = glps_detect_cb_hook();
		remove_action( $current_hook['hook'], 'genesis_do_breadcrumbs', $current_hook['priority'] );
		// Remove WooCommerce breadcrumbs
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			if( is_woocommerce() || is_cart() || is_checkout() ) {
				$woo_breadcrumbs = glps_detect_cb_hook( 'woocommerce_breadcrumb' );
				remove_action( $woo_breadcrumbs['hook'], 'woocommerce_breadcrumb', $woo_breadcrumbs['priority'] );
			}
		}
	}
	
	if( $hide_page_title ) {
		add_filter( 'post_class', 'glps_hide_post_type_title' );
		// Remove WooCommerce page title
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			if( is_woocommerce() || is_cart() || is_checkout() ) {
				add_filter( 'woocommerce_show_page_title', '__return_false' );
			}
		}
	}
	
	if( $hide_footer_widgets ) {
		$current_hook = glps_detect_cb_hook( 'genesis_footer_widget_areas' );
		remove_action( $current_hook['hook'], 'genesis_footer_widget_areas', $current_hook['priority'] );
	}
	
	if ( $hide_footer ) {
		remove_action( 'genesis_footer', 'genesis_footer_markup_open', 5 );
		remove_action( 'genesis_footer', 'genesis_do_footer' );
		remove_action( 'genesis_footer', 'genesis_footer_markup_close', 15 );
	}
	
}


/**
 * Show / hide the elements on the basis of user settings for mobile viewport
 *
 * @param none
 * @return none
 * @since 1.0
 */

function glps_mobile_show_hide_elements() {
	
	// Bail, if not a desktop viewport
	if( glps_is_notdevice() )
		return;
	
	$page_id = get_the_ID();
	
	if( get_option( 'show_on_front' ) == 'page' && is_home() ) {
		$page_id = get_option( 'page_for_posts' );
	}
	
	if( glps_is_woo_shop() && function_exists( 'wc_get_page_id' ) ) {
		$page_id = wc_get_page_id( 'shop' );
	}
	
	// Check the options set by the user
	
	$glps_settings_mobile = get_post_meta( $page_id, '_glps_lp_settings_mobile', true );
	
	$use_global = ( is_array( $glps_settings_mobile ) && array_key_exists( 'glps_global_settings', $glps_settings_mobile ) ) ? $glps_settings_mobile['glps_global_settings'] : true;
	
	if( $use_global ) {
		
		$hide_header = false;
		$hide_page_title = false;
		$hide_footer = false;
		
		$hide_breadcrumbs = glps_get_option( 'hide_breadcrumbs' );
		
		$hide_after_entry_widget = glps_get_option( 'hide_after_entry_widget' );
		
		$hide_footer_widgets = glps_get_option( 'hide_footer_widgets' );
		
	}
	else {
	
		if( !is_singular() && !glps_is_woo_shop() )
			return;
		
		$hide_header = isset( $glps_settings_mobile['glps-mobile-hide-header'] ) ? $glps_settings_mobile['glps-mobile-hide-header'] : false;

		$hide_breadcrumbs = isset( $glps_settings_mobile['glps-mobile-hide-breadcrumbs'] ) ? $glps_settings_mobile['glps-mobile-hide-breadcrumbs'] : false;

		$hide_page_title = isset( $glps_settings_mobile['glps-mobile-hide-title'] ) ? $glps_settings_mobile['glps-mobile-hide-title'] : false;

		$hide_after_entry_widget = isset( $glps_settings_mobile['glps-mobile-hide-after-entry-widget'] ) ? $glps_settings_mobile['glps-mobile-hide-after-entry-widget'] : false;

		$hide_footer_widgets = isset( $glps_settings_mobile['glps-mobile-hide-footer-widgets'] ) ? $glps_settings_mobile['glps-mobile-hide-footer-widgets'] : false;

		$hide_footer = isset( $glps_settings_mobile['glps-mobile-hide-footer'] ) ? $glps_settings_mobile['glps-mobile-hide-footer'] : false;
		
	}
	
	// Hide the elements based on the user settings
	
	if ( $hide_header ) {
		remove_action( 'genesis_header', 'genesis_header_markup_open', 5 );
		remove_action( 'genesis_header', 'genesis_header_markup_close', 15 );
		remove_action( 'genesis_header', 'genesis_do_header' );
	}
	
	if( $hide_breadcrumbs ) {
		$current_hook = glps_detect_cb_hook();
		remove_action( $current_hook['hook'], 'genesis_do_breadcrumbs', $current_hook['priority'] );
		// Remove WooCommerce breadcrumbs
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			if( is_woocommerce() || is_cart() || is_checkout() ) {
				$woo_breadcrumbs = glps_detect_cb_hook( 'woocommerce_breadcrumb' );
				remove_action( $woo_breadcrumbs['hook'], 'woocommerce_breadcrumb', $woo_breadcrumbs['priority'] );
			}
		}
	}
	
	if( $hide_page_title ) {
		add_filter( 'post_class', 'glps_hide_post_type_title' );
		// Remove WooCommerce page title
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			if( is_woocommerce() || is_cart() || is_checkout() ) {
				add_filter( 'woocommerce_show_page_title', '__return_false' );
			}
		}
	}
	
	if( $hide_footer_widgets ) {
		$current_hook = glps_detect_cb_hook( 'genesis_footer_widget_areas' );
		remove_action( $current_hook['hook'], 'genesis_footer_widget_areas', $current_hook['priority'] );
	}
	
	if ( $hide_footer ) {
		remove_action( 'genesis_footer', 'genesis_footer_markup_open', 5 );
		remove_action( 'genesis_footer', 'genesis_do_footer' );
		remove_action( 'genesis_footer', 'genesis_footer_markup_close', 15 );
	}
	
}