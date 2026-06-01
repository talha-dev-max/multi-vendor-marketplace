import { Component, OnInit } from '@angular/core';
import { forkJoin } from 'rxjs';
import { EarningsSummary } from '../../../core/models/vendor.model';
import { VendorService } from '../vendor.service';

@Component({
  selector: 'app-vendor-dashboard',
  templateUrl: './dashboard.component.html',
})
export class VendorDashboardComponent implements OnInit {
  summary: EarningsSummary | null = null;
  productsTotal = 0;
  ordersTotal = 0;
  loading = false;

  constructor(private vendorService: VendorService) {}

  ngOnInit(): void {
    this.loading = true;
    forkJoin({
      summary: this.vendorService.earningsSummary(),
      products: this.vendorService.listProducts(1),
      orders: this.vendorService.listOrders(1),
    }).subscribe({
      next: (res) => {
        this.summary = res.summary;
        this.productsTotal = res.products.meta.total;
        this.ordersTotal = res.orders.meta.total;
        this.loading = false;
      },
      error: () => (this.loading = false),
    });
  }
}
