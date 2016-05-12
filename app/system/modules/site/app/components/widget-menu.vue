<template>

    <div class="uk-grid pk-grid-large pk-width-sidebar-large" data-uk-grid-margin>
        <div class="pk-width-content uk-form-horizontal">

            <div class="uk-form-row">
                <label for="form-title" class="uk-form-label">{{ 'Title' | trans }}</label>

                <div class="uk-form-controls">
                    <input id="form-title" class="uk-form-width-large" type="text" name="title" v-model="widget.title" v-validate:required>

                    <p class="uk-form-help-block uk-text-danger" v-show="form.title.invalid">{{ 'Title cannot be blank.' | trans }}</p>
                </div>
            </div>

            <div class="uk-form-row">
                <label for="form-menu" class="uk-form-label">{{ 'Menu' | trans }}</label>

                <div class="uk-form-controls">
                    <select id="form-menu" class="uk-form-width-large" v-model="widget.data.menu">
                        <option value="">{{ '- Menu -' | trans }}</option>
                        <option v-for="m in menus" :value="m.id">{{ m.label }}</option>
                    </select>
                </div>
            </div>

            <div class="uk-form-row">
                <label for="form-level" class="uk-form-label">{{ 'Start Level' | trans }}</label>

                <div class="uk-form-controls">
                    <select id="form-level" class="uk-form-width-large" v-model="widget.data.start_level" number>
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
                <label for="form-depth" class="uk-form-label">{{ 'Depth' | trans }}</label>

                <div class="uk-form-controls">
                    <select id="form-depth" class="uk-form-width-large" v-model="widget.data.depth" number>
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
                        <label><input type="radio" value="all" v-model="widget.data.mode"> {{ 'Show all' | trans }}</label>
                    </p>

                    <p class="uk-form-controls-condensed">
                        <label><input type="radio" value="active" v-model="widget.data.mode"> {{ 'Show only for active item' | trans }}</label>
                    </p>
                </div>
            </div>

        </div>
        <div class="pk-width-sidebar">

            <partial name="settings"></partial>

        </div>
    </div>

</template>

<script>

    module.exports = {

        section: {
            label: 'Settings'
        },

        props: ['widget', 'config', 'form'],

        data: function () {
            return {
                menus: {}
            }
        },

        created: function () {

            this.$options.partials = this.$parent.$options.partials;

            this.$http.get('api/site/menu').then(function (res) {
                this.$set('menus', res.data.filter(function (menu) {
                    return menu.id !== 'trash';
                }));
            });
        }

    };

    window.Widgets.components['system-menu:settings'] = module.exports;

</script>
