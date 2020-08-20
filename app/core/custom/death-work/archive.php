<?php
/**
 * custom archive template
 *
 * @package knife-theme
 * @since 1.12
 */

get_header(); ?>

<div class="content">

    <div class="caption">
        <div class="caption__description">
            <?php
                printf(
                    '<img src="%s" alt="">',
                    get_template_directory_uri() . '/core/custom/death-work/images/logo-white.svg',
                );

                printf(
                    '<h1>%s</h1>',
                    _x('Как работать с трупами и не сойти с ума', 'custom: death-work', 'knife-theme')
                );

                printf(
                    '<p>%s</p>',
                    _x('Истории людей непростых профессий', 'custom: death-work', 'knife-theme')
                );
            ?>
        </div>
    </div>

    <div class="archive">
        <?php
            while(have_posts()) : the_post();

                get_template_part('core/custom/death-work/loop');

            endwhile;
        ?>
    </div>
</div>

<?php get_template_part('core/custom/death-work/footer');
