import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { UserService } from '../../../core/services/user.service';
import { User } from '../../../core/models/user.model';
import { CartStateService } from '../../../modules/cart/cart-state.service';

@Component({
  selector: 'app-navbar',
  templateUrl: './navbar.component.html',
})
export class NavbarComponent implements OnInit {
  user: User | null = null;
  cartCount = 0;
  searchQuery = '';

  constructor(
    public userService: UserService,
    private cartState: CartStateService,
    private router: Router,
  ) {}

  ngOnInit(): void {
    this.userService.user$.subscribe((u) => (this.user = u));
    this.cartState.count$.subscribe((c) => (this.cartCount = c));
  }

  search(): void {
    const q = this.searchQuery.trim();
    this.router.navigate(['/products'], { queryParams: q ? { q } : {} });
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
