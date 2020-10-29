<?php
/**
 * special archive template
 *
 * @package knife-theme
 * @since 1.12
 * @version 1.13
 */

get_header(); ?>

<div class="content">

    <div class="caption">
        <div class="caption__description">
            <?php
                printf(
                    '<h1>%s</h1>',
                    _x('Это бизнес.<br> И это личное', 'special: new-business', 'knife-theme')
                );

                printf(
                    '<img src="%s" alt="">',
                    get_template_directory_uri() . '/core/special/new-business/images/spinner.png',
                );
            ?>
        </div>
    </div>

    <div class="archive">
        <?php
            while(have_posts()) : the_post();

                get_template_part('core/special/new-business/loop');

            endwhile;
        ?>
    </div>
</div>

<?php get_template_part('core/special/new-business/footer');
