<?php
/**
* Open quiz
*
* Custom post type for quiz
*
* @package knife-theme
* @since 1.6
*/

if (!defined('WPINC')) {
    die;
}

class Knife_Open_Quiz {
    /**
     * Unique slug using for custom post type register and url
     *
     * @access  private
     * @var     string
     */
    private static $slug = 'quiz';


    /**
     * Quiz share query var
     *
     * @access  private
     * @var     string
     */
    private static $query_var = 'share';


    /**
     * Unique nonce string
     *
     * @access  private static
     * @var     string
     */
    private static $nonce = 'knife-quiz-nonce';


    /**
     * Unique meta to store quiz options
     *
     * @access  private
     * @var     string
     */
    private static $meta_options = '_knife-quiz-options';


    /**
     * Unique meta to store quiz items
     *
     * @access  private
     * @var     string
     */
    private static $meta_items = '_knife-quiz-items';


    /**
     * Unique meta to store quiz results
     *
     * @access  private
     * @var     string
     */
    private static $meta_results = '_knife-quiz-results';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Register quiz post type
        add_action('init', [__CLASS__, 'register_type']);

        // Add quiz metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox'], 10);

        // Save metabox
        add_action('save_post', [__CLASS__, 'save_metabox']);

        // Add scripts to admin page
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
    }


    /**
     * Register select post type
     */
    public static function register_type() {
        register_post_type(self::$slug, [
            'labels'                    => [
                'name'                  => __('Тесты', 'knife-theme'),
                'singular_name'         => __('Тест', 'knife-theme'),
                'add_new'               => __('Добавить тест', 'knife-theme'),
                'menu_name'             => __('Тесты', 'knife-theme'),
                'all_items'             => __('Все тесты', 'knife-theme'),
                'add_new_item'          => __('Добавить новый тест', 'knife-theme'),
                'new_item'              => __('Новый тест', 'knife-theme'),
                'edit_item'             => __('Редактировать тест', 'knife-theme'),
                'update_item'           => __('Обновить тест', 'knife-theme'),
                'view_item'             => __('Просмотреть тест', 'knife-theme'),
                'view_items'            => __('Просмотреть тесты', 'knife-theme'),
                'search_items'          => __('Искать тест', 'knife-theme'),
                'insert_into_item'      => __('Добавить в тест', 'knife-theme')
            ],
            'label'                 => __('Тесты', 'knife-theme'),
            'supports'              => ['title', 'thumbnail', 'excerpt', 'comments'],
            'taxonomies'            => ['post_tag'],
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 4,
            'menu_icon'             => 'dashicons-list-view',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true
        ]);
    }


    /**
     * Add quiz metabox
     */
    public static function add_metabox() {
        add_meta_box('knife-quiz-metabox', __('Настройки теста'), [__CLASS__, 'display_metabox'], self::$slug, 'normal', 'high');
    }


    /**
    * Enqueue assets to admin post screen only
    */
    public static function enqueue_assets($hook) {
        if(!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        $post_id = get_the_ID();

        if(get_post_type($post_id) !== self::$slug) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Insert scripts for dynaimc wp_editor
        wp_enqueue_editor();

        // Insert wp media scripts
        wp_enqueue_media();

        // Insert admin styles
        wp_enqueue_style('knife-quiz-metabox', $include . '/styles/quiz-metabox.css', [], $version);

        // Insert admin scripts
        wp_enqueue_script('knife-quiz-metabox', $include . '/scripts/quiz-metabox.js', ['jquery', 'jquery-ui-sortable'], $version);

        $options = [
            'post_id' => absint($post_id),
            'meta_items' => esc_attr(self::$meta_items),
            'meta_results' => esc_attr(self::$meta_results),
            'choose' => __('Выберите изображение', 'knife-theme'),
            'error' => __('Непредвиденная ошибка сервера', 'knife-theme')
        ];

        wp_localize_script('knife-quiz-metabox', 'knife_quiz_metabox', $options);
    }


    /**
     * Display quiz metabox
     */
    public static function display_metabox($post, $box) {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/quiz-metabox.php');
    }


    /**
     * Save quiz options
     */
    public static function save_metabox($post_id) {
        if(get_post_type($post_id) !== self::$slug) {
            return;
        }

        if(!isset($_REQUEST[self::$nonce])) {
            return;
        }

        if(!wp_verify_nonce($_REQUEST[self::$nonce], 'metabox')) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }


        // Update options
        if(isset($_REQUEST[self::$meta_options])) {
            update_post_meta($post_id, self::$meta_options, $_REQUEST[self::$meta_options]);
        }

        // Update items meta
        self::update_items(self::$meta_items, $post_id);

        // Update results meta
        self::update_results(self::$meta_results, $post_id);
    }


    /**
     * Update quiz items meta from post-metabox
     */
    private static function update_items($query, $post_id) {
        // Delete quiz post meta to create it again below
        delete_post_meta($post_id, $query);

        foreach($_REQUEST[$query] as $item) {
            // Filter answer array
            if(isset($item['answer']) && is_array($item['answer'])) {
                foreach($item['answer'] as $i => &$answer) {
                    // Remove answer if empty
                    if((bool) array_filter($answer) === false) {
                        unset($item['answer'][$i]);
                    }
                }
            }

            // Add post meta if not empty
            if(array_filter($item)) {
                add_post_meta($post_id, $query, wp_kses_post_deep($item));
            }
        }
    }


    /**
     * Update quiz results meta from post-metabox
     */
    private static function update_results($query, $post_id) {
        // Delete quiz post meta to create it again below
        delete_post_meta($post_id, $query);

        foreach($_REQUEST[$query] as $result) {
            if(array_filter($result)) {
                add_post_meta($post_id, $query, wp_kses_post_deep($result));
            }
        }
    }
}


/**
 * Load current module environment
 */
Knife_Open_Quiz::load_module();
