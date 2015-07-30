<?php

namespace Pagekit\Site\Model;

use Pagekit\Application as App;
use Pagekit\System\Model\DataTrait;
use Pagekit\System\Model\NodeInterface;
use Pagekit\System\Model\NodeTrait;
use Pagekit\User\Model\AccessTrait;

/**
 * @Entity(tableClass="@system_node")
 */
class Node implements NodeInterface, \JsonSerializable
{
    use AccessTrait, DataTrait, NodeModelTrait, NodeTrait;

    /** @Column(type="integer") @Id */
    public $id;

    /** @Column(type="integer") */
    public $parent_id = 0;

    /** @Column(type="integer") */
    public $priority = 0;

    /** @Column(type="integer") */
    public $status = 0;

    /** @Column(type="string") */
    public $slug;

    /** @Column(type="string") */
    public $path;

    /** @Column(type="string") */
    public $link;

    /** @Column(type="string") */
    public $title;

    /** @Column(type="string") */
    public $type;

    /** @Column(type="string") */
    public $menu = '';

    public function getUrl($referenceType = false)
    {
        return App::url($this->link, [], $referenceType);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toJson(['url' => $this->getUrl('base')]);
    }
}
