<?php

namespace App\Http\Controllers;

use App\Models\InventoryPo;
use App\Models\Item;
use Illuminate\Http\Request;

class InventoryPOController extends Controller
{
    public function index()
    {
        $inventoryPos = InventoryPo::with('item')->latest()->get();
        
        return view('inventory.po.index', compact('inventoryPos'));
    }

    public function create()
    {
        $items = Item::all();

        return view('inventory.po.create', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_po'              => 'required|string|max:100|unique:po_inventories,no_po',
            'item_id'            => 'nullable|exists:items,id',
            'produk_jadi'        => 'required|string|max:255',
            'jumlah_produksi'    => 'required|integer|min:1',
            'tanggal_produksi'   => 'required|date',
            'lokasi_penyimpanan' => 'required|string|max:255',
            'deadline'           => 'required|date',
        ]);

        InventoryPo::create($validated);

        return redirect()->route('inventory.po.index')
            ->with('success', 'Data penyimpanan PO berhasil disimpan!');
    }

    public function show($id)
    {
        $po = InventoryPo::with('item')->findOrFail($id);
        return view('inventory.po.show', compact('po'));
    }

    public function edit($id)
    {
        $po = InventoryPo::findOrFail($id);
        $items = Item::all();
        return view('inventory.po.edit', compact('po', 'items'));
    }

    public function update(Request $request, $id)
    {
        $po = InventoryPo::findOrFail($id);

        $validated = $request->validate([
            'no_po'              => 'required|string|max:100|unique:po_inventories,no_po,' . $id,
            'item_id'            => 'nullable|exists:items,id',
            'produk_jadi'        => 'required|string|max:255',
            'jumlah_produksi'    => 'required|integer|min:1',
            'tanggal_produksi'   => 'required|date',
            'lokasi_penyimpanan' => 'required|string|max:255',
            'deadline'           => 'required|date',
        ]);

        $po->update($validated);

        return redirect()->route('inventory.po.index')
            ->with('success', 'Data penyimpanan PO berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $po = InventoryPo::findOrFail($id);
        $po->delete();

        return redirect()->route('inventory.po.index')
            ->with('success', 'Data penyimpanan PO berhasil dihapus!');
    }
}