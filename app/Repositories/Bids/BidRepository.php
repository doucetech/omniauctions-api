<?php

namespace App\Repositories\Bids;

use App\Interfaces\Bids\BidRepositoryInterface;
use App\Models\Bid;
use App\Models\Product;

class BidRepository implements BidRepositoryInterface
{
    public function placeBid(array $data)
    {
        return Bid::updateOrCreate(
            ['product_id' => $data['product_id'], 'user_id' => $data['user_id']],
            ['amount' => $data['amount']]
        );
    }

    public function updateBid($id, array $data)
    {
        return Bid::whereId($id)->update($data);
    }

    public function getHighestBid(Product $product)
    {
        return $product->bids()->orderBy('amount', 'desc')->first();
    }

    public function myBids($userId)
    {
        $bids = Bid::with(['product' => function ($query) {
            $query->where('status', 'open');
        }])
            ->where('user_id', $userId)
            ->whereHas('product', function ($query) {
                $query->where('status', 'open');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $transformedBids = $bids->getCollection()->transform(function ($bid) {
            $highestBid = $this->getHighestBid($bid->product);
            $isHighestBid = $highestBid && $highestBid->user_id === $bid->user_id;
            $message = $isHighestBid ? 'Winning!' : 'Outbidded';

            return [
                'name' => $bid->product->name,
                'slug' => $bid->product->slug,
                'product_id' => $bid->product->id,
                'description' => $bid->product->description,
                'amount' => $bid->amount,
                'location' => $bid->product->location,
                'featured_image' => $bid->product->featured_image,
                'message' => $message,
            ];
        });

        $bids->setCollection($transformedBids);

        return response()->json($bids);
    }

    public function pastBids($userId)
    {
        // Get the paginated bids
        $bids = Bid::with(['product', 'product.user'])
            ->where('user_id', $userId)
            ->whereHas('product', function ($query) {
                $query->whereIn('status', ['sold']);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Filter and transform the bids while maintaining pagination
        $filteredBids = $bids->getCollection()->filter(function ($bid) {
            $highestBid = $this->getHighestBid($bid->product);
            return $highestBid && $highestBid->user_id === $bid->user_id;
        });

        $transformedBids = $filteredBids->transform(function ($bid) {
            $owner = $bid->product->user;
            return [
                'name' => $bid->product->name,
                'slug' => $bid->product->slug,
                'product_id' => $bid->product->id,
                'description' => $bid->product->description,
                'amount' => $bid->amount,
                'location' => $bid->product->location,
                'featured_image' => $bid->product->featured_image,
                'owner' => [
                    'name' => $owner->name,
                    'email' => $owner->email,
                    'phone' => $owner->phone,
                ],
                'status' => $bid->product->status,
                'message' => $bid->product->status === 'sold' ? 'Won' : 'Winning!',
            ];
        });

        $bids->setCollection($transformedBids);

        return response()->json($bids);
    }

}
