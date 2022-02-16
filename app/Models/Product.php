<?php

namespace App\Models;

use App\Models\ProductGallery;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;
     /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'price',
        'description', 
        'tags', 
        'categories_id'
    ];

    protected $table = 'products';

    public function galleries() {
        return $this->hasMany(ProductGallery::class, 'products_id', 'id');
    }

    public function category() {
        return $this->belongsTo(ProductCategory::class, 'categories_id', 'id');
    }
}
