@extends('layouts.app')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
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
}

/* Styling untuk footer tabel (tfoot) */
#salesTable tfoot td {
    font-size: 12px; /* Mengatur ukuran font untuk teks di dalam footer, sedikit lebih besar dari body */
    padding: 6px 8px; /* Mengurangi padding */
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
</style>
@section('content')
<div class="container">
    <!-- <div class="row">
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
                            <i class="fas fa-percentage fa-2x text-muted"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
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
                        <input type="number" class="form-control" id="tahun" name="tahun" value="2025">
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
                    <table class="table table-stiped table-bordered dashboard-table" id="salesTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th rowspan="2" width="5%">No</th>
                                <th rowspan="2">Hari</th>
                                <th colspan="3"><span id="labelBulan"></span></th>
                                <th colspan="3">Target <span id="labelTahun1"></span></th>
                                <th colspan="2">Target Sales To Go <span id="labelTahun2"></span></th>
                            </tr>
                            <tr>
                                <th>Tanggal</th>
                                <th>Sales</th>
                                <th>Nett Sales</th>
                                <th>Tanggal</th>
                                <th>Target Sales Outlet</th>
                                <th>Sales</th>
                                <th>Tanggal</th>
                                <th>Sales</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyData"></tbody>
                        <tfoot id="tfootData">
                            <tr style="background:yellow;font-weight:bold">
                                <td colspan="2">TOTAL</td>
                                <td></td>                      <!-- kolom Tanggal (Jul-2025) -->
                                <td id="totalSales"></td>      <!-- Sales -->
                                <td id="totalNett"></td>       <!-- Nett Sales -->
                                <td></td>                      <!-- kolom Tanggal (Target) -->
                                <td id="totalTargetOutlet"></td><!-- Target Sales Outlet -->
                                <td id="totalTargetSales"></td><!-- Sales Target -->
                                <td></td>                      <!-- kolom Tanggal (To Go) -->
                                <td id="totalTargetToGo"></td> <!-- Sales To Go -->
                            </tr>
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
                // Kosongkan semua nilai dulu
                $('#tbodyData tr').each(function() {
                    $(this).find('td').eq(3).text(''); // sales
                    $(this).find('td').eq(4).text(''); // nett sales
                    $(this).find('td').eq(6).text(''); // target outlet
                    $(this).find('td').eq(7).text(''); // sales target
                    $(this).find('td').eq(9).text(''); // sales to go
                });

                // Isi data sesuai index (urutannya mengikuti tanggal)
                res.data.forEach(function (row, i) {
                    const tr = $('#tbodyData tr').eq(i); 
                    tr.find('td').eq(3).text('Rp. ' + parseInt(row.sales || 0).toLocaleString());
                    tr.find('td').eq(4).text('Rp. ' + parseInt(row.nett_sales || 0).toLocaleString());
                    tr.find('td').eq(6).text('Rp. ' + parseInt(row.target_sales_outlet || 0).toLocaleString());
                    tr.find('td').eq(7).text('Rp. ' + parseInt(row.sales_target || 0).toLocaleString());
                    tr.find('td').eq(9).text('Rp. ' +parseInt(row.sales_to_go || 0).toLocaleString());
                });

                hitungTotal();
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('Gagal load data');
            }
        });
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
                value: o.nama_outlet, label: o.nama_outlet
            })), 'value', 'label', true))
            .catch(err => console.error('Gagal load outlet:', err));

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
                                <td>${tanggal}</td>
                                <td></td>
                                <td></td>
                                <td>${tanggal}</td>
                                <td></td>
                            </tr>
                        `;
                    }
                    tbody.innerHTML = rows;

                    // Update header label
                    labelBulan.textContent = `${namaBulan[jsMonth]}-${tahun}`;
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

    function hitungTotal() {
        // index kolom angka: 3, 4, 6, 7, 9
        const sumCol = idx =>
            $('#tbodyData tr').toArray().reduce((acc, tr) => {
            const txt = $(tr).find('td').eq(idx).text().replace(/,/g, '');
            return acc + (Number(txt) || 0);
            }, 0);

        $('#totalSales').text(          sumCol(3).toLocaleString() );
        $('#totalNett').text(           sumCol(4).toLocaleString() );
        $('#totalTargetOutlet').text(   sumCol(6).toLocaleString() );
        $('#totalTargetSales').text(    sumCol(7).toLocaleString() );
        $('#totalTargetToGo').text(     sumCol(9).toLocaleString() );
    }


    $(document).ready(function () {

        $.get('/dashboard/summary', function (response) {
            $('#totalOutlet').text(response.total_outlet);
            $('#totalBrand').text(response.total_brand);
            $('#totalPromosi').text(response.total_promosi);
            
        }).fail(function (xhr) {
            console.log('Error:', xhr.responseText);
            $('#totalPromosi').text('Error');
            $('#totalOutlet').text('Error');
            $('#totalBrand').text('Error');
        });

    });

    
</script>

@endpush
