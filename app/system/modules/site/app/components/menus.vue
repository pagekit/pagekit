<template>

    <div class="uk-panel" v-repeat="menu: menus">

        <div class="uk-panel-badge" data-uk-dropdown="{ mode: 'click' }">
            <a class="uk-link-muted" v-on="click: $event.preventDefault()"><i class="uk-icon uk-icon-plus"></i></a>
            <div class="uk-dropdown uk-dropdown-small">
                <ul class="uk-nav uk-nav-dropdown">
                    <li v-repeat="type: types | unmounted"><a v-on="click: add(menu, type)">{{ type.label }}</a></li>
                </ul>
            </div>
        </div>

        <h3 class="uk-panel-title" v-on="click: edit(menu)">{{ menu.label }}</h3>

        <node-list></node-list>

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
                    <input id="form-name" class="uk-width-1-1 uk-form-large" name="label" type="text" v-model="menu.label" v-valid="alphaNum">
                    <p class="uk-form-help-block uk-text-danger" v-show="menuForm.label.invalid">{{ 'Invalid name.' | trans }}</p>
                </div>
            </div>

            <div class="uk-form-row">
                <label for="form-slug" class="uk-form-label">{{ 'Slug' | trans }}</label>
                <div class="uk-form-controls">
                    <input id="form-slug" class="uk-width-1-1 uk-form-large" name="id" type="text" v-model="menu.id" v-valid="alphaNum, unique">
                    <p class="uk-form-help-block uk-text-danger" v-show="menuForm.id.invalid">{{ 'Invalid slug.' | trans }}</p>
                </div>
            </div>

            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-link" v-show="menu.oldId" v-on="click: delete">{{ 'Delete' | trans }}</button>
                <button class="uk-button uk-button-link uk-modal-close" v-on="click: cancel">{{ 'Cancel' | trans }}</button>
                <button class="uk-button uk-button-link" v-attr="disabled: menuForm.invalid">{{ 'Save' | trans }}</button>
            </div>

        </div>

    </div>

</template>

<script>

    module.exports = {

        inherit : true,

        data: function() {
            return { menu: null, unmounted: [] };
        },

        methods: {

            add: function(menu, type) {
                this.select({ menu: menu.id, type: type.id })
            },

            edit: function (menu) {

                menu = Vue.util.extend({}, menu || { label: '', id: '' });
                menu.oldId = menu.id;

                if (menu.fixed) return;

                this.$set('menu', menu);

                this.modal = UIkit.modal(this.$$.modal);
                this.modal.show();
            },

            save: function (e) {
                if (e) e.preventDefault();
                this.Menus[this.menu.oldId ? 'update' : 'save']({ id: this.menu.id }, this.menu, this.load);
                this.cancel();
            },

            'delete': function (e) {
                if (e) e.preventDefault();
                this.Menus.delete({ id: this.menu.id }, this.load);
                this.cancel();
            },

            cancel: function (e) {
                if (e) e.preventDefault();
                this.$set('menu', null);
                this.modal.hide();
            }

        },

        filters: {

            unmounted: function(types) {

                var self = this;

                return types.filter(function(type) {
                    return !type.controllers || !_.some(self.nodes, { type: type.id });
                })

            }

        },

        components: {

            'node-list': require('./list.vue')

        }

    }

</script>
