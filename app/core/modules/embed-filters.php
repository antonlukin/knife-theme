<?php
/**
 * Embed filters
 *
 * Customize default wordpress embed code
 *
 * @package knife-theme
 * @since 1.2
 * @version 1.14
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

        // Wrap all embeds
        add_filter('embed_oembed_html', [__CLASS__, 'append_markup'], 12, 3);

        // Fix Yandex.Music iframe size
        add_filter('embed_oembed_html', [__CLASS__, 'fix_yandex_embed'], 10, 3);

        // Instagram endpoint
        add_filter('oembed_providers', [__CLASS__, 'add_instagram_endpoint']);

        // Facebook endpoints
        add_filter('oembed_providers', [__CLASS__, 'add_facebook_endpoints']);

        // Twitter update
        add_filter('oembed_providers', [__CLASS__, 'hide_twitter_thread']);

        // Add Yandex.Music to whitelisted provider
        add_filter('oembed_providers', [__CLASS__, 'whitelist_yandex_music']);

        // YouTube preloader
        add_filter('pre_oembed_result', [__CLASS__, 'update_youtube_embed'], 10, 2);

        // Vimeo preloader
        add_filter('pre_oembed_result', [__CLASS__, 'update_vimeo_embed'], 10, 2);

        // Replace custom video preloaders in feeds
        add_filter('the_content_feed', [__CLASS__, 'replace_feed_embeds'], 5);

        // Define embed settings if still not
        if(!defined('KNIFE_EMBEDS')) {
            define('KNIFE_EMBEDS', []);
        }
    }


    /**
     * Set default embed sizes
     *
     * @since 1.5
     */
    public static function set_defaults($defaults) {
        $defaults = [
            'width' => 640,
            'height' => 525
        ];

        return $defaults;
    }


    /**
     * Append figure wrapper to embeds
     *
     * @since 1.5
     */
    public static function append_markup($html, $url, $attr) {
        if(!is_feed()) {
            $html = '<figure class="figure figure--embed">' . $html . '</figure>';
        }

        return $html;
    }


    /**
     * Add Yandex.Music to whitelisted provider
     *
     * @link https://github.com/WordPress/WordPress/blob/8e9c4a116a1644f4a80f16c1d255fbb6140641a2/wp-includes/class-wp-oembed.php#L132
     * @since 1.11
     */
    public static function whitelist_yandex_music($providers) {
        $providers['#https?://music.yandex.ru.*#i'] = ['', true];

        return $providers;
    }


    /**
     * Add instagram endpoints with access token
     *
     * @since 1.14
     */
    public static function add_instagram_endpoint($providers) {

        if(empty(KNIFE_EMBEDS['facebook_token'])) {
            return $providers;
        }

        // Create endpoint with access token
        $endpoint = 'https://graph.facebook.com/v8.0/instagram_oembed?access_token=' . KNIFE_EMBEDS['facebook_token'];

        // Add new endpoint
        $providers['#https?://(www\.)?instagr(\.am|am\.com)/(p|tv)/.*#i'] = [$endpoint . '&hidecaption=true', true];

        return $providers;
    }


    /**
     * Add facebook endpoints with access token
     *
     * @since 1.14
     */
    public static function add_facebook_endpoints($providers) {
        if(empty(KNIFE_EMBEDS['facebook_token'])) {
            return $providers;
        }

        // Create endpoints with access token
        $endpoint_post = 'https://graph.facebook.com/v8.0/oembed_post?access_token=' . KNIFE_EMBEDS['facebook_token'];
        $endpoint_video = 'https://graph.facebook.com/v8.0/oembed_video?access_token=' . KNIFE_EMBEDS['facebook_token'];

        // Add post endoints
        $providers['#https?://www\.facebook\.com/.*/posts/.*#i'] = [$endpoint_post, true];
        $providers['#https?://www\.facebook\.com/.*/activity/.*#i'] = [$endpoint_post, true];
        $providers['#https?://www\.facebook\.com/.*/photos/.*#i'] = [$endpoint_post, true];
        $providers['#https?://www\.facebook\.com/photo(s/|\.php).*#i'] = [$endpoint_post, true];
        $providers['#https?://www\.facebook\.com/permalink\.php.*#i'] = [$endpoint_post, true];
        $providers['#https?://www\.facebook\.com/media/.*#i'] = [$endpoint_post, true];
        $providers['#https?://www\.facebook\.com/questions/.*#i'] = [$endpoint_post, true];
        $providers['#https?://www\.facebook\.com/notes/.*#i'] = [$endpoint_post, true];

        // Add video endpoints
        $providers['#https?://www\.facebook\.com/.*/videos/.*#i'] = [$endpoint_video, true];
        $providers['#https?://www\.facebook\.com/video\.php.*#i'] = [$endpoint_video, true];
        $providers['#https?://www\.facebook\.com/watch/?\?v=\d+#i'] = [$endpoint_video, true];

        return $providers;
    }


    /**
     * Hide twitter parent tweet
     *
     * @version 1.9
     */
    public static function hide_twitter_thread($providers) {
        $providers['#https?://(www\.)?twitter\.com/\w{1,15}/status(es)?/.*#i'] = ['https://publish.twitter.com/oembed?hide_thread=true', true];

        return $providers;
    }


    /**
     * Update Yandex.Music iframe sizes
     *
     * @since 1.11
     */
    public static function fix_yandex_embed($html, $url, $attr) {
        if(preg_match('#https?://music.yandex.ru.*#i', $url)) {
            // Find iframe src and height
            preg_match('~\s+src=["\'](.*?)["\'].+?\s+height=["\'](.*?)["\']~i', $html, $match);

            if(isset($match[1], $match[2])) {
                $height = min($match[2], $attr['height']);;

                // Make style attr
                $styles = "border:0; width:100%; height:{$height}px;";

                // Create new iframe
                $html = sprintf(
                    '<iframe src="%s" style="%s"></iframe>', $match[1], $styles
                );
            }
        }

        return $html;
    }


    /**
     * Update YouTube html to show preloader
     *
     * @since 1.5
     */
    public static function update_youtube_embed($result, $url) {
        if(preg_match('#(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})#i', $url, $match)) {
            $preview = "https://img.youtube.com/vi/{$match[1]}/default.jpg";

            foreach(['maxresdefault', 'hqdefault', 'mqdefault'] as $size) {
                $response = wp_remote_head("https://img.youtube.com/vi/{$match[1]}/{$size}.jpg");

                if(wp_remote_retrieve_response_code($response) === 200) {
                    $preview = "https://img.youtube.com/vi/{$match[1]}/{$size}.jpg";

                    break;
                }
            }

            $params = [
                'rel' => 0,
                'showinfo' => 0,
                'autoplay' => 1
            ];

            wp_parse_str(wp_parse_url($url, PHP_URL_QUERY), $query);

            if(isset($query['t'])) {
                $params['start'] = $query['t'];
            }

            $result = sprintf(
                '<div class="embed embed--youtube" data-embed="%1$s">%2$s</div>',
                sprintf(
                    'https://www.youtube.com/embed/%1$s?%2$s',
                    esc_attr($match[1]),
                    build_query($params)
                ),
                sprintf(
                    '<a class="embed__dummy" href="%1$s" target="_blank" style="background-image: url(%2$s)"></a>',
                    esc_url($url),
                    esc_url($preview)
                )
            );
        }

        return $result;
    }


    /**
     * Update Vimeo html to show preloader
     *
     * @since 1.5
     */
    public static function update_vimeo_embed($result, $url) {
        if(preg_match('#https?://(?:.+\.)?vimeo\.com/(?:video/)?([\d]+).*#i', $url, $match)) {
            $request = wp_remote_get("https://vimeo.com/api/v2/video/{$match[1]}.json");
            $preview = '';

            if(!is_wp_error($request)) {
                $meta = json_decode(wp_remote_retrieve_body($request));

                foreach(['thumbnail_large', 'thumbnail_medium', 'thumbnail_small'] as $size) {
                    if(isset($meta[0]->$size)) {
                        $preview = $meta[0]->$size;

                        break;
                    }
                }
            }

            $result = sprintf(
                '<div class="embed embed--vimeo" data-embed="%1$s">%2$s</div>',
                sprintf(
                    'https://player.vimeo.com/video/%s?byline=0&autoplay=1',
                    esc_attr($match[1])
                ),
                sprintf(
                    '<a class="embed__dummy" href="%1$s" target="_blank" style="background-image: url(%2$s)"></a>',
                    esc_url($url),
                    esc_url($preview)
                )
            );
        }

        return $result;
    }


    /**
     * Replace custom video preloaders in feeds
     *
     * @since 1.5
     */
    public static function replace_feed_embeds($content) {
        $content = preg_replace(
            '~<div class="embed\s.+?"\s+data-embed="([^"]+)".+?</div>~is',
            '<iframe src="$1" frameborder="0"></iframe>',
            $content
        );

        return $content;
    }

}


/**
 * Load current module environment
 */
Knife_Embed_Filters::load_module();
