<?php

namespace Pagekit\System\Templating;

use Pagekit\Framework\Application;
use Pagekit\System\Event\EditorLoadEvent;
use Symfony\Component\Templating\Helper\Helper;

class EditorHelper extends Helper
{
    /**
     * @var Application
     */
    protected $app;

    function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'editor';
    }

    /**
     * Renders an editor textarea.
     *
     * @param  string $name
     * @param  string $value
     * @param  array  $attributes
     * @param  array  $parameters
     * @return string
     */
    public function render($name, $value, array $attributes = array(), $parameters = array())
    {
        $this->app['events']->trigger('editor.load', $event = new EditorLoadEvent(array_merge($attributes, compact('name')), $parameters));

        return sprintf('<textarea%s>%s</textarea>', $this->getAttributes($event->getAttributes()), htmlspecialchars($value));
    }

    /**
     * Get html attribute string.
     *
     * @param  array $attributes
     * @return string
     */
    protected function getAttributes(array $attributes)
    {
        $html = '';

        foreach ($attributes as $name => $value) {
            $html .= sprintf(' %s="%s"', $name, htmlspecialchars($value));
        }

        return $html;
    }
}
