<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionOrder; // Menggunakan model Produksi Anda

class WaitingResourceController extends Controller
{
    public function index()
    {
        // 1. Ambil data dari tabel Produksi Anda (Ambil yang berstatus Menunggu Bahan Baku)
        $rawList = collect();
        if (class_exists('\App\Models\ProductionOrder')) {
            $rawList = ProductionOrder::whereIn('status', ['Menunggu Bahan Baku', 'Proses Produksi Berjalan'])->latest()->get();
        }

        // 2. "Terjemahkan" nama kolom database Anda agar pas dengan desain Blade Bonifasius
        $waitingList = $rawList->map(function($item) {
            return (object) [
                'id'              => $item->id,
                'production_code' => $item->no_po,             // no_po diterjemahkan jadi production_code
                'product_name'    => $item->produk,            // produk diterjemahkan jadi product_name
                'quantity'        => $item->jumlah_produksi,   // jumlah diterjemahkan jadi quantity
                'status'          => $item->status,
                'notes'           => $item->keterangan         // keterangan diterjemahkan jadi notes
            ];
        });

        return view('waiting-resources.index', compact('waitingList'));
    }

    // API Endpoint bawaan teman Anda tetap dibiarkan agar tidak error jika ada modul lain yang memanggilnya
    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'production_code' => 'required|string',
            'product_name' => 'required|string',
            'quantity' => 'required|integer',
            'notes' => 'nullable|string',
        ]);

        // Dialihkan untuk menyimpan ke tabel Produksi Anda langsung
        if (class_exists('\App\Models\ProductionOrder')) {
            $waiting = ProductionOrder::create([
                'no_po'           => $validated['production_code'],
                'produk'          => $validated['product_name'],
                'jumlah_produksi' => $validated['quantity'],
                'keterangan'      => $validated['notes'],
                'target_selesai'  => now()->addDays(7), // Default target
                'status'          => 'Menunggu Bahan Baku'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data produksi berhasil disinkronkan ke Resources!',
                'data' => $waiting
            ], 201);
        }

        return response()->json(['success' => false, 'message' => 'Tabel Produksi belum tersedia.'], 500);
    }
}