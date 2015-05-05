<template>

    <div class="uk-margin" v-repeat="menu: menus">
        <div class="uk-flex">
            <span class="uk-panel-title uk-flex-item-1" v-on="click: edit(menu)">{{ menu.label }}</span>

            <div class="uk-button-dropdown" data-uk-dropdown="{ mode: 'click' }">
                <a v-on="click: $event.preventDefault()"><i class="uk-icon uk-icon-plus"></i></a>
                <div class="uk-dropdown uk-dropdown-small">
                    <ul class="uk-nav uk-nav-dropdown">
                        <li v-repeat="type: types | unmounted"><a v-on="click: add(menu, type)">{{ type.label }}</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <node-list></node-list>

    </div>

    <p>
        <a v-on="click: edit()"><i class="uk-icon-th-list"></i> {{ 'Create Menu' | trans }}</a>
    </p>

    <div class="uk-modal" v-el="modal">

        <div class="uk-modal-dialog uk-modal-dialog-slide" v-if="menu">

            <form name="menuform" v-on="valid: save">

                <p>
                    <input class="uk-width-1-1 uk-form-large" name="label" type="text" placeholder="{{ 'Enter Menu Name' | trans }}" v-model="menu.label"  v-valid="alphaNum">
                    <span class="uk-form-help-block uk-text-danger" v-show="menuform.label.invalid">{{ 'Invalid name.' | trans }}</span>
                </p>
                <p>
                    <input class="uk-width-1-1 uk-form-large" name="id" type="text" placeholder="{{ 'Enter Menu Slug' | trans }}" v-model="menu.id" v-valid="alphaNum, unique">
                    <span class="uk-form-help-block uk-text-danger" v-show="menuform.id.invalid">{{ 'Invalid slug.' | trans }}</span>
                </p>

                <button class="uk-button uk-button-primary" v-attr="disabled: menuform.invalid">{{ 'Save' | trans }}</button>
                <button class="uk-button uk-modal-close" v-on="click: cancel">{{ 'Cancel' | trans }}</button>
                <button class="uk-button uk-button-danger uk-float-right" v-show="menu.oldId" v-on="click: delete">{{ 'Delete' | trans }}</button>

            </form>
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
