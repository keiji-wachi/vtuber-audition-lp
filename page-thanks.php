<?php
/**
 * Thanks Page
 *
 * Contact Form 7の送信完了後に表示する
 * 独立したサンクスページ。
 *
 * URL想定：
 * /thanks/
 */

get_header();
?>

<main class="thanks-page">

    <section class="thanks">

        <div class="thanks__inner">

            <!-- =========================
                 Status
            ========================== -->
            <div
                class="thanks__status"
                aria-hidden="true"
            >
                <span class="thanks__status-icon">
                    ✓
                </span>
            </div>


            <!-- =========================
                 Heading
            ========================== -->
            <div class="thanks__heading">

                <p class="thanks__label">
                    THANK YOU
                </p>

                <h1 class="thanks__title">
                    お申し込み<br class="thanks__sp-break">
                    ありがとうございます。
                </h1>

                <p class="thanks__lead">
                    無料体験・相談のお申し込みを受け付けました。
                </p>

            </div>


            <!-- =========================
                 Message
            ========================== -->
            <div class="thanks__message">

                <p>
                    ご入力いただいたメールアドレス宛に、
                    受付確認メールをお送りしています。
                </p>

                <p>
                    内容を確認後、担当スタッフより
                    ご連絡いたします。
                </p>

            </div>


            <!-- =========================
                 Flow
            ========================== -->
            <div class="thanks-flow">

                <div class="thanks-flow__heading">

                    <p class="thanks-flow__label">
                        NEXT STEP
                    </p>

                    <h2 class="thanks-flow__title">
                        今後の流れ
                    </h2>

                </div>


                <ol class="thanks-flow__list">

                    <li class="thanks-flow__item">

                        <span class="thanks-flow__number">
                            01
                        </span>

                        <div class="thanks-flow__content">

                            <strong>
                                お申し込み受付
                            </strong>

                            <p>
                                受付確認メールを自動送信しています。
                            </p>

                        </div>

                    </li>


                    <li class="thanks-flow__item">

                        <span class="thanks-flow__number">
                            02
                        </span>

                        <div class="thanks-flow__content">

                            <strong>
                                担当スタッフよりご連絡
                            </strong>

                            <p>
                                お申し込み内容を確認後、
                                スタッフよりご連絡します。
                            </p>

                        </div>

                    </li>


                    <li class="thanks-flow__item">

                        <span class="thanks-flow__number">
                            03
                        </span>

                        <div class="thanks-flow__content">

                            <strong>
                                日程調整
                            </strong>

                            <p>
                                ご希望を確認しながら
                                体験レッスンの日程を調整します。
                            </p>

                        </div>

                    </li>


                    <li class="thanks-flow__item">

                        <span class="thanks-flow__number">
                            04
                        </span>

                        <div class="thanks-flow__content">

                            <strong>
                                無料体験レッスン
                            </strong>

                            <p>
                                BEATLABのレッスンを
                                実際に体験していただきます。
                            </p>

                        </div>

                    </li>

                </ol>

            </div>


            <!-- =========================
                 Notice
            ========================== -->
            <div class="thanks__notice">

                <p>
                    受付確認メールが届かない場合は、
                    迷惑メールフォルダもご確認ください。
                </p>

            </div>


            <!-- =========================
                 Action
            ========================== -->
            <div class="thanks__actions">

                <a
                    href="<?php echo esc_url(home_url('/')); ?>"
                    class="button button--primary thanks__button"
                >
                    TOPページへ戻る
                    <span aria-hidden="true">
                        →
                    </span>
                </a>

            </div>

        </div>

    </section>

</main>

<?php
get_footer();