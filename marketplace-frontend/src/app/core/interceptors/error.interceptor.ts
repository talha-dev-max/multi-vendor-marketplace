import { HttpErrorResponse, HttpEvent, HttpHandler, HttpInterceptor, HttpRequest } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { LocalStorageService } from '../services/local-storage.service';
import { ToasterService } from '../services/toaster.service';

@Injectable()
export class ErrorInterceptor implements HttpInterceptor {
  constructor(
    private router: Router,
    private toaster: ToasterService,
    private storage: LocalStorageService,
  ) {}

  intercept(req: HttpRequest<unknown>, next: HttpHandler): Observable<HttpEvent<unknown>> {
    return next.handle(req).pipe(
      catchError((err: HttpErrorResponse) => {
        const message = err.error?.message ?? 'Request failed.';

        if (err.status === 401) {
          this.storage.remove('marketplace_token');
          if (this.router.url !== '/auth/login' && this.router.url !== '/auth/register') {
            this.router.navigate(['/auth/login']);
          }
        } else if (err.status === 403) {
          this.toaster.error('You do not have permission to perform this action.');
        } else if (err.status === 422) {
          // Validation errors — let the calling component handle them for field-level display
        } else if (err.status >= 500) {
          this.toaster.error(message);
        }

        return throwError(() => err);
      }),
    );
  }
}
