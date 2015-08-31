<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;

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

        if ($version = App::migrator()->create('system:migrations', $config->get('version'))->run()) {
            $config->set('version', $version);

            $status = 'success';
            $message = __('Your Pagekit database has been updated successfully.');
        } else {
            $status = 'warning';
            $message = __('Your Pagekit database is already up-to-date!');
        }

        if ($redirect) {
            App::message()->add($status, $message);
            return App::redirect($redirect);
        }

        return App::response()->json(compact('status', 'message'));
    }
}
