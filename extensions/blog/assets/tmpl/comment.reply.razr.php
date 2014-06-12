<tr class="js-editor">
    <td colspan="6">
        <form class="uk-form" action="@url.route('@blog/comment/save')">

            <div class="uk-form-row">
                <textarea name="comment[content]" cols="60" rows="6"></textarea>
            </div>

            <input type="hidden" name="comment[parent_id]" value="{{id}}" />
            @token()

            <p>
                <button class="uk-button uk-button-success" type="submit">@trans('Submit')</button>
                <a href="#" class="uk-button cancel">@trans('Cancel')</a>
            </p>

        </form>
    </td>
</tr>