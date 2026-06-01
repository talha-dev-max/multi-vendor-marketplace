import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Category } from '../../../core/models/catalog.model';
import { ToasterService } from '../../../core/services/toaster.service';
import { AdminService } from '../admin.service';

@Component({
  selector: 'app-admin-categories',
  templateUrl: './categories.component.html',
})
export class AdminCategoriesComponent implements OnInit {
  categories: Category[] = [];
  loading = false;
  showModal = false;
  editing: Category | null = null;
  form: FormGroup;
  saving = false;

  constructor(
    private fb: FormBuilder,
    private adminService: AdminService,
    private toaster: ToasterService,
  ) {
    this.form = this.fb.group({
      name: ['', [Validators.required]],
      parent_id: [null],
      sort_order: [0],
      is_active: [true, [Validators.required]],
    });
  }

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.loading = true;
    this.adminService.listCategories().subscribe({
      next: (c) => {
        this.categories = c;
        this.loading = false;
      },
      error: () => (this.loading = false),
    });
  }

  openCreate(): void {
    this.editing = null;
    this.form.reset({ name: '', parent_id: null, sort_order: 0, is_active: true });
    this.showModal = true;
  }

  openEdit(c: Category): void {
    this.editing = c;
    this.form.patchValue(c);
    this.showModal = true;
  }

  closeModal(): void {
    this.showModal = false;
    this.editing = null;
  }

  save(): void {
    if (this.form.invalid) return;
    this.saving = true;
    const op = this.editing
      ? this.adminService.updateCategory(this.editing.id, this.form.value)
      : this.adminService.createCategory(this.form.value);

    op.subscribe({
      next: () => {
        this.toaster.success('Category saved.');
        this.saving = false;
        this.closeModal();
        this.load();
      },
      error: (err) => {
        this.toaster.error(err.error?.message ?? 'Save failed.');
        this.saving = false;
      },
    });
  }

  delete(c: Category): void {
    if (!confirm(`Delete category "${c.name}"?`)) return;
    this.adminService.deleteCategory(c.id).subscribe({
      next: () => {
        this.toaster.success('Deleted.');
        this.load();
      },
      error: (err) => this.toaster.error(err.error?.message ?? 'Delete failed.'),
    });
  }
}
