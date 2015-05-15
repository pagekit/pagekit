<?php

namespace Pagekit\Dashboard;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Pagekit\User\Entity\User;

class DashboardModule extends Module
{
    /**
     * Gets a widget.
     *
     * @param  string $id
     * @return array
     */
    public function getWidget($id)
    {
        $widgets = $this->getWidgets();

        return isset($widgets[$id]) ? $widgets[$id] : null;
    }

    /**
     * Gets all user widgets.
     *
     * @return array
     */
    public function getWidgets()
    {
        return App::user()->get('dashboard', $this->config('defaults'));
    }

    /**
     * Save widgets on user.
     *
     * @param array $widgets
     */
    public function saveWidgets(array $widgets)
    {
        $id = App::user()->getId();

        if ($user = User::find($id)) {
            $user->set('dashboard', $widgets);
            $user->save();
        }
    }
}
