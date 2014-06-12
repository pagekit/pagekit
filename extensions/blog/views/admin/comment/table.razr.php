@if (comments)
<table class="uk-table uk-table-hover uk-table-middle">
    <thead>
        <tr>
            <th class="pk-table-width-minimum"><input type="checkbox" class="js-select-all"></th>
            <th class="pk-table-width-200">@trans('Author')</th>
            <th class="pk-table-width-400">@trans('Comment')</th>
            <th class="pk-table-width-200">@trans('Comment On')</th>
            <th class="pk-table-width-200">@trans('Status')</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach (comments as comment)
        <tr class="js-comment"
            data-id="@comment.id"
            data-author="@comment.author"
            data-email="@comment.email"
            data-url="@comment.url"
            data-content="@comment.content"
            data-user-id="@comment.userId"
            >
            <td>
                <input type="checkbox" name="ids[]" class="js-select" value="@comment.id">
            </td>
            <td>
                @gravatar(comment.email, ['size' => 50, 'attrs' => ['width' => '40', 'height' => '40', 'alt' => comment.author, 'class' => 'uk-border-circle']])
            </td>
            <td>
                @comment.author
                <small class="uk-text-muted">@comment.email - @comment.created|date('l, d-M-y H:i:s')</small>
                <br>
                @comment.content
            </td>
            <td>
                <a href="#" data-filter="post" data-value="@comment.threadId">(@comment.thread.numComments)</a><br>
                <a href="@url.route('@blog/post/edit', ['id' => comment.threadId])">@comment.thread.title</a><br>
                @if (comment.thread.status == 2 && comment.thread.hasAccess(app.user))
                <a href="@url.route('@blog/id', ['id' => comment.threadId])#comment-@comment.id">@trans('View Post')</a>
                @endif
            </td>
            <td>
                @comment.statusText
            </td>
            <td class="actions">
                <a href="#" data-quick-action="reply">@trans('Reply')</a> |
                <a href="#" data-quick-action="edit">@trans('Edit')</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif