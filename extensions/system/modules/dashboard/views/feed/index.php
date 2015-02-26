<?php $app['scripts']->queue('feed', 'extensions/system/modules/dashboard/assets/js/feed.js', ['requirejs']) ?>

<?php if ($title = $widget->get('title')): ?>
<h1 class="uk-h3"><?= $title ?></h1>
<?php endif ?>

<div class="js-feed" data-feed="<?= $this->escape(json_encode(['url' => $widget->get('url'), 'count' => $widget->get('count'), 'content' => $widget->get('content'), 'onFirst' => $widget->get('onFirst')])) ?>">
    <div class="js-spinner uk-text-center"><i class="uk-icon-medium uk-icon-spinner uk-icon-spin"></i></div>
</div>