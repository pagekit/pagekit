<div class="uk-form-row">
    <label for="form-page-title" class="uk-form-label"><?php echo __('Page Title') ?></label>
    <div class="uk-form-controls">
        <input id="form-page-title" class="uk-form-width-large" type="text" name="page[title]" value="<?= $page->getTitle() ?>">
    </div>
</div>

<div class="uk-form-row">
    <label for="form-url" class="uk-form-label"><?php echo __('Content') ?></label>
    <div class="uk-form-controls">
        <textarea name="page[content]" class="uk-form-width-large"><?= $page->getContent() ?></textarea>
    </div>
</div>
