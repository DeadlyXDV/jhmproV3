# JHMPro Project Instructions (GEMINI.md)

This document serves as the primary instructional context for Gemini CLI and other AI agents working on the JHMPro codebase. It outlines the project architecture, tech stack, development conventions, and operational boundaries.

## 1. Project Overview
**JHMPro** is a comprehensive workshop management system for a single modified motorcycle workshop. It handles bookings, work orders, invoices, inventory, point-of-sale (POS), and customer segmentation (RFM).

### Tech Stack
- **PHP:** 8.4+
- **Laravel:** v13 (Bleeding edge)
- **Livewire:** v4 (SFC/MFC components)
- **UI Components:** [Flux UI](https://fluxui.dev/) v2
- **CSS:** Tailwind CSS v4
- **Auth:** Laravel Fortify (Guard: `web`) & Custom Admin Auth (Guard: `admin`)
- **Testing:** Pest PHP v4
- **Linting:** Laravel Pint v1
- **Static Analysis:** Larastan (PHPStan) v3
- **Bundler:** Vite 8

## 2. AI Operational Boundaries
The project follows a strict division of labor between AI providers to avoid conflicts:

| AI Agent | Ownership Scope | DO NOT TOUCH |
| :--- | :--- | :--- |
| **Claude** | **Backend:** Models, Controllers, Livewire Classes (`.php`), Migrations, Routes, Tests, Auth logic. | Pure Blade/view files, design system, raw CSS/JS assets. |
| **Gemini** | **Frontend:** Blade files (`.blade.php`), Tailwind classes, UI components, Layouts, Design system. | PHP logic files (Models, Controllers, Migrations), Routes, Tests. |

*Note: As Gemini CLI, your primary focus is the **Frontend** and **UI consistency**, unless explicitly instructed to perform full-stack tasks.*

## 3. Building and Running
The project uses `composer` scripts to manage most development tasks.

### Core Commands
- **Complete Setup:** `composer run setup`
- **Development Server:** `composer run dev` (Runs server, queue, logs, and Vite concurrently)
- **Linting:** `composer run lint` (Pint)
- **Type Checking:** `composer run types:check` (PHPStan)
- **Run Tests:** `composer run test` (Clears config, lints, checks types, and runs Pest)
- **Browser Testing:** `npm run test:browser` (Playwright)

### UI Verification Workflow
Setelah melakukan perubahan pada UI/Frontend, Gemini **wajib** melakukan verifikasi menggunakan Playwright:
1. Buat atau update test di `tests/Browser/` untuk mencakup perubahan baru.
2. Jalankan `npm run test:browser` untuk memastikan fungsionalitas dan visual tidak broken.
3. Gunakan screenshot Playwright jika diperlukan untuk membandingkan presisi visual.
- **Frontend Build:** `npm run build`

## 4. Design System & Frontend Conventions
Consistency in the UI is paramount. Adhere to these rules:

- **Primary Color:** `#DC2626` (Tailwind `red-600`)
- **Sidebar Background:** `#111827` (Tailwind `gray-900`)
- **Sidebar Active Item:** `bg-red-600 text-white`
- **Page Background:** `#F3F4F6` (Tailwind `gray-100`)
- **Typography:** Inter font family.
- **Icons:** Use **Heroicons** via `blade-ui-kit/blade-heroicons` (e.g., `<x-heroicon-o-user />`).
- **NO EMOJIS:** Do not use emojis for icons or status indicators. Use CSS-based indicators (e.g., `<span class="bg-green-500"></span>`).
- **Tailwind v4:** Use the latest Tailwind v4 syntax and utilities.

## 5. Architectural Conventions
- **Roles:** The system uses `super_admin`, `admin`, `mekanik`, and `customer`.
- **Middleware:** Role-based access is managed via the `role` middleware (e.g., `middleware(['auth:admin', 'role:super_admin,admin'])`).
- **Dates:** `CarbonImmutable` is used by default application-wide.
- **Livewire:** Prefer Flux UI components (`<flux:button>`, `<flux:input>`, etc.) over custom HTML where possible.
- **NO Filament:** The admin panel is completely custom-built using Blade, Livewire, and Tailwind. Do not attempt to use Filament features.

## 6. Testing & Validation
- **Pest:** All tests are written using Pest PHP.
- **Feature Tests:** Use the `RefreshDatabase` trait and extend `Tests\TestCase`.
- **Validation:** Always run `composer run lint` and `composer run types:check` after changes to ensure codebase health.

## 7. Key Directories
- `app/Livewire/Admin/`: Admin-facing components.
- `app/Livewire/Mekanik/`: Mechanic-facing components.
- `app/Models/`: All Eloquent models.
- `resources/views/layouts/`: Base layouts (`admin.blade.php`, `mekanik.blade.php`, `app.blade.php`).
- `resources/views/flux/`: Custom or published Flux UI components.
- `routes/web.php`: Primary route definitions.

## 8. Wajib Dilakukan Sebelum Apapun
- **Baca MEMORY.md sampai selesai** sebelum mengerjakan apapun.
- Pahami section 0 (AI Task Ownership).
- Pahami section 4 (Design System).
- Pahami section 10 (Interface Contracts) sebelum menyentuh view Livewire.
- Cek section `CURRENT SESSION` untuk konteks sesi sebelumnya.
- **Konfirmasi dengan merangkum:** scope, design system, dan status `CURRENT SESSION`.

## 9. Scope Kamu (Frontend Only)
| BOLEH | DILARANG |
|---|---|
| `resources/views/` | `app/*.php` |
| `resources/css/` | `database/migrations/` |
| `resources/js/` | `routes/` |
| Alpine.js di atribut `x-*` | `tests/` |
| | `composer.json`, `composer.lock`, `.env` |

- Jika task mengharuskan perubahan di luar scope, **HENTIKAN** dan informasikan ke user.

## 10. Konvensi Wajib
- **Ikon:** gunakan Heroicons via `<x-heroicon-o-*>` — **DILARANG** emoji.
- **Warna:** brand primary `#DC2626`, sidebar `bg-[#111827]`, nav aktif `bg-red-600`, page bg `#F3F4F6`, card `#FFFFFF`, font Inter — jangan hardcode warna baru.
- **Livewire:** jangan ubah nama properti `$wire`, wire action, atau event — selalu cek Interface Contracts di `MEMORY.md` section 10.
- Jika interface contract komponen belum ada di `MEMORY.md`, **minta Claude membuatnya dulu**.

## 11. Wajib Dilakukan Setelah Selesai
- **Update section CURRENT SESSION di MEMORY.md** setelah setiap task selesai.
- **Isi:** sedang dikerjakan, file yang dimodifikasi, berhenti di, AI sebelumnya: Gemini.
- Jika menemukan bug di luar scope frontend, catat di section `Known Issues` `MEMORY.md` — **jangan fix sendiri**.
