@extends('layouts.app')
@section('content')
<!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-2 text-gray-800">Tabel Data Semua Sub-Branch</h1>
            <a href="#" data-bs-toggle="modal" data-bs-target="#eventTambahSubBranchModal"
            class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                <i class="fas fa-cloud-upload-alt fa-sm text-white-50"></i> Tambah Sub Branch
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
                <h6 class="m-0 font-weight-bold text-primary">Data Sub-Branch</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="subBranchTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Sub-Branch</th>
                                <th>Nama Branch</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Branch -->
    <div class="modal fade" id="eventTambahSubBranchModal" tabindex="-1" aria-labelledby="eventTambahBranchModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="eventTambahBranchModalLabel">Tambah Sub Branch</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                  <form id="formTambahSubBranch">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_sub_branch" class="form-label">Nama Branch</label>
                            <input type="text" name="nama_sub_branch" id="nama_sub_branch" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="branch_id" class="form-label">Pilih Branch</label>
                            <select name="branch_id" id="branch_id" class="form-select" required>
                                <option value="">-- Pilih Branch --</option>
                            </select>
                        </div>
                        <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}"> -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#subBranchTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/branch/sub_brand/list-data',   // URL ke route Laravel
                type: 'GET'
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama_sub_branch', name: 'nama_sub_branch' },
                { data: 'branch.nama_branch', name: 'branch.nama_branch' },
                {
                    data: 'id',
                    render: function (data, type, row) {
                        return `<button class="btn btn-sm btn-primary disabled" onclick="editBranch(${data})">Edit</button>`;
                    },
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $('#eventTambahSubBranchModal').on('show.bs.modal', function () {
            $.ajax({
                url: '/branch/sub-brand/list',
                type: 'GET',
                success: function (data) {
                    let select = $('#branch_id');
                    select.empty();
                    select.append('<option value="">-- Pilih Branch --</option>');
                    data.forEach(function (item) {
                        select.append('<option value="'+item.id+'">'+item.nama_branch+'</option>');
                    });
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    alert('Gagal memuat daftar brand');
                }
            });
        });
    });

    $('#formTambahSubBranch').on('submit', function (e) {
        e.preventDefault();

        let formData = $(this).serialize() + '&_token={{ csrf_token() }}';

        $.ajax({
            url: '/branch/sub_branch/store',   // route untuk simpan
            type: 'POST',
            data: formData,
            success: function (res) {
                if (res.success) {
                    $('#eventTambahSubBranchModal').modal('hide');
                    $('#subBranchTable').DataTable().ajax.reload();
                    $('#formTambahSubBranch')[0].reset();
                } else {
                    alert('Gagal menambahkan data');
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('Terjadi kesalahan');
            }
        });
    });
</script>
@endpush