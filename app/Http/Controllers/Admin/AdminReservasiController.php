<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminReservasiController extends Controller
{
    /**
     * Tampilkan semua reservasi untuk admin dengan filter.
     */
    public function index(Request $request): Response
    {
        $query = Reservasi::with(['user', 'lapangan']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%");
                })->orWhere('id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tanggal')) {
            $query->where('tanggal', $request->tanggal);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reservasi = $query
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_mulai', 'desc')
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'user_name' => $r->user->name ?? 'Guest',
                'lapangan_nama' => $r->lapangan->nama ?? 'Lapangan Terhapus',
                'tanggal' => $r->tanggal->toDateString(),
                'jam_mulai' => substr($r->jam_mulai, 0, 5),
                'jam_selesai' => substr($r->jam_selesai, 0, 5),
                'status' => $r->status,
                'created_at' => $r->created_at->toDateTimeString(),
            ]);

        return Inertia::render('Admin/Reservasi/Index', [
            'reservasiList' => $reservasi,
            'filters' => $request->only(['search', 'tanggal', 'status']),
        ]);
    }

    /**
     * Ubah status reservasi (Konfirmasi / Batalkan).
     */
    public function updateStatus(Request $request, Reservasi $reservasi): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:KONFIRMASI,BATAL'],
        ], [
            'status.required' => 'Status wajib ditentukan.',
            'status.in' => 'Status yang dipilih tidak valid.',
        ]);

        $reservasi->update([
            'status' => $validated['status'],
        ]);

        $actionText = $validated['status'] === 'KONFIRMASI' ? 'dikonfirmasi' : 'dibatalkan';

        return redirect()
            ->route('admin.reservasi.index')
            ->with('success', "Reservasi berhasil {$actionText}!");
    }

    /**
     * Hapus reservasi secara permanen.
     */
    public function destroy(Reservasi $reservasi): RedirectResponse
    {
        $reservasi->delete();

        return redirect()
            ->route('admin.reservasi.index')
            ->with('success', 'Reservasi berhasil dihapus.');
    }
}
