<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Exception;
use Pagekit\User\Entity\User;

/**
 * @Route("/")
 */
class SystemController
{
    /**
     * @Access(admin=true)
     * @Request({"order": "array"})
     * @Response("json")
     */
    public function adminMenuAction($order)
    {
        try {

            if (!$order) {
                throw new Exception('Missing order data.');
            }

            $user = User::find(App::user()->getId());
            $user->set('admin.menu', $order);
            $user->save();

            return ['message' => __('Order saved.')];

        } catch (Exception $e) {

            return ['message' => $e->getMessage(), 'error' => true];
        }
    }
}
