@extends('modules.staff.components.layouts.app')
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
                            <p class="text-muted mb-0 text-truncate">Academic Staff</p>
                        </div>

                        <div class="col-sm-8">
                            <div class="pt-4">

                                <div class="row">
                                    <div class="col-6">
                                        <h5 class="font-size-15">Workspace</h5>
                                        <p class="text-muted mb-0">academic</p>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="font-size-15">Campus</h5>
                                        
                                        <p class="text-muted mb-0">{{ $user->campus_name }} </p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('staff.profile') }}" class="btn btn-primary waves-effect waves-light btn-sm">View Profile <i class="mdi mdi-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                </div>
            </div>
        </div>

        <div class="col-xl-8">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Students Statistics Based On Academic Year</h4>
                    <div class="page-title-right">
                    
                    <form class="row row-cols-lg-auto g-3 align-items-center" method="GET" action="{{ route('session.staff.academic-year.update') }}">
                        <div class="col-12">
                            <label class="visually-hidden" for="inlineFormSelectPref">Academic Year</label>
                            <select class="form-select" id="inlineFormSelectPref" name="academic_year_uid" required onchange="this.form.submit()">
                                <option value="" disabled {{ session('academic_year_id') ? '' : 'selected' }}>
                                    Choose academic year...
                                </option>
                                @foreach ($academicYears as $year)
                                    <option value="{{ $year->uid }}" {{ session('academic_year_range') == $year->year_range ? 'selected' : '' }}>
                                        {{ $year->year_range }}
                                    </option>

                                @endforeach
                            </select>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
            <div class="card">
                <div class="card-body">
                    <div class="d-sm-flex flex-wrap">
                        <h4 class="card-title mb-4">Statistical Data</h4>
                    </div>
                    


                    <div class="row">
                        <div class="col-md-3">
                            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <a class="nav-link mb-2 active" id="v-pills-home-tab" data-bs-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">Students & Staff By Campus</a>
                            <a class="nav-link mb-2" id="v-pills-profile-tab" data-bs-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">Students By Academic Year</a>
                            <a class="nav-link mb-2" id="v-pills-messages-tab" data-bs-toggle="pill" href="#v-pills-messages" role="tab" aria-controls="v-pills-messages" aria-selected="false">Students By Programs</a>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="tab-content text-muted mt-4 mt-md-0" id="v-pills-tabContent">
                                <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                                    <div id="chart-campus-line" style="max-height: 500px;"></div>
                                </div>
                                <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                    <div id="chart-academic-line" style="max-height: 500px;"></div>
                                </div>
                                <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                    <div id="chart-program-line" style="max-height: 500px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
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
    const staffByCampus = @json($staffAndStudentByCampus);
    const studentsByCampus = @json($studentsByCampus);
    const studentsByAcademicYear = @json($studentsByAcademicYear);

    // Campus-wise line chart
    const campusNames = [...new Set([...Object.keys(staffByCampus), ...Object.keys(studentsByCampus)])];
    const staffData = campusNames.map(name => staffByCampus[name] || 0);
    const studentData = campusNames.map(name => studentsByCampus[name] || 0);

    var optionsCampus = {
        chart: {
            type: 'area',
            height: 400
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
            height: 400
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
        height: 400
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