<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;
use Pagekit\Installer\Package\PackageManager;

/**
 * @Access("system: software updates", admin=true)
 */
class MigrationController
{
    /**
     * @Request({"redirect": "string"})
     */
    public function indexAction($redirect = null)
    {
        return [
            '$view' => [
                'title' => __('Update Pagekit'),
                'name' => 'system/theme:views/migration.php',
                'layout' => false
            ],
            'redirect' => $redirect
        ];
    }

    /**
     * @Request({"redirect": "string"}, csrf=true)
     */
    public function migrateAction($redirect = null)
    {
        $config = App::config('system');
        $manager = new PackageManager();

        $scripts = $manager->loadScripts(null, __DIR__ . '/../../scripts.php');
        if (isset($scripts['updates'])) {
            $updates = $manager->filterUpdates($scripts['updates'], $config->get('version'));
            $manager->execute($updates);
        }

        $config->set('version', App::version());
        $message =  __('Your Pagekit database has been updated successfully.');

        if ($redirect) {
            App::message()->success($message);
            return App::redirect($redirect);
        }

        return App::response()->json(compact('status', 'message'));
    }
}