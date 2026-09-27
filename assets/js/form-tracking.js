/**
 * BEATLAB Drum School
 * Form Tracking
 *
 * - LP訪問計測
 * - UTMパラメータ取得
 * - referrer取得
 * - landing URL取得
 * - Contact Form 7 hidden fieldへ設定
 * - dataLayerイベント送信
 * - フォームSTEP進捗計測
 * - WordPress DBへの匿名イベント保存
 * - 管理画面プレビュー除外
 * - 二重発火防止
 */

document.addEventListener("DOMContentLoaded", () => {

    const form =
        document.querySelector(
            ".multi-step-form"
        );

    if (!form) {
        return;
    }


    /* ==================================================
       Admin Preview
    ================================================== */

    const urlParams =
        new URLSearchParams(
            window.location.search
        );


    /**
     * WordPress管理画面内の
     * サイトプレビュー判定。
     */
    const isAdminPreview =
        urlParams.get(
            "admin_preview"
        ) === "1";


    /* ==================================================
       dataLayer
    ================================================== */

    window.dataLayer =
        window.dataLayer || [];


    const firedEvents =
        new Set();


    /* ==================================================
       Analytics Session
    ================================================== */

    const SESSION_STORAGE_KEY =
        "beatlab_form_session_id";


    const createSessionId =
        () => {

            if (
                window.crypto &&
                typeof window.crypto.randomUUID ===
                    "function"
            ) {

                return window.crypto.randomUUID();

            }


            return [
                Date.now().toString(36),
                Math.random()
                    .toString(36)
                    .substring(2),
                Math.random()
                    .toString(36)
                    .substring(2),
            ].join("-");
        };


    const getSessionId =
        () => {

            let sessionId =
                sessionStorage.getItem(
                    SESSION_STORAGE_KEY
                );


            if (!sessionId) {

                sessionId =
                    createSessionId();


                sessionStorage.setItem(
                    SESSION_STORAGE_KEY,
                    sessionId
                );

            }


            return sessionId;
        };


    const analyticsSessionId =
        getSessionId();


    /* ==================================================
       WordPress Analytics
    ================================================== */

    const sendEventToWordPress =
        (eventName) => {

            /**
             * 管理画面プレビューからは
             * 一切保存しない。
             */
            if (isAdminPreview) {
                return;
            }


            if (
                typeof drumSchoolAnalyticsConfig ===
                    "undefined"
            ) {
                return;
            }


            if (
                !drumSchoolAnalyticsConfig.ajaxUrl ||
                !drumSchoolAnalyticsConfig.nonce
            ) {
                return;
            }


            const body =
                new FormData();


            body.append(
                "action",
                "drum_school_track_form_event"
            );


            body.append(
                "nonce",
                drumSchoolAnalyticsConfig.nonce
            );


            body.append(
                "session_id",
                analyticsSessionId
            );


            body.append(
                "event_name",
                eventName
            );


            if (
                navigator.sendBeacon
            ) {

                navigator.sendBeacon(
                    drumSchoolAnalyticsConfig.ajaxUrl,
                    body
                );

                return;
            }


            fetch(
                drumSchoolAnalyticsConfig.ajaxUrl,
                {
                    method:
                        "POST",

                    body,

                    credentials:
                        "same-origin",

                    keepalive:
                        true,
                }
            ).catch(
                (error) => {

                    console.error(
                        "[BEATLAB Analytics Error]",
                        error
                    );

                }
            );
        };


    const pushEvent = (
        eventName,
        params = {}
    ) => {

        /**
         * 管理画面プレビューからは
         * GTM / GA4も含めて
         * 計測イベントを発火しない。
         */
        if (isAdminPreview) {
            return;
        }


        if (
            firedEvents.has(
                eventName
            )
        ) {
            return;
        }


        firedEvents.add(
            eventName
        );


        window.dataLayer.push({
            event:
                eventName,

            form_name:
                "beatlab_contact",

            ...params,
        });


        sendEventToWordPress(
            eventName
        );


        console.log(
            "[BEATLAB Tracking]",
            eventName,
            params
        );
    };


    /* ==================================================
       LP Page View
    ================================================== */

    pushEvent(
        "page_view",
        {
            page:
                "landing_page",
        }
    );


    /* ==================================================
       Tracking Parameters
    ================================================== */

    const trackingValues = {

        utm_source:
            urlParams.get(
                "utm_source"
            ) || "",

        utm_medium:
            urlParams.get(
                "utm_medium"
            ) || "",

        utm_campaign:
            urlParams.get(
                "utm_campaign"
            ) || "",

        utm_content:
            urlParams.get(
                "utm_content"
            ) || "",

        utm_term:
            urlParams.get(
                "utm_term"
            ) || "",

        referrer:
            document.referrer || "",

        landing_url:
            window.location.href,

    };


    Object.entries(
        trackingValues
    ).forEach(
        ([name, value]) => {

            const input =
                form.querySelector(
                    `[name="${name}"]`
                );

            if (!input) {
                return;
            }


            input.value =
                value;

        }
    );


    /* ==================================================
       form_start
    ================================================== */

    const handleFormStart =
        () => {

            pushEvent(
                "form_start",
                {
                    step:
                        1,

                    step_name:
                        "ご相談内容",
                }
            );

        };


    form.addEventListener(
        "click",
        handleFormStart,
        {
            once:
                true,
        }
    );


    form.addEventListener(
        "input",
        handleFormStart,
        {
            once:
                true,
        }
    );


    form.addEventListener(
        "change",
        handleFormStart,
        {
            once:
                true,
        }
    );


    /* ==================================================
       STEP Tracking
    ================================================== */

    const steps =
        Array.from(
            form.querySelectorAll(
                ".js-form-step"
            )
        );


    const getVisibleStep =
        () => {

            const visibleStep =
                steps.find(
                    (step) =>
                        !step.hidden
                );


            if (!visibleStep) {
                return null;
            }


            return Number(
                visibleStep.dataset.step
            );
        };


    let previousVisibleStep =
        getVisibleStep();


    const stepObserver =
        new MutationObserver(() => {

            const currentStep =
                getVisibleStep();


            if (
                currentStep === null ||
                previousVisibleStep === null
            ) {

                previousVisibleStep =
                    currentStep;

                return;
            }


            if (
                currentStep >
                previousVisibleStep
            ) {

                const completedStep =
                    previousVisibleStep;


                const completedElement =
                    steps.find(
                        (step) =>
                            Number(
                                step.dataset.step
                            ) ===
                            completedStep
                    );


                pushEvent(
                    `step_${completedStep}_complete`,
                    {
                        step:
                            completedStep,

                        step_name:
                            completedElement
                                ?.dataset
                                .stepName || "",
                    }
                );

            }


            previousVisibleStep =
                currentStep;

        });


    steps.forEach(
        (step) => {

            stepObserver.observe(
                step,
                {
                    attributes:
                        true,

                    attributeFilter:
                        ["hidden"],
                }
            );

        }
    );


    /* ==================================================
       STEP 5 Complete
    ================================================== */

    document.addEventListener(
        "wpcf7beforesubmit",
        (event) => {

            const cf7Form =
                event.target;


            if (
                !cf7Form ||
                !cf7Form.contains(
                    form
                )
            ) {
                return;
            }


            pushEvent(
                "step_5_complete",
                {
                    step:
                        5,

                    step_name:
                        "ご連絡先",
                }
            );

        }
    );


    /* ==================================================
       Submit Success
    ================================================== */

    document.addEventListener(
        "wpcf7mailsent",
        (event) => {

            const cf7Form =
                event.target;


            if (
                !cf7Form ||
                !cf7Form.contains(
                    form
                )
            ) {
                return;
            }


            pushEvent(
                "submit_success",
                {
                    step:
                        5,

                    conversion:
                        true,
                }
            );

        }
    );

});