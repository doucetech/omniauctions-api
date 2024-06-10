<?php

namespace App\Repositories\Users;

use App\Interfaces\Users\SubscriptionRepositoryInterface;
use App\Models\Subscription;
use Carbon\Carbon;

class SubscriptionRepository implements SubscriptionRepositoryInterface
{
    public function createTrialSubscription($userId)
    {
        return Subscription::create([
            'user_id' => $userId,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays(30),
            'status' => 'trial',
        ]);
    }

    public function renewSubscription($data, $id)
    {
        return Subscription::whereId($id)->update($data);
    }
}
