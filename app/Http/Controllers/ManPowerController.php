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

        // JIKA USER: Masuk ke Approval Request (Menunggu Admin/Super Admin)
        if (auth()->user()->role === 'user') {
            ApprovalRequest::create([
                'user_id'     => auth()->id(),
                'target_type' => 'ManPower',
                'target_id'   => 0, // Belum ada ID karena baru mau dibuat
                'target_name' => $data['nama'],
                'action_type' => 'create',
                'payload'     => json_encode($data),
            ]);

            return redirect()->route('man-power.index')->with('success', 'Pengajuan penambahan Man Power telah dikirim ke Admin/Super Admin.');
        }

        // JIKA ADMIN / SUPER ADMIN: Langsung Eksekusi
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

        // JIKA USER: Masuk ke Approval Request untuk Update
        if (auth()->user()->role === 'user') {
            ApprovalRequest::create([
                'user_id'     => auth()->id(),
                'target_type' => 'ManPower',
                'target_id'   => $manPower->id,
                'target_name' => $data['nama'],
                'action_type' => 'update',
                'payload'     => json_encode($data),
            ]);

            return redirect()->route('man-power.index')->with('success', 'Pengajuan pembaruan data Man Power telah dikirim ke Admin/Super Admin.');
        }

        // JIKA ADMIN / SUPER ADMIN: Langsung Eksekusi
        if ($request->hasFile('foto') && $manPower->foto && Storage::disk('public')->exists($manPower->foto)) {
            Storage::disk('public')->delete($manPower->foto);
        }

        $manPower->update($data);
        return redirect()->route('man-power.index')->with('success', 'Data Man Power berhasil diperbarui.');
    }

    public function destroy(Request $request, ManPower $manPower)
    {
        // SUPER ADMIN: Hapus Permanen Langsung
        if (auth()->user()->role === 'super_admin') {
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

        // ADMIN / USER: Masuk ke Approval Request untuk Delete
        ApprovalRequest::create([
            'user_id'     => auth()->id(),
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
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:Idle,Kerja,Cuti']);
        $manPower = ManPower::findOrFail($id);
        
        if (auth()->user()->role === 'user') {
            return response()->json(['success' => false, 'message' => 'User tidak memiliki izin ubah status langsung.'], 403);
        }

        $manPower->update(['status' => $request->status]);
        if ($request->status === 'Idle') {
            MachinePower::where('man_power_id', $manPower->id)->update(['status' => 'Standby', 'man_power_id' => null]);
        }

        return response()->json(['success' => true, 'message' => 'Status diperbarui.']);
    }
}