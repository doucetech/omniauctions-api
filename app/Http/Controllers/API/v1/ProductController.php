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
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        $product = $this->productRepository->create($data);

        return response()->json($product, 201);
    }

    public function addImages(Request $request, $productId)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product = $this->productRepository->findById($productId);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        foreach ($request->file('images') as $image) {
            $imagePath = $image->store('product_images');

            $this->productRepository->addImage($product, ['path' => $imagePath]);
        }

        return response()->json(['message' => 'Images added successfully'], 201);
    }
}
