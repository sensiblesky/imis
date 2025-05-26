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
                                            <h5>Basic Settings</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="row">

                                    <form action="{{ route('admin.settings.basic.update') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="app_name">App Name</label>
                                                    <input type="text" name="app_name" id="app_name" class="form-control" value="{{ old('app_name', $settings->app_name ?? '') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="cc">CC</label>
                                                    <input type="text" name="cc" id="cc" class="form-control" value="{{ old('cc', $settings->cc ?? '') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="developer">Developer</label>
                                                    <input type="text" name="developer" id="developer" class="form-control" value="{{ old('developer', $settings->developer ?? '') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="app_url">App URL</label>
                                                    <input type="url" name="app_url" id="app_url" class="form-control" value="{{ old('app_url', $settings->app_url ?? '') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="app_phone">App Phone</label>
                                                    <input type="text" name="app_phone" id="app_phone" class="form-control" value="{{ old('app_phone', $settings->app_phone ?? '') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="app_email">App Email</label>
                                                    <input type="email" name="app_email" id="app_email" class="form-control" value="{{ old('app_email', $settings->app_email ?? '') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="app_address">App Address</label>
                                                    <input type="text" name="app_address" id="app_address" class="form-control" value="{{ old('app_address', $settings->app_address ?? '') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="logo_dark">Dark Logo (1370x300)</label>
                                                    <input type="file" name="logo_dark" id="logo_dark" class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="logo_icon">Icon Logo</label>
                                                    <input type="file" name="logo_icon" id="logo_icon" class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="logo_light">Light Logo (1370x300)</label>
                                                    <input type="file" name="logo_light" id="logo_light" class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="logo_light">Maintenance Mode</label>
                                                    <p class="text-danger">This does not affect the admin panel, When enabled, the app will be in maintenance mode and users will see a maintenance page, except for the admin panel.</p>
                                                    <div class="d-flex">
                                                        <div class="square-switch">
                                                            <input type="checkbox" id="square-switch1" name="maintenance_mode" 
                                                                switch="none" {{ $maintenance_mode == 1 ? 'checked' : '' }} />
                                                            <label for="square-switch1" data-on-label="On" data-off-label="Off"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            


                                            

                                            <div class="col-md-12 text-end">
                                                <button type="submit" class="btn btn-primary waves-effect btn-label waves-light"><i class="bx bx-check-double label-icon"></i> Save Settings</button>
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
