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

        @include('brand.modal_upload_brand')
        @include('brand.modal_edit_brand')
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-2 text-gray-800">Tabel Data Semua Brand</h1>
            <a href="#" data-bs-toggle="modal" data-bs-target="#eventTambahBrandModal"
            class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                <i class="fas fa-cloud-upload-alt fa-sm text-white-50"></i> Tambah Nama Brand
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
                <div class="table-responsive">
                    <table class="table table-bordered" id="brandTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Brand</th>
                                <th>Nama Brand</th>
                                <th>Logo</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($brand_data as $event)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if(!empty($event['kode_brand']))
                                        {{ $event['kode_brand'] }}
                                    @else
                                        kode belum diberikan
                                    @endif
                                </td>
                                <td>{{ $event['nama_brand'] }}</td>
                                <td>
                                    @if($event['logo_path'])
                                        <img src="{{ asset($event['logo_path']) }}" 
                                            alt="Poster" width="100" 
                                            class="img-thumbnail preview-img" 
                                            data-src="{{ asset($event['logo_path']) }}">
                                    @else
                                        Tidak ada gambar
                                    @endif
                                </td>
                                <td>
                                    @if($event['status'] == 'aktif')
                                        <span style="background-color: #28a745; color: white; padding: 3px 8px; border-radius: 5px; font-size: 0.875rem;">
                                            {{ $event['status'] }}
                                        </span>
                                    @else
                                        <span style="background-color: #dc3545; color: white; padding: 3px 8px; border-radius: 5px; font-size: 0.875rem;">
                                            {{ $event['status'] }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <button 
                                        type="button"
                                        class="btn btn-sm btn-primary edit-brand-btn"
                                        data-id="{{ $event['id'] }}"
                                        data-nama_brand="{{ $event['nama_brand'] }}"
                                        data-logo_path="{{ asset($event['logo_path']) }}"
                                    >
                                        Edit
                                    </button>

                                    <!-- <button class="btn btn-sm {{ $event['status'] === 'aktif' ? 'btn-warning' : 'btn-success' }} toggle-status-btn status-brand-btn"
                                        data-id="{{ $event['id'] }}">
                                        {{ $event['status'] === 'aktif' ? 'Non Aktif' : 'Aktif' }}
                                    </button> -->

                                    @if(Auth::user()->role == 'Marketing' || Auth::user()->role == 'Super Admin')
                                        <button class="btn btn-sm btn-danger delete-brand-btn"
                                            data-id="{{ $event['id'] }}">
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

<div id="imageModal" class="img-modal" style="display:none;" onclick="closeModal(event)">
    <span class="img-modal-close" onclick="closeModal(event)">&times;</span>
    <img class="img-modal-content" id="modalImage">
</div>

@push('scripts')

    <script>
        document.querySelectorAll('.preview-img').forEach(function(img) {
            img.addEventListener('click', function(event) {
                event.stopPropagation();
                var modal = document.getElementById("imageModal");
                var modalImg = document.getElementById("modalImage");
                modal.style.display = "block";
                modalImg.src = this.dataset.src;
            });
        });

        function closeModal() {
            document.getElementById("imageModal").style.display = "none";
        }
    </script>

    <script>
        $(document).ready(function() {
            $('.edit-brand-btn').click(function() {
                var id = $(this).data('id');
                var nama_brand = $(this).data('nama_brand');
                var logo_path = $(this).data('logo_path');
                var status = $(this).data('status');

                $('#edit_id').val(id);
                $('#edit_nama_brand').val(nama_brand);
                $('#edit_logo_brand').attr('src', logo_path);
                $('#edit_status').val(status);

                $('#editBrandModal').modal('show');
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.delete-brand-btn');
            const deactivatedButtons = document.querySelectorAll('.status-brand-btn');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.dataset.id;

                    if (confirm('Yakin ingin menghapus data ini?')) {
                        fetch(`/brand/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
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

            deactivatedButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.dataset.id;

                    if (confirm('Yakin ingin menonaktifkan data brand ini ?')) {
                        fetch(`/brand/deactivated/${id}`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            console.log(data)
                            alert(data.message || 'data brand berhasil dinonaktifkan');
                            location.reload();
                        })
                        .catch(err => {
                            alert('Terjadi kesalahan saat menonaktifkan data brand');
                            console.error(err);
                        });
                    }
                });
            });
        });
    </script>

    <!-- Tambahkan script ini -->
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

    <!-- js datatable  -->
<script>
  $(document).ready(function() {
    $('#brandTable').DataTable({
      responsive: true, // Jika mau tabel responsif
      autoWidth: false, // Agar lebar kolom tidak auto
      ordering: true,   // Aktifkan sorting
      paging: true,     // Aktifkan pagination
      lengthChange: true, // Tampilkan opsi jumlah data
      searching: true,  // Aktifkan pencarian
      language: {
        search: "Cari:", // Label pencarian
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