<?php

declare(strict_types=1);

namespace Modules\Order\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PARTIALLY_SHIPPED = 'partially_shipped';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_PARTIALLY_DELIVERED = 'partially_delivered';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELED = 'canceled';

    public const PAYMENT_COD = 'cod';
    public const PAYMENT_STRIPE = 'stripe';

    public const PAYMENT_STATUS_PENDING = 'pending';
    public const PAYMENT_STATUS_PAID = 'paid';
    public const PAYMENT_STATUS_FAILED = 'failed';

    protected $fillable = [
        'customer_id',
        'total',
        'currency',
        'status',
        'payment_method',
        'payment_status',
        'shipping_address',
        'placed_at',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'shipping_address' => 'array',
            'placed_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function vendorOrders(): HasMany
    {
        return $this->hasMany(VendorOrder::class);
    }
}
