import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { Cart } from '../../../core/models/cart.model';
import { ToasterService } from '../../../core/services/toaster.service';
import { CustomerOrderService } from '../../catalog/order.service';
import { CartService } from '../cart.service';

@Component({
  selector: 'app-checkout',
  templateUrl: './checkout.component.html',
})
export class CheckoutComponent implements OnInit {
  cart: Cart | null = null;
  form: FormGroup;
  loading = false;
  placing = false;
  error = '';

  constructor(
    private fb: FormBuilder,
    private cartService: CartService,
    private orderService: CustomerOrderService,
    private toaster: ToasterService,
    private router: Router,
  ) {
    this.form = this.fb.group({
      payment_method: ['cod', [Validators.required]],
      shipping_address: this.fb.group({
        full_name: ['', [Validators.required]],
        phone: ['', [Validators.required]],
        address_line1: ['', [Validators.required]],
        address_line2: [''],
        city: ['', [Validators.required]],
        state: ['', [Validators.required]],
        postal_code: ['', [Validators.required]],
        country: ['US', [Validators.required, Validators.minLength(2), Validators.maxLength(2)]],
      }),
    });
  }

  ngOnInit(): void {
    this.loading = true;
    this.cartService.getCart().subscribe({
      next: (c) => {
        this.cart = c;
        this.loading = false;
        if (c.items.length === 0) {
          this.router.navigate(['/cart']);
        }
      },
      error: () => (this.loading = false),
    });
  }

  placeOrder(): void {
    if (this.form.invalid) {
      return;
    }
    this.placing = true;
    this.error = '';

    const payload = {
      ...this.form.value,
      stripe_success_url: `${window.location.origin}/my/orders?checkout=success`,
      stripe_cancel_url: `${window.location.origin}/cart/checkout?checkout=cancel`,
    };

    this.orderService.place(payload).subscribe({
      next: (res) => {
        this.toaster.success('Order placed successfully!');
        if (res.stripe_checkout_url) {
          window.location.href = res.stripe_checkout_url;
        } else {
          this.router.navigate(['/my/orders', res.order.id]);
        }
      },
      error: (err) => {
        this.error = err.error?.message ?? 'Unable to place order.';
        this.placing = false;
      },
    });
  }
}
