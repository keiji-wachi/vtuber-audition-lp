/**
 * FAQ Accordion
 *
 * FAQの開閉状態を制御する。
 *
 * - aria-expanded更新
 * - hidden属性更新
 * - 高さアニメーション
 * - 一度に1項目のみ開く
 * - prefers-reduced-motion対応
 *
 * button要素を使用しているため、
 * Enter / Spaceによるキーボード操作は
 * ブラウザ標準で利用できる。
 */

document.addEventListener(
    "DOMContentLoaded",
    () => {

        const questions =
            Array.from(
                document.querySelectorAll(
                    ".faq-item__question"
                )
            );


        if (!questions.length) {
            return;
        }


        /* ==================================================
           Reduced Motion
        ================================================== */

        const prefersReducedMotion =
            window.matchMedia(
                "(prefers-reduced-motion: reduce)"
            );


        /* ==================================================
           Get Answer
        ================================================== */

        const getAnswer = (question) => {

            const answerId =
                question.getAttribute(
                    "aria-controls"
                );


            if (!answerId) {
                return null;
            }


            return document.getElementById(
                answerId
            );

        };


        /* ==================================================
           Open
        ================================================== */

        const openAnswer = (
            question,
            answer
        ) => {

            question.setAttribute(
                "aria-expanded",
                "true"
            );


            /*
             * motion軽減設定では
             * 即時表示する。
             */
            if (
                prefersReducedMotion.matches
            ) {

                answer.hidden =
                    false;

                answer.style.height =
                    "auto";

                return;
            }


            /*
             * hiddenを先に解除してから
             * 高さ0 → 実際の高さへ変化させる。
             */
            answer.hidden =
                false;


            answer.style.height =
                "0px";


            /*
             * ブラウザへ0px状態を
             * 一度確定させる。
             */
            answer.offsetHeight;


            const targetHeight =
                answer.scrollHeight;


            answer.style.height =
                `${targetHeight}px`;


            const handleOpened = (
                event
            ) => {

                if (
                    event.propertyName
                    !==
                    "height"
                ) {
                    return;
                }


                answer.style.height =
                    "auto";


                answer.removeEventListener(
                    "transitionend",
                    handleOpened
                );

            };


            answer.addEventListener(
                "transitionend",
                handleOpened
            );

        };


        /* ==================================================
           Close
        ================================================== */

        const closeAnswer = (
            question,
            answer
        ) => {

            question.setAttribute(
                "aria-expanded",
                "false"
            );


            if (answer.hidden) {
                return;
            }


            /*
             * motion軽減設定では
             * 即時非表示。
             */
            if (
                prefersReducedMotion.matches
            ) {

                answer.hidden =
                    true;

                answer.style.height =
                    "0px";

                return;
            }


            /*
             * height:auto では
             * transitionできないため、
             * 現在の実高さをpxでセットする。
             */
            answer.style.height =
                `${answer.scrollHeight}px`;


            /*
             * 現在高さをブラウザへ確定。
             */
            answer.offsetHeight;


            /*
             * 0まで縮める。
             */
            answer.style.height =
                "0px";


            const handleClosed = (
                event
            ) => {

                if (
                    event.propertyName
                    !==
                    "height"
                ) {
                    return;
                }


                /*
                 * 閉じている状態なら
                 * hiddenを付与する。
                 *
                 * 高速クリックなどで
                 * 再度開かれていた場合は
                 * hiddenにしない。
                 */
                if (
                    question.getAttribute(
                        "aria-expanded"
                    )
                    ===
                    "false"
                ) {

                    answer.hidden =
                        true;

                }


                answer.removeEventListener(
                    "transitionend",
                    handleClosed
                );

            };


            answer.addEventListener(
                "transitionend",
                handleClosed
            );

        };


        /* ==================================================
           Close Other Questions
        ================================================== */

        const closeOtherQuestions = (
            currentQuestion
        ) => {

            questions.forEach(
                (question) => {

                    if (
                        question
                        ===
                        currentQuestion
                    ) {
                        return;
                    }


                    const answer =
                        getAnswer(
                            question
                        );


                    if (!answer) {
                        return;
                    }


                    const isOpen =
                        question.getAttribute(
                            "aria-expanded"
                        )
                        ===
                        "true";


                    if (!isOpen) {
                        return;
                    }


                    closeAnswer(
                        question,
                        answer
                    );

                }
            );

        };


        /* ==================================================
           Click
        ================================================== */

        questions.forEach(
            (question) => {

                question.addEventListener(
                    "click",
                    () => {

                        const answer =
                            getAnswer(
                                question
                            );


                        if (!answer) {
                            return;
                        }


                        const isOpen =
                            question.getAttribute(
                                "aria-expanded"
                            )
                            ===
                            "true";


                        /*
                         * 開いている項目を
                         * クリックした場合は閉じる。
                         */
                        if (isOpen) {

                            closeAnswer(
                                question,
                                answer
                            );

                            return;
                        }


                        /*
                         * 他のFAQを閉じてから
                         * 選択項目を開く。
                         */
                        closeOtherQuestions(
                            question
                        );


                        openAnswer(
                            question,
                            answer
                        );

                    }
                );

            }
        );

    }
);