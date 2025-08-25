@extends('layouts.app')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

@section('content')
<!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-2 text-gray-800">Data Semua Salary Outlet Per Outlet</h1>
            <a href="#" data-bs-toggle="modal" data-bs-target="#outleSalarytModal"
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
                <h6 class="m-0 font-weight-bold text-primary">Data Salary Outlet / Bulan</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tableSalaryOutletTable" class="table table-bordered table-striped w-100" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Nama Outlet</th>
                                <th>Nominal</th>
                                <th>Bulan</th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div> 
        </div>
    </div>

<!-- modal start  -->
 <!-- Modal -->
<div class="modal fade" id="outleSalarytModal" tabindex="-1" aria-labelledby="outletModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="outletModalLabel">Tambah Nama Outlet</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formOutlet" method="POST">
        @csrf
        <div class="modal-body">
          <!-- Select Outlet -->
          <div class="mb-3">
            <label for="outlet" class="form-label">Nama Outlet</label>
            <select id="outlet" name="outlet" class="form-select form-select-sm">
                <option value="" selected disabled>Pilih Outlet</option>
            </select>
          </div>

          <!-- Nominal -->
          <div class="mb-3">
            <label for="nominal" class="form-label">Nominal</label>
            <input type="number" class="form-control" id="nominal" name="nominal" placeholder="Masukkan nominal" required>
          </div>

          <!-- Periode Bulan -->
          <div class="mb-3">
            <label for="periode" class="form-label">Periode Bulan</label>
            <input type="month" class="form-control" id="periode" name="periode" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
 <!-- modal end  -->
@endsection

<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

@push('scripts')

<!-- js datatabel -->
<script>

    $(document).ready(function() {
        $('#tableSalaryOutletTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/salary-outlet/list',
            columns: [
                { data: 'outlet_name', name: 'outlet_name' },
                { data: 'total_salary', name: 'total_salary',
                render: $.fn.dataTable.render.number(',', '.', 0, 'Rp ') },
                { data: 'month', name: 'month' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

         // Hapus data
        $(document).on('click', '.deleteBtn', function() {
            let id = $(this).data('id');
            if(confirm('Hapus data ini?')) {
                $.ajax({
                    url: '/salary-outlet/delete/' + id,
                    method: 'DELETE',
                    data: {_token: '{{ csrf_token() }}'},
                    success: function(res) {
                        alert(res.message);
                        $('#tableSalaryOutletTable').DataTable().ajax.reload();
                    }
                });
            }
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const outletSelect = document.getElementById('outlet'); // perbaikan ID
        const outletChoices = new Choices(outletSelect, {
            placeholderValue: 'Pilih Outlet',
            searchPlaceholderValue: 'Cari Outlet',
            removeItemButton: false,
            shouldSort: false
        });

        fetch('/salary-outlet/outlets')
            .then(res => res.json())
            .then(data => {
                outletChoices.setChoices(
                    data.data_outlet.map(o => ({
                        value: o.id,        // gunakan id sebagai value
                        label: o.nama_outlet
                    })),
                    'value',
                    'label',
                    true
                );
            })
        .catch(err => console.error('Gagal load outlet:', err));
    });

    $('#outleSalarytModal form').on('submit', function(e){
        e.preventDefault(); // cegah reload

        let formData = {
            outlet_id: $('#outlet').val(),
            nominal: $('#nominal').val(),
            periode: $('#periode').val(),
            _token: "{{ csrf_token() }}"
        };

        $.ajax({
            url: "{{ route('store-salary-outlet.store') }}",
            type: "POST",
            data: formData,
            success: function(response){
                // console.log(response)
                alert(response.message);
                document.getElementById("formOutlet").reset();
                $('#tableSalaryOutletTable').DataTable().ajax.reload();
                $('#outleSalarytModal').modal('hide');
                // di sini bisa reload table atau update data tanpa reload full page
            },
            error: function(xhr){
                if(xhr.status === 422){
                    let errors = xhr.responseJSON.errors;
                    let message = '';
                    for (let field in errors) {
                        message += errors[field][0] + "\n";
                    }
                    alert(message);
                } else {
                    alert('Terjadi kesalahan server.');
                }
            }
        });
    });
</script>
@endpush