<div class="uk-form-row">
    <label for="form-menu" class="uk-form-label">@trans('Menu')</label>
    <div class="uk-form-controls">
        <select id="form-menu" class="uk-form-width-large" name="widget[settings][menu]">
            @foreach (menus as menu)
            <option value="@menu.id"@( menu.id == widget.get('menu') ? ' selected' )>@menu.name</option>
            @endforeach
        </select>
    </div>
</div>
<div class="uk-form-row">
    <label for="form-style" class="uk-form-label">@trans('Style')</label>
    <div class="uk-form-controls">
        <select id="form-style" class="uk-form-width-large" name="widget[settings][style]">
            @foreach (styles as style)
            <option value="@style"@( style == widget.get('style') ? ' selected' )>@style</option>
            @endforeach
        </select>
    </div>
</div>