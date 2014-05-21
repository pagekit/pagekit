<div class="uk-form-row">
    @editor('widget[settings][content]', widget.get('content'), ['id' => 'form-content', 'markdown' => widget.get('markdown', 0)])
</div>
<div class="uk-form-row">
    <label><input type="checkbox" name="widget[settings][markdown]" value="1"@(widget.get('markdown', 0) ? ' checked')> @trans('Enable Markdown')</label>
</div>