<?php

namespace Pagekit\System\Controller;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\Application\Exception;
use Pagekit\User\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/")
 */
class SystemController extends Controller
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

    /**
     * @Route("/tmpl/{template}")
     */
    public function tmplAction($template = '')
    {
        $file = App::view()->tmpl()->get($template);

        if (!$file) {
            throw new NotFoundHttpException(__('Template not found.'));
        }

        $output   = App::view($file);
        $response = App::response()->create()
            ->setETag(md5($output))
            ->setPublic();

        if ($response->isNotModified(App::request())) {
            return $response;
        }

        return $response->setContent($output);
    }

    /**
     * @Route("/tmpls/{templates}")
     */
    public function tmplsAction($templates = '')
    {
        $data = [];

        foreach (explode(',', $templates) as $template) {
            if ($file = App::view()->tmpl()->get($template)) {
                $data[$template] = App::view($file);
            }
        }

        return App::response(sprintf('var templates = %s;', json_encode($data)), 200, ['Content-Type' =>'application/javascript']);
    }
}
