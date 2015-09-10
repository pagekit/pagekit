<?php

namespace Pagekit\Database\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonArrayType as BaseJsonArrayType;

class JsonArrayType extends BaseJsonArrayType
{
    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return is_array($value) ? $value : parent::convertToPHPValue($value, $platform);
    }
}
