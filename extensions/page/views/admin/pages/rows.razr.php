@foreach (pages as page)
<tr class="uk-visible-hover">
    <td>
        <input type="checkbox" name="ids[]" value="@page.id">
    </td>
    <td>
        <a href="@url.route('@page/page/edit', ['id' => page.id])">@page.title</a>
    </td>
    <td class="uk-text-center">
        <a href="#" data-action="@url.route('@page/page/status', ['ids[]' => page.id, 'status' => page.status ? '0' : '1'])" title="@page.statusText">
            <i class="uk-icon-circle uk-text-@(page.status ? 'success' : 'danger')" title="@page.statusText"></i>
        </a>
    </td>
    <td class="pk-table-text-break">
        @set(link = url.route('@page/id', ['id' => page.id], 'base') ?: '/')
        @if (page.status == 1)
        <a href="@url.route('@page/id', ['id' => page.id])" target="_blank">@link</a>
        @else
        @link
        @endif
    </td>
    <td>
        @(levels[page.accessId].name ?: trans('No access level'))
    </td>
    <td>
        <ul class="uk-subnav pk-subnav-icon uk-invisible">
            <li><a class="uk-icon-minus-circle"></a></li>
        </ul>
    </td>
</tr>
@endforeach