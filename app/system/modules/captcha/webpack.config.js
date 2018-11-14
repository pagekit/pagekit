module.exports = [

    {
        entry: {
            "captcha-interceptor": "./app/interceptor.js"
        },
        output: {
            filename: "./app/bundle/[name].js",
            library: "Captcha"
        }
    }

];