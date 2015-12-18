<template>

    <form class="pk-panel-teaser uk-form uk-form-stacked" v-if="editing">

        <div class="uk-form-row">
            <label for="form-feed-title" class="uk-form-label">{{ 'Title' | trans }}</label>

            <div class="uk-form-controls">
                <input id="form-feed-title" class="uk-width-1-1" type="text" name="widget[title]" v-model="widget.title">
            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-feed-url" class="uk-form-label">{{ 'URL' | trans }}</label>

            <div class="uk-form-controls">
                <input id="form-feed-url" class="uk-width-1-1" type="text" name="url" v-model="widget.url" lazy>
            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-feed-count" class="uk-form-label">{{ 'Number of Posts' | trans }}</label>

            <div class="uk-form-controls">
                <select id="form-feed-count" class="uk-width-1-1" v-model="widget.count" number>
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

    </form>

    <div v-show="status != 'loading'">

        <h3 class="uk-panel-title" v-if="widget.title">{{ widget.title }}</h3>

        <ul class="uk-list uk-list-line uk-margin-remove">
            <li v-for="entry in feed.entries | count">
                <a :href="entry.link" target="_blank">{{ entry.title }}</a> <span class="uk-text-muted uk-text-nowrap">{{ entry.publishedDate | relativeDate }}</span>

                <p class="uk-margin-small-top" v-if="widget.content == '1'">{{ entry.contentSnippet }}</p>

                <p class="uk-margin-small-top" v-if="widget.content == '2'">{{ $index == 0 ? entry.contentSnippet : '' }}</p>
            </li>
        </ul>

        <div v-if="status == 'error'">{{ 'Unable to retrieve feed data.' | trans }}</div>

        <div v-if="!widget.url && !editing">{{ 'No URL given.' | trans }}</div>

    </div>

    <div class="uk-text-center" v-else>
        <v-loader></v-loader>
    </div>

</template>

<script>

    module.exports = {

        type: {

            id: 'feed',
            label: 'Feed',
            description: function () {

            },
            defaults: {
                count: 5,
                url: 'http://pagekit.com/blog/feed',
                content: ''
            }

        },

        replace: false,

        props: ['widget', 'editing'],

        data: function () {
            return {
                status: '',
                feed: {}
            }
        },

        filters: {

            count: function (entries) {
                return entries ? entries.slice(0, this.$get('widget.count')) : [];
            }

        },

        watch: {

            'widget.url': function (url) {

                if (!url) {
                    this.$parent.edit(true);
                }

                this.load();
            },

            'widget.count': function (count, old) {
                var entries = this.$get('feed.entries');
                if (entries && count > old && count > entries.length) {
                    this.load();
                }
            }

        },

        ready: function () {
            if (this.$get('widget.url')) {
                this.load();
            }
        },

        methods: {

            load: function () {

                this.$set('feed', {});
                this.$set('status', '');

                if (!this.$get('widget.url')) {
                    return;
                }

                this.$set('status', 'loading');

                // TODO: The Google Feed API is deprecated.
                this.$http.jsonp('//ajax.googleapis.com/ajax/services/feed/load', {v: '1.0', q: this.$get('widget.url'), num: this.$get('widget.count')}).then(function (res) {
                            var data = res.data;

                            if (data.responseStatus === 200) {
                                this.$set('feed', data.responseData.feed);
                                this.$set('status', 'done');
                            } else {
                                this.$set('status', 'error');
                            }
                        }, function () {
                            this.$set('status', 'error');
                        }
                    );
            }

        }

    }

</script>
