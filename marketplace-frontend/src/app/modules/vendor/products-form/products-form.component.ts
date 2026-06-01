import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { Category } from '../../../core/models/catalog.model';
import { ToasterService } from '../../../core/services/toaster.service';
import { CatalogService } from '../../catalog/catalog.service';
import { VendorService } from '../vendor.service';

@Component({
  selector: 'app-vendor-products-form',
  templateUrl: './products-form.component.html',
})
export class VendorProductsFormComponent implements OnInit {
  form: FormGroup;
  categories: Category[] = [];
  editingId: number | null = null;
  loading = false;
  uploadingImage = false;

  constructor(
    private fb: FormBuilder,
    private vendorService: VendorService,
    private catalogService: CatalogService,
    private toaster: ToasterService,
    private route: ActivatedRoute,
    private router: Router,
  ) {
    this.form = this.fb.group({
      name: ['', [Validators.required]],
      description: [''],
      category_id: [null],
      price: [0, [Validators.required, Validators.min(0)]],
      stock: [0, [Validators.required, Validators.min(0)]],
      status: ['draft', [Validators.required]],
    });
  }

  ngOnInit(): void {
    this.catalogService.listCategories().subscribe((c) => (this.categories = c));

    const idParam = this.route.snapshot.paramMap.get('id');
    if (idParam) {
      this.editingId = +idParam;
      // In a real app we'd fetch the single product here. For simplicity, we'll rely on the listing to pre-fill.
    }
  }

  submit(): void {
    if (this.form.invalid) return;
    this.loading = true;

    const op = this.editingId
      ? this.vendorService.updateProduct(this.editingId, this.form.value)
      : this.vendorService.createProduct(this.form.value);

    op.subscribe({
      next: (p) => {
        this.toaster.success(this.editingId ? 'Product updated.' : 'Product created.');
        this.editingId = p.id;
        this.loading = false;
      },
      error: (err) => {
        this.toaster.error(err.error?.message ?? 'Save failed.');
        this.loading = false;
      },
    });
  }

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (!input.files || input.files.length === 0 || !this.editingId) return;
    const file = input.files[0];
    this.uploadingImage = true;
    this.vendorService.uploadImage(this.editingId, file, false).subscribe({
      next: () => {
        this.toaster.success('Image uploaded.');
        this.uploadingImage = false;
      },
      error: (err) => {
        this.toaster.error(err.error?.message ?? 'Upload failed.');
        this.uploadingImage = false;
      },
    });
  }

  goBack(): void {
    this.router.navigate(['/vendor/products']);
  }
}
