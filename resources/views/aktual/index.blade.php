@extends('layouts.app')
<!-- DataTables core CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<!-- DataTables RowGroup extension CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.3.1/css/rowGroup.dataTables.min.css">
<style>
  tr.dtrg-group {
    background: #eee;
  }
  tr.dtrg-group:hover {
    background: #ddd;
  }
</style>

<!-- info mode  -->
<style>
  .info-icon-wrapper {
    position: relative;
    display: inline-block;
    cursor: pointer;
  }

  .info-tooltip {
    visibility: hidden;
    opacity: 0;
    transition: opacity 0.3s ease;
    width: 240px;
    background-color: #f8f9fa;
    color: #111;
    border: 1px solid #ccc;
    border-radius: 6px;
    padding: 10px;
    font-size: 13px;
    position: absolute;
    z-index: 999;
    top: 50%;
    left: 110%; /* tampil di kanan ikon (default desktop) */
    transform: translateY(-50%);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    white-space: normal;
  }

  .info-icon-wrapper:hover .info-tooltip,
  .info-icon-wrapper:focus-within .info-tooltip {
    visibility: visible;
    opacity: 1;
  }

  /* Responsif untuk layar kecil (mobile) */
  @media (max-width: 768px) {
    .info-tooltip {
      top: 110%;
      left: -300%;
      transform: translateX(-50%);
    }
  }

  /* Perkecil font tabel */
    .dataTables_wrapper table.dataTable {
        font-size: 13px; /* atau 11px sesuai kebutuhan */
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
        @include('aktual.modal_edit_aktual')
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
            <h1 class="h3 mb-2 text-gray-800">Master Aktual</h1>

            <div class="d-flex gap-2">
                <a href="#" id="btnFormAktual" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" title="Input Aktual">
                    <i class="fas fa-edit"></i>
                </a>
                <a href="#" id="btnDownloadTemplateDM" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm" title="Download Template SRDR">
                    <i class="fas fa-file-download"></i>
                </a>
            </div>
        </div>
        
        <div id="divFormAktual" class="card shadow mb-4" style="display: none;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Form Aktual</h6>
            </div>
            <div class="card-body">
                <form id="formAktual" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Promosi</label>
                                <select name="promosi_id" id="promosi_id" class="form-select" required>
                                    <option value="" disabled selected>Pilih Promosi</option>
                                </select>
                            </div>
                        </div>
                        

                        <div class="col-md-6">
                            <div class="form-group">
                                <div id="targetSCS">
                                    <!-- Field date per outlet akan di-generate di sini -->
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Brand</label>
                                <input type="text" id="brand_name" class="form-control" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Outlet</label>
                                <input type="text" id="outlet_name" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="brand_id" id="brand_id_hidden">
                    <input type="hidden" name="outlet_id" id="outlet_id_hidden">

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
                <h6 class="m-0 font-weight-bold text-primary">Data Aktual</h6>
            </div>
            <div class="card-body">
                <div class="box-body table-responsive">
                    <table class="table table-stiped table-bordered aktual-table" style="width:100%;">
                        <thead>
                            <th width="5%">No</th>
                            <!-- <th>Nama Promosi</th> -->
                            <th>Menu Category</th>
                            <th>Sales Number</th>
                            <!-- <th>Nama Outlet</th> -->
                            <!-- <th>Jumlah Menu</th> -->
                            <th width="10%"><i class="fa fa-cog"></i></th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')

<!-- DataTables core JS -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<!-- DataTables RowGroup extension JS -->
<script src="https://cdn.datatables.net/rowgroup/1.3.1/js/dataTables.rowGroup.min.js"></script>

<script>
$(document).ready(function() {
    
    $('#btnFormAktual').click(function() {
        $('#divFormAktual').toggle('show');

        if ($('#divFormAktual').is(':visible')) {
            // Jalankan AJAX
            $.ajax({
                url: '/aktualAPI/getDataFormAktual',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    const promosiData = response.data;
                    // console.log(promosiData)
                    // Kosongkan promosi_id
                    $('#promosi_id').empty().append('<option value="" disabled selected>Pilih Promosi</option>');

                    // promosiData.forEach(function(promo) {

                    //     $('#promosi_id').append(
                    //         `<option value="${promo.id}" 
                    //             data-outlet='${JSON.stringify(promo.outlet)}'>
                    //             ${promo.judul_promosi}
                    //         </option>`
                    //     );
                    // });

                    promosiData.forEach(function(promo) {
                        // Pastikan promo.outlet tidak null untuk menghindari error
                        if (promo.outlet) {
                            $('#promosi_id').append(
                                // UBAH BAGIAN INI: Tambahkan kode outlet di sebelah judul promosi
                                `<option value="${promo.id}" 
                                        data-outlet='${JSON.stringify(promo.outlet)}'>
                                        ${promo.judul_promosi} | ${promo.outlet.kode_outlet} 
                                </option>`
                            );
                        }
                    });

                    // Bind event change sekali di sini
                    $('#promosi_id').off('change').on('change', function() {
                        let selectedOption = $(this).find(':selected');
                        let outlet = JSON.parse(selectedOption.attr('data-outlet') || '{}');

                        // Set text outlet & brand
                        $('#outlet_name').val(outlet.nama_outlet || '-');
                        $('#brand_name').val(outlet.brand ? outlet.brand.nama_brand : '-');

                        // Set hidden input
                        $('#outlet_id_hidden').val(outlet.id || '');
                        $('#brand_id_hidden').val(outlet.brand ? outlet.brand.id : '');

                        // Generate target container
                        const container = $('#targetSCS');
                        container.empty();
                        container.append(`
                                <label for="file" class="form-label">Upload data aktual</label>
                                <input type="file" name="file" id="file" class="form-control" accept=".xlsx,.xls" required>
                                <p style="font-size: 12px; margin-top: 4px;">Silakan upload file Excel berisikan data Aktual</p>
                        `);
                        
                    });
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                }
            });
        } else {
            $('#formAktual')[0].reset();
            $('#targetSCS').empty();
        }
    });

    // Tombol batal
    $('#cancelScs').click(function() {
        $('#divFormAktual').toggle('show');
        $('#formAktual')[0].reset();
        $('#targetSCS').empty();
    });

    $('#btnDownloadTemplatePIC').click(function(e) {
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
                link.download = 'Template-Aktual.xlsx';
                link.click();
            },
            error: function(xhr, status, error) {
                alert('Gagal download template!');
                console.error(error);
            }
        });
    });

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

    
    
    
});
    
</script>
<script>
$(document).ready(function() {
    let collapsedGroups = {};
    let table = $('.aktual-table').DataTable({
      serverSide: true,
      processing: true,
      ajax: '/aktualAPI',
      columns: [
        { data: 'DT_RowIndex', searchable: false, sortable: false },
        // { data: 'promosi.judul_promosi' },
        {
            data: 'sales_number',
            render: function (data, type, row) {
                return data ? data : 'data belum ada';
            }
        },
        {
            data: 'menu_category',
            render: function (data, type, row) {
                return data ? data : 'data belum ada';
            }
        },
        // {
        //     data: 'nama_outlet',
        //     render: function (data, type, row) {
        //         return data ? data : 'data belum ada';
        //     }
        // },
        
        { data: 'aksi', searchable: false, sortable: false }
      ],
      order: [[1, 'asc']],
      rowGroup: {
            dataSrc: 'promosi.judul_promosi',
            startRender: function (rows, group) {
            // ⏬ SET DEFAULT COLLAPSED kalau group belum pernah di klik
            if (typeof collapsedGroups[group] === 'undefined') {
                collapsedGroups[group] = true; // TRUE = collapsed default
            }
            let collapsed = collapsedGroups[group];

            rows.nodes().each(function (r) {
                if (collapsed) {
                $(r).hide();
                } else {
                $(r).show();
                }
            });

            return $('<tr/>')
                .append('<td colspan="9" style="cursor:pointer; font-weight:bold;">' + 
                (collapsed ? '➕ ' : '➖ ') + group + '</td>')
                .attr('data-name', group)
                .toggleClass('collapsed', collapsed);
            }
        }
    });

    $('.aktual-table tbody').on('click', 'tr.dtrg-group', function () {
        let group = $(this).data('name');
        collapsedGroups[group] = !collapsedGroups[group];
        table.draw(false); // redraw halaman sekarang
    });

    
    // Inisialisasi AJAX dengan default settings termasuk CSRF dan error handling
});

// Fungsi delete terpadu dengan konfigurasi khusus DELETE
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
                $('.aktual-table').DataTable().ajax.reload();
                // if (typeof callback === 'function') {
                //     callback(response);
                // } else {
                //     // Default behavior
                //     alert(response.message || 'Data berhasil dihapus');
                //     if (typeof table !== 'undefined') {
                //         table.ajax.reload();
                //     }
                // }
            },
            error: function (xhr) {
                console.log(xhr.responseText);
            }
        })
    }
}

function editForm(url) {
    $.get(url)
    .done(function(data) {
        $('#edit-sales_number').val(data.sales_number);
        $('#edit-menu_code').val(data.menu_code);
        $('#edit-menu').val(data.menu);
        $('#edit-branch').val(data.branch);
        $('#edit-brand').val(data.brand);
        $('#edit-city').val(data.city);
        $('#edit-visit_purpose').val(data.visit_purpose);
        $('#edit-payment_method').val(data.payment_method);
        $('#edit-menu_code').val(data.menu_code);
        $('#edit-menu_category').val(data.menu_category);
        $('#edit-menu_category_detail').val(data.menu_category_detail);
        $('#edit-order_mode').val(data.order_mode);
        $('#edit-qty').val(data.qty);
        $('#edit-price').val(data.price);
        $('#edit-subtotal').val(data.subtotal);
        $('#edit-discount').val(data.discount);
        $('#edit-total').val(data.total);
        $('#edit-nett_sales').val(data.nett_sales);
        $('#edit-bill_discount').val(data.bill_discount);
        $('#edit-total_after_bill_discount').val(data.total_after_bill_discount);
        $('#edit-waiters').val(data.waiters);

        $('#formEditAktual').attr('action', '/aktualAPI/' + data.id);
        $('#editAktualModal').modal('show');
    })
    .fail(function() {
        alert('Gagal mengambil data.');
    });
}

$('#formEditAktual').submit(function(e) {
  e.preventDefault();

  var form = $(this);
  var url = form.attr('action');
  var formData = form.serialize();

  $.ajax({
    url: url,
    method: 'POST', // tetap pakai POST, karena ada @method('PUT') di form
    data: formData,
    success: function(response) {
      $('#editAktualModal').modal('hide');
      $('.aktual-table').DataTable().ajax.reload();
      alert('Data berhasil diperbarui!');
    },
    error: function(xhr) {
      alert('Terjadi error saat menyimpan.');
    }
  });
});
</script>

<script>
$(document).ready(function() {
    $('#formAktual').on('submit', function(e) {
        e.preventDefault(); // Mencegah submit form default

        // Siapkan data dari form
        let formData = new FormData(this);

        $.ajax({
            url: '/aktualAPI', // Ganti dengan route POST kamu
            type: 'POST',
            data: formData,
            processData: false, // Supaya FormData tidak diproses menjadi query string
            contentType: false, // Supaya jQuery tidak set Content-Type secara manual
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Kalau pakai Laravel atau framework yg pakai CSRF
            },
            success: function(response) {
                // Handle response sukses
                // console.log(response);
                alert('Data berhasil disimpan!');
                $('#divFormAktual').toggle('show');
                $('#formAktual')[0].reset();
                // Misalnya close modal, reset form, reload data table, dll
                $('.aktual-table').DataTable().ajax.reload();
                
            },
            error: function(xhr) {
                let response = xhr.responseJSON;

                if (response && response.errors) {
                    let messages = '';
                    $.each(response.errors, function(key, value) {
                        messages += `<li>${value[0]}</li>`;
                    });

                    $('.alert-danger ul').html(messages);
                    $('.alert-danger').show();

                    // Reset timer jika sebelumnya sudah ada
                    if (window.alertCountdown) {
                        clearInterval(window.alertCountdown);
                    }

                    let countdown = 5;
                    $('.alert-danger').append(`<div class="mt-2 text-sm text-muted">Menutup dalam <span id="countdown-timer">${countdown}</span> detik...</div>`);

                    window.alertCountdown = setInterval(function () {
                        countdown--;
                        $('#countdown-timer').text(countdown);
                        if (countdown <= 0) {
                            $('.alert-danger').hide().find('#countdown-timer').parent().remove();
                            clearInterval(window.alertCountdown);
                        }
                    }, 1000);

                } else {
                    alert('Terjadi kesalahan tak terduga.');
                }
            }
        });
    });

});
</script>
@endpush
