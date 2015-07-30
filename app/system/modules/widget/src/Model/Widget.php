<?php

namespace Pagekit\Widget\Model;

use Pagekit\Application as App;
use Pagekit\System\Model\DataTrait;
use Pagekit\User\Model\AccessTrait;

/**
 * @Entity(tableClass="@system_widget")
 */
class Widget
{
    use AccessTrait, DataTrait, WidgetModelTrait;

    public $theme = [];
    public $position = '';

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
