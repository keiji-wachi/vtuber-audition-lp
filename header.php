<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>


<header class="site-header">

    <div class="site-header__inner">

        <!-- =========================
             Logo
        ========================== -->
        <a
            href="<?php echo esc_url(home_url('/')); ?>"
            class="site-logo"
            aria-label="BEATLAB Drum School トップへ"
        >

            <span class="site-logo__beat">
                BEAT
            </span>

            <span class="site-logo__lab">
                LAB
            </span>

            <span class="site-logo__sub">
                Drum School
            </span>

        </a>


        <!-- =========================
             Navigation
        ========================== -->
        <nav
            id="site-navigation"
            class="site-nav"
            aria-label="メインナビゲーション"
        >

            <a
                href="<?php echo esc_url(
                    home_url('/#features')
                ); ?>"
            >
                スクールの特徴
            </a>


            <a
                href="<?php echo esc_url(
                    home_url('/#lessons')
                ); ?>"
            >
                レッスン内容
            </a>


            <a
                href="<?php echo esc_url(
                    home_url('/#instructor')
                ); ?>"
            >
                講師紹介
            </a>


            <a
                href="<?php echo esc_url(
                    home_url('/#voice')
                ); ?>"
            >
                受講生の声
            </a>


            <a
                href="<?php echo esc_url(
                    home_url('/#faq')
                ); ?>"
            >
                よくある質問
            </a>


            <!-- Mobile CTA -->
            <a
                href="<?php echo esc_url(
                    home_url('/#contact')
                ); ?>"
                class="site-nav__cta"
            >
                無料体験を申し込む

                <span aria-hidden="true">
                    →
                </span>
            </a>

        </nav>


        <!-- =========================
             Desktop CTA
        ========================== -->
        <a
            href="<?php echo esc_url(
                home_url('/#contact')
            ); ?>"
            class="site-header__cta"
        >
            無料体験を申し込む
        </a>


        <!-- =========================
             Mobile Menu Button
        ========================== -->
        <button
            type="button"
            class="site-menu-button"
            aria-label="メニューを開く"
            aria-expanded="false"
            aria-controls="site-navigation"
        >

            <span class="site-menu-button__line"></span>
            <span class="site-menu-button__line"></span>
            <span class="site-menu-button__line"></span>

        </button>

    </div>

</header>