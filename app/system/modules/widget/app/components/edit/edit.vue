<template>

    <form class="uk-form uk-container uk-container-center" name="widgetForm" v-on="valid: save">

        <div class="uk-clearfix uk-margin" data-uk-margin>

            <div class="uk-float-left">

                <h2 class="uk-h2" v-if="widget.id">{{ widget.title }} ({{ type.name }})</h2>
                <h2 class="uk-h2" v-if="!widget.id">{{ 'Add %type%' | trans {type:type.name} }}</h2>

            </div>

            <div class="uk-float-right">

                <a class="uk-button" v-on="click: cancel">{{ 'Cancel' | trans }}</a>
                <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>

            </div>

        </div>

        <div class="uk-grid" data-uk-grid-margin>

            <div class="uk-width-medium-3-4 uk-form-horizontal">

                <ul class="uk-tab" v-el="tab">
                    <li v-repeat="section: sections | active | orderBy 'priority'"><a>{{ section.label | trans }}</a></li>
                </ul>

                <div class="uk-switcher uk-margin" v-el="content">
                    <div v-repeat="section: sections | active | orderBy 'priority'">
                        <div v-component="{{ section.name }}" widget="{{ widget }}" type="{{ type }}" config="{{ config }}" form="{{ widgetForm }}"></div>
                    </div>
                </div>

            </div>

            <div class="uk-width-medium-1-4" v-component="sidebar"></div>

        </div>

    </form>

</template>

<script>

    module.exports = {

        paramAttributes: ['widget', 'type', 'config', 'position'],

        data: function() {
            return _.merge({}, window.$widgets);
        },

        ready: function () {

            UIkit.tab(this.$$.tab, { connect: this.$$.content });
            this.$set('widget.settings', _.defaults({}, this.widget.settings, this.type.defaults));

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

            sections: function () {

                var sections = [];

                _.each(window.Widgets.options.components, function (component) {
                    if (component.options.section) {
                        sections.push(component.options.section);
                    }
                });

                return sections;
            }

        },

        methods: {

            save: function (e) {

                e.preventDefault();

                var data = { widget: this.widget, config: this.config };

                this.$broadcast('save', data);

                this.$resource('api/widget/:id').save({ id: this.widget.id }, data, function() {
                    this.$dispatch('saved');
                });

            },

            cancel: function (e) {

                e.preventDefault();

                this.$dispatch('cancel');

            }

        },

        partials: {

            settings: require('./settings.html')

        },

        components: {

            sidebar: require('./sidebar.vue')

        }
    };

</script>
