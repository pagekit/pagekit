<?php

namespace Pagekit\User\Entity;

use Pagekit\Component\Database\ORM\Repository;

class AccessLevelRepository extends Repository
{
    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->query()->orderBy('priority')->get();
    }
}
