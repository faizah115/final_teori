<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Tampilkan halaman landing page dengan daftar lapangan tersedia.
     */
    public function index(): Response
    {
        $lapanganTersedia = Lapangan::where('is_tersedia', true)
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return Inertia::render('Home', [
            'lapangan'      => $lapanganTersedia,
            'totalLapangan' => Lapangan::count(),
            'totalTersedia' => Lapangan::where('is_tersedia', true)->count(),
        ]);
    }
}
