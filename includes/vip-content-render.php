<?php

function display_vip_content( $content ) {

  // Get current post ID and categories
  $post_id = get_the_ID();
  $current_categories = wp_get_post_categories( $post_id );

  // Get VIP Content settings (corrected)
  $vip_content_settings = get_post_meta( $post_id, '_vip_content_settings', true ); // Retrieve from the correct post
  $saved_categories = isset( $vip_content_settings['categories'] ) ? $vip_content_settings['categories'] : array();
  $number_of_paragraphs = isset( $vip_content_settings['paragraphs_before'] ) ? intval( $vip_content_settings['paragraphs_before'] ) : 0;

  // Check category and fallback conditions
  if ( !empty( $saved_categories ) && ( array_intersect( $current_categories, $saved_categories ) || in_array( 'fallback', $saved_categories ) ) ) {

    // Get VIP Content post (corrected)
    $vip_content = get_post( get_the_ID(), ARRAY_A, 'vip_content' ); // Retrieve the VIP Content for the current post
    if ( $vip_content ) {
      $vip_content_content = $vip_content->post_content;

      // Split content into paragraphs
      $paragraphs = explode( '</p>', $content );

      // Insert VIP Content at the specified position
      if ($number_of_paragraphs > 0) {
        array_splice($paragraphs, $number_of_paragraphs - 1, 0, $vip_content_content); // Insert before the desired paragraph
      } else {
        $paragraphs = array_merge(array($vip_content_content), $paragraphs); // Insert at the beginning
      }

      $content = implode('</p>', $paragraphs);
    }
  }

  return $content;
}
