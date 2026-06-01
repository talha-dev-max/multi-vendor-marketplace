<?php

declare(strict_types=1);

namespace Modules\Earning\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Earning\Models\VendorEarning;
use Modules\Earning\Repositories\Contracts\VendorEarningRepositoryInterface;

class VendorEarningRepository implements VendorEarningRepositoryInterface
{
    public function findByVendorOrderId(int $vendorOrderId): ?VendorEarning
    {
        return VendorEarning::query()->where('vendor_order_id', $vendorOrderId)->first();
    }

    public function create(int $vendorOrderId, int $vendorId, float $gross, float $commission, float $net): VendorEarning
    {
        return VendorEarning::query()->create([
            'vendor_order_id' => $vendorOrderId,
            'vendor_id' => $vendorId,
            'gross' => $gross,
            'commission' => $commission,
            'net' => $net,
            'status' => VendorEarning::STATUS_PENDING,
        ]);
    }

    public function markReleased(VendorEarning $earning): VendorEarning
    {
        $earning->fill([
            'status' => VendorEarning::STATUS_RELEASED,
            'released_at' => now(),
        ])->save();

        return $earning->fresh();
    }

    public function paginateForVendor(int $vendorId, int $perPage = 15): LengthAwarePaginator
    {
        return VendorEarning::query()
            ->with('vendorOrder.order')
            ->where('vendor_id', $vendorId)
            ->latest()
            ->paginate($perPage);
    }

    public function summaryForVendor(int $vendorId): array
    {
        $rows = VendorEarning::query()
            ->selectRaw('status, SUM(gross) as gross_sum, SUM(net) as net_sum')
            ->where('vendor_id', $vendorId)
            ->groupBy('status')
            ->get();

        $pending = 0.0;
        $released = 0.0;
        $grossTotal = 0.0;
        $netTotal = 0.0;

        foreach ($rows as $row) {
            $grossTotal += (float) $row->gross_sum;
            $netTotal += (float) $row->net_sum;
            if ($row->status === VendorEarning::STATUS_RELEASED) {
                $released += (float) $row->net_sum;
            } else {
                $pending += (float) $row->net_sum;
            }
        }

        return [
            'gross' => round($grossTotal, 2),
            'net' => round($netTotal, 2),
            'pending' => round($pending, 2),
            'released' => round($released, 2),
        ];
    }
}
