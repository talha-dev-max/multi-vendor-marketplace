import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './core/guards/auth.guard';
import { GuestGuard } from './core/guards/guest.guard';
import { RoleGuard } from './core/guards/role.guard';
import { AdminLayoutComponent } from './layouts/admin-layout/admin-layout.component';
import { PublicLayoutComponent } from './layouts/public-layout/public-layout.component';
import { VendorLayoutComponent } from './layouts/vendor-layout/vendor-layout.component';

const routes: Routes = [
  {
    path: 'auth',
    canActivate: [GuestGuard],
    loadChildren: () => import('./modules/auth/auth.module').then((m) => m.AuthModule),
  },
  {
    path: '',
    component: PublicLayoutComponent,
    children: [
      {
        path: '',
        loadChildren: () => import('./modules/catalog/catalog.module').then((m) => m.CatalogModule),
      },
      {
        path: 'cart',
        canActivate: [AuthGuard],
        loadChildren: () => import('./modules/cart/cart.module').then((m) => m.CartModule),
      },
    ],
  },
  {
    path: 'vendor',
    component: VendorLayoutComponent,
    canActivate: [AuthGuard, RoleGuard],
    data: { roles: ['vendor'] },
    loadChildren: () => import('./modules/vendor/vendor.module').then((m) => m.VendorModule),
  },
  {
    path: 'admin',
    component: AdminLayoutComponent,
    canActivate: [AuthGuard, RoleGuard],
    data: { roles: ['admin'] },
    loadChildren: () => import('./modules/admin/admin.module').then((m) => m.AdminModule),
  },
  { path: '**', redirectTo: '' },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule],
})
export class AppRoutingModule {}
