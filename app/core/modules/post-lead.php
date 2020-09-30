<?php
/**
 * Lead post meta
 *
 * Insert lead metabox to admin post screen
 *
 * @package knife-theme
 * @since 1.2
 * @version 1.14
 */


if (!defined('WPINC')) {
    die;
}


class Knife_Post_Lead {
   /**
     * Lead text post meta
     *
     * @access  public
     * @var     string
     */
    public static $meta_lead = '_knife-lead';


   /**
     * Default post type lead text availible
     *
     * @access  public
     * @var     array
     */
    public static $post_type = ['post', 'club', 'generator', 'quiz'];


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.3
     */
    public static function load_module() {
        // Save meta
        add_action('save_post', [__CLASS__, 'save_metabox']);

        // Add lead-text metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox']);

        // Prepend lead to feeds content
        add_filter('the_content_feed', [__CLASS__, 'upgrade_feeds'], 12);
    }


    /**
     * Add lead-text metabox
     */
    public static function add_metabox() {
        add_meta_box('knife-lead-metabox',
            __('Лид текст', 'knife-theme'),
            [__CLASS__, 'print_metabox'],
            self::$post_type, 'normal', 'high'
        );
    }


    /**
     * Print wp-editor based metabox for lead-text meta
     *
     * @link https://wordpress.stackexchange.com/a/292324/126253
     */
    public static function print_metabox($post, $box) {
        $lead = get_post_meta($post->ID, self::$meta_lead, true);

        wp_editor($lead, 'knife-lead-editor', [
            'media_buttons' => true,
            'textarea_name' => self::$meta_lead,
            'tinymce' => [
                'toolbar1' => 'link',
                'toolbar2' => ''
            ],
            'quicktags' => [
                'buttons' => 'link'
            ],
            'editor_height' => 100,
            'drag_drop_upload' => false
        ]);
    }


    /**
     * Get lead post meta
     */
    public static function get_lead($post = null, $lead = '') {
        $post = get_post($post);

        if(!empty($post->ID)) {
            // Get post lead
            $lead = get_post_meta($post->ID, self::$meta_lead, true);

            if(!empty($lead)) {
                $lead = wpautop($lead);
            }
        }

        return $lead;
    }


    /**
     * Prepend lead to feed content
     *
     * @since 1.7
     */
    public static function upgrade_feeds($content) {
        return self::get_lead() . $content;
    }


    /**
     * Save post options
     */
    public static function save_metabox($post_id) {
        if(!in_array(get_post_type($post_id), self::$post_type)) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save lead-text meta
        if(empty($_REQUEST[self::$meta_lead])) {
            return delete_post_meta($post_id, self::$meta_lead);
        }

        // Sanitize post lead meta
        $post_lead = apply_filters('content_save_pre', $_REQUEST[self::$meta_lead]);

        update_post_meta($post_id, self::$meta_lead, $post_lead);
    }
}


Knife_Post_Lead::load_module();
