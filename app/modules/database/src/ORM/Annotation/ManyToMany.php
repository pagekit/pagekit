<?php

namespace Pagekit\Database\ORM\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
final class ManyToMany implements Annotation
{
    /** @var string */
    public $targetEntity;

    /** @var string */
    public $keyFrom;

    /** @var string */
    public $keyTo;

    /** @var string */
    public $keyThroughFrom;

    /** @var string */
    public $tableThrough;

    /** @var string */
    public $keyThroughTo;

    /** @var array */
    public $orderBy = [];
}
