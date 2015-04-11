<?php

namespace Pagekit\Mail\Controller;

use Pagekit\Application as App;

/**
 * TODO needs to be updated
 *
 * @Access("system: access settings", admin=true)
 * @Response("json")
 */
class MailController
{
    /**
     * @Request({"option": "array"}, csrf=true)
     */
    public function smtpAction($option = [])
    {
        try {

            $option = array_merge([
                'port' => '',
                'host' => '',
                'username' => '',
                'password' => '',
                'encryption' => ''
            ], $option);

            App::mailer()->testSmtpConnection($option['host'], $option['port'], $option['username'], $option['password'], $option['encryption']);

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
                'driver' => '',
                'port' => '',
                'host' => '',
                'username' => '',
                'password' => '',
                'encryption' => '',
                'from.name' => '',
                'from.address' => ''
            ], $option);

            foreach ($option as $key => $value) {
                App::config()->set($key, $value);
            }

            $response['success'] = (bool) App::mailer()->create(__('Test email!'), __('Testemail'), $option['from.address'])->send();
            $response['message'] = $response['success'] ? __('Mail successfully sent!') : __('Mail delivery failed!');

        } catch (\Exception $e) {

            $response = ['success' => false, 'message' => sprintf(__('Mail delivery failed! (%s)'), $e->getMessage())];
        }

        return $response;
    }
}
