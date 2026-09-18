<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\ItemRequest;
use App\Models\User;
use App\Models\ProductionOrder;
use App\Models\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryProduksiController extends Controller
{
    // ==========================================
    // HELPER: NORMALISASI ROLE & FILTER
    // ==========================================
    private function getNormalizedRole($user)
    {
        if (!$user || empty($user->role)) return 'user';
        return strtolower(str_replace([' ', '-'], '_', trim($user->role)));
    }

    private function filterLaporan(Request $request)
    {
        $tanggalMulai   = $request->input('tanggal_mulai', date('Y-m-01'));
        $tanggalSelesai = $request->input('tanggal_selesai', date('Y-m-d'));
        $jenis          = $request->input('jenis');

        $query = ItemRequest::with(['item', 'user', 'productionOrder', 'resource'])
            ->whereIn('type', ['in', 'out'])
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
        $barangs = Item::all();
        $suppliers = Supplier::where('status', 'Aktif')->get();

        return view('inventory.produksi.master', compact('barangs', 'suppliers'));
    }

    public function barangMasuk()
    {
        $barangs   = Item::all();
        $suppliers = Supplier::all();
        return view('inventory.produksi.barang_masuk', compact('barangs', 'suppliers'));
    }

    public function createBarangMasuk()
    {
        $barangs   = Item::all();
        $suppliers = Supplier::all();
        return view('inventory.produksi.barang_masuk', compact('barangs', 'suppliers'));
    }

    public function barangKeluar()
    {
        $barangs          = Item::all();
        $productionOrders = ProductionOrder::all();
        $resources        = Resource::all();
        return view('inventory.produksi.barang_keluar', compact('barangs', 'productionOrders', 'resources'));
    }

    public function createBarangKeluar()
    {
        $barangs          = Item::all();
        $productionOrders = ProductionOrder::all();
        $resources        = Resource::all();
        return view('inventory.produksi.create_barang_keluar', compact('barangs', 'productionOrders', 'resources'));
    }

    public function laporan(Request $request)
    {
        $filtered = $this->filterLaporan($request);
        $laporans = $filtered['query']->orderBy('created_at', 'desc')->get();
        $barangs  = Item::all();

        return view('inventory.produksi.laporan', compact('laporans', 'barangs'));
    }

    // ==========================================
    // 2. EXPORT LAPORAN
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
                            <th>SPK Produksi</th>
                            <th>Resource</th>
                            <th>Nama Barang</th>
                            <th>Qty</th>
                            <th>Satuan</th>
                            <th>Jenis</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        foreach ($laporans as $index => $row) {
            $namaBarang   = $row->item ? $row->item->name : ($row->nama_barang ?? '-');
            $satuan       = $row->item ? $row->item->unit : ($row->satuan ?? '-');
            $spkCode      = $row->productionOrder ? ($row->productionOrder->code ?? $row->productionOrder->request_code ?? '-') : '-';
            $resourceName = $row->resource ? ($row->resource->name ?? '-') : '-';

            $html .= '<tr>
                        <td>' . ($index + 1) . '</td>
                        <td>' . $row->created_at . '</td>
                        <td>' . ($row->request_code ?? '-') . '</td>
                        <td>' . e($spkCode) . '</td>
                        <td>' . e($resourceName) . '</td>
                        <td>' . e($namaBarang) . '</td>
                        <td>' . $row->qty . '</td>
                        <td>' . e($satuan) . '</td>
                        <td>' . strtoupper($row->type) . '</td>
                        <td>' . e($row->notes ?? '-') . '</td>
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
                                <th style="padding: 5px;">SPK Produksi</th>
                                <th style="padding: 5px;">Resource</th>
                                <th style="padding: 5px;">Nama Barang</th>
                                <th style="padding: 5px;">Qty</th>
                                <th style="padding: 5px;">Satuan</th>
                                <th style="padding: 5px;">Jenis</th>
                                <th style="padding: 5px;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>';
        
        foreach ($laporans as $index => $row) {
            $namaBarang   = $row->item ? $row->item->name : ($row->nama_barang ?? '-');
            $satuan       = $row->item ? $row->item->unit : ($row->satuan ?? '-');
            $spkCode      = $row->productionOrder ? ($row->productionOrder->code ?? $row->productionOrder->request_code ?? '-') : '-';
            $resourceName = $row->resource ? ($row->resource->name ?? '-') : '-';

            $html .= '<tr>
                        <td style="padding: 5px; text-align: center;">' . ($index + 1) . '</td>
                        <td style="padding: 5px;">' . $row->created_at . '</td>
                        <td style="padding: 5px;">' . ($row->request_code ?? '-') . '</td>
                        <td style="padding: 5px;">' . e($spkCode) . '</td>
                        <td style="padding: 5px;">' . e($resourceName) . '</td>
                        <td style="padding: 5px;">' . e($namaBarang) . '</td>
                        <td style="padding: 5px; text-align: center;">' . $row->qty . '</td>
                        <td style="padding: 5px; text-align: center;">' . e($satuan) . '</td>
                        <td style="padding: 5px; text-align: center;">' . strtoupper($row->type) . '</td>
                        <td style="padding: 5px;">' . e($row->notes ?? '-') . '</td>
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
        $itemCode = $request->input('item_code') ?? $request->input('code') ?? $request->input('kode_barang');
        $name     = $request->input('name') ?? $request->input('nama_barang');
        $category = $request->input('category') ?? $request->input('kategori');
        $unit     = $request->input('unit') ?? $request->input('satuan');
        
        if (!$itemCode && !$name && $request->has('supplier')) {
            return $this->storeBarangMasukTransaksi($request);
        }

        if (!$itemCode || !$name || !$category || !$unit) {
            return redirect()->back()->with('error', 'Kode, Nama, Kategori, dan Satuan barang wajib diisi!')->withInput();
        }

        try {
            Item::create([
                'item_code' => $itemCode,
                'name'      => $name,
                'category'  => $category,
                'unit'      => $unit,
                'stok'      => $request->input('current_stock') ?? $request->input('stok') ?? 0,
                'stok_min'  => $request->input('minimum_stock') ?? $request->input('stok_min') ?? 5,
                'lokasi'    => $request->input('location') ?? $request->input('lokasi_rak') ?? $request->input('lokasi'),
                'supplier'  => $request->input('supplier_id') ?? $request->input('supplier_main') ?? $request->input('supplier_utama') ?? $request->input('supplier'),
                'harga'     => $request->input('price') ?? $request->input('harga') ?? 0,
                'status'    => $request->input('status') ?? 'Aktif',
            ]);

            return redirect()->back()->with('success', 'Data barang berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Kesalahan pada storeBarang: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan data barang: ' . $e->getMessage())->withInput();
        }
    }

    public function updateBarang(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $request->validate([
            'item_code' => 'required|string|max:255|unique:items,item_code,' . $id,
            'name'      => 'required|string|max:255',
            'category'  => 'required|string|max:255',
            'unit'      => 'required|string|max:50',
            'stok'      => 'required|numeric|min:0',
        ]);

        try {
            $rawPrice = $request->input('price', 0);
            $cleanPrice = preg_replace('/[^\d]/', '', $rawPrice);

            $item->update([
                'item_code' => $request->input('item_code'),
                'name'      => $request->input('name'),
                'category'  => $request->input('category'),
                'unit'      => $request->input('unit'),
                'stok'      => $request->input('stok'),
                'stok_min'  => $request->input('minimum_stock', 5),
                'lokasi'    => $request->input('location'),
                'supplier'  => $request->input('supplier_id') ?? $request->input('supplier_main'),
                'harga'     => $cleanPrice !== '' ? $cleanPrice : 0,
                'status'    => $request->input('status', 'Aktif'),
            ]);

            return redirect()->back()->with('success', 'Data barang berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Kesalahan pada updateBarang: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui data barang: ' . $e->getMessage())->withInput();
        }
    }

    public function storeBarangMasuk(Request $request)
    {
        DB::beginTransaction();
        try {
            $supplierId = $request->input('supplier_id');
            $supplier   = $supplierId ?? $request->input('supplier');
            $noSurat    = $request->input('no_surat_jalan');
            $catatan    = $request->input('keterangan');
            
            $items = [];

            if ($request->has('items') && is_array($request->input('items'))) {
                foreach ($request->input('items') as $row) {
                    $idB = $row['item_id'] ?? $row['barang_id'] ?? null;
                    if (!empty($idB)) {
                        $items[] = [
                            'item_id' => $idB,
                            'qty'     => $row['qty'] ?? 1,
                            'notes'   => $row['notes'] ?? $catatan
                        ];
                    }
                }
            }

            if (empty($items)) {
                $itemIds = $request->input('item_id') ?? $request->input('barang_id') ?? [];
                $qtys    = $request->input('qty') ?? [];

                if (!is_array($itemIds)) $itemIds = [$itemIds];
                if (!is_array($qtys)) $qtys = [$qtys];

                foreach ($itemIds as $index => $idBarang) {
                    if (!empty($idBarang)) {
                        $items[] = [
                            'item_id' => $idBarang,
                            'qty'     => $qtys[$index] ?? 1,
                            'notes'   => $catatan
                        ];
                    }
                }
            }

            if (empty($items)) {
                return redirect()->back()->with('error', 'Silakan pilih nama barang terlebih dahulu!')->withInput();
            }

            $user = Auth::user();
            $role = $this->getNormalizedRole($user);
            $statusApproval = ($role === 'super_admin') ? 'Approved by Super Admin' : 'Approved by Admin';
            $mainRequestCode = 'TRX-IN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            foreach ($items as $row) {
                $barangId = $row['item_id'];
                $jumlah   = $row['qty'] ?? 1;
                
                $barang = Item::find($barangId);
                if (!$barang) continue;

                ItemRequest::create([
                    'request_code'        => $mainRequestCode,
                    'user_id'             => $user ? $user->id : null,
                    'supplier_id'         => $supplierId,
                    'production_order_id' => $request->input('production_order_id'),
                    'resource_id'         => $request->input('resource_id'),
                    'department'          => 'Gudang / Produksi',
                    'item_id'             => $barang->id,
                    'qty'                 => $jumlah,
                    'type'                => 'in',
                    'status'              => $statusApproval,
                    'notes'               => (!empty($noSurat) ? "No. Surat Jalan: {$noSurat}. " : "") . ($row['notes'] ?? 'Barang Masuk'),
                ]);

                $barang->stok += $jumlah;
                if (!empty($supplier)) {
                    $barang->supplier = $supplier;
                }
                $barang->save();
            }

            DB::commit();
            return redirect('/inventory/produksi/laporan')->with('success', 'Transaksi barang masuk berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan pada storeBarangMasuk: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    private function storeBarangMasukTransaksi(Request $request)
    {
        return $this->storeBarangMasuk($request);
    }

    public function destroyBarang($id)
    {
        $user = Auth::user();
        $role = $this->getNormalizedRole($user);
        $item = Item::findOrFail($id);

        if ($role === 'admin') {
            ItemRequest::create([
                'request_code' => 'REQ-DEL-' . strtoupper(uniqid()),
                'user_id'      => $user->id,
                'department'   => 'Admin Produksi',
                'item_id'      => $item->id,
                'qty'          => 0,
                'type'         => 'del',
                'status'       => 'Pending Super Admin',
                'notes'        => 'Permintaan hapus barang (ID: ' . $id . ') oleh Admin: ' . $user->name,
            ]);

            return redirect()->back()->with('warning', 'Permintaan hapus barang telah dikirim dan menunggu persetujuan Super Admin!');
        }

        if ($role === 'super_admin') {
            $item->delete();
            return redirect()->back()->with('success', 'Barang berhasil dihapus oleh Super Admin.');
        }

        return redirect()->back()->with('error', 'Akses ditolak.');
    }

    // ==========================================
    // 4. SUPPLIER CRUD
    // ==========================================
    public function storeSupplier(Request $request)
    {
        $supplierName    = $request->input('name') ?? $request->input('nama_supplier');
        $supplierCode    = $request->input('code') ?? $request->input('kode_supplier') ?? $request->input('supplier_code');
        $supplierPhone   = $request->input('phone') ?? $request->input('kontak') ?? $request->input('no_telp');
        $supplierAddress = $request->input('address') ?? $request->input('alamat');
        $supplierEmail   = $request->input('email');
        $supplierPic     = $request->input('pic') ?? $request->input('pic_name') ?? $request->input('contact_person');

        if (empty($supplierName)) {
            return redirect()->back()->with('error', 'Nama supplier wajib diisi!')->withInput();
        }

        if (empty($supplierCode)) {
            $nextId = (Supplier::max('id') ?? 0) + 1;
            $supplierCode = 'SUP-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        }

        Supplier::create([
            'supplier_code' => $supplierCode,
            'name'          => $supplierName,
            'alamat'        => $supplierAddress,
            'phone'         => $supplierPhone,
            'email'         => $supplierEmail,
            'pic'           => $supplierPic,
            'status'        => $request->input('status', 'Aktif'),
        ]);

        return redirect('/inventory/produksi/master')->with('success', 'Supplier baru berhasil ditambahkan.');
    }

    public function updateSupplier(Request $request, $id)
    {
        $supplierName    = $request->input('name') ?? $request->input('nama_supplier');
        $supplierCode    = $request->input('code') ?? $request->input('kode_supplier') ?? $request->input('supplier_code');
        $supplierPhone   = $request->input('phone') ?? $request->input('kontak') ?? $request->input('no_telp');
        $supplierAddress = $request->input('address') ?? $request->input('alamat');
        $supplierEmail   = $request->input('email');
        $supplierPic     = $request->input('pic') ?? $request->input('pic_name') ?? $request->input('contact_person');

        if (empty($supplierName)) {
            return redirect()->back()->with('error', 'Nama supplier wajib diisi!')->withInput();
        }

        $supplier = Supplier::findOrFail($id);

        if (empty($supplierCode)) {
            $supplierCode = $supplier->kode_supplier ?? $supplier->code ?? ('SUP-' . str_pad($supplier->id, 3, '0', STR_PAD_LEFT));
        }

        $supplier->update([
        'supplier_code' => $supplierCode,
        'name'          => $supplierName,
        'alamat'        => $supplierAddress,
        'phone'         => $supplierPhone,
        'email'         => $supplierEmail,
        'pic'           => $supplierPic,
        'status'        => $request->input('status') ?? $supplier->status ?? 'Aktif',
    ]);

        return redirect('/inventory/produksi/master')->with('success', 'Data supplier berhasil diperbarui.');
    }

    public function destroySupplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->route('inventory.produksi.index', ['tab' => 'supplier'])
            ->with('success', 'Data supplier berhasil dihapus!');
    }

    // ==========================================
    // BARANG KELUAR (PRODUKSI & RESOURCE LOGIC)
    // ==========================================
 public function storeBarangKeluar(Request $request)
    {
        $details = $request->input('detail');

        if (empty($details) || !is_array($details)) {
            return redirect()->back()->with('error', 'Silakan pilih minimal satu nama barang terlebih dahulu!')->withInput();
        }

        $user = Auth::user();
        $role = $this->getNormalizedRole($user);

        DB::beginTransaction();
        try {
            foreach ($details as $row) {
                $idB        = $row['item_id'] ?? $row['barang_id'] ?? null;
                $namaBarang = $row['nama_barang'] ?? null;
                $qty        = $row['qty'] ?? 1;

                if (empty($idB) && empty($namaBarang)) {
                    continue;
                }

                if ($idB) {
                    $barang = Item::find($idB);
                } else {
                    $barang = Item::where('name', $namaBarang)->orWhere('id', $namaBarang)->first();
                }

                if (!$barang) {
                    $searchKey = $idB ?? $namaBarang;
                    throw new \Exception("Barang '{$searchKey}' tidak ditemukan di master database!");
                }

                if ($barang->stok < $qty) {
                    throw new \Exception("Stok barang '{$barang->name}' tidak mencukupi! Sisa stok: {$barang->stok}");
                }

                $statusApproval = ($role === 'super_admin') ? 'Approved by Super Admin' : (($role === 'admin') ? 'Approved by Admin' : 'Pending Admin');

                ItemRequest::create([
                    'request_code'        => 'REQ-OUT-' . strtoupper(uniqid()),
                    'user_id'             => $user ? $user->id : null,
                    'production_order_id' => $row['production_order_id'] ?? $request->input('production_order_id'),
                    'resource_id'         => $row['resource_id'] ?? $request->input('resource_id'),
                    'department'          => $request->input('department') ?? 'Produksi / Gudang',
                    'item_id'             => $barang->id,
                    'qty'                 => $qty,
                    'type'                => 'out',
                    'status'              => $statusApproval,
                    'notes'               => $request->input('notes') ?? $request->input('keterangan') ?? 'Transaksi Barang Keluar Produksi',
                ]);

                // Langsung kurangi stok tanpa batasan role
                $barang->stok -= $qty;
                $barang->save();
            } // <--- KURUNG KURAWAL INI TADI KURANG SEHINGGA FOREACH TIDAK TERTUTUP

            DB::commit();
            return redirect('/inventory/produksi/laporan')->with('success', 'Transaksi barang keluar berhasil dicatat.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan pada storeBarangKeluar: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    // ==========================================
    // 5. PERMINTAAN & APPROVAL BARANG
    // ==========================================
    public function indexRequest()
    {
        $requests = ItemRequest::with(['item', 'user', 'productionOrder', 'resource'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $barangs = Item::all();
        
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

        $user   = Auth::user();
        $barang = Item::whereRaw('LOWER(name) = ?', [strtolower(trim($request->nama_barang))])->first();

        ItemRequest::create([
            'request_code'        => 'REQ-GENERAL-' . strtoupper(uniqid()),
            'user_id'             => $user ? $user->id : null,
            'production_order_id' => $request->input('production_order_id'),
            'resource_id'         => $request->input('resource_id'),
            'department'          => $request->department,
            'item_id'             => $barang ? $barang->id : null,
            'qty'                 => $request->qty,
            'type'                => 'general_request',
            'status'              => 'Pending Admin',
            'notes'               => "Permintaan barang: {$request->nama_barang} ({$request->qty} {$request->satuan}). " . ($request->notes ?? '')
        ]);

        return redirect('/inventory/produksi/requests')->with('success', 'Permintaan berhasil dikirim ke Admin untuk disetujui.');
    }

    public function approveRequest($id)
    {
        $user        = Auth::user();
        $itemRequest = ItemRequest::findOrFail($id);
        $role        = $this->getNormalizedRole($user);

        if (!in_array($role, ['admin', 'super_admin'])) {
            return redirect('/inventory/produksi/requests')->with('error', 'Akses ditolak.');
        }

        if (in_array($itemRequest->status, ['Approved by Admin', 'Approved by Super Admin'])) {
            return redirect('/inventory/produksi/requests')->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        DB::beginTransaction();

        try {
            $newStatus = ($role === 'super_admin') ? 'Approved by Super Admin' : 'Approved by Admin';

            if (strtolower($itemRequest->type) === 'del') {
                $itemRequest->update(['status' => $newStatus]);
                
                if ($itemRequest->item_id) {
                    Item::where('id', $itemRequest->item_id)->delete();
                }

                DB::commit();
                return redirect('/inventory/produksi/requests')->with('success', 'Barang berhasil dihapus.');
            }

            if (in_array($itemRequest->type, ['in', 'create_barang', 'general_request'])) {
                $barang = $itemRequest->item_id ? Item::find($itemRequest->item_id) : null;
                
                if ($barang) {
                    $barang->stok += $itemRequest->qty;
                    $barang->save();
                }
            } 
            elseif ($itemRequest->type === 'out') {
                $barang = $itemRequest->item_id ? Item::find($itemRequest->item_id) : null;

                if ($barang) {
                    if ($barang->stok < $itemRequest->qty) {
                        DB::rollBack();
                        return redirect('/inventory/produksi/requests')->with('error', 'Stok barang tidak mencukupi!');
                    }
                    $barang->stok -= $itemRequest->qty;
                    $barang->save();
                } else {
                    DB::rollBack();
                    return redirect('/inventory/produksi/requests')->with('error', 'Barang tidak ditemukan di sistem master.');
                }
            }

            $itemRequest->update(['status' => $newStatus]);

            DB::commit();
            return redirect('/inventory/produksi/requests')->with('success', 'Permintaan berhasil disetujui dan stok diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kesalahan saat approve request: ' . $e->getMessage());
            return redirect('/inventory/produksi/requests')->with('error', 'Terjadi kesalahan sistem saat menyetujui: ' . $e->getMessage());
        }
    }

    public function rejectRequest($id)
    {
        $user        = Auth::user();
        $itemRequest = ItemRequest::findOrFail($id);
        $role        = $this->getNormalizedRole($user);

        if (in_array($role, ['admin', 'super_admin'])) {
            $itemRequest->update(['status' => 'Rejected']);
            return redirect('/inventory/produksi/requests')->with('success', 'Permintaan berhasil ditolak.');
        }

        return redirect('/inventory/produksi/requests')->with('error', 'Akses ditolak.');
    }

    public function destroyRequest($id)
    {
        $user = Auth::user();
        $role = $this->getNormalizedRole($user);

        if ($role === 'super_admin') {
            $itemRequest = ItemRequest::findOrFail($id);
            $itemRequest->delete();
            return redirect('/inventory/produksi/requests')->with('success', 'Data permintaan berhasil dihapus.');
        }

        return redirect('/inventory/produksi/requests')->with('error', 'Akses ditolak.');
    }

    public function destroyBulkRequests(Request $request)
    {
        $user = Auth::user();
        $role = $this->getNormalizedRole($user);

        if ($role !== 'super_admin') {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $ids = $request->input('ids');

        if ($ids && is_array($ids)) {
            ItemRequest::whereIn('id', $ids)->delete();
            return response()->json(['message' => 'Data permintaan berhasil dihapus.']);
        }

        return response()->json(['message' => 'Tidak ada data yang dipilih.'], 400);
    }
}