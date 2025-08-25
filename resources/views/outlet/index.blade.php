@extends('layouts.app')
<style>
    .img-modal {
        display: none; 
        position: fixed; 
        z-index: 9999; 
        padding-top: 60px; 
        left: 0; 
        top: 0; 
        width: 100%; 
        height: 100%; 
        overflow: auto; 
        background-color: rgba(0,0,0,0.8);
    }

    .img-modal-content {
        margin: auto;
        display: block;
        max-width: 80%;
        max-height: 80%;
    }

    .img-modal-close {
        position: absolute;
        top: 20px;
        right: 35px;
        color: #fff;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
    }

    /* Perkecil font tabel */
    .dataTables_wrapper table.dataTable {
        font-size: 15px; /* atau 11px sesuai kebutuhan */
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
</style>
@section('content')
<!-- Begin Page Content -->
    <div class="container-fluid">

        @include('outlet.modal_upload_outlet')
        @include('outlet.modal_edit_outlet')
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-2 text-gray-800">Tabel Data Semua Outlet</h1>
            <a href="#" data-bs-toggle="modal" data-bs-target="#outletModal"
            class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                <i class="fas fa-cloud-upload-alt fa-sm text-white-50"></i> Tambah Nama Outlet
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
                <h6 class="m-0 font-weight-bold text-primary">Data Outlet</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="outletTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Outlet</th>
                                <th>Nama Brand</th>
                                <th>Nama Outlet</th>
                                <th>Lokasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($outlet_data as $data)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if(!empty($data->kode_outlet))
                                        {{ $data->kode_outlet }}
                                    @else
                                        kode belum diberikan
                                    @endif
                                </td>

                                <td>{{ $data->nama_brand }}</td>
                                <td>{{ $data->nama_outlet }}</td>
                                <td>{{ $data->lokasi }}</td>
                                <td>
                                    <button 
                                        type="button"
                                        class="btn btn-sm btn-primary edit-outlet-btn"
                                        data-id="{{ $data->id }}"
                                        data-nama_outlet="{{ $data->nama_outlet }}"
                                        data-brand_id="{{ $data->brand_id }}"
                                        data-kode_outlet="{{ $data->kode_outlet }}"
                                        data-lokasi="{{ $data->lokasi }}"
                                    >
                                        Edit
                                    </button>

                                    @if(Auth::user()->role == 'Marketing' || Auth::user()->role == 'Super Admin')
                                        <button 
                                            class="btn btn-sm btn-danger delete-outlet-btn"
                                            data-id="{{ $data->id }}"
                                        >
                                            Hapus
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">Data tidak ada</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')

<script>
    $(document).ready(function() {
        $('.edit-outlet-btn').click(function() {
            var id = $(this).data('id');
            var nama_outlet = $(this).data('nama_outlet');
            var brand_id = $(this).data('brand_id');
            var kode_outlet = $(this).data('kode_outlet');
            var lokasi = $(this).data('lokasi');

            $('#edit_id').val(id);
            $('#edit_nama_outlet').val(nama_outlet);
            $('#edit_brand_id').val(brand_id);
            $('#edit_kode_outlet').val(kode_outlet);
            $('#edit_lokasi').val(lokasi);
            //memberikan route di form by id form
            $('#editOutletForm').attr('action', '/outlet/update/' + id);
            $('#editOutletModal').modal('show');
        });
    });
</script>

<!-- script ini berfungsi menutup notifikasi -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Seleksi semua alert Bootstrap yang muncul
        const alerts = document.querySelectorAll('.alert-dismissible');

        alerts.forEach(function (alert) {
            // Tunggu 3 detik (3000ms), lalu tutup
            setTimeout(function () {
                // Bootstrap 5: panggil .alert('close') secara manual
                let alertInstance = bootstrap.Alert.getOrCreateInstance(alert);
                alertInstance.close();
            }, 3000);
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.delete-outlet-btn');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.dataset.id;

                if (confirm('Yakin ingin menghapus data ini?')) {
                    fetch(`/outlet/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.message || 'Data berhasil dihapus');
                        location.reload();
                    })
                    .catch(err => {
                        alert('Terjadi kesalahan saat menghapus data');
                        console.error(err);
                    });
                }
            });
        });
    });
</script>

<!-- js datatabel -->
<script>
  $(document).ready(function() {
    $('#outletTable').DataTable({
      responsive: true,
      autoWidth: false,
      ordering: true,
      paging: true,
      lengthChange: true,
      searching: true,
      language: {
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ data per halaman",
        zeroRecords: "Data tidak ditemukan",
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        infoEmpty: "Data tidak tersedia",
        infoFiltered: "(disaring dari _MAX_ total data)"
      }
    });
  });
</script>
@endpush