<?php

namespace Pagekit\DefaultTheme\Controller;

use Pagekit\Framework\Controller\Controller;

/**
 * @Access(admin=true)
 */
class SettingsController extends Controller
{
    /**
     * @View("theme://alpha/views/settings.razr.php")
     */
    public function indexAction()
    {
        return array('meta.title' => __('Settings'), 'config' => array());
    }

    /**
     * @Request({"config": "array"})
     */
    public function saveAction($config = array())
    {
        $this('message')->success(__('Settings saved.'));

        return $this->redirect('@system/themes/settings', array('name' => 'default'));
    }
}
