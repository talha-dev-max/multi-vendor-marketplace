import { Component, OnInit } from '@angular/core';
import { Paginated } from '../../../core/models/api-response.model';
import { VendorProfile } from '../../../core/models/vendor.model';
import { ToasterService } from '../../../core/services/toaster.service';
import { AdminService } from '../admin.service';

@Component({
  selector: 'app-vendor-approvals',
  templateUrl: './vendor-approvals.component.html',
})
export class VendorApprovalsComponent implements OnInit {
  vendors: VendorProfile[] = [];
  meta: Paginated<VendorProfile>['meta'] = { current_page: 1, last_page: 1, per_page: 15, total: 0 };
  statusFilter: string | null = 'pending';
  loading = false;

  constructor(
    private adminService: AdminService,
    private toaster: ToasterService,
  ) {}

  ngOnInit(): void {
    this.load(1);
  }

  load(page: number): void {
    this.loading = true;
    this.adminService.listVendors(this.statusFilter, page).subscribe({
      next: (res) => {
        this.vendors = res.items;
        this.meta = res.meta;
        this.loading = false;
      },
      error: () => (this.loading = false),
    });
  }

  changeFilter(status: string | null): void {
    this.statusFilter = status;
    this.load(1);
  }

  approve(id: number): void {
    this.adminService.approveVendor(id).subscribe({
      next: () => {
        this.toaster.success('Vendor approved.');
        this.load(this.meta.current_page);
      },
      error: (err) => this.toaster.error(err.error?.message ?? 'Approve failed.'),
    });
  }

  reject(id: number): void {
    const reason = prompt('Reason for rejection:');
    if (!reason) return;
    this.adminService.rejectVendor(id, reason).subscribe({
      next: () => {
        this.toaster.success('Vendor rejected.');
        this.load(this.meta.current_page);
      },
      error: (err) => this.toaster.error(err.error?.message ?? 'Reject failed.'),
    });
  }
}
