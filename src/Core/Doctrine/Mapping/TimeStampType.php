<?php

declare(strict_types=1);

namespace App\Core\Doctrine\Mapping;

use DateTimeImmutable;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

final class TimeStampType extends Type
{
    public const TYPE = 'timestamp';

    /**
     * {@inheritDoc}
     *
     * @param array $column
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'timestamp';
    }

    /**
     * @throws ConversionException
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        try {
            return $value?->setTimezone($this->getUTCDateTimeZone())->format($platform->getDateTimeFormatString());
        } catch (ConversionException $exception) {
            throw $exception::conversionFailedInvalidType($value, self::TYPE, ['null', 'DateTimeImmutable']);
        }
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?DateTimeImmutable
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof DateTimeImmutable) {
            $value = clone $value;

            return $value->setTimezone($this->getUTCDateTimeZone());
        }

        $convertedValue = DateTimeImmutable::createFromFormat(
            $platform->getDateTimeFormatString(),
            $value,
            $this->getUTCDateTimeZone(),
        );

        if ($convertedValue === false) {
            throw ConversionException::conversionFailedFormat(
                $value,
                self::TYPE,
                $platform->getDateTimeFormatString(),
            );
        }

        return $convertedValue;
    }

    public function getName(): string
    {
        return self::TYPE;
    }

    private function getUTCDateTimeZone(): DateTimeZone
    {
        return new DateTimeZone('UTC');
    }
}
