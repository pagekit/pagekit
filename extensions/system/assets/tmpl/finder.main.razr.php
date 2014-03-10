<div>
    <div class="pk-options uk-form uk-clearfix" data-messages='{ "confirm" : "@trans('Are you sure?')", "newname" : "@trans('New Name')", "foldername" : "@trans('Folder Name')" }'>
        <div class="uk-float-left">

            {{ #writable }}
            <button class="uk-button uk-button-primary" data-cmd="createFolder">@trans('New Folder')</button>

            <form class="js-upload uk-form-file" action="@url('@system/media/upload')" method="post">
                <button class="uk-button">@trans('Upload')</button>
                <input type="file" name="files[]" multiple="multiple" onchange="jQuery(this.form).trigger('submit')">
            </form>

            <button class="uk-button uk-button-danger js-show-on-select" data-cmd="removeSelected">@trans('Delete')</button>
            {{ /writable }}

        </div>
        <div class="uk-float-right">

            <input class="js-search" type="text" placeholder="@trans('Search')">

            <div class="uk-button-group">
                <button class="uk-button uk-icon-bars" data-cmd="switchView" data-view="table"></button>
                <button class="uk-button uk-icon-th" data-cmd="switchView" data-view="thumbnail"></button>
            </div>

        </div>
    </div>

    <ul class="uk-breadcrumb pk-breadcrumb js-breadcrumbs">
        <li><a href="#/" data-cmd="loadPath" data-path="/">@trans('Home')</a></li>
    </ul>

    <div class="uk-progress uk-progress-striped uk-active uk-hidden">
        <div class="uk-progress-bar" style="width: 0%;">0%</div>
    </div>

    <div class="js-finder-files"></div>

    <div class="uk-placeholder uk-text-center uk-text-muted js-show-when-empty">
        <img src="@url('asset://system/images/icon-finder-droparea.svg')" width="20" height="20" alt="@trans('Droparea')"> @trans('Drop files here.')
    </div>
</div>