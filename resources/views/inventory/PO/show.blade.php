@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Detail Purchase Order (PO)</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th width="30%">Jumlah Produksi</th>
                    <td>{{ $po->jumlah_produksi }}</td>
                </tr>
                <tr>
                    <th>Tanggal Produksi</th>
                    <td>{{ $po->tanggal_produksi }}</td>
                </tr>
                <tr>
                    <th>Lokasi Penyimpanan</th>
                    <td>{{ $po->lokasi_penyimpanan }}</td>
                </tr>
                <tr>
                    <th>Deadline</th>
                    <td>{{ $po->deadline }}</td>
                </tr>
            </table>
            <a href="{{ route('inventory.po.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</div>
@endsection