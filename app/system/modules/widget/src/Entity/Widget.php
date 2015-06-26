<?php

namespace Pagekit\Widget\Entity;

use Pagekit\Application as App;
use Pagekit\Database\ORM\ModelTrait;
use Pagekit\System\Entity\DataTrait;
use Pagekit\User\Entity\AccessTrait;
use Pagekit\Widget\Model\WidgetInterface;

/**
 * @Entity(tableClass="@system_widget", eventPrefix="system.widget")
 */
class Widget implements WidgetInterface
{
    use AccessTrait, DataTrait, ModelTrait;

    /** @Column(type="integer") @Id */
    protected $id;

    /** @Column(type="string") */
    protected $type;

    /** @Column */
    protected $title = '';

    /** @Column(name="nodes", type="simple_array") */
    protected $nodes = [];

    /** @Column(type="json_array") */
    protected $data = [];

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getNodes()
    {
        return (array) $this->nodes;
    }

    public function setNodes($nodes)
    {
        $this->nodes = $nodes;
    }

    public function jsonSerialize()
    {
        $widget = get_object_vars($this);

        if (!$widget['data']) {
            $widget['data'] = new \stdClass();
        }

        return $widget;
    }
}
