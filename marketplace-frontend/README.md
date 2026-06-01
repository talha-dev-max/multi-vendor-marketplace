# Multi-Vendor Marketplace — Angular Frontend

Angular 14 SPA for the Multi-Vendor Marketplace. A Daraz/Amazon-style storefront where customers browse products from multiple vendors in one unified experience, vendors manage their own products/orders/earnings, and admins oversee the platform.

This is the **frontend only**. The Laravel 11 backend API lives in the sibling [`marketplace/`](../marketplace) directory.

---

## Tech Stack

- **Angular 14.2** (NgModules-based, matching pro-suit conventions)
- **TypeScript 4.7**
- **RxJS 7.5**
- **Angular Material 14** (snackbar for toasts)
- **SCSS** for styling (no utility framework — hand-written design system)
- **Reactive forms**
- **Lazy-loaded feature modules**

---

## Architecture

### Folder layout

```
src/app/
├── core/                            # singletons
│   ├── services/
│   │   ├── backend-api.service.ts   # HTTP wrapper (unwraps { success, data } envelope)
│   │   ├── user.service.ts          # auth state, role/permission helpers, BehaviorSubject
│   │   ├── local-storage.service.ts
│   │   └── toaster.service.ts       # Angular Material snackbar
│   ├── guards/
│   │   ├── auth.guard.ts            # redirects to /auth/login
│   │   ├── guest.guard.ts           # blocks authed users from /auth/*
│   │   └── role.guard.ts            # reads route.data.roles[]
│   ├── interceptors/
│   │   ├── auth.interceptor.ts      # attaches Bearer token to every request
│   │   └── error.interceptor.ts     # 401 → logout, 403 → toast, 5xx → toast
│   └── models/                      # typed interfaces for every API entity
├── shared/
│   ├── components/
│   │   ├── navbar/                  # responsive navbar with cart badge
│   │   ├── footer/
│   │   ├── product-card/
│   │   ├── pagination/
│   │   └── stat-card/
│   └── shared.module.ts             # re-exports CommonModule, Forms, Router + shared components
├── layouts/
│   ├── public-layout/               # navbar + footer for catalog/cart
│   ├── vendor-layout/               # sidebar for vendor portal
│   └── admin-layout/                # sidebar for admin portal
├── modules/                         # lazy-loaded feature modules
│   ├── auth/                        # login, register, forgot-password, reset-password
│   ├── catalog/                     # home (with filters), product-detail, my-orders, order-detail, vendor-apply
│   ├── cart/                        # cart, checkout (COD + Stripe)
│   ├── vendor/                      # dashboard, products CRUD, orders, earnings
│   └── admin/                       # dashboard, vendor-approvals, orders, users, categories
├── app.module.ts                    # root — registers interceptors, shared, layouts
├── app-routing.module.ts            # top-level routing with lazy loadChildren + guards
└── styles/                          # global SCSS partials (variables, buttons, forms, tables, cards, layout, utilities)
```

### Routing

| Route | Module | Guards | Layout |
|---|---|---|---|
| `/auth/*` | AuthModule | GuestGuard | guest card (no chrome) |
| `/` and `/products/*` | CatalogModule | none | PublicLayout |
| `/cart`, `/cart/checkout` | CartModule | AuthGuard | PublicLayout |
| `/my/orders`, `/my/orders/:id` | CatalogModule | AuthGuard | PublicLayout |
| `/vendor-apply` | CatalogModule | AuthGuard | PublicLayout |
| `/vendor/*` | VendorModule | AuthGuard + RoleGuard(roles=['vendor']) | VendorLayout |
| `/admin/*` | AdminModule | AuthGuard + RoleGuard(roles=['admin']) | AdminLayout |

### State management

Plain Angular services with `BehaviorSubject` (no NgRx — matching pro-suit):
- `UserService.user$` — current user + roles + permissions
- `CartStateService.cart$` — current cart for navbar badge

### HTTP layer

All API calls go through `BackendApiService` which:
- Wraps `HttpClient` with `get/post/put/delete/upload` methods
- Automatically unwraps the `{ success, message, data, errors }` envelope, returning just `data`
- Takes a typed params object, serialized to `HttpParams` safely
- Base URL from `environment.apiUrl`

Interceptors:
- **AuthInterceptor** — reads token from `LocalStorageService` and attaches `Authorization: Bearer <token>`
- **ErrorInterceptor** — intercepts 401 (logout + redirect), 403 (toast), 5xx (toast)

---

## Prerequisites

- Node.js 18+ (22 recommended)
- npm 9+

---

## Setup

```bash
# 1. Install dependencies
npm install

# 2. Ensure backend is running at http://localhost:8000
#    See ../marketplace/README.md

# 3. Start Angular dev server
npm start
# → http://localhost:4200
```

### Environment

`src/environments/environment.ts`:
```typescript
{
  production: false,
  apiUrl: 'http://localhost:8000/api/v1',
  storageUrl: 'http://localhost:8000/storage',
  appName: 'Multi-Vendor Marketplace',
}
```

For production, use `environment.prod.ts` with relative paths (served from same origin as API).

---

## Build

```bash
# Development build
npm run build -- --configuration=development

# Production build (replaces environment.ts with environment.prod.ts)
npm run build
```

---

## Feature Walkthrough

### Customer flow
1. Browse `/` (home) → filter by category, price range, search by keyword, sort
2. Click a product → `/products/:slug` → image gallery, description, add to cart
3. Add to cart (blocked if not logged in → redirect to login)
4. Navbar shows cart badge count updating in real time
5. `/cart` → update quantities, remove items, see total
6. `/cart/checkout` → fill shipping address, choose COD or Stripe
7. Submit → creates order with vendor splitting → redirects to `/my/orders/:id`
8. If Stripe: redirects to Stripe Checkout, returns to `/my/orders?checkout=success`
9. `/my/orders` → paginated order history with per-vendor breakdown

### Vendor flow
1. Register as customer, then `/vendor-apply` → submit store application
2. Wait for admin approval (role swap happens in backend)
3. Log out + log in to refresh role + permissions in `/auth/me`
4. `/vendor/dashboard` → stats cards (products, orders, pending/released earnings)
5. `/vendor/products` → list + create/edit/delete + image upload
6. `/vendor/orders` → fulfill sub-orders (pending → confirmed → shipped → delivered)
7. `/vendor/earnings` → history + aggregated summary

### Admin flow
1. Log in as seeded admin (`admin@marketplace.test` / `password`)
2. `/admin/dashboard` → platform stats (users, vendors, products, orders, gross sales)
3. `/admin/vendor-approvals` → filter by pending/approved/rejected, approve/reject
4. `/admin/orders` → all platform orders
5. `/admin/users` → all users with role badges
6. `/admin/categories` → CRUD with modal form

---

## Styling

Hand-written SCSS design system in `src/styles/`:
- `_variables.scss` — colors, spacing, typography, breakpoints
- `_mixins.scss` — card, button-base, container
- `_base.scss` — reset, typography defaults
- `_buttons.scss` — `.btn`, `.btn-primary`, `.btn-secondary`, `.btn-danger`, `.btn-ghost`, sizes
- `_forms.scss` — `.form-group`, `.form-row`, focus rings, errors
- `_cards.scss` — `.card`, `.stat-card`, `.product-card`
- `_tables.scss` — `.data-table`, status badges
- `_layout.scss` — layouts (guest, sidebar, public nav+footer)
- `_modal.scss` — modal overlay + toast variants
- `_utilities.scss` — spacing, flexbox, grid, alerts, responsive classes

**Color palette:** orange primary (`#ff6b00`), navy secondary (`#1a3a6c`), with success/danger/warning/info accents.

---

## Built-in Features

- ✅ Reactive forms with validation across every form
- ✅ Lazy-loaded feature modules (each ~30-75KB gzipped)
- ✅ Route guards for auth + role-based access
- ✅ HTTP interceptors for token attachment + error handling
- ✅ Standardized API response unwrapping (no manual `.data` access in components)
- ✅ Real-time cart badge via BehaviorSubject
- ✅ Responsive grid layouts (4 → 2 → 1 columns)
- ✅ Status badges for all statuses (order, vendor order, payment, vendor application, earning)
- ✅ Pagination component used across all tables
- ✅ Stripe Checkout redirect flow
- ✅ Image gallery on product detail
- ✅ Image upload on vendor product form
- ✅ Modal with reactive form for category CRUD

---

## Directory Structure Summary

- **60 TypeScript files** (components, services, guards, interceptors, models, modules)
- **5 lazy-loaded feature modules**: auth, catalog, cart, vendor, admin
- **40+ API service methods**, all matched to real backend routes
- **Zero hardcoded data** — every view loads from the API
- **Zero TODOs / empty ngOnInit** — fully implemented

---

## License

MIT
