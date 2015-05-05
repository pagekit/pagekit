<template>

    <form class="uk-form uk-form-horizontal" name="form" v-show="node.type" v-on="valid: save">

        <div class="uk-clearfix uk-margin">

            <div class="uk-float-left">

                <h2 class="uk-h2" v-if="node.id">{{ node.title }} ({{ type.label }})</h2>
                <h2 class="uk-h2" v-if="!node.id">{{ 'Add %type%' | trans {type:type.label} }}</h2>

            </div>

            <div class="uk-float-right">

                <a class="uk-button" v-on="click: cancel()">{{ 'Cancel' | trans }}</a>
                <button class="uk-button uk-button-primary" type="submit" v-attr="disabled: form.invalid">{{ 'Save' | trans }}</button>

            </div>

        </div>

        <div v-el="edit"></div>

    </form>

</template>

<script>

    module.exports = {

        inherit: true,

        data: function() {
            return { node: {} }
        },

        watch: {

            selected: 'reload'

        },

        computed: {

            type: function() {
                return (_.find(this.types, { id: this.node.type }) || {});
            },

            path: function() {
                return (this.node.path ? this.node.path.split('/').slice(0, -1).join('/') : '') + '/' + (this.node.slug || '');
            },

            isFrontpage: function() {
                return this.node.id === this.frontpage;
            }

        },

        methods: {

            reload: function() {

                var self = this;

                if (!this.selected) {
                    this.node = {};
                    return;
                }

                this.$http.get(this.$url('admin/site/edit', (this.selected.id ? { id: this.selected.id } : { type: this.selected.type })), function(data) {

                    if (self.edit) {
                        self.edit.$destroy();
                    }

                    data.node.menu = self.selected.menu;

                    self.$set('node', data.node);

                    $(self.$$.edit).empty().html(data.view);

                    self.edit = self.$addChild({

                        inherit: true,
                        data: data.data,
                        el: self.$$.edit,

                        ready: function() {
                            UIkit.tab(this.$$.tab, { connect: this.$$.content });
                        }

                    });
                });
            },

            save: function (e) {

                e.preventDefault();

                var data = _.merge($(":input", e.target).serialize().parse(), { node: this.node });

                this.$broadcast('save', data);

                this.Nodes.save({ id: this.node.id }, data, function(node) {

                    vm.selected.id = parseInt(node.id);
                    vm.load();

                    if (data.frontpage) {
                        vm.$set('frontpage', node.id);
                    }
                });
            },

            cancel: function() {
                if (this.node.id) {
                    this.reload();
                } else {
                    this.select();
                }
            }

        }

    }

</script>
