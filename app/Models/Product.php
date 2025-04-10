<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function productCategory(){
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function productCoupon(){
        return $this->belongsTo(Coupon::class, 'promo_code', 'id');
    }
}
