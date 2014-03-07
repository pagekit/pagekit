{{ #data }}
<ul class="uk-grid uk-grid-width-small-1-2 uk-grid-width-large-1-3 uk-grid-width-xlarge-1-4" data-uk-grid-margin data-uk-grid-match="{target:'.uk-panel'}">
    {{ #folders }}
    <li data-name="{{ name }}" data-type="folder" data-url="{{ url }}">
        <div class="uk-panel uk-panel-box uk-text-center uk-visible-hover">
            <div class="uk-panel-teaser">
                <div class="pk-finder-thumbnail pk-finder-thumbnail-folder"></div>
            </div>
            <div class="uk-text-truncate">
                {{ #writable }}
                <input type="checkbox" class="js-select" data-name="{{ name }}">
                {{ /writable }}
                <a href="#" data-cmd="loadPath" data-path="{{ path }}">{{ name }}</a>
                {{ #writable }}
                <ul class="uk-subnav pk-subnav-icon pk-finder-subnav uk-hidden">
                    <li><a class="uk-icon-pencil" data-cmd="rename" data-name="{{ name }}"></a></li>
                    <li><a class="uk-icon-minus-circle" data-cmd="rename" data-name="{{ name }}"></a></li>
                </ul>
                {{ /writable }}
            </div>
        </div>
    </li>
    {{ /folders }}

    {{ #files }}
    <li data-name="{{ name }}" data-url="{{ url }}" data-type="file">
        <div class="uk-panel uk-panel-box uk-text-center uk-visible-hover">
            <div class="uk-panel-teaser">
                <div class="pk-finder-thumbnail pk-finder-thumbnail-file"></div>
            </div>
            <div class="uk-text-nowrap uk-text-truncate">
                {{ #writable }}
                <input type="checkbox" class="js-select" data-name="{{ name }}">
                {{ /writable }}
                {{ name }}
            </div>
            {{ #writable }}
            <ul class="uk-subnav pk-subnav-icon pk-finder-subnav uk-hidden">
                <li><a class="uk-icon-pencil" data-cmd="rename" data-name="{{ name }}"></a></li>
                <li><a class="uk-icon-minus-circle" data-cmd="remove" data-name="{{ name }}"></a></li>
            </ul>
            {{ /writable }}
        </div>
    </li>
    {{ /files }}
</ul>
{{ /data }}