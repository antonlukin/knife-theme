<?php
/**
 * Adult content
 *
 * Add checkbox to determine posts with adult content
 *
 * @package knife-theme
 * @since 1.11
 * @version 1.17
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Knife_Adult_Content {
    /**
     * Post meta to store adult content option
     *
     * @access  public
     * @var     string
     */
    public static $meta_adult = '_knife-adult-content';

    /**
     * Default post type with adult content
     *
     * @access  public
     * @var     array
     */
    public static $post_type = array( 'post', 'club', 'quiz' );

    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Add option to mark adult content
        add_action( 'post_submitbox_misc_actions', array( __CLASS__, 'print_checkbox' ), 12 );

        // Update adult posts meta
        add_action( 'save_post', array( __CLASS__, 'save_meta' ) );
    }

    /**
     * Prints checkbox in post publish action section
     */
    public static function print_checkbox( $post ) {
        if ( ! in_array( $post->post_type, self::$post_type, true ) ) {
            return;
        }

        $adult = get_post_meta( $post->ID, self::$meta_adult, true );

        printf(
            '<div class="misc-pub-section"><label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label></div>',
            esc_attr( self::$meta_adult ),
            esc_html__( 'Содержимое для взрослых', 'knife-theme' ),
            checked( $adult, 1, false )
        );
    }

    /**
     * Save post meta
     */
    public static function save_meta( $post_id ) {
        if ( isset( $_POST['_inline_edit'] ) ) {
            $verify = wp_verify_nonce( sanitize_key( $_POST['_inline_edit'] ), 'inlineeditnonce' );

            if ( $verify ) {
                return;
            }
        }

        if ( ! in_array( get_post_type( $post_id ), self::$post_type, true ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( empty( $_REQUEST[ self::$meta_adult ] ) ) {
            return delete_post_meta( $post_id, self::$meta_adult );
        }

        return update_post_meta( $post_id, self::$meta_adult, 1 );
    }
}

/**
 * Load current module environment
 */
Knife_Adult_Content::load_module();
