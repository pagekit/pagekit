<div id="editor-image" class="uk-modal">
    <div class="uk-modal-dialog uk-form uk-form-stacked" v-class="uk-modal-dialog-large: view == 'finder'">
        <div v-show="view == 'settings'">
            <h1 class="uk-h3">{{ 'Add Image' | trans }}</h1>
            <div class="uk-form-row">
                <div class="uk-form-controls">
                    <div class="pk-thumbnail pk-thumbnail-image" v-attr="style: img"></div>
                    <p class="uk-margin-small-top"><a v-on="click: openFinder">{{ 'Select image' | trans }}</a></p>
                </div>
            </div>
            <div class="uk-form-row">
                <label for="form2-url" class="uk-form-label">{{ 'URL' | trans }}</label>
                <div class="uk-form-controls">
                    <input id="form2-url" type="text" class="uk-width-1-1" v-model="url">
                </div>
            </div>
            <div class="uk-form-row">
                <label for="form2-alt" class="uk-form-label">{{ 'Alt' | trans }}</label>
                <div class="uk-form-controls">
                    <input id="form2-alt" type="text" class="uk-width-1-1" v-model="alt">
                </div>
            </div>
            <div class="uk-form-row uk-margin-top">
                <button class="uk-button uk-button-primary" type="button" v-on="click: update">{{ 'Update' | trans }}</button>
                <button class="uk-button uk-modal-close" type="button">{{ 'Cancel' | trans }}</button>
            </div>
        </div>
        <div v-show="view == 'finder'">
            <h1 class="uk-h3">{{ 'Select Image' | trans }}</h1>
            <div v-component="v-finder" v-ref="finder"></div>
            <div class="uk-margin-top">
                <button class="uk-button uk-button-primary" type="button" v-on="click: closeFinder">{{ 'Select' | trans }}</button>
                <button class="uk-button" type="button" v-on="click: closeFinder">{{ 'Cancel' | trans }}</button>
            </div>
        </div>
    </div>
</div>