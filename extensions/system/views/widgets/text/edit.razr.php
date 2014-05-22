<div class="uk-form-row">
    <div class="uk-form-controls">
        @editor('widget[settings][content]', widget.get('content'), ['id' => 'form-content', 'markdown' => widget.get('markdown', 0)])
        <p class="uk-form-controls-condensed">
            <label><input type="checkbox" name="widget[settings][markdown]" value="1"@(widget.get('markdown', 0) ? ' checked')> @trans('Enable Markdown')</label>
        </p>
    </div>
</div>

<script>

    jQuery(function($) {

        $('input[name="widget[settings][markdown]"]').on('change', function() {
            $('#form-content').trigger($(this).prop('checked') ? 'enableMarkdown' : 'disableMarkdown');
        });

    });

</script>