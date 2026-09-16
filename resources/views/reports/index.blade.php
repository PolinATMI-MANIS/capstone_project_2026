@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    
    <!-- HEADER & TOMBOL EXPORT PDF -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-10 print-hide">
        <div>
            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.6rem;">Laporan Rekapitulasi Operasional</h2>
            <p class="text-muted small mb-0">Laporan terintegrasi sistem Man Power, Machine Power, dan Production Resources.</p>
        </div>
        <button onclick="exportToPDF()" id="downloadBtn" class="btn fw-bold text-white px-4 py-2 shadow-sm d-flex align-items-center gap-2" style="background-color: #ff6600; border: none; border-radius: 8px;">
            <i class="fa-solid fa-file-pdf"></i> Download PDF
        </button>
    </div>

    <!-- AREA LAPORAN YANG AKAN DI-EXPORT -->
    <div class="card border-0 shadow-sm p-4 rounded-4 bg-white" id="reportArea">
        
        <!-- KOP LAPORAN -->
        <div class="text-center border-bottom pb-4 mb-4">
            <h4 class="fw-bold text-dark text-uppercase mb-1" style="letter-spacing: 1px;">CAPSTONE 2026</h4>
            <p class="text-muted small mb-0">Laporan Status Alokasi Sumber Daya Pabrik & Integrasi Mesin</p>
            <!-- Keterangan waktu dengan ID untuk update otomatis per detik saat tombol diklik -->
            <div>
                <span class="badge bg-light text-secondary font-monospace mt-2" id="downloadTimestamp">
                    Dicetak pada: {{ now()->format('d F Y, H:i:s') }} WIB
                </span>
            </div>
        </div>

        <!-- RINGKASAN STATISTIK (KPI) -->
        <div class="mb-4">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-chart-pie text-warning me-2"></i> Ringkasan Eksekutif</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="p-3 border rounded-3 bg-light">
                        <span class="text-muted small d-block">Man Power Aktif</span>
                        <h4 class="fw-bold text-dark mb-0">{{ $stats['working_man'] }} <span class="fs-6 fw-normal text-muted">/ {{ $stats['total_man'] }} Orang</span></h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 border rounded-3 bg-light">
                        <span class="text-muted small d-block">Mesin Running (Aktif)</span>
                        <h4 class="fw-bold text-success mb-0">{{ $stats['running_machine'] }} <span class="fs-6 fw-normal text-muted">/ {{ $stats['total_machine'] }} Unit</span></h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 border rounded-3 bg-light">
                        <span class="text-muted small d-block">SPK Ready to Process</span>
                        <h4 class="fw-bold text-primary mb-0">{{ $stats['ready_order'] }} <span class="fs-6 fw-normal text-muted">/ {{ $stats['total_order'] }} Item</span></h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABEL 1: STATUS MAN POWER -->
        <div class="mb-5">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-users text-primary me-2"></i> Data Status Man Power</h6>
            <div class="table-responsive">
                <table class="table table-bordered align-middle small">
                    <thead class="table-light">
                        <tr>
                            <th>No ID</th>
                            <th>Nama Operator</th>
                            <th>Posisi</th>
                            <th>Status Saat Ini</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($manPowers as $mp)
                        <tr>
                            <td class="font-monospace">#{{ str_pad($mp->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td class="fw-bold">{{ $mp->nama }}</td>
                            <td>{{ $mp->posisi }}</td>
                            <td>
                                <span class="badge {{ $mp->status == 'Kerja' ? 'bg-success' : ($mp->status == 'Cuti' ? 'bg-secondary' : 'bg-warning text-dark') }}">
                                    {{ $mp->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TABEL 2: STATUS MACHINE POWER -->
        <div class="mb-5">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-gears text-success me-2"></i> Data Status Mesin & Integrasi Operator</h6>
            <div class="table-responsive">
                <table class="table table-bordered align-middle small">
                    <thead class="table-light">
                        <tr>
                            <th>No ID</th>
                            <th>Nama Mesin</th>
                            <th>Tipe</th>
                            <th>Status</th>
                            <th>Operator Bertugas</th>
                            <th>Jam Mulai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($machinePowers as $mac)
                        <tr>
                            <td class="font-monospace">#{{ str_pad($mac->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td class="fw-bold">{{ $mac->machine_name }}</td>
                            <td>{{ $mac->machine_type }}</td>
                            <td>
                                <span class="badge {{ $mac->status == 'Running' ? 'bg-success' : ($mac->status == 'Breakdown' ? 'bg-danger' : 'bg-secondary') }}">
                                    {{ $mac->status }}
                                </span>
                            </td>
                            <td>{{ $mac->operator->nama ?? '-' }}</td>
                            <td>{{ $mac->start_time ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TABEL 3: PRODUCTION ORDERS -->
        <div class="mb-3">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-box-open text-warning me-2"></i> Rekapitulasi Surat Perintah Kerja (SPK)</h6>
            <div class="table-responsive">
                <table class="table table-bordered align-middle small">
                    <thead class="table-light">
                        <tr>
                            <th>No PO</th>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Status Pengerjaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productionOrders as $po)
                        <tr>
                            <td class="font-monospace fw-bold">#{{ $po->no_po }}</td>
                            <td>{{ $po->produk }}</td>
                            <td>{{ $po->jumlah_produksi }} Pcs</td>
                            <td>
                                <span class="badge bg-dark bg-opacity-10 text-dark border">
                                    {{ $po->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- SCRIPT HTML2PDF DARI CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function exportToPDF() {
        const element = document.getElementById('reportArea');
        const btn = document.getElementById('downloadBtn');
        
        // Buat format tanggal, bulan, tahun, jam, menit, detik secara real-time saat tombol diklik
        const now = new Date();
        const optionsDate = { day: 'numeric', month: 'long', year: 'numeric' };
        const tanggalStr = now.toLocaleDateString('id-ID', optionsDate);
        
        const jam = String(now.getHours()).padStart(2, '0');
        const menit = String(now.getMinutes()).padStart(2, '0');
        const detik = String(now.getSeconds()).padStart(2, '0');
        const waktuStr = `${jam}:${menit}:${detik}`;

        // Masukkan keterangan waktu persis ke dalam elemen PDF sebelum di-render
        document.getElementById('downloadTimestamp').innerText = 
            `Didownload pada: ${tanggalStr}, ${waktuStr} WIB`;

        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generating PDF...';
        btn.disabled = true;

        const opt = {
            margin:       10,
            filename:     'Laporan-Operasional-Capstone.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        html2pdf().from(element).set(opt).save().then(() => {
            btn.innerHTML = '<i class="fa-solid fa-file-pdf"></i> Download PDF';
            btn.disabled = false;
        }).catch(err => {
            console.error(err);
            alert('Gagal mengexport PDF.');
            btn.innerHTML = '<i class="fa-solid fa-file-pdf"></i> Download PDF';
            btn.disabled = false;
        });
    }
</script>
@endsection