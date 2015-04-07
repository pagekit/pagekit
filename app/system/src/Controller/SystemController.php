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
     * @Response("system:views/admin/settings/index.php")
     */
    public function indexAction()
    {
        $packages = [];

        foreach (App::system()->config('extensions') as $name) {
            if ($extension = App::module($name) and $extension->config('settings.view')) {
                $packages[$extension->name] = App::package()->getRepository('extension')->findPackage($extension->name);
            }
        }

        return ['head.title' => __('Settings'), 'user' => App::user(), 'packages' => $packages];
    }

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
     * @Access("system: manage storage", admin=true)
     * @Response("system:views/admin/settings/storage.php")
     */
    public function storageAction()
    {
        return ['head.title' => __('Storage'), 'root' => App::system()->config('storage'), 'mode' => 'write'];
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
