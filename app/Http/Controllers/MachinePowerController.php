<?php

namespace App\Http\Controllers;

use App\Models\MachinePower;
use App\Models\ManPower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MachinePowerController extends Controller
{
    public function index()
    {
        // Sinkronisasi otomatis: Jika operator di-return di halaman Man Power, lepaskan mesinnya kembali ke Standby
        $runningMachines = MachinePower::with('operator')->where('status', 'Running')->get();
        foreach ($runningMachines as $machine) {
            if ($machine->operator && strtolower($machine->operator->status) !== 'kerja') {
                $machine->update([
                    'status' => 'Standby',
                    'man_power_id' => null
                ]);
            }
        }

        $machinePowers = MachinePower::with('operator')->get();
        
        // Ambil operator yang statusnya 'Kerja' tapi belum memegang mesin apa pun
        $waitingOperators = ManPower::where('status', 'Kerja')->get()->filter(function($worker) {
            return MachinePower::where('man_power_id', $worker->id)->count() === 0;
        });

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
            'location'     => 'required|string|max:255',
            'capacity'     => 'required|string|max:255',
            'status'       => 'required|in:Running,Breakdown,Standby',
            'foto'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except(['_token']);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('machine-power-photos', 'public');
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
            'location'     => 'required|string|max:255',
            'capacity'     => 'required|string|max:255',
            'status'       => 'required|in:Running,Breakdown,Standby',
            'foto'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except(['_token', '_method']);

        if ($request->hasFile('foto')) {
            if ($machinePower->foto && Storage::disk('public')->exists($machinePower->foto)) {
                Storage::disk('public')->delete($machinePower->foto);
            }
            $data['foto'] = $request->file('foto')->store('machine-power-photos', 'public');
        }

        $machinePower->update($data);

        return redirect()->route('machine-power.index')->with('success', 'Data Machine Power berhasil diperbarui.');
    }

    public function destroy(MachinePower $machinePower)
    {
        if ($machinePower->foto && Storage::disk('public')->exists($machinePower->foto)) {
            Storage::disk('public')->delete($machinePower->foto);
        }

        $machinePower->delete();

        return redirect()->route('machine-power.index')->with('success', 'Data Machine Power berhasil dihapus.');
    }

    public function updateStatus(Request $request, $id)
    {
        $machinePower = MachinePower::findOrFail($id);
        
        $status = $request->input('status');
        $manPowerId = $request->input('man_power_id');

        $updateData = [];

        if ($status) {
            $updateData['status'] = $status;
        }

        if ($request->has('man_power_id')) {
            $updateData['man_power_id'] = $manPowerId;
            $updateData['status'] = 'Running';
        }

        if ($status === 'Standby') {
            $updateData['man_power_id'] = null;
        }

        $machinePower->update($updateData);

        return response()->json([
            'success' => true, 
            'message' => 'Status dan alokasi mesin berhasil diperbarui',
            'machine' => $machinePower->load('operator')
        ]);
    }
}