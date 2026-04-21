<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    /**
     * 一括代入を許可するカラム
     */
    protected $fillable = [
        'product_id',
    ];

    /**
     * 商品とのリレーション
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}