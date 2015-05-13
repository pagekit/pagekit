<template>

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
            <select id="form-weather-count" class="uk-form-width-large" v-model="widget.settings.count" number>
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

</template>

<script>

    module.exports = {

        section: {
            name: 'feed',
            priority: 0,
            active: 'dashboard.feed'
        }

    }

</script>
