<?php
/**
 * Template Name: Карточки
 * Template Post Type: post
 *
 * Template for displaying aside post format
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.12
 */

get_header(); ?>

<?php
    if(is_active_sidebar('knife-feature')) :
        dynamic_sidebar('knife-feature');
    endif;
?>

<section class="content">
    <?php
        while(have_posts()) : the_post();

            get_template_part('partials/content', 'cards');

        endwhile;
    ?>
</section>

<?php get_footer();
