<?php

namespace App\Enums;

class SubscriptionStatus
{
    public const TRIAL = 'trial';
    public const ACTIVE = 'active';
    public const EXPIRED = 'expired';

    public static function getStatuses(): array
    {
        return [
            self::TRIAL,
            self::ACTIVE,
            self::EXPIRED,
        ];
    }
}
