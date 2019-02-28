<?php

/**
 *
 * Genesis Landing Page Settings — Mobile devices detection wrapper functions
 * Description: This file defines functions that can be used to detect the mobile device types, user is visiting the site on. Uses PHP Mobile Detect class to detect browser or device type
 *
 * @package Genesis Landing Page Settings
 * @author Shivanand Sharma
 * @since 1.0
 * 
 */

if( !class_exists( 'Mobile_Detect' ) ) {
	include_once( GLPS_PLUGIN_DIR . 'functions/mobile-detect.php' );
}

global $detect;
$detect = new Mobile_Detect();

/**
 * Returns true when on desktops or tablets
 */

function glps_is_notphone() {
	global $detect;
	if ( !$detect->isMobile() || $detect->isTablet() )
		return true;
}

/**
 * Returns true when on desktops or phones
 */

function glps_is_nottab() {
	global $detect;
	if ( !$detect->isTablet() )
		return true;
}

/**
 * Returns true when on desktops only
 */

function glps_is_notdevice() {
	global $detect;
	if ( !$detect->isMobile() && !$detect->isTablet() )
		return true;
}

/**
 * Returns true when on phones ONLY
 */

function glps_is_phone() {
	global $detect;
	if ( $detect->isMobile() && !$detect->isTablet() )
		return true;
}


/**
 * Returns true when on Tablets ONLY
 */

function glps_is_tablet() {
	global $detect;
	if ( $detect->isTablet() )
		return true;
}

/**
 * Returns true when on phones or tablets but NOT destkop
 */

function glps_is_device() {
	global $detect;
	if ( $detect->isMobile() || $detect->isTablet() )
		return true;
}

/**
 * Returns true when on iOS
 */

function glps_is_ios() {
	global $detect;
	if ( $detect->isiOS() )
		return true;
}

/**
 * Returns true when on iPhone
 */

function glps_is_iphone() {
	global $detect;
	if ( $detect->isiPhone() )
		return true;
}

/**
 * Returns true when on iPad
 */

function glps_is_ipad() {
	global $detect;
	if ( $detect->isiPad() )
		return true;
}

/**
 * Returns true when on Android OS
 */

function glps_is_android() {
	global $detect;
	if ( $detect->isAndroidOS() )
		return true;
}

/**
 * Returns true when on a Blackberry device
 */

function glps_is_blackberry() {
	global $detect;
	if ( $detect->isBlackBerry() )
		return true;
}

/**
 * Returns true when on Windows OS
 */

function glps_is_windows_mobile() {
	global $detect;
	if ( $detect->isWindowsMobileOS() )
		return true;
}

/**
 * Returns true when in a Chrome browser
 */

function glps_is_chrome_browser() {
	global $detect;
	if ( $detect->isChrome() )
		return true;
}

/**
 * Returns true when in a Opera browser
 */

function glps_is_opera_browser() {
	global $detect;
	if ( $detect->isOpera() )
		return true;
}

/**
 * Returns true when in a IE browser
 */

function glps_is_ie_browser() {
	global $detect;
	if ( $detect->isIE() )
		return true;
}

/**
 * Returns true when in a Firefox browser
 */

function glps_is_firefox_browser() {
	global $detect;
	if ( $detect->isFirefox() )
		return true;
}

/**
 * Returns true when in a Safari browser
 */

function glps_is_safari_browser() {
	global $detect;
	if ( $detect->isSafari() )
		return true;
}
