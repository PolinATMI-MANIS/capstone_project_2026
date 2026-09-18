@extends('layouts.app')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap');

    :root {
        --paper: #ffffff;
        --page-bg: #f4f5f3;
        --ink: #252525;
        --ink-soft: #555555;
        --muted: #858585;
        --line: #e5e5e1;
        --line-dark: #d4d4cf;

        --orange: #f05a28;
        --blue: #4272a8;
        --green: #4d8a68;
    }

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family: 'Manrope', sans-serif;
        color: var(--ink);
        background: var(--page-bg);
    }

    /* =========================
       TOOLBAR
    ========================= */

    .report-toolbar {
        width: min(1200px, calc(100% - 40px));
        margin: 30px auto 24px;

        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
    }

    .toolbar-left {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .toolbar-kicker {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: var(--muted);
    }

    .toolbar-title {
        font-size: 22px;
        font-weight: 800;
        letter-spacing: -0.6px;
    }

    .print-button {
        border: 1px solid var(--ink);
        background: var(--ink);
        color: white;

        padding: 10px 17px;
        border-radius: 6px;

        font-family: inherit;
        font-size: 12px;
        font-weight: 700;

        cursor: pointer;
        transition: 0.2s ease;
    }

    .print-button:hover {
        background: #444;
        border-color: #444;
    }

    /* =========================
       PRINT AREA
    ========================= */

    #print-area {
        width: 100%;
        padding-bottom: 50px;
    }

    /* =========================
       A4 PAPER
    ========================= */

    .a4-paper {
        width: 210mm;
        min-height: 297mm;

        margin: 0 auto 30px;
        padding: 15mm 16mm 16mm;

        background: var(--paper);

        box-shadow:
            0 12px 40px rgba(0, 0, 0, 0.08);

        position: relative;

        overflow: hidden;
    }

    /* =========================
       PAGE HEADER
    ========================= */

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;

        padding-bottom: 18px;
        border-bottom: 2px solid var(--ink);
    }

    .brand-block {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .brand-small {
        font-size: 9px;
        font-weight: 800;
        letter-spacing: 2.2px;
        text-transform: uppercase;
        color: var(--muted);
    }

    .report-title {
        margin: 0;

        font-size: 27px;
        line-height: 1.05;
        font-weight: 800;
        letter-spacing: -1.2px;
    }

    .report-subtitle {
        margin-top: 6px;

        font-size: 11px;
        font-weight: 500;
        color: var(--ink-soft);
    }

    .page-number {
        text-align: right;

        font-size: 9px;
        line-height: 1.5;
        font-weight: 700;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .page-number strong {
        display: block;

        font-size: 20px;
        line-height: 1;

        color: var(--ink);
        letter-spacing: -0.5px;
    }

    /* =========================
       SECTION
    ========================= */

    .section {
        margin-top: 25px;
    }

    .section-heading {
        display: flex;
        align-items: baseline;
        justify-content: space-between;

        margin-bottom: 12px;
        padding-bottom: 7px;

        border-bottom: 1px solid var(--line-dark);
    }

    .section-title {
        margin: 0;

        font-size: 15px;
        font-weight: 800;
        letter-spacing: -0.3px;
    }

    .section-label {
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: var(--muted);
    }

    /* =========================
       KPI
    ========================= */

    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);

        border-top: 1px solid var(--line);
        border-left: 1px solid var(--line);
    }

    .kpi {
        min-height: 78px;

        padding: 13px 15px;

        border-right: 1px solid var(--line);
        border-bottom: 1px solid var(--line);

        position: relative;
    }

    .kpi::before {
        content: '';

        position: absolute;
        left: 0;
        top: 0;

        width: 3px;
        height: 100%;
    }

    .kpi.orange::before {
        background: var(--orange);
    }

    .kpi.blue::before {
        background: var(--blue);
    }

    .kpi.green::before {
        background: var(--green);
    }

    .kpi-label {
        font-size: 9px;
        font-weight: 700;

        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .kpi-value {
        margin-top: 4px;

        font-size: 27px;
        line-height: 1;

        font-weight: 800;
        letter-spacing: -1px;
    }

    /* =========================
       STATUS ROW
    ========================= */

    .status-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);

        margin-top: 10px;
        gap: 8px;
    }

    .status-item {
        display: flex;
        align-items: center;
        justify-content: space-between;

        padding: 9px 11px;

        border: 1px solid var(--line);

        font-size: 10px;
    }

    .status-name {
        color: var(--ink-soft);
        font-weight: 600;
    }

    .status-value {
        font-weight: 800;
    }

    /* =========================
       TABLE
    ========================= */

    .report-table {
        width: 100%;
        border-collapse: collapse;

        font-size: 10px;
    }

    .report-table thead th {
        padding: 8px 9px;

        text-align: left;

        background: #f7f7f5;

        border-top: 1px solid var(--line-dark);
        border-bottom: 1px solid var(--line-dark);

        font-size: 8px;
        font-weight: 800;

        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    .report-table tbody td {
        padding: 9px;

        border-bottom: 1px solid var(--line);

        vertical-align: top;
    }

    .report-table tbody tr:last-child td {
        border-bottom: 1px solid var(--line-dark);
    }

    .table-number {
        width: 35px;

        color: var(--muted);
        font-weight: 700;
    }

    .primary-text {
        font-weight: 700;
    }

    .secondary-text {
        margin-top: 2px;

        font-size: 8px;
        color: var(--muted);
    }

    .qty {
        font-weight: 800;
    }

    /* =========================
       BADGE
    ========================= */

    .badge {
        display: inline-flex;
        align-items: center;

        padding: 4px 7px;

        border-radius: 3px;

        font-size: 8px;
        font-weight: 800;

        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-running {
        background: #edf4ef;
        color: var(--green);
    }

    .badge-pending {
        background: #fdf1ec;
        color: var(--orange);
    }

    .badge-done {
        background: #eef2f6;
        color: var(--blue);
    }

    /* =========================
       RESOURCE GRID
    ========================= */

    .resource-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;

        gap: 20px;
    }

    .resource-box {
        border-top: 2px solid var(--ink);
        padding-top: 10px;
    }

    .resource-title {
        margin-bottom: 10px;

        font-size: 11px;
        font-weight: 800;
    }

    .resource-line {
        display: flex;
        justify-content: space-between;
        align-items: center;

        padding: 8px 0;

        border-bottom: 1px solid var(--line);

        font-size: 10px;
    }

    .resource-name {
        color: var(--ink-soft);
    }

    .resource-number {
        font-weight: 800;
    }

    /* =========================
       MAN POWER
    ========================= */

    .person-name {
        font-weight: 700;
    }

    .person-position {
        margin-top: 2px;

        font-size: 8px;
        color: var(--muted);
    }

    /* =========================
       RUNNING MACHINE
    ========================= */

    .machine-box {
        margin-top: 12px;

        padding: 12px;

        border: 1px solid var(--line-dark);
        border-left: 3px solid var(--green);

        background: #fafbf9;
    }

    .machine-top {
        display: flex;
        justify-content: space-between;
        align-items: center;

        margin-bottom: 9px;
    }

    .machine-title {
        font-size: 10px;
        font-weight: 800;
    }

    .machine-info {
        display: grid;
        grid-template-columns: 1fr 1fr;

        gap: 10px;
    }

    .machine-field {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .machine-label {
        font-size: 7px;
        font-weight: 800;

        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    .machine-value {
        font-size: 10px;
        font-weight: 700;
    }

    /* =========================
       EMPTY
    ========================= */

    .empty-state {
        padding: 18px;

        text-align: center;

        border: 1px dashed var(--line-dark);

        color: var(--muted);

        font-size: 9px;
    }

    /* =========================
       FOOTER
    ========================= */

    .page-footer {
        position: absolute;

        bottom: 8mm;
        left: 16mm;
        right: 16mm;

        display: flex;
        justify-content: space-between;

        padding-top: 7px;

        border-top: 1px solid var(--line);

        font-size: 7px;
        font-weight: 600;

        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.7px;
    }

    /* =====================================================
       PRINT
       ===================================================== */

    @media print {

        /* FULL A4 */
        @page {
            size: A4 portrait;
            margin: 0;
        }

        html,
        body {
            width: 210mm !important;
            min-width: 210mm !important;

            margin: 0 !important;
            padding: 0 !important;

            background: white !important;
        }

        /* HILANGKAN UI APLIKASI */
        .print-hide,
        aside,
        nav,
        header,
        footer,
        .navbar,
        .sidebar {
            display: none !important;
        }

        /* PAKSA WRAPPER FULL A4 */
        #app,
        main,
        .content-wrapper,
        .container,
        .container-fluid,
        #print-area {
            width: 210mm !important;
            max-width: none !important;

            margin: 0 !important;
            padding: 0 !important;
        }

        #print-area {
            display: block !important;
        }

        /* A4 PAPER EXACT */
        .a4-paper {
            width: 210mm !important;
            height: 297mm !important;
            min-height: 297mm !important;

            margin: 0 !important;

            /*
             * Ini margin isi dokumen.
             * Ukuran kertas tetap 210 x 297 mm.
             */
            padding: 15mm 16mm 16mm !important;

            box-sizing: border-box !important;

            background: white !important;

            box-shadow: none !important;
            border-radius: 0 !important;

            page-break-after: always;
            break-after: page;

            overflow: hidden !important;

            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .a4-paper:last-child {
            page-break-after: auto;
            break-after: auto;
        }

        /* TABLE JANGAN PECAH SEMBARANGAN */
        .report-table {
            page-break-inside: auto;
        }

        .report-table tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .section {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .kpi-grid,
        .resource-grid,
        .machine-box {
            page-break-inside: avoid;
            break-inside: avoid;
        }
    }

    /* =========================
       RESPONSIVE SCREEN
    ========================= */

    @media screen and (max-width: 800px) {

        .report-toolbar {
            width: calc(100% - 24px);
        }

        .a4-paper {
            transform-origin: top center;
            width: 210mm;
        }

        .kpi-grid {
            grid-template-columns: 1fr;
        }

        .status-row {
            grid-template-columns: 1fr;
        }

        .resource-grid {
            grid-template-columns: 1fr;
        }
    }
</style>


<!-- =====================================================
     TOOLBAR
===================================================== -->

<div class="report-toolbar print-hide">

    <div class="toolbar-left">

        <div class="toolbar-kicker">
            Capstone / Reporting
        </div>

        <div class="toolbar-title">
            Laporan Operasional
        </div>

    </div>

    <button
        type="button"
        class="print-button"
        onclick="window.print()"
    >
        Cetak / PDF
    </button>

</div>


<!-- =====================================================
     PRINT AREA
===================================================== -->

<div id="print-area">


    <!-- =================================================
         PAGE 01 — PRODUCTION
    ================================================== -->

    <section class="a4-paper">

        <!-- HEADER -->

        <div class="page-header">

            <div class="brand-block">

                <div class="brand-small">
                    Capstone Project
                </div>

                <h1 class="report-title">
                    Laporan Operasional
                </h1>

                <div class="report-subtitle">
                    Production &amp; Work Order Overview
                </div>

            </div>

            <div class="page-number">

                Page

                <strong>01</strong>

                Production

            </div>

        </div>


        <!-- PRODUCTION SUMMARY -->

        <div class="section">

            <div class="section-heading">

                <h2 class="section-title">
                    Production Summary
                </h2>

                <span class="section-label">
                    Current Status
                </span>

            </div>


            <div class="kpi-grid">

                <!-- PENDING -->

                <div class="kpi orange">

                    <div class="kpi-label">
                        SPK Pending
                    </div>

                    <div class="kpi-value">
                        {{ $spk_pending }}
                    </div>

                </div>


                <!-- RUNNING -->

                <div class="kpi blue">

                    <div class="kpi-label">
                        SPK Running
                    </div>

                    <div class="kpi-value">
                        {{ $spk_running }}
                    </div>

                </div>


                <!-- SELESAI -->

                <div class="kpi green">

                    <div class="kpi-label">
                        SPK Selesai
                    </div>

                    <div class="kpi-value">
                        {{ $spk_selesai }}
                    </div>

                </div>

            </div>


            <!-- PRODUCTION STATUS -->

            <div class="status-row">

                <div class="status-item">

                    <span class="status-name">
                        Produksi Pending
                    </span>

                    <span class="status-value">
                        {{ $produksi_pending }}
                    </span>

                </div>


                <div class="status-item">

                    <span class="status-name">
                        Produksi Running
                    </span>

                    <span class="status-value">
                        {{ $produksi_running }}
                    </span>

                </div>


                <div class="status-item">

                    <span class="status-name">
                        Produksi Selesai
                    </span>

                    <span class="status-value">
                        {{ $produksi_selesai }}
                    </span>

                </div>

            </div>

        </div>


        <!-- PRODUCTION ORDER -->

        <div class="section">

            <div class="section-heading">

                <h2 class="section-title">
                    Production Orders
                </h2>

                <span class="section-label">
                    Work Orders
                </span>

            </div>


            @if(isset($produksi) && count($produksi) > 0)

                <table class="report-table">

                    <thead>

                        <tr>

                            <th style="width: 35px;">
                                #
                            </th>

                            <th>
                                No. PO
                            </th>

                            <th>
                                Produk
                            </th>

                            <th>
                                Quantity
                            </th>

                            <th>
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($produksi as $index => $prod)

                            <tr>

                                <td class="table-number">
                                    {{ $index + 1 }}
                                </td>


                                <td>

                                    <div class="primary-text">
                                        {{ $prod->no_po ?? 'N/A' }}
                                    </div>

                                </td>


                                <td>

                                    <div class="primary-text">
                                        {{ $prod->produk ?? $prod->nama_produk ?? $prod->name ?? 'N/A' }}
                                    </div>

                                </td>


                                <td>

                                    <span class="qty">
                                        {{ $prod->jumlah_produksi ?? $prod->qty ?? 0 }}
                                    </span>

                                </td>


                                <td>

                                    @php
                                        $status = $prod->status ?? 'Pending';

                                        $statusClass = match(strtolower($status)) {
                                            'running' => 'badge-running',
                                            'selesai',
                                            'completed',
                                            'done' => 'badge-done',
                                            default => 'badge-pending',
                                        };
                                    @endphp

                                    <span class="badge {{ $statusClass }}">
                                        {{ $status }}
                                    </span>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            @else

                <div class="empty-state">
                    Tidak ada data production order.
                </div>

            @endif

        </div>


        <!-- ACTIVE PRODUCTION -->

        <div class="section">

            <div class="section-heading">

                <h2 class="section-title">
                    Active Production
                </h2>

                <span class="section-label">
                    Running Work
                </span>

            </div>


            <div class="machine-box">

                <div class="machine-top">

                    <div class="machine-title">
                        Active Machine
                    </div>

                    <span class="badge badge-running">
                        Running
                    </span>

                </div>


                <div class="machine-info">

                    <div class="machine-field">

                        <span class="machine-label">
                            Machine
                        </span>

                        <span class="machine-value">
                            Mesin A1
                        </span>

                    </div>


                    <div class="machine-field">

                        <span class="machine-label">
                            Active SPK
                        </span>

                        <span class="machine-value">
                            PO-XX
                        </span>

                    </div>

                </div>

            </div>

        </div>


        <!-- FOOTER -->

        <div class="page-footer">

            <span>
                Operational Report
            </span>

            <span>
                Production Overview
            </span>

        </div>

    </section>



    <!-- =================================================
         PAGE 02 — RESOURCES
    ================================================== -->

    <section class="a4-paper">

        <!-- HEADER -->

        <div class="page-header">

            <div class="brand-block">

                <div class="brand-small">
                    Capstone Project
                </div>

                <h1 class="report-title">
                    Resources
                </h1>

                <div class="report-subtitle">
                    Man Power &amp; Operational Resources
                </div>

            </div>

            <div class="page-number">

                Page

                <strong>02</strong>

                Resources

            </div>

        </div>


        <!-- RESOURCE SUMMARY -->

        <div class="section">

            <div class="section-heading">

                <h2 class="section-title">
                    Resource Summary
                </h2>

                <span class="section-label">
                    Man Power
                </span>

            </div>


            <div class="kpi-grid">

                <!-- TOTAL -->

                <div class="kpi blue">

                    <div class="kpi-label">
                        Total Man Power
                    </div>

                    <div class="kpi-value">
                        {{ count($man_power) }}
                    </div>

                </div>


                <!-- RUNNING -->

                <div class="kpi green">

                    <div class="kpi-label">
                        Running
                    </div>

                    <div class="kpi-value">
                        {{ collect($man_power)->where('status', 'Running')->count() }}
                    </div>

                </div>


                <!-- AVAILABLE -->

                <div class="kpi orange">

                    <div class="kpi-label">
                        Available
                    </div>

                    <div class="kpi-value">
                        {{ collect($man_power)->where('status', '!=', 'Running')->count() }}
                    </div>

                </div>

            </div>

        </div>


        <!-- MAN POWER TABLE -->

        <div class="section">

            <div class="section-heading">

                <h2 class="section-title">
                    Man Power List
                </h2>

                <span class="section-label">
                    Personnel
                </span>

            </div>


            @if(isset($man_power) && count($man_power) > 0)

                <table class="report-table">

                    <thead>

                        <tr>

                            <th style="width: 35px;">
                                #
                            </th>

                            <th>
                                Nama
                            </th>

                            <th>
                                Posisi
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Assignment
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($man_power as $index => $mp)

                            <tr>

                                <td class="table-number">
                                    {{ $index + 1 }}
                                </td>


                                <td>

                                    <div class="person-name">
                                        {{ $mp->nama_pekerja ?? $mp->nama ?? $mp->name ?? 'N/A' }}
                                    </div>

                                </td>


                                <td>

                                    <div class="person-position">
                                        {{ $mp->posisi ?? $mp->position ?? 'N/A' }}
                                    </div>

                                </td>


                                <td>

                                    @if(($mp->status ?? '') == 'Running')

                                        <span class="badge badge-running">
                                            Running
                                        </span>

                                    @else

                                        <span class="badge badge-done">
                                            Available
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    @if(($mp->status ?? '') == 'Running')

                                        <div class="primary-text">
                                            {{ $mp->mesin ?? 'Mesin A1' }}
                                        </div>

                                        <div class="secondary-text">
                                            {{ $mp->spk_aktif ?? 'PO-XX' }}
                                        </div>

                                    @else

                                        <span class="secondary-text">
                                            No active assignment
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            @else

                <div class="empty-state">
                    Tidak ada data man power.
                </div>

            @endif

        </div>


        <!-- RESOURCE BREAKDOWN -->

        <div class="section">

            <div class="section-heading">

                <h2 class="section-title">
                    Resource Breakdown
                </h2>

                <span class="section-label">
                    Overview
                </span>

            </div>


            <div class="resource-grid">


                <!-- PERSONNEL -->

                <div class="resource-box">

                    <div class="resource-title">
                        Personnel Status
                    </div>


                    <div class="resource-line">

                        <span class="resource-name">
                            Total
                        </span>

                        <span class="resource-number">
                            {{ count($man_power) }}
                        </span>

                    </div>


                    <div class="resource-line">

                        <span class="resource-name">
                            Running
                        </span>

                        <span class="resource-number">
                            {{ collect($man_power)->where('status', 'Running')->count() }}
                        </span>

                    </div>


                    <div class="resource-line">

                        <span class="resource-name">
                            Available
                        </span>

                        <span class="resource-number">
                            {{ collect($man_power)->where('status', '!=', 'Running')->count() }}
                        </span>

                    </div>

                </div>


                <!-- OPERATION -->

                <div class="resource-box">

                    <div class="resource-title">
                        Production Status
                    </div>


                    <div class="resource-line">

                        <span class="resource-name">
                            Pending
                        </span>

                        <span class="resource-number">
                            {{ $produksi_pending }}
                        </span>

                    </div>


                    <div class="resource-line">

                        <span class="resource-name">
                            Running
                        </span>

                        <span class="resource-number">
                            {{ $produksi_running }}
                        </span>

                    </div>


                    <div class="resource-line">

                        <span class="resource-name">
                            Selesai
                        </span>

                        <span class="resource-number">
                            {{ $produksi_selesai }}
                        </span>

                    </div>

                </div>

            </div>

        </div>


        <!-- FOOTER -->

        <div class="page-footer">

            <span>
                Operational Report
            </span>

            <span>
                Resource Overview
            </span>

        </div>

    </section>

</div>


@endsection