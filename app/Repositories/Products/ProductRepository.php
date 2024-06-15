<?php

namespace App\Repositories\Products;

use App\Interfaces\Products\ProductRepositoryInterface;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductRepository implements ProductRepositoryInterface
{
    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $slug = Str::slug($data['name']);
            $originalSlug = $slug;
            $counter = 1;

            while (Product::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }

            if (isset($data['featured_image'])) {
                $image = $data['featured_image'];
                $year = date('Y');
                $month = date('m');
                $directory = public_path("images/{$year}/{$month}");

                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true);
                }

                $imageName = $slug . '-' . time() . '.' . $image->getClientOriginalExtension();
                $image->move($directory, $imageName);
                $data['featured_image'] = "{$year}/{$month}/" . $imageName;
            }

            $data['slug'] = $slug;
            $data['status'] = "open";

            $product = Product::create($data);

            if (isset($data['gallery_images']) && count($data['gallery_images']) >= 3) {
                foreach ($data['gallery_images'] as $image) {
                    $imageName = time() . '-' . $image->getClientOriginalName();
                    $image->move(public_path('gallery'), $imageName);
                    $product->images()->create(['path' => 'gallery/' . $imageName]);
                }
            } else {
                throw new Exception('At least 3 gallery images are required.');
            }

            DB::commit();
            return $product;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function addImage(Product $product, array $images)
    {
        foreach ($images as $image) {
            $imageName = time() . '-' . $image->getClientOriginalName();
            $image->move(public_path('gallery'), $imageName);
            $product->images()->create(['path' => 'gallery/' . $imageName]);
        }
    }

    public function findById($id)
    {
        return Product::with(['bids' => function ($query) {
            $query->latest()->first();
        }, 'images'])->find($id);
    }

    public function allProducts()
    {
        return Product::with('images')->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getUserProducts($userId)
    {
        return Product::with('images')->where('user_id', $userId)->orderBy('created_at', 'desc')->paginate(10);
    }
}
