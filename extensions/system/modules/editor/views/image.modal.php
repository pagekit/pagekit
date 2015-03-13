<div id="editor-image" class="uk-modal">
    <div class="uk-modal-dialog uk-form uk-form-stacked" v-class="uk-modal-dialog-large: view == 'finder'">

        <div v-show="view == 'settings'">
            <h1 class="uk-h3">{{ 'Image' | trans }}</h1>
            <div class="uk-form-row">
                <div class="uk-form-controls">
                    <div class="pk-thumbnail pk-thumbnail-image" v-attr="style: style"></div>
                    <p class="uk-margin-small-top"><a v-on="click: openFinder">{{ 'Select image' | trans }}</a></p>
                </div>
            </div>
            <div class="uk-form-row">
                <label for="form-src" class="uk-form-label">{{ 'URL' | trans }}</label>
                <div class="uk-form-controls">
                    <input id="form-src" type="text" class="uk-width-1-1" v-model="image.src">
                </div>
            </div>
            <div class="uk-form-row">
                <label for="form-alt" class="uk-form-label">{{ 'Alt' | trans }}</label>
                <div class="uk-form-controls">
                    <input id="form-alt" type="text" class="uk-width-1-1" v-model="image.alt">
                </div>
            </div>
            <div class="uk-form-row uk-margin-top">
                <button class="uk-button uk-button-primary uk-modal-close" type="button" v-on="click: update">{{ 'Update' | trans }}</button>
                <button class="uk-button uk-modal-close" type="button">{{ 'Cancel' | trans }}</button>
            </div>
        </div>

        <div v-if="view == 'finder'">
            <h1 class="uk-h3">{{ 'Select Image' | trans }}</h1>
            <div v-component="v-finder" v-ref="finder" v-with="root: finder.root"></div>
            <div class="uk-margin-top">
                <button class="uk-button uk-button-primary" type="button" v-attr="disabled: !finder.select" v-on="click: closeFinder(finder.select)">{{ 'Select' | trans }}</button>
                <button class="uk-button" type="button" v-on="click: closeFinder(false)">{{ 'Cancel' | trans }}</button>
            </div>
        </div>

    </div>
</div>