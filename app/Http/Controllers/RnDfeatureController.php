<?php

namespace App\Http\Controllers;

use App\Models\RnDfeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RnDfeatureController extends Controller
{
    // 1. Menampilkan daftar pengajuan & progres RnD
    public function index()
    {
        $features = RnDfeature::latest()->get();
        return view('rnd.index', compact('features'));
    }

    // 2. Menampilkan form pengajuan baru
    public function create()
    {
        return view('rnd.create');
    }

    // 3. Menyimpan data pengajuan baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'nama_pengaju' => 'required|string|max:255',
            'deskripsi_konsep' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'nama_produk.required' => 'Nama produk wajib diisi!',
            'nama_pengaju.required' => 'Nama pengaju wajib diisi!',
            'deskripsi_konsep.required' => 'Deskripsi konsep wajib diisi!',
            'gambar.image' => 'File harus berupa gambar (JPG, PNG, WEBP).',
            'gambar.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('rnd_sketsa', 'public');
        }

        RnDfeature::create([
            'nama_produk' => $request->nama_produk,
            'nama_pengaju' => $request->nama_pengaju,
            'deskripsi_konsep' => $request->deskripsi_konsep,
            'gambar' => $gambarPath,
            'status' => 'Pengajuan Konsep',
        ]);

        return redirect()->route('rnd.index')->with('success', 'Pengajuan konsep produk berhasil disimpan!');
    }

    // 4. Menampilkan detail & timeline status produk
    public function show($id)
    {
        $feature = RnDfeature::findOrFail($id);
        return view('rnd.show', compact('feature'));
    }

    // 5. Update Status / Persetujuan Alur Flowchart R&D
    public function updateStatus(Request $request, $id)
    {
        // PROTEKSI BACKEND: Batasi hanya Admin / SuperAdmin
        if (auth()->check() && (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin())) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui status R&D.');
        }

        $request->validate([
            'action' => 'required|string',
            'catatan_revisi' => 'nullable|string',
            'dokumen_teknis' => 'nullable|file|mimes:pdf,docx,zip|max:5096',
        ], [
            'action.required' => 'Silakan pilih aksi keputusan terlebih dahulu!',
            'dokumen_teknis.mimes' => 'Format file dokumen harus PDF, DOCX, atau ZIP.',
            'dokumen_teknis.max' => 'Ukuran file dokumen maksimal 5MB.',
        ]);

        $feature = RnDfeature::findOrFail($id);
        $action = $request->action;
        $catatan = $request->catatan_revisi;

        switch ($action) {
            case 'komite_approve':
                $feature->status = 'Desain & Rekayasa';
                break;
            case 'komite_revisi':
                $feature->status = 'Konsep Perlu Revisi';
                $feature->catatan_revisi = $catatan;
                break;
            case 'komite_reject':
                $feature->status = 'Ide Dihentikan';
                $feature->catatan_revisi = $catatan;
                break;

            case 'to_prototype':
                $feature->status = 'Pembuatan Prototype';
                break;
            case 'to_uji_internal':
                $feature->status = 'Uji Teknis Internal';
                break;
            case 'teknis_approve':
                $feature->status = 'Pengujian Validasi';
                break;
            case 'teknis_revisi':
                $feature->status = 'Rekayasa Ulang';
                $feature->catatan_revisi = $catatan;
                break;

            case 'sertifikasi_approve':
                $feature->status = 'Persiapan Produksi';
                break;
            case 'sertifikasi_revisi':
                $feature->status = 'Remedial Uji';
                $feature->catatan_revisi = $catatan;
                break;

            case 'to_produksi':
                $feature->status = 'Produksi Komersial';
                break;
            case 'to_launch':
                $feature->status = 'Peluncuran Produk Baru';
                break;
        }

        if ($request->hasFile('dokumen_teknis')) {
            if ($feature->dokumen_teknis && Storage::disk('public')->exists($feature->dokumen_teknis)) {
                Storage::disk('public')->delete($feature->dokumen_teknis);
            }
            $feature->dokumen_teknis = $request->file('dokumen_teknis')->store('rnd_dokumen', 'public');
        }

        $feature->save();

        return redirect()->route('rnd.index')->with('success', 'Status R&D berhasil diperbarui!');
    }

    // 6. Hapus Data R&D
    public function destroy($id)
    {
        // PROTEKSI BACKEND: Batasi hanya Admin / SuperAdmin
        if (auth()->check() && (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin())) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data R&D.');
        }

        $feature = RnDfeature::findOrFail($id);

        // Hapus file gambar jika ada
        if ($feature->gambar && Storage::disk('public')->exists($feature->gambar)) {
            Storage::disk('public')->delete($feature->gambar);
        }

        // Hapus file dokumen teknis jika ada
        if ($feature->dokumen_teknis && Storage::disk('public')->exists($feature->dokumen_teknis)) {
            Storage::disk('public')->delete($feature->dokumen_teknis);
        }

        $feature->delete();

        return redirect()->route('rnd.index')->with('success', 'Data R&D berhasil dihapus!');
    }
}