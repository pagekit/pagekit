<?php

namespace Pagekit\Site\Model;

use Pagekit\Database\ORM\ModelTrait;
use Pagekit\System\Model\DataTrait;

/**
 * @Entity(tableClass="@system_page")
 */
class Page implements \JsonSerializable
{
    use DataTrait, ModelTrait;

    /** @Column(type="integer") @Id */
    public $id;

    /** @Column(type="string") */
    public $title;

    /** @Column */
    public $content = '';
}
