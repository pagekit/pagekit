<?php

namespace Pagekit\Templating;

use Pagekit\Templating\Event\TemplateReferenceEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

class TemplateNameParser implements TemplateNameParserInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $events;

    /**
     * The template file extensions.
     *
     * @var string[]
     */
    protected $extensions = [];

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $events
     */
    public function __construct(EventDispatcherInterface $events)
    {
        $this->events = $events;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($name)
    {
        if ($name instanceof TemplateReferenceInterface) {

            $template = $name;

        } else {

            $template = new TemplateReference($name);

            foreach ($this->extensions as $extension => $engine) {
                if ($extension == substr($name, -strlen($extension))) {
                    $template->set('engine', $engine);
                    break;
                }
            }
        }

        $this->events->dispatch('templating.reference', new TemplateReferenceEvent($template));

        return $template;
    }

    /**
     * Register an engine to a template file extension.
     *
     * @param string $engine
     * @param string $extension
     */
    public function addEngine($engine, $extension)
    {
        if (isset($this->extensions[$extension])) {
            unset($this->extensions[$extension]);
        }

        $this->extensions = array_merge([$extension => $engine], $this->extensions);
    }
}
