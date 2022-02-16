<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionItem extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'transaction_id',
        'quantity'
    ];

    protected $table = 'transaction_items';
    public function product(){
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
