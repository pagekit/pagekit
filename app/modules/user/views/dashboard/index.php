<?php $view->script('user', 'app/modules/user/app/dashboard.js', ['vue-system', 'gravatar']) ?>

<?php if ($widget->get('show') == 'registered'): ?>
<h1 class="uk-h3"><?= _c('{0} No users registered|{1} Last %users% registered user|]1,Inf[ Last %users% registered users', count($users), ['%users%' => count($users)]) ?></h1>
<?php else: ?>
<h1 class="uk-h3"><?= _c('{0} No users logged in|{1} %users% user logged in|]1,Inf[ %users% users logged in', count($users), ['%users%' => count($users)]) ?></h1>
<?php endif ?>

<ul data-user class="uk-grid uk-grid-small uk-grid-medium uk-grid-width-1-4 uk-grid-width-small-1-6 uk-grid-width-medium-1-3 uk-grid-width-xlarge-1-4" data-uk-grid-margin>
    <?php foreach ($users as $user): ?>
    <li>
        <a href="<?= $view->url('@system/user/edit', ['id' => $user->getId()]) ?>" title="<?= $user->getUsername() ?>">
            <img v-gravatar="'<?= $user->getEmail() ?>'" class="uk-border-rounded" width="200" height="200" alt="<?= $user->getUsername() ?>">
        </a>
    </li>
    <?php endforeach ?>
</ul>
