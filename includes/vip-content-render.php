<?php

function display_vip_content( $content ) {

  try {

    // 1. Check for VIP Content settings
    $vip_content_settings = get_post_meta( get_the_ID(), '_vip_content_settings', true );
    if ( empty( $vip_content_settings ) ) {
      // Return content without injection if no settings are defined
      return $content;
    }

    // 2. Get current post categories (corrected to retrieve term IDs directly)
    $currentCategoryIDs = array_map( 'intval', wp_get_post_categories( get_the_ID(), array( 'fields' => 'ids' ) ) );

    // 3. Obtain raw post content without filters
    $raw_content = get_post_field( 'post_content', get_the_ID(), 'raw' );

    // 4. Inject VIP Content based on category matching
    $vip_content_injected = false;

    // Get VIP Content post (corrected)
    $vip_content = get_post( get_the_ID(), ARRAY_A, 'vip_content' );
    if ( $vip_content ) {
      $vip_content_content = $vip_content->post_content;

      $saved_categories = isset( $vip_content_settings['categories'] ) ? $vip_content_settings['categories'] : []; // Use empty array as default
      $number_of_paragraphs = isset( $vip_content_settings['paragraphs_before'] ) ? intval( $vip_content_settings['paragraphs_before'] ) : 0;

      // Inject only if categories match or are empty, and content hasn't been injected yet
      if ((count(array_intersect($saved_categories, $currentCategoryIDs)) > 0 || empty($saved_categories)) && !$vip_content_injected) {
        $vip_content_injected = true; // Mark that content has been injected

        // Proceed with injection
        $paragraphs = explode('<p>', $raw_content); // Explode content based on paragraphs
        $injectionPoint = max(1, $number_of_paragraphs); // Ensure minimum injection point of 1
        if (count($paragraphs) >= $injectionPoint) {
          $paragraphs[$injectionPoint - 1] .= '<div class="vip-content">' . $vip_content_content . '</div>'; // Append to the specified paragraph
          $blog_content = implode('<p>', $paragraphs); // Reconstruct content
        } else {
          // Inject at the beginning if not enough paragraphs
          $blog_content = '<div class="vip-content">' . $vip_content_content . '</div>' . $raw_content;
        }
      }
    }

    // Output the modified content (injected content or original content if no match)
    return $vip_content_injected ? $blog_content : $content;

  } catch (Exception $e) {
    // Display an admin error notice
    add_action('admin_notices', function () use ($e) {
      printf('<div class="notice notice-error"><p>%s</p></div>', esc_html($e->getMessage()));
    });
    // Optionally, log the error for further debugging
    error_log($e->getMessage());

    // Return original content in case of errors
    return $content;
  }
}
