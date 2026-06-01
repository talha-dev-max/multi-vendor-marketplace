import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { SharedModule } from '../../shared/shared.module';
import { CartComponent } from './cart/cart.component';
import { CheckoutComponent } from './checkout/checkout.component';

const routes: Routes = [
  { path: '', component: CartComponent },
  { path: 'checkout', component: CheckoutComponent },
];

@NgModule({
  declarations: [CartComponent, CheckoutComponent],
  imports: [SharedModule, RouterModule.forChild(routes)],
})
export class CartModule {}
