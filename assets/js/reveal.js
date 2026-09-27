/**
 * Scroll Reveal
 *
 * IntersectionObserverを使用して、
 * 画面内へ入った要素へ
 * is-visibleクラスを付与する。
 *
 * HTML側：
 *
 * data-reveal="up"
 *
 * stagger表示：
 *
 * data-reveal-delay="100"
 *
 * のように指定する。
 */

document.addEventListener(
    "DOMContentLoaded",
    () => {

        const revealElements =
            document.querySelectorAll(
                "[data-reveal]"
            );


        /*
         * Reveal対象がないページでは
         * 何もしない。
         */
        if (!revealElements.length) {
            return;
        }


        /* ==================================================
           Reduced Motion
        ================================================== */

        const prefersReducedMotion =
            window.matchMedia(
                "(prefers-reduced-motion: reduce)"
            ).matches;


        /*
         * アニメーション軽減設定の場合は
         * 最初からすべて表示する。
         */
        if (prefersReducedMotion) {

            revealElements.forEach(
                (element) => {

                    element.classList.add(
                        "is-visible"
                    );

                }
            );


            return;
        }


        /* ==================================================
           Setup
        ================================================== */

        /*
         * JSが正常に起動した場合のみ
         * CSS側の初期非表示を有効化する。
         */
        document
            .documentElement
            .classList
            .add("reveal-ready");


        revealElements.forEach(
            (element) => {

                /*
                 * HTMLの
                 *
                 * data-reveal-delay="100"
                 *
                 * をCSS変数へ渡す。
                 */
                const delay =
                    element.dataset
                        .revealDelay;


                if (delay) {

                    element.style
                        .setProperty(
                            "--reveal-delay",
                            `${delay}ms`
                        );

                }

            }
        );


        /* ==================================================
           Observer
        ================================================== */

        const observer =
            new IntersectionObserver(
                (entries) => {

                    entries.forEach(
                        (entry) => {

                            if (
                                !entry.isIntersecting
                            ) {
                                return;
                            }


                            entry
                                .target
                                .classList
                                .add(
                                    "is-visible"
                                );


                            /*
                             * 一度表示されたら
                             * 監視終了。
                             *
                             * スクロール上下のたびに
                             * アニメーションさせない。
                             */
                            observer.unobserve(
                                entry.target
                            );

                        }
                    );

                },
                {
                    threshold: 0.15,

                    rootMargin:
                        "0px 0px -40px 0px",
                }
            );


        revealElements.forEach(
            (element) => {

                observer.observe(
                    element
                );

            }
        );

    }
);