<?php

namespace Pagekit\View\Helper;

use Pagekit\Application;
use Pagekit\View\Asset\AssetManager;
use Pagekit\View\View;

class TemplateHelper extends Helper
{
    /**
     * @var AssetManager
     */
    protected $assets;

    /**
     * Constructor.
     *
     * @param AssetManager $assets
     */
    public function __construct(AssetManager $assets = null)
    {
        $this->assets = $assets ?: new AssetManager();
    }

    /**
     * {@inheritdoc}
     */
    public function register(View $view)
    {
        parent::register($view);

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
