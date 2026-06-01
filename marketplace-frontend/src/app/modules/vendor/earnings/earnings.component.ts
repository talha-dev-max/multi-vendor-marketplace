import { Component, OnInit } from '@angular/core';
import { forkJoin } from 'rxjs';
import { Paginated } from '../../../core/models/api-response.model';
import { EarningsSummary, VendorEarning } from '../../../core/models/vendor.model';
import { VendorService } from '../vendor.service';

@Component({
  selector: 'app-vendor-earnings',
  templateUrl: './earnings.component.html',
})
export class VendorEarningsComponent implements OnInit {
  earnings: VendorEarning[] = [];
  summary: EarningsSummary | null = null;
  meta: Paginated<VendorEarning>['meta'] = { current_page: 1, last_page: 1, per_page: 15, total: 0 };
  loading = false;

  constructor(private vendorService: VendorService) {}

  ngOnInit(): void {
    this.loading = true;
    forkJoin({
      earnings: this.vendorService.listEarnings(1),
      summary: this.vendorService.earningsSummary(),
    }).subscribe({
      next: (res) => {
        this.earnings = res.earnings.items;
        this.meta = res.earnings.meta;
        this.summary = res.summary;
        this.loading = false;
      },
      error: () => (this.loading = false),
    });
  }

  loadPage(page: number): void {
    this.vendorService.listEarnings(page).subscribe((res) => {
      this.earnings = res.items;
      this.meta = res.meta;
    });
  }
}
