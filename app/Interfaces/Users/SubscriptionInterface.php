<?php

namespace App\Interfaces\Users;

interface SubscriptionInterface
{
    public function createTrialSubscription($userId);

    public function renewSubscription(array $data, $id);
}
