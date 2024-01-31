<?php

class VIP_Content_Post_Type {

    public function __construct() {
        add_action( 'init', array( $this, 'register_post_type' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        // Corrected: Use the correct hook for saving meta box data
        add_action( 'save_post_vip_content', array( $this, 'save_meta_box_data' ) );
    }

    public function register_post_type() {
        $labels = array(
            'name' => _x( 'VIP Content', 'Post type general name', 'vip-content' ),
            'singular_name' => _x( 'VIP Content', 'Post type singular name', 'vip-content' ),
            // ... other labels
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => false,
            'menu_icon' => 'dashicons-id-alt', // Corrected icon
            'supports' => array( 'title', 'editor', 'categories' ),
            // ... other arguments
        );

        register_post_type( 'vip_content', $args );
    }

    public function add_meta_boxes() {
        add_meta_box(
            'vip_content_options',
            __( 'VIP Content Options', 'vip-content' ),
            array( $this, 'render_meta_box' ),
            'vip_content',
            'normal',
            'high'
        );
    }

    public function render_meta_box( $post ) {
        ob_start();
        ?>
        <h2><?php _e( 'VIP Content Options', 'vip-content' ); ?></h2>

        <p>
            <label for="vip_content_paragraphs_before"><?php _e( 'Number of Paragraphs Before VIP Content:', 'vip-content' ); ?></label>
            <input type="number" min="1" id="vip_content_paragraphs_before" name="vip_content_paragraphs_before" value="<?php echo esc_attr( get_post_meta( $post->ID, '_vip_content_paragraphs_before', true ) ?: 3 ); ?>">
        </p>

        <p>
            <label for="vip_content_categories"><?php _e( 'Select Categories for VIP Content:', 'vip-content' ); ?></label>
            <ul id="vip_content_categories">
                <?php
                $categories = get_categories( array( 'hide_empty' => false ) );

                if ( $categories ) {
                    foreach ( $categories as $category ) {
                        $selected_categories = get_post_meta( $post->ID, '_vip_content_categories', true );
                        $selected_categories = !is_array( $selected_categories ) ? array() : $selected_categories; // Ensure it's an array
                        $checked = in_array( $category->term_id, $selected_categories, true ) ? 'checked="checked"' : '';
                        echo '<li><label><input type="checkbox" name="vip_content_categories[]" value="' . $category->term_id . '"' . $checked . '> ' . $category->name . '</label></li>';
                    }
                } else {
                    echo '<li>' . __( 'No categories found.', 'vip-content' ) . '</li>';
                }
                ?>
            </ul>
        </p>

        <?php wp_nonce_field( 'vip_content_meta_box_nonce', 'vip_content_meta_box_nonce' ); ?>

        <?php echo ob_get_clean();
    }

    public function activate() {
        // Add any necessary activation tasks here
    }

    public function save_meta_box_data( $post_id ) {
        // Check nonce and permissions
        if ( !isset( $_POST['vip_content_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['vip_content_meta_box_nonce'], 'vip_content_meta_box_nonce' ) ) {
            return;
        }

        if ( !current_user_can( 'edit_post', $post_id ) ) {
           return;
       }

       // Save the selected number of paragraphs (unchanged)
       if ( isset( $_POST['vip_content_paragraphs_before'] ) && intval( $_POST['vip_content_paragraphs_before'] ) > 0 ) {
           update_post_meta( $post_id, '_vip_content_paragraphs_before', intval( $_POST['vip_content_paragraphs_before'] ) );
       } else {
           delete_post_meta( $post_id, '_vip_content_paragraphs_before' );
       }

       // Save the selected categories (corrected)
       if ( isset( $_POST['vip_content_categories'] ) && is_array( $_POST['vip_content_categories'] ) ) {
            $category_ids = array_map( 'intval', $_POST['vip_content_categories'] ); // Sanitize IDs
            update_post_meta( $post_id, '_vip_content_categories', $category_ids ); // Store as post meta
        } else {
            delete_post_meta( $post_id, '_vip_content_categories' ); // Clear meta if none selected
        }
   }
}