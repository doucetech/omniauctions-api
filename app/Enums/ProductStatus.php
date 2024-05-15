<?php

namespace App\Enums;

class ProductStatus
{
    public const DRAFT = 'draft';
    public const PUBLISH = 'published';

    public static function getStatuses(): array
    {
        return [
            self::DRAFT,
            self::PUBLISH,
        ];
    }
}
