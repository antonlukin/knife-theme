<?php
/**
 * special archive template
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
                    get_template_directory_uri() . '/core/special/lighthouse/images/logo.png',
                );

                printf(
                    '<h1>%s</h1>',
                    _x('Дом с маяком', 'special: lighthouse', 'knife-theme')
                );

                printf(
                    '<p>%s</p>',
                    _x(
                        'Истории пациентов, их семей и ассистентов хосписа «Дом с маяком»',
                        'special: lighthouse', 'knife-theme'
                    )
                );
            ?>
        </div>
    </div>

    <div class="archive">
        <?php
            while(have_posts()) : the_post();

                get_template_part('core/special/lighthouse/loop');

            endwhile;
        ?>
    </div>
</div>

<?php get_template_part('core/special/lighthouse/footer');
