<ul v-if="files || folders" class="uk-grid uk-grid-width-small-1-2 uk-grid-width-large-1-3 uk-grid-width-xlarge-1-4 pk-thumbnail-border-remove" data-uk-grid-margin data-uk-grid-match="{ target:'.uk-panel' }">

    <li v-repeat="folders | searched">
        <div class="uk-panel uk-panel-box uk-text-center uk-visible-hover">
            <div class="uk-panel-teaser">
                <div class="pk-thumbnail pk-thumbnail-folder"></div>
            </div>
            <div class="uk-text-truncate">
                <input type="checkbox" value="{{ name }}" v-checkbox="selected">
                <a v-on="click: loadPath(path)">{{ name }}</a>
            </div>
        </div>
    </li>

    <li v-repeat="files | searched">
        <div class="uk-panel uk-panel-box uk-text-center uk-visible-hover">
            <div class="uk-panel-teaser">
                <div v-if="isImage(url)" class="pk-thumbnail" style="background-image: url('{{ encodeURI(url) }}');"></div>
                <div v-if="!isImage(url)" class="pk-thumbnail pk-thumbnail-file"></div>
            </div>
            <div class="uk-text-nowrap uk-text-truncate">
                <input type="checkbox" value="{{ name }}" v-checkbox="selected">
                {{ name }}
            </div>
        </div>
    </li>

</ul>
