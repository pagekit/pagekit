<?php

namespace Pagekit\System\Controller;

use Pagekit\Framework\Controller\Controller;

/**
 * @Access("system: software updates", admin=true)
 */
class MigrationController extends Controller
{
    /**
     * @View("extension://system/theme/templates/migration.razr", layout=false)
     */
    public function indexAction()
    {
        $version = $this('option')->get('system:version');

        if (!$this('migrator')->get('extension://system/migrations', $version)) {

            $this('message')->warning(__('Your Pagekit database is already up-to-date!'));

            return $this->redirect('@system/system/admin');
        }

        return array('head.title' => __('Update Pagekit'));
    }

    /**
     * @Token
     */
    public function migrateAction()
    {
        $this('system')->enable();
        $this('message')->success(__('Your Pagekit database has been updated successfully.'));

        return $this->redirect('@system/system/admin');
    }
}
