import { Component, OnInit } from '@angular/core';
import { Paginated } from '../../../core/models/api-response.model';
import { Product } from '../../../core/models/catalog.model';
import { ToasterService } from '../../../core/services/toaster.service';
import { VendorService } from '../vendor.service';

@Component({
  selector: 'app-vendor-products',
  templateUrl: './products.component.html',
})
export class VendorProductsComponent implements OnInit {
  products: Product[] = [];
  meta: Paginated<Product>['meta'] = { current_page: 1, last_page: 1, per_page: 15, total: 0 };
  loading = false;

  constructor(
    private vendorService: VendorService,
    private toaster: ToasterService,
  ) {}

  ngOnInit(): void {
    this.load(1);
  }

  load(page: number): void {
    this.loading = true;
    this.vendorService.listProducts(page).subscribe({
      next: (res) => {
        this.products = res.items;
        this.meta = res.meta;
        this.loading = false;
      },
      error: () => (this.loading = false),
    });
  }

  delete(id: number): void {
    if (!confirm('Delete this product?')) return;
    this.vendorService.deleteProduct(id).subscribe({
      next: () => {
        this.toaster.success('Product deleted.');
        this.load(this.meta.current_page);
      },
      error: (err) => this.toaster.error(err.error?.message ?? 'Delete failed.'),
    });
  }
}
