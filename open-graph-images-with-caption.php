<?php
/**
Plugin Name: Open Graph Images With Caption
Version: 1.0.4
Description: Easy generate Open Graph images from the post title and featured image
Author: Dmitriy Glashkov
Author URI: https://glashkoff.com
Plugin URI: https://glashkoff.com/open-graph-images-with-caption
License: GPLv2 or later

@package     Open-Graph-Images-With-Caption
@author      Dmitriy Glashkov
@copyright   2018 Dmitriy Glashkov
@license     GPL-2.0+
@wordpress-plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ogi_upgrade_completed( $upgrader_object, $options ) {
	// Function for internal plugin update
	$our_plugin = plugin_basename( __FILE__ );
	if ( 'update' == $options['action'] && 'plugin' == $options['type'] && isset( $options['plugins'] ) ) {
		foreach ( $options['plugins'] as $plugin ) {
			if ( $plugin == $our_plugin ) {
				set_transient( 'ogi_updated', 1 );
			}
		}
	}
}
add_action( 'upgrader_process_complete', 'ogi_upgrade_completed', 10, 2 );

function ogi_plugin_updated() {
	//For future updates
	if ( get_transient( 'ogi_updated' ) ) {
		// If plugin updated, fix file names (because in the first version the files were called not optimal)
		foreach ( glob( wp_upload_dir()['basedir'] . '/social/*.jpg' ) as $ogi_file ) {
			$ogi_path = str_replace( '-1200x630.jpg', '-fb.jpg', $ogi_file );
			$ogi_path = str_replace( '-1024x512.jpg', '-tw.jpg', $ogi_path );
			rename( $ogi_file, $ogi_path );
		}
		delete_transient( 'ogi_updated' );
	}
	if ( '1.0.2' != get_option( 'ogi_plugin_version', '1.0.3' ) ) {
		update_option( 'ogi_plugin_version', '1.0.3' );
	}
}
add_action( 'admin_notices', 'ogi_plugin_updated' );

require plugin_dir_path( __FILE__ ) . '/inc/class-color.php';
require plugin_dir_path( __FILE__ ) . '/inc/settings.php';
require plugin_dir_path( __FILE__ ) . '/inc/metabox.php';
require plugin_dir_path( __FILE__ ) . '/inc/generation.php';
require plugin_dir_path( __FILE__ ) . '/inc/frontend.php';
