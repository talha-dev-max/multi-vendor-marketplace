import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable, tap } from 'rxjs';
import { LoginResponse, User } from '../models/user.model';
import { BackendApiService } from './backend-api.service';
import { LocalStorageService } from './local-storage.service';

const TOKEN_KEY = 'marketplace_token';

@Injectable({ providedIn: 'root' })
export class UserService {
  private userSubject = new BehaviorSubject<User | null>(null);
  public user$ = this.userSubject.asObservable();

  constructor(
    private api: BackendApiService,
    private storage: LocalStorageService,
  ) {}

  get currentUser(): User | null {
    return this.userSubject.value;
  }

  get token(): string | null {
    return this.storage.get<string>(TOKEN_KEY);
  }

  isLoggedIn(): boolean {
    return this.token !== null;
  }

  hasRole(role: string): boolean {
    return this.currentUser?.roles.includes(role) ?? false;
  }

  hasAnyRole(roles: string[]): boolean {
    const mine = this.currentUser?.roles ?? [];
    return roles.some((r) => mine.includes(r));
  }

  hasPermission(permission: string): boolean {
    return this.currentUser?.permissions.includes(permission) ?? false;
  }

  register(payload: { name: string; email: string; password: string; password_confirmation: string }): Observable<User> {
    return this.api.post<User>('/auth/register', payload);
  }

  login(email: string, password: string): Observable<LoginResponse> {
    return this.api.post<LoginResponse>('/auth/login', { email, password }).pipe(
      tap((res) => {
        this.storage.set(TOKEN_KEY, res.token);
        this.userSubject.next(res.user);
      }),
    );
  }

  loadMe(): Observable<User> {
    return this.api.get<User>('/auth/me').pipe(
      tap((user) => this.userSubject.next(user)),
    );
  }

  logout(): Observable<unknown> {
    return this.api.post<unknown>('/auth/logout', {}).pipe(
      tap(() => this.clearLocal()),
    );
  }

  clearLocal(): void {
    this.storage.remove(TOKEN_KEY);
    this.userSubject.next(null);
  }

  forgotPassword(email: string): Observable<unknown> {
    return this.api.post<unknown>('/auth/forgot-password', { email });
  }

  resetPassword(payload: {
    token: string;
    email: string;
    password: string;
    password_confirmation: string;
  }): Observable<unknown> {
    return this.api.post<unknown>('/auth/reset-password', payload);
  }
}
