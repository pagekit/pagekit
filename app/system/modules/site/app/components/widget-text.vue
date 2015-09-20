<template>

    <div class="uk-grid pk-grid-large" data-uk-grid-margin>
        <div class="uk-flex-item-1 uk-form-stacked">

            <div class="uk-form-row">

                <input class="uk-width-1-1 uk-form-large" type="text" name="title" placeholder="{{ 'Enter Title' | trans }}" v-model="widget.title" v-valid="required">
                <p class="uk-form-help-block uk-text-danger" v-show="form.title.invalid">{{ 'Title cannot be blank.' | trans }}</p>

            </div>

            <div class="uk-form-row">
                <v-editor value="{{@ widget.data.content }}" options="{{ {markdown : widget.data.markdown} }}"></v-editor>
                <p>
                    <label><input type="checkbox" v-model="widget.data.markdown"> {{ 'Enable Markdown' | trans }}</label>
                </p>
            </div>

        </div>
        <div class="pk-width-sidebar pk-width-sidebar-large">

            <partial name="settings"></partial>

        </div>
    </div>

</template>

<script>

    module.exports = {

        section: {
            label: 'Settings'
        },

        inherit: true,

        props: ['widget', 'config']

    };

    window.Widgets.components['system-text:settings'] = module.exports;

</script>
