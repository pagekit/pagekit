module.exports = {

    el: '#widget-edit',

    mixins: [window.Widgets],

    data: function () {
        return _.merge({form: {}, sections: [], active: 0}, window.$data);
    },

    created: function () {

        var sections = [], type = _.kebabCase(this.widget.type), active;

        _.forIn(this.$options.components, function (component, name) {

            var options = component.options || {};

            if (options.section) {
                sections.push(_.extend({name: name, priority: 0}, options.section));
            }

        });

        sections = _.sortBy(sections.filter(function (section) {

            active = section.name.match('(.+):(.+)');

            if (active === null) {
                return !_.find(sections, {name: type + ':' + section.name});
            }

            return active[1] == type;
        }, this), 'priority');

        this.$set('sections', sections);

    },

    ready: function () {

        var tab = UIkit.tab(this.$els.tab, {connect: this.$els.content});

        var vm = this;

        tab.on('change.uk.tab', function (tab, current) {
            vm.active = current.index();
        });

        this.$watch('active', function (active) {
            tab.switcher.show(active);
        });

        this.$state('active');

        // set position from get param
        if (!this.widget.id) {
            var match = new RegExp('[?&]position=([^&]*)').exec(location.search);
            this.widget.position = (match && decodeURIComponent(match[1].replace(/\+/g, ' '))) || '';
        }

    },

    methods: {

        save: function () {
            this.$broadcast('save', {widget: this.widget});
            this.$resource('api/site/widget{/id}').save({id: this.widget.id}, {widget: this.widget}).then(function (res) {

                var data = res.data;

                this.$dispatch('saved');

                if (!this.widget.id) {
                    window.history.replaceState({}, '', this.$url.route('admin/site/widget/edit', {id: data.widget.id}));
                }

                this.$set('widget', data.widget);

                this.$notify('Widget saved.');
            }, function (res) {
                this.$notify(res.data, 'danger');
            });
        },

        cancel: function () {
            this.$dispatch('cancel');
        }

    }

};

Vue.ready(module.exports);
