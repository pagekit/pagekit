var _ = Vue.util;
var templateParser = Vue.parsers.template;
var vIf = Vue.directive('if');
var compiler = Vue.compiler;

module.exports = {

    isLiteral: true,

    // same logic reuse from v-if
    compile: vIf.compile,
    teardown: vIf.teardown,
    getContainedComponents: vIf.getContainedComponents,
    unbind: vIf.unbind,

    bind: function () {
        var el = this.el;
        this.start = document.createComment('v-partial-start');
        this.end = document.createComment('v-partial-end');
        if (el.nodeType !== 8) {
            el.innerHTML = '';
        }
        if (el.tagName === 'TEMPLATE' || el.nodeType === 8) {
            _.replace(el, this.end);
        } else {
            el.appendChild(this.end);
        }
        _.before(this.start, this.end);
        if (!this._isDynamicLiteral) {
            this.insert(this.expression);
        }
    },

    update: function (id) {
        this.teardown();
        this.insert(id);
    },

    insert: function (id) {

        var partial = getPartials(this.vm)[id];

        if (!this.vm.$options.partials) {
            this.vm.$options.partials = {};
        }

        if (undefined !== id && !partial) {
            this.vm.$options.partials[id] = partial = templateParser.parse(id);
        }

        if (partial) {
            var filters = this.filters && this.filters.read;
            if (filters) {
                partial = _.applyFilters(partial, filters, this.vm);
            }

            this.template = templateParser.parse(partial, true);

            // compile the nested partial
            this.linker = compiler.compile(
                this.template,
                this.vm.$options,
                true
            );

            this.compile();
        }
    }

};

function getPartials(vm) {

    var partials = {};

    do {

        partials = defaults(partials, vm.$options.partials || {});

        vm = vm.$parent;

    } while (vm);

    return defaults(partials, Vue.partials);
}

function defaults(target, source) {

    return _.extend(_.extend({}, source), target)
}
