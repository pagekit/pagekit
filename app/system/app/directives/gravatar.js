var md5 = require('md5');

module.exports = {

    _cache: {},

    update: function (value) {

        var el = $(this.el), url = '//gravatar.com/avatar/', size = (el.attr('height') || 50), params = [];

        params.push('r=g');
        params.push('d=mm');
        params.push('s=' + (size * 2));
        params.push('d=404');

        url += md5(value) + '?' + params.join('&');

        // load image url from cache if exists
        if (this._cache[url]) {
            return el.attr('src', this._cache[url]);
        }

        var img = new Image();

        el.addClass('uk-invisible');

        img.onload = function() {
            this._cache[url] = url;
            el.attr('src', url).removeClass('uk-invisible');
        }.bind(this);

        img.onerror = function() {
            this._cache[url] = this.letterAvatar(el.attr('title') || el.attr('alt'), size, el.attr('colored'));
            el.attr('src', this._cache[url]).removeClass('uk-invisible');
        }.bind(this);

        img.src = url;
    },

    letterAvatar: function(name, size, colored) {
        name  = name || '';
        size  = size || 60;

        var colours = [
                "#1abc9c", "#2ecc71", "#3498db", "#9b59b6", "#34495e", "#16a085", "#27ae60", "#2980b9", "#8e44ad", "#2c3e50",
                "#f1c40f", "#e67e22", "#e74c3c", "#ecf0f1", "#95a5a6", "#f39c12", "#d35400", "#c0392b", "#bdc3c7", "#7f8c8d"
            ],

            nameSplit = String(name).toUpperCase().split(' '),
            initials, charIndex, colourIndex, canvas, context, dataURI;


        if (nameSplit.length == 1) {
            initials = nameSplit[0] ? nameSplit[0].charAt(0):'?';
        } else {
            initials = nameSplit[0].charAt(0) + nameSplit[1].charAt(0);
        }

        if (window.devicePixelRatio) {
            size = (size * window.devicePixelRatio);
        }

        charIndex     = (initials == '?' ? 72 : initials.charCodeAt(0)) - 64;
        colourIndex   = charIndex % 20;
        canvas        = document.createElement('canvas');
        canvas.width  = size;
        canvas.height = size;
        context       = canvas.getContext("2d");

        context.fillStyle = colored ? colours[colourIndex - 1] : '#cfd2d7';
        context.fillRect (0, 0, canvas.width, canvas.height);
        context.font = Math.round(canvas.width/2)+"px Arial";
        context.textAlign = "center";
        context.fillStyle = "#FFF";
        context.fillText(initials, size / 2, size / 1.5);

        dataURI = canvas.toDataURL();
        canvas  = null;

        return dataURI;
    }

};
