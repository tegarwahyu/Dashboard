@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <br>
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>Pesan</h1>
                <div>
                    <a href="{{ asset('assets/template/template_sender_wa.xlsx') }}" class="btn btn-success mb-3" download>
                        Download Template
                    </a>
                    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addMessageModal">
                        Tambah Pesan
                    </button>
                </div>
            </div>
            <table id="outboxTable" class="table table-bordered">
                <thead>
                    <tr class="table-info">
                        <th>No</th>
                        <th>PERANGKAT</th>
                        <th>PENERIMA</th>
                        <th class="wrap-text">PESAN</th>
                        <th>Status</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($outboxes as $key => $outbox)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $outbox->device ? $outbox->device->name : 'N/A' }}</td>
                            <td>{{ $outbox->number }}</td>
                            <td class="wrap-text">{{ $outbox->text }}</td>
                            <td>
                                @if($outbox->status == 'sent')
                                <span class="badge badge-success">{{ $outbox->status }}</span>
                                @elseif($outbox->status == 'failed')
                                <span class="badge badge-danger">{{ $outbox->status }}</span>
                                @elseif($outbox->status == 'pending')
                                <span class="badge badge-info">{{ $outbox->status }}</span>
                                @endif
                            </td>
                            <td>{{ $outbox->created_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="addMessageModal" tabindex="-1" role="dialog" aria-labelledby="addMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 60%;">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="addMessageModalLabel">Kirim Pesan</h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addMessageForm">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="device">Perangkat</label>
                                                    
                            <select class="form-control" id="device" name="device" style="height: 50px;">
                                <option value="" disabled selected>Pilih Perangkat</option>
                                @foreach($connectedDevices as $device)
                                    <option value="{{ $device->id }}">{{ $device->name }} ({{ $device->number }})</option>
                                @endforeach
                            </select>
			            </div>
                        <div class="form-group col-md-6">
                            <label for="numbers">Import Nomor</label>
                            <div id="numberInputs">
                                <input type="file" class="form-control" id="fileInput" accept=".xlsx, .xls, .csv" style="height: 50px;">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="text">Teks Pesan</label>
                        <textarea class="form-control" id="text" name="text" rows="100" cols="70"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="invalidNumbers">Status Pengecekan</label>
                        <div id="numberInputs">
                            <textarea id="invalidNumbers" class="form-control mt-2" rows="100" cols="70" readonly></textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-warning mr-2" id="checkNumbersButton">Check</button>
                        <button type="button" class="btn btn-primary" id="sendMessageButton" disabled>Kirim Pesan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

        {{-- Selectize CSS via CDN --}}
        <link href="https://cdn.jsdelivr.net/npm/selectize@0.12.6/dist/css/selectize.bootstrap3.css" rel="stylesheet" />

        {{-- DataTables CSS --}}
       <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />


        {{-- Bootstrap Datepicker CSS via CDN --}}
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet" />

        <style>
            .table td.wrap-text {
                white-space: normal;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 300px;
            }

            #invalidNumbers,
            #text {
                width: 100%;
                height: 200px;
            }
        </style>

        {{-- jQuery --}}
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        {{-- DataTables JS --}}
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>


        {{-- SheetJS --}}
        <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>

        {{-- SweetAlert2 --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        {{-- Bootstrap Datepicker --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

        {{-- jQuery Validate --}}
        <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

        {{-- jQuery Validate Bootstrap Tooltip --}}
        <script src="https://cdn.jsdelivr.net/npm/jquery-validation-bootstrap-tooltip@0.10.0/js/jquery-validate.bootstrap-tooltip.min.js"></script>

        

        {{-- Handlebars.js --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js"></script>

        <script>
            // Your inline JS logic here
        </script>
    <script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
    console.log("jQuery loaded:", typeof $ !== 'undefined');
    console.log("DataTable function available:", typeof $.fn.DataTable !== 'undefined');

    $('#outboxTable').DataTable();
});


    $(document).ready(function() {
        $('#outboxTable').DataTable();

        function formatPhoneNumber(number) {
            if (number.charAt(0) === '0') {
                return '62' + number.slice(1);
            } else if (number.charAt(0) === '8') {
                return '62' + number;
            }
            return number;
        }

        function shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        }

        $('#fileInput').change(function(e) {
            var file = e.target.files[0];
            var reader = new FileReader();

            reader.onload = function(e) {
                var data = new Uint8Array(e.target.result);
                var workbook = XLSX.read(data, { type: 'array', cellText: false, cellDates: true });
                var firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                var rows = XLSX.utils.sheet_to_json(firstSheet, { header: 1, raw: true });

                var contacts = [];
                var savedNumbers = [];

                rows.forEach((row, index) => {
                    if (index > 0 && row[1]) { // Assuming fullname is in the first column
                        var contact = {
                            fullname: row[0] ? row[0].toString() : '',
                            number: row[1] ? formatPhoneNumber(row[1].toString()) : '',
                            savedNumber: row[2] ? formatPhoneNumber(row[2].toString()) : ''
                        };
                        contacts.push(contact);
                    }
                    if (index > 0 && row[2]) {
                        var savedNumber = row[2].toString();
                        savedNumbers.push(formatPhoneNumber(savedNumber));
                    }
                });

                savedNumbers = shuffleArray(savedNumbers);

                var contactsWithSaved = [];
                var savedIndex = 0;
                contacts.forEach((contact, index) => {
                    contactsWithSaved.push(contact);
                    if ((index + 1) % 4 == 0 && savedIndex < savedNumbers.length) {
                        contactsWithSaved.push({ fullname: '', number: savedNumbers[savedIndex], savedNumber: '' });
                        savedIndex++;
                    }
                });

                $('#numberInputs').data('contacts', contactsWithSaved);
            };

            reader.readAsArrayBuffer(file);
        });

        $('#checkNumbersButton').click(function() {
            console.log('jalan coy')
            var deviceText = $('#device option:selected').text();
        var deviceName = deviceText.split(' (')[0];
            var contacts = $('#numberInputs').data('contacts');
            var invalidContacts = [];

            var promises = contacts.map((contact) => {
                return $.ajax({
                    url: `{{ env('URL_WA_SERVER') }}/${deviceName}/contacts/${contact.number}@s.whatsapp.net`,
                    type: 'GET'
                }).then(response => {
                    if (!response.exists) {
                        invalidContacts.push(contact);
                    }
                }).fail(() => {
                    invalidContacts.push(contact);
                });
            });

            Promise.all(promises).then(() => {
                if (invalidContacts.length == 0) {
                    $('#invalidNumbers').val("Semua nomor valid.");
                    $('#sendMessageButton').prop('disabled', false);
                } else {
                    var formattedInvalidContacts = invalidContacts.map(contact => `Nomor ${contact.number} milik ${contact.fullname} tidak valid.`).join("\n");
                    $('#invalidNumbers').val(formattedInvalidContacts);
                    $('#sendMessageButton').prop('disabled', invalidContacts.length == contacts.length);
                }
            });
        });

        $('#sendMessageButton').click(function() {
            Swal.fire({
                title: 'Apakah Anda yakin pesan Anda sesuai?',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then((result) => {
                if (result.isConfirmed) {
                    var invalidNumbers = $('#invalidNumbers').val().split('\n').map(line => {
                        let match = line.match(/Nomor (.*) milik .* tidak valid./);
                        return match ? match[1] : null;
                    }).filter(Boolean);

                    var allContacts = $('#numberInputs').data('contacts');
                    var validContacts = allContacts.filter(contact => {
                        return !invalidNumbers.includes(contact.number);
                    });

                    if (validContacts.length === 0) {
                        Swal.fire({
                            title: 'Gagal',
                            text: 'Tidak ada nomor valid untuk dikirim pesan',
                            icon: 'error'
                        });
                        return;
                    }

                    var originalMessage = $('#text').val();

                    var personalizedMessages = validContacts.map(contact => {
                        var personalizedMessage = originalMessage.replace(/\$nama/g, contact.fullname || 'Pelanggan');
                        return {
                            number: contact.number,
                            message: personalizedMessage
                        };
                    });

                    var formData = $('#addMessageForm').serializeArray();
                    formData.push({ name: 'messages', value: JSON.stringify(personalizedMessages) });

                    Swal.fire({
                        title: 'Mengirim pesan...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    $.ajax({
                        url: '{{ route("outbox.store") }}',
                        type: 'POST',
                        data: $.param(formData),
                        success: function(response) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: 'Pesan berhasil dikirim',
                                icon: 'success'
                            }).then(() => {
                                $('#addMessageModal').modal('hide');
                                location.reload();
                            });
                        },
                        error: function(xhr, status, error) {
                            console.log(xhr.responseText);
                            Swal.fire({
                                title: 'Gagal',
                                text: xhr.responseText || 'Terjadi kesalahan saat mengirim pesan',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });
    });
    </script>