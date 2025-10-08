<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StripePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'stripe_price_id',
        'unit_amount',
        'currency',
        'interval',
        'active',
    ];

    /**
     * Relationship: Each price belongs to a product.
     */
    public function product()
    {
        return $this->belongsTo(StripeProduct::class, 'product_id');
    }

    /**
     * Accessor: Get formatted price in dollars.
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->unit_amount / 100, 2) . ' ' . strtoupper($this->currency);
    }
}
