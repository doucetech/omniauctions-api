<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
        ]);

        $product = $this->productRepository->create($request->all());

        return response()->json($product, 201);
    }

    public function addImage(Request $request, $productId)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Assuming image upload
        ]);

        $product = $this->productRepository->findById($productId);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $imagePath = $request->file('image')->store('product_images'); // Store the image

        $this->productRepository->addImage($product, ['path' => $imagePath]);

        return response()->json(['message' => 'Image added successfully'], 201);
    }
}
