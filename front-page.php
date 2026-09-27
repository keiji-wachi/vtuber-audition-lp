<?php

/**
 * Drum School LP
 *
 * ドラムスクールの集客・無料体験レッスン申込用LP。
 */

get_header();

?>

<main class="lp-main">

    <?php get_template_part('template-parts/sections/hero'); ?>

    <?php get_template_part('template-parts/sections/trial-bar'); ?>

    <?php get_template_part('template-parts/sections/features'); ?>

    <?php get_template_part('template-parts/sections/flow'); ?>

    <?php get_template_part('template-parts/sections/pricing'); ?>

    <?php get_template_part('template-parts/sections/instructor'); ?>

    <?php get_template_part('template-parts/sections/voice'); ?>

    <?php get_template_part('template-parts/sections/faq'); ?>

    <?php get_template_part('template-parts/sections/final-cta'); ?>

    <?php get_template_part('template-parts/sections/contact'); ?>

</main>

<?php get_footer(); ?>