<?php

declare(strict_types=1);

namespace App\Outbox\Infrastructure\Persistence\Doctrine\Mapping;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use JsonException;

class JsonBType extends Type
{
    public const TYPE = 'jsonb';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'JSONB';
    }

    /**
     * @param mixed            $value
     * @param AbstractPlatform $platform
     *
     * @return array<array-key, string|int|null>
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        return json_decode($value, true, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws JsonException
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!is_string($value)) {
            $value = json_encode($value, JSON_THROW_ON_ERROR);
        }

        return $value;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): true
    {
        return true;
    }

    public function getName(): string
    {
        return self::TYPE;
    }
}
