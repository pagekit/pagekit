<?php

namespace Pagekit\View\Helper;

use Pagekit\Application;
use Pagekit\View\ViewInterface;
use Pagekit\View\Asset\AssetManager;

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
     * @param AssetManager  $manager
     */
    public function __construct(ViewInterface $view, AssetManager $manager = null)
    {
        $this->view = $view;
        $this->manager = $manager ?: new AssetManager();

        $view->on('head', function ($event) {
            $event->addResult($this->render());
        }, 5);
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

        return preg_replace('/^.*/m', '        $0', $output);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tmpl';
    }
}
