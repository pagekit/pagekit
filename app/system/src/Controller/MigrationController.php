<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;

/**
 * @Access("system: software updates", admin=true)
 */
class MigrationController extends Controller
{
    /**
     * @Response("system/theme:templates/migration.php", layout=false)
     */
    public function indexAction()
    {
        return [
           '$meta' => [
                'title' => __('Update Pagekit')
            ]
        ];
    }

    /**
     * @Request(csrf=true)
     */
    public function migrateAction()
    {
        if (App::migrator()->create('app/system/migrations', App::config('system')->get('version'))->get()) {
            App::system()->enable();
            App::message()->success(__('Your Pagekit database has been updated successfully.'));
        } else {
            App::message()->warning(__('Your Pagekit database is already up-to-date!'));
        }

        return $this->redirect('@system/admin');
    }
}
