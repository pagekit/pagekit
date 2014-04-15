<div class="uk-form-row">
    <label for="form-theme-panel" class="uk-form-label">@trans('Panel Style')</label>
    <div class="uk-form-controls">
        <select id="form-theme-panel" class="uk-form-width-large" name="theme_alpha[panel]">
            @foreach ([
                ''                                    => trans('None'),
                'uk-panel-box'                        => trans('Box'),
                'uk-panel-box uk-panel-box-primary'   => trans('Box Primary'),
                'uk-panel-box uk-panel-box-secondary' => trans('Box Secondary'),
                'uk-panel-header'                     => trans('Header'),
                'uk-panel-space'                      => trans('Space')
            ] as value => name)
            <option value="@value"@((settings[widget.id]['panel'] ?: '') == value ? ' selected')>@name</option>
            @endforeach
        </select>
    </div>
</div>

<div class="uk-form-row">
    <label for="form-theme-badge" class="uk-form-label">@trans('Badge')</label>
    <div class="uk-form-controls">
        <input id="form-theme-badge" class="uk-form-width-small" type="text" name="theme_alpha[badge][text]" value="@settings[widget.id]['badge']['text']">
        <select class="uk-form-width-small" name="theme_alpha[badge][type]">
            @foreach ([
                'uk-panel-badge uk-badge'                  => trans('Default'),
                'uk-panel-badge uk-badge uk-badge-success' => trans('Success'),
                'uk-panel-badge uk-badge uk-badge-warning' => trans('Warning'),
                'uk-panel-badge uk-badge uk-badge-danger'  => trans('Danger')
            ] as value => name)
            <option value="@value"@((settings[widget.id]['badge']['type'] ?: 'uk-panel-badge uk-badge') == value ? ' selected')>@name</option>
            @endforeach
        </select>
    </div>
</div>

<div class="uk-form-row">
    <span class="uk-form-label">@trans('Alignment')</span>
    <div class="uk-form-controls uk-form-controls-text">
        <label><input type="checkbox" name="theme_alpha[alignment]" value="center-content" @(settings[widget.id]['alignment'] == 'center-content' ? 'checked':'')> @trans('Center the title and content.')</label>
    </div>
</div>