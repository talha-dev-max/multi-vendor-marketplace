import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { SharedModule } from '../../shared/shared.module';
import { VendorDashboardComponent } from './dashboard/dashboard.component';
import { VendorEarningsComponent } from './earnings/earnings.component';
import { VendorOrdersComponent } from './orders/orders.component';
import { VendorProductsFormComponent } from './products-form/products-form.component';
import { VendorProductsComponent } from './products/products.component';

const routes: Routes = [
  { path: 'dashboard', component: VendorDashboardComponent },
  { path: 'products', component: VendorProductsComponent },
  { path: 'products/new', component: VendorProductsFormComponent },
  { path: 'products/:id/edit', component: VendorProductsFormComponent },
  { path: 'orders', component: VendorOrdersComponent },
  { path: 'earnings', component: VendorEarningsComponent },
  { path: '', redirectTo: 'dashboard', pathMatch: 'full' },
];

@NgModule({
  declarations: [
    VendorDashboardComponent,
    VendorProductsComponent,
    VendorProductsFormComponent,
    VendorOrdersComponent,
    VendorEarningsComponent,
  ],
  imports: [SharedModule, RouterModule.forChild(routes)],
})
export class VendorModule {}
