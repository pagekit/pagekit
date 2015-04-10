<?php

namespace Pagekit\Database\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonArrayType;

class JsonObjectType extends JsonArrayType
{
    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new \ArrayObject(parent::convertToPHPValue($value, $platform));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'json_object';
    }
}
