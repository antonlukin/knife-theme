<?php
/**
 * Lighthouse: archive template
 *
 * @package knife-theme
 * @since 1.10
 */

get_header(); ?>

<div class="content">

    <div class="caption">
        <div class="caption__description">
            <?php
                printf(
                    '<img src="%s" alt="">',
                    get_template_directory_uri() . "/special/lighthouse/images/logo.png",
                );

                printf(
                    '<h1>%s</h1>',
                    _x('Дом с маяком', 'special: lighthouse', 'knife-theme')
                );

                printf(
                    '<p>%s</p>',
                    _x(
                        'Истории юношей и девушек, находящихся под опекой московского хосписа «дом с маяком»',
                        'special: lighthouse', 'knife-theme'
                    )
                );
            ?>
        </div>
    </div>

    <div class="archive">
        <?php
            while(have_posts()) : the_post();

                get_template_part('special/lighthouse/loop');

            endwhile;
        ?>
    </div>
</div>

<?php get_template_part('special/lighthouse/footer');