<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StripeProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'stripe_product_id',
        'description',
        'active',
    ];

    /**
     * Relationship: A product has many prices.
     */
    public function prices()
    {
        return $this->hasMany(StripePrice::class, 'product_id');
    }
}
