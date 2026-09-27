/**
 * Mobile Header Navigation
 *
 * Tablet / Smartphoneで使用する
 * ハンバーガーメニューを制御する。
 *
 * - メニュー開閉
 * - aria-expanded更新
 * - ☰ / × 切り替え
 * - メニュー選択時に閉じる
 * - Escapeで閉じる
 * - メニュー展開中のスクロール停止
 * - PC幅へ戻った場合の状態リセット
 */

document.addEventListener(
    "DOMContentLoaded",
    () => {

        const header =
            document.querySelector(
                ".site-header"
            );


        const menuButton =
            document.querySelector(
                ".site-menu-button"
            );


        const navigation =
            document.querySelector(
                ".site-nav"
            );


        if (
            !header
            ||
            !menuButton
            ||
            !navigation
        ) {
            return;
        }


        const navigationLinks =
            navigation.querySelectorAll(
                "a"
            );


        /* ==================================================
           Open Menu
        ================================================== */

        const openMenu = () => {

            menuButton.setAttribute(
                "aria-expanded",
                "true"
            );


            menuButton.setAttribute(
                "aria-label",
                "メニューを閉じる"
            );


            navigation.classList.add(
                "is-open"
            );


            document.body.classList.add(
                "is-menu-open"
            );

        };


        /* ==================================================
           Close Menu
        ================================================== */

        const closeMenu = () => {

            menuButton.setAttribute(
                "aria-expanded",
                "false"
            );


            menuButton.setAttribute(
                "aria-label",
                "メニューを開く"
            );


            navigation.classList.remove(
                "is-open"
            );


            document.body.classList.remove(
                "is-menu-open"
            );

        };


        /* ==================================================
           Toggle
        ================================================== */

        menuButton.addEventListener(
            "click",
            () => {

                const isOpen =
                    menuButton.getAttribute(
                        "aria-expanded"
                    )
                    ===
                    "true";


                if (isOpen) {

                    closeMenu();

                    return;
                }


                openMenu();

            }
        );


        /* ==================================================
           Navigation Link
        ================================================== */

        navigationLinks.forEach(
            (link) => {

                link.addEventListener(
                    "click",
                    () => {

                        closeMenu();

                    }
                );

            }
        );


        /* ==================================================
           Escape
        ================================================== */

        document.addEventListener(
            "keydown",
            (event) => {

                if (
                    event.key
                    !==
                    "Escape"
                ) {
                    return;
                }


                const isOpen =
                    menuButton.getAttribute(
                        "aria-expanded"
                    )
                    ===
                    "true";


                if (!isOpen) {
                    return;
                }


                closeMenu();


                menuButton.focus();

            }
        );


        /* ==================================================
           Resize
        ================================================== */

        window.addEventListener(
            "resize",
            () => {

                /*
                 * PC幅へ戻った場合、
                 * モバイルメニューの状態を
                 * リセットする。
                 */
                if (
                    window.innerWidth
                    > 900
                ) {

                    closeMenu();

                }

            }
        );

    }
);