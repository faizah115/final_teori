<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use App\Models\Reservasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservasiController extends Controller
{
    /**
     * Buat reservasi baru. Hanya untuk user yang sudah login.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'lapangan_id' => ['required', 'integer', 'exists:lapangan,id'],
                'tanggal'     => ['required', 'date', 'after_or_equal:today'],
                'jam_mulai'   => ['required', 'date_format:H:i'],
                'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            ],
            [
                'lapangan_id.required'     => 'Lapangan harus dipilih.',
                'lapangan_id.exists'       => 'Lapangan tidak ditemukan.',
                'tanggal.required'         => 'Tanggal harus diisi.',
                'tanggal.after_or_equal'   => 'Tanggal tidak boleh di masa lalu.',
                'jam_mulai.required'       => 'Jam mulai harus diisi.',
                'jam_mulai.date_format'    => 'Format jam mulai tidak valid (HH:MM).',
                'jam_selesai.required'     => 'Jam selesai harus diisi.',
                'jam_selesai.date_format'  => 'Format jam selesai tidak valid (HH:MM).',
                'jam_selesai.after'        => 'Jam selesai harus lebih dari jam mulai.',
            ]
        );

        // ── Cek Ketersediaan Lapangan ───────────────────────────────────────
        $lapangan = Lapangan::find($validated['lapangan_id']);
        if (!$lapangan || !$lapangan->is_tersedia) {
            return back()->withErrors([
                'lapangan_id' => 'Lapangan yang dipilih sedang tidak tersedia.',
            ])->withInput();
        }

        // ── Cek Durasi Minimal (30 Menit) ───────────────────────────────────
        $mulai = strtotime($validated['jam_mulai']);
        $selesai = strtotime($validated['jam_selesai']);
        if (($selesai - $mulai) < 1800) {
            return back()->withErrors([
                'jam_selesai' => 'Durasi reservasi minimal adalah 30 menit.',
            ])->withInput();
        }

        // ── Cek bentrok jadwal ──────────────────────────────────────────────
        $jamMulai = $validated['jam_mulai'] . ':00';
        $jamSelesai = $validated['jam_selesai'] . ':00';

        $bentrok = Reservasi::where('lapangan_id', $validated['lapangan_id'])
            ->where('tanggal', $validated['tanggal'])
            ->whereIn('status', ['PENDING', 'KONFIRMASI'])
            ->where(function ($query) use ($jamMulai, $jamSelesai) {
                // Waktu baru overlap dengan reservasi yang ada
                $query->where(function ($q) use ($jamMulai, $jamSelesai) {
                    $q->where('jam_mulai', '<', $jamSelesai)
                      ->where('jam_selesai', '>', $jamMulai);
                });
            })
            ->exists();

        if ($bentrok) {
            return back()->withErrors([
                'jam_mulai' => 'Jadwal yang dipilih sudah terisi. Silakan pilih waktu lain.',
            ])->withInput();
        }

        // ── Buat Reservasi ────────────────────────────────────────────────────
        Reservasi::create([
            'user_id'     => Auth::id(),
            'lapangan_id' => $validated['lapangan_id'],
            'tanggal'     => $validated['tanggal'],
            'jam_mulai'   => $validated['jam_mulai'] . ':00',
            'jam_selesai' => $validated['jam_selesai'] . ':00',
            'status'      => 'PENDING',
        ]);

        return redirect()
            ->route('lapangan.show', $validated['lapangan_id'])
            ->with('success', 'Reservasi berhasil dibuat! Status PENDING — menunggu konfirmasi admin.');
    }
}
