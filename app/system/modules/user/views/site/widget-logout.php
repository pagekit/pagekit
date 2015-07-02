<?= __('Hi %username%', ['%username%' => $user->getUsername()]) ?><br>
<a href="<?= $view->url('@user/logout', ['redirect' => $redirect]) ?>"><?= __('Logout') ?></a>
