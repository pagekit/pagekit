<?php

namespace Pagekit\Page\Model;

use Pagekit\Database\ORM\ModelTrait;
use Pagekit\System\Model\DataTrait;

/**
 * @Entity(tableClass="@system_page", eventPrefix="page.page")
 */
class Page implements \JsonSerializable
{
    use DataTrait, ModelTrait;

    /** @Column(type="integer") @Id */
    protected $id;

    /** @Column(type="string") */
    protected $title;

    /** @Column */
    protected $content = '';

    /** @Column(type="json_array") */
    protected $data;

    public function getId()
    {
        return $this->id;
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

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }
}
