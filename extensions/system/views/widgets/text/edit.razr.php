<div class="uk-form-row">
    <span class="uk-form-label">@trans('Markdown')</span>
    <div class="uk-form-controls uk-form-controls-text">
        <p class="uk-form-controls-condensed">
            <button class="uk-button uk-button-mini js-markdown  @(widget.get('markdown') ? 'uk-hidden':'')" type="button" data-value="0">@trans('No')</button>
            <button class="uk-button uk-button-mini js-markdown  @(!widget.get('markdown') ? 'uk-hidden':'')" type="button" data-value="1">@trans('Yes')</button>
            <input type="hidden" name="widget[settings][markdown]" value="@widget.get('markdown', '0')">
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

    // markdown status handling
    var markdownStatus   = $('input[name="widget[settings][markdown]"]'),
        markdownStatuses = $('.js-markdown').on('click', function(e){
            e.preventDefault();
            markdownStatus.val(markdownStatuses.addClass('uk-hidden').not(this).removeClass('uk-hidden').data('value'));
            $('#form-content').trigger(markdownStatus.val() == '1' ? 'enableMarkdown' : 'disableMarkdown');
        });
</script>