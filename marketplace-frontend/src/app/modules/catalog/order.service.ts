import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { Paginated } from '../../core/models/api-response.model';
import { Order, PlaceOrderResponse, ShippingAddress } from '../../core/models/order.model';
import { BackendApiService } from '../../core/services/backend-api.service';

export interface PlaceOrderRequest {
  payment_method: 'cod' | 'stripe';
  shipping_address: ShippingAddress;
  stripe_success_url?: string;
  stripe_cancel_url?: string;
}

@Injectable({ providedIn: 'root' })
export class CustomerOrderService {
  constructor(private api: BackendApiService) {}

  place(payload: PlaceOrderRequest): Observable<PlaceOrderResponse> {
    return this.api.post<PlaceOrderResponse>('/orders', payload);
  }

  list(page = 1): Observable<Paginated<Order>> {
    return this.api.get<Paginated<Order>>('/orders', { page });
  }

  get(id: number): Observable<Order> {
    return this.api.get<Order>(`/orders/${id}`);
  }
}
