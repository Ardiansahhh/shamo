<?php

namespace App\Models;

use App\Models\User;
use App\Models\TransactionItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'address',
        'total_price',
        'shipping_price',
        'status', 
        'payment'
    ];

    protected $table = 'transactions';

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function items() {
        return $this->hasMany(TransactionItem::class, 'transaction_id', 'id');
    } 
}
