import { Injectable } from '@angular/core';
import { Observable, tap } from 'rxjs';
import { Cart } from '../../core/models/cart.model';
import { BackendApiService } from '../../core/services/backend-api.service';
import { CartStateService } from './cart-state.service';

@Injectable({ providedIn: 'root' })
export class CartService {
  constructor(
    private api: BackendApiService,
    private state: CartStateService,
  ) {}

  getCart(): Observable<Cart> {
    return this.api.get<Cart>('/cart').pipe(tap((c) => this.state.set(c)));
  }

  addItem(productId: number, quantity: number): Observable<Cart> {
    return this.api.post<Cart>('/cart/items', { product_id: productId, quantity }).pipe(tap((c) => this.state.set(c)));
  }

  updateItem(itemId: number, quantity: number): Observable<Cart> {
    return this.api.put<Cart>(`/cart/items/${itemId}`, { quantity }).pipe(tap((c) => this.state.set(c)));
  }

  removeItem(itemId: number): Observable<Cart> {
    return this.api.delete<Cart>(`/cart/items/${itemId}`).pipe(tap((c) => this.state.set(c)));
  }

  clear(): Observable<Cart> {
    return this.api.delete<Cart>('/cart').pipe(tap((c) => this.state.set(c)));
  }
}
