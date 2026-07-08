<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lapangan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class LapanganController extends Controller
{
    /**
     * Tampilkan daftar lapangan untuk admin.
     */
    public function index(Request $request): Response
    {
        $query = Lapangan::query();

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'ilike', "%{$search}%")
                  ->orWhere('jenis', 'ilike', "%{$search}%");
            });
        }

        $lapangan = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return Inertia::render('Admin/Lapangan/Index', [
            'lapangan' => $lapangan,
            'filters'  => $request->only(['search']),
        ]);
    }

    /**
     * Tampilkan form tambah lapangan.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Lapangan/Create');
    }

    /**
     * Simpan lapangan baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        // Parse is_tersedia ke boolean karena multipart/form-data mengirimkannya sebagai string
        $request->merge([
            'is_tersedia' => filter_var($request->input('is_tersedia'), FILTER_VALIDATE_BOOLEAN),
        ]);

        $validated = $request->validate([
            'nama'         => ['required', 'string', 'max:255'],
            'jenis'        => ['required', 'string', 'in:VIP,Premium,Standar,Outdoor'],
            'harga_per_jam'=> ['required', 'numeric', 'min:0'],
            'deskripsi'    => ['nullable', 'string', 'max:1000'],
            'foto'         => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'is_tersedia'  => ['required', 'boolean'],
        ], [
            'nama.required'         => 'Nama lapangan wajib diisi.',
            'jenis.required'        => 'Jenis lapangan wajib dipilih.',
            'jenis.in'              => 'Jenis lapangan tidak valid.',
            'harga_per_jam.required'=> 'Harga per jam wajib diisi.',
            'harga_per_jam.numeric' => 'Harga per jam harus berupa angka.',
            'harga_per_jam.min'     => 'Harga per jam tidak boleh kurang dari 0.',
            'foto.required'         => 'Foto lapangan wajib diunggah.',
            'foto.image'            => 'File harus berupa gambar.',
            'foto.mimes'            => 'Format foto harus jpeg, png, jpg, atau webp.',
            'foto.max'              => 'Ukuran foto maksimal 2MB.',
            'is_tersedia.required'  => 'Status ketersediaan wajib diisi.',
        ]);

        // Simpan file foto
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('lapangan', 'public');
            $validated['foto_url'] = Storage::url($path);
        }

        // Buat record baru
        Lapangan::create([
            'nama'         => $validated['nama'],
            'jenis'        => $validated['jenis'],
            'harga_per_jam'=> $validated['harga_per_jam'],
            'deskripsi'    => $validated['deskripsi'] ?? null,
            'foto_url'     => $validated['foto_url'] ?? null,
            'is_tersedia'  => $validated['is_tersedia'],
        ]);

        return redirect()
            ->route('admin.lapangan.index')
            ->with('success', 'Lapangan baru berhasil ditambahkan!');
    }

    /**
     * Tampilkan form edit lapangan.
     */
    public function edit(Lapangan $lapangan): Response
    {
        return Inertia::render('Admin/Lapangan/Edit', [
            'lapangan' => $lapangan,
        ]);
    }

    /**
     * Update data lapangan di database.
     */
    public function update(Request $request, Lapangan $lapangan): RedirectResponse
    {
        // Parse is_tersedia ke boolean karena multipart/form-data mengirimkannya sebagai string
        $request->merge([
            'is_tersedia' => filter_var($request->input('is_tersedia'), FILTER_VALIDATE_BOOLEAN),
        ]);

        $validated = $request->validate([
            'nama'         => ['required', 'string', 'max:255'],
            'jenis'        => ['required', 'string', 'in:VIP,Premium,Standar,Outdoor'],
            'harga_per_jam'=> ['required', 'numeric', 'min:0'],
            'deskripsi'    => ['nullable', 'string', 'max:1000'],
            'foto'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'is_tersedia'  => ['required', 'boolean'],
        ], [
            'nama.required'         => 'Nama lapangan wajib diisi.',
            'jenis.required'        => 'Jenis lapangan wajib dipilih.',
            'jenis.in'              => 'Jenis lapangan tidak valid.',
            'harga_per_jam.required'=> 'Harga per jam wajib diisi.',
            'harga_per_jam.numeric' => 'Harga per jam harus berupa angka.',
            'harga_per_jam.min'     => 'Harga per jam tidak boleh kurang dari 0.',
            'foto.image'            => 'File harus berupa gambar.',
            'foto.mimes'            => 'Format foto harus jpeg, png, jpg, atau webp.',
            'foto.max'              => 'Ukuran foto maksimal 2MB.',
            'is_tersedia.required'  => 'Status ketersediaan wajib diisi.',
        ]);

        // Simpan file foto jika ada yang baru diunggah
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($lapangan->foto_url) {
                // Konversi URL ke path storage relatif
                $oldPath = str_replace('/storage/', '', $lapangan->foto_url);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $path = $request->file('foto')->store('lapangan', 'public');
            $validated['foto_url'] = Storage::url($path);
        } else {
            // Tetap gunakan foto url yang lama jika tidak ada upload baru
            $validated['foto_url'] = $lapangan->foto_url;
        }

        // Update record
        $lapangan->update([
            'nama'         => $validated['nama'],
            'jenis'        => $validated['jenis'],
            'harga_per_jam'=> $validated['harga_per_jam'],
            'deskripsi'    => $validated['deskripsi'] ?? null,
            'foto_url'     => $validated['foto_url'],
            'is_tersedia'  => $validated['is_tersedia'],
        ]);

        return redirect()
            ->route('admin.lapangan.index')
            ->with('success', 'Data lapangan berhasil diperbarui!');
    }

    /**
     * Toggle ketersediaan lapangan secara cepat.
     */
    public function toggleStatus(Lapangan $lapangan): RedirectResponse
    {
        $lapangan->update(['is_tersedia' => !$lapangan->is_tersedia]);

        $status = $lapangan->is_tersedia ? 'Tersedia' : 'Tidak Tersedia';

        return back()->with('success', "Status lapangan \"{$lapangan->nama}\" diubah menjadi {$status}.");
    }

    /**
     * Hapus lapangan dari database.
     */
    public function destroy(Lapangan $lapangan): RedirectResponse
    {
        // Hapus file foto dari storage
        if ($lapangan->foto_url) {
            $oldPath = str_replace('/storage/', '', $lapangan->foto_url);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $lapangan->delete();

        return redirect()
            ->route('admin.lapangan.index')
            ->with('success', 'Lapangan berhasil dihapus!');
    }
}
