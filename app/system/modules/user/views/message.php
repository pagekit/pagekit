<p class="uk-alert <?= $success ? 'uk-alert-success' : 'uk-alert-danger'; ?>">
    <?= $message ?>
</p>

<?php if (isset($link)): ?>
<div class="uk-margin-top uk-text-center">
    <a href="<?= $link ?>" class="uk-button"><?= $label ?></a>
</div>
<?php endif; ?>