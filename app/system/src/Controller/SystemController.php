<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;
use Pagekit\User\Entity\User;

/**
 * @Route("/")
 */
class SystemController
{
    /**
     * @Access(admin=true)
     * @Request({"order": "array"})
     */
    public function adminMenuAction($order)
    {
        if (!$order) {
            App::abort(400, __('Missing order data.'));
        }

        $user = User::find(App::user()->getId());
        $user->set('admin.menu', $order);
        $user->save();

        return ['message' => __('Order saved.')];
    }
}
