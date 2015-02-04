<?php

namespace Pagekit\Mail\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;

/**
 * TODO needs to be updated
 *
 * @Access("system: access settings", admin=true)
 * @Response("json")
 */
class TestController extends Controller
{
    /**
     * @Request({"option": "array"}, csrf=true)
     */
    public function smtpAction($option = [])
    {
        try {

            $option = array_merge([
                'system:mail.port' => '',
                'system:mail.host' => '',
                'system:mail.username' => '',
                'system:mail.password' => '',
                'system:mail.encryption' => ''
            ], $option);

            App::mailer()->testSmtpConnection($option['system:mail.host'], $option['system:mail.port'], $option['system:mail.username'], $option['system:mail.password'], $option['system:mail.encryption']);

            return ['success' => true, 'message' => __('Connection established!')];

        } catch (\Exception $e) {

            return ['success' => false, 'message' => sprintf(__('Connection not established! (%s)'), $e->getMessage())];
        }
    }

    /**
     * Note: If the mailer is accessed prior to this controller action, this will possibly test the wrong mailer
     *
     * @Request({"option": "array"}, csrf=true)
     */
    public function emailAction($option = [])
    {
        try {

            $option = array_merge([
                'system:mail.driver' => '',
                'system:mail.port' => '',
                'system:mail.host' => '',
                'system:mail.username' => '',
                'system:mail.password' => '',
                'system:mail.encryption' => '',
                'system:mail.from.name' => '',
                'system:mail.from.address' => ''
            ], $option);

            foreach ($option as $key => $value) {
                App::option()->set($key, $value);
            }

            $response['success'] = (bool) App::mailer()->create(__('Test email!'), __('Testemail'), $option['system:mail.from.address'])->send();
            $response['message'] = $response['success'] ? __('Mail successfully sent!') : __('Mail delivery failed!');

        } catch (\Exception $e) {

            $response = ['success' => false, 'message' => sprintf(__('Mail delivery failed! (%s)'), $e->getMessage())];
        }

        return $response;
    }
}
