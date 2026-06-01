import { Component, OnInit } from '@angular/core';
import { Paginated } from '../../../core/models/api-response.model';
import { Order } from '../../../core/models/order.model';
import { AdminService } from '../admin.service';

@Component({
  selector: 'app-admin-orders',
  templateUrl: './orders.component.html',
})
export class AdminOrdersComponent implements OnInit {
  orders: Order[] = [];
  meta: Paginated<Order>['meta'] = { current_page: 1, last_page: 1, per_page: 15, total: 0 };
  loading = false;

  constructor(private adminService: AdminService) {}

  ngOnInit(): void {
    this.load(1);
  }

  load(page: number): void {
    this.loading = true;
    this.adminService.listOrders(page).subscribe({
      next: (res) => {
        this.orders = res.items;
        this.meta = res.meta;
        this.loading = false;
      },
      error: () => (this.loading = false),
    });
  }
}
