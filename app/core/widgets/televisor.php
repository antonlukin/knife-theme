<?php
/**
 * Television widget
 *
 * Widget shows 4 posts and recent news block
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.12
 */


class Knife_Widget_Televisor extends WP_Widget {
    /**
     * Widget post types
     */
    private $post_type = ['post', 'quiz', 'generator'];


    /**
     * Categories to show in units
     */
    private $category = ['longreads', 'play'];


    /**
     * News category slug
     */
    private $news_name = 'news';


    /**
     * Widget constructor
     */
    public function __construct() {
        $widget_ops = [
            'classname' => 'televisor',
            'description' => __('Выводит телевизор из 4 постов и блока новостей', 'knife-theme'),
            'customize_selective_refresh' => true
        ];

        parent::__construct('knife_widget_televisor', __('[НОЖ] Телевизор', 'knife-theme'), $widget_ops);
    }


    /**
     * Outputs the content of the widget.
     */
    public function widget($args, $instance) {
        $defaults = [
            'title' => '',
            'link' => '',
            'cover' => 0,
            'unique' => 1,
            'posts_per_page' => 7,
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        echo $args['before_widget'];

        include(get_template_directory() . '/templates/widget-televisor.php');

        echo $args['after_widget'];
    }


    /**
     * Sanitize widget form values as they are saved.
     */
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = $new_instance['title'];
        $instance['posts_per_page'] = (int) $new_instance['posts_per_page'];
        $instance['unique'] = $new_instance['unique'] ? 1 : 0;
        $instance['link'] = esc_url($new_instance['link']);
        $instance['cover'] = absint($new_instance['cover']);

        return $instance;
    }


    /**
     * Back-end widget form.
     */
    public function form($instance) {
        $defaults = [
            'title' => '',
            'link' => '',
            'cover' => 0,
            'unique' => 1,
            'posts_per_page' => 7,
            'picture' => ''
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        // Post url
        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr($this->get_field_id('link')),
            esc_attr($this->get_field_name('link')),
            __('Ссылка с фичера:', 'knife-theme'),
            esc_attr($instance['link']),
            __('На запись c этого сайта', 'knife-theme')
        );


        // Widget title
        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr($this->get_field_id('title')),
            esc_attr($this->get_field_name('title')),
            __('Заголовок:', 'knife-theme'),
            esc_attr($instance['title']),
            __('Заполните, чтобы обновить заголовок фичера', 'knife-theme')
        );


        // Exclude duplicate
        printf(
            '<p><input type="checkbox" id="%1$s" name="%2$s" class="checkbox"%4$s><label for="%1$s">%3$s</label></p>',
            esc_attr($this->get_field_id('unique')),
            esc_attr($this->get_field_name('unique')),
            __('Только уникальные посты', 'knife-theme'),
            checked($instance['unique'], 1, false)
        );


        // News count
        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr($this->get_field_id('posts_per_page')),
            esc_attr($this->get_field_name('posts_per_page')),
            __('Количество новостей:', 'knife-theme'),
            esc_attr($instance['posts_per_page'])
        );


        // Widget cover
        if($cover = wp_get_attachment_url($instance['cover'])) {
            $instance['picture'] = sprintf('<img src="%s" alt="" style="max-width: 100%%;">', esc_url($cover));
        }

        printf(
            '<p>%5$s<input id="%1$s" name="%2$s" type="hidden" value="%3$s"><button type="button" class="button knife-widget-image">%4$s</button></p>',
            esc_attr($this->get_field_id('cover')),
            esc_attr($this->get_field_name('cover')),
            esc_attr($instance['cover']),
            __('Выбрать обложку фичера', 'knife-theme'),
            $instance['picture']
        );
    }


    /**
     * Generate query params from instance args
     */
    private function get_query($instance, $exclude) {
        extract($instance);

        $query = [
            'posts_per_page' => 3,
            'post_type' => $this->post_type,
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'tax_query' => [
                [
                    'taxonomy' => 'category',
                    'field' => 'slug',
                    'terms' => $this->category
                ]
            ]
        ];

        // Check option to show posts only unique posts
        if($unique === 1 && !empty($exclude)) {
            $query['post__not_in'] = $exclude;
        }

        return $query;
    }


    /**
     * Show unit using query loop
     */
    private function show_units($instance) {
        $exclude = get_query_var('widget_exclude', []);
        $query = new WP_Query($this->get_query($instance, $exclude));

        if($query->have_posts()) {
            echo '<div class="widget-units">';

            while($query->have_posts()) {
                $query->the_post();
                $size = 'triple';

                include(get_template_directory() . '/templates/widget-units.php');
            }

            wp_reset_query();
            set_query_var('widget_exclude', array_merge($exclude, wp_list_pluck($query->posts, 'ID')));

            echo '</div>';
        }
    }


    /**
     * Show single post part
     */
    private function show_single($instance) {
        $exclude = get_query_var('widget_exclude', []);
        $post_id = url_to_postid($instance['link']);

        $query = new WP_Query([
            'post_status' => 'any',
            'post_type' => 'any',
            'posts_per_page' => 1,
            'ignore_sticky_posts' => 1,
            'post__in' => [$post_id]
        ]);

        if($query->have_posts()) {
            echo '<div class="widget-single">';

            $query->the_post();

            if(empty($instance['title'])) {
                $instance['title'] = get_the_title();
            }

            $instance['link'] = get_permalink();

            // Include single widget template
            include(get_template_directory() . '/templates/widget-single.php');

            set_query_var('widget_exclude', array_merge($exclude, wp_list_pluck($query->posts, 'ID')));
            wp_reset_query();

            echo '</div>';
        }
    }


    /**
     * Show recent news part
     */
    private function show_recent($instance) {
        $query = new WP_Query([
            'category_name' => $this->news_name,
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => $instance['posts_per_page']
        ]);

        // Set widget title
        $instance['title'] = __('Новости', 'knife-theme');

        if($query->have_posts()) {
            echo '<div class="widget-recent">';

            // Include recent widget template
            include(get_template_directory() . '/templates/widget-recent.php');

            echo '</div>';
        }
    }
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
    register_widget('Knife_Widget_Televisor');
});
