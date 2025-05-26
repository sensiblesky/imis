@extends('modules.administrator.components.layouts.app')

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
                                            <h5>Laravel Jobs</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <p style="color: red;">1. nohup php artisan queue:work > storage/logs/queue-worker.log 2>&1 &</p>

                                <div class="mt-2">
                                    <button id="checkJobStatus" class="btn btn-sm btn-primary">Check Status</button>
                                    <span id="jobStatusText" class="ms-2"></span>
                                </div>
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
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
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
    document.getElementById('checkJobStatus').addEventListener('click', function () {
        const statusText = document.getElementById('jobStatusText');
        statusText.innerHTML = 'Checking...';

        fetch('/admin/job-status')
            .then(response => response.json())
            .then(data => {
                if (data.running) {
                    statusText.innerHTML = '<span class="text-success">The service is runing</span>';
                } else {
                    statusText.innerHTML = '<span class="text-danger">Not Running, Please go to your project terminal to run above red command</span>';
                }
                console.log(data.output); // For debugging
            })
            .catch(error => {
                statusText.innerHTML = '<span class="text-warning">Error checking status</span>';
                console.error(error);
            });
    });
</script>



@endpush
