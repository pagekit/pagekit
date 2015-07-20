<template>

    <div class="uk-form-horizontal">

        <partial name="settings"></partial>

        <div class="uk-form-row">
            <label for="form-type" class="uk-form-label">{{ 'Type' | trans }}</label>

            <div class="uk-form-controls">
                <select id="form-type" class="uk-form-width-large" v-model="behavior">
                    <option value="">{{ 'Link' | trans }}</option>
                    <option value="alias">{{ 'URL Alias' | trans }}</option>
                    <option value="redirect">{{ 'Redirect' | trans }}</option>
                </select>
            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-url" class="uk-form-label">{{ 'Url' | trans }}</label>

            <div class="uk-form-controls">
                <input-link id="form-url" class="uk-form-width-large" name="link" link="{{@ node.link}}" required></input-link>
                <div class="uk-form-help-block uk-text-danger" v-show="form.link.invalid">{{ 'Invalid url.' | trans }}</div>
            </div>
        </div>

        <pre>{{ node | json}}</pre>

    </div>

</template>

<script>

    module.exports = {

        props: ['node', 'form', 'type'],

        section: {
            label: 'Settings',
            priority: 0,
            active: 'link'
        },

        created: function () {
            if (this.behavior === 'redirect') {
                this.node.link = this.node.data.redirect;
            }
        },

        computed: {

            behavior: {

                get: function () {
                    if (this.node.data.alias) {
                        return 'alias';
                    } else if (this.node.data.redirect) {
                        return 'redirect';
                    }

                    return '';
                },

                set: function (type) {
                    this.$set('node.data', _.extend(this.node.data, {
                        alias: type === 'alias',
                        redirect: type === 'redirect' ? this.node.link : false
                    }));
                }

            }

        },

        events: {

            save: function () {
                if (this.behavior === 'redirect') {
                    this.node.data.redirect = this.node.link;
                }
            }

        }

    }

</script>
