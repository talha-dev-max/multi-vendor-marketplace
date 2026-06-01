import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { environment } from '../../../../environments/environment';
import { Product } from '../../../core/models/catalog.model';
import { ToasterService } from '../../../core/services/toaster.service';
import { UserService } from '../../../core/services/user.service';
import { CartService } from '../../cart/cart.service';
import { CatalogService } from '../catalog.service';

@Component({
  selector: 'app-product-detail',
  templateUrl: './product-detail.component.html',
})
export class ProductDetailComponent implements OnInit {
  product: Product | null = null;
  selectedImage: string | null = null;
  quantity = 1;
  loading = false;
  addingToCart = false;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private catalog: CatalogService,
    private cart: CartService,
    private userService: UserService,
    private toaster: ToasterService,
  ) {}

  ngOnInit(): void {
    const slug = this.route.snapshot.paramMap.get('slug');
    if (!slug) {
      this.router.navigate(['/']);
      return;
    }

    this.loading = true;
    this.catalog.getProduct(slug).subscribe({
      next: (p) => {
        this.product = p;
        this.selectedImage = this.resolveImageUrl(p.images?.[0]?.path, p.images?.[0]?.url);
        this.loading = false;
      },
      error: () => {
        this.toaster.error('Product not found.');
        this.router.navigate(['/']);
      },
    });
  }

  selectImage(path: string | null, url: string | null): void {
    this.selectedImage = this.resolveImageUrl(path, url);
  }

  resolveImageUrl(path: string | undefined | null, url: string | undefined | null): string | null {
    if (url) return url;
    if (path) return `${environment.storageUrl}/${path}`;
    return null;
  }

  addToCart(): void {
    if (!this.product) return;
    if (!this.userService.isLoggedIn()) {
      this.router.navigate(['/auth/login'], {
        queryParams: { redirect: this.router.url },
      });
      return;
    }

    this.addingToCart = true;
    this.cart.addItem(this.product.id, this.quantity).subscribe({
      next: () => {
        this.toaster.success('Added to cart.');
        this.addingToCart = false;
      },
      error: (err) => {
        this.toaster.error(err.error?.message ?? 'Unable to add to cart.');
        this.addingToCart = false;
      },
    });
  }
}
