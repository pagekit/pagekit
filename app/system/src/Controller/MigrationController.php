<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;

/**
 * @Access("system: software updates", admin=true)
 */
class MigrationController
{
    public function indexAction()
    {
        return [
            '$view' => [
                'title'  => __('Update Pagekit'),
                'name'   => 'system/theme:views/migration.php',
                'layout' => false
            ]
        ];
    }

    /**
     * @Request(csrf=true)
     */
    public function migrateAction()
    {
        $config = App::config('system');

        if ($version = App::migrator()->create('system:migrations', $config->get('version'))->run()) {
            $config->set('version', $version);
            App::message()->success(__('Your Pagekit database has been updated successfully.'));
        } else {
            App::message()->warning(__('Your Pagekit database is already up-to-date!'));
        }

        return App::redirect('@system');
    }
}
