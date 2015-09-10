<?php

namespace Pagekit\Database\ORM\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
final class HasOne implements Annotation
{
    /** @var string */
    public $targetEntity;

    /** @var string */
    public $keyFrom;

    /** @var string */
    public $keyTo;
}
