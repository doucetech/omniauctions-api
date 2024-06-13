<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Products\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index()
    {
        $products = $this->productRepository->allProducts();
        return response()->json($products, 200);
    }

    public function userProducts()
    {

        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $products = $this->productRepository->getUserProducts($user->id);

        return response()->json($products, 200);
    }

    public function show($id)
    {
        $product = $this->productRepository->findById($id);
        return response()->json($product, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'end_time_option' => 'required|in:1,2,3',
            'featured_image' => 'required|image|mimes:jpeg,png,jpg|max:1024',
            'location' => 'required|string',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['featured_image'] = $request->file('featured_image');
        $endTime = now()->addDays($request->end_time_option);
        $data['end_time'] = $endTime;

        $product = $this->productRepository->create($data);

        return response()->json($product, 201);
    }

    public function addImages(Request $request, $productId)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        $product = $this->productRepository->findById($productId);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $images = $request->file('images');
        $this->productRepository->addImage($product, $images);

        return response()->json(['message' => 'Images added successfully'], 201);
    }

}
