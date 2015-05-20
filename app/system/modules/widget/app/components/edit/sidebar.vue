<template>

    <div class="uk-panel uk-panel-divider uk-form-stacked">

        <div class="uk-form-row">
            <label for="form-position" class="uk-form-label">{{ 'Position' | trans }}</label>
            <div class="uk-form-controls">
                <select id="form-position" name="position" class="uk-width-1-1" v-model="position" options="positionOptions"></select>
            </div>
        </div>

        <div class="uk-form-row">
            <span class="uk-form-label">{{ 'Restrict Access' | trans }}</span>
            <div class="uk-form-controls uk-form-controls-text">
                <p v-repeat="role: roles" class="uk-form-controls-condensed">
                    <label><input type="checkbox" value="{{ role.id }}" v-checkbox="widget.roles"> {{ role.name }}</label>
                </p>
            </div>
        </div>

        <div class="uk-form-row">
            <span class="uk-form-label">{{ 'Options' | trans }}</span>
            <div class="uk-form-controls">
                <label><input type="checkbox" v-model="widget.settings.show_title"> {{ 'Show Title' | trans }}</label>
            </div>
        </div>

    </div>

</template>

<script>

    module.exports = {

        inherit: true,

        computed: {

            positionOptions: function() {
                return [{ text: this.$trans('- Assign -'), value: '' }].concat(
                    _.map(this.positions, function(position) {
                        return { text: this.$trans(position.name), value: position.id };
                    }.bind(this))
                );
            }

        },

        events: {

            save: function(data) {

                data['position'] = this.position;

            }

        }

    }

</script>
