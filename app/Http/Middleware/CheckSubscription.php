<?php

namespace App\Http\Middleware;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $subscription = Subscription::where('user_id', $user->id)->latest()->first();

        if (!$subscription) {
            return redirect()->route('subscription.required');
        }

        if ($subscription->status === SubscriptionStatus::EXPIRED || Carbon::now()->greaterThan($subscription->end_date)) {
            $subscription->update(['status' => SubscriptionStatus::EXPIRED]);

            return redirect()->route('subscription.expired');
        }
        return $next($request);
    }
}
