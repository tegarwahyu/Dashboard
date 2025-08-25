@extends('layouts.app') 

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="container">
            <br>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>Daftar Perangkat</h1>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#deviceModal" data-mode="add">
                    Tambah Perangkat Baru
                </button>
            </div>

            <table id="deviceTable" class="table table-bordered table-striped">
                <thead>
                    <tr class="table-info">
                        <th>Nama Perangkat</th>
                        <th>Pemilik Perangkat</th>
                        <th>Nomor</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($devices as $device)
                    <tr>
                        <td>{{ $device->name }}</td>
                        <td>{{ $device->user_fullname }}</td>
                        <td>{{ $device->number }}</td>
                        <td>
                            @if($device->status == 'TERPUTUS')
                            <span class="badge badge-danger">{{ $device->status }}</span> @elseif($device->status == 'TERHUBUNG')
                            <span class="badge badge-success">{{ $device->status }}</span> @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center">
                                @if ($device->status == 'TERHUBUNG')
                                <a href="{{ route('devices.disconnect', ['name' => $device->id]) }}" class="btn btn-outline-danger">
                                    <i class="fas fa-unlink"></i>&nbsp;Lepas Tautan
                                </a>
                                <a href="{{ route('devices.chats', ['id' => $device->id]) }}" class="btn btn-outline-info">
                                    <i class="fas fa-comment"></i>
                                </a>
                                @elseif ($device->status == 'TERPUTUS')
                                <a href="{{ route('devices.scan', ['name' => $device->id]) }}" class="btn btn-primary">
                                    <i class="fa fa-qrcode"></i>
                                </a>
                                @if (auth()->user()->id == 1)
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#deviceModal" data-mode="edit" data-device="{{ json_encode($device) }}">
                                    <i class="fa fa-pen"></i>
                                </button>
                                <a href="{{ route('devices.history', ['id' => $device->id]) }}" class="btn btn-outline-info">
                                    <i class="fas fa-history"></i>
                                </a>
                                <form id="deleteForm{{ $device->id }}" action="{{ route('devices.destroy', $device->id) }}" method="POST" style="display: inline;">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-danger delete-device" data-device-name="{{ $device->name }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                                @endif @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <!-- Modal -->
            <div class="modal fade" id="deviceModal" tabindex="-1" role="dialog" aria-labelledby="deviceModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" id="deviceModalLabel">Tambah Perangkat Baru</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="deviceForm" action="{{ route('devices.store') }}" method="POST">
                                @csrf
                                <input type="hidden" id="deviceId" name="id">
                                <div class="form-group">
                                    <label for="name">Nama Perangkat :</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="number">Nomor WhatsApp:</label>
                                    <input type="text" class="form-control" id="number" name="number" required>
                                </div>
                                <button type="submit" class="btn btn-success">Tambah Perangkat</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    


@endsection @push('css')
{{-- Selectize CSS via CDN --}}
<link href="https://cdn.jsdelivr.net/npm/selectize@0.12.6/dist/css/selectize.bootstrap3.css" rel="stylesheet" />

{{-- DataTables CSS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
{{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

{{-- DataTables JS --}}
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
{{-- jQuery Validate Bootstrap Tooltip --}}
<script src="https://cdn.jsdelivr.net/npm/jquery-validation-bootstrap-tooltip@0.10.0/js/jquery-validate.bootstrap-tooltip.min.js"></script>
{{-- Handlebars.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js"></script>
<script>
    $(document).ready(function() {
            $('#deviceTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": false,
                "info": true,
                "autoWidth": false,
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            });

            $('#deviceModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var mode = button.data('mode');
                var modal = $(this);
                var form = modal.find('#deviceForm');

                if (mode === 'edit') {
                    var device = button.data('device');
                    modal.find('.modal-title').text('Edit Perangkat');
                    form.attr('action', '/device/update/' + device.id);
                    form.find('input[name="name"]').val(device.name);
                    form.find('input[name="number"]').val(device.number);
                    form.find('input[name="description"]').val(device.description);
                    form.append('<input type="hidden" name="_method" value="PUT">');
                    form.find('button[type="submit"]').text('Update Perangkat');
                } else {
                    modal.find('.modal-title').text('Tambah Perangkat Baru');
                    form.attr('action', '{{ route('devices.store') }}');
                    form.find('input[name="name"]').val('');
                    form.find('input[name="number"]').val('');
                    form.find('input[name="description"]').val('');
                    form.find('input[name="_method"]').remove();
                    form.find('button[type="submit"]').text('Tambah Perangkat');
                }
            });

            $('.delete-device').click(function() {
                var deviceName = $(this).data('device-name');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda akan menghapus perangkat '" + deviceName + "'. Tindakan ini tidak dapat dibatalkan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var formId = $(this).closest('form').attr('id');
                        $('#' + formId).submit();
                    }
                });
            });

            @if(session('success'))
                Swal.fire({
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            @endif
        });
</script>
@endpush