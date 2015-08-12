<?php

namespace Pagekit\Database\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\SimpleArrayType as BaseSimpleArrayType;

class SimpleArrayType extends BaseSimpleArrayType
{
    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (is_string($value) && $array = @json_decode("[{$value}]")) {
            return $array;
        }

        return is_array($value) ? $value : parent::convertToPHPValue($value, $platform);
    }
}
