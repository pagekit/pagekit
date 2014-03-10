<div class="uk-form-row">
    <label for="form-menu" class="uk-form-label">@trans('Menu')</label>
    <div class="uk-form-controls">
        <select id="form-menu" class="uk-form-width-large" name="widget[settings][menu]">
            @foreach (menus as menu)
            <option value="@menu.id"@(menu.id == widget.get('menu') ? ' selected')>@menu.name</option>
            @endforeach
        </select>
    </div>
</div>
<div class="uk-form-row">
    <label for="form-style" class="uk-form-label">@trans('Style')</label>
    <div class="uk-form-controls">
        <select id="form-style" class="uk-form-width-large" name="widget[settings][style]">
            @foreach (styles as style)
            <option value="@style"@(style == widget.get('style') ? ' selected')>@style</option>
            @endforeach
        </select>
    </div>
</div>
<div class="uk-form-row">
    <label for="form-style" class="uk-form-label">@trans('Start Level')</label>
    <div class="uk-form-controls">
        <select id="form-style" class="uk-form-width-large" name="widget[settings][start_level]">
            @foreach([trans('1'), trans('2'), trans('3'), trans('4'), trans('5'), trans('6'), trans('7'), trans('8'), trans('9'), trans('10'), ] as value => text)
            <option value="@(value + 1)"@(value + 1 == widget.get('start_level') ? ' selected')>@text</option>
            @endforeach
        </select>
    </div>
</div>
<div class="uk-form-row">
    <label for="form-style" class="uk-form-label">@trans('Depth')</label>
    <div class="uk-form-controls">
        <select id="form-style" class="uk-form-width-large" name="widget[settings][depth]">
            <option value="">@trans('- No Limit -')</option>
            @foreach([trans('1'), trans('2'), trans('3'), trans('4'), trans('5'), trans('6'), trans('7'), trans('8'), trans('9'), trans('10'), ] as value => text)
            <option value="@(value + 1)"@(value + 1 == widget.get('depth') ? ' selected')>@text</option>
            @endforeach
        </select>
    </div>
</div>
<div class="uk-form-row">
    <span class="uk-form-label">@trans('Sub menu items')</span>
    <div class="uk-form-controls uk-form-controls-text">
        <p class="uk-form-controls-condensed">
            <label><input type="radio" name="widget[settings][mode]" value="all"@('all' === widget.get('mode', 'all') ? ' checked')> @trans('Show all')</label>
        </p>
        <p class="uk-form-controls-condensed">
            <label><input type="radio" name="widget[settings][mode]" value="active"@('active' === widget.get('mode') ? ' checked')> @trans('Show for active parent')</label>
        </p>
    </div>
</div>