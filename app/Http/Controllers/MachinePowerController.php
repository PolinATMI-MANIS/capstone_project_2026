<?php

namespace App\Http\Controllers;

use App\Models\MachinePower;
use App\Models\ManPower;
use App\Models\ApprovalRequest;
use Illuminate\Http\Request;

class MachinePowerController extends Controller
{
    public function index()
    {
        $machinePowers = MachinePower::with('operator')->get();
        
        $waitingOperators = ManPower::where('status', 'Kerja')
            ->whereDoesntHave('machines', function($query) {
                $query->where('status', 'Running');
            })
            ->get();

        return view('machine-power.index', compact('machinePowers', 'waitingOperators'));
    }

    public function create()
    {
        return view('machine-power.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'machine_name' => 'required|string|max:255',
            'machine_type' => 'required|string|max:255',
            'status'       => 'required|in:Standby,Running,Breakdown',
        ]);

        $data = $request->except(['_token']);

        if (auth()->user()->role === 'user') {
            ApprovalRequest::create([
                'user_id'     => auth()->id(),
                'target_type' => 'MachinePower',
                'target_id'   => 0,
                'target_name' => $data['machine_name'],
                'action_type' => 'create',
                'payload'     => json_encode($data),
            ]);

            return redirect()->route('machine-power.index')->with('success', 'Pengajuan penambahan Machine Power telah dikirim ke Admin/Super Admin.');
        }

        MachinePower::create($data);
        return redirect()->route('machine-power.index')->with('success', 'Data Machine Power berhasil ditambahkan.');
    }

    public function edit(MachinePower $machinePower)
    {
        return view('machine-power.edit', compact('machinePower'));
    }

    public function update(Request $request, MachinePower $machinePower)
    {
        $request->validate([
            'machine_name' => 'required|string|max:255',
            'machine_type' => 'required|string|max:255',
            'status'       => 'required|in:Standby,Running,Breakdown',
        ]);

        $data = $request->except(['_token', '_method']);

        if (auth()->user()->role === 'user') {
            ApprovalRequest::create([
                'user_id'     => auth()->id(),
                'target_type' => 'MachinePower',
                'target_id'   => $machinePower->id,
                'target_name' => $data['machine_name'],
                'action_type' => 'update',
                'payload'     => json_encode($data),
            ]);

            return redirect()->route('machine-power.index')->with('success', 'Pengajuan pembaruan data Machine Power telah dikirim ke Admin/Super Admin.');
        }

        $machinePower->update($data);
        return redirect()->route('machine-power.index')->with('success', 'Data Machine Power berhasil diperbarui.');
    }

    public function destroy(Request $request, MachinePower $machinePower)
    {
        if (auth()->user()->role === 'super_admin') {
            $machinePower->delete();
            $msg = 'Data Machine Power berhasil dihapus permanen.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return redirect()->route('machine-power.index')->with('success', $msg);
        }

        ApprovalRequest::create([
            'user_id'     => auth()->id(),
            'target_type' => 'MachinePower',
            'target_id'   => $machinePower->id,
            'target_name' => $machinePower->machine_name,
            'action_type' => 'delete',
        ]);
        
        $msg = 'Permintaan hapus mesin telah dikirim ke Super Admin.';
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }
        return redirect()->route('machine-power.index')->with('success', $msg);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Standby,Running,Breakdown,Idle,Kerja,Cuti'
        ]);
        
        if (auth()->user()->role === 'user') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $machinePower = MachinePower::findOrFail($id);
        
        $updateData = ['status' => $request->status];

        if ($request->has('man_power_id')) {
            $updateData['man_power_id'] = $request->man_power_id;
        }

        if ($request->has('start_time')) {
            $updateData['start_time'] = $request->start_time;
        }

        if ($request->status === 'Standby') {
            $updateData['man_power_id'] = null;
            $updateData['start_time'] = null;
        }

        $machinePower->update($updateData);

        return response()->json(['success' => true, 'message' => 'Status dan jam mulai mesin diperbarui.']);
    }
}