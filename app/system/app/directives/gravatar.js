var md5 = require('md5');

module.exports = {

    priority: 100,

    params: ['colored'],

    update: function (value) {

        var el = this.el, vm = this, cache = this.vm.$session, img = new Image(),
            name = this.el.getAttribute('title') || this.el.getAttribute('alt'),
            colored = this.params.colored,
            size = this.el.getAttribute('height') || 50,
            url = '//gravatar.com/avatar/' + md5(value) + '?' + ['r=g', 's=' + (size * 2)].join('&'),
            key = 'gravatar.' + [url, colored, name, size].join('.');

        // load image url from cache if exists
        if (cache[key]) {
            el.setAttribute('src', cache[key]);
            return;
        }

        cache[key] = vm.letterAvatar(name, size, colored);
        el.setAttribute('src', cache[key]);

        if (img.crossOrigin !== undefined) {

            img.crossOrigin = 'anonymous';
            url += '&d=blank';
            img.onload = function () {
                cache[key] = vm.letterAvatar(name, size, colored, img);
                el.setAttribute('src', cache[key]);
                el.classList.remove('uk-invisible');
            };

        } else {

            // IE Fallback (no CORS support for img):
            url += '&d=404';
            img.onload = function () {
                delete cache[key]; // remove dummy image from cache
                el.setAttribute('src', url);
                el.classList.remove('uk-invisible');
            };

        }

        img.src = url;
    },

    letterAvatar: function (name, size, colored, img) {
        name = name || '';
        size = size || 60;

        var colours = [
                "#1abc9c", "#2ecc71", "#3498db", "#9b59b6", "#34495e", "#16a085", "#27ae60", "#2980b9", "#8e44ad", "#2c3e50",
                "#f1c40f", "#e67e22", "#e74c3c", "#ecf0f1", "#95a5a6", "#f39c12", "#d35400", "#c0392b", "#bdc3c7", "#7f8c8d"
            ],

            nameSplit = String(name).toUpperCase().split(' '),
            initials, charIndex, colourIndex, canvas, context, dataURI;


        if (nameSplit.length == 1) {
            initials = nameSplit[0] ? nameSplit[0].charAt(0) : '?';
        } else {
            initials = nameSplit[0].charAt(0) + nameSplit[1].charAt(0);
        }

        if (window.devicePixelRatio) {
            size = (size * window.devicePixelRatio);
        }

        charIndex = (initials == '?' ? 72 : initials.charCodeAt(0)) - 64;
        colourIndex = charIndex % 20;
        canvas = document.createElement('canvas');
        canvas.width = size;
        canvas.height = size;
        context = canvas.getContext("2d");

        context.fillStyle = colored ? colours[colourIndex - 1] : '#cfd2d7';
        context.fillRect(0, 0, canvas.width, canvas.height);
        context.font = Math.round(canvas.width / 2) + "px Arial";
        context.textAlign = "center";
        context.fillStyle = "#FFF";
        context.fillText(initials, size / 2, size / 1.5);

        if (img) {
            context.drawImage(img, 0, 0, size, size);
        }

        dataURI = canvas.toDataURL();
        canvas = null;

        return dataURI;
    }

};
