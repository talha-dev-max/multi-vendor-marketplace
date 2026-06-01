# Multi-Vendor Marketplace — Backend API

A **Daraz/Amazon-style multi-vendor marketplace** backend built with Laravel 11, a modular monolith architecture using `nwidart/laravel-modules`, and Spatie role/permission management. Multiple vendors sell products through one storefront, customers buy from multiple vendors in a single checkout, and each order is **split per vendor** so each seller manages only their own fulfillment.

This is the **backend API** only. The Angular 14 frontend lives in the sibling [`marketplace-frontend/`](../marketplace-frontend) directory.

---

## Tech Stack

- **Laravel 11** (API-only, no blade views)
- **PHP 8.3+**
- **MySQL 8**
- **`nwidart/laravel-modules`** — 8 self-contained modules
- **`spatie/laravel-permission`** — roles + permissions
- **Laravel Sanctum** — Bearer token auth (stateless)
- **`stripe/stripe-php`** — Stripe Checkout + webhooks
- **`intervention/image`** — product image resize/thumbnail
- **`barryvdh/laravel-dompdf`** — PDF generation

---

## Architecture

Strict layered pattern enforced in every module:

```
Route
  → FormRequest (validation + toDto())
    → Controller (thin, try-catch wrapped)
      → Manager (owns DB::transaction)
        → Service (single-domain business logic)
          → Repository (the ONLY layer that queries the DB)
            → Model (Eloquent)
```

### Layer rules (enforced)

| Layer | Responsibility | Rules |
|---|---|---|
| **FormRequest** | Validate + authorize. Build a DTO via `toDto()` | No business logic, no DB access |
| **Controller** | HTTP glue. Call manager, return `ApiResource` or JSON. | Every method wrapped in `try { ... } catch (Throwable $e) { Log::error; return $this->error(...); }` |
| **Manager** | Coordinate services. Own DB transactions. | **Never injects Repositories directly.** Only Services. |
| **Service** | Single-domain business logic | **Never touches Eloquent.** Accepts DTOs/typed args. Calls Repositories. |
| **Repository** | The only layer that queries the DB | Exposes named methods (`createFromRegisterDto`, `attachStripeCustomerId`, `markCanceled`, `decrementStock`), **not raw arrays from callers** |
| **Model** | Relationships, casts, scopes | No business logic |

### Code standards enforced (100%)

- `declare(strict_types=1);` in every PHP file
- Every function has an explicit return type
- Every parameter is type-hinted
- Custom domain exceptions (`DomainException` base) with `httpStatus(): int`
- Standardized JSON response envelope: `{ success, message, data, errors }`
- No column arrays constructed in services — repositories expose domain-meaningful methods

---

## Modules

Each module under `Modules/{Name}/` is self-contained with its own migrations, models, controllers, managers, services, repositories, DTOs, FormRequests, routes, and service provider.

1. **Auth** — register, login, logout, /me, email verification, password reset
2. **Vendor** — vendor profile, admin approval flow (role swap to `vendor` via Spatie)
3. **Catalog** — categories, products (vendor-scoped CRUD), product images with thumbnails, public search with filters
4. **Cart** — authenticated cart, unit_price snapshot on add
5. **Order** — **CheckoutService with atomic transaction** + `SELECT ... FOR UPDATE` stock lock, vendor splitting, order status rollup, vendor fulfillment flow
6. **Payment** — COD + Stripe Checkout, **idempotent webhooks** (stripe_webhook_events table)
7. **Earning** — vendor earnings ledger, commission calculation, release-on-delivery event listener
8. **Admin** — dashboard stats, users management

---

## Roles & Permissions (Spatie)

### Roles
- `customer` — assigned on registration
- `vendor` — assigned by admin on approval (customer role NOT removed — user can still shop)
- `admin` — seeded manually

### Permissions (13)
```
Catalog:  view-products, manage-products, manage-categories
Order:    place-order, view-own-orders, cancel-own-order
Vendor:   view-own-vendor-orders, fulfill-own-orders, view-own-earnings
Admin:    approve-vendors, view-all-orders, manage-users, view-stats
```

### Frontend integration
The `/api/v1/auth/me` endpoint returns `roles[]` and `permissions[]` arrays, letting the SPA do conditional UI rendering without extra API calls.

---

## Prerequisites

- PHP 8.3+ with: `curl`, `mbstring`, `pdo_mysql`, `bcmath`, `gd`, `zip`, `intl`, `dom`, `fileinfo`, `openssl`, `tokenizer`, `xml`
- Composer 2+
- MySQL 8

---

## Setup

```bash
# 1. Install PHP dependencies
composer install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Configure .env (MySQL credentials, Stripe keys)
# Set DB_PASSWORD, STRIPE_KEY, STRIPE_SECRET, STRIPE_WEBHOOK_SECRET

# 4. Create database
mysql -uroot -p -e "CREATE DATABASE marketplace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 5. Migrate + seed (admin user + permissions + default categories)
php artisan migrate --seed

# 6. Public storage link (for product images)
php artisan storage:link

# 7. Run the server
php artisan serve
# → http://localhost:8000
```

### Seeded accounts

| Role | Email | Password |
|---|---|---|
| Admin | `admin@marketplace.test` | `password` |

Customers and vendors are created through the register / vendor-apply flow.

---

## API Reference

All endpoints are under `/api/v1/*`. Response envelope:

```json
{ "success": true|false, "message": "...", "data": ..., "errors": ... }
```

### Public

| Method | Path | Description |
|---|---|---|
| POST | `/auth/register` | Create account (assigns `customer` role) |
| POST | `/auth/login` | Obtain Sanctum token |
| POST | `/auth/forgot-password` | Send password reset link |
| POST | `/auth/reset-password` | Reset password with token |
| GET | `/auth/verify-email/{id}/{hash}` | Verify email (signed URL) |
| GET | `/products` | List active products (search, filter, paginate) |
| GET | `/products/{slug}` | Product detail |
| GET | `/categories` | List active categories |
| POST | `/webhooks/stripe` | Stripe webhook receiver |

### Authenticated (`Authorization: Bearer <token>`)

| Method | Path | Description |
|---|---|---|
| POST | `/auth/logout` | Revoke current token |
| GET | `/auth/me` | Current user + roles + permissions |
| POST | `/auth/email/resend` | Resend verification email |
| GET | `/cart` | Get current cart |
| POST | `/cart/items` | Add to cart |
| PUT | `/cart/items/{id}` | Update quantity |
| DELETE | `/cart/items/{id}` | Remove item |
| DELETE | `/cart` | Clear cart |
| POST | `/orders` | Place order (COD or Stripe) |
| GET | `/orders` | My orders |
| GET | `/orders/{id}` | Order detail |
| POST | `/vendor/applications` | Apply to become a vendor |

### Vendor (`role:vendor`)

| Method | Path | Description |
|---|---|---|
| GET | `/vendor/profile` | Vendor profile |
| PUT | `/vendor/profile` | Update profile |
| GET | `/vendor/products` | My products |
| POST | `/vendor/products` | Create product |
| PUT | `/vendor/products/{id}` | Update product |
| DELETE | `/vendor/products/{id}` | Delete product |
| POST | `/vendor/products/{id}/images` | Upload product image |
| DELETE | `/vendor/products/{id}/images/{imageId}` | Delete image |
| GET | `/vendor/orders` | Vendor sub-orders |
| PUT | `/vendor/orders/{id}/status` | Update status (confirmed → shipped → delivered) |
| GET | `/vendor/earnings` | Earnings history |
| GET | `/vendor/earnings/summary` | Aggregated earnings |

### Admin (`role:admin`)

| Method | Path | Description |
|---|---|---|
| GET | `/admin/dashboard/stats` | Platform stats |
| GET | `/admin/users` | All users |
| GET | `/admin/vendors` | Vendor applications (filter by status) |
| POST | `/admin/vendors/{id}/approve` | Approve vendor |
| POST | `/admin/vendors/{id}/reject` | Reject vendor |
| GET | `/admin/orders` | All orders |
| GET | `/admin/orders/{id}` | Order detail |
| GET | `/admin/categories` | All categories |
| POST | `/admin/categories` | Create category |
| PUT | `/admin/categories/{id}` | Update category |
| DELETE | `/admin/categories/{id}` | Delete category |

---

## Core Flows

### Checkout (the critical one)

```
POST /api/v1/orders { payment_method, shipping_address }
  ↓
OrderController::place (try-catch)
  ↓
CheckoutManager::placeOrder (DB::transaction)
  ↓
CheckoutService::execute:
  1. Load cart with items + products
  2. SELECT products WHERE id IN (...) FOR UPDATE  ← row-level lock
  3. Validate stock availability (throw InsufficientStockException on failure)
  4. Group cart items by vendor_id
  5. Create parent `orders` row
  6. For each vendor group:
     a. vendor_orders row (subtotal, commission, net, status=pending)
     b. order_items rows (product name + unit_price snapshots)
     c. products.stock decrement (still under the row lock)
     d. vendor_earnings row (status=pending)
  7. Clear the cart
  8. If payment=stripe: create Stripe Checkout Session → return URL
     If payment=cod: payment row stays pending
  9. Return { order, stripe_checkout_url }
```

### Vendor fulfillment

Vendor marks their `vendor_order` as `confirmed → shipped → delivered`. Status transitions are enforced by `VendorOrderService::TRANSITIONS`. When `delivered`:
- Parent order's `status` is re-computed by `OrderStatusRollupService`
- `VendorOrderDelivered` event fires
- `ReleaseEarningOnDelivery` listener flips the matching `vendor_earnings` row to `released`

### Stripe webhook (idempotent)

```
POST /api/v1/webhooks/stripe
  ↓
StripeWebhookController::handle (try-catch)
  ↓
WebhookManager::handleStripeWebhook (DB::transaction)
  ↓
StripeService::verifyWebhook (signature verification)
  ↓
WebhookDispatchService::process:
  - Check stripe_webhook_events by event.id (idempotency)
  - Insert event record
  - Dispatch by type (checkout.session.completed, payment_intent.succeeded, payment_intent.payment_failed)
  - Mark record processed
```

Replaying the same Stripe event is a no-op.

### Stock race prevention

Two customers buying the last unit simultaneously → `SELECT ... FOR UPDATE` inside the checkout transaction serializes them. One succeeds, the other gets `InsufficientStockException`.

---

## Directory Structure

```
marketplace/
├── app/
│   ├── Http/
│   │   ├── Controllers/Controller.php  (base)
│   │   └── Traits/ApiResponse.php
│   ├── Exceptions/Domain/DomainException.php
│   ├── Models/User.php  (HasApiTokens + HasRoles)
│   └── Providers/AppServiceProvider.php
├── Modules/
│   ├── Auth/
│   ├── Vendor/
│   ├── Catalog/
│   ├── Cart/
│   ├── Order/
│   ├── Payment/
│   ├── Earning/
│   └── Admin/
├── config/
│   ├── marketplace.php  (commission_rate, currency, product limits)
│   ├── modules.php       (nwidart)
│   └── permission.php    (spatie)
├── database/
│   ├── migrations/       (shared: users, tokens, permissions)
│   └── seeders/          (PermissionSeeder, AdminUserSeeder)
├── routes/api.php        (auto-loads module routes)
├── storage/app/public/products/  (product images)
└── tests/
```

Each module follows:
```
Modules/{Name}/
├── app/
│   ├── DTOs/              (final readonly)
│   ├── Exceptions/         (domain exceptions)
│   ├── Http/
│   │   ├── Controllers/    (thin, try-catch)
│   │   ├── Requests/       (with toDto())
│   │   └── Resources/      (API response shapes)
│   ├── Managers/           (DB::transaction, orchestration)
│   ├── Services/           (business logic, calls repositories)
│   ├── Repositories/
│   │   ├── Contracts/      (interfaces)
│   │   └── *Repository.php (the only layer touching Eloquent)
│   ├── Models/
│   └── Providers/
│       ├── *ServiceProvider.php  (binds interfaces)
│       └── RouteServiceProvider.php
├── database/migrations/
└── routes/api.php
```

---

## Testing Stripe Webhooks Locally

```bash
# Install Stripe CLI: https://stripe.com/docs/stripe-cli
stripe login
stripe listen --forward-to localhost:8000/api/v1/webhooks/stripe

# Copy the whsec_... from the CLI output into .env STRIPE_WEBHOOK_SECRET
```

Use test card `4242 4242 4242 4242` with any future expiry + any CVC.

---

## Configuration

### `config/marketplace.php`

```php
'commission_rate' => 0.15,                 // 15% platform cut
'currency' => 'usd',
'products' => [
    'max_images_per_product' => 6,
    'image_thumb_width' => 200,
    'image_thumb_height' => 200,
    'image_medium_width' => 600,
    'image_medium_height' => 600,
],
```

### Rate limiting

- Auth endpoints: `throttle:20,1` (20/min)
- Public product search: `throttle:60,1`
- Stripe webhook: `throttle:60,1`
- Default: Laravel's `api` group (~60/min)

---

## License

MIT
