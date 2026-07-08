<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration ini membuat tabel lapangan dan reservasi yang sinkron dengan Prisma schema.
 *
 * CATATAN:
 * - Jika menggunakan `npm run db:push` (Prisma), migration ini tidak perlu dijalankan.
 * - Gunakan migration ini HANYA jika ingin setup lewat Laravel migration.
 * - Jangan jalankan keduanya sekaligus di DB yang sama.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ─── Lapangan ─────────────────────────────────────────────────────
        Schema::create('lapangan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
                        $table->string('jenis', 100);
            $table->string('deskripsi')->nullable();
            $table->decimal('harga_per_jam', 10, 2);
            $table->string('foto_url', 500)->nullable();
            $table->boolean('is_tersedia')->default(true);
            $table->timestampTz('created_at')->useCurrent();
        });

        // ─── Reservasi ────────────────────────────────────────────────────
        Schema::create('reservasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lapangan_id')->constrained('lapangan')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->enum('status', ['PENDING', 'KONFIRMASI', 'BATAL'])->default('PENDING');
            $table->timestampTz('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservasi');
        Schema::dropIfExists('lapangan');
    }
};
