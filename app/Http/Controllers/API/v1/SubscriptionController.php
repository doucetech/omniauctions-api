<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Users\SubscriptionRepository;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected $subscriptionRepo;

    public function __construct(SubscriptionRepository $subscriptionRepo)
    {
        $this->subscriptionRepo = $subscriptionRepo;
    }

    public function show(Request $request)
    {
        $subscription = $request->user()->subscription;

        if (!$subscription) {
            return response()->json(['message' => 'No subscription found'], 404);
        }

        return response()->json($subscription, 200);
    }

    public function renew(Request $request, $id)
    {
        $validatedData = $request->validate([
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        $sub = $this->subscriptionRepo->renewSubscription($validatedData, $id);

        return response()->json($sub, 201);
    }

    public function trial()
    {
        $sub = $this->subscriptionRepo->createTrialSubscription(1);

        return response()->json($sub, 201);
    }
}
