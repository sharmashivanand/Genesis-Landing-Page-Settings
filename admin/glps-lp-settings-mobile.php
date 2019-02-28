<?php

/**
 *
 * Genesis Landing Page Settings — Mobile viewport settings
 * Description: Builds the metabox on supported post types that allows users to show / hide elements on that post type
 
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
 * Adds the mobile landing page settings metabox to supported post types
 * Users can enable/disable the supported post types on the plugin settings page
 *
 * @return none
 * @since 1.0
 */

add_action( 'add_meta_boxes', 'glps_settings_mobile_vp' );

function glps_settings_mobile_vp() {
	$supported_post_types = glps_enabled_post_types();
	
	if( !$supported_post_types )
		return;

	foreach( $supported_post_types as $glps_post_type ) {
		if( post_type_supports( $glps_post_type, 'glps-mobile-landing-page-settings' ) ) {
			add_meta_box( 'glps-lp-mobile', sprintf( __( '%s for Mobile Viewport', 'genesis-landing-page-settings' ), GLPS_PLUGIN_NAME ), 'glps_mobile_lp_settings_box', $glps_post_type, 'normal', 'default' );
		}
	}
}


/**
 * Builds the metabox for landing page settings - desktop
 *
 * @return none
 * @since 1.0
 */
 
function glps_mobile_lp_settings_box( $post ) {
	
	global $post, $typenow;
	
	$lp_settings_mobile = get_post_meta( $post->ID, '_glps_lp_settings_mobile', true );
	
	$glps_use_global = isset( $lp_settings_mobile['glps_global_settings'] ) ? $lp_settings_mobile['glps_global_settings'] : true;
	
	$hide_header = isset( $lp_settings_mobile['glps-mobile-hide-header'] ) ? $lp_settings_mobile['glps-mobile-hide-header'] : false;

	$hide_breadcrumbs = isset( $lp_settings_mobile['glps-mobile-hide-breadcrumbs'] ) ? $lp_settings_mobile['glps-mobile-hide-breadcrumbs'] : false;

	$hide_page_title = isset( $lp_settings_mobile['glps-mobile-hide-title'] ) ? $lp_settings_mobile['glps-mobile-hide-title'] : false;

	$hide_after_entry_widget = isset( $lp_settings_mobile['glps-mobile-hide-after-entry-widget'] ) ? $lp_settings_mobile['glps-mobile-hide-after-entry-widget'] : false;
	
	$hide_footer_widgets = isset( $lp_settings_mobile['glps-mobile-hide-footer-widgets'] ) ? $lp_settings_mobile['glps-mobile-hide-footer-widgets'] : false;

	$hide_footer = isset( $lp_settings_mobile['glps-mobile-hide-footer'] ) ? $lp_settings_mobile['glps-mobile-hide-footer'] : false;
	
	wp_nonce_field( 'glps_save_lp_mobile', 'glps_lp_mobile_nonce' );
	
	?>
	<p><?php printf( __( 'You can use these settings to show / hide the following elements for this %s. The elements you hide using will only be hidden from the users visiting your site on mobile devices.', 'genesis-landing-page-settings' ), $typenow ); ?></p>
	<p><?php printf( __( 'By default this %s uses the %sglobal settings%s to show / hide the elements on this page for mobile viewport.', 'genesis-landing-page-settings' ), $typenow, '<a href="' . menu_page_url( GLPS_SETTINGS_FIELD, false ) . '">', '</a>' ); ?></p>
	
	<p><em><?php printf( __( '%sNote:%s You\'ll be able to configure these settings only for the elements that are enabled in your theme. Additional settings will keep on adding automatically for the elements as and when they\'re enabled in the theme.', 'genesis-landing-page-settings' ), '<strong>', '</strong>' ); ?></em></p>
	
	<table class="glps-settings-layout">
		<!-- Use mobile global setting -->
		<tr>
			<td class="glps-label">
			<p>
				<label for="glps_global_settings"><?php _e( 'Use Global Settings', 'genesis-landing-page-settings' ) ?></label>
			</p>
			</td>
			
			<td class="glps-input">
			<p>
				<input type="checkbox" id="glps_global_settings" name="glps_global_settings" value="1" <?php checked( $glps_use_global, true ); ?> />
			</p>
			</td>
		</tr>
	</table>
		
	<div id="glps-mobile-use-global">
	<table class="glps-settings-layout">
		<!-- Settings to hide header -->
		<tr>
			<td class="glps-label">
			<p>
				<label for="glps-mobile-hide-header"><?php _e( 'Hide Header', 'genesis-landing-page-settings' ) ?></label>
			</p>
			</td>
			
			<td class="glps-input">
			<p>
				<input type="checkbox" id="glps-mobile-hide-header" name="glps-mobile-hide-header" value="1" <?php checked( $hide_header, true ); ?> />
			</p>
			</td>
		</tr>	
		
		<!-- Settings to hide breadcrumbs, conditionally -->
		<?php
		if( function_exists( 'genesis_do_breadcrumbs' ) ) {
			?>
			<tr>
				<?php
				if ( $typenow == 'page' ) {
					if ( 'page' === get_option( 'show_on_front' ) ) {
						$front_page = get_option( 'page_on_front' );
						$blog_page  = get_option( 'page_for_posts' );
						if( $front_page == $post->ID ) {
							if( genesis_get_option( 'breadcrumb_front_page' ) == 1 ) {
								?>
								<td class="glps-label">
								<p>
									<label for="glps-mobile-hide-breadcrumbs"><?php _e( 'Hide Breadcrumbs', 'genesis-landing-page-settings' ); ?></label>
								</p>
								</td>
								
								<td class="glps-input">
								<p>
									<input type="checkbox" id="glps-mobile-hide-breadcrumbs" name="glps-mobile-hide-breadcrumbs" value="1" <?php checked( $hide_breadcrumbs, true ); ?> />
								</p>
								</td>
								<?php
							}
						} else {
							if( $blog_page == $post->ID ) {
								if( genesis_get_option( 'breadcrumb_posts_page' ) == 1 ) {
									?>
									<td class="glps-label">
									<p>
										<label for="glps-mobile-hide-breadcrumbs"><?php _e( 'Hide Breadcrumbs', 'genesis-landing-page-settings' ); ?></label>
									</p>
									</td>
									
									<td class="glps-input">
									<p>
										<input type="checkbox" id="glps-mobile-hide-breadcrumbs" name="glps-mobile-hide-breadcrumbs" value="1" <?php checked( $hide_breadcrumbs, true ); ?> />
									</p>
									</td>
									<?php
								}
							} else {
								if( genesis_get_option( 'breadcrumb_page' ) == 1 ) {
									?>
									<td class="glps-label">
									<p>
										<label for="glps-mobile-hide-breadcrumbs"><?php _e( 'Hide Breadcrumbs', 'genesis-landing-page-settings' ); ?></label>
									</p>
									</td>
									
									<td class="glps-input">
									<p>
										<input type="checkbox" id="glps-mobile-hide-breadcrumbs" name="glps-mobile-hide-breadcrumbs" value="1" <?php checked( $hide_breadcrumbs, true ); ?> />
									</p>
									</td>
									<?php
								}
							}
						}
					} else {
						if( genesis_get_option( 'breadcrumb_page' ) == 1 ) {
							?>
							<td class="glps-label">
							<p>
								<label for="glps-mobile-hide-breadcrumbs"><?php _e( 'Hide Breadcrumbs', 'genesis-landing-page-settings' ); ?></label>
							</p>
							</td>
							
							<td class="glps-input">
							<p>
								<input type="checkbox" id="glps-mobile-hide-breadcrumbs" name="glps-mobile-hide-breadcrumbs" value="1" <?php checked( $hide_breadcrumbs, true ); ?> />
							</p>
							</td>
							<?php
						}
					}
				} else {
					if( genesis_get_option( 'breadcrumb_single' ) == 1 ) {
						?>
						<td class="glps-label">
						<p>
							<label for="glps-mobile-hide-breadcrumbs"><?php _e( 'Hide Breadcrumbs', 'genesis-landing-page-settings' ); ?></label>
						</p>
						</td>
						
						<td class="glps-input">
						<p>
							<input type="checkbox" id="glps-mobile-hide-breadcrumbs" name="glps-mobile-hide-breadcrumbs" value="1" <?php checked( $hide_breadcrumbs, true ); ?> />
						</p>
						</td>
						<?php
					}
				}
				?>
			</tr>
			<?php
		}
				
		// Settings to hide page title
		
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		?>
			<tr>
				<td class="glps-label">
				<p>
					<label for="glps-mobile-hide-title"><?php _e( 'Hide Page Title', 'genesis-landing-page-settings' ); ?></label>
				</p>
				</td>
				
				<td class="glps-input">
				<p>
					<input type="checkbox" id="glps-mobile-hide-title" name="glps-mobile-hide-title" value="1" <?php checked( $hide_page_title, true ); ?> />
				</p>
				</td>
			</tr>
		<?php
		}
		
		// Settings to hide after entry widget area
				
		global $wp_registered_sidebars;

		if( ( current_theme_supports( 'genesis-after-entry-widget-area' ) || isset( $wp_registered_sidebars['after-entry'] ) ) && is_active_sidebar( 'after-entry' ) && $typenow == 'post' ) {
			?>
			<tr>
				<td class="glps-label">
				<p>
					<label for="glps-mobile-hide-after-entry-widget"><?php _e( 'Hide After Entry Widgets', 'genesis-landing-page-settings' ); ?></label>
				</p>
				</td>
				
				<td class="glps-input">
				<p>
					<input type="checkbox" id="glps-mobile-hide-after-entry-widget" name="glps-mobile-hide-after-entry-widget" value="1" <?php checked( $hide_after_entry_widget, true ); ?> />
				</p>
				</td>
			</tr>
			<?php
		}
		
		// Settings to hide footer widgets
		if( current_theme_supports( 'genesis-footer-widgets' ) ) {
			?>
			<tr>
				<td class="glps-label">
				<p>
					<label for="glps-mobile-hide-footer-widgets"><?php _e( 'Hide Footer Widgets', 'genesis-landing-page-settings' ) ?></label>
				</p>
				</td>
				
				<td class="glps-input">
				<p>
					<input type="checkbox" id="glps-mobile-hide-footer-widgets" name="glps-mobile-hide-footer-widgets" value="1" <?php checked( $hide_footer_widgets, true ); ?> />
				</p>
				</td>
			</tr>
			<?php
		}
		?>
		
		<tr>
			<td class="glps-label">
			<p>
				<label for="glps-mobile-hide-footer"><?php _e( 'Hide Footer', 'genesis-landing-page-settings' ); ?></label>
			</p>
			</td>
			
			<td class="glps-input">
				<input type="checkbox" id="glps-mobile-hide-footer" name="glps-mobile-hide-footer" value="1" <?php checked( $hide_footer, true ); ?> />
			</td>
		</tr>
	</table>
	</div>
	<?php

}


/**
 * Save the options set by the user for the post type
 */
 
add_action( 'save_post', 'glps_save_lp_settings_mobile' );

function glps_save_lp_settings_mobile( $post_id ) {
	
	// Check if our nonce is set.
	if ( !isset( $_POST['glps_lp_mobile_nonce'] ) ) {
		return;
	}
	// Verify that the nonce is valid.
	if ( !wp_verify_nonce( $_POST['glps_lp_mobile_nonce'], 'glps_save_lp_mobile' ) ) {
		return;
	}
	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} else {
		if ( !current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	/* It's safe for us to save the data now. */
	// Make sure that it is set
	
	$glps_settings_mobile = array();
	
	$glps_settings_mobile['glps_global_settings'] = isset( $_POST['glps_global_settings'] ) ? true : false;
	
	if( $glps_settings_mobile['glps_global_settings'] ) {
		
		$lp_settings_mobile = get_post_meta( $post_id, '_glps_lp_settings_mobile', true );
		
		$lp_settings_mobile['glps_global_settings'] = isset( $_POST['glps_global_settings'] ) ? true : false;
		
		update_post_meta( $post_id, '_glps_lp_settings_mobile', $lp_settings_mobile );
	
	}
	else {
	
		$glps_settings_mobile['glps_global_settings'] = isset( $_POST['glps_global_settings'] ) ? true : false;
		
		$glps_settings_mobile['glps-mobile-hide-header'] = isset( $_POST['glps-mobile-hide-header'] ) ? true : false;
		
		$glps_settings_mobile['glps-mobile-hide-breadcrumbs'] = isset( $_POST['glps-mobile-hide-breadcrumbs'] ) ? true : false;
		
		$glps_settings_mobile['glps-mobile-hide-title'] = isset( $_POST['glps-mobile-hide-title'] ) ? true : false;
		
		$glps_settings_mobile['glps-mobile-hide-after-entry-widget'] = isset( $_POST['glps-mobile-hide-after-entry-widget'] ) ? true : false;
		
		$glps_settings_mobile['glps-mobile-hide-footer-widgets'] = isset( $_POST['glps-mobile-hide-footer-widgets'] ) ? true : false;
		
		$glps_settings_mobile['glps-mobile-hide-footer'] = isset( $_POST['glps-mobile-hide-footer'] ) ? true : false;

		update_post_meta( $post_id, '_glps_lp_settings_mobile', $glps_settings_mobile );
		
	}
	
}