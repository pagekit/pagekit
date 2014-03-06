<?php

namespace Pagekit\System\Entity;

/**
 * @Entity(tableClass="@system_url_alias", eventPrefix="system.alias")
 */
class Alias
{
    /** @Column(type="integer") @Id */
    protected $id;

    /** @Column */
    protected $source = '';

    /** @Column */
    protected $alias;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function setSource($source)
    {
        $this->source = $source;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;
    }
}
