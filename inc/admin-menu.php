<?php

/**
 * BEATLAB Drum School
 * Admin Menu Customization
 *
 * クライアントが運用時に迷わないよう、
 * WordPress管理画面のメニューを整理する。
 */


/* ==================================================
   Menu Customization
================================================== */

/**
 * 管理画面メニューを整理する。
 */
function drum_school_customize_admin_menu() {

    global $menu;


    /* =========================
       Rename
    ========================== */

    foreach (
        $menu
        as $index => $menu_item
    ) {

        if (
            ! isset(
                $menu_item[2]
            )
        ) {
            continue;
        }


        /**
         * Media
         */
        if (
            $menu_item[2] ===
            'upload.php'
        ) {

            $menu[$index][0] =
                '画像管理';

        }


        /**
         * Pages
         */
        if (
            $menu_item[2] ===
            'edit.php?post_type=page'
        ) {

            $menu[$index][0] =
                'ページ編集';

        }


        /**
         * Contact Form 7
         */
        if (
            $menu_item[2] ===
            'wpcf7'
        ) {

            $menu[$index][0] =
                'お問い合わせ管理';

        }

    }


    /* =========================
       Add "View Site"
    ========================== */

    add_menu_page(
        'サイトを表示',
        'サイトを表示',
        'read',
        'drum-school-view-site',
        'drum_school_render_site_preview_page',
        'dashicons-visibility',
        3
    );


    /* =========================
       Hide
    ========================== */

    remove_menu_page(
        'edit.php'
    );


    remove_menu_page(
        'edit-comments.php'
    );


    remove_menu_page(
        'themes.php'
    );


    remove_menu_page(
        'plugins.php'
    );


    remove_menu_page(
        'users.php'
    );


    remove_menu_page(
        'tools.php'
    );


    remove_menu_page(
        'options-general.php'
    );

}

add_action(
    'admin_menu',
    'drum_school_customize_admin_menu',
    999
);


/* ==================================================
   Site Preview
================================================== */

/**
 * 管理画面右側へ
 * フロントページをプレビュー表示する。
 *
 * admin_preview=1 を付けることで、
 * form-tracking.js側で
 * アクセス解析対象から除外する。
 */
function drum_school_render_site_preview_page() {

    if (
        ! current_user_can(
            'read'
        )
    ) {
        return;
    }


    $preview_url =
        add_query_arg(
            'admin_preview',
            '1',
            home_url('/')
        );

    ?>

    <div class="wrap drum-school-site-preview">

        <div class="drum-school-site-preview__header">

            <div>

                <p class="drum-school-site-preview__eyebrow">
                    BEATLAB Drum School
                </p>

                <h1>
                    サイトを表示
                </h1>

                <p class="drum-school-site-preview__description">
                    公開中のLPを管理画面内で確認できます。
                </p>

            </div>


            <a
                href="<?php echo esc_url(
                    home_url('/')
                ); ?>"
                class="button button-secondary"
                target="_blank"
                rel="noopener noreferrer"
            >
                新しいタブで開く
            </a>

        </div>


        <div class="drum-school-site-preview__frame-wrap">

            <iframe
                src="<?php echo esc_url(
                    $preview_url
                ); ?>"
                class="drum-school-site-preview__frame"
                title="BEATLAB Drum School Preview"
            ></iframe>

        </div>

    </div>

    <?php
}


/* ==================================================
   Preview CSS
================================================== */

function drum_school_site_preview_admin_styles(
    $hook_suffix
) {

    if (
        $hook_suffix !==
        'toplevel_page_drum-school-view-site'
    ) {
        return;
    }

    ?>

    <style>

        body.toplevel_page_drum-school-view-site
        #wpcontent {
            background:
                linear-gradient(
                    180deg,
                    #f6f7f7 0%,
                    #eef1f5 100%
                );

            min-height: 100vh;
        }


        .drum-school-site-preview {
            max-width: 1500px;
            margin-top: 24px;
        }


        .drum-school-site-preview__header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;

            gap: 20px;

            margin-bottom: 20px;
        }


        .drum-school-site-preview__eyebrow {
            margin: 0 0 6px;

            font-size: 12px;
            font-weight: 700;

            letter-spacing: 0.08em;
            text-transform: uppercase;

            color: #646970;
        }


        .drum-school-site-preview__header h1 {
            margin: 0;
            font-size: 28px;
        }


        .drum-school-site-preview__description {
            margin: 7px 0 0;
            color: #646970;
        }


        .drum-school-site-preview__frame-wrap {
            overflow: hidden;

            background: #ffffff;

            border:
                1px solid
                #dcdcde;

            border-radius: 14px;

            box-shadow:
                0 12px 36px
                rgba(0, 0, 0, 0.1);
        }


        .drum-school-site-preview__frame {
            display: block;

            width: 100%;

            height:
                calc(
                    100vh - 205px
                );

            min-height: 650px;

            border: 0;

            background: #ffffff;
        }


        @media screen and (max-width: 782px) {

            .drum-school-site-preview {
                margin-right: 10px;
            }


            .drum-school-site-preview__header {
                align-items: flex-start;
                flex-direction: column;
            }


            .drum-school-site-preview__frame {
                height:
                    calc(
                        100vh - 250px
                    );

                min-height: 550px;
            }

        }

    </style>

    <?php
}

add_action(
    'admin_enqueue_scripts',
    'drum_school_site_preview_admin_styles'
);


/* ==================================================
   Menu Order
================================================== */

function drum_school_custom_admin_menu_order(
    $menu_order
) {

    if (
        ! $menu_order
    ) {
        return true;
    }


    return array(
        'index.php',
        'drum-school-view-site',
        'drum-school-form-analytics',
        'wpcf7',
        'edit.php?post_type=page',
        'upload.php',
    );
}


add_filter(
    'custom_menu_order',
    '__return_true'
);


add_filter(
    'menu_order',
    'drum_school_custom_admin_menu_order'
);