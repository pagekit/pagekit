<tr class="js-editor">
    <td colspan="6">
        <form class="uk-form" action="@url.route('@blog/comment/save')">
            <div class="uk-form-row">
                <label>@trans('Name')
                    <input type="text" name="comment[author]" value="{{author}}" />
                </label>
                <label>@trans('E-mail')
                    <input type="text" name="comment[email]" value="{{email}}" />
                </label>
                <label>@trans('URL')
                    <input type="text" name="comment[url]" value="{{url}}" />
                </label>
            </div>

            <div class="uk-form-row">
                <textarea name="comment[content]" cols="60" rows="6">{{content}}</textarea>
            </div>

            <input type="hidden" name="id" value="{{id}}" />
            @token()

            <p>
                <button class="uk-button uk-button-success" type="submit">@trans('Submit')</button>
                <a href="#" class="uk-button cancel">@trans('Cancel')</a>
            </p>

        </form>
    </td>
</tr>