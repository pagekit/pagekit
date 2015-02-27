<?= __('Hi') . $user->getUsername() ?><br>
<a href="<?= $app['url']->get(@system/auth/logout', ['redirect' => $redirect]) ?>"><?= __('Logout') ?></a>