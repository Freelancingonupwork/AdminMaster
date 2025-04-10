<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public function parentCategory(){
        return $this->belongsTo('App\Models\Category','parent_id')->select('id', 'category_name');
    }

    public function subCategory(){
        return $this->hasMany(Category::class ,'parent_id', 'id')->where('status',1);
    }
}
