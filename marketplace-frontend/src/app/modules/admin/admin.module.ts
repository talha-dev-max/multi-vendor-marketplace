import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { SharedModule } from '../../shared/shared.module';
import { AdminCategoriesComponent } from './categories/categories.component';
import { AdminDashboardComponent } from './dashboard/dashboard.component';
import { AdminOrdersComponent } from './orders/orders.component';
import { AdminUsersComponent } from './users/users.component';
import { VendorApprovalsComponent } from './vendor-approvals/vendor-approvals.component';

const routes: Routes = [
  { path: 'dashboard', component: AdminDashboardComponent },
  { path: 'vendor-approvals', component: VendorApprovalsComponent },
  { path: 'orders', component: AdminOrdersComponent },
  { path: 'users', component: AdminUsersComponent },
  { path: 'categories', component: AdminCategoriesComponent },
  { path: '', redirectTo: 'dashboard', pathMatch: 'full' },
];

@NgModule({
  declarations: [
    AdminDashboardComponent,
    VendorApprovalsComponent,
    AdminOrdersComponent,
    AdminUsersComponent,
    AdminCategoriesComponent,
  ],
  imports: [SharedModule, RouterModule.forChild(routes)],
})
export class AdminModule {}
