<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * DatabaseSeeder — Reservasi Lapangan Bulutangkis
 *
 * Seeder ini mengisi tabel 'users' via Laravel (untuk auth session).
 * Data lapangan & reservasi di-seed via Prisma: jalankan `npm run db:seed`
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ── Bersihkan tabel users (hati-hati di production!) ──────────────
        DB::table('users')->truncate();

        // ── 1 Admin ───────────────────────────────────────────────────────
        DB::table('users')->insert([
            'name'       => 'Administrator',
            'email'      => 'admin@badminton.com',
            'password'   => Hash::make('admin123'),
            'role'       => 'ADMIN',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ── 2 User Biasa ─────────────────────────────────────────────────
        DB::table('users')->insert([
            [
                'name'       => 'Budi Santoso',
                'email'      => 'budi@example.com',
                'password'   => Hash::make('user123'),
                'role'       => 'USER',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Siti Rahayu',
                'email'      => 'siti@example.com',
                'password'   => Hash::make('user123'),
                'role'       => 'USER',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('✅ Users seeded (1 admin, 2 user biasa)');
        $this->command->info('🏸 Untuk seed lapangan, jalankan: npm run db:seed');
    }
}
