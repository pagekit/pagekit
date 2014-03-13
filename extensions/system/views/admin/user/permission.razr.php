@style('user', 'system/css/user.css')
@script('user', 'system/js/user/permission.js', ['requirejs'])

<form id="js-permission" class="uk-form" action="@url('@system/permission/save')" method="post">

    <table class="uk-table uk-table-hover uk-table-middle pk-table-subheading pk-table-indent pk-table-head-sticky" data-uk-sticky>
        <thead>
            <tr>
                <th>@trans('Permission')</th>
                @foreach (roles as role)
                <th class="pk-table-width-100 uk-text-truncate uk-text-center">@role.name</th>
                @endforeach
            </tr>
        </thead>
    </table>

    <table class="uk-table uk-table-hover uk-table-middle pk-table-subheading pk-table-indent uk-margin-remove">
        <tbody>
            @foreach (app.permissions as extension => permission)
            <tr id="ext-@extension">
                <th>@app.extensions.repository.findPackage(extension).title</th>
                @foreach (roles as role)
                <th class="pk-table-width-100"></th>
                @endforeach
            </tr>
                @foreach (permission as name => data)
                <tr>
                    <td>
                        @trans(data.title)
                        @if (data.description)
                        <small class="uk-text-muted uk-display-block">@trans(data.description)</small>
                        @endif
                    </td>
                    @foreach (roles as role)
                    <td class="uk-text-center">
                        @if (role.administrator)
                        <input type="checkbox" checked disabled>
                        @else
                        <input class="@( !role.locked ? 'pk-checkbox' )" type="checkbox" name="permissions[@role.id][]" value="@name"@( role.hasPermission(name) ? ' checked' )>
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    @token()

</form>