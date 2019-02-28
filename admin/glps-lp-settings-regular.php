<?php

/**
 *
 * Genesis Landing Page Settings — Desktop viewport settings (All, but not mobile)
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
 * Adds the landing page settings metabox to supported post types
 * Users can enable/disable the supported post types on the plugin settings page
 *
 * @return none
 * @since 1.0
 */
 
add_action( 'add_meta_boxes', 'glps_settings_regular_vp' );

function glps_settings_regular_vp() {
	$supported_post_types = glps_enabled_post_types();
	
	if( empty( $supported_post_types ) )
		return;

	foreach( $supported_post_types as $glps_post_type ) {
		if( post_type_supports( $glps_post_type, 'glps-landing-page-settings' ) ) {
			add_meta_box( 'glps-lp-regular', sprintf( __( '%s for Regular Viewport', 'genesis-landing-page-settings' ), GLPS_PLUGIN_NAME ), 'glps_lp_settings_box', $glps_post_type, 'normal', 'default' );
		}
	}
}


/**
 * Builds the metabox for landing page settings - desktop
 *
 * @return none
 * @since 1.0
 */
 
function glps_lp_settings_box( $post ) {
	
	global $post, $typenow;
	
	$lp_settings_regular = get_post_meta( $post->ID, '_glps_lp_settings_regular', true );
	
	$hide_header = isset( $lp_settings_regular['glps-hide-header'] ) ? $lp_settings_regular['glps-hide-header'] : false;

	$hide_breadcrumbs = isset( $lp_settings_regular['glps-hide-breadcrumbs'] ) ? $lp_settings_regular['glps-hide-breadcrumbs'] : false;

	$hide_page_title = isset( $lp_settings_regular['glps-hide-title'] ) ? $lp_settings_regular['glps-hide-title'] : false;

	$hide_after_entry_widget = isset( $lp_settings_regular['glps-hide-after-entry-widget'] ) ? $lp_settings_regular['glps-hide-after-entry-widget'] : false;

	$hide_footer_widgets = isset( $lp_settings_regular['glps-hide-footer-widgets'] ) ? $lp_settings_regular['glps-hide-footer-widgets'] : false;

	$hide_footer = isset( $lp_settings_regular['glps-hide-footer'] ) ? $lp_settings_regular['glps-hide-footer'] : false;
	
	wp_nonce_field( 'glps_save_lp_regular', 'glps_lp_regular_nonce' );
	
	?>
	<p><?php printf( __( 'You can use these settings to show / hide the following elements for this %s. These settings will take effect on all viewports %sother than mobile devices%s.', 'genesis-landing-page-settings' ), $typenow, '<strong>', '</strong>' ); ?></p>
	
	<p><em><?php printf( __( '%sNote:%s You\'ll be able to configure these settings only for the elements that are enabled in your theme. Additional settings will keep on adding automatically for the elements as and when they\'re enabled in the theme.', 'genesis-landing-page-settings' ), '<strong>', '</strong>' ); ?></em></p>
	
	<table class="glps-settings-layout">
		<!-- Settings to hide header -->
		<tr>
			<td class="glps-label">
			<p>
				<label for="glps-hide-header"><?php _e( 'Hide Header', 'genesis-landing-page-settings' ) ?></label>
			</p>
			</td>
			
			<td class="glps-input">
			<p>
				<input type="checkbox" id="glps-hide-header" name="glps-hide-header" value="1" <?php checked( $hide_header, true ); ?> />
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
									<label for="glps-hide-breadcrumbs"><?php _e( 'Hide Breadcrumbs', 'genesis-landing-page-settings' ); ?></label>
								</p>
								</td>
								
								<td class="glps-input">
								<p>
									<input type="checkbox" id="glps-hide-breadcrumbs" name="glps-hide-breadcrumbs" value="1" <?php checked( $hide_breadcrumbs, true ); ?> />
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
										<label for="glps-hide-breadcrumbs"><?php _e( 'Hide Breadcrumbs', 'genesis-landing-page-settings' ); ?></label>
									</p>
									</td>
									
									<td class="glps-input">
									<p>
										<input type="checkbox" id="glps-hide-breadcrumbs" name="glps-hide-breadcrumbs" value="1" <?php checked( $hide_breadcrumbs, true ); ?> />
									</p>
									</td>
									<?php
								}
							} else {
								if( genesis_get_option( 'breadcrumb_page' ) == 1 ) {
									?>
									<td class="glps-label">
									<p>
										<label for="glps-hide-breadcrumbs"><?php _e( 'Hide Breadcrumbs', 'genesis-landing-page-settings' ); ?></label>
									</p>
									</td>
									
									<td class="glps-input">
									<p>
										<input type="checkbox" id="glps-hide-breadcrumbs" name="glps-hide-breadcrumbs" value="1" <?php checked( $hide_breadcrumbs, true ); ?> />
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
								<label for="glps-hide-breadcrumbs"><?php _e( 'Hide Breadcrumbs', 'genesis-landing-page-settings' ); ?></label>
							</p>
							</td>
							
							<td class="glps-input">
							<p>
								<input type="checkbox" id="glps-hide-breadcrumbs" name="glps-hide-breadcrumbs" value="1" <?php checked( $hide_breadcrumbs, true ); ?> />
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
							<label for="glps-hide-breadcrumbs"><?php _e( 'Hide Breadcrumbs', 'genesis-landing-page-settings' ); ?></label>
						</p>
						</td>
						
						<td class="glps-input">
						<p>
							<input type="checkbox" id="glps-hide-breadcrumbs" name="glps-hide-breadcrumbs" value="1" <?php checked( $hide_breadcrumbs, true ); ?> />
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
					<label for="glps-hide-title"><?php _e( 'Hide Page Title', 'genesis-landing-page-settings' ); ?></label>
				</p>
				</td>
				
				<td class="glps-input">
				<p>
					<input type="checkbox" id="glps-hide-title" name="glps-hide-title" value="1" <?php checked( $hide_page_title, true ); ?> />
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
					<label for="glps-hide-after-entry-widget"><?php _e( 'Hide After Entry Widgets', 'genesis-landing-page-settings' ); ?></label>
				</p>
				</td>
				
				<td class="glps-input">
				<p>
					<input type="checkbox" id="glps-hide-after-entry-widget" name="glps-hide-after-entry-widget" value="1" <?php checked( $hide_after_entry_widget, true ); ?> />
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
					<label for="glps-hide-footer-widgets"><?php _e( 'Hide Footer Widgets', 'genesis-landing-page-settings' ) ?></label>
				</p>
				</td>
				
				<td class="glps-input">
				<p>
					<input type="checkbox" id="glps-hide-footer-widgets" name="glps-hide-footer-widgets" value="1" <?php checked( $hide_footer_widgets, true ); ?> />
				</p>
				</td>
			</tr>
			<?php
		}
		?>
		
		<tr>
			<td class="glps-label">
			<p>
				<label for="glps-hide-footer"><?php _e( 'Hide Footer', 'genesis-landing-page-settings' ); ?></label>
			</p>
			</td>
			
			<td class="glps-input">
				<input type="checkbox" id="glps-hide-footer" name="glps-hide-footer" value="1" <?php checked( $hide_footer, true ); ?> />
			</td>
		</tr>
	</table>
	<?php

}


/**
 * Save the options set by the user for the post type
 */
 
add_action( 'save_post', 'glps_save_lp_settings_regular' );

function glps_save_lp_settings_regular( $post_id ) {
	
	// Check if our nonce is set.
	if ( !isset( $_POST['glps_lp_regular_nonce'] ) ) {
		return;
	}
	// Verify that the nonce is valid.
	if ( !wp_verify_nonce( $_POST['glps_lp_regular_nonce'], 'glps_save_lp_regular' ) ) {
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
	
	$glps_settings_regular = array();
	
	$glps_settings_regular['glps-hide-header'] = isset( $_POST['glps-hide-header'] ) ? true : false;
	$glps_settings_regular['glps-hide-breadcrumbs'] = isset( $_POST['glps-hide-breadcrumbs'] ) ? true : false;
	$glps_settings_regular['glps-hide-title'] = isset( $_POST['glps-hide-title'] ) ? true : false;
	$glps_settings_regular['glps-hide-after-entry-widget'] = isset( $_POST['glps-hide-after-entry-widget'] ) ? true : false;
	$glps_settings_regular['glps-hide-footer-widgets'] = isset( $_POST['glps-hide-footer-widgets'] ) ? true : false;
	$glps_settings_regular['glps-hide-footer'] = isset( $_POST['glps-hide-footer'] ) ? true : false;
	
	update_post_meta( $post_id, '_glps_lp_settings_regular', $glps_settings_regular );
	
}