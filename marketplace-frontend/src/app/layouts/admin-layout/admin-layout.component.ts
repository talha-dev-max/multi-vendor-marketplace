import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { User } from '../../core/models/user.model';
import { UserService } from '../../core/services/user.service';

@Component({
  selector: 'app-admin-layout',
  templateUrl: './admin-layout.component.html',
})
export class AdminLayoutComponent {
  user: User | null = null;

  constructor(
    private userService: UserService,
    private router: Router,
  ) {
    this.userService.user$.subscribe((u) => (this.user = u));
  }

  logout(): void {
    this.userService.logout().subscribe({
      next: () => this.router.navigate(['/auth/login']),
      error: () => {
        this.userService.clearLocal();
        this.router.navigate(['/auth/login']);
      },
    });
  }
}
