<?php

namespace Pagekit\Dashboard\Controller;

use Pagekit\Application as App;
use Pagekit\Module\Module;

/**
 * @Access(admin=true)
 */
class DashboardController
{
    /**
     * @var Module
     */
    protected $dashboard;

    /**
     * @var string
     */
    protected $api = 'http://api.openweathermap.org/data/2.5';

    /**
     * @var string
     */
    protected $apiKey = '08c012f513db564bd6d4bae94b73cc94';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->dashboard = App::module('system/dashboard');
    }

    /**
     * @Route("/", methods="GET")
     */
    public function indexAction()
    {
        return [
            '$view' => [
                'title' => __('Dashboard'),
                'name' => 'system/dashboard:views/index.php'
            ],
            '$data' => [
                'widgets' => array_values($this->dashboard->getWidgets()),
                'api' => App::get('system.api'),
                'version' => App::version(),
                'channel' => 'stable'
            ]
        ];
    }

    /**
     * @Request({"widgets": "array"}, csrf=true)
     */
    public function saveWidgetsAction($widgets = [])
    {

        $widgets = array_replace($this->dashboard->getWidgets(), $widgets);

        $this->dashboard->saveWidgets($widgets);

        return ['widgets' => $widgets];
    }


    /**
     * @Route("/", methods="POST")
     * @Route("/{id}", methods="POST", requirements={"id"="\w+"})
     * @Request({"id", "widget": "array"}, csrf=true)
     */
    public function saveAction($id = 0, $widget = [])
    {
        if ($new = !$id) {
            $id = uniqid();
        }

        $widget['id'] = $id;

        $this->dashboard->saveWidgets(array_replace($this->dashboard->getWidgets(), [$id => $widget]));

        return $widget;
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id"="\w+"})
     * @Request({"id"}, csrf=true)
     */
    public function deleteAction($id)
    {
        $widgets = $this->dashboard->getWidgets();

        unset($widgets[$id]);

        $this->dashboard->saveWidgets($widgets);

        return ['message' => __('Widget deleted.')];
    }

    /**
     * @Request({"order": "array"}, csrf=true)
     */
    public function reorderAction($order = [])
    {
        $widgets = $this->dashboard->getWidgets();
        $reordered = [];

        foreach ($order as $id) {
            if ($widget = $this->dashboard->getWidget($id)) {
                $reordered[$id] = $widget;
            }
        }

        if (count($widgets) == count($reordered)) {
            $this->dashboard->saveWidgets($reordered);
        }

        return ['message' => __('Widgets reordered.')];
    }

    /**
     * @Request({"data": "array", "action": "string",})
     */
    public function weatherAction($data, $action)
    {
        $url = $this->api;

        if ($action === 'weather') {
            $url .= '/weather';
        } elseif ($action === 'find') {
            $url .= '/find';
        }

        $data['APPID'] = $this->apiKey;
        $url .= '?' . http_build_query($data);

        return App::response(file_get_contents($url), 200, ['Content-Type' => 'application/json']);
    }
}
