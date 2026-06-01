import { Component, OnInit } from '@angular/core';
import { Cart } from '../../../core/models/cart.model';
import { ToasterService } from '../../../core/services/toaster.service';
import { CartService } from '../cart.service';

@Component({
  selector: 'app-cart',
  templateUrl: './cart.component.html',
})
export class CartComponent implements OnInit {
  cart: Cart | null = null;
  loading = false;

  constructor(
    private cartService: CartService,
    private toaster: ToasterService,
  ) {}

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.loading = true;
    this.cartService.getCart().subscribe({
      next: (c) => {
        this.cart = c;
        this.loading = false;
      },
      error: () => (this.loading = false),
    });
  }

  updateQuantity(itemId: number, quantity: number): void {
    if (quantity < 1) return;
    this.cartService.updateItem(itemId, quantity).subscribe({
      next: (c) => (this.cart = c),
      error: (err) => this.toaster.error(err.error?.message ?? 'Unable to update.'),
    });
  }

  remove(itemId: number): void {
    this.cartService.removeItem(itemId).subscribe({
      next: (c) => {
        this.cart = c;
        this.toaster.success('Item removed.');
      },
    });
  }

  clear(): void {
    if (!confirm('Clear entire cart?')) return;
    this.cartService.clear().subscribe({
      next: (c) => {
        this.cart = c;
        this.toaster.success('Cart cleared.');
      },
    });
  }
}
