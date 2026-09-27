<?php

/**
 * BEATLAB Drum School
 * Form Analytics
 *
 * LP訪問から問い合わせ完了までの
 * 行動イベントをWordPress DBへ匿名保存し、
 * WordPress管理画面から
 * 到達数・離脱率・CV率を確認する。
 *
 * 保存するデータ：
 * - session_id
 * - event_name
 * - created_at
 *
 * 個人情報は保存しない。
 */


/* ==================================================
   Constants
================================================== */

define(
    'DRUM_SCHOOL_FORM_ANALYTICS_DB_VERSION',
    '1.0.0'
);


/* ==================================================
   Table Name
================================================== */

function drum_school_form_analytics_table_name() {

    global $wpdb;


    return
        $wpdb->prefix
        . 'drum_school_form_events';
}


/* ==================================================
   Create Table
================================================== */

function drum_school_create_form_analytics_table() {

    global $wpdb;


    $table_name =
        drum_school_form_analytics_table_name();


    $charset_collate =
        $wpdb->get_charset_collate();


    $sql = "
        CREATE TABLE {$table_name} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            session_id VARCHAR(64) NOT NULL,
            event_name VARCHAR(64) NOT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY session_event (
                session_id,
                event_name
            ),
            KEY event_name (
                event_name
            ),
            KEY created_at (
                created_at
            )
        ) {$charset_collate};
    ";


    require_once
        ABSPATH
        . 'wp-admin/includes/upgrade.php';


    dbDelta(
        $sql
    );


    update_option(
        'drum_school_form_analytics_db_version',
        DRUM_SCHOOL_FORM_ANALYTICS_DB_VERSION
    );
}


/* ==================================================
   Database Version Check
================================================== */

function drum_school_maybe_create_form_analytics_table() {

    $current_version =
        get_option(
            'drum_school_form_analytics_db_version'
        );


    if (
        $current_version !==
        DRUM_SCHOOL_FORM_ANALYTICS_DB_VERSION
    ) {

        drum_school_create_form_analytics_table();

    }
}

add_action(
    'init',
    'drum_school_maybe_create_form_analytics_table'
);


/* ==================================================
   Allowed Events
================================================== */

function drum_school_form_analytics_allowed_events() {

    return array(
        'page_view',
        'form_start',
        'step_1_complete',
        'step_2_complete',
        'step_3_complete',
        'step_4_complete',
        'step_5_complete',
        'submit_success',
    );
}


/* ==================================================
   AJAX
================================================== */

function drum_school_save_form_analytics_event() {

    check_ajax_referer(
        'drum_school_form_analytics',
        'nonce'
    );


    $session_id =
        isset($_POST['session_id'])
            ? sanitize_text_field(
                wp_unslash(
                    $_POST['session_id']
                )
            )
            : '';


    $event_name =
        isset($_POST['event_name'])
            ? sanitize_key(
                wp_unslash(
                    $_POST['event_name']
                )
            )
            : '';


    if (
        empty($session_id) ||
        empty($event_name)
    ) {

        wp_send_json_error(
            array(
                'message' =>
                    'Required parameters are missing.',
            ),
            400
        );

    }


    if (
        strlen($session_id) > 64
    ) {

        wp_send_json_error(
            array(
                'message' =>
                    'Invalid session ID.',
            ),
            400
        );

    }


    if (
        ! in_array(
            $event_name,
            drum_school_form_analytics_allowed_events(),
            true
        )
    ) {

        wp_send_json_error(
            array(
                'message' =>
                    'Invalid event.',
            ),
            400
        );

    }


    global $wpdb;


    $table_name =
        drum_school_form_analytics_table_name();


    $result =
        $wpdb->query(
            $wpdb->prepare(
                "
                INSERT IGNORE INTO {$table_name}
                (
                    session_id,
                    event_name,
                    created_at
                )
                VALUES
                (
                    %s,
                    %s,
                    %s
                )
                ",
                $session_id,
                $event_name,
                current_time(
                    'mysql'
                )
            )
        );


    if (
        $result === false
    ) {

        wp_send_json_error(
            array(
                'message' =>
                    'Database insert failed.',
            ),
            500
        );

    }


    wp_send_json_success(
        array(
            'saved' =>
                $result === 1,

            'duplicate' =>
                $result === 0,
        )
    );
}


add_action(
    'wp_ajax_drum_school_track_form_event',
    'drum_school_save_form_analytics_event'
);


add_action(
    'wp_ajax_nopriv_drum_school_track_form_event',
    'drum_school_save_form_analytics_event'
);


/* ==================================================
   Admin Menu
================================================== */

function drum_school_form_analytics_admin_menu() {

    add_menu_page(
        'フォーム分析',
        'フォーム分析',
        'manage_options',
        'drum-school-form-analytics',
        'drum_school_render_form_analytics_page',
        'dashicons-chart-bar',
        30
    );
}

add_action(
    'admin_menu',
    'drum_school_form_analytics_admin_menu'
);


/* ==================================================
   Admin Assets
================================================== */

function drum_school_form_analytics_admin_assets(
    $hook_suffix
) {

    if (
        $hook_suffix !==
        'toplevel_page_drum-school-form-analytics'
    ) {
        return;
    }


    wp_enqueue_style(
        'drum-school-admin-analytics',
        get_template_directory_uri()
            . '/assets/css/admin-analytics.css',
        array(),
        '1.0.0'
    );
}

add_action(
    'admin_enqueue_scripts',
    'drum_school_form_analytics_admin_assets'
);


/* ==================================================
   Date Helpers
================================================== */

function drum_school_is_valid_analytics_date(
    $date
) {

    $date_object =
        DateTime::createFromFormat(
            'Y-m-d',
            $date
        );


    return (
        $date_object &&
        $date_object->format(
            'Y-m-d'
        ) ===
        $date
    );
}


function drum_school_get_analytics_date_range() {

    $timezone =
        wp_timezone();


    $now =
        new DateTime(
            'now',
            $timezone
        );


    $default_start =
        $now->format(
            'Y-m-01'
        );


    $default_end =
        $now->format(
            'Y-m-d'
        );


    $start_date =
        isset($_GET['start_date'])
            ? sanitize_text_field(
                wp_unslash(
                    $_GET['start_date']
                )
            )
            : $default_start;


    $end_date =
        isset($_GET['end_date'])
            ? sanitize_text_field(
                wp_unslash(
                    $_GET['end_date']
                )
            )
            : $default_end;


    if (
        ! drum_school_is_valid_analytics_date(
            $start_date
        )
    ) {

        $start_date =
            $default_start;

    }


    if (
        ! drum_school_is_valid_analytics_date(
            $end_date
        )
    ) {

        $end_date =
            $default_end;

    }


    if (
        $start_date >
        $end_date
    ) {

        $start_date =
            $default_start;

        $end_date =
            $default_end;

    }


    return array(
        'start_date' =>
            $start_date,

        'end_date' =>
            $end_date,
    );
}


/* ==================================================
   Analytics Query
================================================== */

function drum_school_get_form_analytics_counts(
    $start_date,
    $end_date
) {

    global $wpdb;


    $table_name =
        drum_school_form_analytics_table_name();


    $start_datetime =
        $start_date
        . ' 00:00:00';


    $end_datetime =
        $end_date
        . ' 23:59:59';


    /**
     * session_id単位で集計。
     *
     * page_viewを単純なPVではなく、
     * この分析上の訪問セッション数として扱う。
     */
    $rows =
        $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT
                    event_name,
                    COUNT(
                        DISTINCT session_id
                    ) AS event_count

                FROM
                    {$table_name}

                WHERE
                    created_at >= %s
                    AND
                    created_at <= %s

                GROUP BY
                    event_name
                ",
                $start_datetime,
                $end_datetime
            ),
            ARRAY_A
        );


    $counts =
        array();


    foreach (
        drum_school_form_analytics_allowed_events()
        as $event_name
    ) {

        $counts[
            $event_name
        ] = 0;

    }


    foreach (
        $rows
        as $row
    ) {

        $event_name =
            $row['event_name'];


        if (
            array_key_exists(
                $event_name,
                $counts
            )
        ) {

            $counts[
                $event_name
            ] =
                (int)
                $row['event_count'];

        }

    }


    return $counts;
}


/* ==================================================
   Analytics Helpers
================================================== */

function drum_school_calculate_rate(
    $value,
    $base
) {

    if (
        $base <= 0
    ) {
        return 0;
    }


    return round(
        (
            $value
            / $base
        )
        * 100,
        1
    );
}


function drum_school_calculate_dropoff_rate(
    $current,
    $previous
) {

    if (
        $previous <= 0
    ) {
        return 0;
    }


    $rate =
        (
            (
                $previous
                - $current
            )
            / $previous
        )
        * 100;


    return round(
        max(
            0,
            $rate
        ),
        1
    );
}


/* ==================================================
   Admin Page
================================================== */

function drum_school_render_form_analytics_page() {

    if (
        ! current_user_can(
            'manage_options'
        )
    ) {
        return;
    }


    $date_range =
        drum_school_get_analytics_date_range();


    $start_date =
        $date_range['start_date'];


    $end_date =
        $date_range['end_date'];


    $counts =
        drum_school_get_form_analytics_counts(
            $start_date,
            $end_date
        );


    $page_view =
        $counts['page_view'];


    $form_start =
        $counts['form_start'];


    $step_1 =
        $counts['step_1_complete'];


    $step_2 =
        $counts['step_2_complete'];


    $step_3 =
        $counts['step_3_complete'];


    $step_4 =
        $counts['step_4_complete'];


    $step_5 =
        $counts['step_5_complete'];


    $submit_success =
        $counts['submit_success'];


    /**
     * LPを訪問したセッションのうち
     * フォームを開始した割合。
     */
    $form_start_rate =
        drum_school_calculate_rate(
            $form_start,
            $page_view
        );


    /**
     * LP訪問から問い合わせ完了までの
     * 最終CV率。
     */
    $lp_cv_rate =
        drum_school_calculate_rate(
            $submit_success,
            $page_view
        );


    $stages =
        array(

            array(
                'label' =>
                    'サイト訪問',

                'count' =>
                    $page_view,

                'dropoff' =>
                    null,
            ),

            array(
                'label' =>
                    'フォーム開始',

                'count' =>
                    $form_start,

                'dropoff' =>
                    drum_school_calculate_dropoff_rate(
                        $form_start,
                        $page_view
                    ),
            ),

            array(
                'label' =>
                    'STEP1 完了',

                'count' =>
                    $step_1,

                'dropoff' =>
                    drum_school_calculate_dropoff_rate(
                        $step_1,
                        $form_start
                    ),
            ),

            array(
                'label' =>
                    'STEP2 完了',

                'count' =>
                    $step_2,

                'dropoff' =>
                    drum_school_calculate_dropoff_rate(
                        $step_2,
                        $step_1
                    ),
            ),

            array(
                'label' =>
                    'STEP3 完了',

                'count' =>
                    $step_3,

                'dropoff' =>
                    drum_school_calculate_dropoff_rate(
                        $step_3,
                        $step_2
                    ),
            ),

            array(
                'label' =>
                    'STEP4 完了',

                'count' =>
                    $step_4,

                'dropoff' =>
                    drum_school_calculate_dropoff_rate(
                        $step_4,
                        $step_3
                    ),
            ),

            array(
                'label' =>
                    'STEP5 完了',

                'count' =>
                    $step_5,

                'dropoff' =>
                    drum_school_calculate_dropoff_rate(
                        $step_5,
                        $step_4
                    ),
            ),

            array(
                'label' =>
                    'お問い合わせ完了',

                'count' =>
                    $submit_success,

                'dropoff' =>
                    drum_school_calculate_dropoff_rate(
                        $submit_success,
                        $step_5
                    ),
            ),
        );

    ?>

    <div class="wrap beatlab-analytics">

        <div class="beatlab-analytics__header">

            <div>

                <p class="beatlab-analytics__eyebrow">
                    BEATLAB Drum School
                </p>

                <h1>
                    フォーム分析
                </h1>

                <p class="beatlab-analytics__description">
                    LP訪問から問い合わせ完了までの
                    到達数・離脱率・CV率を確認できます。
                </p>

            </div>

        </div>


        <form
            method="get"
            class="beatlab-analytics-filter"
        >

            <input
                type="hidden"
                name="page"
                value="drum-school-form-analytics"
            >


            <div class="beatlab-analytics-filter__field">

                <label for="start_date">
                    開始日
                </label>

                <input
                    type="date"
                    id="start_date"
                    name="start_date"
                    value="<?php
                    echo esc_attr(
                        $start_date
                    );
                    ?>"
                >

            </div>


            <div class="beatlab-analytics-filter__field">

                <label for="end_date">
                    終了日
                </label>

                <input
                    type="date"
                    id="end_date"
                    name="end_date"
                    value="<?php
                    echo esc_attr(
                        $end_date
                    );
                    ?>"
                >

            </div>


            <button
                type="submit"
                class="button button-primary"
            >
                表示する
            </button>

        </form>


        <div class="beatlab-analytics-summary">

            <div class="beatlab-analytics-card">

                <span class="beatlab-analytics-card__label">
                    サイト訪問
                </span>

                <strong class="beatlab-analytics-card__value">
                    <?php
                    echo esc_html(
                        number_format(
                            $page_view
                        )
                    );
                    ?>
                </strong>

                <span class="beatlab-analytics-card__unit">
                    セッション
                </span>

            </div>


            <div class="beatlab-analytics-card">

                <span class="beatlab-analytics-card__label">
                    フォーム開始
                    （<?php
                    echo esc_html(
                        number_format(
                            $form_start_rate,
                            1
                        )
                    );
                    ?>%）
                </span>

                <strong class="beatlab-analytics-card__value">
                    <?php
                    echo esc_html(
                        number_format(
                            $form_start
                        )
                    );
                    ?>
                </strong>

                <span class="beatlab-analytics-card__unit">
                    件
                </span>

            </div>


            <div class="beatlab-analytics-card">

                <span class="beatlab-analytics-card__label">
                    お問い合わせ完了
                </span>

                <strong class="beatlab-analytics-card__value">
                    <?php
                    echo esc_html(
                        number_format(
                            $submit_success
                        )
                    );
                    ?>
                </strong>

                <span class="beatlab-analytics-card__unit">
                    件
                </span>

            </div>


            <div
                class="
                    beatlab-analytics-card
                    beatlab-analytics-card--accent
                "
            >

                <span class="beatlab-analytics-card__label">
                    LP CV率
                </span>

                <strong class="beatlab-analytics-card__value">
                    <?php
                    echo esc_html(
                        number_format(
                            $lp_cv_rate,
                            1
                        )
                    );
                    ?>
                </strong>

                <span class="beatlab-analytics-card__unit">
                    %
                </span>

            </div>

        </div>


        <section class="beatlab-analytics-panel">

            <div class="beatlab-analytics-panel__heading">

                <div>

                    <h2>
                        LP・フォームファネル
                    </h2>

                    <p>
                        サイト訪問から問い合わせ完了までの
                        到達状況と離脱率です。
                    </p>

                </div>

            </div>


            <?php if ($page_view === 0) : ?>

                <div class="beatlab-analytics-empty">

                    <span
                        class="dashicons dashicons-chart-bar"
                    ></span>

                    <p>
                        選択した期間には
                        分析データがありません。
                    </p>

                </div>

            <?php else : ?>

                <div class="beatlab-funnel">

                    <?php
                    foreach (
                        $stages
                        as $stage
                    ) :

                        $width =
                            drum_school_calculate_rate(
                                $stage['count'],
                                $page_view
                            );

                        ?>

                        <div class="beatlab-funnel__row">

                            <div class="beatlab-funnel__meta">

                                <span class="beatlab-funnel__label">

                                    <?php
                                    echo esc_html(
                                        $stage['label']
                                    );
                                    ?>

                                </span>


                                <div class="beatlab-funnel__numbers">

                                    <strong>
                                        <?php
                                        echo esc_html(
                                            number_format(
                                                $stage['count']
                                            )
                                        );
                                        ?>
                                    </strong>

                                    <span>
                                        件
                                    </span>


                                    <?php
                                    if (
                                        $stage['dropoff']
                                        !== null
                                    ) :
                                        ?>

                                        <span class="beatlab-funnel__dropoff">

                                            離脱
                                            <?php
                                            echo esc_html(
                                                number_format(
                                                    $stage['dropoff'],
                                                    1
                                                )
                                            );
                                            ?>%

                                        </span>

                                    <?php endif; ?>

                                </div>

                            </div>


                            <div class="beatlab-funnel__track">

                                <div
                                    class="beatlab-funnel__bar"
                                    style="width: <?php
                                    echo esc_attr(
                                        $width
                                    );
                                    ?>%;"
                                ></div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>


        <section class="beatlab-analytics-panel">

            <div class="beatlab-analytics-panel__heading">

                <div>

                    <h2>
                        STEP別詳細
                    </h2>

                    <p>
                        LP訪問から問い合わせ完了までの
                        詳細データです。
                    </p>

                </div>

            </div>


            <div class="beatlab-analytics-table-wrap">

                <table class="beatlab-analytics-table">

                    <thead>

                        <tr>

                            <th>
                                項目
                            </th>

                            <th>
                                到達数
                            </th>

                            <th>
                                サイト訪問に対する到達率
                            </th>

                            <th>
                                前段階からの離脱率
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php
                        foreach (
                            $stages
                            as $stage
                        ) :

                            $reach_rate =
                                drum_school_calculate_rate(
                                    $stage['count'],
                                    $page_view
                                );

                            ?>

                            <tr>

                                <td>
                                    <?php
                                    echo esc_html(
                                        $stage['label']
                                    );
                                    ?>
                                </td>


                                <td>

                                    <strong>
                                        <?php
                                        echo esc_html(
                                            number_format(
                                                $stage['count']
                                            )
                                        );
                                        ?>
                                    </strong>

                                    件

                                </td>


                                <td>
                                    <?php
                                    echo esc_html(
                                        number_format(
                                            $reach_rate,
                                            1
                                        )
                                    );
                                    ?>%
                                </td>


                                <td>

                                    <?php
                                    if (
                                        $stage['dropoff']
                                        === null
                                    ) {

                                        echo '—';

                                    } else {

                                        echo esc_html(
                                            number_format(
                                                $stage['dropoff'],
                                                1
                                            )
                                        );

                                        echo '%';

                                    }
                                    ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </section>


        <p class="beatlab-analytics__privacy">

            ※ 本ダッシュボードでは、
            匿名セッションIDと行動イベントのみを集計しています。
            氏名・メールアドレス・電話番号などの
            個人情報は保存していません。

        </p>

    </div>

    <?php
}