<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'price', 'status', 'user_id'];

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = in_array($value, ProductStatus::getStatuses()) ? $value : ProductStatus::DRAFT;
    }
}
