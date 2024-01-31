<?php
/**
 * Plugin Name: VIP Site Mods
 * Description: This plugin adds custom marketing functions.
 * Version:     1.20240117085405
 * Author:      Michael McNew
 * Author URI:  https://www.visceralconcepts.com
 */

// Include the VIP Content post type class
require_once plugin_dir_path(__FILE__) . 'includes/vip-content-post-type.php';

// Include the VIP Content rendering functions
require_once plugin_dir_path(__FILE__) . 'includes/vip-content-render.php';

// Instantiate the VIP Content post type
$vip_content_post_type = new VIP_Content_Post_Type();

// Load plugin text domain
function vip_content_load_textdomain() {
    load_plugin_textdomain( 'vip-content', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'vip_content_load_textdomain' );

// Run activation/deactivation logic
register_activation_hook( __FILE__, array( $vip_content_post_type, 'activate' ) );
register_deactivation_hook( __FILE__, 'vip_content_deactivate' );

// Include the VIP Content meta box code
require_once plugin_dir_path(__FILE__) . 'templates/vip-content-meta-box.php';

// Call the function to display VIP Content
add_filter( 'the_content', 'display_vip_content' );

// Deactivation function to delete all VIP Content data
function vip_content_deactivate() {
    global $wpdb;

    // Delete all VIP Content posts
    $wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'vip_content'" );

    // Delete any associated post meta
    $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE post_id NOT IN (SELECT id FROM {$wpdb->posts})" );

    // Delete any associated terms
    $wpdb->query( "DELETE FROM {$wpdb->term_relationships} WHERE object_id NOT IN (SELECT id FROM {$wpdb->posts})" );
}
