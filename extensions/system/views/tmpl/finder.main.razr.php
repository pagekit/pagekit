<div class="pk-toolbar uk-form uk-clearfix" data-messages='{ "confirm" : "@trans('Are you sure?')", "newname" : "@trans('New Name')", "foldername" : "@trans('Folder Name')" }'>
    <div class="js-writable uk-float-left">

        <button class="uk-button uk-button-primary uk-form-file">
            @trans('Upload')
            <input type="file" name="files[]" multiple="multiple">
        </button>

        <button class="uk-button" data-cmd="createFolder">@trans('Add Folder')</button>

        <button class="uk-button pk-button-danger js-show-on-select" data-cmd="removeSelected">@trans('Delete')</button>
        <button class="uk-button js-show-on-single-select" data-cmd="renameSelected">@trans('Rename')</button>

    </div>
    <div class="uk-float-right uk-hidden-small">

        <input class="js-search" type="text" placeholder="@trans('Search')">

        <div class="uk-button-group">
            <button class="uk-button uk-icon-bars" data-cmd="switchView" data-view="table"></button>
            <button class="uk-button uk-icon-th" data-cmd="switchView" data-view="thumbnail"></button>
        </div>

    </div>
</div>

<ul class="uk-breadcrumb pk-breadcrumb js-breadcrumbs">
    <li><a href="#" data-cmd="loadPath" data-path="/">@trans('Home')</a></li>
</ul>

<div class="uk-progress uk-progress-striped uk-active uk-hidden">
    <div class="uk-progress-bar" style="width: 0%;">0%</div>
</div>

<div class="js-finder-files"></div>