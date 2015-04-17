Vue.component('widgets-index', {

    template: '#widget-index-tmpl',

    data: function() {
        return _.extend({
            search: '',
            widgets: [],
            selected: [],
            sorted: {},
            widget: null
        }, window.$widgets)
    },

    created: function () {
        this.Widgets = this.$resource('api/widget/:id');
        this.$watch('search', _.debounce(this.load, 200), false, true);
    },

    ready: function() {
        this.modal = UIkit.modal(this.$$.modal);
        this.modal.on('hide.uk.modal', this.cancel);
    },

    computed: {

        positionOptions: function() {
            return [{ text: this.$trans('- Assign -'), value: '' }].concat(
                _.map(this.positions, function(position) { return { text: this.$trans(position.name), value: position.id };}.bind(this))
            );
        }

    },

    methods: {

        add: function(type) {
            this.edit({ type: type.id });
        },

        edit: function(widget) {
            var edit = _.extend({}, widget);

            this.$set('editUrl', this.$url('admin/widgets/edit', (widget.id ? { id: widget.id } : { type: widget.type })));

            this.modal.show();
        },

        cancel: function() {
            if (this.modal) {
                this.modal.hide();
            }

            this.editUrl = null;

            this.load();
        },

        load: function() {

            var self = this;

            this.Widgets.query((this.search ? { search: this.search } : {}), function (widgets) {
                self.$set('selected', []);
                self.$set('widgets', widgets);
                self.$set('sorted', _.groupBy(self.widgets, 'position'));
            });
        },

        status: function(status) {

            var widgets = this.getSelected();

            widgets.forEach(function(widget) {
                widget.status = status;
            });

            this.Widgets.save({ id: 'bulk' }, { widgets: widgets }, this.load);
        },

        copy: function() {

            var widgets = _.merge([], this.getSelected());

            widgets.forEach(function(widget) {
                delete widget.id;
            });

            this.Widgets.save({ id: 'bulk' }, { widgets: widgets }, this.load);
        },

        remove: function() {
            this.Widgets.delete({ id: 'bulk' }, { ids: this.selected }, this.load);
        },

        getSelected: function() {
            return this.widgets.filter(function(widgets) {
                return this.selected.indexOf(widgets.id.toString()) !== -1;
            }.bind(this));
        },

        save: function (widget) {
            var self = this;
            _.defer(function() {
                self.Widgets.save({ id: widget.id }, { widget: widget }, self.load)
            });
        },

        preventSubmit: function(e) {
            if (e.keyCode == '13') {
                e.preventDefault()
            }
        }

    },

    filters: {

        assignable: function() {
            return this.positions.concat([{ id: '', name: this.$trans('Unassigned Widgets') }]);
        }

    },

    components: {

        'widget-list': {

            inherit: true,

            ready: function() {
                var self = this;
                UIkit.nestable(this.$el, { maxDepth: 1, group: 'widgets' }).element.on('change.uk.nestable', function (e, el, type, root, nestable) {
                    if (type !== 'removed' && e.target.tagName == 'UL') {
                        self.Widgets.save({ id: 'updateOrder' }, { position: self.position.id, widgets: nestable.list() }, self.load);
                    }
                });
            }

        },

        'widget-item': {

            inherit: true,

            ready: function() {
                UIkit.formSelect(this.$$.select, { target: 'a' });
            },

            computed: {

                type: function() {
                    return _.find(this.types, { id: this.widget.type });
                },

                typeName: function() {
                    return this.type ? this.type.name : this.$trans('Extension not loaded');
                }

            },

            methods: {

                toggleStatus: function () {
                    this.widget.status = !!this.widget.status ? 0 : 1;
                    this.save(this.widget);
                }

            }

        }

    }

});
