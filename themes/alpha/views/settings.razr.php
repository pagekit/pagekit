<form class="uk-form uk-form-horizontal" action="@url.route('@alpha/settings/save')" method="post">

	<div class="uk-form-row">
        <label for="form-sidebar-width" class="uk-form-label">@trans('Sidebar Width')</label>
        <div class="uk-form-controls">
            <select id="form-sidebar-width" class="uk-form-width-large" name="config[sidebar-width]">
                @foreach (['20', '25', '30', '33', '40', '50'] as value)
	            <option value="@value" @(config['sidebar-width'] == value ? 'selected')>@value%</option>
	            @endforeach
            </select>
        </div>
    </div>

	<div class="uk-form-row">
        <span class="uk-form-label">@trans('Sidebar Alignment')</span>
        <div class="uk-form-controls uk-form-controls-text">
            <p class="uk-form-controls-condensed">
                <label><input type="radio" name="config[sidebar-alignment]" value="left"@(config['sidebar-alignment'] == 'left' ? ' checked')> @trans('Left')</label>
            </p>
            <p class="uk-form-controls-condensed">
                <label><input type="radio" name="config[sidebar-alignment]" value="right"@(config['sidebar-alignment'] == 'right' ? ' checked')> @trans('Right')</label>
            </p>
        </div>
    </div>

    <p>
        <button class="uk-button uk-button-primary" type="submit">Save</button>
        <a class="uk-button" href="@url.route('@system/themes/index')">@trans('Close')</a>
    </p>

</form>