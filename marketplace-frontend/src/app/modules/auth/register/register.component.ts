import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { UserService } from '../../../core/services/user.service';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
})
export class RegisterComponent {
  form: FormGroup;
  loading = false;
  error = '';
  fieldErrors: Record<string, string[]> = {};

  constructor(
    private fb: FormBuilder,
    private userService: UserService,
    private router: Router,
  ) {
    this.form = this.fb.group({
      name: ['', [Validators.required, Validators.minLength(2)]],
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(8)]],
      password_confirmation: ['', [Validators.required]],
    });
  }

  submit(): void {
    if (this.form.invalid) {
      return;
    }
    this.loading = true;
    this.error = '';
    this.fieldErrors = {};

    this.userService.register(this.form.value).subscribe({
      next: () => {
        const { email, password } = this.form.value;
        this.userService.login(email, password).subscribe({
          next: () => this.router.navigate(['/']),
          error: (err) => {
            this.error = err.error?.message ?? 'Login failed after registration.';
            this.loading = false;
          },
        });
      },
      error: (err) => {
        this.error = err.error?.message ?? 'Registration failed.';
        this.fieldErrors = err.error?.errors ?? {};
        this.loading = false;
      },
    });
  }
}
