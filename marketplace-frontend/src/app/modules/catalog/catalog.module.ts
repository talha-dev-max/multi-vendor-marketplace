import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthGuard } from '../../core/guards/auth.guard';
import { SharedModule } from '../../shared/shared.module';
import { HomeComponent } from './home/home.component';
import { MyOrdersComponent } from './my-orders/my-orders.component';
import { OrderDetailComponent } from './order-detail/order-detail.component';
import { ProductDetailComponent } from './product-detail/product-detail.component';
import { VendorApplyComponent } from './vendor-apply/vendor-apply.component';

const routes: Routes = [
  { path: '', component: HomeComponent },
  { path: 'products', component: HomeComponent },
  { path: 'products/:slug', component: ProductDetailComponent },
  { path: 'my/orders', component: MyOrdersComponent, canActivate: [AuthGuard] },
  { path: 'my/orders/:id', component: OrderDetailComponent, canActivate: [AuthGuard] },
  { path: 'vendor-apply', component: VendorApplyComponent, canActivate: [AuthGuard] },
];

@NgModule({
  declarations: [
    HomeComponent,
    ProductDetailComponent,
    MyOrdersComponent,
    OrderDetailComponent,
    VendorApplyComponent,
  ],
  imports: [SharedModule, RouterModule.forChild(routes)],
})
export class CatalogModule {}
