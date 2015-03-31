<?= __('Hi') . $user->getUsername() ?><br>
<a href="<?= $view->url('@auth/logout', ['redirect' => $redirect]) ?>"><?= __('Logout') ?></a>
