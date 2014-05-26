<div class="uk-form-row">
    <label class="uk-form-label" for="form-url">@trans('URL')</label>
    <div class="uk-form-controls">
        <input id="form-url" type="text" class="js-url uk-width-1-1">
    </div>
</div>
<div class="uk-form-row">
    <label class="uk-form-label" for="form-type">@trans('Type')</label>
    <div class="uk-form-controls">
        <select id="form-type" class="js-types uk-width-1-1" name="type">
            <option value="">@trans('Pick URL')</option>
            @foreach(links as link)
            <option value="@link.route">@link.label</option>
            @endforeach
        </select>
    </div>
</div>
<div class="js-edit uk-form-row uk-hidden">
    @foreach(links as link)
    <div data-type="@link.route">
        @link.renderForm()
    </div>
    @endforeach
</div>