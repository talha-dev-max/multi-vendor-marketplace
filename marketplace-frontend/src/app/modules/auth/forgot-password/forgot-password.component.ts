import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { UserService } from '../../../core/services/user.service';

@Component({
  selector: 'app-forgot-password',
  templateUrl: './forgot-password.component.html',
})
export class ForgotPasswordComponent {
  form: FormGroup;
  loading = false;
  error = '';
  success = '';

  constructor(
    private fb: FormBuilder,
    private userService: UserService,
  ) {
    this.form = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
    });
  }

  submit(): void {
    if (this.form.invalid) {
      return;
    }
    this.loading = true;
    this.error = '';
    this.success = '';

    this.userService.forgotPassword(this.form.value.email).subscribe({
      next: () => {
        this.success = 'Reset link sent. Check your email.';
        this.loading = false;
      },
      error: (err) => {
        this.error = err.error?.message ?? 'Unable to send reset link.';
        this.loading = false;
      },
    });
  }
}
