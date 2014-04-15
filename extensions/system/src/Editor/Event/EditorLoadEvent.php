<?php

namespace Pagekit\Editor\Event;

use Pagekit\Editor\EditorInterface;
use Pagekit\Framework\Event\Event;

class EditorLoadEvent extends Event
{
    /**
     * @var EditorInterface
     */
    protected $editor;

    /**
     * @return EditorInterface|null
     */
    public function getEditor()
    {
        return $this->editor;
    }

    /**
     * @param EditorInterface $editor
     */
    public function setEditor(EditorInterface $editor)
    {
        $this->editor = $editor;
    }
}
