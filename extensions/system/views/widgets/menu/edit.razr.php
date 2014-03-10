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
            <option value="1"@('1' === widget.get('start_level') ? ' selected')>@trans('1')</option>
            <option value="2"@('2' === widget.get('start_level') ? ' selected')>@trans('2')</option>
            <option value="3"@('3' === widget.get('start_level') ? ' selected')>@trans('3')</option>
            <option value="4"@('4' === widget.get('start_level') ? ' selected')>@trans('4')</option>
            <option value="5"@('5' === widget.get('start_level') ? ' selected')>@trans('5')</option>
            <option value="6"@('6' === widget.get('start_level') ? ' selected')>@trans('6')</option>
            <option value="7"@('7' === widget.get('start_level') ? ' selected')>@trans('7')</option>
            <option value="8"@('8' === widget.get('start_level') ? ' selected')>@trans('8')</option>
            <option value="9"@('9' === widget.get('start_level') ? ' selected')>@trans('9')</option>
            <option value="10"@('10' === widget.get('start_level') ? ' selected')>@trans('10')</option>
        </select>
    </div>
</div>
<div class="uk-form-row">
    <label for="form-style" class="uk-form-label">@trans('Depth')</label>
    <div class="uk-form-controls">
        <select id="form-style" class="uk-form-width-large" name="widget[settings][depth]">
            <option value="">@trans('- No Limit -')</option>
            <option value="1"@('1' === widget.get('depth') ? ' selected')>@trans('1')</option>
            <option value="2"@('2' === widget.get('depth') ? ' selected')>@trans('2')</option>
            <option value="3"@('3' === widget.get('depth') ? ' selected')>@trans('3')</option>
            <option value="4"@('4' === widget.get('depth') ? ' selected')>@trans('4')</option>
            <option value="5"@('5' === widget.get('depth') ? ' selected')>@trans('5')</option>
            <option value="6"@('6' === widget.get('depth') ? ' selected')>@trans('6')</option>
            <option value="7"@('7' === widget.get('depth') ? ' selected')>@trans('7')</option>
            <option value="8"@('8' === widget.get('depth') ? ' selected')>@trans('8')</option>
            <option value="9"@('9' === widget.get('depth') ? ' selected')>@trans('9')</option>
            <option value="10"@('10' === widget.get('depth') ? ' selected')>@trans('10')</option>
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