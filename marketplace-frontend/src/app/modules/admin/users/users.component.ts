import { Component, OnInit } from '@angular/core';
import { Paginated } from '../../../core/models/api-response.model';
import { User } from '../../../core/models/user.model';
import { AdminService } from '../admin.service';

@Component({
  selector: 'app-admin-users',
  templateUrl: './users.component.html',
})
export class AdminUsersComponent implements OnInit {
  users: User[] = [];
  meta: Paginated<User>['meta'] = { current_page: 1, last_page: 1, per_page: 15, total: 0 };
  loading = false;

  constructor(private adminService: AdminService) {}

  ngOnInit(): void {
    this.load(1);
  }

  load(page: number): void {
    this.loading = true;
    this.adminService.listUsers(page).subscribe({
      next: (res) => {
        this.users = res.items;
        this.meta = res.meta;
        this.loading = false;
      },
      error: () => (this.loading = false),
    });
  }
}
