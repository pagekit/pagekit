<?php

namespace Pagekit\System\Controller;

use Pagekit\Framework\Controller\Controller;

/**
 * @Access("system: software updates", admin=true)
 */
class MigrationController extends Controller
{
    /**
     * @Response("extension://system/theme/templates/migration.razr", layout=false)
     */
    public function indexAction()
    {
        return ['head.title' => __('Update Pagekit')];
    }

    /**
     * @Request(csrf=true)
     */
    public function migrateAction()
    {
        if ($this['migrator']->create('extension://system/migrations', $this['option']->get('system:version'))->get()) {
            $this['system']->enable();
            $this['message']->success(__('Your Pagekit database has been updated successfully.'));
        } else {
            $this['message']->warning(__('Your Pagekit database is already up-to-date!'));
        }

        return $this->redirect('@system/system/admin');
    }
}
