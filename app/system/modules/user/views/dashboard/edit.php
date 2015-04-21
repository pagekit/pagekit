<div class="uk-form-row" v-component="user-edit" inline-template>

    <div class="uk-form-row">
        <span class="uk-form-label">{{ 'User Type' | trans }}</span>
        <div class="uk-form-controls uk-form-controls-text">
            <p class="uk-form-controls-condensed">
                <label><input type="radio" v-model="widget.settings.show" value="login"> {{ 'Logged in' | trans }}</label>
            </p>
            <p class="uk-form-controls-condensed">
                <label><input type="radio" v-model="widget.settings.show" value="registered"> {{ 'Last registered' | trans }}</label>
            </p>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Number of Users' | trans }}</label>
        <div class="uk-form-controls">
            <select class="uk-form-width-large" v-model="widget.settings.count">
                <?php foreach ([1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16] as $value): ?>
                <option value="<?= $value ?>"><?= $value ?></option>
                <?php endforeach ?>
            </select>
        </div>
    </div>

</div>

<script>

    Vue.component('user-edit', {

        inherit: true,

        ready: function() {

            var self = this;

            this.$watch('widget', function(widget) {

                if (!widget.settings.show) {
                    self.$set('widget.settings.show', 'login');
                }

                if (!widget.settings.count) {
                    self.$set('widget.settings.count', '1');
                }

            }, true, true);

        }

    });

</script>
