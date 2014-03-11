<ul class="uk-grid uk-grid-width-medium-1-2 uk-grid-width-large-1-3 uk-grid-width-xlarge-1-4" data-uk-grid-margin data-uk-grid-match="{target:'.uk-panel'}">
    {{ #packages }}
    <li>
        <a class="uk-panel uk-panel-box pk-marketplace-panel uk-overlay-toggle" href="#" data-package="{{ name }}">

            <div class="uk-panel-teaser"><img src="{{ extra.teaser }}" width="800" height="600" alt="{{ title }}"></div>

            <h2 class="uk-panel-title">
                {{ title }}
                <span class="uk-display-block uk-text-small uk-text-muted">by {{ author.name }}</span>
            </h2>

            <p class="uk-margin-remove">{{ description }}</p>

            <div class="uk-overlay-area">
                <div class="uk-overlay-area-content">
                    <button class="uk-button uk-button-primary uk-button-large">@trans('Details')</button>
                </div>
            </div>

        </a>
    </li>
    {{ /packages }}
</ul>