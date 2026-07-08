<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lapangan;
use App\Models\Reservasi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->format('Y-m-d');
        
        // --- 1. Statistik Dasar ---
        $stats = [
            'total_pengguna'   => User::where('role', 'user')->count(),
            'total_lapangan'   => Lapangan::count(),
            'total_reservasi'  => Reservasi::count(),
            'reservasi_hari_ini'=> Reservasi::where('tanggal', $today)->count(),
            'total_pending'    => Reservasi::where('status', 'PENDING')->count(),
            'total_konfirmasi' => Reservasi::where('status', 'KONFIRMASI')->count(),
            'total_batal'      => Reservasi::where('status', 'BATAL')->count(),
        ];

        // --- 2. Data Grafik (7 Hari Terakhir) ---
        $chartData = [];
        $chartLabels = [];
        
        // Ambil data dari 6 hari lalu sampai hari ini (total 7 hari)
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('d M');
            $count = Reservasi::where('tanggal', $date->format('Y-m-d'))->count();
            $chartData[] = $count;
        }

        return Inertia::render('Admin/Dashboard', [
            'stats'       => $stats,
            'chartLabels' => $chartLabels,
            'chartData'   => $chartData,
        ]);
    }
}
