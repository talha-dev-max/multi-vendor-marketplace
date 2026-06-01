import { Component, OnInit } from '@angular/core';
import { Paginated } from '../../../core/models/api-response.model';
import { Order } from '../../../core/models/order.model';
import { CustomerOrderService } from '../order.service';

@Component({
  selector: 'app-my-orders',
  templateUrl: './my-orders.component.html',
})
export class MyOrdersComponent implements OnInit {
  orders: Order[] = [];
  meta: Paginated<Order>['meta'] = { current_page: 1, last_page: 1, per_page: 15, total: 0 };
  loading = false;

  constructor(private orderService: CustomerOrderService) {}

  ngOnInit(): void {
    this.load(1);
  }

  load(page: number): void {
    this.loading = true;
    this.orderService.list(page).subscribe({
      next: (res) => {
        this.orders = res.items;
        this.meta = res.meta;
        this.loading = false;
      },
      error: () => (this.loading = false),
    });
  }
}
