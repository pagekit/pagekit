/*global $data*/

jQuery(function ($) {

    var vm = new Vue({

        el: '#js-widget-edit',

        mixins: [Vue.mixins['site-tree']],

        data: _.merge({

        }, $data),

        created: function () {
            this.Widgets = this.$resource('api/widget/:id');
        },

        ready: function() {
            UIkit.tab(this.$$.tab, { connect: this.$$.content });

            var h = -1;
            setInterval(function() {
                if (h != document.body.offsetHeight) {
                    h = window.frameElement.style.height = document.body.offsetHeight + 'px';
                }
            }, 120);

        },

        computed: {

            positionOptions: function() {
                return [{ text: this.$trans('- Assign -'), value: '' }].concat(
                    _.map(this.positions, function(position) { return { text: this.$trans(position.name), value: position.id };}.bind(this))
                );
            },

            type: function() {
                return _.find(this.types, { id: this.widget.type });
            },

            typeName: function() {
                return this.type ? this.type.name : this.$trans('Extension not loaded');
            }

        },

        methods: {

            cancel: function() {

                var e = document.createEvent('HTMLEvents');
                e.initEvent('close', true, false);
                window.frameElement.dispatchEvent(e);

            },

            save: function (e) {

                e.preventDefault();

                var data = _.merge($(":input", e.target).serialize().parse(), { widget: this.widget });

                this.$broadcast('save', data);

                this.Widgets.save({ id: this.widget.id }, data);

                this.cancel();

            }

        }

    });

});
