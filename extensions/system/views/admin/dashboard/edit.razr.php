<form class="uk-form uk-form-horizontal" action="@url.route('@system/dashboard/save', ['id' => widget.id])" method="post">
    <p>@type.renderForm(widget)</p>
    <p>
        <input type="hidden" name="widget[type]" value="@type.id">
        <button class="uk-button uk-button-primary" type="submit">@trans('Save')</button>
        <a class="uk-button" href="@url.route('@system/dashboard/settings')">@(widget.id ? trans('Close') : trans('Cancel'))</a>
    </p>

    @token()

</form>