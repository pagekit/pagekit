<?php

namespace Pagekit\View\Helper;

use Pagekit\Application;
use Pagekit\View\ViewManager;
use Pagekit\View\Asset\AssetManager;

class TemplateHelper implements HelperInterface
{
    /**
     * @var ViewManager
     */
    protected $view;

    /**
     * @var AssetManager
     */
    protected $assets;

    /**
     * Constructor.
     *
     * @param ViewManager  $view
     * @param AssetManager $assets
     */
    public function __construct(ViewManager $view, AssetManager $assets = null)
    {
        $this->view = $view;
        $this->assets = $assets ?: new AssetManager();

        $view->on('head', function ($event, $view) {
            $view->addResult($this->render());
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

        foreach ($this->assets as $asset) {
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
