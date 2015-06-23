<template>

    <div class="uk-panel">

        <ul class="uk-nav uk-nav-side">
            <li class="uk-visible-hover" v-repeat="menu: menus" v-class="uk-active: isActive(menu)">
                <a v-on="click: select(menu)">{{ menu.label }}</a>
                <ul class="uk-subnav pk-subnav-icon uk-hidden" v-if="!menu.fixed">
                    <li><a class="pk-icon-edit pk-icon-hover" title="{{ 'Edit' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: editMenu(menu)"></a></li>
                    <li><a class="pk-icon-delete pk-icon-hover" title="{{ 'Delete' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: removeMenu(menu)" v-confirm="'Delete menu?'"></a></li>
                </ul>
            </li>
        </ul>

        <p>
            <a class="uk-button" v-on="click: editMenu()">{{ 'Add Menu' | trans }}</a>
        </p>

    </div>

    <div class="uk-modal" v-el="modal">

        <form class="uk-modal-dialog uk-form-stacked" name="menuForm" v-on="valid: saveMenu" v-if="edit">

            <div class="uk-modal-header">
                <h2>{{ 'Add Menu' | trans }}</h2>
            </div>

            <div class="uk-form-row">
                <label for="form-name" class="uk-form-label">{{ 'Name' | trans }}</label>
                <div class="uk-form-controls">
                    <input id="form-name" class="uk-width-1-1 uk-form-large" name="label" type="text" v-model="edit.label | trim" v-valid="required">
                    <p class="uk-form-help-block uk-text-danger" v-show="menuForm.label.invalid">{{ 'Invalid name.' | trans }}</p>
                </div>
            </div>

            <div class="uk-form-row">
                <label for="form-slug" class="uk-form-label">{{ 'Slug' | trans }}</label>
                <div class="uk-form-controls">
                    <input id="form-slug" class="uk-width-1-1 uk-form-large" name="id" type="text" v-model="edit.id" v-valid="unique">
                    <p class="uk-form-help-block uk-text-danger" v-show="menuForm.id.invalid">{{ 'Id must be unique.' | trans }}</p>
                </div>
            </div>

            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-link uk-modal-close" v-on="click: cancel">{{ 'Cancel' | trans }}</button>
                <button class="uk-button uk-button-link" v-attr="disabled: menuForm.invalid || !edit.label || !edit.id">{{ 'Save' | trans }}</button>
            </div>

        </form>

    </div>

</template>

<script>

    module.exports = {

        props: ['menu'],

        data: function() {
            return { menus: [], edit: undefined };
        },

        created: function() {
            this.Menus = this.$resource('api/site/menu/:id:label', {}, { update: { method: 'PUT' }});
            this.load();
        },

        methods: {

            isActive: function(menu) {
                return this.menu && this.menu.id === menu.id;
            },

            select: function(menu) {
                this.$parent.$set('menu', menu)
            },

            editMenu: function (menu) {

                var edit = _.extend({}, menu || { label: '', id: '' });
                edit.oldId = edit.id;

                this.$set('edit', edit);

                this.modal = UIkit.modal(this.$$.modal);
                this.modal.show();
            },

            saveMenu: function (e) {
                if (e) e.preventDefault();
                this.Menus[this.edit.oldId ? 'update' : 'save']({ label: this.edit.label }, this.edit, function() {
                    this.load();
                    this.$set('menu.id', this.edit.id);
                    this.cancel();
                }).error(function(msg) {
                    UIkit.notify(msg, 'danger');
                });

            },

            removeMenu: function (menu) {
                this.Menus.delete({ id: menu.id }, this.load);
            },

            cancel: function (e) {
                if (e) e.preventDefault();
                this.$set('edit', null);
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
                this.select(_.find(menus, {id: this.$get('menu.id')}) || menus[0])
            }

        },

        validators: {

            unique: function(value) {
                var menu = _.find(this.menus, { id: value });
                return !menu || this.edit.oldId === menu.id;
            }

        }

    }

</script>
