<?php

namespace Pagekit\Widget\Model;

use Pagekit\System\Model\DataTrait;
use Pagekit\User\Model\AccessTrait;

/**
 * @Entity(tableClass="@system_widget")
 */
class Widget implements \JsonSerializable
{
    use AccessTrait, DataTrait, WidgetModelTrait;

    /** @Column(type="integer") @Id */
    public $id;

    /** @Column */
    public $title = '';

    /** @Column(type="string") */
    public $type;

    /** @Column(type="integer") */
    public $status = 1;

    /** @Column(name="nodes", type="simple_array") */
    public $nodes = [];
}
