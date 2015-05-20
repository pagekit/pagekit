<template>

    <div class="uk-grid" data-uk-grid-margin>

        <div class="uk-width-medium-3-4 uk-form-horizontal">

            <ul class="uk-tab" v-el="tab">
                <li v-repeat="section: sections | active | orderBy 'priority'"><a>{{ section.label | trans }}</a></li>
            </ul>

            <div class="uk-switcher uk-margin" v-el="content">
                <div v-repeat="section: sections | active | orderBy 'priority'">
                    <div v-component="{{ section.name }}" widget="{{ widget }}" config="{{ widgetConfig }}" form="{{ form }}"></div>
                </div>
            </div>

        </div>

        <div class="uk-width-medium-1-4">

            <div class="uk-panel uk-panel-divider uk-form-stacked">

                <div class="uk-form-row">
                    <label for="form-position" class="uk-form-label">{{ 'Position' | trans }}</label>
                    <div class="uk-form-controls">
                        <select id="form-position" name="position" class="uk-width-1-1" v-model="position" options="positionOptions"></select>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{ 'Restrict Access' | trans }}</span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <p v-repeat="role: roles" class="uk-form-controls-condensed">
                            <label><input type="checkbox" value="{{ role.id }}" v-checkbox="widget.roles"> {{ role.name }}</label>
                        </p>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{ 'Options' | trans }}</span>
                    <div class="uk-form-controls">
                        <label><input type="checkbox" v-model="widget.settings.show_title"> {{ 'Show Title' | trans }}</label>
                    </div>
                </div>

            </div>

        </div>

    </div>

</template>

<script>

    var Widgets = require('widgets');

    Widgets.addSection(require('../widgets/text.vue'));
    Widgets.addSection(require('../edit/assignment.vue'));

    module.exports = {

        paramAttributes: ['widget', 'config', 'position', 'form'],

        data: function() {
            return _.merge({}, window.$widgets);
        },

        created: function () {
            var self = this;

            Widgets.sections.forEach(function(options) {
                self.$options.components[options.name] = Vue.extend(options);
            });

            this.Widgets = this.$resource('api/widget/:id');
        },

        ready: function() {
            UIkit.tab(this.$$.tab, { connect: this.$$.content });
        },

        watch: {

            widget: function(widget) {

                if (widget) {
                    this.$set('widget.settings', _.defaults({}, widget.settings, this.type.defaults));
                }

            }

        },

        filters: {

            active: function(sections) {

                var type = this.$get('type.id');

                return sections.filter(function(section) {
                    return !section.active || type && type.match(section.active);
                });

            }

        },

        computed: {

            positionOptions: function() {
                return [{ text: this.$trans('- Assign -'), value: '' }].concat(
                    _.map(this.positions, function(position) {
                        return { text: this.$trans(position.name), value: position.id };
                    }.bind(this))
                );
            },

            type: function() {
                return _.find(this.types, { id: this.widget.type });
            },

            typeName: function() {
                return this.type ? this.type.name : this.$trans('Extension not loaded');
            },

            sections: function() {
                return Widgets.sections;
            }

        },

        methods: {

            save: function (e) {

                e.preventDefault();

                var data = { widget: this.widget, config: this.config, position: this.position };

                this.$broadcast('save', data);

                this.Widgets.save({ id: this.widget.id }, data, function() {
                    this.$dispatch('saved', data);
                });

            }

        },

        partials: {

            settings: require('./settings.html')

        }

    };

</script>
