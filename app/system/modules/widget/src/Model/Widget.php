<?php

namespace Pagekit\Widget\Model;

use Pagekit\Application as App;
use Pagekit\System\Model\DataTrait;
use Pagekit\User\Model\AccessTrait;

/**
 * @Entity(tableClass="@system_widget", eventPrefix="system.widget")
 */
class Widget implements WidgetInterface
{
    use AccessTrait, DataTrait, WidgetModelTrait;

    public $theme = [];
    public $position = '';

    /** @Column(type="integer") @Id */
    protected $id;

    /** @Column */
    protected $title = '';

    /** @Column(type="string") */
    protected $type;

    /** @Column(type="integer") */
    protected $status = 0;

    /** @Column(name="nodes", type="simple_array") */
    protected $nodes = [];

    /** @Column(type="json_array") */
    protected $data = [];

    public function getId()
    {
        return (int) $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
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
