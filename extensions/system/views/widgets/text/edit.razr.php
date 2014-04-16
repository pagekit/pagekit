<div class="uk-form-row">
    <span class="uk-form-label">@trans('Markdown')</span>
    <div class="uk-form-controls uk-form-controls-text">
        <p class="uk-form-controls-condensed">
            <label class="uk-margin-small-right"><input type="radio" name="widget[settings][markdown]" value="1"@('1' == widget.get('markdown') ? ' checked')> @trans('Enabled')</label>
            <label><input type="radio" name="widget[settings][markdown]" value="0"@('0' == widget.get('markdown', '0') ? ' checked')> @trans('Disabled')</label>
        </p>
    </div>
</div>

<div class="uk-form-row">
    @editor('widget[settings][content]', widget.get('content'), ['id' => 'form-content', 'markdown' => widget.get('markdown', '0')])
</div>

<script>
    $('input[name="widget[settings][markdown]"]').on("click", function(){
        $('#form-content').trigger(this.value=='1' ? 'enableMarkdown':'disableMarkdown');
    });
</script>