import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, CanActivate, Router, UrlTree } from '@angular/router';
import { ToasterService } from '../services/toaster.service';
import { UserService } from '../services/user.service';

@Injectable({ providedIn: 'root' })
export class RoleGuard implements CanActivate {
  constructor(
    private userService: UserService,
    private router: Router,
    private toaster: ToasterService,
  ) {}

  canActivate(route: ActivatedRouteSnapshot): boolean | UrlTree {
    const requiredRoles = (route.data['roles'] ?? []) as string[];

    if (!this.userService.isLoggedIn()) {
      return this.router.createUrlTree(['/auth/login']);
    }

    if (requiredRoles.length === 0 || this.userService.hasAnyRole(requiredRoles)) {
      return true;
    }

    this.toaster.error('You do not have access to this area.');
    return this.router.createUrlTree(['/']);
  }
}
