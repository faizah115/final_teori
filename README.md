# 🏸 Reservasi Lapangan Bulutangkis

Aplikasi web fullstack untuk reservasi lapangan bulutangkis, dibangun dengan stack modern:

- **Backend**: Laravel 11
- **Frontend**: Inertia.js + Vue 3 (Composition API)
- **Styling**: Tailwind CSS + Vite
- **ORM**: Prisma (query layer) + Eloquent (auth)
- **Database**: Supabase (PostgreSQL)
- **Auth**: Laravel Breeze (session-based)

---

## 🚀 Cara Setup Project

### 1. Clone & Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 2. Environment

```bash
# Salin file environment
cp .env.example .env

# Generate Laravel app key
php artisan key:generate
```

Edit `.env` dan isi variabel Supabase:
```env
DATABASE_URL="postgresql://postgres.[PROJECT-REF]:[PASSWORD]@aws-0-[REGION].pooler.supabase.com:6543/postgres?pgbouncer=true&connection_limit=1"
DIRECT_URL="postgresql://postgres.[PROJECT-REF]:[PASSWORD]@aws-0-[REGION].pooler.supabase.com:5432/postgres"
SUPABASE_URL=https://[PROJECT-REF].supabase.co
SUPABASE_ANON_KEY=your-anon-key
SUPABASE_SERVICE_KEY=your-service-role-key
```

### 3. Setup Prisma

```bash
# Generate Prisma Client
npm run db:generate

# Push schema ke Supabase (development)
npm run db:push

# Atau gunakan migration (production)
npm run db:migrate
```

### 4. Seed Database

```bash
# Seed via Prisma (lapangan + users)
npm run db:seed

# Seed via Laravel (hanya users untuk auth)
php artisan db:seed
```

### 5. Jalankan Aplikasi

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server
npm run dev
```

Buka: **http://localhost:8000**

---

## 🔑 Kredensial Default (Setelah Seeding)

| Role  | Email                  | Password   |
|-------|------------------------|------------|
| Admin | admin@badminton.com    | `admin123` |
| User  | budi@example.com       | `user123`  |
| User  | siti@example.com       | `user123`  |

---

## 📁 Struktur Project

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Auth/              ← Breeze auth controllers
│   │   └── Middleware/
│   │       ├── Authenticate.php
│   │       └── HandleInertiaRequests.php
│   └── Models/
│       └── User.php               ← Eloquent User (auth layer)
├── prisma/
│   ├── schema.prisma              ← Schema: User, Lapangan, Reservasi
│   └── seed.js                    ← Node.js seeder
├── resources/
│   ├── css/
│   │   └── app.css                ← Tailwind + custom styles
│   ├── js/
│   │   ├── app.js                 ← Inertia + Vue 3 entry
│   │   ├── bootstrap.js           ← Axios config
│   │   ├── Pages/                 ← Vue page components (Inertia)
│   │   └── Components/            ← Shared Vue components
│   └── views/
│       └── app.blade.php          ← Root Inertia template
├── routes/
│   ├── web.php
│   └── auth.php
├── database/
│   ├── migrations/
│   │   └── create_users_table.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── .env.example
├── composer.json
├── package.json
├── tailwind.config.js
├── postcss.config.js
└── vite.config.js
```

---

## 🎨 Design System (Cream Palette)

| Token          | Hex       | Penggunaan                   |
|----------------|-----------|------------------------------|
| `cream-bg`     | `#FDF6EC` | Background utama             |
| `cream-card`   | `#FFFAF2` | Card / surface               |
| `cream-border` | `#E8D9C5` | Border / divider             |
| `cream-accent` | `#C8956C` | Aksen / tombol CTA           |
| `cream-text`   | `#3B2F2F` | Teks utama                   |
| `cream-muted`  | `#8B7355` | Teks sekunder / placeholder  |

---

## 🛠️ NPM Scripts

| Script           | Perintah                  |
|------------------|---------------------------|
| Dev server       | `npm run dev`             |
| Build production | `npm run build`           |
| Prisma generate  | `npm run db:generate`     |
| Prisma push      | `npm run db:push`         |
| Prisma migrate   | `npm run db:migrate`      |
| Prisma seed      | `npm run db:seed`         |
| Prisma studio    | `npm run db:studio`       |

---

## 📌 Catatan Arsitektur

> **Prisma vs Eloquent**
>
> Project ini menggunakan **dual ORM approach**:
> - **Eloquent** digunakan hanya untuk autentikasi (Laravel Breeze session-based auth)
> - **Prisma** digunakan sebagai query layer utama untuk model `Lapangan` dan `Reservasi`
>
> Ini memungkinkan kita memanfaatkan ekosistem auth Laravel (Breeze, Sanctum)
> sekaligus mendapat type-safety dan developer experience yang lebih baik dari Prisma.
