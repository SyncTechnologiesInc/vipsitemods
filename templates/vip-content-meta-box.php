<?php

// Function to render the meta box content
function render_vip_content_meta_box( $post ) {
  // Retrieve saved categories
  $selected_categories = get_post_meta( $post->ID, '_vip_content_categories', true );

  ?>
  <h2><?php _e( 'VIP Content Options', 'vip-content' ); ?></h2>

  <p>
    <label for="vip_content_categories"><?php _e( 'Apply VIP Content to Categories:', 'vip-content' ); ?></label>
    <ul id="vip_content_categories">
      <?php
      $all_categories = get_categories( array( 'hide_empty' => false ) );
      foreach ( $all_categories as $category ) : ?>
        <li>
          <label>
            <input type="checkbox" name="vip_content_categories[]" value="<?php echo esc_attr( $category->term_id ); ?>" <?php echo in_array( $category->term_id, (array) $selected_categories, true ) ? 'checked="checked"' : ''; ?>>
            <?php echo esc_html( $category->name ); ?>
          </label>
        </li>
      <?php endforeach; ?>
    </ul>
  </p>

  <input type="hidden" name="vip_content_meta_box_nonce" value="<?php echo wp_create_nonce( 'vip_content_meta_box_nonce' ); ?>">

  <script>
    // No need for Select2 here since using checkboxes
  </script>
  <?php
}

// Function to save the meta box data
function save_vip_content_meta_box( $post_id ) {
  // Check nonce and permissions
  if ( !isset( $_POST['vip_content_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['vip_content_meta_box_nonce'], 'vip_content_meta_box_nonce' ) ) {
    return;
  }

  if ( !current_user_can( 'edit_post', $post_id ) ) {
    return;
  }

  // Save the selected categories
  if ( isset( $_POST['vip_content_categories'] ) ) {
    update_post_meta( $post_id, '_vip_content_categories', $_POST['vip_content_categories'] );
  } else {
    delete_post_meta( $post_id, '_vip_content_categories' );
  }
}
add_action( 'save_post_vip_content', 'save_vip_content_meta_box' );

// Enqueue scripts and styles in the correct hook
add_action( 'admin_enqueue_scripts', 'enqueue_vip_content_scripts' );
function enqueue_vip_content_scripts() {
  // Removed unused Select2 scripts and styles
}
