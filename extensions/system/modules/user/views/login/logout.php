<?= __('Hi') . $user->getUsername() ?><br>
<a href="<?= $view->url(@system/auth/logout', ['redirect' => $redirect]) ?>"><?= __('Logout') ?></a>