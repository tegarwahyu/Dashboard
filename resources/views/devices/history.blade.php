@extends('layouts.app')
@section('content')

<div class="container">
    <br>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Daftar History {{ $device->number }}</h1>
        <a href="{{ route('devices.index') }}" class="btn btn-primary">Kembali</a>
    </div>

    <!-- Form Filter -->
    <form method="GET" action="{{ route('devices.history', $device->id) }}" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="number"><b>Search Number</b></label>
                <input type="text" name="number" id="number" class="form-control" value="{{ old('number', $filters['number'] ?? '') }}" placeholder="Enter number">
            </div>
            <div class="col-md-3">
                <label for="start_date"><b>Start Date</b></label>
                <input type="text" name="start_date" id="start_date" class="form-control datepicker" value="{{ old('start_date', $filters['start_date'] ?? '') }}" placeholder="YYYY-MM-DD">
            </div>
            <div class="col-md-3">
                <label for="end_date"><b>End Date</b></label>
                <input type="text" name="end_date" id="end_date" class="form-control datepicker" value="{{ old('end_date', $filters['end_date'] ?? '') }}" placeholder="YYYY-MM-DD">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </div>
    </form>

    <!-- Tabel History -->
    <table id="historyTable" class="table table-bordered">
        <thead class="thead-light">
            <tr>
                <th>Number</th>
                <th>Dikirim</th>
                <th width="20%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($outboxData as $data)
            <tr>
                <td>{{ $data->number }}</td>
                <td>{{ \Carbon\Carbon::parse($data->updated_at)->format('Y-m-d H:i') }}</td>
                <td>
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-success show-chat" data-device-id="{{ $device->id }}" data-outbox-number="{{ $data->number }}">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Chat Panel -->
    <div id="chatPanel" class="chat-panel">
        <div class="chat-header">
            <h3 id="chatTitle"></h3>
            <button id="closeChatPanel" class="btn btn-danger btn-sm">&times;</button>
        </div>
        <div class="chat-body" id="chatRoom">
            <!-- Chat content will be dynamically loaded here -->
        </div>
    </div>

</div>

@include('layouts.footers.auth')
@endsection

@push('css')
<link href="{{ asset('assets/vendor/selectize/css/selectize.bootstrap3.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('assets/vendor/datatables/datatables.min.css') }}">
<link href="{{ asset('assets/vendor/bsdatepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
<link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet" />

<style>
    .chat-panel {
        position: fixed;
        right: -400px;
        top: 0;
        width: 400px;
        height: 100%;
        background-color: #f8f9fa;
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.5);
        transition: right 0.3s ease;
        z-index: 1050;
        display: flex;
        flex-direction: column;
    }

    .chat-header {
        background-color: #5e72e4;
        color: white;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chat-body {
        flex-grow: 1;
        padding: 15px;
        overflow-y: auto;
    }

    .bubble {
        display: inline-block;
        padding: 10px 15px;
        border-radius: 20px;
        margin-bottom: 10px;
        max-width: 75%;
    }


    .bubble.from-me {
        background-color: #5e72e4;
        color: white;
        text-align: right;
        align-self: flex-end;
        margin-left: auto; /* Agar bubble dari saya ke kanan */
    }

    .bubble.not-from-me {
        background-color: #bfa975;
        color: white;
        text-align: left;
        align-self: flex-start;
        margin-right: auto; /* Agar bubble dari orang lain ke kiri */
    }

    #chatRoom {
        display: flex;
        flex-direction: column;
        gap: 10px; /* Memberikan jarak antar pesan */
    }

    #chatTitle {
        color: #f8f9fa;
    }


    #closeChatPanel {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: white;
        cursor: pointer;
    }

    /* Datepicker Styling */
    .datepicker {
        background-color: white;
    }

    @media (max-width: 768px) {
        .chat-panel {
            width: 100%;
            right: -100%;
        }
    }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/vendor/bsdatepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('assets/vendor/jquery/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/vendor/jquery/jquery-validate.bootstrap-tooltip.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatables/handlebars.js') }}"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi DataTable
        $('#historyTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "pageLength": 10,
            "order": [[1, "desc"]]
        });

        // Inisialisasi Datepicker
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });

        // Buka panel chat ketika tombol "show-chat" diklik
        $(document).on('click', '.show-chat', function() {
            var deviceId = $(this).data('device-id');
            var outboxNumber = $(this).data('outbox-number');

            // Ajax call untuk mendapatkan chat history
            $.ajax({
                url: '/admin/device/' + deviceId + '/chats/' + outboxNumber,
                method: 'GET',
                success: function(data) {
                    var chatRoom = $('#chatRoom');
                    chatRoom.empty();

                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(function(chat) {
                            if (chat.conversation_from_me !== null) {
                                chatRoom.append(`<div class="bubble from-me">${escapeHtml(chat.conversation_from_me)}</div>`);
                            }
                            if (chat.conversation_not_from_me !== null) {
                                chatRoom.append(`<div class="bubble not-from-me">${escapeHtml(chat.conversation_not_from_me)}</div>`);
                            }
                        });
                    } else {
                        chatRoom.append('<p>No chat history available.</p>');
                    }

                    // Set judul chat dan tampilkan panel chat
                    $('#chatTitle').text('Chat Number: ' + outboxNumber);
                    $('#chatPanel').css('right', '0'); // Slide panel in
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error', 'Failed to fetch chat history', 'error');
                }
            });
        });

        // Tutup panel chat
        $('#closeChatPanel').on('click', function() {
            $('#chatPanel').css('right', '-400px'); // Slide panel out
        });

        // Fungsi untuk escape HTML guna mencegah XSS
        function escapeHtml(text) {
            return $('<div>').text(text).html();
        }
    });
</script>
@endpush
