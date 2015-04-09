<div v-cloak>
    <div class="pk-toolbar uk-form uk-clearfix">
        <div v-if="isWritable()" class="uk-float-left">

            <button class="uk-button uk-button-primary uk-form-file">
                {{ 'Upload' | trans }}
                <input type="file" name="files[]" multiple="multiple">
            </button>

            <button class="uk-button" v-on="click: createFolder()">{{ 'Add Folder' | trans }}</button>

            <button class="uk-button pk-button-danger" v-show="selected.length" v-on="click: remove">{{ 'Delete' | trans }}</button>
            <button class="uk-button" v-show="selected.length === 1" v-on="click: rename">{{ 'Rename' | trans }}</button>

        </div>
        <div class="uk-float-right uk-hidden-small">

            <input type="text" placeholder="{{ 'Search' | trans }}" v-model="search" lazy>

            <div class="uk-button-group">
                <button class="uk-button uk-icon-bars" v-class="'uk-active': view == 'table'"     v-on="click: view = 'table'"></button>
                <button class="uk-button uk-icon-th"   v-class="'uk-active': view == 'thumbnail'" v-on="click: view = 'thumbnail'"></button>
            </div>

        </div>
    </div>

    <ul class="uk-breadcrumb pk-breadcrumb">
        <li v-repeat="breadcrumbs" v-class="'uk-active': current">
            <span v-show="current">{{ title }}</span>
            <a v-show="!current" v-on="click: setPath(path)">{{ title }}</a>
        </li>
    </ul>

    <div v-show="upload.running" class="uk-progress uk-progress-striped uk-active">
        <div class="uk-progress-bar" v-style="width: upload.progress + '%'">{{ upload.progress }}%</div>
    </div>

    <div v-partial="#finder-{{ view }}"></div>

    <div v-if="isWritable()" class="uk-placeholder uk-text-center uk-text-muted">
        <img v-attr="src: $url('app/system/assets/images/finder-droparea.svg', true)" width="22" height="22" alt="{{ 'Droparea' | trans }}"> {{ 'Drop files here.' | trans }}
    </div>

</div>
