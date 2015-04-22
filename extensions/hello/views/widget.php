<?= __('Hello Widget!') ?>

<p>
    <?php if ($user->isAuthenticated()) : ?>
        <?= __('You are logged in.') ?>
    <?php else : ?>
        <?= __('Not logged in.') ?>
    <?php endif ?>
</p>
