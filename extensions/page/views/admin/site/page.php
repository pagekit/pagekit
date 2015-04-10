<div v-component="site-page" inline-template>
    <div class="uk-form-row">
        <label for="form-page-title" class="uk-form-label">{{ 'Page Title' | trans }}</label>
        <div class="uk-form-controls">
            <input id="form-page-title" class="uk-form-width-large" type="text" name="page[title]" v-model="page.title">
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-url" class="uk-form-label">{{ 'Content' | trans }}</label>
        <div class="uk-form-controls">
            <?= $view->editor('page[content]', '', ['v-model' => 'page.content', 'v-el' => 'editor']) ?>
        </div>
    </div>

    <div class="uk-form-row">
        <span class="uk-form-label">{{ 'Options' | trans }}</span>
        <div class="uk-form-controls">
            <label><input type="checkbox" name="page[data][title]" v-model="page.data.title"> {{ 'Show Title' | trans }}</label>
        </div>
        <div class="uk-form-controls">
            <label><input type="checkbox" name="page[data][markdown]" v-model="page.data.markdown"> {{ 'Enable Markdown' | trans }}</label>
        </div>
    </div>

</div>

<script>

    Vue.component('site-page', {

        inherit: true,

        ready: function() {
            this.editor = UIkit.htmleditor(this.$$.editor, $.extend({}, { marked: marked, CodeMirror: CodeMirror }, { markdown: this.page.data.markdown }));
        },

        events: {

            save: function(data) {
                data.page = this.page;
            }

        },

        watch: {

            'page.data.markdown': function(markdown) {
                this.editor.trigger(markdown ? 'enableMarkdown' : 'disableMarkdown');
            }

        }

    });

</script>
