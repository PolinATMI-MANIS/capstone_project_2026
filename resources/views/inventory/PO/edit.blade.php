@extends('layouts.app') {{-- Sesuaikan dengan layout utama Anda --}}

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark fw-bold">
            Edit Data Penyimpanan PO
        </div>
        <div class="card-body">
            <form action="{{ route('inventory.po.update', $po->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="no_po" class="form-label">No. PO</label>
                    <input type="text" class="form-control @error('no_po') is-invalid @enderror" id="no_po" name="no_po" value="{{ old('no_po', $po->no_po) }}" required>
                    @error('no_po')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="item_id" class="form-label">Barang / Item</label>
                    <select class="form-select @error('item_id') is-invalid @enderror" id="item_id" name="item_id">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ old('item_id', $po->item_id) == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_barang ?? $item->produk_jadi ?? 'Item #' . $item->id }}
                            </option>
                        @endforeach
                    </select>
                    @error('item_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="produk_jadi" class="form-label">Produk Jadi</label>
                    <input type="text" class="form-control @error('produk_jadi') is-invalid @enderror" id="produk_jadi" name="produk_jadi" value="{{ old('produk_jadi', $po->produk_jadi) }}" required>
                    @error('produk_jadi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="jumlah_produksi" class="form-label">Jumlah Produksi</label>
                    <input type="number" class="form-control @error('jumlah_produksi') is-invalid @enderror" id="jumlah_produksi" name="jumlah_produksi" value="{{ old('jumlah_produksi', $po->jumlah_produksi) }}" required>
                    @error('jumlah_produksi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="tanggal_produksi" class="form-label">Tanggal Produksi</label>
                    <input type="date" class="form-control @error('tanggal_produksi') is-invalid @enderror" id="tanggal_produksi" name="tanggal_produksi" value="{{ old('tanggal_produksi', $po->tanggal_produksi) }}" required>
                    @error('tanggal_produksi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="lokasi_penyimpanan" class="form-label">Lokasi Penyimpanan</label>
                    <input type="text" class="form-control @error('lokasi_penyimpanan') is-invalid @enderror" id="lokasi_penyimpanan" name="lokasi_penyimpanan" value="{{ old('lokasi_penyimpanan', $po->lokasi_penyimpanan) }}" required>
                    @error('lokasi_penyimpanan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="deadline" class="form-label">Deadline</label>
                    <input type="date" class="form-control @error('deadline') is-invalid @enderror" id="deadline" name="deadline" value="{{ old('deadline', $po->deadline) }}" required>
                    @error('deadline')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Update Data</button>
                <a href="{{ route('inventory.po.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection