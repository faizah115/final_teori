<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lapangan extends Model
{
    /**
     * Nama tabel sesuai @@map("lapangan") di Prisma schema.
     * Laravel secara default akan mencari "lapangans" (plural), jadi kita override.
     */
    protected $table = 'lapangan';

    /**
     * Prisma schema hanya mendefinisikan created_at, bukan updated_at.
     */
    const UPDATED_AT = null;

    protected $fillable = [
        'nama',
        'jenis',
        'harga_per_jam',
        'foto_url',
        'deskripsi',
        'is_tersedia',
    ];

    protected $casts = [
        'harga_per_jam' => 'decimal:2',
        'is_tersedia'   => 'boolean',
        'created_at'    => 'datetime',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function reservasi(): HasMany
    {
        return $this->hasMany(Reservasi::class);
    }
}
