<?php

namespace Pagekit\Editor;

use Pagekit\Application as App;
use Pagekit\Editor\Event\EditorLoadEvent;
use Pagekit\System\Event\TmplEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Editor implements EditorInterface, EventSubscriberInterface
{
    /**
     * @var array
     */
    protected $plugins = [];

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @return array
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * @param  string $name
     * @return mixed
     */
    public function getPlugin($name)
    {
        return isset($this->plugins[$name]) ? $this->plugins[$name] : null;
    }

    /**
     * @param string $name
     * @param mixed  $callback
     */
    public function addPlugin($name, $callback)
    {
        $this->plugins[$name] = $callback;
    }

    /**
     * @param string $name
     */
    public function removePlugin($name)
    {
        unset($this->plugins[$name]);
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @param array $attribute
     */
    public function addAttribute($attribute)
    {
        $this->attributes = array_merge($this->attributes, $attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function render($value, array $attributes = [])
    {
        App::scripts('editor', 'extensions/system/modules/editor/assets/js/editor.js', 'requirejs', [
            'data-editor' => json_encode(array_values($this->getPlugins()))
        ]);

        $this->addAttribute([
            'data-editor' => true, 'autocomplete' => 'off', 'style' => 'visibility:hidden; height:543px;',
            'data-finder' => json_encode(['root' => App::config('app.storage')])
        ]);

        return sprintf('<textarea%s>%s</textarea>', $this->parseAttributes(array_merge($this->attributes, $attributes)), htmlspecialchars($value));
    }

    /**
     * Get html attribute string
     *
     * @param  array $attributes
     * @return string
     */
    protected function parseAttributes($attributes)
    {
        $html = '';

        foreach ($attributes as $name => $val) {
            $html .= is_bool($val) ? " $name" : sprintf(' %s="%s"', $name, htmlspecialchars($val));
        }

        return $html;
    }

    /**
     * Loads the editor.
     */
    public function onEditorLoad(EditorLoadEvent $event)
    {
        if ($event->getEditor()) {
            return;
        }

        $event->setEditor($this);

        $this->addPlugin('link', 'extensions/system/modules/editor/assets/js/link');
        $this->addPlugin('video', 'extensions/system/modules/editor/assets/js/video');
        $this->addPlugin('image', 'extensions/system/modules/editor/assets/js/image');
        $this->addPlugin('urlresolver', 'extensions/system/modules/editor/assets/js/urlresolver');
    }

    /**
     * Register Tmpl's callback.
     *
     * @param TmplEvent $event
     */
    public function onSystemTmpl(TmplEvent $event)
    {
        $event->register('image.modal', 'extensions/system/views/tmpl/image.modal.razr');
        $event->register('image.replace', 'extensions/system/views/tmpl/image.replace.razr');
        $event->register('link.modal', 'extensions/system/views/tmpl/link.modal.razr');
        $event->register('link.replace', 'extensions/system/views/tmpl/link.replace.razr');
        $event->register('video.modal', 'extensions/system/views/tmpl/video.modal.razr');
        $event->register('video.replace', 'extensions/system/views/tmpl/video.replace.razr');
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'editor.load' => ['onEditorLoad', -8],
            'system.tmpl' => 'onSystemTmpl'
        ];
    }
}
