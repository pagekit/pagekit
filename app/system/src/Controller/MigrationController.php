<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;
use Pagekit\Installer\Package\PackageManager;

/**
 * @Access("system: software updates", admin=true)
 */
class MigrationController
{
    public function indexAction()
    {
        return [
            '$view' => [
                'title' => __('Update Pagekit'),
                'name' => 'system/theme:views/migration.php',
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
        $manager = new PackageManager();

        $scripts = $manager->loadScripts(null, __DIR__ . '/../../scripts.php');
        if (isset($scripts['updates'])) {
            $updates = $manager->filterUpdates($scripts['updates'], $config->get('version'));
            $manager->execute($updates);
        }

        $config->set('version', App::version());

        App::message()->success(__('Your Pagekit database has been updated successfully.'));
        return App::redirect('@system');
    }
}
