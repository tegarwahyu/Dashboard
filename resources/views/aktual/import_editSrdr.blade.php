@extends('layouts.app')

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
                <a href="#" id="btnDownloadTemplateDM" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm" title="Download Template SRDR">
                    <i class="fas fa-file-download"></i>
                </a>
            </div>
        </div>
        
        <div id="divFormSrdr" class="card shadow mb-4" style="max-width: 800px; margin: auto;">
            <div class="card-header py-3">
                <h2>Ganti / Timpa Data SRDR</h2>
    
                <div class="alert alert-warning">
                    <strong>PERINGATAN:</strong> Proses ini akan **MENGHAPUS** semua data yang ada di database dengan kombinasi <strong>Cabang, Merek, dan Tanggal Penjualan</strong> yang sama seperti di file yang Anda unggah, lalu menggantinya dengan data baru dari file tersebut.
                </div>
            </div>
            <div class="card-body small">
                 <form id="import-form" enctype="multipart/data">
                        @csrf
                        <div class="form-group">
                            <label for="file">Pilih File Excel Baru</label>
                            <input type="file" class="form-control" id="file" name="file" required>
                        </div>
                        <button type="submit" class="btn btn-danger" id="submit-button">Mulai Proses Ganti Data</button>
                    </form>
                    
                    <div id="progress-section" style="display: none; margin-top: 20px;">
                        </div>

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


    </div>

@endsection

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

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
        submitButton.innerText = 'Mempersiapkan...'; // Teks diubah menjadi lebih umum
        progressSection.style.display = 'block';
        progressStatus.innerText = 'Mengunggah & memproses file awal... (Mohon tunggu, ini bisa memakan waktu beberapa menit)';
        showAlert('', 'info', false);

        const formData = new FormData(this);

        // HANYA SATU PANGGILAN API UNTUK SETUP DAN DELETE
        axios.post("{{ route('import.setup.update') }}", formData)
            .then(response => {
                totalRows = response.data.total_rows;
                const deletedCount = response.data.deleted_rows_count;

                submitButton.innerText = 'Memproses...';
                progressStatus.innerText = `Persiapan selesai. ${deletedCount} baris lama dihapus. Sekarang memasukkan ${totalRows} baris baru...`;
                
                // Langsung mulai proses impor per potongan (chunk)
                processChunk(0);
            })
            .catch(error => {
                handleError(error);
            });
    });

    // --- FUNGSI DI BAWAH INI TIDAK ADA PERUBAHAN SAMA SEKALI ---
    // processChunk, handleError, showAlert, resetForm tetap sama persis
    // seperti pada jawaban-jawaban sebelumnya.

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
                    submitButton.innerText = 'Impor Selesai';
                    showAlert('Impor data berhasil diselesaikan! Form akan direset.', 'success');
                    resetForm();
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
        resetForm();
    }

    function showAlert(message, type, show = true) {
        alertSection.style.display = show ? 'block' : 'none';
        alertSection.className = `alert alert-${type}`;
        alertSection.innerText = message;
    }

    function resetForm() {
        form.reset(); 
        submitButton.disabled = false;
        submitButton.innerText = 'Mulai Proses Ganti Data';
        setTimeout(() => {
            progressSection.style.display = 'none';
            progressBar.style.width = '0%';
            progressBar.innerText = '0%';
            progressBar.setAttribute('aria-valuenow', '0');
            progressStatus.innerText = '';
        }, 4000);
    }
</script>
@endpush
