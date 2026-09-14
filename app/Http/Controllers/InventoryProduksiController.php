<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\ItemRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryProduksiController extends Controller
{
    // ==========================================
    // PRIVATE HELPER: FILTER LAPORAN
    // ==========================================
    private function filterLaporan(Request $request)
    {
        $tanggalMulai   = $request->input('tanggal_mulai', date('Y-m-01'));
        $tanggalSelesai = $request->input('tanggal_selesai', date('Y-m-d'));
        $jenis          = $request->input('jenis');

        $query = ItemRequest::whereIn('type', ['in', 'out'])
            ->whereDate('created_at', '>=', $tanggalMulai)
            ->whereDate('created_at', '<=', $tanggalSelesai);

        if ($jenis === 'masuk') {
            $query->where('type', 'in');
        } elseif ($jenis === 'keluar') {
            $query->where('type', 'out');
        }

        return [
            'query'          => $query,
            'tanggalMulai'   => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
        ];
    }

    // ==========================================
    // 1. DASHBOARD & HALAMAN UTAMA PRODUKSI
    // ==========================================
    public function index(Request $request)
    {
        $search = $request->input('search');
        $barangs = Item::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('item_code', 'like', "%{$search}%");
        })->get();

        return view('inventory.produksi.index', compact('barangs'));
    }

    public function master()
    {
        $barangs   = Item::all();
        $suppliers = Supplier::all();
        return view('inventory.produksi.master', compact('barangs', 'suppliers'));
    }

    public function barangMasuk()
    {
        $suppliers = Supplier::all();
        return view('inventory.produksi.barang_masuk', compact('suppliers'));
    }

    public function createBarangMasuk()
    {
        $suppliers = Supplier::all();
        return view('inventory.produksi.create_barang_masuk', compact('suppliers'));
    }

    public function barangKeluar()
    {
        $barangs = Item::all();
        return view('inventory.produksi.barang_keluar', compact('barangs'));
    }

    public function createBarangKeluar()
    {
        $barangs = Item::all();
        return view('inventory.produksi.create_barang_keluar', compact('barangs'));
    }

    public function laporan(Request $request)
    {
        $filtered = $this->filterLaporan($request);
        $laporans = $filtered['query']->orderBy('created_at', 'desc')->get();
        $barangs  = Item::all();

        return view('inventory.produksi.laporan', compact('laporans', 'barangs'));
    }

    // ==========================================
    // 2. EXPORT LAPORAN (REAL DOWNLOAD)
    // ==========================================
    public function exportPdf(Request $request)
    {
        $filtered       = $this->filterLaporan($request);
        $laporans       = $filtered['query']->orderBy('created_at', 'desc')->get();
        $tanggalMulai   = $filtered['tanggalMulai'];
        $tanggalSelesai = $filtered['tanggalSelesai'];
        
        $pdf = Pdf::loadView('inventory.produksi.pdf_laporan', compact('laporans', 'tanggalMulai', 'tanggalSelesai'));
        return $pdf->download('Laporan_Inventory_' . date('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $filtered = $this->filterLaporan($request);
        $laporans = $filtered['query']->orderBy('created_at', 'desc')->get();
        $fileName = "Laporan_Inventory_" . date('Y-m-d') . ".xls";

        $html = '<table border="1">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Kode Transaksi</th>
                            <th>Nama Barang</th>
                            <th>Qty</th>
                            <th>Satuan</th>
                            <th>Jenis</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        foreach ($laporans as $index => $row) {
            $html .= '<tr>
                        <td>' . ($index + 1) . '</td>
                        <td>' . $row->created_at . '</td>
                        <td>' . ($row->request_code ?? '-') . '</td>
                        <td>' . $row->nama_barang . '</td>
                        <td>' . $row->qty . '</td>
                        <td>' . $row->satuan . '</td>
                        <td>' . strtoupper($row->type) . '</td>
                        <td>' . ($row->notes ?? '-') . '</td>
                    </tr>';
        }
        
        $html .= '</tbody></table>';

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function exportWord(Request $request)
    {
        $filtered       = $this->filterLaporan($request);
        $laporans       = $filtered['query']->orderBy('created_at', 'desc')->get();
        $tanggalMulai   = $filtered['tanggalMulai'];
        $tanggalSelesai = $filtered['tanggalSelesai'];
        $fileName       = "Laporan_Inventory_" . date('Y-m-d') . ".doc";

        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
                <head><title>Laporan Inventory</title></head>
                <body>
                    <h2 style="text-align: center;">Laporan Transaksi Inventory Produksi</h2>
                    <p>Periode: ' . $tanggalMulai . ' s/d ' . $tanggalSelesai . '</p>
                    <table border="1" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f2f2f2;">
                                <th style="padding: 5px;">No</th>
                                <th style="padding: 5px;">Tanggal</th>
                                <th style="padding: 5px;">Kode Transaksi</th>
                                <th style="padding: 5px;">Nama Barang</th>
                                <th style="padding: 5px;">Qty</th>
                                <th style="padding: 5px;">Satuan</th>
                                <th style="padding: 5px;">Jenis</th>
                                <th style="padding: 5px;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>';
        
        foreach ($laporans as $index => $row) {
            $html .= '<tr>
                        <td style="padding: 5px; text-align: center;">' . ($index + 1) . '</td>
                        <td style="padding: 5px;">' . $row->created_at . '</td>
                        <td style="padding: 5px;">' . ($row->request_code ?? '-') . '</td>
                        <td style="padding: 5px;">' . $row->nama_barang . '</td>
                        <td style="padding: 5px; text-align: center;">' . $row->qty . '</td>
                        <td style="padding: 5px; text-align: center;">' . $row->satuan . '</td>
                        <td style="padding: 5px; text-align: center;">' . strtoupper($row->type) . '</td>
                        <td style="padding: 5px;">' . ($row->notes ?? '-') . '</td>
                    </tr>';
        }
        
        $html .= '</tbody></table></body></html>';

        return response($html)
            ->header('Content-Type', 'application/msword')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    // ==========================================
    // 3. MASTER BARANG & SUPPLIER (CRUD)
    // ==========================================
    public function storeBarang(Request $request)
    {
        $request->validate([
            'item_code'     => 'nullable|string|max:50',
            'name'          => 'required|string|max:255',
            'category'      => 'required|string|max:100',
            'unit'          => 'required|string|max:50',
            'current_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'nullable|numeric|min:0',
            'location'      => 'nullable|string|max:100',
            'price'         => 'nullable|numeric|min:0',
            'supplier_main' => 'nullable|string|max:255',
            'status'        => 'nullable|string|max:50',
        ]);

        $user = Auth::user();
        $role = $user ? $user->role : 'user';

        if ($role === 'user') {
            ItemRequest::create([
                'request_code' => 'REQ-BARANG-' . strtoupper(uniqid()),
                'user_id'      => $user->id,
                'department'   => 'Produksi / Gudang',
                'nama_barang'  => $request->name,
                'qty'          => $request->current_stock,
                'satuan'       => $request->unit,
                'harga'        => $request->price ?? 0,
                'type'         => 'create_barang',
                'status'       => 'Pending Admin',
                'notes'        => 'Pengajuan master barang baru oleh User: ' . $user->name
            ]);

            return redirect('/inventory/produksi/master')->with('success', 'Pengajuan data barang berhasil dikirim dan menunggu approval Admin.');
        }

        Item::create([
            'item_code' => $request->item_code ?? ('BRG-' . strtoupper(uniqid())),
            'name'      => $request->name,
            'category'  => $request->category,
            'unit'      => $request->unit,
            'stok'      => $request->current_stock,
            'stok_min'  => $request->minimum_stock,
            'lokasi'    => $request->location,
            'harga'     => $request->price ?? 0,
            'supplier'  => $request->supplier_main,
            'status'    => $request->status ?? 'Aktif',
        ]);

        return redirect('/inventory/produksi/master')->with('success', 'Data barang master berhasil ditambahkan.');
    }

    public function updateBarang(Request $request, $id)
    {
        $request->validate([
            'item_code'     => 'nullable|string|max:50',
            'name'          => 'required|string|max:255',
            'category'      => 'required|string|max:100',
            'unit'          => 'required|string|max:50',
            'stok'          => 'required|numeric|min:0',
            'minimum_stock' => 'nullable|numeric|min:0',
            'location'      => 'nullable|string|max:100',
            'harga'         => 'nullable|numeric|min:0',
            'supplier_main' => 'nullable|string|max:255',
            'status'        => 'nullable|string|max:50',
        ]);

        $item = Item::findOrFail($id);

        $item->update([
            'item_code' => $request->item_code,
            'name'      => $request->name,
            'category'  => $request->category,
            'unit'      => $request->unit,
            'stok'      => $request->stok,
            'stok_min'  => $request->minimum_stock,
            'lokasi'    => $request->location,
            'harga'     => $request->harga,
            'supplier'  => $request->supplier_main,
            'status'    => $request->status,
        ]);

        return redirect('/inventory/produksi/master')->with('success', 'Data barang master berhasil diperbarui.');
    }

    public function destroyBarang($id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($id);
        $role = $user ? $user->role : 'user';

        if ($role === 'super-admin') {
            $item->delete();
            return redirect('/inventory/produksi/master')->with('success', 'Data barang berhasil dihapus permanen oleh Super Admin.');
        } 
        
        if ($role === 'admin') {
            ItemRequest::create([
                'request_code' => 'REQ-DEL-' . strtoupper(uniqid()),
                'user_id'      => $user->id,
                'department'   => 'Produksi / Gudang',
                'item_id'      => $item->id,
                'nama_barang'  => $item->name,
                'qty'          => 0,
                'satuan'       => $item->unit,
                'harga'        => $item->harga,
                'type'         => 'del', 
                'status'       => 'Pending Super Admin',
                'notes'        => 'Permintaan hapus barang (ID: ' . $item->id . ') oleh Admin: ' . $user->name
            ]);

            return redirect('/inventory/produksi/master')->with('success', 'Permintaan penghapusan data telah dikirim ke Super Admin untuk disetujui.');
        }

        return redirect('/inventory/produksi/master')->with('error', 'Akses ditolak.');
    }

    public function storeSupplier(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Supplier::create($request->all());
        return redirect('/inventory/produksi/master')->with('success', 'Supplier baru berhasil ditambahkan.');
    }

    public function updateSupplier(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->update($request->all());

        return redirect('/inventory/produksi/master')->with('success', 'Data supplier berhasil diperbarui.');
    }

    public function destroySupplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        return redirect('/inventory/produksi/master')->with('success', 'Supplier berhasil dihapus.');
    }

    // ==========================================
    // 4. TRANSAKSI BARANG MASUK & KELUAR
    // ==========================================
    public function storeBarangMasuk(Request $request)
    {
        $request->validate([
            'tanggal'             => 'required|date',
            'supplier_id'         => 'required',
            'no_surat_jalan'      => 'required|string|max:255',
            'keterangan'          => 'nullable|string|max:500',
            'detail'              => 'required|array|min:1',
            'detail.*.nama_barang'=> 'required|string|max:255',
            'detail.*.qty'        => 'required|numeric|min:1',
            'detail.*.satuan'     => 'required|string|max:50',
            'detail.*.harga'      => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $user     = Auth::user();
            $supplier = Supplier::find($request->supplier_id);
            $namaSupplier = $supplier ? $supplier->name : 'Supplier ID: ' . $request->supplier_id;

            foreach ($request->detail as $itemDetail) {
                $barang = Item::where('name', $itemDetail['nama_barang'])->first();

                if ($barang) {
                    $barang->stok += $itemDetail['qty'];
                    $barang->supplier = $namaSupplier;
                    $barang->save();
                } else {
                    $barang = Item::create([
                        'item_code' => 'BRG-' . strtoupper(uniqid()),
                        'name'      => $itemDetail['nama_barang'],
                        'category'  => 'Umum',
                        'unit'      => $itemDetail['satuan'],
                        'stok'      => $itemDetail['qty'],
                        'harga'     => $itemDetail['harga'],
                        'supplier'  => $namaSupplier,
                        'status'    => 'Aktif'
                    ]);
                }

                ItemRequest::create([
                    'request_code' => 'REQ-IN-' . strtoupper(uniqid()),
                    'user_id'      => $user ? $user->id : null,
                    'department'   => 'Produksi / Gudang',
                    'item_id'      => $barang->id,
                    'nama_barang'  => $itemDetail['nama_barang'],
                    'qty'          => $itemDetail['qty'],
                    'satuan'       => $itemDetail['satuan'],
                    'harga'        => $itemDetail['harga'],
                    'type'         => 'in',
                    'status'       => 'Approved by Admin',
                    'notes'        => 'No. Surat Jalan: ' . $request->no_surat_jalan . ' | ' . $request->keterangan
                ]);
            }

            DB::commit();
            return redirect('/inventory/produksi/barang-masuk')->with('success', 'Barang masuk berhasil disimpan, stok bertambah, dan tercatat di laporan!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Gagal simpan barang masuk: ' . $e->getMessage());
            return redirect('/inventory/produksi/create-barang-masuk')->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function storeBarangKeluar(Request $request)
    {
        $request->validate([
            'tanggal'              => 'required|date',
            'tujuan'               => 'required|string|max:255',
            'detail'               => 'required|array|min:1',
            'detail.*.item_id'     => 'nullable|exists:items,id',
            'detail.*.nama_barang' => 'nullable|string',
            'detail.*.qty'         => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();

            foreach ($request->detail as $detail) {
                $item = null;
                if (!empty($detail['item_id'])) {
                    $item = Item::find($detail['item_id']);
                } elseif (!empty($detail['nama_barang'])) {
                    $item = Item::where('name', $detail['nama_barang'])->first();
                }

                if (!$item) {
                    throw new \Exception('Barang tidak ditemukan di database.');
                }

                ItemRequest::create([
                    'request_code' => 'REQ-OUT-' . strtoupper(uniqid()),
                    'user_id'      => $user ? $user->id : null,
                    'department'   => 'Produksi / Gudang',
                    'item_id'      => $item->id,
                    'nama_barang'  => $item->name,
                    'qty'          => $detail['qty'],
                    'satuan'       => $item->unit,
                    'harga'        => $item->harga,
                    'type'         => 'out',
                    'status'       => 'Pending Admin',
                    'notes'        => 'Barang keluar tujuan: ' . $request->tujuan
                ]);
            }

            DB::commit();
            return redirect('/inventory/produksi/barang-keluar')->with('success', 'Barang keluar berhasil dicatat dan menunggu approval.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect('/inventory/produksi/create-barang-keluar')->withInput()->with('error', $e->getMessage());
        }
    }

    // ==========================================
    // 5. PERMINTAAN & APPROVAL BARANG
    // ==========================================
    public function indexRequest()
    {
        $requests = ItemRequest::orderBy('created_at', 'desc')->get();
        $barangs  = Item::all();
        
        return view('inventory.produksi.requests', compact('requests', 'barangs'));
    }

    public function storeRequest(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'qty'         => 'required|numeric|min:1',
            'satuan'      => 'required|string|max:50',
            'department'  => 'required|string|max:100',
            'notes'       => 'nullable|string'
        ]);

        $user = Auth::user();

        ItemRequest::create([
            'request_code' => 'REQ-GENERAL-' . strtoupper(uniqid()),
            'user_id'      => $user ? $user->id : null,
            'department'   => $request->department,
            'nama_barang'  => $request->nama_barang,
            'qty'          => $request->qty,
            'satuan'       => $request->satuan,
            'harga'        => 0,
            'type'         => 'general_request',
            'status'       => 'Pending Admin',
            'notes'        => $request->notes ?? 'Permintaan barang baru'
        ]);

        return redirect('/inventory/produksi/requests')->with('success', 'Request berhasil dikirim ke Admin untuk disetujui.');
    }

    public function approveRequest($id)
    {
        $user        = Auth::user();
        $itemRequest = ItemRequest::findOrFail($id);
        $role        = $user ? $user->role : 'user';

        if ($role === 'user') {
            return redirect('/inventory/produksi/requests')->with('error', 'Akses ditolak.');
        }

        DB::beginTransaction();

        try {
            if ($role === 'admin') {
                if ($itemRequest->status !== 'Pending Admin') {
                    DB::rollBack();
                    return redirect('/inventory/produksi/requests')->with('error', 'Permintaan ini sudah diproses.');
                }
                
                $itemRequest->update(['status' => 'Approved by Admin']);
                
                if (in_array($itemRequest->type, ['in', 'create_barang', 'general_request'])) {
                    $barang = null;
                    if ($itemRequest->item_id) {
                        $barang = Item::find($itemRequest->item_id);
                    }

                    if (!$barang) {
                        $barang = Item::firstOrCreate(
                            ['name' => $itemRequest->nama_barang],
                            [
                                'item_code' => 'BRG-' . rand(1000, 9999),
                                'category'  => 'Umum',
                                'unit'      => $itemRequest->satuan,
                                'stok'      => 0,
                                'harga'     => $itemRequest->harga ?? 0,
                                'status'    => 'Aktif'
                            ]
                        );
                    }

                    $barang->stok += $itemRequest->qty;
                    $barang->save();
                } 
                elseif ($itemRequest->type == 'out') {
                    $barang = $itemRequest->item_id 
                        ? Item::find($itemRequest->item_id) 
                        : Item::where('name', $itemRequest->nama_barang)->first();

                    if ($barang) {
                        if ($barang->stok < $itemRequest->qty) {
                            DB::rollBack();
                            return redirect('/inventory/produksi/requests')->with('error', 'Stok barang tidak mencukupi!');
                        }
                        $barang->stok -= $itemRequest->qty;
                        $barang->save();
                    }
                }

                DB::commit();
                return redirect('/inventory/produksi/requests')->with('success', 'Permintaan berhasil disetujui.');
            }

            if ($role === 'super-admin') {
                if ($itemRequest->type === 'del') {
                    $itemRequest->update(['status' => 'Approved by Super Admin']);
                    
                    if ($itemRequest->item_id) {
                        Item::where('id', $itemRequest->item_id)->delete();
                    } else {
                        Item::where('name', $itemRequest->nama_barang)->delete();
                    }

                    DB::commit();
                    return redirect('/inventory/produksi/requests')->with('success', 'Barang berhasil dihapus.');
                }

                $itemRequest->update(['status' => 'Approved by Super Admin']);
                DB::commit();
                return redirect('/inventory/produksi/requests')->with('success', 'Permintaan disetujui Super Admin.');
            }

            DB::rollBack();
            return redirect('/inventory/produksi/requests');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving request: ' . $e->getMessage());
            return redirect('/inventory/produksi/requests')->with('error', 'Terjadi kesalahan sistem saat approve.');
        }
    }

    public function rejectRequest($id)
    {
        $user        = Auth::user();
        $itemRequest = ItemRequest::findOrFail($id);
        $role        = $user ? $user->role : 'user';

        if (in_array($role, ['admin', 'super-admin'])) {
            $itemRequest->update(['status' => 'Rejected']);
            return redirect('/inventory/produksi/requests')->with('success', 'Permintaan berhasil ditolak.');
        }

        return redirect('/inventory/produksi/requests')->with('error', 'Akses ditolak.');
    }

    public function destroyRequest($id)
    {
        $user = Auth::user();
        $role = $user ? $user->role : 'user';

        if ($role === 'super-admin') {
            $itemRequest = ItemRequest::findOrFail($id);
            $itemRequest->delete();
            return redirect('/inventory/produksi/requests')->with('success', 'Data permintaan berhasil dihapus.');
        }

        return redirect('/inventory/produksi/requests')->with('error', 'Akses ditolak.');
    }

    public function simulasiLogin($role)
    {
        if (!app()->environment('local')) {
            abort(404);
        }

        $user = \App\Models\User::where('role', $role)->first();

        if ($user) {
            \Illuminate\Support\Facades\Auth::login($user);
        }

        return redirect('/inventory/produksi/master')->with('success', 'Berhasil login sebagai ' . $role);
    }
}