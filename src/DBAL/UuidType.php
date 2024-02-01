<?php

namespace App\DBAL;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\Uid\Uuid;

/**
 * My custom datatype.
 */
class UuidType extends Type
{
    public const MYTYPE = 'uuid'; // modify to match your type name

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return self::MYTYPE;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        if (null === $value || '' === $value) {
            return null;
        }
        if (false == Uuid::isValid($value)) {
            throw ConversionException::conversionFailed($value, self::MYTYPE);
        }

        return (string) $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if (null === $value || '' === $value) {
            return null;
        }
        if (false == Uuid::isValid($value)) {
            throw ConversionException::conversionFailed($value, self::MYTYPE);
        }

        return (string) $value;
    }

    public function getName(): string
    {
        return self::MYTYPE; // modify to match your constant name
    }

    /**
     * @return string[]
     */
    public function getMappedDatabaseTypes(AbstractPlatform $platform): array
    {
        return [self::MYTYPE];
    }
}