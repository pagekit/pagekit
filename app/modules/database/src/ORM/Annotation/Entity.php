<?php

namespace Pagekit\Database\ORM\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class Entity implements Annotation
{
    /** @var string */
    public $tableClass;

    /** @var string */
    public $eventPrefix;
}
