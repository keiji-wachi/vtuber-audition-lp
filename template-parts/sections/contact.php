<section
    id="contact"
    class="contact section"
>

    <div class="section__inner">

        <!-- =========================
             Section Heading
        ========================== -->
        <div class="section-heading">

            <p class="section-heading__label">
                FREE TRIAL
            </p>

            <h2 class="section-heading__title">
                無料体験・相談のお申し込み
            </h2>

            <p class="section-heading__text">
                約2分で完了します。<br>
                まずはあなたの希望を教えてください。
            </p>

        </div>


        <!-- =========================
             Contact Form 7
        ========================== -->

        <!--
            Contact Form 7を送信基盤として使用。

            現在は基本フォームのみ実装。

            後続Issueで、
            1つのContact Form 7フォームを
            JavaScriptで5STEP表示へ拡張する。

            STEP 1：ご相談内容
            STEP 2：経験・目的
            STEP 3：希望レッスン
            STEP 4：希望日時
            STEP 5：連絡先・個人情報同意

            最終送信処理・メール通知は
            Contact Form 7側で行う。
        -->
        <div class="contact-form">

            <?php
            echo do_shortcode(
                '[contact-form-7 id="fd0a211" title="無料体験・相談フォーム"]'
            );
            ?>

        </div>

    </div>

</section>