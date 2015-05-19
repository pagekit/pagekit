<template>

    {{> settings}}

    <div class="uk-form-row">
        <div class="uk-form-controls">

            <!-- TODO: integrate editor-->
            <textarea autocomplete="off" style="visibility:hidden; height:543px;" data-finder-options="{root:'\/storage'}" v-model="widget.settings.content" v-el="editor"></textarea>

            <p class="uk-form-controls-condensed">
                <label><input type="checkbox" name="widget[settings][markdown]" v-model="widget.settings.markdown"> {{ 'Enable Markdown' | trans }}</label>
            </p>
        </div>
    </div>

</template>

<script>

    module.exports = {

        name: 'site-text',
        label: 'Settings',
        active: 'site.text',
        priority: 0,

        template: __vue_template__,
        paramAttributes: ['widget', 'config', 'form'],

        ready: function() {
            this.editor = UIkit.htmleditor(this.$$.editor, $.extend({}, { marked: marked, CodeMirror: CodeMirror }, { markdown: this.$get('widget.settings.markdown') }));
        },

        watch: {

            'widget.settings.markdown': function (markdown) {
                this.editor.trigger(markdown ? 'enableMarkdown' : 'disableMarkdown');
            }

        }

    }

</script>
