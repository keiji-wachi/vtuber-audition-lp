<?php

/**
 * BEATLAB Drum School Theme Functions
 *
 * テーマで使用するCSS / JavaScriptの読み込み、
 * テーマ初期設定、
 * Google Tag Manager、
 * 各機能ファイルの読み込みを管理する。
 */


/* ==================================================
   Constants
================================================== */

/**
 * Google Tag Manager
 * Container ID.
 */
define(
    'DRUM_SCHOOL_GTM_ID',
    'GTM-KM8DBH7V'
);


/* ==================================================
   Feature Files
================================================== */

/**
 * Contact Form 7
 * カスタムバリデーションを読み込む。
 */
require_once
    get_template_directory()
    . '/inc/cf7-validation.php';


/**
 * Form Analytics
 *
 * フォーム操作イベントの
 * DB保存処理を読み込む。
 */
require_once
    get_template_directory()
    . '/inc/form-analytics.php';

/**
 * Admin Menu Customization
 */
require_once
    get_template_directory()
    . '/inc/admin-menu.php';


/* ==================================================
   Google Tag Manager
================================================== */

/**
 * GTM本体。
 *
 * wp_headを利用して
 * <head>内へ出力する。
 */
function drum_school_lp_gtm_head() {

    if (
        ! defined('DRUM_SCHOOL_GTM_ID') ||
        empty(DRUM_SCHOOL_GTM_ID)
    ) {
        return;
    }


    $gtm_id =
        esc_js(
            DRUM_SCHOOL_GTM_ID
        );


    echo "\n";

    echo "<!-- Google Tag Manager -->\n";


    echo '<script>
(function(w,d,s,l,i){
    w[l]=w[l]||[];

    w[l].push({
        "gtm.start":
            new Date().getTime(),
        event:"gtm.js"
    });

    var f=
            d.getElementsByTagName(s)[0],
        j=
            d.createElement(s),
        dl=
            l!="dataLayer"
                ? "&l="+l
                : "";

    j.async=true;

    j.src=
        "https://www.googletagmanager.com/gtm.js?id="
        + i
        + dl;

    f.parentNode.insertBefore(
        j,
        f
    );

})(
    window,
    document,
    "script",
    "dataLayer",
    "' . $gtm_id . '"
);
</script>' . "\n";


    echo "<!-- End Google Tag Manager -->\n";

}

add_action(
    'wp_head',
    'drum_school_lp_gtm_head',
    1
);


/**
 * GTM noscript。
 *
 * wp_body_openを利用して
 * <body>開始直後へ出力する。
 */
function drum_school_lp_gtm_body() {

    if (
        ! defined('DRUM_SCHOOL_GTM_ID') ||
        empty(DRUM_SCHOOL_GTM_ID)
    ) {
        return;
    }


    $gtm_id =
        esc_attr(
            DRUM_SCHOOL_GTM_ID
        );


    echo "\n";

    echo "<!-- Google Tag Manager (noscript) -->\n";


    echo '<noscript>
<iframe
    src="https://www.googletagmanager.com/ns.html?id=' . $gtm_id . '"
    height="0"
    width="0"
    style="display:none;visibility:hidden"
    title="Google Tag Manager"
></iframe>
</noscript>' . "\n";


    echo "<!-- End Google Tag Manager (noscript) -->\n";

}

add_action(
    'wp_body_open',
    'drum_school_lp_gtm_body'
);


/* ==================================================
   Assets
================================================== */

function drum_school_lp_enqueue_assets() {

    $theme_uri =
        get_template_directory_uri();


    /* =========================
       CSS
    ========================== */

    $styles = array(
        'base',
        'layout',
        'components',
        'motion',
        'header',
        'hero',
        'trial-bar',
        'features',
        'flow',
        'pricing',
        'instructor',
        'voice',
        'faq',
        'final-cta',
        'contact',
        'footer',
    );


    foreach (
        $styles
        as $style
    ) {

        wp_enqueue_style(
            'drum-school-' . $style,
            $theme_uri
                . '/assets/css/'
                . $style
                . '.css',
            array(),
            '1.0.0'
        );

    }


    /* =========================
       Thanks Page CSS
    ========================== */

    if (
        is_page(
            'thanks'
        )
    ) {

        wp_enqueue_style(
            'drum-school-thanks',
            $theme_uri
                . '/assets/css/thanks.css',
            array(),
            '1.0.0'
        );

    }


    /* =========================
       JavaScript
    ========================== */

    /**
     * LP共通処理。
     */
    wp_enqueue_script(
        'drum-school-lp',
        $theme_uri
            . '/assets/js/lp.js',
        array(),
        '1.0.0',
        true
    );


    /**
     * Mobile Header Navigation.
     */
    wp_enqueue_script(
        'drum-school-header',
        $theme_uri
            . '/assets/js/header.js',
        array(),
        '1.0.0',
        true
    );


    /**
     * Scroll Reveal.
     */
    wp_enqueue_script(
        'drum-school-reveal',
        $theme_uri
            . '/assets/js/reveal.js',
        array(),
        '1.0.0',
        true
    );


    /**
     * FAQ Accordion.
     */
    wp_enqueue_script(
        'drum-school-faq',
        $theme_uri
            . '/assets/js/faq.js',
        array(),
        '1.0.0',
        true
    );


    /**
     * Contact Form 7
     * 5STEPフォーム制御。
     */
    wp_enqueue_script(
        'drum-school-multi-step-form',
        $theme_uri
            . '/assets/js/multi-step-form.js',
        array(),
        '1.0.0',
        true
    );


    /**
     * Form Tracking.
     *
     * UTMパラメータ、
     * referrer、
     * landing URL、
     * dataLayerイベント、
     * WordPress DBイベント保存など、
     * フォーム計測関連の処理を管理する。
     */
    wp_enqueue_script(
        'drum-school-form-tracking',
        $theme_uri
            . '/assets/js/form-tracking.js',
        array(),
        '1.0.0',
        true
    );


    /**
     * Form Trackingへ
     * WordPress AJAX設定を渡す。
     */
    wp_localize_script(
        'drum-school-form-tracking',
        'drumSchoolAnalyticsConfig',
        array(
            'ajaxUrl' =>
                admin_url(
                    'admin-ajax.php'
                ),

            'nonce' =>
                wp_create_nonce(
                    'drum_school_form_analytics'
                ),
        )
    );


    /**
     * JavaScriptへ
     * サンクスページURLを渡す。
     */
    wp_localize_script(
        'drum-school-multi-step-form',
        'drumSchoolFormConfig',
        array(
            'thanksUrl' =>
                home_url(
                    '/thanks/'
                ),
        )
    );

}

add_action(
    'wp_enqueue_scripts',
    'drum_school_lp_enqueue_assets'
);


/* ==================================================
   Theme Setup
================================================== */

function drum_school_lp_setup() {

    add_theme_support(
        'title-tag'
    );

}

add_action(
    'after_setup_theme',
    'drum_school_lp_setup'
);