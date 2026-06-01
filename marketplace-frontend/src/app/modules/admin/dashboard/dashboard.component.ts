import { Component, OnInit } from '@angular/core';
import { AdminStats } from '../../../core/models/vendor.model';
import { AdminService } from '../admin.service';

@Component({
  selector: 'app-admin-dashboard',
  templateUrl: './dashboard.component.html',
})
export class AdminDashboardComponent implements OnInit {
  stats: AdminStats | null = null;
  loading = false;

  constructor(private adminService: AdminService) {}

  ngOnInit(): void {
    this.loading = true;
    this.adminService.stats().subscribe({
      next: (s) => {
        this.stats = s;
        this.loading = false;
      },
      error: () => (this.loading = false),
    });
  }
}
