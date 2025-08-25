@extends('layouts.app')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<style>
</style>
@section('content')
<!-- Begin Page Content -->
    <div class="container-fluid">
        @include('target_outlet.upload_modal_target_outlet')
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-2 text-gray-800">Tabel Data Target Outlet</h1>
            <a href="#" data-bs-toggle="modal" data-bs-target="#targetOutletModal"
                class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                <i class="fas fa-cloud-upload-alt fa-sm text-white-50"></i> Setting Target Outlet
            </a>

        </div>
        
        <!-- Content Row -->
        @if (session('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Notifikasi error validasi -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Data Brand</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="filter_outlet">Outlet</label>
                        <select id="outlet_id_filter" class="form-select">
                            <option value="">Semua Outlet</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="filter_bulan">Bulan</label>
                        <select id="filter_bulan" class="form-select">
                            <option value="">Semua Bulan</option>
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
                    <div class="col-md-4 d-flex align-items-end">
                        <button id="btnFilter" class="btn btn-success">Filter</button>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-stiped table-bordered menu-template-table" id="target-sales-tables" style="width: 100%;">
                        <thead>
                            <th width="5%">No</th>
                            <th>Minggu ke</th>
                            <th>Senin</th>
                            <th>Selasa</th>
                            <th>Rabu</th>
                            <th>Kamis</th>
                            <th>Jum'at</th>
                            <th>Sabtu</th>
                            <th>Minggu</th>
                            <!-- <th width="10%"><i class="fa fa-cog"></i></th> -->
                        </thead>
                         <tbody id="targetTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
@push('scripts')

<script>
$(document).ready(function(){
    function loadTargetData() {
        const outlet = $('#outlet_id_filter').val();
        const bulan = $('#filter_bulan').val();

        // Jika parameter kosong, tampilkan pesan tanpa request ke server
        if(outlet === '' || bulan === ''){
            $('#targetTableBody').html(`<tr><td colspan="10" class="text-center">Tidak ada data</td></tr>`);
            return;
        }

        $.ajax({
            url: "{{ route('getDataTergetSales') }}",
            type: "GET",
            data: {
                outlet_id: outlet,
                bulan: bulan
            },
            success: function(data) {
                let html = '';
                if(data.length > 0) {
                    data.forEach(function(item){
                        html += `
                            <tr>
                                <td>`+(data.length)+`</td>
                                <td>${item.week_number}</td>
                                <td>${item.senin}</td>
                                <td>${item.selasa}</td>
                                <td>${item.rabu}</td>
                                <td>${item.kamis}</td>
                                <td>${item.jumat}</td>
                                <td>${item.sabtu}</td>
                                <td>${item.minggu}</td>
                            </tr>
                        `;
                    });
                } else {
                    html = `<tr><td colspan=10" class="text-center">Tidak ada data</td></tr>`;
                }
                $('#targetTableBody').html(html);
            },
            error: function(xhr){
                console.error(xhr.responseText);
            }
        });
    }

    // tombol filter
    $('#btnFilter').click(function(){
        loadTargetData();
    });

    // load awal → kosong dulu
    $('#targetTableBody').html(`<tr><td colspan="10" class="text-center">Tidak ada data</td></tr>`);
});

// === 1. Choices untuk Filter di Index ===
    const outletFilterSelect = document.getElementById('outlet_id_filter');
    const outletFilterChoices = new Choices(outletFilterSelect, {
        placeholderValue: 'Pilih Outlet',
        searchPlaceholderValue: 'Cari Outlet',
        removeItemButton: false,
        shouldSort: false
    });

    // Load data untuk filter index sekali di awal
    $.ajax({
        url: "{{ route('getDataOutlet') }}",
        type: "GET",
        dataType: "json",
        success: function (data) {
            outletFilterChoices.clearChoices();
            outletFilterChoices.setChoices([{
                value: '',
                label: '-- Semua Outlet --',
                selected: true,
                disabled: false
            }], 'value', 'label', false);

            data.forEach(function (item) {
                outletFilterChoices.setChoices([{
                    value: item.id,
                    label: item.name
                }], 'value', 'label', false);
            });
        },
        error: function (xhr) {
            console.error(xhr.responseText);
        }
    });

   
</script>
@endpush