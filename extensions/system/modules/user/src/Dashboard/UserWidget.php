<?php

namespace Pagekit\User\Dashboard;

use Pagekit\Application as App;
use Pagekit\User\Entity\User;
use Pagekit\Widget\Model\TypeInterface;
use Pagekit\Widget\Model\WidgetInterface;

class UserWidget implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'widget.user';
    }

    /**
     * {@inheritdoc}
     */
    public function getName($settings = null)
    {
        return __('Users');
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

        return App::view('extensions/system/modules/user/views/dashboard/index.razr', compact('widget', 'users', 'options'));
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        return App::view('extensions/system/modules/user/views/dashboard/edit.razr', compact('widget'));
    }
}
