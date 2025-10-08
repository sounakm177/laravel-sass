<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TenantPaymentMethod extends Model
{
     use HasFactory;

    protected $table = 'tenant_payment_methods';

    protected $fillable = [
        'tenant_id',
        'stripe_payment_method_id',
        'stripe_customer_id',
        'brand',
        'last4',
        'exp_month',
        'exp_year',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Relationship: Payment method belongs to a Tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
