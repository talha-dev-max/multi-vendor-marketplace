import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable, map } from 'rxjs';
import { environment } from '../../../environments/environment';
import { ApiResponse } from '../models/api-response.model';

/**
 * Central HTTP wrapper. All API calls go through here.
 * Automatically unwraps the { success, message, data, errors } envelope.
 */
@Injectable({ providedIn: 'root' })
export class BackendApiService {
  constructor(private http: HttpClient) {}

  get<T>(path: string, params?: Record<string, unknown>): Observable<T> {
    return this.http
      .get<ApiResponse<T>>(this.url(path), { params: this.toParams(params) })
      .pipe(map((r) => r.data as T));
  }

  post<T>(path: string, body: unknown = {}): Observable<T> {
    return this.http
      .post<ApiResponse<T>>(this.url(path), body)
      .pipe(map((r) => r.data as T));
  }

  put<T>(path: string, body: unknown = {}): Observable<T> {
    return this.http
      .put<ApiResponse<T>>(this.url(path), body)
      .pipe(map((r) => r.data as T));
  }

  delete<T>(path: string): Observable<T> {
    return this.http
      .delete<ApiResponse<T>>(this.url(path))
      .pipe(map((r) => r.data as T));
  }

  upload<T>(path: string, formData: FormData): Observable<T> {
    return this.http
      .post<ApiResponse<T>>(this.url(path), formData)
      .pipe(map((r) => r.data as T));
  }

  private url(path: string): string {
    return environment.apiUrl + (path.startsWith('/') ? path : '/' + path);
  }

  private toParams(params?: Record<string, unknown>): HttpParams {
    let hp = new HttpParams();
    if (!params) {
      return hp;
    }
    for (const [key, value] of Object.entries(params)) {
      if (value !== null && value !== undefined && value !== '') {
        hp = hp.set(key, String(value));
      }
    }
    return hp;
  }
}
