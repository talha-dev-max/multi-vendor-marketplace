import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Paginated } from '../../../core/models/api-response.model';
import { Category, Product } from '../../../core/models/catalog.model';
import { CatalogService, ProductSearchParams } from '../catalog.service';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
})
export class HomeComponent implements OnInit {
  products: Product[] = [];
  categories: Category[] = [];
  meta: Paginated<Product>['meta'] = { current_page: 1, last_page: 1, per_page: 20, total: 0 };
  loading = false;
  filters: ProductSearchParams = { sort: 'newest', page: 1, per_page: 12 };

  constructor(
    private catalog: CatalogService,
    private route: ActivatedRoute,
    private router: Router,
  ) {}

  ngOnInit(): void {
    this.catalog.listCategories().subscribe((cats) => (this.categories = cats));

    this.route.queryParams.subscribe((params) => {
      this.filters = {
        q: params['q'] ?? null,
        category_id: params['category_id'] ? +params['category_id'] : null,
        price_min: params['price_min'] ? +params['price_min'] : null,
        price_max: params['price_max'] ? +params['price_max'] : null,
        sort: params['sort'] ?? 'newest',
        page: params['page'] ? +params['page'] : 1,
        per_page: 12,
      };
      this.load();
    });
  }

  load(): void {
    this.loading = true;
    this.catalog.listProducts(this.filters).subscribe({
      next: (res) => {
        this.products = res.items;
        this.meta = res.meta;
        this.loading = false;
      },
      error: () => (this.loading = false),
    });
  }

  applyFilters(): void {
    this.router.navigate([], {
      queryParams: {
        q: this.filters.q || null,
        category_id: this.filters.category_id || null,
        price_min: this.filters.price_min || null,
        price_max: this.filters.price_max || null,
        sort: this.filters.sort,
        page: 1,
      },
      queryParamsHandling: 'merge',
    });
  }

  goToPage(page: number): void {
    this.router.navigate([], { queryParams: { page }, queryParamsHandling: 'merge' });
  }

  resetFilters(): void {
    this.router.navigate([], { queryParams: {} });
  }
}
