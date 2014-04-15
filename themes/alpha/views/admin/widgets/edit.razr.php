<div class="uk-form-row">
    <label for="form-panel" class="uk-form-label">@trans('Panel Style')</label>
    <div class="uk-form-controls">
        <select id="form-panel" class="uk-form-width-large" name="theme_alpha[panel]">
            @foreach ([
                'uk-panel'                                     => trans('None'),
                'uk-panel uk-panel-box'                        => trans('Box'),
                'uk-panel uk-panel-box uk-panel-box-primary'   => trans('Box Primary'),
                'uk-panel uk-panel-box uk-panel-box-secondary' => trans('Box Secondary'),
                'uk-panel uk-panel-header'                     => trans('Header'),
                'uk-panel uk-panel-space'                      => trans('Space')
            ] as value => name)
            <option value="@value"@((settings[widget.id]['panel'] ?: 'uk-panel uk-panel-box') == value ? ' selected')>@name</option>
            @endforeach
        </select>
    </div>
</div>

<div class="uk-form-row">
    <label for="form-title" class="uk-form-label">@trans('Badge')</label>
    <div class="uk-form-controls">
        <p class="uk-form-controls-condensed">
            <select id="form-panel" class="uk-form-width-large" name="theme_alpha[badge][type]">
                @foreach ([
                    'uk-panel-badge uk-badge'                  => trans('None'),
                    'uk-panel-badge uk-badge uk-badge-success' => trans('Success'),
                    'uk-panel-badge uk-badge uk-badge-warning' => trans('Warning'),
                    'uk-panel-badge uk-badge uk-badge-danger'  => trans('Danger')
                ] as value => name)
                <option value="@value"@((settings[widget.id]['badge']['type'] ?: 'uk-panel-badge uk-badge') == value ? ' selected')>@name</option>
                @endforeach
            </select>
            <input class="uk-form-width-large" type="text" name="theme_alpha[badge][text]" value="@settings[widget.id]['badge']['text']" placeholder="@trans('Badge Text')">
        </p>
    </div>
</div>

<div class="uk-form-row">
    <span class="uk-form-label">@trans('Alignment')</span>
    <div class="uk-form-controls uk-form-controls-text">
        <p class="uk-form-controls-condensed">
            <label><input type="checkbox" name="theme_alpha[alignment]" value="center-content" @(settings[widget.id]['alignment'] == 'center-content' ? 'checked':'')> @trans('Center Content')</label>
        </p>
    </div>
</div>