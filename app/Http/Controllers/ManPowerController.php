<?php

namespace App\Http\Controllers;

use App\Models\ManPower;
use App\Models\MachinePower;
use App\Models\ApprovalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ManPowerController extends Controller
{
    public function index()
    {
        $manPowers = ManPower::all();
        return view('man-power.index', compact('manPowers'));
    }

    public function create()
    {
        return view('man-power.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'   => 'required|string|max:255',
            'posisi' => 'required|string|max:255',
            'status' => 'required|in:Idle,Kerja,Cuti',
            'foto'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except(['_token']);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('man-power-photos', 'public');
        }

        $userRole = auth()->check() ? auth()->user()->role : 'user';
        $userId   = auth()->check() ? auth()->id() : 1;

        if ($userRole === 'user') {
            try {
                ApprovalRequest::create([
                    'user_id'     => $userId,
                    'target_type' => 'ManPower',
                    'target_id'   => 0, 
                    'target_name' => $data['nama'],
                    'action_type' => 'create',
                    'payload'     => json_encode($data),
                ]);
                return redirect()->route('man-power.index')->with('success', 'Pengajuan penambahan Man Power telah dikirim.');
            } catch (\Exception $e) {
                return redirect()->route('man-power.index')->with('warning', 'Sistem Approval belum siap.');
            }
        }

        ManPower::create($data);
        return redirect()->route('man-power.index')->with('success', 'Data Man Power berhasil ditambahkan.');
    }

    public function edit(ManPower $manPower)
    {
        return view('man-power.edit', compact('manPower'));
    }

    public function update(Request $request, ManPower $manPower)
    {
        $request->validate([
            'nama'   => 'required|string|max:255',
            'posisi' => 'required|string|max:255',
            'status' => 'required|in:Idle,Kerja,Cuti',
            'foto'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except(['_token', '_method']);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('man-power-photos', 'public');
        }

        $userRole = auth()->check() ? auth()->user()->role : 'user';
        $userId   = auth()->check() ? auth()->id() : 1;

        if ($userRole === 'user') {
            try {
                ApprovalRequest::create([
                    'user_id'     => $userId,
                    'target_type' => 'ManPower',
                    'target_id'   => $manPower->id,
                    'target_name' => $data['nama'],
                    'action_type' => 'update',
                    'payload'     => json_encode($data),
                ]);
                return redirect()->route('man-power.index')->with('success', 'Pengajuan pembaruan data dikirim.');
            } catch (\Exception $e) {
                return redirect()->route('man-power.index')->with('warning', 'Sistem Approval belum siap.');
            }
        }

        if ($request->hasFile('foto') && $manPower->foto && Storage::disk('public')->exists($manPower->foto)) {
            Storage::disk('public')->delete($manPower->foto);
        }

        $manPower->update($data);
        return redirect()->route('man-power.index')->with('success', 'Data Man Power berhasil diperbarui.');
    }

    public function destroy(Request $request, ManPower $manPower)
    {
        $userRole = auth()->check() ? auth()->user()->role : 'user';
        $userId   = auth()->check() ? auth()->id() : 1;

        if ($userRole === 'super_admin') {
            MachinePower::where('man_power_id', $manPower->id)->update([
                'status' => 'Standby',
                'man_power_id' => null
            ]);
            if ($manPower->foto && Storage::disk('public')->exists($manPower->foto)) {
                Storage::disk('public')->delete($manPower->foto);
            }
            $manPower->delete();
            $msg = 'Data berhasil dihapus permanen.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return redirect()->route('man-power.index')->with('success', $msg);
        }

        try {
            ApprovalRequest::create([
                'user_id'     => $userId,
                'target_type' => 'ManPower',
                'target_id'   => $manPower->id,
                'target_name' => $manPower->nama,
                'action_type' => 'delete',
            ]);
            
            $msg = 'Permintaan hapus telah dikirim ke Super Admin.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return redirect()->route('man-power.index')->with('success', $msg);
        } catch (\Exception $e) {
            $msg = 'Sistem Approval belum siap.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 500);
            }
            return redirect()->route('man-power.index')->with('warning', $msg);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $manPower = ManPower::findOrFail($id);
            
            $statusInput = $request->input('status') ?? $request->json('status') ?? 'Idle';
            $manPower->status = $statusInput;
            
            // HAPUS TOTAL LOGIKA PRODUCTION_ORDER_ID SUPAYA TIDAK ERROR 500
            $manPower->save();

            // Jika pekerja ditarik kembali ke Idle atau Cuti, bersihkan mesin terkait
            if (in_array($statusInput, ['Idle', 'Cuti'])) {
                MachinePower::where('man_power_id', $id)->update([
                    'status'       => 'Standby',
                    'man_power_id' => null,
                    'start_time'   => null
                ]);
            }

            return response()->json([
                'success' => true, 
                'message' => 'Status berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ERROR: ' . $e->getMessage()
            ], 500);
        }
    }
}