<?php

namespace App\Enums;

class ProductStatus
{
    public const DRAFT = 'draft';
    public const OPEN = 'open';
    public const CLOSED = 'closed';
    public const SOLD = 'sold';
    public const REVIEW = 'review';

    public static function getStatuses(): array
    {
        return [
            self::DRAFT,
            self::OPEN,
            self::CLOSED,
            self::SOLD,
            self::REVIEW,
        ];
    }
}
