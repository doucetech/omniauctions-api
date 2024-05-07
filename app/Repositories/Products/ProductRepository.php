<?php

namespace App\Repositories\Products;

use App\Interfaces\Products\ProductRepositoryInterface;
use App\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{
    public function create(array $data)
    {
        return Product::create($data);
    }

    public function addImage(Product $product, array $data)
    {
        return $product->images()->create($data);
    }

    public function findById($id)
    {
        return Product::find($id);
    }
}
