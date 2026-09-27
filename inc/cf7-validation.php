<?php

/**
 * Contact Form 7 Custom Validation
 *
 * BEATLABの5STEPフォームで使用する
 * サーバー側バリデーション。
 *
 * JavaScript側の入力チェックだけでは、
 * JS無効化・直接POSTなどで回避できるため、
 * 最終的な入力チェックをPHP / CF7側でも行う。
 */


/* ==================================================
   Phone Validation
================================================== */

/**
 * 電話番号フィールドのカスタムバリデーション。
 *
 * 現在のフォーム仕様：
 *
 * - 希望連絡方法 = メール
 *   → 電話番号は任意
 *
 * - 希望連絡方法 = 電話
 *   → 電話番号は必須
 *
 * また、電話番号が入力されている場合は
 * 日本国内の一般的な10〜11桁形式か確認する。
 */
function drum_school_validate_phone(
    $result,
    $tag
) {

    /*
     * 他のtelフィールドには影響させない。
     *
     * 今回対象なのは
     * [tel your-tel]
     * のみ。
     */
    if (
        'your-tel' !== $tag->name
    ) {
        return $result;
    }


    /* =========================
       Contact Method
    ========================== */

    $contact_method = '';

    if (
        isset(
            $_POST['contact-method']
        )
    ) {

        $contact_method =
            sanitize_text_field(
                wp_unslash(
                    $_POST['contact-method']
                )
            );
    }


    /* =========================
       Telephone
    ========================== */

    $telephone = '';

    if (
        isset(
            $_POST['your-tel']
        )
    ) {

        $telephone =
            sanitize_text_field(
                wp_unslash(
                    $_POST['your-tel']
                )
            );
    }


    /*
     * 「電話」を希望しているのに
     * 電話番号が空の場合はエラー。
     */
    if (
        '電話' === $contact_method
        && '' === trim($telephone)
    ) {

        $result->invalidate(
            $tag,
            '電話連絡をご希望の場合は電話番号を入力してください。'
        );

        return $result;
    }


    /*
     * 電話番号が未入力で、
     * 連絡方法もメールなら問題なし。
     */
    if (
        '' === trim($telephone)
    ) {
        return $result;
    }


    /* =========================
       Phone Format
    ========================== */

    /*
     * 判定時のみ
     * ハイフン・半角スペースを除去する。
     *
     * 例：
     *
     * 09012345678
     * 090-1234-5678
     *
     * の両方を許可する。
     */
    $normalized_phone =
        preg_replace(
            '/[-\s]/',
            '',
            $telephone
        );


    /*
     * 日本国内の電話番号として、
     *
     * ・0から始まる
     * ・10〜11桁
     *
     * を基本条件とする。
     */
    if (
        ! preg_match(
            '/^0\d{9,10}$/',
            $normalized_phone
        )
    ) {

        $result->invalidate(
            $tag,
            '正しい電話番号を入力してください。'
        );

        return $result;
    }


    return $result;
}


/*
 * 現在のフィールドは
 *
 * [tel your-tel]
 *
 * なので wpcf7_validate_tel が対象。
 */
add_filter(
    'wpcf7_validate_tel',
    'drum_school_validate_phone',
    20,
    2
);


add_filter(
    'wpcf7_validate_tel*',
    'drum_school_validate_phone',
    20,
    2
);