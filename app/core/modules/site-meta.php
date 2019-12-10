<?php
/**
 * Add custom site header meta and footer description
 *
 * @package knife-theme
 * @since 1.5
 * @version 1.11
 */


if (!defined('WPINC')) {
    die;
}


class Knife_Site_Meta {
    /**
     * Option to store footer description
     *
     * @access  private
     * @var     string
     */
    private static $footer_description = 'footer-description';


    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        add_action('wp_head', [__CLASS__, 'add_manifest'], 4);
        add_action('wp_head', [__CLASS__, 'add_seo_tags'], 4);

        add_action('wp_head', [__CLASS__, 'add_og_tags'], 5);
        add_action('wp_head', [__CLASS__, 'add_twitter_tags'], 5);
        add_action('wp_head', [__CLASS__, 'add_facebook_tags'], 5);
        add_action('wp_head', [__CLASS__, 'add_telegram_tags'], 5);
        add_action('wp_head', [__CLASS__, 'add_yandex_meta'], 5);

        // Add custom theme lang attributes
        add_filter('language_attributes', [__CLASS__, 'add_xmlns']);

        // Add footer description field to customizer
        add_action('customize_register', [__CLASS__, 'update_customize_settings']);

        // Remove comments feed link
        // For the reason that we don't use comments in this theme we have to remove comments feed link from header
        add_filter('feed_links_show_comments_feed', '__return_false');

        // Add google tagmanager script
        add_action('wp_head', [__CLASS__, 'add_tagmanager'], 20);
    }


    /**
     * Add tagmanager script to header
     */
    public static function add_tagmanager() {
        if(defined('WP_DEBUG') && WP_DEBUG === false) {
            $tagmanager_id = 'GTM-KZ7MHM';

            $include = get_template_directory() . '/core/include';
            include_once($include . '/templates/tagmanager-script.php');
        }
    }


    /**
     * Footer description option
     */
    public static function update_customize_settings($wp_customize) {
        $wp_customize->add_setting(self::$footer_description);

        $wp_customize->add_section('knife_footer', [
            'title' => __('Подвал сайта','knife-theme'),
            'priority' => 160,
        ]);

        $wp_customize->add_control(new WP_Customize_Code_Editor_Control($wp_customize,
            self::$footer_description, [
                 'label' => __('Описание в подвале', 'knife-theme'),
                 'section' => 'knife_footer',
                 'code_type' => 'text/html',
                 'priority' => 10
             ]
        ));

        // Remove site icon controls from admin customizer
        $wp_customize->remove_control('site_icon');
    }


    /**
     * Add og xmlns
     */
    public static function add_xmlns($output) {
        return 'prefix="og: http://ogp.me/ns#" ' . $output;
    }


    /**
     * Add manifest and header icons
     *
     * @since 1.11
     */
    public static function add_manifest() {
        $meta = [];

        $meta[] = sprintf(
            '<link rel="manifest" href="%s" crossorigin="use-credentials">',
            esc_url(home_url('/manifest.json'))
        );

        $meta[] = sprintf(
            '<link rel="shortcut icon" href="%s" crossorigin="use-credentials">',
            esc_url(home_url('/favicon.ico'))
        );

        $meta[] = sprintf(
            '<link rel="icon" type="image/png" sizes="32x32" href="%s">',
            esc_url(home_url('/icon-32.png'))
        );

        $meta[] = sprintf(
            '<link rel="icon" type="image/png" sizes="192x192" href="%s">',
            esc_url(home_url('/icon-192.png'))
        );

        $meta[] = sprintf(
            '<link rel="apple-touch-icon" sizes="180x180" href="%s">',
            esc_url(home_url('/icon-180.png'))
        );

        return self::print_tags($meta);
    }


    /**
     * Add seo tags
     */
    public static function add_seo_tags() {
        $meta = [];

        // Get description
        $description = self::get_description();

        $meta[] = sprintf(
            '<meta name="description" content="%s">',
            esc_attr($description)
        );

        return self::print_tags($meta);
    }


    /**
     * Add yandex browser meta
     *
     * @link https://tech.yandex.ru/browser/tableau/doc/dg/concepts/about-docpage/
     */
    public static function add_yandex_meta() {
        $meta = [];

        // Get assets path
        $path = get_template_directory_uri() . '/assets/images';

        $meta[] = sprintf(
            '<meta name="yandex-tableau-widget" content="logo=%s, color=#002349">',
            esc_url($path . '/logo-feature.png')
        );

        return self::print_tags($meta);
    }


    /**
     * Add og tags
     *
     * @link https://developers.facebook.com/docs/sharing/webmasters
     */
    public static function add_og_tags() {
        $meta = [];

        // Get description
        $description = self::get_description();

        $meta[] = sprintf(
            '<meta property="og:site_name" content="%s">',
            esc_attr(get_bloginfo('name'))
        );

        $meta[] = sprintf(
             '<meta property="og:locale" content="%s">',
             esc_attr(get_locale())
        );

        $meta[] = sprintf(
            '<meta property="og:description" content="%s">',
            esc_attr($description)
        );

        if(method_exists('Knife_Snippet_Image', 'get_social_image')) {
            // Get social image array
            $social_image = Knife_Snippet_Image::get_social_image();

            $meta[] = sprintf(
                '<meta property="og:image" content="%s">',
                esc_attr($social_image[0])
            );

            $meta[] = sprintf(
                '<meta property="og:image:width" content="%s">',
                esc_attr($social_image[1])
            );

            $meta[] = sprintf(
                '<meta property="og:image:height" content="%s">',
                esc_attr($social_image[2])
            );
        }

        if(is_post_type_archive()) {
            $meta[] = sprintf(
                '<meta property="og:url" content="%s">',
                esc_url(get_post_type_archive_link(get_post_type()))
            );
        }

        if(is_tax() || is_category() || is_tag()) {
            $meta[] = sprintf(
                '<meta property="og:url" content="%s">',
                esc_url(get_term_link(get_queried_object()->term_id))
            );
        }

        if(is_front_page()) {
            $meta[] = sprintf(
                '<meta property="og:url" content="%s">',
                esc_url(home_url('/'))
            );

            $meta[] = sprintf(
                '<meta property="og:title" content="%s">',
                esc_attr(get_bloginfo('title'))
            );
        }

        if(is_singular() && !is_front_page()) {
            $object_id = get_queried_object_id();

            array_push($meta, '<meta property="og:type" content="article">');

            $meta[] = sprintf(
                '<meta property="og:url" content="%s">',
                esc_url(get_permalink($object_id))
            );

            $meta[] = sprintf(
                '<meta property="og:title" content="%s">',
                esc_attr(strip_tags(get_the_title($object_id)))
            );
        }

        if(is_archive()) {
            $object_type = get_queried_object();

            $meta[] = sprintf(
                '<meta property="og:title" content="%s">',
                esc_attr(wp_get_document_title())
            );
        }

        return self::print_tags($meta);
    }


    /**
     * Add twitter tags
     *
     * Note: we shouldn't duplicate og tags
     *
     * @link https://developer.twitter.com/en/docs/tweets/optimize-with-cards/guides/getting-started.html
     */
    public static function add_twitter_tags() {
        $meta = [
            '<meta name="twitter:card" content="summary_large_image">',
            '<meta name="twitter:site" content="@knife_media">'
        ];

        if(method_exists('Knife_Snippet_Image', 'get_social_image')) {
            // Get social image array
            $social_image = Knife_Snippet_Image::get_social_image();

            $meta[] = sprintf(
                '<meta name="twitter:image" content="%s">',
                esc_attr($social_image[0])
            );
        }

        return self::print_tags($meta);
    }


    /**
     * Add facebook additional tag
     */
    public static function add_facebook_tags() {
        $meta = [
            '<meta property="fb:app_id" content="1281081571902073">',
            '<meta property="fb:page_id" content="518169241541755">'
        ];

        return self::print_tags($meta);
    }


    /**
     * Add telegram meta tag
     */
    public static function add_telegram_tags() {
        $meta = [
            '<meta name="telegram:channel" content="@knifemedia">'
        ];

        return self::print_tags($meta);
    }


    /**
     * Get description
     */
    private static function get_description() {
        if(is_singular() && !is_front_page()) {
            $object_id = get_queried_object_id();

            if(has_excerpt($object_id)) {
                return trim(strip_tags(get_the_excerpt($object_id)));
            }
        }

        if(is_archive()) {
            $object_type = get_queried_object();

            if(!empty($object_type->description)) {
                return $object_type->description;
            }

            if(!empty($object_type->name)) {
                $description = __('Журнал Нож – архив статей по теме ', 'knife-theme') . strip_tags($object_type->name);

                return trim($description);
            }
        }

        return get_bloginfo('description');
    }


    /**
     * Print tags if not empty array
     */
    private static function print_tags($meta) {
        foreach($meta as $tag) {
            echo $tag . PHP_EOL;
        }
    }
}


/**
 * Load current module environment
 */
Knife_Site_Meta::load_module();
