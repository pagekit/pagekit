<div id="{{ marker.uid }}" class="uk-overlay uk-display-block">
    {{#src}}
      <img src="{{{ marker.found.3 }}}" alt="{{{ marker.found.2 }}}">
    {{/src}}
    {{^src}}
      <div class="uk-placeholder uk-placeholder-large uk-text-center uk-vertical-align">
          <div class="uk-vertical-align-middle"><img src="@url('asset://system/images/placeholder-editor-image.svg')" width="60" height="60" alt="@trans('Placeholder Image')"></div>
      </div>
    {{/src}}

    <div class="uk-overlay-area">
        <div class="uk-overlay-area-content">
            <div>{{{ marker.found.2 }}}</div>
            <button class="uk-button uk-button-primary js-config" type="button">@trans('Settings')</button>
            <button class="uk-button uk-button-danger js-remove" type="button">@trans('Delete')</button>
        </div>
    </div>
</div>