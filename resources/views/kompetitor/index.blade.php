@extends('layouts.app')
<!-- DataTables core CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

@section('content')
<!-- Begin Page Content -->
    <div class="container-fluid">
        @include('kompetitor.modal_insert_kompetitor')
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
            <h1 class="h3 mb-2 text-gray-800">Data Kompetitor</h1>

            <div class="d-flex gap-2">
                <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="createForm()">
                    <i class="fas fa-edit"></i>
                </a>
            </div>
        </div>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tabel Kompetitor</h6>
            </div>
            <div class="card-body">
                <div class="box-body table-responsive">
                    <table class="table table-stiped table-bordered kompetitor-table">
                        <thead>
                            <th width="5%">No</th>
                            <th>Kompetitor</th>
                            <th>Waktu Survei</th>
                            <th>PIC</th>
                            <th>Lokasi</th>
                            <th>% Pengunjung</th>
                            <th width="10%"><i class="fa fa-cogs"></i></th>
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

  let table = $('.kompetitor-table').DataTable({
    serverSide: true,
    processing: true,
    ajax: `{{ route('getDataKompetitor') }}`,
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

  function createForm() {
    $('#formCreateCompetitor')[0].reset(); // reset form
    $('#modalCreateCompetitor').modal('show');

    $("#closeKompetitorModal").click(function() {
      $('#formCreateCompetitor')[0].reset();
      $('#modalCreateCompetitor').modal('hide');
    });

    $('.btn-cancel').click(function() {
      $('#formCreateCompetitor')[0].reset();
      $('#modalCreateCompetitor').modal('hide');
    });

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
          $('#modalCreateCompetitor').modal('hide');
          form[0].reset();

          table.ajax.reload(null, false);

          alert('Data berhasil disimpan!');
        },
        error: function(xhr) {
          alert('Gagal menyimpan data!');
          console.log(xhr.responseText);
        }
      });
    });

  }

</script>

@endpush
