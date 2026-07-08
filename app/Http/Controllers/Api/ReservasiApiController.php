<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lapangan;
use App\Models\Reservasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservasiApiController extends Controller
{
    /**
     * GET /api/reservasi
     * Menampilkan daftar reservasi milik user yang sedang login.
     */
    public function index(): JsonResponse
    {
        $reservasi = Reservasi::with('lapangan')
            ->where('user_id', Auth::id())
            ->orderByDesc('tanggal')
            ->orderByDesc('jam_mulai')
            ->get()
            ->map(fn ($r) => [
                'id'            => $r->id,
                'tanggal'       => $r->tanggal->toDateString(),
                'jam_mulai'     => $r->jam_mulai,
                'jam_selesai'   => $r->jam_selesai,
                'status'        => $r->status,
                'lapangan_nama' => $r->lapangan?->nama ?? '-',
                'harga_per_jam' => $r->lapangan?->harga_per_jam ?? 0,
                'created_at'    => $r->created_at->format('Y-m-d H:i:s'),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Daftar reservasi berhasil diambil.',
            'data'    => $reservasi,
        ]);
    }

    /**
     * POST /api/reservasi
     * Buat reservasi baru.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate(
            [
                'lapangan_id' => ['required', 'integer', 'exists:lapangan,id'],
                'tanggal'     => ['required', 'date', 'after_or_equal:today'],
                'jam_mulai'   => ['required', 'date_format:H:i'],
                'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            ],
            [
                'lapangan_id.required'   => 'Lapangan harus dipilih.',
                'lapangan_id.exists'     => 'Lapangan tidak ditemukan.',
                'tanggal.required'       => 'Tanggal harus diisi.',
                'tanggal.after_or_equal' => 'Tanggal tidak boleh di masa lalu.',
                'jam_mulai.required'     => 'Jam mulai harus diisi.',
                'jam_mulai.date_format'  => 'Format jam mulai tidak valid (HH:MM).',
                'jam_selesai.required'   => 'Jam selesai harus diisi.',
                'jam_selesai.date_format'=> 'Format jam selesai tidak valid (HH:MM).',
                'jam_selesai.after'      => 'Jam selesai harus lebih dari jam mulai.',
            ]
        );

        // Cek ketersediaan lapangan
        $lapangan = Lapangan::find($validated['lapangan_id']);
        if (!$lapangan || !$lapangan->is_tersedia) {
            return response()->json([
                'success' => false,
                'message' => 'Lapangan yang dipilih sedang tidak tersedia.',
            ], 422);
        }

        // Cek durasi minimal (30 menit)
        $mulai   = strtotime($validated['jam_mulai']);
        $selesai = strtotime($validated['jam_selesai']);
        if (($selesai - $mulai) < 1800) {
            return response()->json([
                'success' => false,
                'message' => 'Durasi reservasi minimal adalah 30 menit.',
            ], 422);
        }

        // Cek bentrok jadwal
        $jamMulai   = $validated['jam_mulai'] . ':00';
        $jamSelesai = $validated['jam_selesai'] . ':00';

        $bentrok = Reservasi::where('lapangan_id', $validated['lapangan_id'])
            ->where('tanggal', $validated['tanggal'])
            ->whereIn('status', ['PENDING', 'KONFIRMASI'])
            ->where(function ($query) use ($jamMulai, $jamSelesai) {
                $query->where(function ($q) use ($jamMulai, $jamSelesai) {
                    $q->where('jam_mulai', '<', $jamSelesai)
                      ->where('jam_selesai', '>', $jamMulai);
                });
            })
            ->exists();

        if ($bentrok) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal yang dipilih sudah terisi. Silakan pilih waktu lain.',
            ], 409);
        }

        // Buat reservasi
        $reservasi = Reservasi::create([
            'user_id'     => Auth::id(),
            'lapangan_id' => $validated['lapangan_id'],
            'tanggal'     => $validated['tanggal'],
            'jam_mulai'   => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'status'      => 'PENDING',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reservasi berhasil dibuat! Status PENDING — menunggu konfirmasi admin.',
            'data'    => $reservasi,
        ], 201);
    }

    /**
     * GET /api/reservasi/{id}
     * Menampilkan detail satu reservasi milik user.
     */
    public function show(Reservasi $reservasi): JsonResponse
    {
        if ($reservasi->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke reservasi ini.',
            ], 403);
        }

        $reservasi->load('lapangan');

        return response()->json([
            'success' => true,
            'message' => 'Detail reservasi berhasil diambil.',
            'data'    => $reservasi,
        ]);
    }
}
