<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Bids\BidRepository;
use App\Repositories\Products\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BidController extends Controller
{
    protected $bidRepository;
    protected $productRepository;

    public function __construct(BidRepository $bidRepository, ProductRepository $productRepository)
    {
        $this->bidRepository = $bidRepository;
        $this->productRepository = $productRepository;
    }

    public function userBids()
    {

        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $bids = $this->bidRepository->myBids($user->id);

        return response()->json($bids, 200);
    }

    public function pastBids()
    {

        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $bids = $this->bidRepository->pastBids($user->id);

        return response()->json($bids, 200);
    }

    public function getNextBidOptions($productId)
    {
        $product = $this->productRepository->findById($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $highestBid = $this->bidRepository->getHighestBid($product);
        $currentAmount = $highestBid ? $highestBid->amount : $product->price;

        $nextBids = $this->generateNextBids($currentAmount);

        return response()->json($nextBids, 200);
    }

    public function placeBid(Request $request, $productId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $product = $this->productRepository->findById($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $user = Auth::guard('sanctum')->user();

        if ($product->user_id === $user->id) {
            return response()->json(['message' => 'You cannot bid on your own product'], 403);
        }

        $highestBid = $this->bidRepository->getHighestBid($product);
        $currentAmount = $highestBid ? $highestBid->amount : $product->price;

        $nextBids = $this->generateNextBids($currentAmount);

        if (!in_array($request->amount, $nextBids)) {
            return response()->json(['message' => 'Invalid bid amount'], 400);
        }

        $bid = $this->bidRepository->placeBid([
            'product_id' => $productId,
            'user_id' => $user->id,
            'amount' => $request->amount,
        ]);

        return response()->json($bid, 201);
    }

    private function generateNextBids($currentAmount)
    {
        $increments = [5];
        $nextBids = [];

        foreach ($increments as $increment) {
            $nextBids[] = $currentAmount + $increment;
        }

        return $nextBids;
    }
}