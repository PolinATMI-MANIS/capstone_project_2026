<?php

namespace App\Http\Controllers;

use App\Models\MachinePower;
use App\Models\ManPower;
use App\Models\ProductionOrder;
use App\Models\ApprovalRequest;
use Illuminate\Http\Request;

class MachinePowerController extends Controller
{
    public function index()
    {
        $machinePowers = MachinePower::all();

        // AUTO-HEALING: RESET MESIN JIKA PEKERJA DI-RETURN
        foreach ($machinePowers as $machine) {
            if ($machine->status === 'Running' && $machine->man_power_id) {
                $pekerja = ManPower::find($machine->man_power_id);
                
                if (!$pekerja || in_array($pekerja->status, ['Idle', 'Cuti'])) {
                    $machine->update([
                        'status'              => 'Standby',
                        'man_power_id'        => null,
                        'start_time'          => null,
                        'production_order_id' => null
                    ]);
                }
            }
        }

        $machinePowers = MachinePower::all();

        $assignedOperatorIds = $machinePowers->where('status', 'Running')
            ->where('man_power_id', '!=', null)
            ->pluck('man_power_id')
            ->toArray();

        $waitingOperators = ManPower::where('status', 'Kerja')
            ->whereNotIn('id', $assignedOperatorIds)
            ->get();

        // Ambil SPK aktif yang sedang ready untuk dipasangkan ke mesin running jika kosong
        $activeSpk = ProductionOrder::whereIn('status', ['ready', 'Proses Produksi Berjalan'])->first();

        return view('machine-power.index', compact('machinePowers', 'waitingOperators', 'activeSpk'));
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
            'status'       => 'required|in:Standby,Running,Maintenance,Idle',
        ]);

        $data = $request->except(['_token']);

        $userRole = auth()->check() ? auth()->user()->role : 'user';
        $userId   = auth()->check() ? auth()->id() : 1;

        if ($userRole === 'user') {
            try {
                ApprovalRequest::create([
                    'user_id'     => $userId,
                    'target_type' => 'MachinePower',
                    'target_id'   => 0,
                    'target_name' => $data['machine_name'],
                    'action_type' => 'create',
                    'payload'     => json_encode($data),
                ]);
                return redirect()->route('machine-power.index')->with('success', 'Pengajuan penambahan Machine Power telah dikirim.');
            } catch (\Exception $e) {
                return redirect()->route('machine-power.index')->with('warning', 'Sistem Approval belum siap.');
            }
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
            'status'       => 'required|in:Standby,Running,Maintenance,Idle',
        ]);

        $data = $request->except(['_token', '_method']);

        $userRole = auth()->check() ? auth()->user()->role : 'user';
        $userId   = auth()->check() ? auth()->id() : 1;

        if ($userRole === 'user') {
            try {
                ApprovalRequest::create([
                    'user_id'     => $userId,
                    'target_type' => 'MachinePower',
                    'target_id'   => $machinePower->id,
                    'target_name' => $data['machine_name'],
                    'action_type' => 'update',
                    'payload'     => json_encode($data),
                ]);
                return redirect()->route('machine-power.index')->with('success', 'Pengajuan pembaruan data mesin dikirim.');
            } catch (\Exception $e) {
                return redirect()->route('machine-power.index')->with('warning', 'Sistem Approval belum siap.');
            }
        }

        $machinePower->update($data);
        return redirect()->route('machine-power.index')->with('success', 'Data Machine Power berhasil diperbarui.');
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $machine = MachinePower::findOrFail($id);
            $newStatus = $request->input('status', 'Standby');
            
            $machine->status = $newStatus;

            if ($request->has('man_power_id')) {
                $machine->man_power_id = $request->input('man_power_id');
            }

            if ($request->has('start_time')) {
                $machine->start_time = $request->input('start_time');
            }

            // Otomatis pasangkan SPK yang sedang ready jika ada
            $latestSpk = ProductionOrder::whereIn('status', ['ready', 'Proses Produksi Berjalan'])->first();
            if ($latestSpk) {
                $machine->production_order_id = $latestSpk->id;
            }

            $machine->save();

            $normalizedStatus = strtolower(trim($newStatus));
            if (in_array($normalizedStatus, ['standby', 'idle', 'maintenance', 'breakdown'])) {
                if ($machine->man_power_id) {
                    $operator = ManPower::find($machine->man_power_id);
                    if ($operator) {
                        $operator->update(['status' => 'Idle']);
                    }
                }

                $machine->update([
                    'man_power_id'        => null,
                    'start_time'          => null,
                    'production_order_id' => null
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Status mesin berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status mesin: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, MachinePower $machinePower)
    {
        $userRole = auth()->check() ? auth()->user()->role : 'user';
        $userId   = auth()->check() ? auth()->id() : 1;

        if ($userRole === 'super_admin') {
            $machinePower->delete();
            $msg = 'Data mesin berhasil dihapus permanen.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return redirect()->route('machine-power.index')->with('success', $msg);
        }

        try {
            ApprovalRequest::create([
                'user_id'     => $userId,
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
        } catch (\Exception $e) {
            $msg = 'Sistem Approval belum siap.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 500);
            }
            return redirect()->route('machine-power.index')->with('warning', $msg);
        }
    }
}