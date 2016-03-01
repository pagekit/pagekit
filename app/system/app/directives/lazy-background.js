module.exports = {

    update: function (value) {

        var el = $(this.el), img = new Image();

        img.onload = function() {
            el.css('background-image', "url('"+value+"')");
        };

        img.src = value;
    }

};
