import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { Paginated } from '../../core/models/api-response.model';
import { Category } from '../../core/models/catalog.model';
import { Order } from '../../core/models/order.model';
import { User } from '../../core/models/user.model';
import { AdminStats, VendorProfile } from '../../core/models/vendor.model';
import { BackendApiService } from '../../core/services/backend-api.service';

@Injectable({ providedIn: 'root' })
export class AdminService {
  constructor(private api: BackendApiService) {}

  stats(): Observable<AdminStats> {
    return this.api.get<AdminStats>('/admin/dashboard/stats');
  }

  // Vendors
  listVendors(status: string | null = null, page = 1): Observable<Paginated<VendorProfile>> {
    return this.api.get<Paginated<VendorProfile>>('/admin/vendors', { status, page });
  }

  approveVendor(id: number): Observable<VendorProfile> {
    return this.api.post<VendorProfile>(`/admin/vendors/${id}/approve`);
  }

  rejectVendor(id: number, reason: string): Observable<VendorProfile> {
    return this.api.post<VendorProfile>(`/admin/vendors/${id}/reject`, { reason });
  }

  // Orders
  listOrders(page = 1): Observable<Paginated<Order>> {
    return this.api.get<Paginated<Order>>('/admin/orders', { page });
  }

  getOrder(id: number): Observable<Order> {
    return this.api.get<Order>(`/admin/orders/${id}`);
  }

  // Users
  listUsers(page = 1): Observable<Paginated<User>> {
    return this.api.get<Paginated<User>>('/admin/users', { page });
  }

  // Categories
  listCategories(): Observable<Category[]> {
    return this.api.get<Category[]>('/admin/categories');
  }

  createCategory(payload: Partial<Category>): Observable<Category> {
    return this.api.post<Category>('/admin/categories', payload);
  }

  updateCategory(id: number, payload: Partial<Category>): Observable<Category> {
    return this.api.put<Category>(`/admin/categories/${id}`, payload);
  }

  deleteCategory(id: number): Observable<unknown> {
    return this.api.delete<unknown>(`/admin/categories/${id}`);
  }
}
