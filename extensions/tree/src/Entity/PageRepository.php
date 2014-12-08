<?php

namespace Pagekit\Tree\Entity;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\ApplicationTrait;

class PageRepository extends Repository implements \ArrayAccess
{
    use ApplicationTrait;

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        $pages = parent::findAll();

        foreach ($this['mounts'] as $mount => $label) {

            if (!array_filter($pages, function($page) use ($mount) { return $page->getMount() == $mount; })) {
                $page = new Page;
                $page->setTitle($label);
                $page->setSlug($mount);
                $page->setStatus(1);
                $page->setMount($mount);

                $this->save($page);
                $pages[$page->getId()] = $page;
            }

        }

        return $pages;
    }
}
