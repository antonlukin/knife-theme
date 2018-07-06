<?php
/**
 * Transparent widget
 *
 * Transparent recent posts with optional stickers
 *
 * @package knife-theme
 * @since 1.1
 */


class Knife_Transparent_Widget extends WP_Widget {

    public function __construct() {
        $widget_ops = [
            'classname' => 'transparent',
            'description' => __('Выводит список из четырех прозрачных постов со стикерами.', 'knife-theme'),
            'customize_selective_refresh' => true
        ];

        parent::__construct('knife_theme_transparent', __('[НОЖ] Прозрачный', 'knife-theme'), $widget_ops);
    }


    /**
     * Outputs the content of the widget
     */
    public function widget($args, $instance) {
        $defaults = [
            'title' => '',
            'link' => '',
            'offset' => 0,
            'sticker' => 0,
            'taxonomy' => 'category',
            'termlist' => []
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        extract($instance);

        // Check cache before creating WP_Query object
        $html = get_transient($this->id);

        if($html === false) :
            $q = new WP_Query($this->get_query($instance));

            ob_start();

            if($q->have_posts()) :
                echo $args['before_widget'];

                if(!empty($title)) {
                    if(empty($link)) {
                        printf('<div class="widget__head meta"><span class="meta__item">%s</span></div>',
                            esc_html($title)
                        );
                    }
                    else {
                        printf('<div class="widget__head meta"><a class="meta__link" href="%2$s">%1$s</a></div>',
                            esc_html($title), esc_url($link)
                        );
                    }
                }

                while($q->have_posts()) : $q->the_post();
                    $image = '';

                    if($picture = get_post_meta(get_the_ID(), '_knife-sticker', true)) {
                        $image = sprintf('<img class="widget__sticker" src="%s">', $picture);
                    }

                    $meta = the_info(
                        '<div class="widget__meta meta">', '</div>',
                        ['author', 'date'], false
                    );

                    $link = sprintf(
                        '<a class="widget__link" href="%2$s">%1$s</a>',
                        the_title('<p class="widget__title">', '</p>', false),
                        get_permalink()
                    );

                    $footer = sprintf('<footer class="widget__footer">%1$s %2$s</footer>',
                        $meta, $link
                    );

                    printf('<article class="widget__item"><div class="widget__parent">%s</div></article>',
                        $image . $footer
                    );
                endwhile;

                echo $args['after_widget'];
            endif;

            wp_reset_query();

            $html = ob_get_clean();
            set_transient($this->id, $html, 24 * HOUR_IN_SECONDS);
        endif;

        echo $html;
    }


    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance) {

        if(taxonomy_exists($new_instance['taxonomy'])) {
            $taxonomy = $new_instance['taxonomy'];

            if(isset($_REQUEST['widget-id']) && $_REQUEST['widget-id'] == $this->id) {
                $posted_terms = [];

                if(isset($_POST['post_category']))
                    $posted_terms = $_POST['post_category'];

                if(isset($_POST['tax_input'][$taxonomy]))
                    $posted_terms = $_POST['tax_input'][$taxonomy];

                foreach($posted_terms as $term) {
                    if(term_exists(absint($term), $taxonomy))
                        $terms[] = absint($term);
                }
            }
        }

        $instance = $old_instance;

        $instance['offset'] = absint($new_instance['offset']);
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['link'] = esc_url($new_instance['link']);
        $instance['taxonomy'] = sanitize_text_field($new_instance['taxonomy']);
        $instance['termlist'] = $terms;
        $instance['sticker'] = $new_instance['sticker'] ? 1 : 0;

        return $instance;
    }


    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance) {
        $defaults = [
            'title' => '',
            'link' => '',
            'offset' => 0,
            'sticker' => 0,
            'taxonomy' => 'category',
            'termlist' => []
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        $taxes = get_taxonomies([
            'public' => true
        ], 'object');

        $terms = wp_terms_checklist(0, [
            'taxonomy' => $instance['taxonomy'],
            'selected_cats' => $instance['termlist'],
            'echo' => false
        ]);


        // Widget title
        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr($this->get_field_id('title')),
            esc_attr($this->get_field_name('title')),
            __('Заголовок:', 'knife-theme'),
            esc_attr($instance['title']),
            __('Отобразится на странице в лейбле', 'knife-theme')
        );


         // Widget title link
        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr($this->get_field_id('link')),
            esc_attr($this->get_field_name('link')),
            __('Ссылка с лейбла:', 'knife-theme'),
            esc_attr($instance['link']),
            __('Можно оставить поле пустым', 'knife-theme')
        );


        // Taxonomies filter
        printf(
            '<p><label for="%1$s">%3$s</label><select class="widefat knife-widget-taxonomy" id="%1$s" name="%2$s">',
            esc_attr($this->get_field_id('taxonomy')),
             esc_attr($this->get_field_name('taxonomy')),
            __('Фильтр записей:', 'knife-theme')
        );

        foreach($taxes as $name => $object) {
            printf('<option value="%1$s"%3$s>%2$s</option>', $name, $object->label, selected($instance['taxonomy'], $name, false));
        }

        echo '</select></p>';


        // Terms filter
        printf(
            '<ul class="cat-checklist categorychecklist knife-widget-termlist" id="%1$s">%2$s</ul>',
            esc_attr($this->get_field_id('termlist')),
            $terms
        );


        // Ony with stickers checkbox
        printf(
            '<p><input type="checkbox" id="%1$s" name="%2$s" class="checkbox"%4$s><label for="%1$s">%3$s</label></p>',
            esc_attr($this->get_field_id('sticker')),
            esc_attr($this->get_field_name('sticker')),
            __('Только со стикерами', 'knife-theme'),
            checked($instance['sticker'], 1, false)
        );


        // Posts offset
        printf(
            '<p><label for="%1$s">%3$s</label> <input class="tiny-text" id="%1$s" name="%2$s" type="number" value="%4$s"></p>',
            esc_attr($this->get_field_id('offset')),
            esc_attr($this->get_field_name('offset')),
            __('Пропустить записей:', 'knife-theme'),
            esc_attr($instance['offset'])
        );
    }


    /**
     * Generate query params from instance args
     */
    private function get_query($instance) {
        extract($instance);

        $query = [
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'offset' => $offset,
            'posts_per_page' => 4,
            'tax_query' => [[
                'field' => 'id',
                'taxonomy' => $taxonomy,
                'terms' => $termlist
            ]]
        ];

        // If user selected option to show posts only with stickers
        if($sticker === 1) {
            $query['meta_query'] = [[
                'key' => '_knife-sticker',
                'value' => '',
                'compare' => '!='
            ]];
        }

        return $query;
    }
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
    register_widget('Knife_Transparent_Widget');
});
