@style('user', 'system/css/user.css')
@script('user', 'system/js/user/role.js', ['requirejs'])

<div class="uk-grid" data-uk-grid-margin data-uk-grid-match>
    <div class="pk-sidebar uk-width-medium-1-4">
        <form id="js-role" class="uk-form" method="post">

            <ul class="pk-sortable" data-uk-sortable="{ maxDepth: 1, prefix: 'pk' }" data-update-url="@url.route('@system/role/priority')">
                @foreach (roles as r)
                <li data-id="@r.id">
                    <div class="pk-sortable-item uk-visible-hover@( r == role ? ' pk-active')">
                        <div class="pk-sortable-handle"></div>
                        @if (!r.locked)
                        <ol class="uk-subnav pk-subnav-icon uk-hidden">
                            <li><a href="#" data-edit="@url.route('@system/role/save', ['id' => r.id])" data-name="@r.name" title="@trans('Edit')"><i class="uk-icon-pencil"></i></a></li>
                            <li><a href="#" data-action="@url.route('@system/role/delete', ['id' => r.id])" data-confirm="@trans('Are you sure?')" title="@trans('Delete')"><i class="uk-icon-minus-circle"></i></a></li>
                        </ol>
                        @endif
                        <a href="@url.route('@system/role/index', ['id' => r.id])">@r.name</a>
                    </div>
                </li>
                @endforeach
            </ul>
            <hr>
            <a class="uk-button" href="#" data-edit="@url.route('@system/role/save', ['id' => 0])">@trans('Add Role')</a>
            @token()

        </form>
    </div>
    <div class="pk-content uk-width-medium-3-4">
        <form id="js-role-permissions" class="uk-form" action="@url.route('@system/role/save', ['id' => role.id])" method="post">

            @if (role.id)
            <table class="uk-table uk-table-hover uk-table-middle pk-table-subheading pk-table-indent">
                <thead>
                    <tr>
                        <th>@trans('Permission')</th>
                        <th class="pk-table-width-minimum"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (app.permissions as extension => permission)
                    <tr>
                        <th colspan="2">@app.extensions.repository.findPackage(extension).title</th>
                    </tr>
                        @foreach (permission as name => data)
                        <tr>
                            <td>
                                @trans(data.title)
                                @if (data.description)
                                <small class="uk-text-muted uk-display-block">@trans(data.description)</small>
                                @endif
                            </td>
                            <td class="@( role.locked && authrole.hasPermission(name) ? 'pk-role-inherited')@(role.hasPermission(name) ? 'pk-role-enabled')">
                                @if (role.administrator)
                                    <input type="checkbox" checked disabled>
                                @else
                                    <input class="@(!role.locked ? 'pk-checkbox')" type="checkbox" name="permissions[]" value="@name"@(role.hasPermission(name) ? ' checked')>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
            @endif


            @token()

        </form>
    </div>
</div>

<div id="modal-role" class="uk-modal">
    <div class="uk-modal-dialog uk-modal-dialog-slide">

        <form class="uk-form" method="post">

            <p>
                <input class="uk-width-1-1 uk-form-large" type="text" name="name" value="" placeholder="@trans('Enter Role Name')">
            </p>

            <button class="uk-button uk-button-primary" type="submit">@trans('Save')</button>
            <button class="uk-button uk-modal-close" type="submit">@trans('Cancel')</button>

            @token()

        </form>

    </div>
</div>