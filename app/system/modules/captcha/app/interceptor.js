const config = window.$captcha;
let requestResolve, requestReject;

if (config.grecaptcha) {

    Vue.asset({
        js: ['https://www.google.com/recaptcha/api.js?onload=pagekit_onRecaptchaLoad&render=explicit']
    });

    let resolveLoad;
    const loadPromise = new Vue.Promise(resolve => {
        resolveLoad = resolve;
    });
    window.pagekit_onRecaptchaLoad = () => {
        let div = document.createElement('div');

        document.body.appendChild(div);

        grecaptcha.render(div, {
            sitekey: config.grecaptcha,
            callback: onSubmit,
            'expired-callback': onExpire,
            'error-callback': onError,
            size: 'invisible'
        });
        resolveLoad();
    };

    Vue.http.interceptors.push(() => {

        return {

            request: request => {
                if (!config.routes || request.method.toLowerCase() !== 'post' || !config.routes.some(route => {
                    const exp = new RegExp(route.replace(/{.+?}/, '.+?'));
                    return exp.test(request.url);
                })) {
                    return request;
                }

                return new Vue.Promise(
                    (resolve, reject) => {
                        requestResolve = (gRecaptchaResponse) => {
                            grecaptcha.reset();
                            request.data.gRecaptchaResponse = gRecaptchaResponse;
                            resolve(request);
                        };
                        requestReject = (error) => {
                            return reject({
                                data: error
                            });
                        };

                        loadPromise.then(() => grecaptcha.execute());
                    }
                )
            }

        };

    });

}

function onSubmit(gRecaptchaResponse) {
    requestResolve(gRecaptchaResponse);
}

function onExpire() {
    requestReject('reCAPTCHA expired. Please try again.');  // TODO: Translation
}

function onError() {
    requestReject('An error occured during reCAPTCHA execution. Please try again.'); // TODO: Translation
}
