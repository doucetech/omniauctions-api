<?php

namespace App\Repositories\Bids;

use App\Interfaces\Bids\BidRepositoryInterface;
use App\Models\Bid;
use App\Models\Product;

class BidRepository implements BidRepositoryInterface
{
    public function placeBid(array $data)
    {
        return Bid::create($data);
    }

    public function getHighestBid(Product $product)
    {
        return $product->bids()->orderBy('amount', 'desc')->first();
    }

    public function myBids($userId)
    {
        $bids = Bid::where('user_id', $userId)
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return $bids;
    }
}
