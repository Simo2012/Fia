<?php

namespace SceauBundle\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonArrayType;

class JsonbArrayType extends JsonArrayType
{
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'JSONB';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'jsonb_array';
    }
}
