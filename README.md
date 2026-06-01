# Multi-Vendor Marketplace

A full-stack **Daraz/Amazon-style multi-vendor marketplace** where multiple vendors sell products through one storefront, customers buy from multiple vendors in a single checkout, and each order is **automatically split per vendor** so each seller manages only their own fulfillment while the customer sees one unified order.

---

## About This Project

This marketplace solves the core problems of multi-vendor e-commerce:

- **Order splitting** — a single customer checkout with items from 3 different vendors produces 1 parent order + 3 vendor sub-orders, each independently fulfillable
- **Stock race prevention** — `SELECT ... FOR UPDATE` row-level locking prevents two customers from buying the last unit simultaneously
- **Commission engine** — configurable platform commission (default 15%) calculated atomically during checkout, earnings released automatically when vendors mark orders as delivered
- **Role-based access** — 3 roles (customer, vendor, admin) with 13 granular permissions managed by Spatie, enforced at both API middleware and frontend route guard levels
- **Stripe + COD** — dual payment support with idempotent webhook handling (replaying the same Stripe event is a safe no-op)

---

## Tech Stack

| Layer | Technology |
|---|---|
| **Backend API** | Laravel 11, PHP 8.3, MySQL 8 |
| **Modular Architecture** | `nwidart/laravel-modules` — 8 self-contained modules |
| **Roles & Permissions** | `spatie/laravel-permission` — 3 roles, 13 permissions |
| **Authentication** | Laravel Sanctum (Bearer token, stateless) |
| **Payments** | Stripe Checkout + Cash on Delivery |
| **Image Processing** | `intervention/image` (resize + thumbnails) |
| **Frontend SPA** | Angular 14, TypeScript 4.7, RxJS 7.5 |
| **UI Components** | Angular Material 14 + hand-written SCSS design system |
| **State Management** | Plain Angular services with BehaviorSubject |

---

## Repository Structure

This repository contains two independent applications:

```
multi-vendor-marketplace/
├── marketplace/                 ← Laravel 11 Backend API
│   ├── Modules/
│   │   ├── Auth/                  Register, login, password reset, email verification
│   │   ├── Vendor/                Vendor profiles, admin approval flow
│   │   ├── Catalog/               Categories, products, images, search + filters
│   │   ├── Cart/                  Authenticated cart with price snapshots
│   │   ├── Order/                 Checkout with vendor splitting + stock locking
│   │   ├── Payment/               COD + Stripe Checkout + idempotent webhooks
│   │   ├── Earning/               Commission calculation + release on delivery
│   │   └── Admin/                 Dashboard stats + user management
│   ├── app/                       Shared base (User model, ApiResponse trait, DomainException)
│   ├── config/marketplace.php     Commission rate, currency, product image limits
│   └── README.md                  Detailed backend docs + full API reference
│
├── marketplace-frontend/        ← Angular 14 Frontend SPA
│   └── src/app/
│       ├── core/                  Services, guards, interceptors, models
│       ├── shared/                Reusable components (navbar, product-card, pagination, stat-card)
│       ├── layouts/               Public, vendor sidebar, admin sidebar
│       ├── modules/
│       │   ├── auth/              Login, register, forgot/reset password
│       │   ├── catalog/           Home with filters, product detail, my orders, vendor apply
│       │   ├── cart/              Cart + checkout (COD / Stripe)
│       │   ├── vendor/            Dashboard, products CRUD, orders, earnings
│       │   └── admin/             Dashboard, vendor approvals, all orders, users, categories
│       └── README.md              Detailed frontend docs + feature walkthrough
│
└── README.md                    ← You are here
```

---

## Features

### Customer

- Browse products with **search, category filter, price range, and sort** (newest, price asc/desc, name)
- View product detail with **image gallery**
- **Add to cart** (login required) — unit price is snapshot-frozen at add time
- **Single checkout** for items from multiple vendors — choose COD or Stripe
- View unified **order history** with per-vendor breakdown and fulfillment status per vendor
- Track order status: `pending → partially_shipped → shipped → delivered`
- **Apply to become a vendor** with store name and description

### Vendor

- Submit vendor application → **admin approves** → Spatie role swap to `vendor`
- **Product management** — create, edit, delete with status (draft/active/inactive)
- **Image upload** with automatic thumbnail generation (200x200) via Intervention Image
- **Order fulfillment** — view only their own sub-orders, update status (`confirmed → shipped → delivered`)
- **Status transitions enforced** — can't skip steps (e.g. can't go from pending to delivered)
- **Earnings dashboard** — gross, commission, net, pending vs released
- Earnings **automatically released** when vendor marks order as delivered (event-driven)

### Admin

- **Platform dashboard** — total users, approved/pending vendors, products, orders, gross sales
- **Vendor approval flow** — list pending/approved/rejected, approve or reject with reason
- **All orders view** — platform-wide order list
- **User management** — list all users with role badges
- **Category CRUD** — create/edit/delete with parent category support

---

## Architecture

### Backend — Strict Layered Pattern

Every request flows through the same pipeline, enforced across all 8 modules:

```
Route
  → FormRequest (validation + toDto())
    → Controller (thin, try-catch wrapped, calls Manager)
      → Manager (owns DB::transaction, calls Services only)
        → Service (single-domain business logic, calls Repositories)
          → Repository (the ONLY layer that touches Eloquent)
            → Model
```

**Key rules enforced (100% compliance across 235+ PHP files):**

| Rule | What it means |
|---|---|
| `declare(strict_types=1)` | Every PHP file |
| Typed everything | Every function has return type, every parameter is type-hinted |
| Controller = thin | Only try-catch + delegate to Manager + return Resource |
| Manager = transaction boundary | `DB::transaction(fn () => ...)` — **never injects Repositories** |
| Service = business logic | **Never touches Eloquent** — calls Repository methods only |
| Repository = DB layer | Only place that runs queries — exposes named methods, not raw arrays |
| Domain exceptions | Each module has typed exceptions extending `DomainException` with `httpStatus(): int` |
| DTOs | `final readonly` classes — immutable data transfer between layers |
| FormRequest | Every endpoint has a dedicated request class with `toDto()` |
| API Resources | Every response is shaped by a Resource class |

### Frontend — Angular 14 (NgModules)

| Pattern | Implementation |
|---|---|
| **Lazy loading** | 5 feature modules loaded on demand (~30-75KB each) |
| **HTTP layer** | `BackendApiService` wraps `HttpClient`, auto-unwraps `{ success, data }` envelope |
| **Auth** | `AuthInterceptor` attaches Bearer token, `ErrorInterceptor` handles 401/403/5xx |
| **Guards** | `AuthGuard`, `GuestGuard`, `RoleGuard` (reads `route.data.roles[]`) |
| **State** | `BehaviorSubject` in services (UserService, CartStateService) |
| **Forms** | Reactive forms with validation across every form |

---

## The Checkout Flow (the hardest part)

When a customer's cart has items from multiple vendors:

```
POST /api/v1/orders
  │
  ├─ PlaceOrderRequest validates + builds PlaceOrderDto
  │
  ├─ CheckoutManager wraps in DB::transaction
  │
  └─ CheckoutService::execute():
       │
       ├─ 1. Load cart items
       ├─ 2. SELECT products FOR UPDATE          ← row-level lock
       ├─ 3. Validate stock >= quantity           ← throws InsufficientStockException
       ├─ 4. Group cart items by vendor_id
       ├─ 5. Create 1 parent `orders` row
       ├─ 6. For each vendor group:
       │     ├─ Create `vendor_orders` (subtotal, commission, net)
       │     ├─ Create `order_items` (product name + price snapshots)
       │     ├─ Decrement product stock
       │     └─ Create `vendor_earnings` (status=pending)
       ├─ 7. Clear cart
       ├─ 8. Create payment (COD or Stripe Checkout)
       └─ 9. Return { order, stripe_checkout_url }
```

**Result:** 1 order, N vendor sub-orders, M line items, N earning records — all atomic. If any step fails, everything rolls back including stock decrements.

---

## Roles & Permissions

Managed by [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission):

| Role | Permissions | How assigned |
|---|---|---|
| `customer` | `view-products`, `place-order`, `view-own-orders`, `cancel-own-order` | Auto on registration |
| `vendor` | Customer permissions + `manage-products`, `view-own-vendor-orders`, `fulfill-own-orders`, `view-own-earnings` | Admin approves vendor application |
| `admin` | All 13 permissions | Seeded manually |

The `/api/v1/auth/me` endpoint returns `roles[]` and `permissions[]` arrays, enabling the Angular frontend to conditionally render UI elements without extra API calls.

---

## Quick Start

### Prerequisites

- PHP 8.3+ (with extensions: curl, mbstring, pdo_mysql, bcmath, gd, zip, intl)
- Composer 2+
- MySQL 8
- Node.js 18+ and npm 9+

### Backend Setup

```bash
cd marketplace

# Install dependencies
composer install

# Configure environment
cp .env.example .env
php artisan key:generate
# Edit .env: set DB_PASSWORD, STRIPE_KEY, STRIPE_SECRET, STRIPE_WEBHOOK_SECRET

# Create database
mysql -uroot -p -e "CREATE DATABASE marketplace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Migrate + seed (creates admin user + roles + permissions + categories)
php artisan migrate --seed

# Link storage for product images
php artisan storage:link

# Start server
php artisan serve
# → http://localhost:8000
```

### Frontend Setup

```bash
cd marketplace-frontend

# Install dependencies
npm install

# Start dev server
npm start
# → http://localhost:4200
```

### Seeded Account

| Role | Email | Password |
|---|---|---|
| Admin | `admin@marketplace.test` | `password` |

Customers and vendors are created through the registration + vendor application flow.

---

## API Overview

45+ endpoints under `/api/v1/*`. Full reference in [`marketplace/README.md`](marketplace/README.md).

### Public
`POST /auth/register` · `POST /auth/login` · `GET /products` · `GET /products/{slug}` · `GET /categories` · `POST /auth/forgot-password` · `POST /auth/reset-password`

### Customer (Bearer token)
`GET /auth/me` · `POST /auth/logout` · `GET /cart` · `POST /cart/items` · `PUT /cart/items/{id}` · `DELETE /cart/items/{id}` · `POST /orders` · `GET /orders` · `GET /orders/{id}` · `POST /vendor/applications`

### Vendor
`GET /vendor/profile` · `PUT /vendor/profile` · `GET /vendor/products` · `POST /vendor/products` · `PUT /vendor/products/{id}` · `DELETE /vendor/products/{id}` · `POST /vendor/products/{id}/images` · `GET /vendor/orders` · `PUT /vendor/orders/{id}/status` · `GET /vendor/earnings` · `GET /vendor/earnings/summary`

### Admin
`GET /admin/dashboard/stats` · `GET /admin/users` · `GET /admin/vendors` · `POST /admin/vendors/{id}/approve` · `POST /admin/vendors/{id}/reject` · `GET /admin/orders` · `GET /admin/categories` · `POST /admin/categories` · `PUT /admin/categories/{id}` · `DELETE /admin/categories/{id}`

### Webhook
`POST /webhooks/stripe` — signature-verified, idempotent (same event replayed = no-op)

---

## Module Breakdown (Backend)

Each module is self-contained under `Modules/{Name}/` with its own migrations, models, DTOs, exceptions, FormRequests, controllers, managers, services, repositories, resources, and routes.

| Module | Key Responsibility | Highlights |
|---|---|---|
| **Auth** | Registration, login, password reset, email verification | Assigns `customer` role on register, `/me` returns roles + permissions |
| **Vendor** | Vendor profile, admin approval | Role swap via Spatie on approval (`customer` → `customer + vendor`) |
| **Catalog** | Categories, products, images, search | Indexed search filters (name LIKE, category, price range, vendor), Intervention Image thumbnails |
| **Cart** | Authenticated cart | `unit_price_snapshot` frozen at add-time — price changes don't affect cart |
| **Order** | Checkout + vendor splitting + fulfillment | `SELECT ... FOR UPDATE` stock lock, `CheckoutService` (130 lines of atomic logic), `OrderStatusRollupService` |
| **Payment** | COD + Stripe Checkout + webhooks | Idempotent via `stripe_webhook_events` table, cross-module service calls to `OrderService` |
| **Earning** | Commission + earnings release | Event-driven: `VendorOrderDelivered` → `ReleaseEarningOnDelivery` listener |
| **Admin** | Dashboard stats + user management | Aggregated queries across all modules |

---

## Frontend Modules (Angular)

5 lazy-loaded feature modules:

| Module | Route | Pages |
|---|---|---|
| **Auth** | `/auth/*` | Login, register, forgot password, reset password |
| **Catalog** | `/`, `/products/*`, `/my/*` | Home with search + filters, product detail, my orders, order detail, vendor apply |
| **Cart** | `/cart/*` | Cart with quantity management, checkout with shipping form + COD/Stripe selection |
| **Vendor** | `/vendor/*` | Dashboard with stats, products CRUD + image upload, order fulfillment, earnings history |
| **Admin** | `/admin/*` | Platform dashboard, vendor approvals, all orders, users, categories CRUD |

---

## Configuration

### Commission Rate

Set in `.env`:
```
MARKETPLACE_COMMISSION_RATE=0.15    # 15% platform cut
```

Applied atomically during checkout in `CheckoutService`. Each `vendor_orders` row stores `subtotal`, `commission`, and `net` for full audit trail.

### Stripe

```
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

Test locally with Stripe CLI:
```bash
stripe listen --forward-to localhost:8000/api/v1/webhooks/stripe
```

Test card: `4242 4242 4242 4242`, any future date, any CVC.

---

## Stock Race Prevention

Two customers buying the last unit at the same time:

```sql
-- Inside DB::transaction in CheckoutService:
SELECT * FROM products WHERE id IN (1, 2, 3) FOR UPDATE;
-- Row-level lock acquired — second transaction waits here until first commits/rollbacks
-- First transaction decrements stock and commits
-- Second transaction reads the updated stock → throws InsufficientStockException
```

This is handled transparently by `ProductRepository::lockForUpdateByIds()` called from `CheckoutService::execute()`.

---

## Event-Driven Earnings

```
Vendor marks order "delivered"
  → VendorOrderService dispatches VendorOrderDelivered event
    → ReleaseEarningOnDelivery listener (Earning module)
      → EarningManager.releaseForVendorOrder()
        → EarningService flips status to "released" + sets released_at
```

The vendor dashboard earnings summary reflects this in real time.

---

## Numbers

| Metric | Count |
|---|---|
| Backend PHP files | 235+ |
| Backend modules | 8 |
| API endpoints | 45+ |
| Frontend TypeScript files | 60+ |
| Frontend lazy modules | 5 |
| Frontend API service methods | 40+ |
| Spatie roles | 3 |
| Spatie permissions | 13 |
| Hardcoded mock data | 0 |

---

## License

MIT
