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
                                            <h5>SMTP Settings</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="row">
                                    <form method="POST" action="{{ route('admin.system.smtp.update') }}">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="mailer">Mailer</label>

                                                    <input type="text" name="mailer" id="mailer" class="form-control" value="{{ old('mailer', $settings->mailer ?? '') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="host">Host</label>
                                                    <input type="text" name="host" id="host" class="form-control" value="{{ old('host', $settings->host ?? '') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="port">Port</label>
                                                    <input type="number" name="port" id="port" class="form-control" value="{{ old('port', $settings->port ?? '') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="username">Username</label>
                                                    <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $settings->username ?? '') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="password">Password</label>
                                                    <input type="password" name="password" id="password" class="form-control" value="Hidden for security" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="encryption">Encryption (tls/ssl)</label>
                                                    <input type="text" name="encryption" id="encryption" class="form-control" value="{{ old('encryption', $settings->encryption ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="from_address">From Address</label>
                                                    <input type="email" name="from_address" id="from_address" class="form-control" value="{{ old('from_address', $settings->from_address ?? '') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="from_name">From Name</label>
                                                    <input type="text" name="from_name" id="from_name" class="form-control" value="{{ old('from_name', $settings->from_name ?? '') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-12 text-end">
                                                <button type="submit" class="btn btn-primary waves-effect btn-label waves-light"><i class="bx bx-check-double label-icon"></i> Update SMTP Settings</button>
                                            </div>
                                        </div>
                                    </form>
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


@endpush