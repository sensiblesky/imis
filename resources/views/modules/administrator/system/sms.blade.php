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
                                <form action="{{ route('admin.system.sms.update') }}" method="POST">
                                    @csrf

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="gateway_name">Gateway Name</label>
                                                <input type="text" name="gateway_name" id="gateway_name" class="form-control"
                                                    value="{{ old('gateway_name', $settings->gateway_name ?? '') }}" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="api_key">API Key</label>
                                                <input type="text" name="api_key" id="api_key" class="form-control"
                                                    value="{{ old('api_key', $settings->api_key ?? '') }}" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="sender_id">Sender ID</label>
                                                <input type="text" name="sender_id" id="sender_id" class="form-control"
                                                    value="{{ old('sender_id', $settings->sender_id ?? '') }}" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="base_url">Base URL</label>
                                                <input type="url" name="base_url" id="base_url" class="form-control"
                                                    value="{{ old('base_url', $settings->base_url ?? '') }}" required>
                                            </div>
                                        </div>

                                        <div class="col-md-12 text-end">
                                            <button type="submit" class="btn btn-primary waves-effect btn-label waves-light"><i class="bx bx-check-double label-icon"></i> Update SMS Settings</button>
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
