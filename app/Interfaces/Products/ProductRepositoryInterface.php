<?php

namespace App\Interfaces;

use App\Models\Product;

interface ProductRepositoryInterface
{
    public function create(array $data);
    public function addImage(Product $product, array $data);

    public function findById($id);
}
