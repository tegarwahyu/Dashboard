@extends('layouts.app')
<!-- DataTables core CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<style>
    .drop-zone {
        border: 2px dashed #6c757d;
        border-radius: 10px;
        padding: 30px;
        text-align: center;
        color: #6c757d;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .drop-zone.dragover {
        background-color: #f8f9fa;
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
        @include('menu_template.edit_menu')
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
            <h1 class="h3 mb-2 text-gray-800">Master Menu Template</h1>

            <div class="d-flex gap-2">
                
                <a href="#" class="btn btn-sm btn-primary shadow-sm" id="btnFormMenuTemplate">
                    <i class="fas fa-edit"></i>
                </a>
            </div>
        </div>

        <div id="divFormImportMenuTemplate" class="card shadow mb-4" style="display: none;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Form Import Data Menu Template</h6>
            </div>
            <div class="card-body">
                <form id="formMenuTemplate" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="drop-zone" id="dropZone">
                                    <p>Tarik file ke sini atau klik untuk memilih</p>
                                    <input type="file" name="file" id="fileInput" class="form-control d-none">
                                </div>
                                <p style="font-size: 12px; margin-top: 4px;">Silakan upload file Excel berisikan data Menu Template</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3" id="progressWrapper" style="display: none;">
                        <label class="form-label">Progress Upload</label>
                        <div class="progress">
                            <div class="progress-bar" id="progressBar" role="progressbar" style="width: 0%">0%</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="cancelScs">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan Aktual</button>
                    </div>
                    
                </form>
            </div>
        </div>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Data User</h6>
            </div>
            <div class="card-body">
                <div class="box-body table-responsive">
                    <table class="table table-stiped table-bordered menu-template-table" id="menu-tables" style="width: 100%;">
                        <thead>
                            <th width="5%">No</th>
                            <th>Menu Code</th>
                            <th>Menu Name</th>
                            <th>Menu Category</th>
                            <th>Status</th>
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
$(document).ready(function () {

    // Set CSRF token ke semua request AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Toggle form upload
    $('#btnFormMenuTemplate').click(function () {
        $('#divFormImportMenuTemplate').toggle('show');

        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');

        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('dragover');

            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                dropZone.querySelector('p').textContent = e.dataTransfer.files[0].name;
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                dropZone.querySelector('p').textContent = fileInput.files[0].name;
            }
        });
    });

    $('#cancelScs').click(function () {
        $('#divFormImportMenuTemplate').hide();
        $('#formMenuTemplate')[0].reset();
        const fileInput = document.getElementById('fileInput');
        fileInput.value = '';
        const dropZone = document.getElementById('dropZone');
        dropZone.querySelector('p').textContent = 'Drop file here or click';
        dropZone.classList.remove('dragover');
    });
});
</script>

<script>
$(document).ready(function() {

    // CSRF token global (cukup SEKALI)
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        // url: '{{ route("importMenuTemplate") }}',
    });

    let table = $('.menu-template-table').DataTable({
            serverSide: true,
            processing: true,
            ajax: `{{ route('getDataMenu') }}`,
            columns: [
            { data: 'DT_RowIndex', searchable: false, sortable: false },
            { data: 'menu_code' },
            { data: 'menu_name' },
            { data: 'menu_category' },
            { data: 'status' },
            { data: 'aksi', searchable: false, sortable: false }
            ],
            order: [[1, 'asc']]
    });
    
        // SUBMIT FORM upload
    $('#formMenuTemplate').submit(function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        $('#progressWrapper').show();
        var progressBar = $('#progressBar');
        progressBar.css('width', '0%').text('0%');

        $.ajax({
            xhr: function () {
                let xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function (e) {
                    if (e.lengthComputable) {
                        let percent = Math.round((e.loaded / e.total) * 100);
                        progressBar.css('width', percent + '%').text(percent + '%');
                    }
                }, false);
                return xhr;
            },
            type: 'POST',
            url: '{{ route("importMenuTemplate") }}',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                // alert(response.message);
                $('#menu-tables').DataTable().ajax.reload();
                $('#divFormImportMenuTemplate').hide();
                const fileInput = document.getElementById('fileInput');
                fileInput.value = '';
                const dropZone = document.getElementById('dropZone');
                dropZone.querySelector('p').textContent = 'Drop file here or click';
                dropZone.classList.remove('dragover');
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('Gagal mengunggah file. Pastikan format benar dan coba lagi.');
                $('#progressWrapper').hide();
            }
        });
    });
});

// show edit action
function editForm(url) {
    $.get(url)
    .done(function(data) {
        $('#edit-menu-template').val(data.menu_template_name);
        $('#edit-menu-category').val(data.menu_category);
        $('#edit-menu-category-detail').val(data.menu_category_detail);
        $('#edit-menu-name').val(data.menu_name);
        $('#edit-menu-short-name').val(data.menu_short_name);
        $('#edit-menu-code').val(data.menu_code);
        $('#edit-price').val(data.price);
        $('#edit-status').val(data.status);

        // Atur action form-nya agar sesuai ID
        $('#formEditMenuTemplate').attr('action', '/menu-template/' + data.id);

        // Tampilkan modal
        $('#editMenuTemplateModal').modal('show');
    })
    .fail(function() {
        alert('Gagal mengambil data.');
    });
}
// save action edit 
$('#formEditMenuTemplate').submit(function(e) {
    e.preventDefault();

    const url = $(this).attr('action');
    const formData = $(this).serialize();

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        headers: {
            'X-HTTP-Method-Override': 'PUT'
        },
        success: function () {
            $('#editMenuTemplateModal').modal('hide'); // Tutup modal
            $('#menu-tables').DataTable().ajax.reload(); // Reload datatable
        },
        error: function (xhr) {
            console.error(xhr.responseText); // Debug jika perlu
        }
    });
});

// delete action
function deleteData(url, callback) {
    $.ajaxSetup({
        headers:{
            'X_CSRF_TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    if (confirm('Yakin ingin menghapus data?')) {
            $.ajax({
            url: url,
            type: 'DELETE',
            dataType: 'JSON',
            data:{
                '_token': '{{ csrf_token() }}',
            },
            success: function (response) {
                if (typeof callback === 'function') {
                    callback(response);
                } else {
                    // Default behavior
                    alert(response.message || 'Data berhasil dihapus');
                    $('#menu-tables').DataTable().ajax.reload(); // Reload datatable
                }
            },
            error: function (xhr) {
                console.log(xhr.responseText);
            }
        })
    }
}

</script>

@endpush
