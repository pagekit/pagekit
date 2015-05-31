<template>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>

            <div class="uk-button-dropdown" data-uk-dropdown="{ mode: 'click' }">
                <button class="uk-button uk-button-primary" type="button">{{ 'Add Widget' | trans }}</button>
                <div class="uk-dropdown uk-dropdown-small">
                    <ul class="uk-nav uk-nav-dropdown">
                        <li v-repeat="type: config.types"><a v-on="click: add(type)">{{ type.name }}</a></li>
                    </ul>
                </div>
            </div>

            <a class="uk-button pk-button-danger" v-show="selected.length" v-on="click: remove">{{ 'Delete' | trans }}</a>

            <div class="uk-button-dropdown" v-show="selected.length" data-uk-dropdown="{ mode: 'click' }">
                <button class="uk-button" type="button">{{ 'More' | trans }} <i class="uk-icon-caret-down"></i></button>
                <div class="uk-dropdown uk-dropdown-small">
                    <ul class="uk-nav uk-nav-dropdown">
                        <li><a v-on="click: copy">{{ 'Copy' | trans }}</a></li>
                    </ul>
                </div>
            </div>

        </div>
        <div data-uk-margin>
            <input type="text" placeholder="{{ 'Search' | trans }}" v-model="search" v-on="keypress: $event.preventDefault() | key enter" debounce="200">
        </div>
    </div>

    <div class="uk-overflow-container">

        <div class="pk-table-fake pk-table-fake-header pk-table-fake-border">
            <div class="pk-table-width-minimum"><input type="checkbox"  v-check-all="selected: input[name=id]"></div>
            <div class="pk-table-min-width-100">{{ 'Title' | trans }}</div>
            <div class="pk-table-width-150">{{ 'Position' | trans }}</div>
            <div class="pk-table-width-150">{{ 'Type' | trans }}</div>
        </div>

        <div v-repeat="position: positions" v-show="position | hasWidgets">

            <div class="pk-table-fake pk-table-fake-header pk-table-fake-subheading">
                <div>
                    {{ position.name | trans }}
                    <span class="uk-text-muted" v-if="position.description">{{ position.description | trans }}</span>
                </div>
            </div>

            <widget-list></widget-list>

        </div>

    </div>

</template>

<script>

    module.exports = Vue.extend({

        data: function() {

            return {
                search: '',
                positions: [],
                config: window.$widgets
            };

        },

        created: function() {

            this.$addChild(require('../edit/modal.vue'));

            this.Widgets = this.$resource('api/widget/:id');
            this.load();
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

                this.Widgets.query({ id: 'config' }, function (data) {
                    self.$set('config.configs', data);
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
                this.$set('widget', _.extend({}, widget));
            }

        },

        components: {

            'widget-list': require('./list.vue'),
            'site-text': require('../widgets/text.vue'),
            'assignment': require('../edit/assignment.vue')

        }

    });

</script>
