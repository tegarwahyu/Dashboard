@extends('layouts.app')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>

/* Styling untuk tabel DataTables secara keseluruhan */
#salesTable {
    font-size: 12px; /* Mengatur ukuran font dasar untuk seluruh tabel */
}

/* Styling untuk header tabel (thead) */
#salesTable thead th {
    font-size: 11px; /* Mengatur ukuran font untuk teks di dalam header */
    padding: 6px 8px; /* Mengurangi padding untuk membuat header lebih ringkas */
    white-space: nowrap; /* Mencegah teks header wrapping */
    
}

/* Styling untuk body tabel (tbody) */
#salesTable tbody td {
    font-size: 11px; /* Mengatur ukuran font untuk teks di dalam sel data */
    padding: 4px 8px; /* Mengurangi padding untuk membuat sel lebih ringkas */
    text-align: right;
}

/* Styling untuk footer tabel (tfoot) */
#salesTable tfoot td {
    font-size: 12px; /* Mengatur ukuran font untuk teks di dalam footer, sedikit lebih besar dari body */
    padding: 6px 8px; /* Mengurangi padding */
    text-align: right;
}

/* Jika Anda menggunakan Bootstrap atau tema lain yang memiliki class .table,
   terkadang ada styling default yang perlu di-override */
.table th, .table td {
    vertical-align: middle; /* Memastikan teks di tengah secara vertikal */
}

/* CSS tambahan jika ada masalah dengan tampilan pada tampilan responsif */
.dataTables_wrapper .dataTables_scrollBody {
    font-size: 11px; /* Pastikan font tetap kecil jika ada scrolling body */
}

/* css tabel summary */
#reportTable{
    font-size: 12px; /* Mengatur ukuran font dasar untuk seluruh tabel */
}

/* Styling untuk header tabel (thead) */
#reportTable thead th {
    font-size: 11px; /* Mengatur ukuran font untuk teks di dalam header */
    padding: 6px 8px; /* Mengurangi padding untuk membuat header lebih ringkas */
    white-space: nowrap; /* Mencegah teks header wrapping */
}

/* Styling untuk body tabel (tbody) */
#reportTable tbody td {
    font-size: 11px; /* Mengatur ukuran font untuk teks di dalam sel data */
    padding: 4px 8px; /* Mengurangi padding untuk membuat sel lebih ringkas */
}

/* Styling untuk footer tabel (tfoot) */
#reportTable tfoot td {
    font-size: 12px; /* Mengatur ukuran font untuk teks di dalam footer, sedikit lebih besar dari body */
    padding: 6px 8px; /* Mengurangi padding */
}
/* komparasi Table */
#tableCompareTable{
    font-size: 12px; /* Mengatur ukuran font dasar untuk seluruh tabel */
}

/* Styling untuk header tabel (thead) */
#tableCompareTable thead th {
    font-size: 11px; /* Mengatur ukuran font untuk teks di dalam header */
    padding: 6px 8px; /* Mengurangi padding untuk membuat header lebih ringkas */
    white-space: nowrap; /* Mencegah teks header wrapping */
}

/* Styling untuk body tabel (tbody) */
#tableCompareTable tbody td {
    font-size: 11px; /* Mengatur ukuran font untuk teks di dalam sel data */
    padding: 4px 8px; /* Mengurangi padding untuk membuat sel lebih ringkas */
}

/* Styling untuk footer tabel (tfoot) */
#tableCompareTable tfoot td {
    font-size: 12px; /* Mengatur ukuran font untuk teks di dalam footer, sedikit lebih besar dari body */
    padding: 6px 8px; /* Mengurangi padding */
}
/* rankingTable */
#rankingTable{
    font-size: 12px; /* Mengatur ukuran font dasar untuk seluruh tabel */
}

/* Styling untuk header tabel (thead) */
#rankingTable thead th {
    font-size: 11px; /* Mengatur ukuran font untuk teks di dalam header */
    padding: 6px 8px; /* Mengurangi padding untuk membuat header lebih ringkas */
    white-space: nowrap; /* Mencegah teks header wrapping */
}

/* Styling untuk body tabel (tbody) */
#rankingTable tbody td {
    font-size: 11px; /* Mengatur ukuran font untuk teks di dalam sel data */
    padding: 4px 8px; /* Mengurangi padding untuk membuat sel lebih ringkas */
}

/* Styling untuk footer tabel (tfoot) */
#rankingTable tfoot td {
    font-size: 12px; /* Mengatur ukuran font untuk teks di dalam footer, sedikit lebih besar dari body */
    padding: 6px 8px; /* Mengurangi padding */
}

/* css untuk analisa data start */

/* Terapkan ke kedua tabel */
#analisaItemTable table,
#total_analisa_tabel table {
    font-family: "Consolas", "Courier New", monospace;
    font-size: 13px;
    line-height: 1.4;
    color: #212529;
}

/* Header */
#analisaItemTable thead th,
#total_analisa_tabel thead th {
    background-color: #f8f9fa;
    /* font-weight: bold; */
    padding: 6px 8px;
    text-align: center;
    border-bottom: 2px solid #dee2e6;
}

/* Body */
#analisaItemTable td,
#total_analisa_tabel td {
    padding: 6px 8px;
    vertical-align: middle;
}

/* Baris zebra */
#analisaItemTable tbody tr:nth-child(even),
#total_analisa_tabel tbody tr:nth-child(even) {
    background-color: #f8f9fa;
}

/* Hover effect */
#analisaItemTable tbody tr:hover,
#total_analisa_tabel tbody tr:hover {
    background-color: #f1f7ff;
}

/* css untuk analisa data end */

/* summary sales report css start  */

/* Cari div.choices yang berisi select dengan ID tertentu */
.choices[data-type="select-one"]:has(#outlet_summart_sales_report) {
  position: relative;
  z-index: 9999 !important;
}

.choices[data-type="select-one"]:has(#outlet_summart_sales_report) .choices__list--dropdown {
  z-index: 10000 !important;
}

/* -------------------- Gaya untuk Tabel dan Wadahnya -------------------- */
.financial-report-container {
  /* overflow-x: auto; */
  overflow: visible !important; /* biar dropdown bisa keluar */
  border: 1px solid #ccc;
  margin: 20px 0;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

#financialReportTable {
  width: 100%;
  border-collapse: collapse;
  white-space: nowrap;
}

#financialReportTable th, 
#financialReportTable td {
  padding: 10px 15px;
  border: 1px solid #ddd;
  text-align: left;
}

/* -------------------- Gaya untuk Header Tabel (z-index diperbarui) -------------------- */
#financialReportTable thead {
  background-color: #4CAF50;
  color: white;
  position: sticky;
  top: 0;
  z-index: 10; /* Diperbarui dari 1 menjadi 10. Nilai ini lebih rendah dari dropdown */
}

#financialReportTable thead th {
  text-align: center;
  font-weight: bold;
}

/* -------------------- Gaya untuk Body dan Footer Tabel -------------------- */
#tableBodyData tr:nth-child(even) {
  background-color: #040404ff;
}

#tableBodyData tr:hover {
  background-color: #ddd;
}

#financialReportTable tfoot {
  font-weight: bold;
  background-color: #000000ff;
}

#financialReportTable tfoot td {
  text-align: right;
}

/* -------------------- Gaya untuk Kolom Pertama (z-index diperbarui) -------------------- */
#financialReportTable th:first-child,
#financialReportTable td:first-child {
  position: sticky;
  left: 0;
  background-color: #c9c9c9ff;
  z-index: 11; /* Diperbarui dari 2 menjadi 11. Tetap di atas thead (10) tapi di bawah dropdown */
  text-align: center;
}

/* -------------------- Memperbaiki z-index untuk perpotongan (diperbarui) -------------------- */
#financialReportTable thead th:first-child {
  z-index: 12; /* Diperbarui dari 3 menjadi 12. Tetap di atas kolom lengket dan thead */
}

/* summary sales report css end  */
</style>
@section('content')
<div class="container">
    <br>
    <h1 class="mb-4">Dashboard</h1>
</div>
    
<div class="container-fluid">
    

    <div class="container">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Rekap Daily Sales</h6>
                <hr>
                <div class="row g-1 align-items-end">
                    <div class="col-5">
                        <label for="outlet" class="form-label small"><b>Nama Outlet</b></label>
                        <select id="outlet" name="outlet" class="form-select form-select-sm">
                            <option value="" selected disabled>Pilih Outlet</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <!-- Bulan Dropdown -->
                        <label for="bulan" name="bulan" class="form-label">Bulan</label>
                        <select id="bulan" class="form-select">
                            <option value="1">Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                            <option value="4">April</option>
                            <option value="5">Mei</option>
                            <option value="6">Juni</option>
                            <option value="7">Juli</option>
                            <option value="8">Agustus</option>
                            <option value="9">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label for="tahun" name="tahun" class="form-label">Tahun</label>
                        <input type="number" class="form-control" id="tahun" name="tahun" value="{{ now()->year }}">
                    </div>
                    <div class="col-auto">
                        <button id="btnSearch" class="btn btn-primary btn-sm mt-4">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="box-body table-responsive">
                    <div class="box-body table-responsive">
                        <table class="table table-striped table-bordered dashboard-table" id="salesTable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width: 5%;">No</th>
                                    <th rowspan="2">Hari</th>
                                    <th colspan="2"><span id="labelBulan">Agu-2025</span></th>
                                    <th rowspan="2">Target <span id="labelTahun1">2025</span></th>
                                    <th rowspan="2">Target Sales To Go <span id="labelTahun2">2025</span></th>
                                    <th colspan="3"><span id="labelBulan2">Agu-2025</span></th>
                                    <th colspan="4">Sales Item <span id="labelTahun1">2025</span></th>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Sales</th>
                                    <th>Bill</th>
                                    <th>Guest</th>
                                    <th>Avg/Bill</th>
                                    <th>Avg/Guest</th>
                                    <th>Food</th>
                                    <th>Beverage</th>
                                    <th>Total F&B</th>
                                    <th style="display: none;">Nett Sales</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyData"></tbody>
                            <tfoot id="tfootData99">
                                <tr style="background-color: #ffff99; font-weight: bold;">
                                    <td colspan="3">TOTAL</td>
                                    <td id="totalSales"></td>
                                    <td id="totalNettSales" style="display: none;"></td>
                                    <td id="totalTargetSales"></td>
                                    <td id="totalTargetToGo"></td>
                                    <td id="totalBill"></td>
                                    <td id="totalGuest"></td>
                                    <td id="totalAvgBill"></td>
                                    <td id="totalAvgGuest"></td>
                                    <td id="totalFood"></td>
                                    <td id="totalBeverage"></td>
                                    <td id="totalFB"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Summary</h6>
            <!-- <hr> -->
            <!-- <div class="row g-1 align-items-end">
                <div class="col-5">
                    <label for="outlet_summary" class="form-label small"><b>Nama Outlet</b></label>
                    <select id="outlet_summary" name="outlet_summary" class="form-select form-select-sm">
                        <option value="" selected disabled>Pilih Outlet</option>
                    </select>
                </div>
                <div class="col-auto">
                    <label for="tahun_summary" class="form-label">Tahun</label>
                    <input type="number" class="form-control" id="tahun_summary" name="tahun_summary" value="{{ now()->year }}">
                </div>
                <div class="col-auto">
                    <button id="btnSearchSummary" class="btn btn-primary btn-sm mt-4">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div> -->
            <div class="card-body">
                <div class="table-responsive">
                    <table id="reportTable" class="table table-bordered table-striped w-100" style="width: 100%;">
                        <thead>
                        <tr>
                            <th rowspan="2">BULAN</th>
                            <th colspan="2">{{ now()->year }}</th>
                            <th colspan="4">{{ now()->year }}</th>
                        </tr>
                        <tr>
                            <th>REALISASI</th>
                            <th>TARGET</th>
                            <th>BILL</th>
                            <th>GUEST</th>
                            <th>AVG/BILL</th>
                            <th>AVG/GUEST</th>
                        </tr>
                        </thead>
                        <tbody id="tbodyData2"></tbody>
                        <tfoot id="tfootData2" class="fw-bold text-end text-nowrap">
                        <tr>
                            <td class="text-center">TOTAL</td>
                            <td></td><td></td><td></td><td></td><td></td><td></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>   
        </div>
    </div>
</div>

<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Table Compare</h6>
            <hr>
            <div class="row g-1 align-items-end">
                <div class="col-5">
                    <label for="outlet_compare" class="form-label small"><b>Nama Brand</b></label>
                    <select id="outlet_compare" name="outlet_compare" class="form-select form-select-sm">
                        <option value="" disabled>Pilih Brand</option>
                    </select>
                </div>
                <div class="col-auto">
                    <!-- Bulan Dropdown -->
                    <label for="bulan_compare" name="bulan_compare" class="form-label">Bulan</label>
                    <select id="bulan_compare" class="form-select">
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maret</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                        <option value="7">Juli</option>
                        <option value="8">Agustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>
                <div class="col-auto">
                    <!-- Tahun Dropdown -->
                    <label for="tahun_compare" class="form-label">Tahun</label>
                    <select id="tahun_compare" name="tahun_compare" class="form-select">
                        <!-- Generate tahun secara dinamis -->
                        @php
                            $currentYear = date('Y');
                        @endphp
                        @for ($year = $currentYear; $year >= $currentYear - 5; $year--)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <!-- <div class="col-auto">
                    <label for="date_range_compare" class="form-label">Periode</label>
                    <input type="text" class="form-control" id="date_range_compare" name="date_range_compare" placeholder="Pilih rentang tanggal">
                </div> -->
                <div class="col-auto">
                    <button id="btnSearchCompare" class="btn btn-primary btn-sm mt-4">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-stiped table-bordered dashboard-table" id="tableCompareTable" style="width: 100%;">
                    <!-- <table id="tableCompareTable" class="table table-bordered table-striped w-100" style="width: 100%;"> -->
                        <thead>
                            <tr>
                                <th rowspan="2">OUTLET</th>
                                <th colspan="11" style="text-align: center;">DATA JULI</th>
                            </tr>
                            <tr>
                                <th>GAJI JULI</th>
                                <th>SALES JUL</th>
                                <th>NET SALES</th>
                                <th>RANK</th>
                                <th>TARGET</th>
                                <th>BILL</th>
                                <th>GUEST</th>
                                <th>AVG/BILL</th>
                                <th>AVG/GUEST</th>
                                <th>QTY F&amp;B</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyData3"></tbody>
                    </table>
                </div>
            </div>   
        </div>
    </div>
</div>

<div class="container" style="display: none;">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Table Rank Prestasi</h6>
            <hr>
            <div class="row g-1 align-items-end">
                <div class="col-5">
                    <label for="outlet_rank" class="form-label small"><b>Nama Brand</b></label>
                    <select id="outlet_rank" name="outlet_rank" class="form-select form-select-sm">
                        <option value="" disabled>Pilih Brand</option>
                    </select>
                </div>
                <div class="col-auto">
                    <!-- Bulan Dropdown -->
                    <label for="bulan_rank" name="bulan_rank" class="form-label"><b>Bulan</b></label>
                    <select id="bulan_rank" class="form-select">
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maret</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                        <option value="7">Juli</option>
                        <option value="8">Agustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>
                <div class="col-auto">
                    <!-- Tahun Dropdown -->
                    <label for="tahun_rank" class="form-label">Tahun</label>
                    <select id="tahun_rank" name="tahun_rank" class="form-select">
                        <!-- Generate tahun secara dinamis -->
                        @php
                            $currentYear = date('Y');
                        @endphp
                        @for ($year = $currentYear; $year >= $currentYear - 5; $year--)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-auto">
                    <button id="btnSearchRank" class="btn btn-primary btn-sm mt-4">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="rankingTable" class="table table-bordered text-center align-middle w-100" >
                    <thead class="table-success">
                        <tr>
                            <th>RANKING</th>
                            <th>OUTLET</th>
                            <th>LAST RANKING</th>
                            <th>LAST</th>
                            <th>PRESENT</th>
                            <th>%</th>
                            <th>TARGET</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody id="salesSection"></tbody>
                    <tbody id="billSection"></tbody>
                    <tbody id="averageBillSection"></tbody>
                    <tbody id="guestSection"></tbody>
                    <tbody id="averageGuestSection"></tbody>
                    <tbody id="totalItemSalesSection"></tbody>
                    <!-- buat tbody tambahan untuk kategori lain -->
                </table>
            </div>
        </div>
    </div>
</div>


<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Analisa Item</h6>
            <hr>
            <div class="row g-1 align-items-end">
                <div class="col-5">
                    <label for="outlet_analisa" class="form-label small"><b>Nama outlet</b></label>
                    <select id="outlet_analisa" name="outlet_analisa" class="form-select form-select-sm">
                        <option value="" disabled>Pilih Outlet</option>
                    </select>
                </div>
                <div class="col-auto">
                    <!-- Bulan Dropdown -->
                    <label for="bulan_analisa" name="bulan_analisa" class="form-label">Bulan</label>
                    <select id="bulan_analisa" class="form-select">
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maret</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                        <option value="7">Juli</option>
                        <option value="8">Agustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>
                <div class="col-auto">
                    <!-- Tahun Dropdown -->
                    <label for="tahun_analisa" class="form-label">Tahun</label>
                    <select id="tahun_analisa" name="tahun_analisa" class="form-select">
                        <!-- Generate tahun secara dinamis -->
                        @php
                            $currentYear = date('Y');
                        @endphp
                        @for ($year = $currentYear; $year >= $currentYear - 5; $year--)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <!-- <div class="col-auto">
                    <label for="date_range_compare" class="form-label">Periode</label>
                    <input type="text" class="form-control" id="date_range_compare" name="date_range_compare" placeholder="Pilih rentang tanggal">
                </div> -->
                <div class="col-auto">
                    <button id="btnSearchAnalisa" class="btn btn-primary btn-sm mt-4">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div id="analisaItemTable" class="mt-4"></div>
                </div>
            </div>   
        </div>
    </div>
</div>

<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h4 class="m-0 font-weight-bold text-primary">Summary Sales Report <span id="tahun_summary_sales_report">{{ date('Y') }}</span></h4>
            <hr>
            <div class="row g-1 align-items-end">
                <div class="col-5">
                    <label for="outlet_summart_sales_report" class="form-label small"><b>Nama outlet</b></label>
                    <select id="outlet_summart_sales_report" name="outlet_summart_sales_report" class="form-select form-select-sm">
                        <option value="" disabled>Pilih Outlet</option>
                    </select>
                </div>
                <div class="col-auto">
                    <!-- Tahun Dropdown -->
                    <label for="tahun_summart_sales_report" class="form-label">Tahun</label>
                    <select id="tahun_summart_sales_report" name="tahun_summart_sales_report" class="form-select">
                        <!-- Generate tahun secara dinamis -->
                        @php
                            $currentYear = date('Y');
                        @endphp
                        @for ($year = $currentYear; $year >= $currentYear - 5; $year--)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-auto">
                    <button id="btnSearchSummartSalesReport" class="btn btn-primary btn-sm mt-4">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">

                    <table id="financialReportTable">
                        <thead>
                            <tr>
                                <th rowspan="2">Bulan</th>
                                <th rowspan="2">OMSET</th>
                                <th rowspan="2">TARGET</th>
                                <th rowspan="2">NET SALES</th>
                                <th rowspan="2">TAX</th>
                                <th rowspan="2">SERVICE CHARGE</th>
                                <th colspan="2">20% TANPA SYARAT</th>
                                <th colspan="2">20% BERSYARAT</th>
                                <th rowspan="2">L&B</th>
                                <th colspan="3">GAJI</th>
                                <th rowspan="2">AVG/BILL</th>
                                <th rowspan="2">AVG/GUEST</th>
                                <th rowspan="2">QTY FOOD</th>
                                <th rowspan="2">QTY BEVERAGE</th>
                                <th rowspan="2">TOTAL F&B</th>
                            </tr>
                            <tr>
                                <th>OPERASIONAL</th>
                                <th>OFFICE</th>
                                <th>OPERASIONAL</th>
                                <th>OFFICE</th>
                                <th>OPERASIONAL</th>
                                <th>BILL</th>
                                <th>GUEST</th>
                            </tr>
                        </thead>
                        <tbody id="tableBodyData">
                            </tbody>
                        <tfoot id="tableFooterSummary">
                            </tfoot>
                    </table>
                </div>
            </div>   
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowgroup/1.3.1/js/dataTables.rowGroup.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>


<script>
    // point Rekap Daily Sales start
    $('#btnSearch').on('click', function () {
        const outlet = $('#outlet').val();
        const bulan  = $('#bulan').val();
        const tahun  = $('#tahun').val();

        if (!outlet) {
            alert('Pilih outlet terlebih dahulu!');
            return;
        }

        $.ajax({
            url: '/dashboard-accounting/data',
            data: { outlet, bulan, tahun },
            success: function (res) {
                // console.log(res);

                // Hitung total target & total realisasi
                const totalTarget = res.data.reduce((sum, row) => sum + (parseInt(row.target_sales) || 0), 0);
                const totalRealisasi = res.data.reduce((sum, row) => sum + (parseInt(row.sales) || 0), 0);

                // Hitung jumlah hari dengan realisasi = 0
                const hariKosong = res.data.filter(row => (parseInt(row.sales) || 0) === 0).length || 1;

                let html = '';
                res.data.forEach(function(row, i) {
                    const tgl = new Date(row.tanggal);
                    const hari = tgl.toLocaleDateString('id-ID', { weekday: 'long' });
                    const bill = parseInt(row.bill) || 0;
                    const sales = parseInt(row.sales) || 0;
                    const nett_sales = parseInt(row.nett_sales) || 0;
                    const target = parseInt(row.target_sales) || 0;
                    const guest = parseInt(row.pax) || 0;
                    const avgBill = parseInt(row.avg_bill) || 0;
                    const avgGuest = parseInt(row.avg_guest) || 0;
                    const food = parseInt(row.food) || 0;
                    const beverage = parseInt(row.beverage) || 0;
                    const total_f_b = parseInt(row.food) + parseInt(row.beverage) || 0;
                    let sales_to_go = 0;

                    // Terapkan logika Excel:
                    if (sales === 0) {
                        if ((totalTarget - totalRealisasi) < 0) {
                            sales_to_go = target;
                        } else {
                            sales_to_go = target + ((totalTarget - totalRealisasi) / hariKosong);
                        }
                    } else if (sales > 0) {
                        sales_to_go = sales;
                    } else {
                        sales_to_go = 0;
                    }

                    html += `
                        <tr>
                            <td>${i + 1}</td>
                            <td>${hari}</td>
                            <td>${row.tanggal}</td>
                            <td>${sales.toLocaleString()}</td>
                            <td class="nett-sales" style="display: none;">${nett_sales.toLocaleString()}</td>
                            <td>${target.toLocaleString()}</td>
                            <td>${Math.round(sales_to_go).toLocaleString()}</td>
                            <td>${bill}</td>
                            <td>${guest}</td>
                            <td>${avgBill.toFixed(0).toLocaleString()}</td>
                            <td>${avgGuest.toFixed(0).toLocaleString()}</td>
                            <td>${food.toLocaleString()}</td>
                            <td>${beverage.toLocaleString()}</td>
                            <td>${total_f_b.toLocaleString()}</td>
                        </tr>
                    `;
                });

                $('#tbodyData').html(html);
                hitungTotal();
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('Gagal load data');
            }
        });
        
        // fitur summary start
        $.ajax({
            url: '/dashboard/summary',
            data: { outlet, tahun },
            success: function (res) {
                // bersihkan tabel
                $('#tbodyData2').empty();

                let totalSales = 0;
                let totalTarget = 0;
                let totalBill = 0;
                let totalGuest = 0;
                let totalNettSales = 0;

                const cleanNumber = (txt) => parseFloat(txt.toString().replace(/[^\d.-]/g, '')) || 0;

                res.data.forEach(row => {
                    const bulanNama = new Date(tahun, row.bulan - 1).toLocaleString('id-ID', { month: 'long' });

                    totalSales += Number(row.sales);
                    totalTarget += Number(row.target);
                    totalBill += Number(row.bill);
                    totalGuest += Number(row.guest);
                    totalNettSales += Number(row.nett_sales); // ✅ ambil langsung dari data, bukan DOM

                    $('#tbodyData2').append(`
                        <tr>
                            <td class="text-center">${bulanNama}</td>
                            <td class="text-end">${Number(row.sales).toLocaleString()}</td>
                            <td class="text-nett-sales" style="display:none;">${Number(row.nett_sales).toLocaleString()}</td>
                            <td class="text-end">${Number(row.target).toLocaleString()}</td>
                            <td class="text-end">${Number(row.bill).toLocaleString()}</td>
                            <td class="text-end">${Number(row.guest).toLocaleString()}</td>
                            <td class="text-end">${Number(row.avg_bill).toLocaleString()}</td>
                            <td class="text-end">${Number(row.avg_guest).toLocaleString()}</td>
                        </tr>
                    `);
                });

                // render footer total
                $('#tfootData2 tr').html(`
                    <td class="text-center">TOTAL</td>
                    <td class="text-end">${totalSales.toLocaleString()}</td>
                    <td class="text-end" style="display:none;">${totalNettSales.toLocaleString()}</td>
                    <td class="text-end">${totalTarget.toLocaleString()}</td>
                    <td class="text-end">${totalBill.toLocaleString()}</td>
                    <td class="text-end">${totalGuest.toLocaleString()}</td>
                    <td class="text-end">${(totalNettSales / (totalBill || 1)).toLocaleString('id-ID', { maximumFractionDigits: 2 })}</td>
                    <td class="text-end">${(totalNettSales / (totalGuest || 1)).toLocaleString('id-ID', { maximumFractionDigits: 2 })}</td>
                `);
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('Gagal load data');
            }
        });

        // fitur summary end 
    });

    document.addEventListener('DOMContentLoaded', () => {
        const outletSelect = document.getElementById('outlet');
        const outletChoices = new Choices(outletSelect, {
            placeholderValue: 'Pilih Outlet',
            searchPlaceholderValue: 'Cari Outlet',
            removeItemButton: false,
            shouldSort: false
        });

        fetch('/dashboard-accounting/parameter')
            .then(res => res.json())
            .then(data => outletChoices.setChoices(data.data_outlet.map(o => ({
                value: o.nama_sub_branch, label: o.nama_sub_branch
            })), 'value', 'label', true))
            .catch(err => console.error('Gagal load outlet:', err));
        // fetch('/dashboard-accounting/parameter')
            // .then(res => res.json())
            // .then(data => outletChoices.setChoices(data.data_outlet.map(o => ({
            //     value: o.nama_outlet, label: o.nama_outlet
            // })), 'value', 'label', true))
            // .catch(err => console.error('Gagal load outlet:', err));

            // generate hari dan tanggal 
                const tbody = document.getElementById('tbodyData');
                const bulanSelect = document.getElementById('bulan');
                const tahunInput = document.getElementById('tahun');
                const namaHari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                const namaBulan = [
                    'Jan','Feb','Mar','Apr','Mei','Jun',
                    'Jul','Agu','Sep','Okt','Nov','Des'
                ];

                const labelBulan = document.getElementById('labelBulan');
                const labelBulan2 = document.getElementById('labelBulan2');
                const labelTahun1 = document.getElementById('labelTahun1');
                const labelTahun2 = document.getElementById('labelTahun2');

                function generateTable(bulan, tahun) {
                    const jsMonth = bulan - 1; // konversi dari 1–12 ke 0–11
                    const lastDay = new Date(tahun, jsMonth + 1, 0).getDate();
                    let rows = '';

                    for (let i = 1; i <= lastDay; i++) {
                        const date = new Date(tahun, jsMonth, i);
                        const hari = namaHari[date.getDay()];
                        const tanggal = `${String(bulan).padStart(2,'0')}/${String(i).padStart(2,'0')}/${tahun}`;
                        rows += `
                            <tr>
                                <td>${i}</td>
                                <td>${hari}</td>
                                <td>${tanggal}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        `;
                    }
                    tbody.innerHTML = rows;

                    // Update header label
                    labelBulan.textContent = `${namaBulan[jsMonth]}-${tahun}`;
                    labelBulan2.textContent = `${namaBulan[jsMonth]}-${tahun}`;
                    labelTahun1.textContent = tahun;
                    labelTahun2.textContent = tahun;
                }

                // default bulan sekarang (konversi ke 1–12 untuk select)
                const today = new Date();
                bulanSelect.value = (today.getMonth() + 1).toString();
                tahunInput.value = today.getFullYear();
                generateTable(parseInt(bulanSelect.value), parseInt(tahunInput.value));

                // event onchange
                bulanSelect.addEventListener('change', () => {
                    generateTable(parseInt(bulanSelect.value), parseInt(tahunInput.value));
                });
                tahunInput.addEventListener('input', () => {
                    generateTable(parseInt(bulanSelect.value), parseInt(tahunInput.value));
                });
            
    });

    function formatAngka(num) {
        return num.toLocaleString('id-ID', { maximumFractionDigits: 0 });
    }

    function hitungTotal() {
        const cleanNumber = (txt) => parseFloat(txt.replace(/[^\d.-]/g, '')) || 0;

        let totalSales = 0;
        let totalNettSales = 0;
        let totalTargetSales = 0;
        let totalTargetToGo = 0;
        let totalBill = 0;
        let totalGuest = 0;
        let totalFood = 0;
        let totalBeverage = 0;
        let totalFB = 0;

        $('#salesTable tbody tr').each(function () {
            const row = $(this);
            const tds = row.find('td');

            totalSales        += cleanNumber(tds.eq(3).text());
            totalNettSales    += cleanNumber(row.find('td.nett-sales').text());
            totalTargetSales  += cleanNumber(tds.eq(5).text());
            totalTargetToGo   += cleanNumber(tds.eq(6).text());
            totalBill         += cleanNumber(tds.eq(7).text());
            totalGuest        += cleanNumber(tds.eq(8).text());
            totalFood         += cleanNumber(tds.eq(11).text());
            totalBeverage     += cleanNumber(tds.eq(12).text());
            totalFB           += cleanNumber(tds.eq(13).text());
        });

        // ✅ Perhitungan avg sesuai permintaan
        const avgBill = totalNettSales / (totalBill || 1);     // Hindari division by zero
        const avgGuest = totalNettSales / (totalGuest || 1);

        $('#totalSales').text(formatAngka(totalSales));
        $('#totalNettSales').text(formatAngka(totalNettSales));
        $('#totalTargetSales').text(formatAngka(totalTargetSales));
        $('#totalTargetToGo').text(formatAngka(totalTargetToGo));
        $('#totalBill').text(formatAngka(totalBill));
        $('#totalGuest').text(formatAngka(totalGuest));
        $('#totalAvgBill').text(formatAngka(avgBill));
        $('#totalAvgGuest').text(formatAngka(avgGuest));
        $('#totalFood').text(formatAngka(totalFood));
        $('#totalBeverage').text(formatAngka(totalBeverage));
        $('#totalFB').text(formatAngka(totalFB));
    }



// point Rekap Daily Sales END
</script>

<script>
  // Summary Start
    document.addEventListener('DOMContentLoaded', function () {
        const tbody = document.getElementById('tbodyData2');
        const tahunInput = document.getElementById('tahun_summary');
        const outletSelect = document.getElementById('outlet_summary');
        const labelTahun3 = document.getElementById('labelTahun3');
        const labelTahun4 = document.getElementById('labelTahun4');

        const namaBulan = [
            'Januari','Februari','Maret','April','Mei','Juni',
            'Juli','Agustus','September','Oktober','November','Desember'
        ];

        function generateMonthlyTable(tahun) {
            tbody.innerHTML = ''; // Kosongkan isi tabel

            for (let i = 0; i < 12; i++) {
                const row = `
                    <tr class="text-end">
                        <td class="text-start">${namaBulan[i]}</td>
                        <td></td> <!-- Realisasi -->
                        <td></td> <!-- Target -->
                        <td></td> <!-- Bill -->
                        <td></td> <!-- Guest -->
                        <td></td> <!-- AVG/BILL -->
                        <td></td> <!-- AVG/GUEST -->
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            }

            // Update tahun pada header
            if (labelTahun3,labelTahun4) {
                labelTahun3.textContent = tahun;
                 labelTahun4.textContent = tahun;
            }

            // Reset footer TOTAL
            const tfoot = document.getElementById('tfootData');
            if (tfoot) {
                tfoot.innerHTML = `
                    <tr class="fw-bold text-end">
                        <td class="text-center">TOTAL</td>
                        <td></td><td></td><td></td><td></td><td></td><td></td>
                    </tr>
                `;
            }
        }

        
    });

    // $('#btnSearchSummary').on('click', function () {
    //     const outlet = $('#outlet_summary').val();
    //     const tahun  = $('#tahun_summary').val();

    //     if (!outlet) {
    //         alert('Pilih outlet terlebih dahulu!');
    //         return;
    //     }

        
    // });
 
    document.addEventListener('DOMContentLoaded', () => {
        const outletSelect = document.getElementById('outlet_summary');
        const outletChoices = new Choices(outletSelect, {
            placeholderValue: 'Pilih Outlet',
            searchPlaceholderValue: 'Cari Outlet',
            removeItemButton: false,
            shouldSort: false
        });

        fetch('/dashboard-accounting/parameter')
            .then(res => res.json())
            .then(data => outletChoices.setChoices(data.data_outlet.map(o => ({
                value: o.nama_outlet, label: o.nama_outlet
            })), 'value', 'label', true))
            .catch(err => console.error('Gagal load outlet:', err));
            
    });

    // Summary END
</script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // fungsi tabel komparasi start
    $('#btnSearchCompare').on('click', function () {
        const outlet = $('#outlet_compare').val();
        const date = $('#bulan_compare').val();
        const tahun = $('#tahun_compare').val();

        if (!outlet) {
            alert('Pilih outlet terlebih dahulu!');
            return;
        }

        if ($.fn.DataTable.isDataTable('#tableCompareTable')) {
            $('#tableCompareTable').DataTable().destroy();
        }

        $('#tableCompareTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '/dashboard/compare',
                data: { outlet, date, tahun },
                dataSrc: function (json) {
                    // ======== HITUNG RANK BERDASARKAN SALES ========
                    const sorted = [...json.data].sort((a, b) => b.sales - a.sales);
                    sorted.forEach((row, idx) => row.rank = idx + 1);

                    // kembalikan data dalam urutan rank (peringkat 1 di atas)
                    return sorted;
                }
            },
            columns: [
                { data: 'outlet' },
                { data: 'total_salary', render: $.fn.dataTable.render.number(',', '.', 0) }, // GAJI JULI (dummy)
                { data: 'sales', render: $.fn.dataTable.render.number(',', '.', 0) },
                { data: 'nett_sales', render: $.fn.dataTable.render.number(',', '.', 0) },
                { data: 'rank' }, // <- sudah dihitung
                { data: 'target', render: $.fn.dataTable.render.number(',', '.', 0) },
                { data: 'bill' },
                { data: 'guest' },
                { data: 'avg_bill', render: $.fn.dataTable.render.number(',', '.', 0) },
                { data: 'avg_guest', render: $.fn.dataTable.render.number(',', '.', 0) },
                { data: 'qty_fnb' }
            ],
            paging: false,
            searching: false,
            ordering: false,
            rowCallback: function (row, data, index) {
                const maxRank = this.api().data().length;
                const ratio = (data.rank - 1) / (maxRank - 1 || 1); // 0=rank1 → 1=rank terakhir
                const r = Math.round(255 * ratio);
                const g = Math.round(255 * (1 - ratio));
                const color = `rgba(${r},${g},0,0.3)`; // <- gunakan rgba

                $(row).css('background-color', color);
            }
        });
    });

    flatpickr("#date_range_compare", {
        mode: "range",
        dateFormat: "Y-m-d",
        locale: {
            firstDayOfWeek: 1 // Mulai dari Senin
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const outletSelect = document.getElementById('outlet_compare');

        const outletChoices = new Choices(outletSelect, {
            placeholderValue: 'Pilih Brand',
            searchPlaceholderValue: 'Cari Brand',
            removeItemButton: true,
            shouldSort: false,
            maxItemCount: 1,
            duplicateItemsAllowed: false,
            itemSelectText: '', // hilangkan teks pilih

        });

        fetch('/dashboard-accounting/parameter-brand')
            .then(res => res.json())
            .then(data => {
                outletChoices.setChoices(
                    data.data_brand.map(o => ({
                        value: o.nama_brand,
                        label: o.nama_brand
                    })),
                    'value',
                    'label',
                    true
                );
            })
            .catch(err => console.error('Gagal load brand:', err));
    });
    // fungsi tabel komparasi end 

</script>

<script>
    // fungsi tabel Rank

    $('#btnSearchRank').on('click', function () {
        const outlet = $('#outlet_rank').val();
        const date = $('#bulan_rank').val();
        const tahun = $('#tahun_rank').val();

        if (!outlet) {
            alert('Pilih outlet terlebih dahulu!');
            return;
        }

        if ($.fn.DataTable.isDataTable('#rankingTable')) {
            $('#rankingTable').DataTable().destroy();
        }

        $('#rankingTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '/dashboard/rank',
                data: { outlet, date, tahun },
                dataSrc: function (json) {
                    // ======== HITUNG RANK BERDASARKAN SALES ========
                    const sorted = [...json.data].sort((a, b) => b.sales - a.sales);
                    sorted.forEach((row, idx) => row.rank = idx + 1);

                    // kembalikan data dalam urutan rank (peringkat 1 di atas)
                    return sorted;
                }
            },
            columns: [
                { data: 'outlet' },
                { data: 'total_salary', render: $.fn.dataTable.render.number(',', '.', 0) }, // GAJI JULI (dummy)
                { data: 'sales', render: $.fn.dataTable.render.number(',', '.', 0) },
                { data: 'nett_sales', render: $.fn.dataTable.render.number(',', '.', 0) },
                { data: 'rank' }, // <- sudah dihitung
                { data: 'target', render: $.fn.dataTable.render.number(',', '.', 0) },
                { data: 'bill' },
                { data: 'guest' },
                { data: 'avg_bill', render: $.fn.dataTable.render.number(',', '.', 0) },
                { data: 'avg_guest', render: $.fn.dataTable.render.number(',', '.', 0) },
                { data: 'qty_fnb' }
            ],
            paging: false,
            searching: false,
            ordering: false,
            rowCallback: function (row, data, index) {
                const maxRank = this.api().data().length;
                const ratio = (data.rank - 1) / (maxRank - 1 || 1); // 0=rank1 → 1=rank terakhir
                const r = Math.round(255 * ratio);
                const g = Math.round(255 * (1 - ratio));
                const color = `rgba(${r},${g},0,0.3)`; // <- gunakan rgba

                $(row).css('background-color', color);
            }
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const outletSelect = document.getElementById('outlet_rank');

        const outletChoices = new Choices(outletSelect, {
            placeholderValue: 'Pilih Brand',
            searchPlaceholderValue: 'Cari Brand',
            removeItemButton: true,
            shouldSort: false,
            maxItemCount: 1,
            duplicateItemsAllowed: false,
            itemSelectText: '', // hilangkan teks pilih

        });

        fetch('/dashboard-accounting/parameter-brand')
            .then(res => res.json())
            .then(data => {
                outletChoices.setChoices(
                    data.data_brand.map(o => ({
                        value: o.nama_brand,
                        label: o.nama_brand
                    })),
                    'value',
                    'label',
                    true
                );
            })
            .catch(err => console.error('Gagal load brand:', err));
    });

    // fungsi tabel Rank end 
</script>

<script>
    // fungsi analisa item/menu start
    $('#btnSearchAnalisa').on('click', function (e) {
        e.preventDefault();

        let outlet = $('#outlet_analisa').val();
        let bulan  = $('#bulan_analisa').val();
        let tahun  = $('#tahun_analisa').val();

        if (!outlet || !bulan || !tahun) {
            alert('Semua filter wajib dipilih!');
            return;
        }

        $('#analisaItemTable').html('<p>Loading data...</p>');

        $.ajax({
            url: '/dashboard-accounting/analisa-item',
            type: 'GET',
            dataType: 'json',
            data: { outlet: outlet, bulan: bulan, tahun: tahun },
            success: function (res) {
                if (!res.data || !Array.isArray(res.data)) {
                    $('#analisaItemTable').html('<p class="text-danger">Format data salah</p>');
                    return;
                }

                $('#analisaItemTable').empty();

                const kategoriList = [...new Set(res.data.map(row => row.kategori))];

                kategoriList.forEach(function (kategori) {
                    let tableId = 'table_' + kategori.replace(/[^a-zA-Z0-9]/g, '_');

                    let tableHtml = `
                        <h5 class="mt-4 fw-bold">Menu Category Detail: 
                            <span style="background-color: #28a745; color: white; padding: 3px 8px; border-radius: 5px; font-size: 0.875rem;">
                                ${kategori}
                            </span>
                        </h5>
                        <div class="table-responsive">
                            <table id="${tableId}" class="table table-bordered table-striped w-100">
                                <thead class="table-success">
                                    <tr>
                                        <th>No</th>
                                        <th>KODE</th>
                                        <th>NAMA MENU</th>
                                        <th>QTY</th>
                                        <th>COST</th>
                                        <th>TOTAL COST</th>
                                        <th>PRICE LIST</th>
                                        <th>TOTAL SALES</th>
                                        <th>% COST</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot class="fw-bold table-secondary">
                                    <tr>
                                        <td colspan="3">TOTAL ${kategori}</td>
                                        <td class="total-qty text-end"></td>
                                        <td class="total-cost text-end"></td>
                                        <td class="total-cost text-end"></td>
                                        <td class="total-price-list text-end"></td>
                                        <td class="total-sales text-end"></td>
                                        <td class="total-pcost text-end"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    `;
                    $('#analisaItemTable').append(tableHtml);

                    let dataKategori = res.data.filter(row => row.kategori === kategori);

                    dataKategori = dataKategori.map(row => ({
                        ...row,
                        cost: Number(row.cost) || 0,
                        total_cost: Number(row.total_cost) || 0,
                        price_list: Number(row.price_list) || Number(row.price) || 0,
                        total_sales: Number(row.total_sales) || 0,
                        percent_cost: (row.percent_cost !== null && row.percent_cost !== undefined) 
                                        ? (parseFloat(row.percent_cost) || 0).toFixed(2) + '%' 
                                        : '0%'
                    }));

                    $('#' + tableId).DataTable({
                        data: dataKategori,
                        paging: false,
                        searching: false,
                        ordering: false,
                        info: false,
                        columns: [
                            { data: null, render: (d, t, r, m) => m.row + 1 },
                            { data: 'kode' },
                            { data: 'nama_menu' },
                            { data: 'qty', className: "text-right" },
                            { data: 'cost', render: d => d.toLocaleString('id-ID'), className: "text-right"},
                            { data: 'total_cost', render: d => d.toLocaleString('id-ID'), className: "text-right" },
                            { 
                                data: 'price_list', 
                                render: d => Number(d || 0).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 }), 
                                className: "text-right" 
                            },
                            { data: 'total_sales', render: d => d.toLocaleString('id-ID'), className: "text-right" },
                            { data: 'percent_cost', className: "text-right" }
                        ],
                        footerCallback: function (row, data, start, end, display) {
                            let api = this.api();

                            const sumColumn = (index) => {
                                return api.column(index, { page: 'current' }).data()
                                    .reduce((a, b) => {
                                        let clean = typeof b === 'string' ? b.replace(/[^0-9.-]+/g, '') : b;
                                        return a + parseFloat(clean || 0);
                                    }, 0);
                            };

                            let totalQty = sumColumn(3);
                            let totalPriceList = sumColumn(6);
                            let totalSales = sumColumn(7);
                            let totalCost = sumColumn(5);
                            let percentCost = totalSales > 0 ? ((totalCost / totalSales) * 100).toFixed(2) + '%' : '0%';

                            $(api.column(3).footer()).html(totalQty.toLocaleString('id-ID'));
                            $(api.column(4).footer()).html(''); // kosongkan COST di row total
                            $(api.column(5).footer()).html(totalCost.toLocaleString('id-ID'));
                            $(api.column(6).footer()).html('');
                            $(api.column(7).footer()).html(totalSales.toLocaleString('id-ID'));
                            $(api.column(8).footer()).html(percentCost);
                        }
                    });
                });

                // Ringkasan total langsung dari controller
                let summaryHtml = `
                    <h5 class="mt-5 fw-bold text-primary">Ringkasan Total</h5>
                    <table class="table table-bordered table-striped w-100" id="total_analisa_tabel">
                        <thead class="table-success">
                            <tr>
                                <th>Jenis Total</th>
                                <th>QTY</th>
                                <th>TOTAL COST</th>
                                <th>TOTAL SALES</th>
                                <th>% COST</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>TOTAL BEVERAGE</td>
                                <td style="text-align: right;">${(res.total_beverage || 0).toLocaleString('id-ID')}</td>
                                <td style="text-align: right;">${(res.total_cost_beverage || 0).toLocaleString('id-ID')}</td>
                                <td style="text-align: right;">${(res.total_sales_beverage || 0).toLocaleString('id-ID')}</td>
                                <td style="text-align: right;">
                                    ${res.total_sales_beverage ? ((res.total_cost_beverage / res.total_sales_beverage) * 100).toFixed(2) + '%' : '0%'}
                                </td>
                            </tr>
                            <tr>
                                <td>TOTAL FOOD</td>
                                <td style="text-align: right;">${(res.total_food || 0).toLocaleString('id-ID')}</td>
                                <td style="text-align: right;">${(res.total_cost_food || 0).toLocaleString('id-ID')}</td>
                                <td style="text-align: right;">${(res.total_sales_food || 0).toLocaleString('id-ID')}</td>
                                <td style="text-align: right;">
                                    ${res.total_sales_food ? ((res.total_cost_food / res.total_sales_food) * 100).toFixed(2) + '%' : '0%'}
                                </td>
                            </tr>
                            <tr>
                                <td>TOTAL OTHER</td>
                                <td style="text-align: right;">${(res.total_other || 0).toLocaleString('id-ID')}</td>
                                <td style="text-align: right;">${(res.total_cost_other || 0).toLocaleString('id-ID')}</td>
                                <td style="text-align: right;">${(res.total_sales_other || 0).toLocaleString('id-ID')}</td>
                                <td style="text-align: right;">
                                    ${res.total_sales_other ? ((res.total_cost_other / res.total_sales_other) * 100).toFixed(2) + '%' : '0%'}
                                </td>
                            </tr>
                            <tr>
                                <td>GRAND TOTAL</td>
                                <td style="text-align: right;">
                                    ${( (res.total_beverage || 0) + (res.total_food || 0) + (res.total_other || 0) ).toLocaleString('id-ID')}
                                </td>
                                <td style="text-align: right;">
                                    ${( (res.total_cost_beverage || 0) + (res.total_cost_food || 0) + (res.total_cost_other || 0) ).toLocaleString('id-ID')}
                                </td>
                                <td style="text-align: right;">
                                    ${( (res.total_sales_beverage || 0) + (res.total_sales_food || 0) + (res.total_sales_other || 0) ).toLocaleString('id-ID')}
                                </td>
                                <td style="text-align: right;">
                                    ${((res.total_sales_beverage || 0) + (res.total_sales_food || 0) + (res.total_sales_other || 0))
                                        ? (
                                            (
                                                (res.total_cost_beverage || 0) +
                                                (res.total_cost_food || 0) +
                                                (res.total_cost_other || 0)
                                            ) /
                                            (
                                                (res.total_sales_beverage || 0) +
                                                (res.total_sales_food || 0) +
                                                (res.total_sales_other || 0)
                                            ) * 100
                                        ).toFixed(2) + '%'
                                        : '0%'}
                                </td>
                            </tr>
                            <tr>
                                <td>TOTAL FOOD & BEVERAGE</td>
                                <td style="text-align: right;">
                                    ${( (res.total_beverage || 0) + (res.total_food || 0) ).toLocaleString('id-ID')}
                                </td>
                                <td style="text-align: right;">
                                    ${( (res.total_cost_beverage || 0) + (res.total_cost_food || 0) ).toLocaleString('id-ID')}
                                </td>
                                <td style="text-align: right;">
                                    ${( (res.total_sales_beverage || 0) + (res.total_sales_food || 0) ).toLocaleString('id-ID')}
                                </td>
                                <td style="text-align: right;">
                                    ${((res.total_sales_beverage || 0) + (res.total_sales_food || 0))
                                        ? (
                                            (
                                                (res.total_cost_beverage || 0) +
                                                (res.total_cost_food || 0)
                                            ) /
                                            (
                                                (res.total_sales_beverage || 0) +
                                                (res.total_sales_food || 0)
                                            ) * 100
                                        ).toFixed(2) + '%'
                                        : '0%'}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                `;
                $('#analisaItemTable').append(summaryHtml);
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                $('#analisaItemTable').html('<p class="text-danger">Gagal mengambil data</p>');
            }
        });

        // ajax call versi pertama 
        // $.ajax({
        //     url: '/dashboard-accounting/analisa-item',
        //     type: 'GET',
        //     dataType: 'json',
        //     data: { outlet: outlet, bulan: bulan, tahun: tahun },
        //     success: function (res) {
        //         if (!res.data || !Array.isArray(res.data)) {
        //             $('#analisaItemTable').html('<p class="text-danger">Format data salah</p>');
        //             return;
        //         }

        //         $('#analisaItemTable').empty();

        //         let kategoriList = [...new Set(res.data.map(row => row.kategori))];

        //         kategoriList.forEach(function (kategori) {
        //             let tableId = 'table_' + kategori.replace(/[^a-zA-Z0-9]/g, '_');

        //             let tableHtml = `
        //                 <h5 class="mt-4 fw-bold"> Menu Category Detail : <span style="background-color: #28a745;color: white;padding: 3px 8px;border-radius: 5px;font-size: 0.875rem;">${kategori}</span></h5>
        //                 <table id="${tableId}" class="table table-bordered table-striped w-100">
        //                     <thead class="table-success">
        //                         <tr>
        //                             <th>No</th>
        //                             <th>KODE</th>
        //                             <th>NAMA MENU</th>
        //                             <th>QTY</th>
        //                             <th>COST</th>
        //                             <th>TOTAL COST</th>
        //                             <th>PRICE LIST</th>
        //                             <th>TOTAL SALES</th>
        //                             <th>% COST</th>
        //                         </tr>
        //                     </thead>
        //                     <tbody></tbody>
        //                     <tfoot class="fw-bold table-secondary">
        //                         <tr>
        //                             <td colspan="3">TOTAL ${kategori}</td>
        //                             <td class="total-qty"></td>
        //                             <td class="total-cost"></td>
        //                             <td class="total-cost"></td>
        //                             <td class="total-price-list"></td>
        //                             <td class="total-sales">0</td>
        //                             <td class="total-pcost">0%</td>
        //                         </tr>
        //                     </tfoot>
        //                 </table>
        //             `;
        //             $('#analisaItemTable').append(tableHtml);

        //             let dataKategori = res.data.filter(row => row.kategori === kategori);
        //             let totalQty = 0;
        //             let totalPriceList = 0;

        //             // Inject dummy values for cost-related fields
        //             dataKategori = dataKategori.map(row => ({
        //                 ...row,
        //                 cost: 0,
        //                 total_cost: 0,
        //                 // price_list: 0,
        //                 // total_sales: 0,
        //                 percent_cost: '0%'
        //             }));

        //             $('#' + tableId).DataTable({
        //                 data: dataKategori,
        //                 paging: false,
        //                 searching: false,
        //                 ordering: false,
        //                 info: false,
        //                 columns: [
        //                     { data: null, render: (d,t,r,m) => m.row + 1 },
        //                     { data: 'kode' },
        //                     { data: 'nama_menu' },
        //                     { data: 'qty', render: d => { totalQty += d; return d; } },
        //                     { data: 'cost' },
        //                     { data: 'total_cost', render: d => d.toLocaleString() },
        //                     { data: 'price_list', render: d => { totalPriceList += d; return d; } },
        //                     { data: 'total_sales', render: d => d.toLocaleString() },
        //                     { data: 'percent_cost' }
        //                 ],
        //                 footerCallback: function (row, data, start, end, display) {
        //                     let api = this.api();

        //                     // Total qty
        //                     let totalQty = api
        //                         .column(3, { page: 'current' })
        //                         .data()
        //                         .reduce((a, b) => {
        //                             let clean = typeof b === 'string' ? b.replace(/[^0-9.-]+/g, '') : b;
        //                             return a + parseFloat(clean || 0);
        //                         }, 0);
        //                     let totalSales = api
        //                         .column(7, { page: 'current' })
        //                         .data()
        //                         .reduce((a, b) => {
        //                             let clean = typeof b === 'string' ? b.replace(/[^0-9.-]+/g, '') : b;
        //                             return a + parseFloat(clean || 0);
        //                         }, 0);
        //                     let totalPriceList = api
        //                         .column(6, { page: 'current' })
        //                         .data()
        //                         .reduce((a, b) => {
        //                             let clean = typeof b === 'string' ? b.replace(/[^0-9.-]+/g, '') : b;
        //                             return a + parseFloat(clean || 0);
        //                         }, 0);
        //                     // Update footer
        //                     $(api.column(3).footer()).html(totalQty.toLocaleString());
        //                     $(api.column(6).footer()).html(totalPriceList.toLocaleString());
        //                     $(api.column(7).footer()).html(totalSales.toLocaleString());
        //                 }
        //             });
        //         });
        //     },
        //     error: function (xhr) {
        //         console.error(xhr.responseText);
        //         $('#analisaItemTable').html('<p class="text-danger">Gagal mengambil data</p>');
        //     }
        // });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const outletSelect = document.getElementById('outlet_analisa');
        const outletChoices = new Choices(outletSelect, {
            placeholderValue: 'Pilih Outlet',
            searchPlaceholderValue: 'Cari Outlet',
            removeItemButton: false,
            shouldSort: false
        });

        fetch('/dashboard-accounting/parameter')
            .then(res => res.json())
            .then(data => outletChoices.setChoices(data.data_outlet.map(o => ({
                value: o.nama_sub_branch, label: o.nama_sub_branch
            })), 'value', 'label', true))
            .catch(err => console.error('Gagal load outlet:', err));
            
    });
    //fungsi analisa item/menu end
</script>

<script>
    
   document.addEventListener('DOMContentLoaded', () => {
        const outletSelect = document.getElementById('outlet_summart_sales_report');
        const outletChoices = new Choices(outletSelect, {
            placeholderValue: 'Pilih Outlet',
            searchPlaceholderValue: 'Cari Outlet',
            removeItemButton: false,
            shouldSort: false
        });

        fetch('/dashboard-accounting/parameter')
            .then(res => res.json())
            .then(data => outletChoices.setChoices(
                data.data_outlet.map(o => ({
                    value: o.nama_sub_branch,
                    label: o.nama_sub_branch
                })), 
                'value', 'label', true
            ))
            .catch(err => console.error('Gagal load outlet:', err));
    });

    $('#btnSearchSummartSalesReport').on('click', function () {
        const outlet = $('#outlet_summart_sales_report').val();
        const tahun = $('#tahun_summart_sales_report').val();

        if (!outlet) {
            alert('Pilih outlet terlebih dahulu!');
            return;
        }

        // if ($.fn.DataTable.isDataTable('#financialReportTable')) {
        //     $('#financialReportTable').DataTable().destroy();
        // }

        $('#financialReportTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '/dashboard/summary-sales-report',
                data: function (d) {
                    d.outlet = outlet; // variabel outlet
                    d.tahun  = tahun;  // variabel tahun
                },
                dataSrc: function (json) {
                    // sort berdasarkan nett_sales (atau omset kalau sudah ada)
                    const sorted = [...json.data].sort((a, b) => b.nett_sales - a.nett_sales);
                    return sorted;
                }
            },
            columns: [
                { data: 'bulan' },
                { data: 'omset', render: $.fn.dataTable.render.number(',', '.', 0, ''), defaultContent: '-' },
                { data: 'total_target_per_bulan', render: $.fn.dataTable.render.number(',', '.', 0, ''), defaultContent: '-' },
                { data: 'nett_sales', render: $.fn.dataTable.render.number(',', '.', 0, ''), defaultContent: '-' },
                { data: 'tax', render: $.fn.dataTable.render.number(',', '.', 0, ''), defaultContent: '-' },
                { data: 'service_charge', render: $.fn.dataTable.render.number(',', '.', 0, ''), defaultContent: '-' },
                { data: 'OperasionalTS', render: $.fn.dataTable.render.number(',', '.', 0, ''), defaultContent: '-' },
                { data: 'OfficeTS', render: $.fn.dataTable.render.number(',', '.', 0, ''), defaultContent: '-' },
                { data: 'OperasionalBS', render: $.fn.dataTable.render.number(',', '.', 0, ''), defaultContent: '-' },
                { data: 'OfficeBS', render: $.fn.dataTable.render.number(',', '.', 0, ''), defaultContent: '-' },
                { data: 'LnB', render: $.fn.dataTable.render.number(',', '.', 0, ''), defaultContent: '-' },
                { data: 'gaji_per_bulan', render: $.fn.dataTable.render.number(',', '.', 0, ''), defaultContent: '-' },
                { data: 'bill', render: $.fn.dataTable.render.number(',', '.', 0, ''), defaultContent: '-' },
                { data: 'gaji_guest', render: $.fn.dataTable.render.number(',', '.', 0, ''), defaultContent: '-' },
                { data: 'avg_bill', render: $.fn.dataTable.render.number(',', '.', 0, ''), defaultContent: '-' },
                { data: 'avg_guest', render: $.fn.dataTable.render.number(',', '.', 0, ''), defaultContent: '-' },
                { data: 'qty_food', render: $.fn.dataTable.render.number(',', '.', 0, ''), defaultContent: '-' },
                { data: 'qty_beverage', render: $.fn.dataTable.render.number(',', '.', 0, ''), defaultContent: '-' },
                { data: 'total_fnb', render: $.fn.dataTable.render.number(',', '.', 0, ''), defaultContent: '-' }
            ],
            paging: false,
            searching: false,
            ordering: false
        });
    });
</script>


@endpush
