@extends('layouts.app')
<!-- DataTables core CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<!-- DataTables RowGroup extension CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.3.1/css/rowGroup.dataTables.min.css">

@section('content')
<!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Content Row -->
        @if (session('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="display: block;">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @else
            <div class="alert alert-danger alert-dismissible fade show" style="display:none;" role="alert">
                <ul class="mb-0"></ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-2 text-gray-800">Import SRDR</h1>

            <div class="d-flex gap-2">
                <!-- <a href="#" id="btnDownloadTemplateDM" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm" title="Download Template SRDR">
                    <i class="fas fa-file-download"></i>
                </a> -->
                <a href="{{ route('importEditForm') }}"  class="d-none d-sm-inline-block btn btn-sm btn-info" title="Download Template SRDR">
                    <i class="fas fa-file-download"></i>
                </a>
            </div>
        </div>
        
        <div id="divFormSrdr" class="card shadow mb-4" style="max-width: 800px; margin: auto;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Form Import SRDR</h6>
            </div>
            <div class="card-body small">
                <!-- <form id="formSrdr" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="file" class="form-label">Upload data SRDR</label>
                        <input type="file" name="file" id="file" class="form-control" accept=".xlsx,.xls" required>
                        <small class="text-muted">Silakan upload file Excel berisikan data Aktual</small>
                    </div>

                    <input type="hidden" name="brand_id" id="brand_id_hidden">
                    <input type="hidden" name="outlet_id" id="outlet_id_hidden">

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" id="cancelScs">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan Aktual</button>
                    </div>
                </form> -->
                <form id="import-form" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="file">Pilih File Excel</label>
                        <input type="file" class="form-control" id="file" name="file" required>
                    </div>
                    <button type="submit" class="btn btn-primary" id="submit-button">Mulai Impor</button>
                </form>
                
                <div id="progress-section" style="display: none; margin-top: 20px;">
                    <h4>Memproses...</h4>
                    <div class="progress">
                        <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                    </div>
                    <div id="progress-status" style="margin-top: 10px;"></div>
                    <div id="alert-section" class="alert" style="display: none; margin-top: 15px;"></div>
                </div>
            </div>
        </div>


        <!-- DataTales Example -->
        <!-- <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Data Aktual</h6>
            </div>
            <div class="card-body">
                <div class="box-body table-responsive">
                    <table class="table table-stiped table-bordered aktual-table" style="width:100%;">
                        <thead>
                            <th width="5%">No</th>
                            <th>Menu Category</th>
                            <th>Sales Number</th>
                            <th width="10%"><i class="fa fa-cog"></i></th>
                        </thead>
                    </table>
                </div>
            </div>
        </div> -->
    </div>

@endsection

@push('scripts')

<!-- DataTables core JS -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<!-- DataTables RowGroup extension JS -->
<script src="https://cdn.datatables.net/rowgroup/1.3.1/js/dataTables.rowGroup.min.js"></script>

<script>
    const form = document.getElementById('import-form');
    const submitButton = document.getElementById('submit-button');
    const progressSection = document.getElementById('progress-section');
    const progressBar = document.getElementById('progress-bar');
    const progressStatus = document.getElementById('progress-status');
    const alertSection = document.getElementById('alert-section');

    let totalRows = 0;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        submitButton.disabled = true;
        submitButton.innerText = 'Mengunggah...';
        progressSection.style.display = 'block';
        showAlert('', 'info', false);

        const formData = new FormData(this);

        axios.post("{{ route('import.setup') }}", formData)
            .then(response => {
                totalRows = response.data.total_rows;
                submitButton.innerText = 'Memproses...';
                progressStatus.innerText = `File diterima. Total ${totalRows} baris akan diproses.`;
                
                processChunk(0);
            })
            .catch(error => {
                handleError(error);
            });
    });

    function processChunk(offset) {
        axios.post("{{ route('import.process') }}", { offset: offset })
            .then(response => {
                const data = response.data;
                const totalProcessed = data.total_processed;
                
                const percentage = totalRows > 0 ? Math.round((totalProcessed / totalRows) * 100) : 0;
                progressBar.style.width = percentage + '%';
                progressBar.innerText = percentage + '%';
                progressBar.setAttribute('aria-valuenow', percentage);
                progressStatus.innerText = `Memproses... ${totalProcessed} dari ${totalRows} baris selesai.`;

                if (totalProcessed < totalRows) {
                    processChunk(totalProcessed);
                } else {
                    // Selesai!
                    submitButton.innerText = 'Impor Selesai';
                    showAlert('Impor data berhasil diselesaikan! Form akan direset.', 'success');
                    resetForm(); // <-- TAMBAHAN: Panggil fungsi reset saat berhasil
                }
            })
            .catch(error => {
                handleError(error);
            });
    }
    
    function handleError(error) {
        let message = 'Terjadi kesalahan yang tidak diketahui.';
        if (error.response && error.response.data && error.response.data.error) {
            message = error.response.data.error;
        } else if (error.message) {
            message = error.message;
        }
        showAlert(message, 'danger');
        resetForm(); // <-- TAMBAHAN: Panggil fungsi reset saat error juga
    }

    function showAlert(message, type, show = true) {
        alertSection.style.display = show ? 'block' : 'none';
        alertSection.className = `alert alert-${type}`;
        alertSection.innerText = message;
    }

    // <-- TAMBAHAN: Fungsi baru untuk mereset seluruh tampilan form
    function resetForm() {
        // 1. Reset field input file
        form.reset(); 
        
        // 2. Aktifkan kembali tombol dan kembalikan teksnya
        submitButton.disabled = false;
        submitButton.innerText = 'Mulai Impor';

        // 3. Sembunyikan kembali bagian progress setelah beberapa detik
        //    agar pengguna sempat membaca pesan sukses/error.
        setTimeout(() => {
            progressSection.style.display = 'none';
            // Reset visual progress bar untuk impor selanjutnya
            progressBar.style.width = '0%';
            progressBar.innerText = '0%';
            progressBar.setAttribute('aria-valuenow', '0');
            progressStatus.innerText = '';
        }, 4000); // 4000 milidetik = 4 detik
    }

    $('#btnDownloadTemplateDM').click(function(e) {
        e.preventDefault();

        $.ajax({
            url: '/aktualAPI/download-template',
            type: 'GET',
            xhrFields: {
                responseType: 'blob' // Supaya file binary
            },
            success: function(data) {
                // Buat blob dan link sementara
                const blob = new Blob([data]);
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'Template-SRDR.xlsx';
                link.click();
            },
            error: function(xhr, status, error) {
                alert('Gagal download template!');
                console.error(error);
            }
        });
    });

    $(document).ready(function () {
        $('#formSrdr').on('submit', function (e) {
            e.preventDefault();

            var form = $(this)[0];
            var formData = new FormData(form);

            // Tombol loading (opsional)
            let $submitBtn = $(this).find('button[type="submit"]');
            $submitBtn.prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: "{{ route('srdr.import') }}", // ganti dengan route kamu
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    // Handle response sukses
                    alert('Data berhasil diimport!');
                    $('#formSrdr')[0].reset();
                },
                error: function (xhr) {
                    // Handle error
                    console.error(xhr.responseText);
                    alert('Terjadi kesalahan saat mengimpor data!');
                },
                complete: function () {
                    $submitBtn.prop('disabled', false).text('Simpan Aktual');
                }
            });
        });

        $('#cancelScs').on('click', function () {
            $('#formSrdr')[0].reset();
        });
    });
</script>
@endpush
