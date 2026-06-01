import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { Paginated } from '../../core/models/api-response.model';
import { Category, Product } from '../../core/models/catalog.model';
import { BackendApiService } from '../../core/services/backend-api.service';

export interface ProductSearchParams {
  q?: string | null;
  category_id?: number | null;
  vendor_id?: number | null;
  price_min?: number | null;
  price_max?: number | null;
  sort?: 'newest' | 'price_asc' | 'price_desc' | 'name';
  page?: number;
  per_page?: number;
}

@Injectable({ providedIn: 'root' })
export class CatalogService {
  constructor(private api: BackendApiService) {}

  listProducts(params: ProductSearchParams): Observable<Paginated<Product>> {
    return this.api.get<Paginated<Product>>('/products', params as Record<string, unknown>);
  }

  getProduct(slug: string): Observable<Product> {
    return this.api.get<Product>(`/products/${slug}`);
  }

  listCategories(): Observable<Category[]> {
    return this.api.get<Category[]>('/categories');
  }
}
