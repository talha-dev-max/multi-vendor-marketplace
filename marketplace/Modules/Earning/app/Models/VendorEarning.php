<?php

declare(strict_types=1);

namespace Modules\Earning\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Order\Models\VendorOrder;
use Modules\Vendor\Models\VendorProfile;

class VendorEarning extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_RELEASED = 'released';

    protected $fillable = [
        'vendor_order_id',
        'vendor_id',
        'gross',
        'commission',
        'net',
        'status',
        'released_at',
    ];

    protected function casts(): array
    {
        return [
            'gross' => 'decimal:2',
            'commission' => 'decimal:2',
            'net' => 'decimal:2',
            'released_at' => 'datetime',
        ];
    }

    public function vendorOrder(): BelongsTo
    {
        return $this->belongsTo(VendorOrder::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(VendorProfile::class, 'vendor_id');
    }
}
