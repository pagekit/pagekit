Vue.component('widgets-index', {

    template: '#widget-index-tmpl',

    data: function() {
        return _.extend({
            search: '',
            widgets: [],
            selected: [],
            widget: null
        }, window.$widgets)
    },

    created: function () {
        this.Widgets = this.$resource('api/widget/:id');
        this.$watch('search', _.debounce(this.load, 200), false, true);
    },

    ready: function() {

        var self = this;
        this.$.positions.forEach(function(pos) {

            UIkit.nestable(pos.$$.nestable, { maxDepth: 1, group: 'widgets' }).element.on('change.uk.nestable', function (e, el, type, root, nestable) {
                if (type !== 'removed' && e.target.tagName == 'UL') {
                    self.Widgets.save({ id: 'updateOrder' }, { position: pos.position.id, widgets: nestable.list() }, self.load);
                }
            });

        });

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
                self.$set('widgets', widgets);
                self.$set('selected', []);

                Vue.nextTick(function() {
                    self.$.positions.forEach(function(pos) {
                        pos.$.widgets.forEach(function(widget) {
                            UIkit.formSelect(widget.$$.select, { target: 'a' });
                        });
                    });
                })
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

        toggleStatus: function (widget) {
            widget.status = !!widget.status ? 0 : 1;
            this.save(widget);
        },

        save: function (widget) {
            var self = this;
            _.defer(function() {
                self.Widgets.save({ id: widget.id }, { widget: widget }, self.load)
            });
        },

        getType: function(type) {
            return _.find(this.types, { id: type });
        },

        getTypeName: function(type) {
            type = this.getType(type);
            return type ? type.name : this.$trans('Extension not loaded');
        },

        preventSubmit: function(e) {
            if (e.keyCode == '13') {
                e.preventDefault()
            }
        },

        test: function() {
            console.log('Test');
        }

    },

    filters: {

        position: function(widgets) {

            var self = this;

            if (this.position.id === '') {
                return widgets.filter(function(widget) {
                    return widget.position === '' || !_.some(self.positions, { id: widget.position });
                });
            }

            return _.filter(widgets, { position: this.position.id })
        },

        assignable: function() {
            return this.positions.concat([{ id: '', name: this.$trans('Unassigned Widgets') }]);
        }

    }

});
