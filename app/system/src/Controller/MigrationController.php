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
        if (!$this->getUpdates()) {
            return App::redirect($redirect ?: '@system');
        }

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
        if ($updates = $this->getUpdates()) {
            $manager = new PackageManager();
            $manager->execute($updates);
            $message =  __('Your Pagekit database has been updated successfully.');
        } else {
            $message =  __('Your database is up to date.');
        }

        App::config('system')->set('version', App::version());

        if ($redirect) {
            App::message()->success($message);
            return App::redirect($redirect);
        }

        return App::response()->json(compact('status', 'message'));
    }

    /**
     * @return array
     */
    protected function getUpdates()
    {
        $manager = new PackageManager();

        $scripts = $manager->loadScripts(null, __DIR__ . '/../../scripts.php');
        if (isset($scripts['updates'])) {
            return $manager->filterUpdates($scripts['updates'], App::config('system')->get('version'));
        }

        return [];
    }
}
