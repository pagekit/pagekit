<template>

    <div class="uk-panel">

        <ul class="uk-nav uk-nav-side">
            <li class="uk-visible-hover" v-repeat="menu: menus" v-class="pk-active: active.id === menu.id">
                <a v-on="click: active = menu">{{ menu.label }}</a>
                <ul class="uk-subnav pk-subnav-icon uk-hidden" v-if="!menu.fixed">
                    <li><a title="{{ 'Edit' | trans }}" v-on="click: edit(menu)"><i class="uk-icon-pencil"></i></a></li>
                    <li><a title="{{ 'Delete' | trans }}" v-on="click: remove(menu)"><i class="uk-icon-minus-circle"></i></a></li>
                </ul>
            </li>
        </ul>

    </div>

    <p>
        <a class="uk-button uk-width-1-1" v-on="click: edit()"><i class="uk-icon-th-list"></i> {{ 'Create Menu' | trans }}</a>
    </p>

    <div class="uk-modal" v-el="modal">

        <form class="uk-modal-dialog uk-form uk-form-stacked" name="menuForm" v-on="valid: save" v-if="menu">

            <div class="uk-modal-header">
                <h2>{{ 'Add Menu' | trans }}</h2>
            </div>

            <div class="uk-form-row">
                <label for="form-name" class="uk-form-label">{{ 'Name' | trans }}</label>

                <div class="uk-form-controls">
                    <input id="form-name" class="uk-width-1-1 uk-form-large" name="label" type="text" v-model="menu.label | trim" v-valid="required">

                    <p class="uk-form-help-block uk-text-danger" v-show="menuForm.label.invalid">{{ 'Invalid name.' | trans }}</p>
                </div>
            </div>

            <div class="uk-form-row">
                <label for="form-slug" class="uk-form-label">{{ 'Slug' | trans }}</label>

                <div class="uk-form-controls">
                    <input id="form-slug" class="uk-width-1-1 uk-form-large" name="id" type="text" v-model="menu.id">
                </div>
            </div>

            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-link uk-modal-close" v-on="click: cancel">{{ 'Cancel' | trans }}</button>
                <button class="uk-button uk-button-link" v-attr="disabled: menuForm.invalid || !menu.label || !menu.id">{{ 'Save' | trans }}</button>
            </div>

        </form>

    </div>

</template>

<script>

    module.exports = {

        paramAttributes: ['active'],

        created: function() {
            this.Menus = this.$resource('api/site/menu/:id:label', {}, { 'update': { method: 'PUT' }});
            this.load();
        },

        methods: {

            edit: function (menu) {

                menu = Vue.util.extend({}, menu || { label: '', id: '' });
                menu.oldId = menu.id;

                this.$set('menu', menu);

                this.modal = UIkit.modal(this.$$.modal);
                this.modal.show();
            },

            save: function (e) {
                if (e) e.preventDefault();
                this.Menus[this.menu.oldId ? 'update' : 'save']({ label: this.menu.label }, this.menu, function() {
                    this.load();
                    this.$set('active', this.menu.id);
                    this.cancel();
                }).error(function(msg) {
                    UIkit.notify(msg, 'danger');
                });

            },

            remove: function (menu) {
                this.Menus.delete({ id: menu.id }, this.load);
            },

            cancel: function (e) {
                if (e) e.preventDefault();
                this.$set('menu', null);
                this.modal.hide();
            },

            load: function () {
                return this.Menus.query(function(data) {
                    this.$set('menus', data);
                });
            }

        },

        watch: {

            menus: function(menus) {
                if (!_.find(menus, {id: this.$get('active.id')})) {
                    this.$set('active', menus[0] || undefined);
                }
            }

        },

        validators: {

            unique: function(value) {
                var menu = _.find(this.menus, { id: value });
                return !menu || this.menu.oldId == menu.id;
            }

        }

    }

</script>
