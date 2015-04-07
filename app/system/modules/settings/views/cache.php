<h2 class="pk-form-heading">{{ 'Cache' | trans }}</h2>

<div class="uk-form-row">
    <span class="uk-form-label">{{ 'Cache' | trans }}</span>
    <div class="uk-form-controls uk-form-controls-text">
        <p v-repeat="cache: caches" class="uk-form-controls-condensed">
            <label><input type="radio" v-model="config['cache'].caches.cache.storage" value="{{ $key }}" v-attr="disabled: !cache.supported"> {{ cache.name }}</label>
        </p>
    </div>
</div>
<div class="uk-form-row">
    <span class="uk-form-label">{{ 'Developer' | trans }}</span>
    <div class="uk-form-controls uk-form-controls-text">
        <p class="uk-form-controls-condensed">
            <label><input type="checkbox" v-model="config['cache'].nocache" value="1"> {{ 'Disable cache' | trans }}</label>
        </p>
        <p>
            <button id="clearcache" class="uk-button uk-button-primary">{{ 'Clear Cache' | trans }}</button>
        </p>
    </div>
</div>

<div id="modal-clearcache" class="uk-modal">
   <div class="uk-modal-dialog">

       <h4><?= __('Select caches to clear:') ?></h4>

       <form class="uk-form" action="<?= $view->url('@system/cache/clear') ?>" method="post">

           <div class="uk-form-row">
               <div class="uk-form-controls uk-form-controls-text">
                   <p class="uk-form-controls-condensed">
                       <label><input type="checkbox" name="caches[cache]" value="1" checked> <?= __('System Cache') ?></label>
                   </p>
               </div>
           </div>
           <div class="uk-form-row">
               <div class="uk-form-controls uk-form-controls-text">
                   <p class="uk-form-controls-condensed">
                       <label><input type="checkbox" name="caches[temp]" value="1"> <?= __('Temporary Files') ?></label>
                   </p>
               </div>
           </div>
           <p>
               <button class="uk-button uk-button-primary" type="submit"><?= __('Clear') ?></button>
               <button class="uk-button uk-modal-close" type="submit"><?= __('Cancel') ?></button>
           </p>

       </form>

   </div>
</div>
