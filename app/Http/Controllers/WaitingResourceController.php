<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaitingResource;

class WaitingResourceController extends Controller
{
    public function index()
    {
        $waitingList = WaitingResource::latest()->get();
        return view('waiting-resources.index', compact('waitingList'));
    }

    // API Endpoint untuk menerima data dari modul produksi temanmu
    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'production_code' => 'required|string',
            'product_name' => 'required|string',
            'quantity' => 'required|integer',
            'notes' => 'nullable|string',
        ]);

        $waiting = WaitingResource::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data produksi berhasil dikirim ke Resources!',
            'data' => $waiting
        ], 201);
    }
}