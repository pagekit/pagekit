<?php

namespace Pagekit\System\Dashboard;

use Pagekit\Widget\Model\Type;
use Pagekit\Widget\Model\WidgetInterface;

class UserWidget extends Type
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
    public function render(WidgetInterface $widget, $options = array())
    {
        $query = $this('users')->getUserRepository()->query();

        if ($widget->get('show') == 'registered') {
            $query->orderBy('registered', 'DESC');
        } else {
            $query->where('access > ?', array(date('Y-m-d H:i:s', time() - 300)))->orderBy('access', 'DESC');
        }

        $users = $query->limit($widget->get('count') ?: 8)->get();

        return $this('view')->render('system/admin/dashboard/user/index.razr.php', compact('widget', 'users', 'options'));
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm(WidgetInterface $widget)
    {
        return $this('view')->render('system/admin/dashboard/user/edit.razr.php', compact('widget'));
    }
}
