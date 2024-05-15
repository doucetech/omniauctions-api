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
}
