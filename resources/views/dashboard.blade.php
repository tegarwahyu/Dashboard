@extends('layouts.app')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Perkecil font tabel */
    .dataTables_wrapper table.dataTable {
        font-size: 12px; /* atau 11px sesuai kebutuhan */
    }

    /* Perkecil padding header dan body */
    .dataTables_wrapper table.dataTable th,
    .dataTables_wrapper table.dataTable td {
        padding: 4px 8px; /* default biasanya 8px 10px */
    }

    /* Perkecil font search & dropdown */
    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_filter label,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        font-size: 12px;
    }

    /* Dropdown & input dalam header */
    #tableInformasiPromo2, #brandName.form-select {
        font-size: 0.75rem;
        height: calc(1.5em + 0.5rem + 2px);
        padding: 0.25rem 0.5rem;
    }

    /* Header tabel utama */
    #tableInformasiPromo2, #tableProgramPromo, .sales-menu-performance-report thead th {
        font-size: 0.7rem; 
        white-space: nowrap;
    }

    /* Isi tabel utama */
    #tableInformasiPromo2, #tableProgramPromo, .sales-menu-performance-report tbody td {
        font-size: 0.75rem;
        padding: 0.35rem 0.4rem;
    }

    /* .sales-menu-performance-report{
        font-size: 12px;
    } */
</style>
@section('content')
<div class="container">
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-primary shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-uppercase text-primary small mb-1 fw-bold">
                                Total Outlet
                            </div>
                            <div class="h5 mb-0 fw-bold text-dark" id="totalOutlet">
                                Loading...
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-store fa-2x text-muted"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-primary shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-uppercase text-warning small mb-1 fw-bold">
                                Total Brand
                            </div>
                            <div class="h5 mb-0 fw-bold text-dark" id="totalBrand">
                                Loading...
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-muted"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-success shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-uppercase text-success small mb-1 fw-bold">
                                Total Program
                            </div>
                            <div class="h5 mb-0 fw-bold text-dark" id="totalPromosi">
                                Loading...
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fab fa-adversal fa-2x text-muted"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-success shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-uppercase text-info small mb-1 fw-bold">
                                Total Promo
                            </div>
                            <div class="h5 mb-0 fw-bold text-dark">
                                0
                            </div>
                        </div>
                        <div class="col-auto">
                            <!-- <i class="fab fa-adversal fa-2x text-muted"></i> -->
                            <i class="fas fa-percentage fa-2x text-muted"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <h1 class="mb-4">Dashboard</h1>
</div>
    
<div class="container-fluid">
    

    <div class="container">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Data Promosi</h6>
                <hr>
                <div class="row g-1 align-items-end">
                    <div class="col-auto">
                        <label for="start_date" class="form-label small"><b>Tanggal Mulai</b></label>
                        <input type="date" id="start_date" class="form-control form-control-sm">
                    </div>
                    <div class="col-auto">
                        <label for="end_date" class="form-label small"><b>Tanggal Akhir</b></label>
                        <input type="date" id="end_date" class="form-control form-control-sm">
                    </div>
                    <div class="col-auto">
                        <label for="brand" class="form-label small"><b>Brand</b></label>
                        <select id="brand" class="form-select form-select-sm">
                            <option value="" selected disabled>Pilih Brand</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label for="outlet" class="form-label small"><b>Outlet</b></label>
                        <select id="outlet" class="form-select form-select-sm">
                            <option value="" selected disabled>Pilih Outlet</option>
                        </select>
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
                    <table class="table table-stiped table-bordered dashboard-table" style="width:100%">
                        <thead>
                            <th width="5%">No</th>
                            <th>NAMA PROMOSI</th>
                            <th>BRAND</th>
                            <th>OUTLET</th>
                            <th>SALES PERIODE</th>
                            <!-- <th>PROGRAM PERIODE</th> -->
                            <th>TOTAL QTY</th>
                            <th>TOTAL SALES PROMO</th>
                            <th>TOTAL SALES All</th>
                            <th>TOTAL SALES ALL (%)</th>
                            <th width="10%">GRAFIK</th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="card-body">
                    <!-- Bagian Header Informasi -->
                    <div class="mb-4 container-fluid">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0"><b>DASHBOARD PROGRAM PROMO</b></h4>
                            <button id="exportExcelBtn" class="btn btn-primary btn-sm" title="Export Data ke Excel">
                                <i class="fas fa-file-export"></i>
                            </button>
                        </div>
                        <hr>
                        <!-- BRAND -->
                        <div class="row mb-2 align-items-center">
                            <label for="brandName" class="col-12 col-sm-2 fw-bold">BRAND</label>
                            <div class="col-auto">:</div>
                            <div class="col-5">
                                <select id="brandName" class="form-select form-select-sm">...</select>
                            </div>
                        </div>
                        <!-- MENU KATEGORI -->
                        <div class="row mb-2 align-items-center">
                            <label for="menuCategory" class="col-12 col-sm-2 fw-bold">Pilih Menu Kategori</label>
                            <div class="col-auto">:</div>
                            <div class="col-5">
                                <select id="menuCategory" name="menu_category" class="form-select form-select-sm"></select>
                            </div>
                        </div>

                        <!-- MENU KATEGORI DETAIL -->
                        <div class="row mb-2 align-items-center">
                            <label for="menuCategoryDetail" class="col-12 col-sm-2 fw-bold">Pilih Menu Kategori Detail</label>
                            <div class="col-auto">:</div>
                            <div class="col-5">
                                <select id="menuCategoryDetail" name="menu_category_detail" class="form-select form-select-sm"></select>
                            </div>
                        </div>

                        <!-- multiple select -->
                        <div class="row mb-2 align-items-center">
                            <label for="menu_code" class="col-12 col-sm-2 fw-bold">Pilih Menu Kode</label>
                            <div class="col-auto">:</div>
                            <div class="col-5">
                                <select name="menu_code[]" id="menu_code" class="form-select" multiple required></select>
                            </div>
                        </div>

                        <!-- PERIODE -->
                        <div class="row mb-2 align-items-center" style="display: none;">
                            <label for="programName" class="col-12 col-sm-2 fw-bold">Nama Program/Promosi</label>
                            <div class="col-auto">:</div>
                            <div class="col-5">
                                <select id="programName" class="form-select form-select-sm">...</select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-7 text-end">
                                <button id="btnSearch2" class="btn btn-primary">Cari</button>
                            </div>
                        </div>
                    </div>

                    <!-- Review Program -->
                    <div class="table-responsive">
                        <table class="table table-bordered mb-4" id="tableInformasiPromo2" style="width: 60%; border: 1px solid #000;">
                            <thead class="table-light">
                                <tr>
                                    <th colspan="3">REVIEW PROGRAM</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 25%;"><strong>* Progress Omzet &amp; Pencapaian Target</strong></td>
                                    <td style="width: 2%;">:</td>
                                    <td id="progressOmzet"></td>
                                </tr>
                                <tr>
                                    <td><strong>* Evaluasi Program</strong></td>
                                    <td>:</td>
                                    <td id="evaluasiProgram"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Tabel -->
                    <div class="table-responsive">
                        <table id="tableProgramPromo" class="table table-bordered table-striped w-100">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>OUTLET</th>
                                    <!-- <th>TARGET OMZET (PAKET/DAY)</th> -->
                                    <th title="Silakan isi target omzet per hari untuk setiap outlet">
                                        TARGET OMZET (PAKET/DAY) 🔍
                                    </th>
                                    <th>DETAIL AKTUAL</th>
                                    <th>ACH. PER DAY (%)</th>
                                    <th>ACH. AVG ACT. (%)</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-end">Total</th>
                                    <th id="totalTarget"></th>
                                    <th></th>
                                    <th id="totalAchDay"></th>
                                    <th id="totalAchAvg"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <div class="container">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tabel Menu Performance</h6>
                    <hr>
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-sm-7 col-lg-4">
                            <label for="branch_performance" class="form-label small"><b>Branch</b></label>
                            <select id="branch_performance" name="branch_performance"
                                    class="form-select form-select-sm w-100">
                            <option value="" selected disabled>Pilih Branch</option>
                            </select>
                        </div>

                        <div class="col-6 col-sm-3 col-lg-3">
                            <label for="bulan_performance" class="form-label small"><b>Periode</b></label>
                            <select id="bulan_performance" name="bulan_performance"
                                    class="form-select form-select-sm w-100">
                                <option value="" disabled selected>Pilih Bulan</option>
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

                        <div class="col-6 col-sm-3 col-lg-3">
                            <label for="type_performance" class="form-label small"><b>Type</b></label>
                            <select id="type_performance" name="type_performance"
                                    class="form-select form-select-sm w-100">
                                    <option value="" disabled selected>Pilih Type</option>
                                    <option value="qty">QTY</option>
                                    <option value="value">Value/Total Sales</option>
                            </select>
                        </div>

                        <div class="col-auto">
                            <button id="btnSearchPerformance" class="btn btn-primary btn-sm mt-4">
                            <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="box-body table-responsive">
                        <table class="table table-stiped table-bordered sales-menu-performance-report" style="width:100%">
                            <thead>
                                <th width="5%">No</th>
                                <th>Rank</th>
                                <th>Menu Name</th>
                                <th>Menu Code</th>
                                <th>Menu Category Name</th>
                                <!-- <th>PROGRAM PERIODE</th> -->
                                <th>Menu Category Detail Name</th>
                                <th>Value</th>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- modal tanggal  -->
        <!-- Modal -->
        <div class="modal fade" id="actualModal" tabindex="-1" aria-labelledby="actualModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="actualModalLabel">Detail Actual Per Hari</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table id="modalActualTable" class="table table-bordered table-striped w-100 text-center">
                        <thead>
                            <tr>
                                <th>No</th>
                                <!-- <th>Kode Menu</th> -->
                                <th>Tanggal</th>
                                <th>Target(Harian)</th>
                                <th>Actual (Paket)</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <!-- data diisi via JS -->
                        </tbody>
                        <tfoot>
                            <tr class="table-secondary">
                            <th colspan="2" class="text-end">Total</th>
                            <th id="modalTotalTarget"></th>
                            <th id="modalTotalActual"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
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
    $(document).ready(function () {

        $.get('/dashboard/general-data', function (response) {
            $('#totalOutlet').text(response.total_outlet);
            $('#totalBrand').text(response.total_brand);
            $('#totalPromosi').text(response.total_promosi);
            
        }).fail(function (xhr) {
            console.log('Error:', xhr.responseText);
            $('#totalPromosi').text('Error');
            $('#totalOutlet').text('Error');
            $('#totalBrand').text('Error');
        });

        let table = $('.dashboard-table').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
            url: `{{ route('getDataDashboard') }}`,
                data: function (d) {
                    // Ambil nilai filter
                    d.start_date = $('#start_date').val();
                    d.end_date   = $('#end_date').val();
                    d.promo_name = $('#promo_name').val();
                    d.brand_id   = $('#brand').val();
                    d.outlet_id  = $('#outlet').val();
                },
                dataSrc: function (json) {
                    // console.log('Total Qty:', json.totalQty);
                    $('#totalQtyDisplay').text(json.totalQty?.toLocaleString() ?? 0);
                    return json.data;
                }
            },
                columns: [
                { data: 'DT_RowIndex', searchable: false, sortable: false },
                { data: 'judul_promosi' },
                { data: 'brand' },
                { data: 'nama_outlet' },
                { data: 'date_periode' },
                // {
                //     data: null, // kita ambil dari dua field: mulai dan akhir
                //     name: 'promo_period',
                //     render: function(data, type, row) {
                //         return `${row.mulai} s/d ${row.akhir}`;
                //     }
                // },
                { data: 'total_qty' },
                {
                    data: 'total_sales_promo'
                },
                {
                    data: 'total_sales_all'
                },
                {
                    data: 'total_sales_all_percent'
                },
                // { data: 'outlet.nama_outlet' },
                { data: 'aksi', searchable: false, sortable: false }
            ],
            order: [[1, 'asc']]
        });

        // Load brand saat pertama kali
        $.ajax({
            url: '/get-brands', // route untuk ambil brand
            method: 'GET',
            success: function(data) {
                $('#brand').append(data.map(b => `<option value="${b.id}">${b.nama_brand}</option>`));
            }
        });

        // Saat brand berubah → load outlet
        $('#brand').on('change', function() {
            let brandId = $(this).val();
            $('#outlet').html('<option value="">Pilih Outlet</option>'); // reset

            if(brandId){
                $.ajax({
                    url: '/get-outlets?brand=' + brandId, // route untuk ambil outlet
                    method: 'GET',
                    success: function(data) {
                        data.forEach(o => {
                            $('#outlet').append(`<option value="${o.id}">${o.nama_outlet}</option>`);
                        });
                    }
                });
            }
        });

        // Tombol cari → reload DataTables
        $('#btnSearch').on('click', function(){
            table.ajax.reload();
        });

    });

    document.addEventListener('DOMContentLoaded', function () {

        if ($.fn.DataTable.isDataTable('#tableProgramPromo')) {
            $('#tableProgramPromo').DataTable().destroy();
        }
        
        const tableProgramPromo = $('#tableProgramPromo').DataTable({
            columns: [
                { data: 'no' },
                { data: 'outlet' },
                { data: 'target' },
                { data: 'detail' },
                { data: 'ach_day' },
                { data: 'ach_avg' }
            ]
        });

        // Fungsi untuk mengelola status tombol ekspor
        function toggleExportButton() {
            const tableData = tableProgramPromo.rows().data();
            const exportBtn = $('#exportExcelBtn');
            if (tableData.length === 0) {
                exportBtn.prop('disabled', true);
                exportBtn.attr('title', 'Tidak ada data untuk diekspor');
            } else {
                exportBtn.prop('disabled', false);
                exportBtn.attr('title', 'Export Data ke Excel');
            }
        }
        toggleExportButton();
        tableProgramPromo.on('draw.dt', function () {
            toggleExportButton();
        });

       // --- EXPORT CSV (pivot) — tambah kolom Nama Menu; dukung data flat & nested ---
        $('#exportExcelBtn').on('click', function () {
            const rawData = tableProgramPromo.rows().data().toArray();
            if (!rawData.length) {
                alert('Tidak ada data yang tersedia untuk diekspor.');
                return;
            }

            // Ambil nomor tanggal (1..31) dari 'YYYY-MM-DD'
            const getDayNum = (s) => {
                if (!s) return null;
                const m = String(s).match(/^(\d{4})-(\d{2})-(\d{2})/);
                if (m) return parseInt(m[3], 10);
                const d = new Date(s);
                return isNaN(d) ? null : d.getUTCDate();
            };

            // Kumpulkan semua hari yang muncul di seluruh rows
            const daySet = new Set();

            // Helper: bangun peta (code -> day->qty) dan (code -> nama_menu)
            function buildCodeMaps(details) {
                const codeDayMap  = new Map(); // Map<string, Map<number, number>>
                const codeNameMap = new Map(); // Map<string, string>

                if (!Array.isArray(details)) return { codeDayMap, codeNameMap };

                const isNested = details.length && details[0] && typeof details[0] === 'object' && 'menus' in details[0];

                if (isNested) {
                details.forEach(d => {
                    const day = getDayNum(d.sales_date);
                    if (!day) return;
                    daySet.add(day);
                    (d.menus || []).forEach(m => {
                    const code = String(m.menu_code || '').trim();
                    const name = String(m.nama_menu || m.menu_name || '').trim();
                    const qty  = Number(m.jumlah_menu || 0);
                    if (!code) return;

                    const dayMap = codeDayMap.get(code) || new Map();
                    dayMap.set(day, (dayMap.get(day) || 0) + qty);
                    codeDayMap.set(code, dayMap);

                    if (name && !codeNameMap.has(code)) codeNameMap.set(code, name);
                    });
                });
                } else {
                // Flat: [{sales_date, menu_code, nama_menu/menu_name, jumlah_menu}, ...]
                details.forEach(d => {
                    const day  = getDayNum(d.sales_date);
                    const code = String(d.menu_code || '').trim();
                    const name = String(d.nama_menu || d.menu_name || '').trim();
                    const qty  = Number(d.jumlah_menu || 0);
                    if (!day || !code) return;

                    daySet.add(day);
                    const dayMap = codeDayMap.get(code) || new Map();
                    dayMap.set(day, (dayMap.get(day) || 0) + qty);
                    codeDayMap.set(code, dayMap);

                    if (name && !codeNameMap.has(code)) codeNameMap.set(code, name);
                });
                }
                return { codeDayMap, codeNameMap };
            }

            // Build maps untuk tiap row
            const perRowMaps = rawData.map(r => buildCodeMaps(r.detail_harian));

            const dayCols = Array.from(daySet).sort((a,b)=>a-b);
            if (!dayCols.length) {
                alert('Tidak ada tanggal pada data untuk diekspor.');
                return;
            }

            // Header (tambah "Nama Menu")
            const headers = [
                "Outlet",
                "Kode Menu",
                "Nama Menu",
                "Target",
                ...dayCols.map(d => `Hari ke ${d}`),
                "Achievement Day (%)",
                "Achievement Avg (%)",
            ];
            const exportData = [headers];

            // Baris per outlet × kode
            rawData.forEach((r, idx) => {
                const outlet = r.outlet || r.nama_outlet || '-';
                const target = Number(r.qty_target || 0);
                const { codeDayMap, codeNameMap } = perRowMaps[idx];

                if (!codeDayMap || codeDayMap.size === 0) {
                exportData.push([outlet, "", "", target, ...dayCols.map(()=>0), 0, 0]);
                return;
                }

                Array.from(codeDayMap.keys()).sort().forEach(code => {
                const name    = codeNameMap.get(code) || "";   // ← ambil nama_menu bila ada
                const dayMap  = codeDayMap.get(code) || new Map();
                const dayVals = dayCols.map(d => Number(dayMap.get(d) || 0));

                // Ach Day: last non-zero / target * 100
                let lastActual = 0;
                for (let i = dayVals.length - 1; i >= 0; i--) {
                    if (dayVals[i] > 0) { lastActual = dayVals[i]; break; }
                }
                const achDay = target > 0 ? (lastActual / target) * 100 : 0;

                // Ach Avg: average(values > 0) / target * 100
                const positives = dayVals.filter(v => v > 0);
                const avgActual = positives.length ? positives.reduce((a,b)=>a+b,0) / positives.length : 0;
                const achAvg = target > 0 ? (avgActual / target) * 100 : 0;

                exportData.push([outlet, code, name, target, ...dayVals, achDay, achAvg]);
                });
            });

            // Generate CSV
            const csvContent = "data:text/csv;charset=utf-8," + exportData
                .map(row => row.map(item => {
                const value = Array.isArray(item) ? item.join(';') : item;
                return (typeof value === 'string' && (value.includes(',') || value.includes('"') || value.includes('\n')))
                    ? `"${value.replace(/"/g, '""')}"`
                    : value;
                }).join(","))
                .join("\n");

            const a = document.createElement("a");
            a.href = encodeURI(csvContent);
            a.download = "program_promo_pivot.csv";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        });


        const brandSelect = document.getElementById('brandName');
        const programSelect = document.getElementById('programName');

        // 🔥 Inisialisasi Choices
        const brandChoices = new Choices(brandSelect, {
            placeholderValue: '-- Pilih Brand --',
            searchPlaceholderValue: 'Cari Brand',
            shouldSort: false,
            removeItemButton: false,
        });

        const programChoices = new Choices(programSelect, {
            placeholderValue: '-- Pilih Program --',
            searchPlaceholderValue: 'Cari Program',
            shouldSort: false,
            removeItemButton: false,
        });

        // 🚀 Fetch and populate Brand dropdown
        fetch('/get-brand-list')
            .then(res => res.json())
            .then(brands => {
                const brandItems = brands.map(brand => ({
                    value: brand,
                    label: brand
                }));

                brandChoices.setChoices(brandItems, 'value', 'label', true);
            })
            .catch(err => console.error('Gagal load brand:', err));

        // 🎯 Listener untuk brand change → ambil program
        // brandSelect.addEventListener('change', function () {
        //     const selectedBrand = this.value;

        //     // Kosongkan pilihan program sebelumnya
        //     programChoices.clearStore();

        //     if (!selectedBrand) return;

        //     fetch(`/get-promotions-by-brand/${encodeURIComponent(selectedBrand)}`)
        //         .then(res => res.json())
        //         .then(promotions => {
        //             const promoItems = promotions.map(promo => ({
        //                 value: promo,
        //                 label: promo
        //             }));

        //             programChoices.setChoices(promoItems, 'value', 'label', true);
        //         })
        //         .catch(err => console.error('Gagal load program:', err));
        // });

        // 🔥 Listener untuk perubahan program
        // programSelect.addEventListener('change', function () {
        //     const selectedProgram = this.value;
        //     const SelectBrand = $('#brandName').val();
        //     if (!selectedProgram) return;

        //     const params = new URLSearchParams({ program: selectedProgram, brand: SelectBrand });

        //     fetch(`/dashboard/get-program-dashboard?${params}`)
        //         .then(res => res.json())
        //         .then(response => {
        //             const data = response.data;
        //             if (!Array.isArray(data)) {
        //                 console.error('Data bukan array:', data);
        //                 return;
        //             }

        //             const formatted = data.map((item, index) => ({
        //                 no: index + 1,
        //                 outlet: item.nama_outlet || '-',
        //                 target: item.qty_target || 0,
        //                 detail: item.detail || '-',
        //                 ach_day: parseFloat(String(item.ach_day).replace('%','')) || 0, // bisa kamu hitung kalau ada data aktual
        //                 ach_avg: parseFloat(String(item.ach_avg).replace('%','')) || 0
        //             }));

        //             tableProgramPromo.clear().rows.add(formatted).draw();

        //             // hitung total
        //             let totalTarget = formatted.reduce((sum, row) => sum + row.target, 0);
        //             let totalAchDay = formatted.reduce((sum, row) => sum + row.ach_day, 0);
        //             let totalAchAvg = formatted.reduce((sum, row) => sum + row.ach_avg, 0);

        //             // update footer
        //             $('#totalTarget').text(totalTarget);
        //             // $('#totalAchDay').text(totalAchDay.toFixed(1) + '%');
        //             // $('#totalAchAvg').text(totalAchAvg.toFixed(1) + '%');
        //         })
        //         .catch(err => console.error('Gagal load data program:', err));
        // });
        
        // brandSelect.addEventListener('change', function () {
        //     const selectedBrand = this.value;
        //     if (!selectedBrand) return;

        //     const params = new URLSearchParams({ brand: selectedBrand });

        //     fetch(`/dashboard/get-program-dashboard2?${params}`)
        //         .then(res => res.json())
        //         .then(response => {
        //             const data = response.data;
        //             if (!Array.isArray(data)) {
        //                 console.error('Data bukan array:', data);
        //                 return;
        //             }

        //             const formatted = data.map((item, index) => {
        //                 // Nilai target sekarang berupa string
        //                 const qtyTargetString = item.qty_target || '';
                        
        //                 // Mengubah input menjadi type="text" agar bisa menerima string
        //                 return {
        //                     no: index + 1,
        //                     outlet: item.nama_outlet || '-',
        //                     target: `<input type="number" class="form-control target-input" 
        //                             value="${qtyTargetString}" data-row="${index}" min="0" />`,
        //                     detail: item.detail || '-',
        //                     ach_day: 0, // Awalnya 0, karena perhitungan akan dilakukan di event listener
        //                     ach_avg: 0, // Awalnya 0, karena perhitungan akan dilakukan di event listener
        //                     detail_harian: item.detail_harian || [],
        //                     qty_target: qtyTargetString // Menyimpan nilai string
        //                 };
        //             });

        //             tableProgramPromo.clear().rows.add(formatted).draw();
        //             updateFooter();
        //         })
        //         .catch(err => console.error('Gagal load data program:', err));
        // });

        const menuCodeSelect = document.getElementById('menu_code');
        let menuCodeChoices;
        let menuMap = [];

        // Load choices untuk menu_code
        fetch('/dashboard/get-menu-kode')
            .then(response => response.json())
            .then(data => {
                menuMap = data;
                const codeChoices = data.map(item => ({
                    value: item.menu_code,
                    label: `${item.menu_code} - ${item.menu_name}`
                }));

                menuCodeChoices = new Choices(menuCodeSelect, {
                    removeItemButton: true,
                    placeholderValue: 'Pilih Menu Code',
                    searchPlaceholderValue: 'Cari Menu Code',
                    choices: codeChoices
                });
            });
    


        // ==== MENU KATEGORI & DETAIL (baru) ====
        const menuCategorySelect = document.getElementById('menuCategory');
        const menuCategoryDetailSelect = document.getElementById('menuCategoryDetail');
        // const brandSelect = document.getElementById('brandName');

        const menuCategoryChoices = new Choices(menuCategorySelect, {
            placeholderValue: 'Pilih Menu Kategori',
            searchPlaceholderValue: 'Cari Kategori',
            shouldSort: false,
        });

        const menuCategoryDetailChoices = new Choices(menuCategoryDetailSelect, {
            placeholderValue: 'Pilih Menu Kategori Detail',
            searchPlaceholderValue: 'Cari Kategori Detail',
            shouldSort: false,
        });

        // util reset choices dengan placeholder
        function resetChoices(instance, placeholder) {
            instance.clearChoices();
            instance.setChoices(
                [{ value: '', label: placeholder, selected: true, disabled: true }],
                'value',
                'label',
                true
            );
        }

        // load kategori berdasar brand
        async function loadCategories(brand) {
            resetChoices(menuCategoryChoices, 'Pilih Menu Kategori');
            resetChoices(menuCategoryDetailChoices, 'Pilih Menu Kategori Detail'); // ikut kosongkan detail
            if (!brand) return;

            try {
                // GANTI endpoint ini sesuai route kamu nanti
                const url = `/dashboard/get-menu-category?brand=${encodeURIComponent(brand)}`;
                const list = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(r => r.json());
                const items = list.map(v => ({ value: v, label: v }));
                // inject pilihan
                menuCategoryChoices.setChoices(items, 'value', 'label', true);
            } catch (e) {
                console.error('Gagal load kategori:', e);
            }
        }

        // load kategori detail berdasar brand + kategori
        async function loadCategoryDetails(brand, category) {
            resetChoices(menuCategoryDetailChoices, 'Pilih Menu Kategori Detail');
            if (!brand || !category) return;

            try {
                // GANTI endpoint ini sesuai route kamu nanti
                const url = `/dashboard/get-menu-category-detail?brand=${encodeURIComponent(brand)}&menu_category=${encodeURIComponent(category)}`;
                const list = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(r => r.json());
                const items = list.map(v => ({ value: v, label: v }));
                menuCategoryDetailChoices.setChoices(items, 'value', 'label', true);
            } catch (e) {
                console.error('Gagal load kategori detail:', e);
            }
        }

        // event: ketika brand berubah → muat kategori
        brandSelect.addEventListener('change', () => {
            const brandVal = brandSelect.value;              // FIX: dulu brandChoices.getValue(true)
            loadCategories(brandVal);
        });

        // event: ketika kategori berubah → muat kategori detail
        menuCategorySelect.addEventListener('change', () => {
            const brandVal = brandSelect.value;              // FIX
            const catVal   = menuCategorySelect.value;       // FIX: dulu menuCategoryChoices.getValue(true)
            loadCategoryDetails(brandVal, catVal);
        });

        // Event tombol Cari
        document.getElementById('btnSearch2').addEventListener('click', function () {
            const selectedBrand = brandSelect.value;
            const selectedMenuCodes = menuCodeChoices.getValue(true);
            const selectedMenuCategory = menuCategoryChoices.getValue(true) || menuCategorySelect.value;  // ✅
            const selectedMenuCategoryDetail = menuCategoryDetailChoices.getValue(true) || menuCategoryDetailSelect.value; // ✅

            if (!selectedBrand) {
                alert("Pilih Brand terlebih dahulu!");
                return;
            }
            if (selectedMenuCodes.length === 0) {
                alert("Pilih minimal satu Menu Code!");
                return;
            }

            const params = new URLSearchParams({
                brand: selectedBrand,
                MenuCategory: selectedMenuCategory,
                MenuCategoryDetail: selectedMenuCategoryDetail,
                menu_codes: JSON.stringify(selectedMenuCodes)
            });

            fetch(`/dashboard/get-program-dashboard2?${params}`)
                .then(res => res.json())
                .then(response => {
                    const data = response.data;
                    if (!Array.isArray(data)) {
                        console.error('Data bukan array:', data);
                        return;
                    }

                    const formatted = data.map((item, index) => ({
                        no: index + 1,
                        outlet: item.nama_outlet || '-',
                        target: `<input type="number" class="form-control target-input"
                                        value="${item.qty_target || ''}" data-row="${index}" min="0" />`,
                        detail: item.detail || '-',
                        ach_day: 0,
                        ach_avg: 0,
                        detail_harian: item.detail_harian || [],
                        qty_target: item.qty_target || ''
                    }));

                    tableProgramPromo.clear().rows.add(formatted).draw();
                    updateFooter();
                })
                .catch(err => console.error('Gagal load data program:', err));
        });

       // Event listener untuk input target
        $('#tableProgramPromo tbody').on('input', '.target-input', function () {
            const rowEl = $(this).closest('tr');
            const row = tableProgramPromo.row(rowEl);
            const rowData = row.data();
            
            if (!rowData) return;

            const inputValue = this.value;
            const newTarget = parseFloat(inputValue) || 0;

            // Perbarui nilai qty_target
            rowData.qty_target = newTarget;

            // Perbarui kolom target agar tetap ada input
            rowData.target = `<input type="number" class="form-control target-input" 
                                    value="${newTarget}" data-row="${row.index()}" min="0" />`;

            // --- FIX: Ambil semua jumlah_menu dari detail_harian
            const allActuals = [];
            (rowData.detail_harian || []).forEach(d => {
                (d.menus || []).forEach(m => {
                    allActuals.push(parseFloat(m.jumlah_menu) || 0);
                });
            });

            // Hitung last actual (ambil yang terakhir > 0)
            const lastActual = allActuals.filter(v => v > 0).slice(-1)[0] || 0;

            // Hitung rata-rata
            const validActuals = allActuals.filter(v => v > 0);
            const avgActual = validActuals.length > 0
                            ? validActuals.reduce((a, b) => a + b, 0) / validActuals.length
                            : 0;
            // const avgActual = allActuals.length > 0
            //     ? allActuals.reduce((a, b) => a + b, 0) / allActuals.length
            //     : 0;

            // Hitung achievement
            rowData.ach_day = newTarget > 0 ? Math.round(lastActual / newTarget * 100) : 0;
            rowData.ach_avg = newTarget > 0 ? Math.round(avgActual / newTarget * 100) : 0;

            // Update baris
            row.data(rowData).invalidate().draw(false);

            updateFooter();
        });

        function updateFooter() {
            let totalTarget = 0;
            
            // Iterasi melalui setiap baris data di DataTables
            tableProgramPromo.rows().data().each(function(rowData) {
                // Ambil nilai qty_target dari data objek, bukan dari DOM
                const targetValue = parseFloat(rowData.qty_target) || 0;
                totalTarget += targetValue;
            });

            let totalAchDay = 0;
            let totalAchAvg = 0;
            const rowCount = tableProgramPromo.rows().data().length;
            
            // Perhitungan rata-rata achievement
            if (rowCount > 0) {
                let totalAchDaySum = 0;
                let totalAchAvgSum = 0;
                
                tableProgramPromo.rows().data().each(function(rowData) {
                    totalAchDaySum += parseFloat(rowData.ach_day) || 0;
                    totalAchAvgSum += parseFloat(rowData.ach_avg) || 0;
                });
                
                // --- MODIFIKASI: Bulatkan hasil rata-rata
                totalAchDay = Math.round(totalAchDaySum / rowCount);
                totalAchAvg = Math.round(totalAchAvgSum / rowCount);
            }
            
            // Memastikan elemen ada sebelum memanipulasi
            const totalTargetEl = document.getElementById('totalTarget');
            if (totalTargetEl) {
                // --- MODIFIKASI: Pastikan tidak ada desimal pada target
                totalTargetEl.textContent = totalTarget.toFixed(0);
            }
            
            const totalAchDayEl = document.getElementById('totalAchDay');
            if (totalAchDayEl) {
                // --- MODIFIKASI: Tampilkan nilai yang sudah dibulatkan
                totalAchDayEl.textContent = `${totalAchDay}%`;
            }
            
            const totalAchAvgEl = document.getElementById('totalAchAvg');
            if (totalAchAvgEl) {
                // --- MODIFIKASI: Tampilkan nilai yang sudah dibulatkan
                totalAchAvgEl.textContent = `${totalAchAvg}%`;
            }
        }
    });
    


    $(document).on('click', '.view-actual', function () {
        const actualModal = new bootstrap.Modal(document.getElementById('actualModal'));
        const $modalTableBody = $('#modalActualTable tbody');
        const $modalTitle = $('#actualModalLabel');

        // baris yang diklik
        const $rowEl = $(this).closest('tr');
        const target = parseFloat($rowEl.find('.target-input').val()) || 0;

        const outlet = $(this).data('outlet');
        const menu   = $(this).data('menu');
        let detail   = $(this).data('detail');

        // parse jika string / HTML-encoded
        if (typeof detail === 'string') {
            try {
            detail = JSON.parse(detail);
            } catch {
            const ta = document.createElement('textarea');
            ta.innerHTML = detail;
            try { detail = JSON.parse(ta.value); } catch { detail = []; }
            }
        }
        if (!Array.isArray(detail)) detail = [];

        // title + clear isi & footer
        $modalTitle.text(`Detail Actual ${menu} (${outlet})`);
        $modalTableBody.empty();
        $('#modalTotalTarget').text('0');
        $('#modalTotalActual').text('0');

        // isi rows + hitung total
        let totalTarget = 0;
        let totalActual = 0;

        detail.forEach((item, idx) => {
            const tanggal = item.sales_date || '';
            const actual  = parseFloat(item.jumlah_menu) || 0;

            // target harian: dari input baris (nilai sama per hari)
            totalTarget += target;
            totalActual += actual;

            const $tr = $('<tr/>');
            $tr.append($('<td/>').text(idx + 1));
            $tr.append($('<td/>').text(tanggal));
            $tr.append($('<td/>').text(target));
            $tr.append($('<td/>').text(actual));
            $modalTableBody.append($tr);
        });

        // update footer (pakai format lokal ID)
        $('#modalTotalTarget').text(totalTarget.toLocaleString('id-ID'));
        $('#modalTotalActual').text(totalActual.toLocaleString('id-ID'));

        actualModal.show();
        });

    // Pastikan hanya diinit sekali
    if ($.fn.DataTable.isDataTable('#tableProgramPromo')) {
        tableProgramPromo = $('#tableProgramPromo').DataTable();
    } else {
        tableProgramPromo = $('#tableProgramPromo').DataTable({
            paging: false,
            searching: false,
            info: false,
            ordering: false,
            autoWidth: false,
            // kalau datanya isi via ajax/array, tambahkan 'columns' di sini
        });
    }
    
    

//    $(document).on('click', '.view-actual', function () {
//         const outlet = $(this).data('outlet');
//         const brand = $('#brandName').val();
//         // const program = $('#programName').val();

//         const params = new URLSearchParams({ outlet, brand });

//         fetch(`/dashboard/get-promo-actual2?${params}`)
//             .then(res => res.json())
//             .then(data => {
//                 let rows = '';
//                 data.forEach((item, index) => {
//                     rows += `<tr>
//                         <td>${index + 1}</td>
//                         <td>${item.sales_date}</td>
//                         <td>${item.qty_target}</td>
//                         <td>${item.actual_paket}</td>
//                     </tr>`;
//                 });
//                 $('#modalActualTable tbody').html(rows);
//                 $('#actualModal').modal('show');
//             })
//             .catch(err => console.error('Gagal load data aktual:', err));
//     });
    // const menuCodeSelect = document.getElementById('menu_code');
    // let menuCodeChoices;
    // let menuMap = []; // Simpan pasangan menu_code <=> menu_name

    // fetch('/dashboard/get-menu-kode')
    //     .then(response => response.json())
    //     .then(data => {
    //         menuMap = data;

    //         // siapkan choices (pakai menu_code sebagai value)
    //         const codeChoices = data.map(item => ({
    //             value: item.menu_code, // simpan kode sebagai value
    //             label: `${item.menu_code} - ${item.menu_name}` // tampilkan kode & nama
    //         }));

    //         // Inisialisasi Choices.js
    //         menuCodeChoices = new Choices(menuCodeSelect, {
    //             removeItemButton: true,
    //             placeholderValue: 'Pilih Menu Code',
    //             searchPlaceholderValue: 'Cari Menu Code',
    //             choices: codeChoices
    //         });

    //         // Event ketika ada perubahan pilihan
    //         menuCodeSelect.addEventListener('change', function () {
    //             const selectedValues = menuCodeChoices.getValue(true); // array of value (menu_code)
    //             console.log("Selected Codes:", selectedValues);

    //             // Jika butuh menu_name bisa ambil dari menuMap
    //             const selectedNames = menuMap
    //                 .filter(item => selectedValues.includes(item.menu_code))
    //                 .map(item => item.menu_name);

    //             console.log("Selected Names:", selectedNames);
    //         });
    //     })
    //     .catch(error => {
    //         console.error('Gagal mengambil data menu:', error);
    //     });
    

    

</script>
<script>
    
    const branchSelect  = document.getElementById('branch_performance');
    const branchChoices = new Choices(branchSelect, {
        placeholderValue: 'Pilih Branch',
        searchPlaceholderValue: 'Cari Branch',
        shouldSort: false,
        removeItemButton: false,
    });

    fetch('/dashboard/get-branch-list')
        .then(res => res.json())
        .then(rows => {
        // rows: [{id:1, nama_branch:"..."}, ...]
        const items = rows.map(r => ({
            value: r.nama_branch,             // nilai yang dikirim saat submit (ID)
            label: r.nama_branch,            // teks yang ditampilkan
            // optional: simpan nama untuk diambil lagi nanti
            customProperties: { nama: r.nama_branch }
        }));

        // reset & set pilihan
        branchChoices.clearChoices();
        branchChoices.setChoices(items, 'value', 'label', true);
        })
        .catch(err => console.error('Gagal load branch:', err));


    let perfTable = $('.sales-menu-performance-report').DataTable({
        paging: true,
        searching: false,
        info: true,
        ordering: true,
        columns: [
        // No (autonumber client)
        { data: null, render: (_, __, ___, meta) => meta.row + 1, className: 'text-center' },
        { data: 'rank', className: 'text-center' },
        { data: 'menu_name' },
        { data: 'menu_code' },
        { data: 'menu_category_name' },
        { data: 'menu_category_detail_name' },
        { data: 'value', className: 'text-end',
            render: $.fn.dataTable.render.number('.', ',', 0) }
        ]
    });

    // Ambil nilai dropdown secara aman (pilih salah satu cara)
    function getSelectedBranchId() {
        const el = document.getElementById('branch_performance');
        // kalau pakai Choices:
        // return (window.branchChoices?.getValue(true)) || el.value || '';
        return el.value || '';
    }

    document.getElementById('btnSearchPerformance').addEventListener('click', async function () {
        const branchId = getSelectedBranchId();
        const month    = document.getElementById('bulan_performance').value;
        const type     = document.getElementById('type_performance').value;
        const year     = new Date().getFullYear(); // bisa ganti kalau kamu pakai selector tahun

        if (!branchId) { alert('Pilih Branch dulu.'); return; }
        if (!month)    { alert('Pilih Periode (bulan).'); return; }

        const params = new URLSearchParams({ branch_id: branchId, month, year, type });

        try {
        const res  = await fetch(`/dashboard/menu-report?${params}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const json = await res.json(); // { rows: [...] }

        // Urutkan by value desc lalu set rank
        const rows = Array.isArray(json.rows) ? json.rows.slice() : [];
        rows.sort((a,b) => (Number(b.value||0) - Number(a.value||0)));
        rows.forEach((r, i) => r.rank = i + 1);

        perfTable.clear().rows.add(rows).draw();
        } catch (e) {
        console.error('Gagal ambil data:', e);
        alert('Gagal memuat data. Coba lagi.');
        }
    });
</script>
@endpush
