@extends('modules.administrator.components.layouts.app')
@push('css')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush
@section('title', 'Dashboard')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Dashboard</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->




    <div class="row">
        <div class="col-xl-4">
            <div class="card overflow-hidden">
                <div class="bg-primary-subtle">
                    <div class="row">
                        <div class="col-7">
                            <div class="text-primary p-3">
                                <h5 class="text-primary">Welcome Back !</h5>
                                <p>Admin Dashboard</p>
                            </div>
                        </div>
                        <div class="col-5 align-self-end">
                            <img src="assets/images/profile-img.png" alt="" class="img-fluid">
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="avatar-md profile-user-wid mb-4">
                                <img src="{{ $authUserPhoto }}" alt="" class="img-thumbnail rounded-circle">
                            </div>
                            <h5 class="font-size-15 text-truncate">{{ $authUserName }}</h5>
                            <p class="text-muted mb-0 text-truncate">System administrator</p>
                        </div>

                        <div class="col-sm-8">
                            <div class="pt-4">

                                <div class="row">
                                    <div class="col-6">
                                        <h5 class="font-size-15">Workspace</h5>
                                        <p class="text-muted mb-0">Admin</p>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="font-size-15">Campus</h5>
                                        
                                        <p class="text-muted mb-0">{{ $user->campus_name }} </p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('admin.profile') }}" class="btn btn-primary waves-effect waves-light btn-sm">View Profile <i class="mdi mdi-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">System Usage</h4>
                    <div class="row">
                        <div class="col-sm-6">
                            <p class="text-muted">CPU Usage</p>
                            <h3 id="cpuUsage">0%</h3>
                            <p class="text-muted">
                                <span class="text-success me-2" id="cpuUsageChange">0.00% <i class="mdi mdi-arrow-up"></i></span>
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <div class="mt-4 mt-sm-0">
                                <p class="text-muted">RAM Usage</p>
                                <h3 id="ramUsage">0 MB</h3>
                                <p class="text-muted">
                                    <span class="text-success me-2" id="ramUsageChange">0.00% <i class="mdi mdi-arrow-up"></i></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Add Chart Container Below -->
                    <div class="row">
                        <div class="col-md-6">
                            <div id="cpuGaugeChart" style="height: 200px;"></div>
                            <p class="text-center">CPU Usage</p>
                        </div>
                        <div class="col-md-6">
                            <div id="ramGaugeChart" style="height: 200px;"></div>
                            <p class="text-center">RAM Usage</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Total Users</p>
                                    <h4 class="mb-0">{{ number_format($totalUsers) }}</h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                        <span class="avatar-title">
                                            <i class="mdi mdi-account-group-outline font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body border-top py-3">
                            <a href="{{ route('admin.users.staff') }}" class="btn btn-outline-primary btn-sm">View All Users</a>
                        </div>
                    </div>
                </div><!--end col-->

                <div class="col-lg-4">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Total Students</p>
                                    <h4 class="mb-0">{{ number_format($totalStudents) }}</h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                        <span class="avatar-title">
                                            <i class="bx bxs-graduation  font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body border-top py-3">
                            <a href="{{ route('admin.users.students') }}" class="btn btn-outline-success btn-sm">View Students</a>
                        </div>
                    </div>
                </div><!--end col-->

                <div class="col-lg-4">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Total Staff</p>
                                    <h4 class="mb-0">{{ number_format($totalStaff) }}</h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                        <span class="avatar-title">
                                            <i class="bx bxs-user-detail font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body border-top py-3">
                            <a href="{{ route('admin.users.staff') }}" class="btn btn-outline-info btn-sm">View Staff</a>
                        </div>
                    </div>
                </div><!--end col-->
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="d-sm-flex flex-wrap">
                        <h4 class="card-title mb-4">Statistical Data</h4>
                    </div>
                    


                    <div class="row">
                        <div class="col-md-3">
                            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <a class="nav-link mb-2 active" id="v-pills-home-tab" data-bs-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">Total Students & Staff</a>
                            <a class="nav-link mb-2" id="v-pills-profile-tab" data-bs-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">Students Academic Year</a>
                            <a class="nav-link mb-2" id="v-pills-messages-tab" data-bs-toggle="pill" href="#v-pills-messages" role="tab" aria-controls="v-pills-messages" aria-selected="false">Students Programs</a>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="tab-content text-muted mt-4 mt-md-0" id="v-pills-tabContent">
                                <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                                    <div id="chart-campus-line" style="max-height: 400px;"></div>
                                </div>
                                <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                    <div id="chart-academic-line" style="max-height: 400px;"></div>
                                </div>
                                <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                    <div id="chart-program-line" style="max-height: 400px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xl-4">
            <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Top Campuses by Students Count</h4>
                    @if ($topCampuses->count())
                        <div class="text-center">
                            <div class="mb-4">
                                <i class="bx bx-map-pin text-primary display-4"></i>
                            </div>
                            <h3>{{ $topCampuses[0]->total }}</h3>
                            <p>{{ $topCampuses[0]->name }}</p>
                        </div>

                        <div class="table-responsive mt-4">
                            <table class="table align-middle table-nowrap">
                                <tbody>
                                    @foreach ($topCampuses as $campus)
                                        <tr>
                                            <td style="width: 30%">
                                                <p class="mb-0">{{ $campus->name }}</p>
                                            </td>
                                            <td style="width: 25%">
                                                <h5 class="mb-0">{{ $campus->total }}</h5>
                                            </td>
                                            <td>
                                                @php
                                                    $percentage = intval(($campus->total / $topCampuses[0]->total) * 100);
                                                    $color = $loop->index === 0 ? 'bg-primary' : ($loop->index === 1 ? 'bg-success' : 'bg-warning');
                                                @endphp
                                                <div class="progress bg-transparent progress-sm">
                                                    <div class="progress-bar {{ $color }} rounded" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center">No data available.</p>
                    @endif
                </div>

            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-5">Login Activity</h4>
                    <div style="max-height: 500px; overflow-y: auto;">
                        <ul class="verti-timeline list-unstyled">
                            @forelse ($authLogs as $log)
                                <li class="event-list {{ $loop->first ? 'active' : '' }}">
                                    <div class="event-timeline-dot">
                                        <i class="bx {{ $loop->first ? 'bxs-right-arrow-circle bx-fade-right' : 'bx-right-arrow-circle' }} font-size-18"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        {{ $log->status === 'success' ? 'Login successful' : 'Login failed' }}
                                                @php
                                                    $browserIcons = [
                                                        'Chrome' => 'mdi mdi-google-chrome',
                                                        'Firefox' => 'mdi mdi-firefox',
                                                        'Edge' => 'mdi mdi-microsoft-edge',
                                                        'Opera' => 'mdi mdi-opera',
                                                        'Safari' => 'mdi mdi-apple-safari',
                                                    ];

                                                    $platformIcons = [
                                                        'Windows' => 'mdi mdi-microsoft-windows',
                                                        'Linux' => 'mdi mdi-linux',
                                                        'Android' => 'mdi mdi-android-debug-bridge',
                                                        'macOS' => 'mdi mdi-apple',
                                                        'FreeBSD' => 'mdi mdi-freebsd',
                                                        'Manjaro' => 'mdi mdi-manjaro',
                                                    ];

                                                    $browserIcon = $browserIcons[$log->browser] ?? 'mdi mdi-google-chrome';
                                                    $platformIcon = $platformIcons[$log->platform] ?? 'mdi mdi-laptop';
                                                @endphp

                                                <table style="border-collapse: collapse; width: 100%;">
                                                    <tr style="border: none;">
                                                        <td style="border: none; padding: 4px;">
                                                            <i class="mdi mdi-clock-outline"></i> Time:
                                                        </td>
                                                        <td style="border: none; padding: 4px;">
                                                            <strong>{{ $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('F j, Y g:i A') : 'N/A' }}</strong>
                                                        </td>
                                                    </tr>
                                                    <tr style="border: none;">
                                                        <td style="border: none; padding: 4px;">
                                                            <i class="mdi mdi-ip-network"></i> IP Address:
                                                        </td>
                                                        <td style="border: none; padding: 4px;">
                                                            <strong>{{ $log->ip_address }}</strong>
                                                        </td>
                                                    </tr>
                                                    <tr style="border: none;">
                                                        <td style="border: none; padding: 4px;">
                                                            <i class="{{ $browserIcon }}"></i> Browser:
                                                        </td>
                                                        <td style="border: none; padding: 4px;">
                                                            <strong>{{ $log->browser }}</strong>
                                                        </td>
                                                    </tr>
                                                    <tr style="border: none;">
                                                        <td style="border: none; padding: 4px;">
                                                            <i class="{{ $platformIcon }}"></i> Platform:
                                                        </td>
                                                        <td style="border: none; padding: 4px;">
                                                            <strong>{{ $log->platform }}</strong>
                                                        </td>
                                                    </tr>
                                                    @if ($log->status === 'failed')
                                                        <tr style="border: none;" class="text-danger">
                                                            <td style="border: none; padding: 4px;">
                                                                <i class="mdi mdi-alert-decagram-outline"></i> username:
                                                            </td>
                                                            <td style="border: none; padding: 4px;">
                                                                <strong>{{ $log->username }}</strong>
                                                            </td>
                                                        </tr>
                                                        <tr style="border: none;" class="text-danger">
                                                            <td style="border: none; padding: 4px;">
                                                                <i class="mdi mdi-alert-decagram-outline"></i> password:
                                                            </td>
                                                            <td style="border: none; padding: 4px;">
                                                                <strong>{{ $log->password }}</strong>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    <tr style="border: none;" colspan="2">
                                                        <td style="border: none;">
                                                            <center>
                                                                <a href="{{ route('admin.logs.authentication.view', ['uid' => $log->uid]) }}" class="">
                                                                View More
                                                            </a>
                                                            </center>
                                                        </td>
                                                    </tr>
                                                </table>



                                    </div>

                                </li>
                            @empty
                                <li class="event-list">
                                    <div class="event-timeline-dot">
                                        <i class="bx bx-right-arrow-circle font-size-18"></i>
                                    </div>
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <div>No recent activity found.</div>
                                        </div>
                                    </div>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="text-center mt-4"><a href="{{ route('admin.logs.authentication') }}" class="btn btn-primary waves-effect waves-light btn-sm">View More <i class="mdi mdi-arrow-right ms-1"></i></a></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">API_CALL_ERROR</h4>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/pages/dashboard-job.init.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
    <script>
    // Data passed from PHP
    const staffByCampus = @json($staffByCampus);
    const studentsByCampus = @json($studentsByCampus);
    const studentsByAcademicYear = @json($studentsByAcademicYear);

    // Campus-wise line chart
    const campusNames = [...new Set([...Object.keys(staffByCampus), ...Object.keys(studentsByCampus)])];
    const staffData = campusNames.map(name => staffByCampus[name] || 0);
    const studentData = campusNames.map(name => studentsByCampus[name] || 0);

    var optionsCampus = {
        chart: {
            type: 'area',
            height: 350
        },
        series: [
            {
                name: 'Staff',
                data: staffData
            },
            {
                name: 'Students',
                data: studentData
            }
        ],
        xaxis: {
            categories: campusNames
        },
        title: {
            text: 'Staff vs Students by Campus'
        }
    };

    var chartCampus = new ApexCharts(document.querySelector("#chart-campus-line"), optionsCampus);
    chartCampus.render();

    // Academic year chart
    const academicYears = Object.keys(studentsByAcademicYear);
    const academicData = Object.values(studentsByAcademicYear);

    var optionsAcademic = {
        chart: {
            type: 'area',
            height: 350
        },
        series: [{
            name: 'Students',
            data: academicData
        }],
        xaxis: {
            categories: academicYears
        },
        title: {
            text: 'Students by Academic Year'
        }
    };

    var chartAcademic = new ApexCharts(document.querySelector("#chart-academic-line"), optionsAcademic);
    chartAcademic.render();




    const studentsByProgram = @json($studentsByProgram);
const programCodes = Object.keys(studentsByProgram);
const programData = Object.values(studentsByProgram);

var optionsProgram = {
    chart: {
        type: 'bar',
        height: 350
    },
    series: [{
        name: 'Students',
        data: programData
    }],
    xaxis: {
        categories: programCodes
    },
    title: {
        text: 'Students by Program Code'
    }
};

var chartProgram = new ApexCharts(document.querySelector("#chart-program-line"), optionsProgram);
chartProgram.render();

</script>
    <script>
        const cpuChart = echarts.init(document.getElementById("cpuGaugeChart"));
        const ramChart = echarts.init(document.getElementById("ramGaugeChart"));

        const createGaugeOption = (formatterText, colors) => ({
            series: [{
                type: 'gauge',
                radius: '80%', // Adjust the radius for smaller gauge
                axisLine: {
                    lineStyle: {
                        width: 15, // Reduce line width for a smaller chart
                        color: colors
                    }
                },
                pointer: {
                    itemStyle: {
                        color: 'auto'
                    }
                },
                axisTick: {
                    distance: -5,
                    length: 6,
                    lineStyle: {
                        color: '#fff',
                        width: 1
                    }
                },
                splitLine: {
                    distance: -15,
                    length: 20,
                    lineStyle: {
                        color: '#fff',
                        width: 2
                    }
                },
                axisLabel: {
                    color: 'inherit',
                    distance: 20,
                    fontSize: 10
                },
                detail: {
                    valueAnimation: true,
                    formatter: formatterText,
                    color: 'inherit',
                    fontSize: 12
                },
                data: [{ value: 0 }]
            }]
        });

        const cpuColors = [
            [0.3, '#67e0e3'],
            [0.7, '#37a2da'],
            [1, '#fd666d']
        ];

        const ramColors = [
            [0.3, '#f1b44c'],
            [0.7, '#556ee6'],
            [1, '#f46a6a']
        ];

        const cpuOption = createGaugeOption('{value}%', cpuColors);
        const ramOption = createGaugeOption('{value} MB', ramColors);

        cpuChart.setOption(cpuOption);
        ramChart.setOption(ramOption);

        let previousCpuUsage = 0;
        let previousRamUsage = 0;

        function fetchUsageData() {
            fetch('/api/system/usage')
                .then(res => res.json())
                .then(data => {
                    const cpuUsage = parseFloat(data.total_cpu_percent ?? 0).toFixed(1);
                    const ramUsage = parseFloat(data.total_ram_percent ?? 0).toFixed(1);
                    const totalRam = data.total_memory_mb ?? 0;

                    // Update the CPU and RAM usage on the page
                    document.getElementById("cpuUsage").innerText = `${cpuUsage}%`;
                    document.getElementById("ramUsage").innerText = `${ramUsage}% / ${totalRam} MB`;

                    // Calculate the change from the previous period
                    const cpuUsageChange = ((cpuUsage - previousCpuUsage) / previousCpuUsage) * 100;
                    const ramUsageChange = ((ramUsage - previousRamUsage) / previousRamUsage) * 100;

                    // Update the change in percentage
                    document.getElementById("cpuUsageChange").innerText = `${cpuUsageChange.toFixed(2)}%`;
                    document.getElementById("ramUsageChange").innerText = `${ramUsageChange.toFixed(2)}%`;

                    // Update the gauge charts
                    cpuChart.setOption({
                        series: [{
                            data: [{ value: cpuUsage }]
                        }]
                    });

                    ramChart.setOption({
                        series: [{
                            data: [{ value: ramUsage }]
                        }]
                    });

                    // Store the current usage for the next comparison
                    previousCpuUsage = cpuUsage;
                    previousRamUsage = ramUsage;
                })
                .catch(err => {
                    console.error('Error fetching system usage:', err);
                });
        }

        // Initial data fetch
        fetchUsageData();
        
        // Run every 2 seconds
        setInterval(fetchUsageData, 2000);
    </script>
@endpush