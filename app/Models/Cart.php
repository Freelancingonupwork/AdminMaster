<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    public function product(){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function productCategory(){
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function productCoupon(){
        return $this->belongsTo(Coupon::class, 'coupon_id', 'id');
    }
}
