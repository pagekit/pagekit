Vue.component('widgets-index', {

    template: '#widget-index-tmpl',

    data: function() {

        return {
            search: '',
            positions: [],
            config: window.$widgets
        };

    },

    created: function() {
        this.Widgets = this.$resource('api/widget/:id');
        this.load();
    },

    ready: function() {
        this.modal = UIkit.modal(this.$$.modal);
        this.modal.on('hide.uk.modal', this.cancel);
    },

    computed: {

        positionOptions: function() {
            return [{ text: this.$trans('- Assign -'), value: '' }].concat(
                _.map(this.config.positions, function(position) { return { text: this.$trans(position.name), value: position.id };}.bind(this))
            );
        }

    },

    filters: {

        hasWidgets: function(position) {
            return position.widgets.filter(function(widget) { return this.applyFilter(widget) }.bind(this)).length;
        },

        showWidget: function(widget) {
            return this.applyFilter(widget);
        }

    },

    methods: {

        applyFilter: function(widget) {
            return !this.search || widget.title.toLowerCase().indexOf(this.search.toLowerCase()) !== -1;
        },

        load: function() {

            var self = this;

            this.Widgets.query({ grouped: true }, function (data) {
                self.$set('selected', []);

                var positions = self.config.positions.concat({ id: '', name: self.$trans('Unassigned Widgets')}).map(function(position) {
                    return _.extend({ widgets: data[position.id] || [] }, position);
                });

                self.$set('positions', positions);
            });
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

        reorder: function(position, widgets) {
            this.Widgets.save({ id: 'positions' }, { position: position, widgets: _.pluck(widgets, 'id') }, this.load);
        },

        getSelected: function() {
            return this.widgets.filter(function(widgets) {
                return this.selected.indexOf(widgets.id.toString()) !== -1;
            }.bind(this));
        },

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
        }

    },

    components: {

        'widget-list': {

            inherit: true,

            ready: function() {
                var self = this;

                UIkit.nestable(this.$el, { maxDepth: 1, group: 'widgets' }).element.on('change.uk.nestable', function (e, el, type, root, nestable) {
                    if (type !== 'removed' && e.target.tagName == 'UL') {
                        self.reorder(self.position.id, nestable.list());
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
                    return _.find(this.config.types, { id: this.widget.type });
                },

                typeName: function() {
                    return this.type ? this.type.name : this.$trans('Extension not loaded');
                }

            },

            methods: {

                reassign: function(e) {

                    e.preventDefault();
                    e.stopPropagation();

                    this.reorder(e.target.value, _.find(this.positions, {id : e.target.value }).widgets.concat(this.widget))

                }

            }

        }

    }

});
