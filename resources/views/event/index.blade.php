@extends('layouts.app')
<!-- daterangepicker CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.3.1/css/rowGroup.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
@push('styles')
<style>
    .img-modal {
        display: none;
        position: fixed;
        z-index: 1050;
        padding-top: 60px;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.7);
        backdrop-filter: blur(4px);
    }

    .img-modal-content {
        margin: auto;
        display: block;
        max-width: 80%;
        max-height: 80%;
        animation: zoomIn 0.3s ease;
    }

    .img-modal-close {
        position: absolute;
        top: 20px;
        right: 35px;
        color: white;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
    }

    @keyframes zoomIn {
        from {transform: scale(0.7);}
        to {transform: scale(1);}
    }

    .preview-img {
        cursor: pointer;
        transition: 0.3s;
    }

    .preview-img:hover {
        opacity: 0.8;
    }

    /* Perkecil font tabel */
    .dataTables_wrapper table.dataTable {
        font-size: 12px; /* atau 11px sesuai kebutuhan */
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
@endpush
@section('content')
<!-- Begin Page Content -->
    <div class="container-fluid">
        
        @include('event.modal_upload_event')
        @include('event.modal_edit_event')
        @include('event.modal_export_event')
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-2 text-gray-800">Tabel Data Promosi Semua Outlet</h1>

            <div class="d-flex gap-2">
                
                <!-- <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="createForm()">
                    <i class="fas fa-edit"></i>
                </a> -->
                <a href="#" onclick="createPromosi()" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" title="Unggah Promosi">
                    <!-- <i class="fas fa-file-download"></i> -->
                    <i class="fas fa-edit"></i>
                </a>

                @if(in_array(Auth::user()->role, ['aspv', 'ma', 'Super Admin']))
                <a href="#" id="openExportModal" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" title="Export Posting">
                    <i class="fas fa-file-export"></i>
                </a>
                @endif
            </div>
        </div>
        
        <!-- Content Row -->
        @if (session('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="row align-items-center">
                    <!-- Judul -->
                    <div class="col-md-4 mb-2 mb-md-0">
                    <h6 class="m-0 font-weight-bold text-primary">Data Promosi</h6>
                    </div>

                    <!-- Field & Tombol -->
                    <div class="col-md-8">
                    <div class="row g-2">
                        <!-- Export Data Button -->
                        <div class="col-auto">
                            <!-- <button type="button" class="btn btn-success btn-sm">
                                <i class="fas fa-file-export"></i> Export Data
                            </button> -->
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="box-body table-responsive">
                    <table class="table table-striped table-bordered" id="eventTable" style="width:100%">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Judul</th>
                                <th>Gambar</th>
                                <th>Deskripsi</th>
                                <th>Aktifitas</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Akhir</th>
                                <th>Outlet</th>
                                <!-- <th>Jenis Promosi</th> -->
                                <th width="10%"><i class="fa fa-cog"></i></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>


@endsection

<!-- Modal -->
<div class="modal fade" id="deskripsiModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #345ca5;color: white;">
        <h5 class="modal-title">Detail Deskripsi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p id="fullDeskripsi"></p>
      </div>
    </div>
  </div>
</div>

{{-- Modal Preview --}}
<div id="imageModal" class="img-modal" onclick="closeModal()">
    <span class="img-modal-close" onclick="closeModal()">&times;</span>
    <img class="img-modal-content" id="modalImage">
</div>

@push('scripts')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/min/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<!-- DataTables RowGroup extension JS -->
<script src="https://cdn.datatables.net/rowgroup/1.3.1/js/dataTables.rowGroup.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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

     $(document).on('click', '.img-thumbnail', function(e) {
        e.stopPropagation();
        var modal = $('#imageModal');
        var modalImg = $('#modalImage');

        modal.show();
        modalImg.attr('src', $(this).attr('src'));
    });

    // Tutup modal
    $('#closeModal').on('click', function() {
        $('#imageModal').hide();
    });

    // Tutup modal kalau klik di luar gambar
    $('#imageModal').on('click', function(e) {
        if (e.target.id === 'imageModal') {
            $('#imageModal').hide();
        }
    });

    function closeModal() {
        document.getElementById("imageModal").style.display = "none";
    }

    $(document).on('click', '.show-desc', function(e) {
        e.preventDefault();
        let fullDesc = $(this).data('desc');
        $('#fullDeskripsi').text(fullDesc);
        $('#deskripsiModal').modal('show');
    });
</script>

<script>
    function createPromosi() {
        $('#eventPromoModal').modal('show');
    }
    $(document).ready(function() {
        


        // // 8) Trigger Edit Promosi
        // $('.edit-btn').on('click', function() {
        //     let id = $(this).data('id');
        //     $.ajax({
        //         url: '/promosi/' + id,
        //         type: 'GET',
        //         success: function(data) {
        //             $('#eventEditPromoForm').attr('action', '/promosi/' + id);
        //             $('#formMethod2').val('PATCH');

        //             $('#jenis_event2').val(data.jenis_promosi);
        //             $('#judul2').val(data.judul_promosi);
        //             $('#deskripsi2').val(data.deskripsi);

        //             $('#brand_id2').val(data.outlet.brand.id).trigger('change');

        //             if (data.promosi_kpi && data.promosi_kpi.length > 0) {
        //                 const item = data.promosi_kpi[0]; 
        //                 const kpiForHiddenField = [{
        //                     outlet_id: String(data.outlet.id), 
        //                     mulai: data.mulai_promosi ? data.mulai_promosi.split(' ')[0] : '', 
        //                     akhir: data.akhir_promosi ? data.akhir_promosi.split(' ')[0] : '', 
        //                     traffic: item ? item.traffic : '', 
        //                     pax: item ? item.pax : '',
        //                     bill: item ? item.bill : '',
        //                     budget: item ? item.budget : '',
        //                     sales: item ? item.sales : ''
        //                 }];
        //                 $('#outletDateTimeField2').val(JSON.stringify(kpiForHiddenField));
        //             } else {
        //                 $('#outletDateTimeField2').val(''); 
        //             }

        //             $(document).one('ajaxStop', function() {
        //                 const outletIdToSelect = String(data.outlet.id); 
                        
        //                 if (outletChoicesInstance) {
        //                     outletChoicesInstance.setChoiceByValue(outletIdToSelect);
        //                 } else {
        //                     $('#outlet_id2').val([outletIdToSelect]);
        //                 }
                        
        //                 $('#outlet_id2').trigger('change');
        //             });

        //             if (data.img_path) {
        //                 $('#logo-preview-edit2').attr('src', data.img_path).show();
        //                 $('#poster2').removeAttr('required');
        //             } else {
        //                 $('#logo-preview-edit2').hide();
        //                 $('#poster2').attr('required', true); 
        //             }

        //             $('#eventeditPromoModal').modal('show');
        //         },
        //         error: function(xhr, status, error) {
        //             console.error("Error fetching promotion data:", status, error, xhr.responseText);
        //             alert("Terjadi kesalahan saat memuat data promosi. Silakan coba lagi.");
        //         }
        //     });
        // });

        
    });

    $('#openExportModal').click(function() {
        $('#eventExportModal').modal('show');
    });

    // Inisialisasi sekali, di luar klik modal!
    $(document).ready(function() {
        $('#dateRange').daterangepicker({
            opens: 'left',
            autoUpdateInput: false,
            locale: {
            cancelLabel: 'Batal',
            applyLabel: 'Pilih',
            format: 'YYYY-MM-DD'
            }
        });

        // fungsi export promosi
        $('#dateRange').on('apply.daterangepicker', function(ev, picker) {
            // Set input
            $(this).val(
            picker.startDate.format('YYYY-MM-DD') + ' s/d ' + picker.endDate.format('YYYY-MM-DD')
            );

            // Panggil AJAX GET ke Laravel route
            $.ajax({
                url: '/promosiAPI/export-data-promosi',
                type: 'GET',
                data: {
                    start_date: picker.startDate.format('YYYY-MM-DD'),
                    end_date: picker.endDate.format('YYYY-MM-DD')
                },
                success: function(response) {
                    console.log('Data berhasil:', response);
                    // $('#export_judul_promosi').val(response[0].judul_promosi);
                    // Lakukan sesuatu: render tabel, download file, dll.
                    let judulContainer = $('#export_judul_promosi_container');

                    if (response.length > 1) {
                        // Lebih dari 1 data: SELECT
                        let select = $('<select></select>')
                        .attr('name', 'export_judul_promosi')
                        .attr('id', 'export_judul_promosi')
                        .addClass('form-control')
                        .attr('required', true);

                        select.append('<option value="" disabled selected>Pilih Judul Promosi</option>');

                        response.forEach(function(item) {
                        select.append(`<option value="${item.judul_promosi}">${item.judul_promosi}</option>`);
                        });

                        judulContainer.html(select);

                    } else if (response.length === 1) {
                        // Hanya 1 data: INPUT text, readonly biar user nggak bisa ubah
                        let input = $('<input>')
                        .attr('type', 'text')
                        .attr('name', 'export_judul_promosi')
                        .attr('id', 'export_judul_promosi')
                        .addClass('form-control')
                        .attr('placeholder', 'Judul Promosi')
                        .attr('readonly', true)  // ✅ Tetap terkirim ke server
                        .val(response[0].judul_promosi);

                        judulContainer.html(input);

                    } else {
                        // Kosong: input readonly kosong
                        let input = $('<input>')
                        .attr('type', 'text')
                        .attr('name', 'export_judul_promosi')
                        .attr('id', 'export_judul_promosi')
                        .addClass('form-control')
                        .attr('placeholder', 'Tidak ada data promosi')
                        .attr('readonly', true);

                        judulContainer.html(input);
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                }
            });
        });

        $('#dateRange').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });

</script>

<script>
    // Tombol Hapus
$(document).on('click', '.delete-btn', function () {
  const id = $(this).data('id');
  if (confirm('Yakin ingin menghapus data ini?')) {
    $.ajax({
        url: `/promosi/${id}`,
        type: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: { '_token': '{{ csrf_token() }}' },
        success: function (data) {
            $('#eventTable').DataTable().ajax.reload(null, false);
        },
        error: function (xhr) {
            console.error(xhr.responseText);
        }
    });
  }
});

// Tombol Activate/Deactivate
$(document).on('click', '.change-status-btn', function () {
  const id = $(this).data('id');
  if (confirm('Yakin ingin menonaktifkan promosi ini?')) {
    $.ajax({
        url: `/promosi/deactivated/${id}`,
        type: 'PUT',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: { '_token': '{{ csrf_token() }}' },
        success: function (data) {
            $('#eventTable').DataTable().ajax.reload(null, false);
        },
        error: function (xhr) {
            console.error(xhr.responseText);
        }
    });
  }
});

$(document).on('click', '.edit-btn', function() {
    let id = $(this).data('id');

    $.ajax({
        url: '/promosi/' + id,
        type: 'GET',
        success: function(data) {
            $('#eventEditPromoForm').attr('action', '/promosi/update/' + id);
            $('#formMethod2').val('PATCH');

            $('#jenis_event2').val(data.jenis_promosi);
            $('#judul2').val(data.judul_promosi);
            $('#deskripsi2').val(data.deskripsi);

            $('#brand_id2').val(data.outlet.brand.id).trigger('change');

            if (data.promosi_kpi && data.promosi_kpi.length > 0) {
                const item = data.promosi_kpi[0];
                const kpiForHiddenField = [{
                    outlet_id: String(data.outlet.id),
                    mulai: data.mulai_promosi ? data.mulai_promosi.split(' ')[0] : '',
                    akhir: data.akhir_promosi ? data.akhir_promosi.split(' ')[0] : '',
                    traffic: item.traffic || '',
                    pax: item.pax || '',
                    bill: item.bill || '',
                    budget: item.budget || '',
                    sales: item.sales || ''
                }];
                $('#outletDateTimeField2').val(JSON.stringify(kpiForHiddenField));
            } else {
                $('#outletDateTimeField2').val('');
            }

            $(document).one('ajaxStop', function() {
                const outletIdToSelect = String(data.outlet.id);
                if (outletChoicesInstance) {
                    outletChoicesInstance.setChoiceByValue(outletIdToSelect);
                } else {
                    $('#outlet_id2').val([outletIdToSelect]);
                }
                $('#outlet_id2').trigger('change');
            });

            if (data.img_path) {
                $('#logo-preview-edit2').attr('src', data.img_path).show();
                $('#poster2').removeAttr('required');
            } else {
                $('#logo-preview-edit2').hide();
                $('#poster2').attr('required', true);
            }

            $('#eventeditPromoModal').modal('show');
        },
        error: function(xhr) {
            console.error("Error fetching promotion data:", xhr.responseText);
            alert("Terjadi kesalahan saat memuat data promosi. Silakan coba lagi.");
        }
    });

    let lastOutletData = [];
    let outletChoicesInstance = null;

    // 1) Load Outlet jika Brand dipilih
    $('#brand_id2').on('change', function() {
        const brandID = $(this).val();
        if (brandID) {
            $.ajax({
                url: '/get-outlet-edit/' + brandID,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    lastOutletData = data; // Ini seharusnya array objek outlet: [{id:.., nama_outlet:..}, ...]
                    
                    // Pastikan ID yang dirender adalah 'outlet_id2'
                    const html = renderSelect('outlet_id2', data);
                    $('#outletContainer2').html(html);

                    // Re-init Choices.js dan simpan instancenya
                    if (outletChoicesInstance) {
                        outletChoicesInstance.destroy();
                    }
                    outletChoicesInstance = new Choices('#outlet_id2', {
                        removeItemButton: true,
                        placeholderValue: 'Pilih Outlet',
                        searchPlaceholderValue: 'Cari Outlet'
                    });

                    // Kalau di hidden ada outlet_datetime (edit), set selected
                    // if ($('#outletDateTimeField2').val()) {
                    //     const dt = JSON.parse($('#outletDateTimeField2').val());
                    //     // Ambil hanya ID outlet dan pastikan bertipe string
                    //     const selected = dt.map(o => String(o.outlet_id)); 
                        
                    //     if (outletChoicesInstance) {
                    //         outletChoicesInstance.setChoiceByValue(selected);
                    //     } else {
                    //         $('#outlet_id2').val(selected);
                    //     }
                    //     // Pemicu 'change' di sini untuk mengisi data KPI saat page load/edit
                    //     $('#outlet_id2').trigger('change'); 
                    // }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching outlets:", status, error, xhr.responseText);
                    alert("Terjadi kesalahan saat memuat data outlet.");
                }
            });
        } else {
            $('#outletContainer2').empty();
            if (outletChoicesInstance) {
                outletChoicesInstance.destroy();
                outletChoicesInstance = null;
            }
        }
    });

    // --- PERBAIKAN UTAMA DI SINI ---
    // 2) Buat <option>
    // Mengasumsikan data dari /get-outlet/{brandID} adalah array objek outlet langsung,
    // seperti: [{id: 8, nama_outlet: "Outlet D", ...}]
    // function createOptions(opt) {
    //     // console.log("Creating option for:", opt); // Debug: Periksa struktur 'opt'
    //     return `<option value="${opt.outlet.id}">${opt.outlet.nama_outlet}</option>`;
    // }
    // function createOptions(opt) {
    //     // console.log(opt.outlet)
    //     // if(isEmptyObject(opt.outlet)){
    //     //     return `<option value="${opt.id}">${opt.nama_outlet}</option>`;
    //     // }else {
    //         return `<option value="${opt.outlet.id}">${opt.outlet.nama_outlet}</option>`;
    //     // }
        
    // }

    // 3) Buat <select>
    // function renderSelect(id, dataArray) {
    //     const options = dataArray.map(opt => createOptions(opt)).join('');
    //     return `
    //         <div class="form-group">
    //             <label for="${id}" class="form-label">Outlet</label>
    //             <select name="${id}[]" id="${id}" class="form-select" multiple required>
    //                 ${options}
    //             </select>
    //         </div>
    //     `;
    // }

    // // 4) Buat Date + Table KPI
    // function generateOutletDateFields(selectedOutlets2, dataArray2) {
    //         const container = $('#outletDateFieldsContainer2');
    //         container.empty();

    //         selectedOutlets2.forEach(outletId => {
    //             const outletData = dataArray2.find(o => String(o.outlet.id) === String(outletId));
    //                 if (!outletData) {
    //                     console.warn(`Outlet data not found for ID: ${outletId}`);
    //                 return;
    //             }
    //         $('#id').val(outletData.id);
    //         $('#img_path_edit').val(outletData.img_path);
    //         $('#promosi_kpi_id').val(outletData.promosi_kip[0].id);
    //         const mulaiPromosi = outletData.mulai_promosi.split(' ')[0];
    //         const akhirPromosi = outletData.akhir_promosi.split(' ')[0];
    //         // console.log(outletData)
    //         if (outletData) { 
    //             container.append(`
    //                 <h6><strong>${outletData.outlet.nama_outlet}</strong></h6>
    //                 <div class="row mb-2">
    //                     <div class="col-md-3">
    //                         <div class="mb-2">
    //                             <label>Mulai Promosi:</label>
    //                             <input type="date" class="form-control outlet-mulai" id="mulai_date_edit" name="mulai_date_edit" value="${mulaiPromosi}">
    //                         </div>
    //                         <div class="mb-2">
    //                             <label>Akhir Promosi:</label>
    //                             <input type="date" class="form-control outlet-akhir" id="akhir_date_edit" name="akhir_date_edit" value="${akhirPromosi}">
    //                         </div>
    //                     </div>
    //                     <div class="col-md-9">
    //                         <div class="table-responsive">
    //                             <table class="table table-bordered table-sm mb-0 text-center align-middle">
    //                                 <thead class="table-success">
    //                                     <tr><th>Traffic</th><th>Pax</th><th>Bill</th><th>Budget/hari</th></tr>
    //                                 </thead>
    //                                 <tbody>
    //                                     <tr>
    //                                         <td><input type="number" min="0" class="form-control traffic-input" name="traffic_edit" id="traffic_edit" value="${outletData.promosi_kip[0].traffic}"></td>
    //                                         <td><input type="number" min="0" class="form-control pax-input" name="pax_edit" id="pax_edit" value="${outletData.promosi_kip[0].pax}"></td>
    //                                         <td><input type="number" min="0" class="form-control bill-input" name="bill_edit" id="bill_edit" value="${outletData.promosi_kip[0].bill}"></td>
    //                                         <td>
    //                                             <div class="input-group">
    //                                                 <span class="input-group-text">Rp</span>
    //                                                 <input type="number" min="0" class="form-control budget-input" name="budget_edit" id="budget_edit" value="${outletData.promosi_kip[0].budget}">
    //                                             </div>
    //                                         </td>
    //                                     </tr>
    //                                 </tbody>
    //                                 <thead class="table-success"><tr><th colspan="4">Sales</th></tr></thead>
    //                                 <tbody>
    //                                     <tr>
    //                                         <td colspan="4">
    //                                             <div class="input-group">
    //                                                 <span class="input-group-text">Rp</span>
    //                                                 <input type="number" min="0" class="form-control sales-input" name="sales_edit" id="sales_edit" value="${outletData.promosi_kip[0].sales}">
    //                                             </div>
    //                                         </td>
    //                                     </tr>
    //                                 </tbody>
    //                             </table>
    //                         </div>
    //                     </div>
    //                 </div>
    //                 <hr style="border: 1px solid black;">
    //             `);
    //         } else {
    //             console.warn(`Outlet data (nested) not found for ID: ${outletId}`);
    //         }
    //     });
    // }

    // // 6) Outlet select listener
    // $(document).on('change', '#outlet_id2', function() {
    //     const selected = Array.isArray($(this).val()) ? $(this).val() : [];
    //     if (selected && selected.length) {
    //         // Panggil generateOutletDateFields dengan data yang sesuai
    //         generateOutletDateFields(selected, lastOutletData);
            
    //         // Setelah field dibuat/diperbarui, isi dengan data lama jika ada
    //         if ($('#outletDateTimeField2').val()) {
    //             const storedData = JSON.parse($('#outletDateTimeField2').val());
    //             selected.forEach(outletId => {
    //                 const kpiData = storedData.find(d => String(d.outlet_id) === String(outletId));
    //                 if (kpiData) {
    //                     $(`.outlet-mulai[data-outlet-id="${outletId}"]`).val(kpiData.mulai);
    //                     $(`.outlet-akhir[data-outlet-id="${outletId}"]`).val(kpiData.akhir);
    //                     $(`.traffic-input[data-outlet-id="${outletId}"]`).val(kpiData.traffic);
    //                     $(`.pax-input[data-outlet-id="${outletId}"]`).val(kpiData.pax);
    //                     $(`.bill-input[data-outlet-id="${outletId}"]`).val(kpiData.bill);
    //                     $(`.budget-input[data-outlet-id="${outletId}"]`).val(kpiData.budget);
    //                     $(`.sales-input[data-outlet-id="${outletId}"]`).val(kpiData.sales);
    //                 }
    //             });
    //         }
    //     } else {
    //         $('#outletDateFieldsContainer2').empty();
    //     }
    //     generateOutletDateTimeArray(); 
    // });

    // function generateOutletDateTimeArray() {
    //     const data = [];
    //     $('#outletDateFieldsContainer2').find('.row.mb-2').each(function() {
    //         const outletId = $(this).data('outlet-id');
    //         const mulai = $(this).find('.outlet-mulai').val();
    //         const akhir = $(this).find('.outlet-akhir').val();
    //         const traffic = $(this).find('.traffic-input').val();
    //         const pax = $(this).find('.pax-input').val();
    //         const bill = $(this).find('.bill-input').val();
    //         const budget = $(this).find('.budget-input').val();
    //         const sales = $(this).find('.sales-input').val();

    //         data.push({
    //             outlet_id: outletId,
    //             mulai: mulai,
    //             akhir: akhir,
    //             traffic: traffic,
    //             pax: pax,
    //             bill: bill,
    //             budget: budget,
    //             sales: sales
    //         });
    //     });
    //     $('#outletDateTimeField2').val(JSON.stringify(data));
    // }

    // Handle submit form edit promosi
    $('#eventEditPromoForm').on('submit', function(e) {
        e.preventDefault(); // Mencegah submit form default
        generateOutletDateTimeArray();
        let formData = new FormData(this);
        // Laravel perlu _method PATCH/PUT untuk route update
        // formData.append('_method', 'PATCH'); // Ini sudah dihandle oleh hidden input id="formMethod2"

        // Ambil ID dari hidden input atau variabel currentPromoId
        // Anda sudah mengatur action form secara dinamis di event edit-btn click
        // const formActionUrl = $(this).attr('action');
        var id = $('#id').val();
        $.ajax({
            url: '/promosi/update/' + id, // URL akan '/promosi/update/{id}'
            type: 'POST', // Metode POST karena _method PATCH digunakan
            data: formData,
            processData: false, 
            contentType: false, 
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Ambil CSRF dari meta tag
            },
            success: function(response) {
                // alert('Promosi berhasil diperbarui!');
                $('#eventEditPromoForm')[0].reset();
                $('#eventeditPromoModal').modal('hide');
                // Jika ada DataTable yang menampilkan promosi, reload datanya
                // $('.aktual-table').DataTable().ajax.reload(); 
            },
            error: function(xhr) {
                console.error("Error updating promotion:", xhr.responseText);
                let errorMessage = 'Terjadi kesalahan saat memperbarui promosi. Silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
            }
        });
    });
});

</script>

<!-- js untuk datatable  -->

<script>
  $(document).ready(function() {
    $('#eventTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("event.data") }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'judul_promosi', name: 'judul_promosi' },
             { data: 'gambar', name: 'gambar' }, // BUKAN img_path
            { data: 'deskripsi', name: 'deskripsi' },
            { data: 'status', name: 'status' },
            { data: 'mulai_promosi', name: 'mulai_promosi' },
            { data: 'akhir_promosi', name: 'akhir_promosi' },
            { data: 'nama_outlet', name: 'tb_outlet.nama_outlet' },
            // { data: 'jenis_promosi', name: 'jenis_promosi' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false },
        ],
        order: [[1, 'asc']],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Data tidak ditemukan",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            },
        },
    });


    // document.querySelectorAll('.img-thumbnail').forEach(function(img) {
    //     img.addEventListener('click', function(event) {
    //         event.stopPropagation();
    //         var modal = document.getElementById("imageModal");
    //         var modalImg = document.getElementById("modalImage");
    //         modal.style.display = "block";
    //         modalImg.src = this.dataset.src;
    //     });
    // });

  });
</script>

@endpush
  
