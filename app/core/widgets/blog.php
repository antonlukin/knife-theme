<?php
/**
 * Blog widget
 *
 * 5 posts with create post button
 *
 * @package knife-theme
 * @since 1.3
 */


class Knife_Blog_Widget extends WP_Widget {

    public function __construct() {
        $widget_ops = [
            'classname' => 'blog',
            'description' => __('Выводит полосу из историй в виде карточек.', 'knife-theme'),
            'customize_selective_refresh' => true
        ];

        parent::__construct('knife_theme_blog', __('[НОЖ] Блоги', 'knife-theme'), $widget_ops);
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
            'offset' => 0
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        extract($instance);

        $html = false;//get_transient($this->id);

        if($html === false) :

            // Get query by widget args
            $q = new WP_Query([
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'post_type' => 'blog',
                'posts_per_page' => 5,
                'offset' => $offset
            ]);

            ob_start();

            echo $args['before_widget'];

            printf('<div class="widget__head"><a class="widget__head-link" href="%2$s">%1$s</a></div>',
                esc_html($title),
                get_post_type_archive_link('blog')
            );

            while($q->have_posts()) : $q->the_post();

?>
    <div class="widget__item">
        <footer class="widget__footer">
			<?php
				knife_theme_meta([
					'opts' => ['author', 'date'],
					'before' => '<div class="widget__meta meta">',
					'after' => '</div>'
				]);

				printf(
					'<a class="widget__link" href="%2$s">%1$s</a>',
					the_title('<p class="widget__title">', '</p>', false),
					get_permalink()
				);
			?>
		</footer>
    </div>
<?php

            endwhile;

?>
    <div class="widget__item">
        <a class="widget__button button">Создать публикацию</a>
    </div>
<?php
            echo $args['after_widget'];

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
        $instance = $old_instance;

        $instance['offset'] = absint($new_instance['offset']);
        $instance['title'] = sanitize_text_field($new_instance['title']);

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
            'posts_per_page' => 4,
            'offset' => 0
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        // Widget title
        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr($this->get_field_id('title')),
            esc_attr($this->get_field_name('title')),
            __('Заголовок:', 'knife-theme'),
            esc_attr($instance['title']),
            __('Отобразится на странице в лейбле', 'knife-theme')
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
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
    register_widget('Knife_Blog_Widget');
});
