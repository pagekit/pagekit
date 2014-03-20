<div class="uk-form-row">
    <div class="uk-form-row">
        <select class="js-types uk-width-1-1" name="type">
            @foreach(app.links as link)
            <option value="@link.route">@link.label</option>
            @endforeach
        </select>
    </div>
    <div class="js-edit uk-form-row uk-hidden">
        @foreach(app.links as link)
        <div data-type="@link.route">
            @link.renderForm()
        </div>
        @endforeach
    </div>
    <input type="hidden" class="js-url">
</div>