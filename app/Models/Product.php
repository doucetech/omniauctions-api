<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'price', 'slug', 'end_time', 'location', 'status', 'featured_image', 'user_id'];

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

    public function getFeaturedImageAttribute($value)
    {
        return Storage::url($value);
    }

}
