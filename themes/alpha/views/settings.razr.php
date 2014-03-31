<form class="uk-form uk-form-horizontal" action="@url.route('@alpha/settings/save')" method="post">

	<div class="uk-form-row">
        <label for="form-sidebar-left-width" class="uk-form-label">@trans('Sidebar Left Width')</label>
        <div class="uk-form-controls">
            <select id="form-sidebar-left-width" class="uk-form-width-large" name="config[sidebars][sidebar-left][width]">
                @foreach ([12 => '20', 15 => '25', 18 => '30', 20 => '33', 24 => '40', 30 => '50'] as value => percent)
                <option value="@value" @(config['sidebars']['sidebar-left']['width'] == value ? 'selected')>@trans('_dd_%', ['_dd_' => percent])</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-sidebar-right-width" class="uk-form-label">@trans('Sidebar Right Width')</label>
        <div class="uk-form-controls">
            <select id="form-sidebar-right-width" class="uk-form-width-large" name="config[sidebars][sidebar-right][width]">
                @foreach ([12 => '20', 15 => '25', 18 => '30', 20 => '33', 24 => '40', 30 => '50'] as value => percent)
                <option value="@value" @(config['sidebars']['sidebar-right']['width'] == value ? 'selected')>@trans('_dd_%', ['_dd_' => percent])</option>
                @endforeach
            </select>
        </div>
    </div>

    <p>
        <button class="uk-button uk-button-primary" type="submit">Save</button>
        <a class="uk-button" href="@url.route('@system/themes/index')">@trans('Close')</a>
    </p>

</form>