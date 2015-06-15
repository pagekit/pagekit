<template>

    {{> settings}}

    <div class="uk-form-row">
        <label for="form-menu" class="uk-form-label">{{ 'Menu' | trans }}</label>
        <div class="uk-form-controls">
            <select id="form-menu" class="uk-form-width-large" v-model="widget.settings.menu" options="menuOptions"></select>
        </div>
    </div>
    <div class="uk-form-row">
        <label for="form-style" class="uk-form-label">{{ 'Start Level' | trans }}</label>
        <div class="uk-form-controls">
            <select id="form-style" class="uk-form-width-large" v-model="widget.settings.start_level">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>
        </div>
    </div>
    <div class="uk-form-row">
        <label for="form-style" class="uk-form-label">{{ 'Depth' | trans }}</label>
        <div class="uk-form-controls">
            <select id="form-style" class="uk-form-width-large" v-model="widget.settings.depth">
                <option value="">{{ 'No Limit' | trans }}</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>
        </div>
    </div>
    <div class="uk-form-row">
        <span class="uk-form-label">{{ 'Sub Items' | trans }}</span>
        <div class="uk-form-controls uk-form-controls-text">
            <p class="uk-form-controls-condensed">
                <label><input type="radio" value="all" v-model="widget.settings.mode"> {{ 'Show all' | trans }}</label>
            </p>
            <p class="uk-form-controls-condensed">
                <label><input type="radio" value="active" v-model="widget.settings.mode"> {{ 'Show only for active item' | trans }}</label>
            </p>
        </div>
    </div>



</template>

<script>

    module.exports = {

        section: {
            name: 'site-menu',
            label: 'Settings',
            active: 'site.menu',
            priority: 0
        },

        template: __vue_template__,
        props: ['widget', 'config', 'form'],

        data: function () {
            return { menus: window.$menus };
        },

        computed: {

            menuOptions: function() {
                return _.map(this.menus, function(menu) { return { text: menu.label, value: menu.id }; });
            }

        }

    };

    window.Widgets.component('site-menu', module.exports)

</script>
