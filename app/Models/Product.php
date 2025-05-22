<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'slug', 'price', 'active'];

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function discount()
    {
        return $this->hasOne(ProductDiscount::class);
    }

    /**
     * Get the discounted price.
     *
     * - Applies percentage or fixed amount discount.
     * - Rounds percent-based discounts to the nearest integer.
     * - Prevents negative prices when using amount-type discounts.
     */
    public function getDiscountedPriceAttribute()
    {
        if (!$this->discount) return $this->price;

        return $this->discount->type === 'percent'
            ? round($this->price * (1 - $this->discount->discount / 100))
            : max(0, $this->price - $this->discount->discount);
    }
}
