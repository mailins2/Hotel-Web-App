# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

**Peach Valley** — a hotel management web app built with Laravel 13 / PHP 8.4. The app is named `Peach Valley` in `APP_NAME` and all UI copy.

## Commands

```bash
# First-time setup (install deps, generate key, migrate, build assets)
composer run setup

# Development (PHP server + queue + Pail log viewer + Vite, all concurrently)
composer run dev

# Run tests
composer run test
# or
php artisan test
# Run a single test file
php artisan test tests/Feature/ExampleTest.php

# Database
php artisan migrate
php artisan migrate:fresh   # drop and re-run all migrations

# Code style (Laravel Pint)
./vendor/bin/pint
```

Default database is SQLite (`database/database.sqlite`). To use MySQL, set `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` in `.env`.

ZaloPay payment requires: `ZALOPAY_APP_ID`, `ZALOPAY_KEY1`, `ZALOPAY_KEY2`, `ZALOPAY_ENDPOINT`.

## Architecture

### Two-layer design

The app has a clear split:

1. **REST API layer** — `routes/api.php` → `app/Http/Controllers/Api/` — all business data endpoints, returns JSON.
2. **UI layer** — `routes/web.php` → Blade views — currently many views render **static/hardcoded data**. The API controllers are built but the frontend JavaScript integration is incomplete. New work should wire the existing API endpoints to the views.

### Route groups (web)

| Prefix | Audience | Layout |
|---|---|---|
| `/customer/*` | Hotel guests (public) | Customer theme |
| `/hotel/*` | Admin/manager | `<x-app-layout>` dashboard |
| `/reception/*` | Receptionist | `<x-app-layout>` dashboard |
| `/admin/*` | Manager | `<x-app-layout>` dashboard |
| `/login`, `/sign-up` | Auth | `<x-guest-layout>` |

Root `/` redirects → `/dashboard` → `/customer`.

### Hotel management module pattern

`/hotel/{moduleKey}` maps to a **single Blade template** (`resources/views/hotel-management/index.blade.php`) that uses `@switch($moduleKey)` to render different table/filter content per module. Valid keys: `accounts`, `customers`, `employees`, `room-types`, `rooms`, `services`, `promotions`, `invoices`, `payments`, `reviews`. Read-only modules (no create/edit): `invoices`, `payments`, `reviews`.

### Authentication

Custom session-based auth in `AuthController` — no Laravel Breeze or Sanctum for web. Session key is `auth_account`:
```php
session('auth_account') // keys: MaTK, Email, LoaiTaiKhoan, MaKH, MaNV, Ten
```
Role routing on login: `LoaiTaiKhoan` 0 → `customer.home`, 1 → `reception.dashboard`, 2 → `admin.dashboard`.

Registration is a two-step session flow: step 1 saves email/password → redirects to `/register/details` → step 2 creates `TaiKhoan` + `KhachHang` in a single DB transaction (via `DB::beginTransaction`).

### Database naming convention

All tables and columns use **Vietnamese PascalCase**. Every model must explicitly set `$table` and `$primaryKey`. Most domain models set `public $timestamps = false`.

```php
protected $table = 'KhachHang';
protected $primaryKey = 'MaKH';
public $timestamps = false;
```

Key integer enums:
- `TaiKhoan.LoaiTaiKhoan`: 0=customer, 1=receptionist, 2=manager
- `TaiKhoan.TrangThai`: 1=active
- `KhachHang.GioiTinh`: 0=female, 1=male, 2=other
- `BangGia.Mua`: 0=off-peak, 1=peak season
- `Phong.TinhTrang`: 0=empty, 1=booked, ...
- `DatPhong.TinhTrang`: 0=booked, 1=in-use, ...
- `HoaDon.TrangThai`: 0=unpaid, 1=paid
- `ThanhToan.PhuongThuc`: 1=card, 2=QRCode
- `ThanhToan.LoaiThanhToan`: 0=deposit, 1=checkout payment

### API route naming

API resource routes use Vietnamese kebab-case: `dat-phong`, `loai-phong`, `khach-hang`, `hoa-don`, `khuyen-mai`, `kho-khuyen-mai`, `dich-vu`, `su-dung-dich-vu`, `tien-nghi`, `nhan-vien`, `den-bu`, `danh-gia`, `hinh-anh`.

### Blade layouts

- `<x-app-layout>` → renders `layouts/dashboard/dashboard.blade.php` → includes sidebar, header, footer partials from `resources/views/partials/dashboard/`.
- `<x-guest-layout>` → `layouts/dashboard/guest.blade.php` — minimal wrapper for auth pages.
- The `assets` prop on `<x-app-layout>` controls conditional JS/CSS loading.

### ZaloPay integration

`app/Http/Controllers/Api/ZaloPay/PaymentController.php` handles payment creation and callback verification. The callback at `POST /api/zalopay-callback` verifies HMAC-SHA256 with `ZALOPAY_KEY2`. The DB update on successful payment is a TODO stub.

### Address data

`AuthController::getCachedAddressData()` fetches Vietnamese provinces and communes from `production.cas.so/address-kit/2025-07-01/` using `Http::pool` for concurrent requests. Results are cached in the database for 30 days under key `address-kit.2025-07-01.all`.
