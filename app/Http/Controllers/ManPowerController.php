<?php

namespace App\Http\Controllers;

use App\Models\ManPower;
use App\Models\MachinePower;
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
        // User dan Admin bisa akses form input, nanti sistem approval yang menentukan
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

        // Jika yang membuat adalah 'user', arahkan ke status pending/approval jika diperlukan
        // Sesuai flowchart: CUD oleh user/admin memicu notifikasi approval
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
            if ($manPower->foto && Storage::disk('public')->exists($manPower->foto)) {
                Storage::disk('public')->delete($manPower->foto);
            }
            $data['foto'] = $request->file('foto')->store('man-power-photos', 'public');
        }

        $manPower->update($data);

        return redirect()->route('man-power.index')->with('success', 'Data Man Power berhasil diperbarui.');
    }

    public function destroy(ManPower $manPower)
    {
        // ATURAN FLOWCHART: Hanya Super Admin yang bisa langsung menghapus data
        if (auth()->user()->role !== 'super_admin') {
            return redirect()->back()->with('error', 'Aksi hapus ditolak. Fitur Delete untuk Admin/User memerlukan approval dari Super Admin.');
        }

        MachinePower::where('man_power_id', $manPower->id)->update([
            'status' => 'Standby',
            'man_power_id' => null
        ]);

        if ($manPower->foto && Storage::disk('public')->exists($manPower->foto)) {
            Storage::disk('public')->delete($manPower->foto);
        }

        $manPower->delete();

        return redirect()->route('man-power.index')->with('success', 'Data Man Power berhasil dihapus secara permanen.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Idle,Kerja,Cuti',
        ]);

        $manPower = ManPower::findOrFail($id);
        $newStatus = $request->status;

        $manPower->update([
            'status' => $newStatus
        ]);

        if ($newStatus === 'Idle') {
            MachinePower::where('man_power_id', $manPower->id)->update([
                'status' => 'Standby',
                'man_power_id' => null
            ]);
        }

        return response()->json([
            'success' => true, 
            'message' => 'Status pekerja berhasil diperbarui ke ' . $newStatus
        ]);
    }
}