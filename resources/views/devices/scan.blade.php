@extends('layouts.app') 
@section('content')
<!-- Your HTML goes here -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Center Panel</title>
    <style>
        .panel {
            margin: auto;
            width: 50%;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .panel-heading {
            text-align: center;
        }
        .panel-body {
            padding: 20px;
        }
        .form-group {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .col-sm-6 {
            flex: 1;
        }
        .qr-code-img {
            max-width: 100%;
            height: auto;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-align: center;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .panel-footer {
            text-align: left;
            padding: 10px 20px;
        }
        .instruction-list {
            text-align: left;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function checkDeviceStatus() {
            const deviceName = "{{ $deviceName }}";
            const url = "{{ $api }}";
        
            fetch(`${url}/sessions/${deviceName}/status`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'AUTHENTICATED') {
                        fetch(`/device/${deviceName}/update-status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({status: 'AUTHENTICATED'})
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Status dari /sessions:', data.status);
                            if (data.status === 'AUTHENTICATED') {
                                Swal.fire({
                                title: 'Success',
                                text: 'AUTHENTICATION SUCCESSFULLY',
                                icon: 'success'
                                }).then((result) => {
                                    console.log('Swal closed');
                                    window.location.href = "{{ route('devices.index') }}";
                                });
                            }
                        })
                        .catch(error => console.error('Error:', error));
                    } else if (data.status === 'DISCONNECTED') {
                        Swal.fire({
                            title: 'Failed',
                            text: 'AUTHENTICATION FAILED',                        
                            icon: 'error',
                            confirmButtonText: 'OK'
                            }).then(() => {
                                    window.location.href = "{{ route('devices.index') }}";
                            });
                    }
                })
                .catch(error => console.error('Error:', error));
        }
        
        setInterval(checkDeviceStatus, 5000);
    </script>


</head>

<body>
    <br>
    <div class='panel panel-default'>
        <div class='panel-heading'>
            <br>
            <h1>Scan Device</h1>
        </div>
        <div class='panel-body'>
            <form method='post'>
                <div class='form-group row'>
                    <div class='col-sm-6'>
                        <h4>To use WhatsApp on your computer:</h4>
                        <ol class="instruction-list">
                            <li>Open WhatsApp on your phone</li>
                            <li>
                                Tap <strong>Menu <span class="menu-icon">⋮</span></strong> or <strong>Settings <span class="settings-icon">⚙️</span></strong> and select <strong>Linked Devices</strong>
                            </li>
                            <li>Point your phone to this screen to capture the code</li>
                        </ol>
                    </div>
                    <div class='col-sm-6'>
                        <img src="{{ $result }}" alt="QR Code" class="qr-code-img">
                    </div>
                </div>
            </form>
        </div>
        <div class='panel-footer'>
            <a class='btn btn-primary' href="{{ route('devices.index') }}" value='Back'>BACK</a>
        </div>
    </div>
</body>

</html>


<link href="https://cdn.jsdelivr.net/npm/selectize@0.12.6/dist/css/selectize.bootstrap3.min.css" rel="stylesheet" />

<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet" />

<!-- Bootstrap Datepicker CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet" />

<style>
    .instruction-list {
        list-style-type: decimal;
        margin-left: 20px;
    }

    .menu-icon,
    .settings-icon {
        display: inline-block;
        vertical-align: middle;
        font-size: 16px;
    }

    .qr-code-img {
        height: 300px;
    }
</style>

<!-- ===== JS ===== -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap Datepicker JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<!-- jQuery Validate -->
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- Handlebars JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js"></script>

<!-- Selectize JS -->
<script src="https://cdn.jsdelivr.net/npm/selectize@0.12.6/dist/js/standalone/selectize.min.js"></script>