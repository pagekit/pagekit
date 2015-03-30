<?php

namespace Pagekit\Database\ORM\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
final class BelongsTo implements Annotation
{
    /** @var string */
    public $targetEntity;

    /** @var string */
    public $keyFrom;

    /** @var string */
    public $keyTo;
}
