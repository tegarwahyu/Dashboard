@extends('layouts.app')

@section('content')
    <style>
        .chart-area {
            position: relative;
            width: 100% !important;
            height: 100% !important;
        }

        canvas {
            width: 100% !important;
            height: 100% !important;
        }
    </style>

    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-dark">Dashboard</h1>
        </div>

        @if(Auth::user()->role == 'Marketing' || Auth::user()->role == 'Super Admin')
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start-primary shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-uppercase text-primary small mb-1 fw-bold">
                                    Jumlah Outlet
                                </div>
                                <div class="h5 mb-0 fw-bold text-dark">
                                    {{ count($outletIds) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-store fa-2x text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start-success shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-uppercase text-success small mb-1 fw-bold">
                                    Jumlah Event
                                </div>
                                <div class="h5 mb-0 fw-bold text-dark">
                                    {{ count($data_even) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa-solid fa-rectangle-ad fa-2x text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-primary">Grafik Semua Promosi per Bulan</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="promosiChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('promosiChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartData['labels']),
                datasets: [
                    {
                        label: 'Diskon',
                        data: @json($chartData['data']['Diskon']),
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Voucher',
                        data: @json($chartData['data']['Voucher']),
                        borderColor: '#1cc88a',
                        backgroundColor: 'rgba(28, 200, 138, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Bundling Menu',
                        data: @json($chartData['data']['Bundling Menu']),
                        borderColor: '#36b9cc',
                        backgroundColor: 'rgba(54, 185, 204, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Live Event',
                        data: @json($chartData['data']['Live Event']),
                        borderColor: '#f6c23e',
                        backgroundColor: 'rgba(246, 194, 62, 0.1)',
                        tension: 0.4
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        animation: false
                    },
                    legend: {
                        position: 'top',
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0
                    }
                }
            }
        });
    </script>
@endsection
