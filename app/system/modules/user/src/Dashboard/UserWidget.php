<?php

namespace Pagekit\User\Dashboard;

use Pagekit\Application as App;
use Pagekit\User\Entity\User;
use Pagekit\Widget\Model\Type;
use Pagekit\Widget\Model\WidgetInterface;

class UserWidget extends Type
{
    public function __construct()
    {
        parent::__construct('dashboard.user', __('Users'), null, [
            'show' => 'login',
            'count' => 5
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(WidgetInterface $widget = null)
    {
        if (null === $widget) {
            return __('Displays a list of users.');
        }

        return $widget->get('show') == 'login' ? __('Logged in') : __('Last registered');
    }

    /**
     * {@inheritdoc}
     */
    public function render(WidgetInterface $widget, $options = [])
    {
        $query = User::query();

        if ($widget->get('show') == 'registered') {
            $query->orderBy('registered', 'DESC');
        } else {
            $query->where('access > ?', [date('Y-m-d H:i:s', time() - 300)])->orderBy('access', 'DESC');
        }

        $users = $query->limit($widget->get('count') ?: 8)->get();

        return App::view('system/user:views/widgets/user/index.php', compact('widget', 'users', 'options'));
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        return App::view('system/user:views/widgets/user/edit.php', compact('widget'));
    }
}
