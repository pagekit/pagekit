<?php

namespace Pagekit\System\View\Helper;

use Pagekit\Application;
use Pagekit\View\ViewInterface;
use Pagekit\View\Helper\HelperInterface;

class TemplateHelper implements HelperInterface
{
    /**
     * @var ViewInterface
     */
    protected $view;

    /**
     * @var AssetManager
     */
    protected $manager;

    /**
     * Constructor.
     *
     * @param ViewInterface $view
     */
    public function __construct(ViewInterface $view, Application $app)
    {
        $this->view = $view;
        $this->manager = $app['scripts'];

        $app->on('kernel.response', function ($event) use ($app) {

            $request   = $event->getRequest();
            $response  = $event->getResponse();
            $templates = $this->render();

            if (!$templates || !$event->isMasterRequest() || $request->isXmlHttpRequest() || $response->isRedirection()) {
                return;
            }

            if (false === $pos = strripos($content = $response->getContent(), '</body>')) {
                return;
            }

            $response->setContent(substr_replace($content, $templates, $pos, 0));

        }, 10);
    }

    /**
     * Renders the template tags.
     *
     * @return string
     */
    public function render()
    {
        $output = '';

        foreach ($this->manager as $asset) {
            if ($template = $asset['template']) {
                $output .= sprintf("<script id=\"%s\" type=\"text/template\">%s</script>\n", $asset->getName(), $this->view->render($template));
            }
        }

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tmpl';
    }
}
