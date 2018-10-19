<?php
/**
* Embed filters
*
* Customize default wordpress embed code
*
* @package knife-theme
* @since 1.2
* @version 1.5
*/

if (!defined('WPINC')) {
    die;
}


class Knife_Embed_Filters {
    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.3
     */
    public static function load_module() {
        // Common filters
        add_filter('embed_defaults', [__CLASS__, 'set_defaults']);
        add_filter('embed_oembed_html', [__CLASS__, 'append_markup'], 12, 3);

        // Instagram update
        add_filter('oembed_providers', [__CLASS__, 'hide_instagram_caption']);
        add_filter('embed_oembed_html', [__CLASS__, 'move_instagram_script'], 10, 4);
        add_filter('script_loader_tag', [__CLASS__, 'update_instagram_loader'], 10, 3);

        // YouTube preloader
        add_filter('pre_oembed_result', [__CLASS__, 'update_youtube_embed'], 10, 2);
    }


    /**
     * Set default embed sizes
     *
     * @since 1.5
     */
    public static function set_defaults() {
        return ['width' => 640, 'height' => 525];
    }


    /**
     * Append figure wrapper to embeds
     *
     * @since 1.5
     */
    public static function append_markup($html, $url, $attr) {
        $html = '<figure class="figure figure--embed">' . $html . '</figure>';

        return $html;
    }


    /**
     * Remove instagram embeds caption
     */
    public static function hide_instagram_caption($providers) {
        $providers['#https?://(www\.)?instagr(\.am|am\.com)/p/.*#i'] = array('https://api.instagram.com/oembed?hidecaption=true', true);

        return $providers;
    }


    /**
     * Remove multiple js script from embeds and insert single with enqueue
     *
     * @since 1.5
     */
    public static function move_instagram_script($cache, $url, $attr, $post_id) {
        if(preg_match('#https?://(www\.)?instagr(\.am|am\.com)/p/.*#i', $url)) {
            wp_enqueue_script('instagram-embed', 'https://www.instagram.com/embed.js', [], null, true);

            $cache = str_replace('<script async src="//www.instagram.com/embed.js"></script>', '', $cache);
        }

        return $cache;
    }


    /**
     * Add async and defer atts to instagram loader tag
     */
    public static function update_instagram_loader($tag, $handle, $src) {
        if($handle === 'instagram-embed') {
            $tag = str_replace('<script', '<script async ', $tag);
        }

        return $tag;
    }


    /**
     * Update YouTube html to show preloader
     *
     * @since 1.5
     */
    public static function update_youtube_embed($result, $url) {
        if(preg_match('%(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
            $preview = "https://img.youtube.com/vi/{$match[1]}/default.jpg";

            foreach(['maxresdefault', 'hqdefault', 'mqdefault'] as $size) {
                $response = wp_remote_head("https://img.youtube.com/vi/{$match[1]}/{$size}.jpg");

                if(wp_remote_retrieve_response_code($response) === 200) {
                    $preview = "https://img.youtube.com/vi/{$match[1]}/{$size}.jpg";

                    break;
                }
            }

            $result = sprintf(
                '<a class="embed-youtube" href="%1$s" target="_blank" data-embed="%2$s" style="background-image: url(%3$s)"></a>',
                esc_url($url),
                esc_attr($match[1]),
                esc_url($preview)
            );
        }

        return $result;
    }
}


/**
 * Load current module environment
 */
Knife_Embed_Filters::load_module();
