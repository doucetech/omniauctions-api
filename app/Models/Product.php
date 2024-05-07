<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'price', 'status'];

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = in_array($value, ProductStatus::getStatuses()) ? $value : ProductStatus::DRAFT;
    }
}
