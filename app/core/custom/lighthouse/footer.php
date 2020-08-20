<?php
/**
 * custom footer template
 *
 * @package knife-theme
 * @since 1.10
 */
?>

<footer class="footer">

    <div class="footer__logo">
        <a class="footer__logo-link" href="<?php echo esc_url(home_url('/')); ?>">
            <svg class="footer__logo-image" viewBox="0 0 992 448" version="1.1">
                <path id="path" d="M177.595 5.758 L177.595 108.33 74.635 108.33 74.635 5.758 2.37 5.758 2.37 283.152 74.635 283.152 74.635 173.205 177.595 173.205 177.595 283.152 249.86 283.152 249.86 5.758 177.595 5.758 Z M434.357 0.706 C344.614 0.706 295.659 62.088 295.659 144.456 295.659 226.824 344.611 288.207 434.357 288.207 524.103 288.207 573.056 226.823 573.056 144.457 573.056 62.091 524.101 0.707 434.359 0.707 Z M434.357 224.496 C391.233 224.496 369.47 191.084 369.47 144.465 369.47 97.458 391.221 64.434 434.357 64.434 477.493 64.434 499.244 97.846 499.244 144.465 499.244 191.084 477.493 224.496 434.37 224.496 Z M757.552 109.876 L709.382 109.876 668.204 5.758 595.55 5.758 651.115 142.52 592.058 283.163 664.705 283.163 708.998 174.757 757.567 174.757 757.567 283.163 827.506 283.163 827.506 174.757 876.072 174.757 920.365 283.163 993.012 283.163 933.955 142.518 989.52 5.756 916.873 5.756 875.682 109.875 827.509 109.875 827.509 5.76 757.567 5.76 757.567 109.878 Z M5.827 447.288 L28.583 447.288 28.583 412.153 48.288 392.448 52.256 392.448 79.909 447.289 104.953 447.289 67.072 372.45 99.758 338.233 73.183 338.233 28.581 383.305 28.581 338.233 5.825 338.233 5.825 447.309 Z M190.474 338.211 L190.474 412.145 186.505 412.145 146.5 338.211 117.005 338.211 117.005 447.287 137.775 447.287 137.775 373.354 141.744 373.354 181.749 447.287 211.228 447.287 211.228 338.211 190.458 338.211 Z M233.394 338.211 L233.394 447.287 256.149 447.287 256.149 338.211 233.379 338.211 Z M358.781 358.529 L358.781 338.211 278.281 338.211 278.281 447.287 301.036 447.287 301.036 403.444 346.557 403.444 346.557 383.592 301.037 383.592 301.037 358.547 358.78 358.547 Z M396.491 426.952 L396.491 401.301 441.548 401.301 441.548 381.448 396.491 381.448 396.491 358.536 454.38 358.536 454.38 338.218 373.722 338.218 373.722 447.293 456.056 447.293 456.056 426.976 396.489 426.976 Z M493.01 447.269 L493.01 424.067 469.484 424.067 469.484 447.287 493.009 447.287 Z M512.565 447.269 L533.801 447.269 533.801 366.018 537.769 366.018 555.942 410.009 578.698 410.009 597.03 366.018 600.998 366.018 600.998 447.276 622.234 447.276 622.234 338.201 587.095 338.201 569.375 382.192 565.407 382.192 547.522 338.201 512.551 338.201 512.551 447.277 Z M666.36 426.952 L666.36 401.301 711.417 401.301 711.417 381.448 666.365 381.448 666.365 358.536 724.254 358.536 724.254 338.218 643.593 338.218 643.593 447.293 725.927 447.293 725.927 426.976 666.365 426.976 Z M741.484 338.211 L741.484 447.287 787.006 447.287 C820.452 447.287 837.561 425.439 837.561 392.749 837.561 359.905 820.453 338.211 787.006 338.211 L741.482 338.211 Z M764.25 426.952 L764.25 358.536 784.102 358.536 C806.097 358.536 814.193 373.808 814.193 392.753 814.193 411.698 806.097 426.97 784.102 426.97 L764.25 426.97 Z M854.658 338.211 L854.658 447.287 877.413 447.287 877.413 338.211 854.657 338.211 Z M971.338 447.288 L994.4 447.288 959.57 338.212 924.283 338.212 889.612 447.288 912.074 447.288 920.928 417.809 962.468 417.809 Z M938.201 360.066 L944.924 360.066 956.375 397.946 926.755 397.946 Z" fill="#ffffff" fill-opacity="1" stroke="none"/>
            </svg>
        </a>

        <svg class="footer__logo-cross" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 42.4 42.4">
            <path d="M38.89-.02l3.536 3.537-38.89 38.89L0 38.872z"></path>
            <path d="M3.536-.02l38.89 38.892-3.535 3.535L0 3.517z"></path>
        </svg>

        <?php
            printf(
                '<img class="footer__logo-special" src="%s" alt="">',
                get_template_directory_uri() . '/core/specials/lighthouse/images/logo.png',
            );
        ?>
    </div>

    <div class="footer__copy">
        <?php
            echo term_description(get_term_by('slug', 'lighthouse', 'special'));
        ?>
    </div>

    <div class="footer__button">
        <?php
            printf(
                '<a class="button" href="https://www.childrenshospice.ru/help/" target="_blank">%s</a>',
                _x('Помочь прямо сейчас', 'special: lighthouse', 'knife-theme')
            );
        ?>
    </div>

</footer>

<?php wp_footer(); ?>
</body>
</html>
