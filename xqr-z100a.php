<?php
/**
 * WordPress XQR-Z100A
 *
 * @package   XQR-Z100A
 * @author    Barry Ceelen <b@rryceelen.com>
 * @license   GPL-2.0+
 * @link      https://github.com/barryceelen/wp-fitvids
 * @copyright 2015 Barry Ceelen
 *
 * @wordpress-plugin
 * Plugin Name:       XQR-Z100A
 * Plugin URI:        https://github.com/barryceelen/wp-xqr-z100a
 * Description:       Do not automatically attach unattached attachments when inserting them into the post content.
 * Version:           1.0.1
 * Author:            Barry Ceelen
 * Author URI:        https://github.com/barryceelen/
 * Text Domain:       xqr-z100a
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/barryceelen/wp-xqr-z100a
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

add_filter( 'wp_insert_post_parent', 'xqr_z100a_filter_insert_post_parent', 10, 2 );

function xqr_z100a_filter_insert_post_parent( $post_parent, $post_ID ) {

	if ( 'send-attachment-to-editor' != $_REQUEST['action'] ) {
		return $post_parent;
	}

	$post_before = get_post( $post_ID );

	if ( 0 != $post_before->post_parent ) {
		return $post_parent;
	}

	return 0;
}