<?php

function display_vip_content( $content ) {
  // Get current post categories
  $current_categories = wp_get_post_categories( get_the_ID() );

  // Get saved categories for VIP Content
  $vip_content_id = get_the_ID(); // Corrected: Use the current post ID
  $saved_categories = get_post_meta( $vip_content_id, '_vip_content_categories', true );

  // Check if any category matches or 'fallback' is selected
  if ( !empty( $saved_categories ) && ( array_intersect( $current_categories, $saved_categories ) || in_array( 'fallback', $saved_categories ) ) ) {
    // Get VIP Content post content
    $vip_content = get_post( $vip_content_id );
    $vip_content_content = $vip_content->post_content;

    // Corrected: Use the correct meta key for paragraph count
    $number_of_paragraphs = get_post_meta( $vip_content_id, '_vip_content_paragraphs_before', true );

    // Corrected: Split content into paragraphs and insert VIP Content at the specified position
    $paragraphs = explode( "\n\n", $content );
    if ( $number_of_paragraphs === '0' ) {
      $content = $vip_content_content . $content;
    } else {
      $new_content = implode( "\n\n", array_slice( $paragraphs, 0, $number_of_paragraphs ) ) . "\n\n" . $vip_content_content . "\n\n" . implode( "\n\n", array_slice( $paragraphs, $number_of_paragraphs ) );
      $content = $new_content;
    }
  }

  return $content;
}
