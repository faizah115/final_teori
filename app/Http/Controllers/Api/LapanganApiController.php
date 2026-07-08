<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lapangan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LapanganApiController extends Controller
{
    /**
     * GET /api/lapangan
     * Menampilkan daftar semua lapangan dengan filter opsional.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->get('search', '');
        $jenis  = $request->get('jenis', '');

        $lapangan = Lapangan::query()
            ->when(
                $search,
                fn ($q) => $q->where('nama', 'ilike', "%{$search}%")
            )
            ->when(
                $jenis,
                fn ($q) => $q->where('jenis', $jenis)
            )
            ->orderByRaw('is_tersedia DESC')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar lapangan berhasil diambil.',
            'data'    => $lapangan,
        ]);
    }

    /**
     * GET /api/lapangan/{id}
     * Menampilkan detail lapangan beserta jadwal yang sudah terisi.
     */
    public function show(Lapangan $lapangan): JsonResponse
    {
        $jadwalTerisi = $lapangan->reservasi()
            ->where('tanggal', '>=', today()->toDateString())
            ->whereIn('status', ['PENDING', 'KONFIRMASI'])
            ->select('tanggal', 'jam_mulai', 'jam_selesai', 'status')
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get()
            ->map(fn ($r) => [
                'tanggal'     => $r->tanggal->toDateString(),
                'jam_mulai'   => substr($r->jam_mulai, 0, 5),
                'jam_selesai' => substr($r->jam_selesai, 0, 5),
                'status'      => $r->status,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Detail lapangan berhasil diambil.',
            'data'    => [
                'lapangan'     => $lapangan,
                'jadwalTerisi' => $jadwalTerisi,
            ],
        ]);
    }
}
