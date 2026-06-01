import { Component, OnInit } from '@angular/core';
import { Paginated } from '../../../core/models/api-response.model';
import { VendorOrder } from '../../../core/models/order.model';
import { ToasterService } from '../../../core/services/toaster.service';
import { VendorService } from '../vendor.service';

@Component({
  selector: 'app-vendor-orders',
  templateUrl: './orders.component.html',
})
export class VendorOrdersComponent implements OnInit {
  orders: VendorOrder[] = [];
  meta: Paginated<VendorOrder>['meta'] = { current_page: 1, last_page: 1, per_page: 15, total: 0 };
  loading = false;

  constructor(
    private vendorService: VendorService,
    private toaster: ToasterService,
  ) {}

  ngOnInit(): void {
    this.load(1);
  }

  load(page: number): void {
    this.loading = true;
    this.vendorService.listOrders(page).subscribe({
      next: (res) => {
        this.orders = res.items;
        this.meta = res.meta;
        this.loading = false;
      },
      error: () => (this.loading = false),
    });
  }

  updateStatus(id: number, status: string): void {
    this.vendorService.updateOrderStatus(id, status).subscribe({
      next: () => {
        this.toaster.success(`Status updated to ${status}.`);
        this.load(this.meta.current_page);
      },
      error: (err) => this.toaster.error(err.error?.message ?? 'Update failed.'),
    });
  }

  nextStatus(current: string): string | null {
    return {
      pending: 'confirmed',
      confirmed: 'shipped',
      shipped: 'delivered',
    }[current] ?? null;
  }
}
