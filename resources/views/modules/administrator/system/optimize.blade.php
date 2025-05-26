@extends('modules.administrator.components.layouts.app')
@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush 
@section('title', 'Dashboard')

@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">System Settings</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">System</a></li>
                        <li class="breadcrumb-item active">Settings</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="d-xl-flex">
        <div class="w-100">
            <div class="d-md-flex">
                
            @include('modules.administrator.components.layouts.partials.innerleftsidebar')

                <div class="w-100">
                    <div class="card">
                        <div class="card-body">
                            <div>
                                <div class="row mb-3">
                                    <div class="col-xl-3 col-sm-6">
                                        <div class="mt-2">
                                            <h5>Optimize System</h5>
                                        </div>
                                    </div>
                                    <div class="col-xl-9 col-sm-6">
                                        <div class="mt-4 mt-sm-0 float-sm-end d-flex align-items-center">
                                            <button type="button" id="optimizeBtn" class="btn btn-danger waves-effect btn-label waves-light">
                                                <i class="mdi mdi-database-refresh-outline label-icon"></i> Optimize System
                                            </button
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                




                                <div class="row">
                                    <div class="col-md-6">
                                        
                                    </div>
                                </div>


                                </div>
                                <!-- end row -->
                            </div>
                        </div>
                    </div>
                    <!-- end card -->
                </div>
                <!-- end w-100 -->
            </div>
        </div>

        @include('modules.administrator.components.layouts.partials.innerrightsidebar')
    </div>
    <!-- end row -->
@endsection

@push('scripts')
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/sweet-alerts.init.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var options = {
                chart: {
                    height: 250,
                    type: 'radialBar',
                    sparkline: {
                        enabled: true
                    }
                },
                series: [{{ $diskUsagePercentage }}],
                colors: ['#556ee6'],
                plotOptions: {
                    radialBar: {
                        startAngle: -135,
                        endAngle: 135,
                        track: {
                            background: '#e7e7e7',
                            strokeWidth: '97%',
                            margin: 5,
                        },
                        dataLabels: {
                            name: {
                                show: false
                            },
                            value: {
                                offsetY: 5,
                                fontSize: '22px',
                                formatter: function (val) {
                                    return val + "%";
                                }
                            }
                        }
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        shadeIntensity: 0.15,
                        inverseColors: false,
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 50, 65, 91]
                    },
                },
                stroke: {
                    lineCap: 'round'
                },
                labels: ['Used'],
            };

            var chart = new ApexCharts(document.querySelector("#myradial-chart"), options);
            chart.render();
        });
    </script>




<script>
document.getElementById('optimizeBtn').addEventListener('click', function () {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will optimize your system (cache config, routes, views, etc.)",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, optimize it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("{{ route('admin.system.optimize.command') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                Swal.fire({
                    title: 'System Optimized',
                    html: `<pre style="text-align: left;">${data.message}</pre>`,
                    icon: 'success',
                    width: 600
                });
            })
            .catch(error => {
                Swal.fire('Error', 'Optimization failed.', 'error');
                console.error(error);
            });
        }
    });
});
</script>
@endpush