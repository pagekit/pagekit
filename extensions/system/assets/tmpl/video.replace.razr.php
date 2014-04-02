<div id="{{ marker.uid }}" class="uk-overlay uk-display-block">
    {{ #src }}
      {{{ preview }}}
    {{ /src }}
    {{ ^src }}
      <div class="uk-placeholder uk-placeholder-large uk-text-center uk-vertical-align">
          <div class="uk-vertical-align-middle"><img src="@url.to('asset://system/images/placeholder-editor-video.svg')" width="60" height="60" alt="@trans('Placeholder Video')"></div>
      </div>
    {{ /src }}

    <div class="uk-overlay-area">
        <div class="uk-overlay-area-content">
            <div>@trans('Video')</div>
            <button class="uk-button uk-button-primary js-config" type="button">@trans('Settings')</button>
            <button class="uk-button uk-button-danger js-remove" type="button">@trans('Delete')</button>
        </div>
    </div>
</div>