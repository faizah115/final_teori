<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class LapanganController extends Controller
{
    /**
     * Tampilkan daftar lapangan dengan filter pencarian.
     */
    public function index(Request $request): Response
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

        // Ambil list jenis unik untuk filter dropdown
        $jenisOptions = Lapangan::select('jenis')
            ->distinct()
            ->orderBy('jenis')
            ->pluck('jenis');

        return Inertia::render('Lapangan/Index', [
            'lapangan'     => $lapangan,
            'jenisOptions' => $jenisOptions,
            'filters'      => [
                'search' => $search,
                'jenis'  => $jenis,
            ],
        ]);
    }

    /**
     * Tampilkan detail lapangan beserta jadwal yang sudah terisi.
     */
    public function show(Lapangan $lapangan): Response
    {
        // Ambil jadwal terisi mulai hari ini ke depan
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

        return Inertia::render('Lapangan/Show', [
            'lapangan'     => $lapangan,
            'jadwalTerisi' => $jadwalTerisi,
        ]);
    }
}
