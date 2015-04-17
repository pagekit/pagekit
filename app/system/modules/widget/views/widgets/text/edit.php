<div class="uk-form-row" v-component="widget-text" inline-template>
    <div class="uk-form-controls">

        <?= $view->editor('widget[settings][content]', '', ['v-el' => 'editor', 'v-model' => 'widget.settings.content', 'data-markdown' => $widget->get('markdown', 0)]) ?>

        <p class="uk-form-controls-condensed">
            <label><input type="checkbox" name="widget[settings][markdown]" v-model="widget.settings.markdown"> {{ 'Enable Markdown' | trans }}</label>
        </p>
    </div>
</div>

<script>

    Vue.component('widget-text', {

        inherit: true,

        watch: {

            'widget.settings.markdown': function(markdown) {
                UIkit.htmleditor(this.$$.editor).trigger(markdown ? 'enableMarkdown' : 'disableMarkdown');
            }

        }

    });

</script>
