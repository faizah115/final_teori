<?php

namespace App\Http\Controllers;

use App\Models\Reservasi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserDashboardController extends Controller
{
    public function index()
    {
        // Admin tidak boleh mengakses dashboard user
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $userId = auth()->id();
        $today = Carbon::today()->format('Y-m-d');
        
        $allReservasi = Reservasi::with('lapangan')
            ->where('user_id', $userId)
            ->orderByDesc('tanggal')
            ->orderByDesc('jam_mulai')
            ->get();

        $reservasiAktif = [];
        $riwayatReservasi = [];

        foreach ($allReservasi as $r) {
            $formatted = [
                'id'                => $r->id,
                'tanggal'           => $r->tanggal,
                'jam_mulai'         => $r->jam_mulai,
                'jam_selesai'       => $r->jam_selesai,
                'status'            => $r->status,
                'lapangan_nama'     => $r->lapangan?->nama ?? '-',
                'lapangan_foto_url' => $r->lapangan?->foto_url ?? null,
                'harga_per_jam'     => $r->lapangan?->harga_per_jam ?? 0,
                'created_at'        => $r->created_at->format('Y-m-d H:i:s'),
            ];

            if ($r->status === 'BATAL') {
                $riwayatReservasi[] = $formatted;
            } elseif ($r->tanggal < $today) {
                $riwayatReservasi[] = $formatted;
            } else {
                $reservasiAktif[] = $formatted;
            }
        }

        return Inertia::render('Dashboard', [
            'reservasiAktif'   => $reservasiAktif,
            'riwayatReservasi' => $riwayatReservasi,
        ]);
    }
}
