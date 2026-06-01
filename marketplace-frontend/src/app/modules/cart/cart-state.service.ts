import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';
import { Cart } from '../../core/models/cart.model';

@Injectable({ providedIn: 'root' })
export class CartStateService {
  private cartSubject = new BehaviorSubject<Cart | null>(null);
  public cart$ = this.cartSubject.asObservable();

  get count$(): Observable<number> {
    return new Observable<number>((subscriber) => {
      const sub = this.cart$.subscribe((c) => subscriber.next(c?.quantity_total ?? 0));
      return () => sub.unsubscribe();
    });
  }

  set(cart: Cart | null): void {
    this.cartSubject.next(cart);
  }

  clear(): void {
    this.cartSubject.next(null);
  }
}
