import { Component, Input } from '@angular/core';
import { Router } from '@angular/router';
import { environment } from '../../../../environments/environment';
import { Product } from '../../../core/models/catalog.model';

@Component({
  selector: 'app-product-card',
  templateUrl: './product-card.component.html',
})
export class ProductCardComponent {
  @Input() product!: Product;

  constructor(private router: Router) {}

  get imageUrl(): string | null {
    const img = this.product.images?.find((i) => i.is_primary) ?? this.product.images?.[0];
    if (img?.url) {
      return img.url;
    }
    if (img?.path) {
      return `${environment.storageUrl}/${img.path}`;
    }
    return null;
  }

  goToProduct(): void {
    this.router.navigate(['/products', this.product.slug]);
  }
}
