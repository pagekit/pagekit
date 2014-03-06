@script('user', 'system/js/user/user.js', 'requirejs')

<form id="js-user" class="uk-form" method="post">

    <div class="pk-options uk-clearfix">
        <div class="uk-float-left">

            <a class="uk-button uk-button-primary" href="@url.to('@system/user/add')">@trans('Add User')</a>

            <div class="uk-button-dropdown" data-uk-dropdown="{ mode: 'click' }">
                <button class="uk-button" type="button">@trans('Actions') <i class="uk-icon-caret-down"></i></button>
                <div class="uk-dropdown uk-dropdown-small">
                    <ul class="uk-nav uk-nav-dropdown">
                        <li><a href="#" data-action="@url.to('@system/user/status', ['status' => 1])">@trans('Activate')</a></li>
                        <li><a href="#" data-action="@url.to('@system/user/status', ['status' => 0])">@trans('Block')</a></li>
                        <li class="uk-nav-divider"></li>
                        <li><a href="#" data-action="@url.to('@system/user/delete')">@trans('Delete')</a></li>
                    </ul>
                </div>
            </div>

        </div>
        <div class="uk-float-right">

            <input type="text" name="filter[search]" placeholder="@trans('Search')" value="@filter['search']">

            <select name="filter[status]">

                <option value="">@trans('- Status -')</option>
                @foreach (statuses as id => status)
                <option value="@id"@( filter['status']|length && filter['status'] == id ? ' selected' )>@status</option>
                @endforeach
            </select>

            <select name="filter[role]">
                <option value="">@trans('- Role -')</option>
                @foreach (roles as role)
                <option value="@role.id"@( filter['role'] == role.id ? ' selected' )>@role.name</option>
                @endforeach
            </select>

            <select class="uk-form-width-medium" name="filter[permission]">
                <option value="">@trans('- Permission -')</option>
                @foreach (app.permissions as ext => permission)
                <optgroup label="@ext">
                    @foreach (permission as id => perm)
                    <option value="@id"@( filter['permission'] == id ? ' selected' )>@trans(perm['title'])</option>
                    @endforeach
                </optgroup>
                @endforeach
            </select>

        </div>
    </div>

    @if (users)
    <table class="uk-table uk-table-hover uk-table-middle">
        <thead>
            <tr>
                <th class="pk-table-width-minimum"><input type="checkbox" class="js-select-all"></th>
                <th>@trans('Username')</th>
                <th class="pk-table-width-100 uk-text-center">@trans('Status')</th>
                <th>@trans('Name')</th>
                <th>@trans('Email')</th>
                <th>@trans('Roles')</th>
            </tr>
        </thead>
        <tbody>
            @foreach (users as user)
            <tr>
                <td><input type="checkbox" name="ids[]" value="@user.id"></td>
                <td>
                    @gravatar(user.email, ['size' => 48, 'attrs' => ['width' => '24', 'height' => '24', 'alt' => user.username, 'class' => 'uk-border-circle uk-margin-small-right']])
                    <a href="@url.to('@system/user/edit', ['id' => user.id])">@user.username</a>
                </td>
                <td class="uk-text-center">
                    <a href="#" class="uk-icon-circle uk-text-@( user.status ? 'success' : 'danger' )" data-action="@url.to('@system/user/status', ['ids[]' => user.id, 'status' => user.status ? 0 : 1])" title="@user.statusText"></a>
                </td>
                <td>@user.name</td>
                <td class="pk-table-width-200 uk-text-truncate">@user.email</td>
                <td class="pk-table-width-200 uk-text-truncate">
                    @if (user.roles|length == 1)
                        @user.roles|implode('')
                    @else
                        @user.roles|slice(1)|implode(', ')
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="uk-alert uk-alert-info">@trans('No user found.')</p>
    @endif

</form>
