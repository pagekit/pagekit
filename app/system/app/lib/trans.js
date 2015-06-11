module.exports = function (Vue) {

    var formats = ['full', 'long', 'medium', 'short'];

    Vue.prototype.$date = function(date, format) {

        var options = format;

        if (typeof date == 'string') {
            date = new Date(date);
        }

        if (typeof options == 'string') {
            if (formats.indexOf(format) != -1) {
                options = {date: format};
            } else {
                options = {skeleton: format};
            }
        }

        return Globalize.formatDate(date, options);
    };

    Vue.prototype.$trans = Globalize.trans;
    Vue.prototype.$transChoice = Globalize.transChoice;

};
