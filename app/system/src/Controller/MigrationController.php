<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;
use Pagekit\Installer\Package\PackageScripts;

/**
 * @Access("system: software updates", admin=true)
 */
class MigrationController
{
    /**
     * @var PackageScripts
     */
    protected $scripts;

    public function __construct()
    {
        $system = App::system();
        $this->scripts = new PackageScripts($system->path.'/scripts.php', $system->config('version'));
    }

    /**
     * @Request({"redirect": "string"})
     */
    public function indexAction($redirect = null)
    {
        if (!$this->scripts->hasUpdates()) {
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
        if ($updates = $this->scripts->hasUpdates()) {
            $this->scripts->update();
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
}
