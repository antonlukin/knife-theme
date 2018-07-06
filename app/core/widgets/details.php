<?php
/**
 * Details widget
 *
 * Recent posts widget showing as bright links
 *
 * @package knife-theme
 * @since 1.1
 */


class Knife_Details_Widget extends WP_Widget {

    public function __construct() {
        $widget_ops = [
            'classname' => 'details',
            'description' => __('Выводит список контрастных ссылок на посты по критерию.', 'knife-theme'),
            'customize_selective_refresh' => true
        ];

        parent::__construct('knife_theme_details', __('[НОЖ] Микроформаты', 'knife-theme'), $widget_ops);
    }


     /**
     * Outputs the content of the widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array args  The array of form elements
     * @param array instance The current instance of the widget
     */
    public function widget($args, $instance) {
        $defaults = [
            'title' => '',
            'posts_per_page' => 10,
            'taxonomy' => 'category',
            'termlist' => []
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        extract($instance);

        $html = get_transient($this->id);

        if($html === false) :
            $q = new WP_Query([
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'posts_per_page' => $posts_per_page,
                'tax_query' => [
                    [
                        'field' => 'id',
                        'taxonomy' => $taxonomy,
                        'terms' => $termlist
                    ],
                ]
            ]);

            ob_start();

            if($q->have_posts()) :
                echo $args['before_widget'];

                while($q->have_posts()) : $q->the_post();

                    printf('<article class="widget__item"><a class="widget__link" href="%1$s">%2$s</a></article>',
                        get_permalink(),
                        get_the_title()
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
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    function form($instance) {
        $defaults = [
            'title' => '',
            'posts_per_page' => 10,
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
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr($this->get_field_id('title')),
            esc_attr($this->get_field_name('title')),
            __('Заголовок:', 'knife-theme'),
            esc_attr($instance['title'])
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

         printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr($this->get_field_id('posts_per_page')),
            esc_attr($this->get_field_name('posts_per_page')),
            __('Количество постов:', 'knife-theme'),
            esc_attr($instance['posts_per_page'])
        );
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

        $instance['posts_per_page'] = absint($new_instance['posts_per_page']);
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['taxonomy'] = sanitize_text_field($new_instance['taxonomy']);
        $instance['termlist'] = $terms;

        return $instance;
    }
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
    register_widget('Knife_Details_Widget');
});
