<?php

namespace App\Interfaces\Bids;

use App\Models\Product;

interface BidRepositoryInterface
{
    public function placeBid(array $data);
    public function getHighestBid(Product $product);

    public function myBids($userId);
}
