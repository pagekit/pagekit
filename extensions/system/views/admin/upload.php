<div class="uk-placeholder uk-text-center uk-text-muted" v-el="drop">
    <img src="<?= $view->url()->getStatic('extensions/system/assets/images/finder-droparea.svg') ?>" width="22" height="22" alt="{{ 'Droparea' | trans }}">
    <?= __('Drop files here or <a class="uk-form-file">select one<input type="file" name="file" v-el="select"></a>') ?>
</div>

<div class="uk-progress" v-show="progress">
    <div class="uk-progress-bar" v-style="width: progress">{{ progress }}</div>
</div>

<div class="uk-modal" v-el="modal">
    <div class="uk-modal-dialog">

        <div class="uk-alert uk-alert-danger uk-hidden" data-msg="checksum-mismatch">
            {{ 'The checksum of the uploaded package does not match the one from the marketplace. The file might be manipulated.' | trans }}
        </div>

        <div class="uk-alert uk-alert-success uk-hidden" data-msg="update-available">
            {{ 'There is an update available for the uploaded package. Please consider installing it instead.' | trans }}
        </div>

        <div class="uk-grid">
            <div class="uk-width-1-1">
                <img class="uk-align-left uk-margin-bottom-remove" src="{{ pkg.extra.image }}" width="50" height="50" alt="{{ pkg.title }}">
                <h1 class="uk-h2 uk-margin-remove">{{ pkg.title }}</h1>
                <ul class="uk-subnav uk-subnav-line uk-margin-top-remove">
                    <li>{{ pkg.author.name }}</li>
                    <li>{{ 'Version' | trans }} {{ pkg.version }}</li>
                </ul>
            </div>
        </div>

        <hr class="uk-grid-divider">

        <div class="uk-grid">
            <div class="uk-width-1-2">
                <div>{{ pkg.description }}</div>
                <ul>
                    <li>{{ 'Path:' | trans }} {{ pkg.name }}</li>
                    <li>{{ 'Type:' | trans }} {{ pkg.type }}</li>
                </ul>
            </div>
        </div>

        <p>
            <button class="uk-button uk-button-primary" data-install="{{ install }}">{{ 'Install' | trans }}</button>
            <button class="uk-button uk-modal-close">{{ 'Cancel' | trans }}</button>
        </p>

    </div>
</div>