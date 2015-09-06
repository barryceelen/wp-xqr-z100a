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
 * Version:           1.0.0
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

remove_action( 'wp_ajax_send-attachment-to-editor', 'wp_ajax_send_attachment_to_editor', 1 );
add_action( 'wp_ajax_send-attachment-to-editor', 'xqr_z100a_ajax_send_attachment_to_editor', 1 );

/**
 * Ajax handler for sending an attachment to the editor.
 *
 * A copy of the core wp_ajax_send_attachment_to_editor function,
 * stripped of its automatically attaching unattached attachments superpowers.
 *
 * @see wp_ajax_send_attachment_to_editor()
 *
 * @since 1.0.0
 */
function xqr_z100a_ajax_send_attachment_to_editor() {
	check_ajax_referer( 'media-send-to-editor', 'nonce' );

	$attachment = wp_unslash( $_POST['attachment'] );

	$id = intval( $attachment['id'] );

	if ( ! $post = get_post( $id ) )
		wp_send_json_error();

	if ( 'attachment' != $post->post_type )
		wp_send_json_error();

	/** Our little pièce de résistance, commented out for all to see: */

	// if ( current_user_can( 'edit_post', $id ) ) {
	// 	// If this attachment is unattached, attach it. Primarily a back compat thing.
	// 	if ( 0 == $post->post_parent && $insert_into_post_id = intval( $_POST['post_id'] ) ) {
	// 		wp_update_post( array( 'ID' => $id, 'post_parent' => $insert_into_post_id ) );
	// 	}
	// }

	$rel = $url = '';
	$html = isset( $attachment['post_title'] ) ? $attachment['post_title'] : '';
	if ( ! empty( $attachment['url'] ) ) {
		$url = $attachment['url'];
		if ( strpos( $url, 'attachment_id') || get_attachment_link( $id ) == $url )
			$rel = ' rel="attachment wp-att-' . $id . '"';
		$html = '<a href="' . esc_url( $url ) . '"' . $rel . '>' . $html . '</a>';
	}

	remove_filter( 'media_send_to_editor', 'image_media_send_to_editor' );

	if ( 'image' === substr( $post->post_mime_type, 0, 5 ) ) {
		$align = isset( $attachment['align'] ) ? $attachment['align'] : 'none';
		$size = isset( $attachment['image-size'] ) ? $attachment['image-size'] : 'medium';
		$alt = isset( $attachment['image_alt'] ) ? $attachment['image_alt'] : '';

		// No whitespace-only captions.
		$caption = isset( $attachment['post_excerpt'] ) ? $attachment['post_excerpt'] : '';
		if ( '' === trim( $caption ) ) {
			$caption = '';
		}

		$title = ''; // We no longer insert title tags into <img> tags, as they are redundant.
		$html = get_image_send_to_editor( $id, $caption, $title, $align, $url, (bool) $rel, $size, $alt );
	} elseif ( wp_attachment_is( 'video', $post ) || wp_attachment_is( 'audio', $post )  ) {
		$html = stripslashes_deep( $_POST['html'] );
	}

	/** This filter is documented in wp-admin/includes/media.php */
	$html = apply_filters( 'media_send_to_editor', $html, $id, $attachment );

	wp_send_json_success( $html );
}