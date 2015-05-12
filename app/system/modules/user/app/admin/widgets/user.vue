<template>

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
            <select class="uk-form-width-large" v-model="widget.settings.count" number>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
                <option value="13">13</option>
                <option value="14">14</option>
                <option value="15">15</option>
                <option value="16">16</option>
            </select>
        </div>
    </div>

</template>

<script>

    module.exports = {

        section: {
            name: 'user',
            priority: 0,
            active: 'dashboard.user'
        },

        template: __vue_template__

    };

    window.Dashboard.component('user', module.exports);

</script>
