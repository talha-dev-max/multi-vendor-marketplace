import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { BackendApiService } from '../../../core/services/backend-api.service';
import { ToasterService } from '../../../core/services/toaster.service';
import { UserService } from '../../../core/services/user.service';

@Component({
  selector: 'app-vendor-apply',
  templateUrl: './vendor-apply.component.html',
})
export class VendorApplyComponent {
  form: FormGroup;
  loading = false;
  error = '';
  success = '';

  constructor(
    private fb: FormBuilder,
    private api: BackendApiService,
    private userService: UserService,
    private toaster: ToasterService,
    private router: Router,
  ) {
    this.form = this.fb.group({
      store_name: ['', [Validators.required, Validators.minLength(3)]],
      description: [''],
    });
  }

  submit(): void {
    if (this.form.invalid) return;
    this.loading = true;
    this.error = '';

    this.api.post('/vendor/applications', this.form.value).subscribe({
      next: () => {
        this.success = 'Application submitted. An admin will review it shortly.';
        this.loading = false;
        this.userService.loadMe().subscribe();
      },
      error: (err) => {
        this.error = err.error?.message ?? 'Unable to submit application.';
        this.loading = false;
      },
    });
  }
}
