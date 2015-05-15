<template>

    <form class="uk-form uk-margin" v-if="editing" v-on="valid: save">

        <div class="uk-form-row">
            <label for="form-feed-title" class="uk-form-label">{{ 'Title' | trans }}</label>
            <div class="uk-form-controls">
                <input id="form-feed-title" class="uk-form-width-large" type="text" name="widget[title]" v-model="widget.title" v-valid="required">
                <p class="uk-form-help-block uk-text-danger" v-show="form['widget[title]'].invalid">{{ 'Title cannot be blank.' | trans }}</p>
            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-feed-url" class="uk-form-label">{{ 'URL' | trans }}</label>
            <div class="uk-form-controls">
                <input id="form-feed-url" class="uk-form-width-large" type="text" name="url" v-model="widget.url" v-valid="required" lazy>
                <p class="uk-form-help-block uk-text-danger" v-show="form.url.invalid">{{ 'URL cannot be blank.' | trans }}</p>
            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-feed-count" class="uk-form-label">{{ 'Number of Posts' | trans }}</label>
            <div class="uk-form-controls">
                <select id="form-weather-count" class="uk-form-width-large" v-model="widget.count" number>
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
                    <label><input type="radio" value="" v-model="widget.content"> {{ "Don't show" | trans }}</label>
                </p>
                <p class="uk-form-controls-condensed">
                    <label><input type="radio" value="1" v-model="widget.content"> {{ 'Show on all posts' | trans }}</label>
                </p>
                <p class="uk-form-controls-condensed">
                    <label><input type="radio" value="2" v-model="widget.content"> {{ 'Only show on first post.' | trans }}</label>
                </p>
            </div>
        </div>

        <hr>

    </form>

    <h1 class="uk-h3" v-show="widget.title">{{ widget.title }}</h1>

    <div>
        <div class="uk-text-center" v-show="status == 'loading'">
            <i class="uk-icon-medium uk-icon-spinner uk-icon-spin"></i>
        </div>
        <div class="uk-alert uk-alert-danger" v-show="status == 'error'">{{ 'Unable to retrieve feed data.' | trans }}</div>
        <ul class="uk-list uk-list-line">
            <li v-repeat="entry: feed.entries | count">
                <a v-attr="href: entry.link">{{ entry.title }}</a> <span class="uk-text-muted uk-text-nowrap">{{ entry.publishedDate }}</span>
                <p class="uk-margin-small-top" v-if="widget.content == '1'">{{ entry.contentSnippet }}</p>
                <p class="uk-margin-small-top" v-if="widget.content == '2'">{{ $index == 0 ? entry.contentSnippet : '' }}</p>
            </li>
        </ul>
    </div>

</template>

<script>

    var Vue = require('vue');
    var $ = require('jquery');
    var api = '//ajax.googleapis.com/ajax/services/feed/load?v=1.0&callback=?';

    module.exports = {

        type: {

            id: 'feed',
            label: 'Feed',
            description: function () {

            },
            defaults: {
                count: 5,
                content: ''
            }

        },

        filters: {

            count: function(entries) {
                return entries ? entries.slice(0, this.$get('widget.count')) : [];
            }

        },

        watch: {

            'widget.url': function() {
                this.load();
            },

            'widget.count': function(count, old) {
                var entries = this.$get('feed.entries');
                if (entries && count > old && count > entries.length) {
                    this.load();
                }
            }

        },

        methods: {

            load: function() {

                var self = this;

                this.$set('feed', {});
                this.$set('status', 'loading');

                $.getJSON(api, {q: this.$get('widget.url'), num: this.$get('widget.count')}, function(data) {

                    if (data.responseStatus === 200) {
                        self.$set('feed', data.responseData.feed);
                        self.$set('status', 'done');
                    } else {
                        self.$set('status', 'error');
                    }

                }).fail(function() {

                    self.$set('status', 'error');

                });

            }

        }

    }

</script>
