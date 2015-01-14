<?php

namespace Pagekit\System\Controller;

use Pagekit\Framework\Application as App;
use Pagekit\Framework\Controller\Controller;

/**
 * @Access("system: software updates", admin=true)
 */
class MigrationController extends Controller
{
    /**
     * @Response("extensions/system/theme/templates/migration.razr", layout=false)
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
        if (App::migrator()->create('extensions/system/migrations', App::option()->get('system:version'))->get()) {
            App::system()->enable();
            App::message()->success(__('Your Pagekit database has been updated successfully.'));
        } else {
            App::message()->warning(__('Your Pagekit database is already up-to-date!'));
        }

        return $this->redirect('@system/admin');
    }
}
