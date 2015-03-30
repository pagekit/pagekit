<?php $view->script('feed', 'app/modules/system/modules/dashboard/app/feed.js', 'vue-system') ?>

<?php if ($title = $widget->get('title')): ?>
<h1 class="uk-h3"><?= $title ?></h1>
<?php endif ?>

<div data-feed="<?= $this->escape(json_encode(['url' => $widget->get('url'), 'count' => $widget->get('count'), 'content' => $widget->get('content')])) ?>" v-cloak>
    <div class="uk-text-center" v-show="status == 'loading'">
        <i class="uk-icon-medium uk-icon-spinner uk-icon-spin"></i>
    </div>
    <div class="uk-alert uk-alert-danger" v-show="status == 'error'">{{ 'Unable to retrieve feed data.' | trans }}</div>
    <ul class="uk-list uk-list-line">
        <li v-repeat="entry: feed.entries">
            <a v-attr="href: entry.link">{{ entry.title }}</a> <span class="uk-text-muted uk-text-nowrap">{{ entry.publishedDate }}</span>
            <p class="uk-margin-small-top" v-if="config.content == 1">{{ entry.contentSnippet }}</p>
            <p class="uk-margin-small-top" v-if="config.content == 2">{{ $index == 0 ? entry.contentSnippet : '' }}</p>
        </li>
    </ul>
</div>
