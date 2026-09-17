<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryOrder;
use App\Models\Purchase;
use Carbon\Carbon;

class DeliveryOrderController extends Controller
{
    public function index()
    {
        $deliveries = DeliveryOrder::with('purchase')->latest()->get();
        $purchases = Purchase::where('status', '!=', 'Cancelled')->get();

        return view('delivery', compact('deliveries', 'purchases'));
    }

    public function store(Request $request)
    {
        if (!in_array(auth()->user()->role ?? 'user', ['admin', 'superadmin'])) {
            return back()->with('error', 'Akses Ditolak! Hanya Admin yang dapat menambah Delivery Order.');
        }

        $request->validate([
            'ref_po'        => 'required',
            'tanggal_kirim' => 'required|date',
            'driver'        => 'nullable|string',
            'keterangan'    => 'nullable|string',
        ]);

        $currentMonth = Carbon::now()->format('Ym');
        $lastDo = DeliveryOrder::where('no_do', 'LIKE', 'DO-' . $currentMonth . '-%')
                               ->orderBy('no_do', 'desc')
                               ->first();

        $newNumber = $lastDo ? ((int) substr($lastDo->no_do, -3)) + 1 : 1;
        $noDo = 'DO-' . $currentMonth . '-' . sprintf('%03d', $newNumber);

        $po = Purchase::where('no_po', $request->ref_po)->first();

        $status = 'On Progress';
        if ($po && $po->waktu_tgl_deadline) {
            $deadline = Carbon::parse($po->waktu_tgl_deadline)->format('Y-m-d');
            if ($request->tanggal_kirim > $deadline) {
                $status = 'Delayed'; 
            }
        }

        DeliveryOrder::create([
            'no_do'             => $noDo,
            'ref_po'            => $request->ref_po,
            'tanggal_kirim'     => $request->tanggal_kirim,
            'driver'            => $request->driver ?? '-',
            'status'            => $request->status ?? $status,
            'status_pengiriman' => $request->status ?? $status,
            'status_approval'   => 'Pending',
            'keterangan'        => $request->keterangan,
        ]);

        return redirect('/delivery')->with('success', 'Delivery Order berhasil dibuat!');
    }

    public function show($id)
    {
        $delivery = DeliveryOrder::with('purchase')->findOrFail($id);

        return view('delivery-detail', compact('delivery'));
    }

    public function confirmDelivery(Request $request, $id)
    {
        $request->validate([
            'tanggal_kirim'     => 'required',
            'status_pengiriman' => 'required',
        ]);

        $delivery = DeliveryOrder::findOrFail($id);

        // Meng-update status_pengiriman & status agar sinkron
        $delivery->update([
            'tanggal_kirim'     => $request->tanggal_kirim,
            'driver'            => $request->driver,
            'status_pengiriman' => $request->status_pengiriman,
            'status'            => $request->status_pengiriman,
        ]);

        return redirect()->back()->with('success', 'Jadwal pengiriman berhasil dikonfirmasi!');
    }

    public function approval(Request $request, $id)
    {
        $request->validate([
            'status_approval' => 'required',
        ]);

        $delivery = DeliveryOrder::findOrFail($id);

        // Meng-update kolom status_approval & catatan_approval yang dibaca Blade
        $delivery->update([
            'status_approval'  => $request->status_approval,
            'catatan_approval' => $request->catatan_approval,
            'status'           => $request->status_approval, // Fallback
            'keterangan'       => $request->catatan_approval,
        ]);

        return redirect()->back()->with('success', 'Approval delivery berhasil diperbarui!');
    }

    // Fungsi Hapus Data Khusus Superadmin
    public function destroy($id)
    {
        if (auth()->user()->role !== 'superadmin') {
            return back()->with('error', 'Akses ditolak! Hanya Super Admin yang bisa menghapus data.');
        }

        $delivery = DeliveryOrder::findOrFail($id);
        $delivery->delete();

        return redirect()->back()->with('success', 'Data Delivery Order berhasil dihapus!');
    }
}