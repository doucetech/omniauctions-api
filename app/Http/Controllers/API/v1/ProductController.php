<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Repositories\Products\ProductRepository;
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
