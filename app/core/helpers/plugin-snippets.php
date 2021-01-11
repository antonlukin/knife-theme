<?php
/**
 * Snippets to foreign plugins
 *
 * Filters and actions same as functions.php file for plugins only
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.14
 */


/**
 * Set public post preview plugin link ttl
 *
 * @link https://wordpress.org/plugins/public-post-preview
 * @since 1.13
 */
add_filter('ppp_nonce_life', function() {
    return 60 * 60 * 24 * 7; // 7 days
});


/**
 * Hide settings for Social Planner plugin
 *
 * @since 1.14
 */
add_filter('social_planner_hide_settings', '__return_true');


/**
 * Hide Social Planner metabox from pages.
 *
 * @since 1.14
 */
add_filter('social_planner_post_types', function($post_types) {
    foreach($post_types as $i => $name) {
        if('page' === $name) {
            unset($post_types[$i]);
        }
    }

    return $post_types;
});


/**
 * Add promo hashtag to Social Planner message
 *
 * @since 1.14
 */
add_filter('social_planner_prepare_excerpt', function($excerpt, $message) {
    $post_id = $message['post_id'];

    if(!class_exists('Knife_Promo_Manager')) {
        return $excerpt;
    }

    $meta_promo = Knife_Promo_Manager::$meta_promo;

    if(get_post_meta($post_id, $meta_promo, true)) {
        $hashtag =  __('#партнерскийматериал', 'knife-theme');

        if (!empty($excerpt)) {
            $excerpt = $excerpt . "\n\n" . $hashtag;
        }
    }

    return $excerpt;
}, 10, 2);
