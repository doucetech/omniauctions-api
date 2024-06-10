<?php

namespace App\Interfaces\Users;

interface SubscriptionRepositoryInterface
{
    public function createTrialSubscription($userId);

    public function renewSubscription(array $data, $id);
}
