@extends('layouts.app')
<!-- DataTables core CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<style>
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
        @include('users.modal_input_user')
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

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-2 text-gray-800">Master Data User</h1>

            <div class="d-flex gap-2">
                <!-- <a href="#"  class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" title="Input Aktual">
                    <i class="fas fa-file-download"></i>
                </a> -->
                <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="createForm()">
                    <i class="fas fa-edit"></i>
                </a>
            </div>
        </div>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Data User</h6>
            </div>
            <div class="card-body">
                <div class="box-body table-responsive">
                    <table class="table table-stiped table-bordered users-table" style="width: 100%;">
                        <thead>
                            <th width="5%">No</th>
                            <th>Nama User</th>
                            <th>Email</th>
                            <th>role</th>
                            <th>Lokasi</th>
                            <th width="10%"><i class="fa fa-cog"></i></th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables core JS -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<!-- DataTables RowGroup extension JS -->
<script src="https://cdn.datatables.net/rowgroup/1.3.1/js/dataTables.rowGroup.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {

  // CSRF token global (cukup SEKALI)
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  let table = $('.users-table').DataTable({
    serverSide: true,
    processing: true,
    ajax: `{{ route('dataUsers') }}`,
    columns: [
      { data: 'DT_RowIndex', searchable: false, sortable: false },
      { data: 'name' },
      { data: 'email' },
      { data: 'role' },
      { data: 'outlet.nama_outlet' },
      { data: 'aksi', searchable: false, sortable: false }
    ],
    order: [[1, 'asc']]
  });

  // ✅ Handler submit form HANYA SATU
  $('form').submit(function(e) {
    e.preventDefault();

    let form = $(this);
    let url = form.attr('action');
    let formData = form.serialize();

    $.ajax({
      url: url,
      method: 'POST', // store selalu POST; update spoof pakai _method=PUT
      data: formData,
      success: function(response) {
        $('#userInsertModal').modal('hide');
        form[0].reset();
        $('#password-group').show();
        $('input[name="_method"]').remove();
        table.ajax.reload(null, false);
        alert('Data berhasil disimpan!');
      },
      error: function(xhr) {
        alert('Gagal menyimpan data!');
        console.log(xhr.responseText);
      }
    });
  });

}); // End document ready


function createForm() {
  $('#eventPromoModalLabel').text('Tambah User');

  // Kosongkan input
  $('#nama').val('');
  $('#email').val('');
  $('#role').val('');
  $('#password').val('');

  $('#password-group').show();
  $('form').attr('action', `/user/store`);
  $('input[name="_method"]').remove();

  // Panggil data outlet via AJAX
  $.ajax({
    url: '/user/get-outlets',
    type: 'GET',
    dataType: 'json',
    success: function(data) {
      let $outletSelect = $('#lokasi');
      $outletSelect.empty(); // kosongkan dulu
      $outletSelect.append('<option value="">-- Pilih Outlet --</option>');

      $.each(data, function(key, outlet) {
        console.log(outlet  )
        $outletSelect.append('<option value="'+ outlet.id +'">'+ outlet.nama_outlet +'</option>');
      });
    },
    error: function(xhr) {
      console.log(xhr.responseText);
      alert('Gagal memuat data outlet!');
    }
  });

  $('#userInsertModal').modal('show');
}


function editData(url) {
  $.get(url)
    .done(response => {
      $('#nama').val(response.name);
      $('#email').val(response.email);
      $('#role').val(response.role);

      $('#password-group').hide();
      $('#password').val('');

      $('#eventPromoModalLabel').text('Edit User');
      $('form').attr('action', `/user/updateUser/${response.id}`);

      if ($('input[name=_method]').length === 0) {
        $('form').append('<input type="hidden" name="_method" value="PUT">');
      } else {
        $('input[name=_method]').val('PUT');
      }

      // 🔑 Ambil semua outlet via AJAX baru
      $.get('/user/get-edit-outlets', function(outlets) {
        let $select = $('#lokasi');
        $select.empty().append('<option value="">-- Pilih Outlet --</option>');

        $.each(outlets, function(_, outlet) {
          const selected = outlet.id == response.outlet_id ? 'selected' : '';
          $select.append(`<option value="${outlet.id}" ${selected}>${outlet.nama_outlet}</option>`);
        });
      });

      $('#userInsertModal').modal('show');
    })
    .fail(err => {
      alert('Gagal mengambil data!');
    });
}

// function editData(url) {
//   $.get(url)
//     .done(response => {
//       $('#nama').val(response.name);
//       $('#judul').val(response.email);
//       $('#role').val(response.role);

//       $('#password-group').hide();
//       $('#password').val('');

//       $('#eventPromoModalLabel').text('Edit User');
//       $('form').attr('action', `/user/updateUser/${response.id}`);

//       if ($('input[name=_method]').length === 0) {
//         $('form').append('<input type="hidden" name="_method" value="PUT">');
//       } else {
//         $('input[name=_method]').val('PUT');
//       }

//       $('#userInsertModal').modal('show');
//     })
//     .fail(err => {
//       alert('Gagal mengambil data!');
//     });
// }

function deleteData(url, callback) {
  if (confirm('Yakin ingin menghapus data?')) {
    $.ajax({
      url: url,
      type: 'DELETE',
      data: { '_token': '{{ csrf_token() }}' },
      success: function(response) {
        if (typeof callback === 'function') {
          callback(response);
        } else {
          alert(response.message || 'Data berhasil dihapus');
          $('.users-table').DataTable().ajax.reload();
        }
      },
      error: function(xhr) {
        console.log(xhr.responseText);
      }
    });
  }
}
</script>

@endpush
