<?php

/**
 *
 * Genesis Landing Page Settings — Plugin Admin page
 * Description: This file builds the plugin settings page which allows users to enable / disable the plugin features
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
 * Hook the plugin settings menu to Genesis admin menu
 *
 * @return none
 * @since 1.0
 */

add_action( 'genesis_admin_menu', 'glps_plugin_settings_menu' );

function glps_plugin_settings_menu() {

	global $_child_theme_settings;
	$_child_theme_settings = new GLPS_Admin_Settings;

}


/**
 * Main plugin class that constructs the plugin settings page with all the settings
 *
 * @since 1.0
 */
 
class GLPS_Admin_Settings extends Genesis_Admin_Boxes {

	function __construct() {
		
		$page_id = GLPS_SETTINGS_FIELD;

		$menu_ops = array(
			'submenu' => array(
				'parent_slug' => __( 'genesis', 'genesis' ),
				'page_title' => GLPS_PLUGIN_NAME,
				'menu_title' => __( 'Landing Page Settings', 'genesis-landing-page-settings' ),
			) 
		);

		$page_ops = array(
			'save_button_text'  => __( 'Save Settings', 'genesis' ),
			'reset_button_text' => __( 'Reset Settings', 'genesis' ),
		);

		$settings_field = GLPS_SETTINGS_FIELD;

		$default_settings = glps_settings_defaults();

		$this->create( $page_id, $menu_ops, $page_ops, $settings_field, $default_settings );

		add_action( 'genesis_settings_sanitizer_init', array( $this, 'sanitizer_filters' ) );

		add_action( 'admin_print_styles', array( $this, 'styles' ) );
	
	}
	
	/* Load parent scripts as well as Genesis admin scripts */

	function scripts() {

		parent::scripts();
		genesis_load_admin_js();
		
	}
	
	/* Sanitize the page options for validation */

	function sanitizer_filters() {
		
		$glps_settings = get_option( 'genesis-landing-page-settings' );
		
		if( !empty( $glps_settings ) ) { 
			foreach( $glps_settings as $glps_settings => $value ) {
				genesis_add_option_filter( 'one_zero', $this->settings_field, array(
					$glps_settings,
				));
			}
		}
		
	}
	
	/* Enqueue the admin page stylesheet */
	
	function styles() {
		wp_enqueue_style( 'glps-admin-styles', GLPS_PLUGIN_URL . 'admin/css/glps-styles.css' );
	}
	
	function metaboxes() {
		
		add_meta_box( 'glps-post-types', __( 'Supported Post Types', 'genesis-landing-page-settings' ), array( $this, 'glps_post_types_settings_box' ), $this->pagehook, 'main' );
		
		add_meta_box( 'glps-mobile-global', __( 'Mobile Viewport Global Options', 'genesis-landing-page-settings' ), array( $this, 'glps_mobile_global_settings_box' ), $this->pagehook, 'main' );
		
	}
	
	function glps_post_types_settings_box() {
    
        $all_post_types = glps_get_public_post_types();
        
        // This is a weird catch, but it is always good to have it in place
        if( empty( $all_post_types ) ) {
            ?>
            <p><?php _e( 'This is certainly strange, but there does not seem to be any post types registered on your site or it may not be publicly queryable. You need to have post types registered on your site in order to be able to use the Landing Page Settings feature for post types.', 'genesis-landing-page-settings' ); ?></p>
            <?php
            return;
        }
        
        ?>
        <div class="glps-wrapper glps-settings-post-type">
            <table class="glps-inner">
            <tr>
                <td colspan="3">
                <p><?php _e( 'You can enable or disable the Landing Page Settings feature for the desired post types here. By default, this feature is available only for Pages and Posts.', 'genesis-landing-page-settings' ); ?></p>
				<p><em><?php _e( 'Custom post types registered by your theme or any plugin will automatically be added to this list, so that you can enable or disable Landing Page Settings feature for that post type.', 'genesis-landing-page-settings' ); ?></em></p>
                </td>
            </tr>
            
			<tr>
				<td class="glps-label">
				<p><?php _e( 'Enable Landing Page Settings feature on', 'genesis-landing-page-settings' ); ?></p>
				</td>
				
				<td class="glps-input">
				<p>
				<?php
				foreach( $all_post_types as $glps_post_type => $glps_post_type_obj ) {
					
					// Do not allow attachments
					if( $glps_post_type !== 'attachment' ) {
						?>
						<input id="<?php $this->field_id( 'glps_post_type_' . $glps_post_type ); ?>" type="checkbox" name="<?php $this->field_name( 'glps_post_type_' . $glps_post_type ); ?>" value="1" <?php checked( $this->get_field_value( 'glps_post_type_' . $glps_post_type ), true ); ?> />

						<label for="<?php $this->field_id( 'glps_post_type_' . $glps_post_type ); ?>"><?php printf( __( '%s', 'genesis-landing-page-settings' ), $glps_post_type_obj->labels->name ); ?></label>
						<br />
						<?php
					}

				}
				?>
				</p>
				</td>
			</tr>
            </table>
        </div>
        <?php
    
    }
	
	function glps_mobile_global_settings_box() {
		
		?>
		<p><?php _e( 'Use these settings to globally show / hide the following elements on the site for mobile visitors. These settings will take effect only when a user is visiting your site on mobile.', 'genesis-landing-page-settings' ); ?></p>
		<p><em><?php printf( __( '%sNote:%s You\'ll not be able to configure the settings for elements / features that are either not supported or disabled in your theme.', 'genesis-landing-page-settings' ), '<strong>', '</strong>' ); ?></em></p>
		
		<div class="glps-wrapper glps-mobile-global-settings">
		<table class="glps-inner">
		
			<!-- Hide breadcrumbs setting -->
			<?php
			
			if( genesis_get_option( 'breadcrumb_front_page' ) == 1 || genesis_get_option( 'breadcrumb_posts_page' ) == 1 || genesis_get_option( 'breadcrumb_home' ) == 1 || genesis_get_option( 'breadcrumb_single' ) == 1 || genesis_get_option( 'breadcrumb_page' ) == 1 ) {
				$disabled = '';
			}
			else {
				$disabled = 'disabled';
			}
			
			?>
			<tr>
				<td class="glps-label">
				<p>
					<label for="<?php $this->field_id( 'hide_breadcrumbs' ); ?>"><?php _e( 'Hide Breadcrumbs', 'genesis-landing-page-settings' ); ?></label>
				</p>
				</td>
				
				<td class="glps-input">
				<p>	
					<input type="checkbox" id="<?php $this->field_id( 'hide_breadcrumbs' ); ?>" name="<?php $this->field_name( 'hide_breadcrumbs' ); ?>" value="1" <?php checked( $this->get_field_value( 'hide_breadcrumbs' ), true ); echo $disabled; ?> />
				</p>
				</td>
			</tr>
			<?php
			
			// Hide After Entry Widget area
			
			global $wp_registered_sidebars;

			if( ( current_theme_supports( 'genesis-after-entry-widget-area' ) || isset( $wp_registered_sidebars['after-entry'] ) ) && is_active_sidebar( 'after-entry' ) ) {
				$disabled = '';
			}
			else {
				$disabled = 'disabled';
			}

			?>
			<tr>
				<td class="glps-label">
				<p>
					<label for="<?php $this->field_id( 'hide_after_entry_widget' ); ?>"><?php _e( 'Hide After Entry Widgets', 'genesis-landing-page-settings' ); ?></label>
				</p>
				</td>
				
				<td class="glps-input">
				<p>
					<input type="checkbox" id="<?php $this->field_id( 'hide_after_entry_widget' ); ?>" name="<?php $this->field_name( 'hide_after_entry_widget' ); ?>" value="1" <?php checked( $this->get_field_value( 'hide_after_entry_widget' ), true ); echo $disabled; ?> />
				</p>
				</td>
			</tr>
			<?php
			
			// Hide footer widgets setting
			
			if( current_theme_supports( 'genesis-footer-widgets' ) ) {
				$disabled = '';
			}
			else {
				$disabled = 'disabled';
			}
			
			?>
			<tr>
				<td class="glps-label">
				<p>
					<label for="<?php $this->field_id( 'hide_footer_widgets' ); ?>"><?php _e( 'Hide Footer Widgets', 'genesis-landing-page-settings' ); ?></label>
				</p>
				</td>
				
				<td class="glps-input">
				<p>
					<input type="checkbox" id="<?php $this->field_id( 'hide_footer_widgets' ); ?>" name="<?php $this->field_name( 'hide_footer_widgets' ); ?>" value="1" <?php checked( $this->get_field_value( 'hide_footer_widgets' ), true ); echo $disabled; ?> />
				</p>
				</td>
			</tr>
			
		</table>
		</div>
		<?php
		
	}
	
}