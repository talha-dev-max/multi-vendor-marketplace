import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { Paginated } from '../../core/models/api-response.model';
import { Product } from '../../core/models/catalog.model';
import { VendorOrder } from '../../core/models/order.model';
import { EarningsSummary, VendorEarning, VendorProfile } from '../../core/models/vendor.model';
import { BackendApiService } from '../../core/services/backend-api.service';

@Injectable({ providedIn: 'root' })
export class VendorService {
  constructor(private api: BackendApiService) {}

  // Profile
  getMyProfile(): Observable<VendorProfile> {
    return this.api.get<VendorProfile>('/vendor/profile');
  }

  // Products
  listProducts(page = 1): Observable<Paginated<Product>> {
    return this.api.get<Paginated<Product>>('/vendor/products', { page });
  }

  createProduct(payload: Partial<Product>): Observable<Product> {
    return this.api.post<Product>('/vendor/products', payload);
  }

  updateProduct(id: number, payload: Partial<Product>): Observable<Product> {
    return this.api.put<Product>(`/vendor/products/${id}`, payload);
  }

  deleteProduct(id: number): Observable<unknown> {
    return this.api.delete<unknown>(`/vendor/products/${id}`);
  }

  uploadImage(productId: number, file: File, isPrimary: boolean): Observable<unknown> {
    const fd = new FormData();
    fd.append('image', file);
    fd.append('is_primary', isPrimary ? '1' : '0');
    return this.api.upload<unknown>(`/vendor/products/${productId}/images`, fd);
  }

  deleteImage(productId: number, imageId: number): Observable<unknown> {
    return this.api.delete<unknown>(`/vendor/products/${productId}/images/${imageId}`);
  }

  // Orders
  listOrders(page = 1): Observable<Paginated<VendorOrder>> {
    return this.api.get<Paginated<VendorOrder>>('/vendor/orders', { page });
  }

  updateOrderStatus(id: number, status: string): Observable<VendorOrder> {
    return this.api.put<VendorOrder>(`/vendor/orders/${id}/status`, { status });
  }

  // Earnings
  listEarnings(page = 1): Observable<Paginated<VendorEarning>> {
    return this.api.get<Paginated<VendorEarning>>('/vendor/earnings', { page });
  }

  earningsSummary(): Observable<EarningsSummary> {
    return this.api.get<EarningsSummary>('/vendor/earnings/summary');
  }
}
