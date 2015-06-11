module.exports = function (Vue) {

    var partial = Vue.directive('partial'), insert = partial.insert;

    partial.insert = function(id) {

        var partial = this.vm.$options.partials[id];

        if (undefined === id || partial) {
            return insert.call(this, id);
        }

        var frag = Vue.parsers.template.parse(id);

        if (frag) {
            this.vm.$options.partials[id] = frag;
            return insert.call(this, id);
        }
    };

};
