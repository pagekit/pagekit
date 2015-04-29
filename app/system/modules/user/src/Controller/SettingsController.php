<?php

namespace Pagekit\User\Controller;

use Pagekit\Application as App;

/**
 * @Access("user: settings", admin=true)
 */
class SettingsController
{
    public function indexAction()
    {
        $module = App::module('system/user');

        return [
            '$view' => [
                'title' => __('User Settings'),
                'name'  => 'system/user:views/admin/settings.php'
            ],
            '$data' => [
                'config' => [
                    'registration' => $module->config('registration'),
                    'require_verification' => $module->config('require_verification')
                ]
            ]
        ];
    }
}
