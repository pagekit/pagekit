<div class="uk-form-row" v-component="feed-edit" inline-template>

    <div class="uk-form-row">
        <label for="form-feed-url" class="uk-form-label">{{ 'URL' | trans }}</label>
        <div class="uk-form-controls">
            <input id="form-feed-url" class="uk-form-width-large" type="text" name="url" v-model="widget.settings.url" v-valid="required">
            <p class="uk-form-help-block uk-text-danger" v-show="form.url.invalid">{{ 'URL cannot be blank.' | trans }}</p>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-feed-count" class="uk-form-label">{{ 'Number of Posts' | trans }}</label>
        <div class="uk-form-controls">
            <select id="form-weather-count" class="uk-form-width-large" v-model="widget.settings.count">
                <?php foreach ([1,2,3,4,5,6,7,8,9,10] as $value): ?>
                <option value="<?= $value ?>" number><?= $value ?></option>
                <?php endforeach ?>
            </select>
        </div>
    </div>

    <div class="uk-form-row">
        <span class="uk-form-label">{{ 'Post Content' | trans }}</span>
        <div class="uk-form-controls uk-form-controls-text">
            <p class="uk-form-controls-condensed">
                <label><input type="radio" v-model="widget.settings.content" value=""> {{ "Don't show" | trans }}</label>
            </p>
            <p class="uk-form-controls-condensed">
                <label><input type="radio" v-model="widget.settings.content" value="1"> {{ 'Show on all posts' | trans }}</label>
            </p>
            <p class="uk-form-controls-condensed">
                <label><input type="radio" v-model="widget.settings.content" value="2"> {{ 'Only show on first post.' | trans }}</label>
            </p>
        </div>
    </div>

</div>

<script>

    Vue.component('feed-edit', {

        inherit: true,

        ready: function() {

            var self = this;

            this.$watch('widget', function(widget) {

                if (!widget.settings.content) {
                    self.$set('widget.settings.content', '');
                }

                if (!widget.settings.count) {
                    self.$set('widget.settings.count', '5');
                }

            }, true, true);
        }

    });

</script>
