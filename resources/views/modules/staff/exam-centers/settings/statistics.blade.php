@extends('modules.staff.components.layouts.app')
@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2-bootstrap-5.css') }}" rel="stylesheet" type="text/css" />
@endpush 
@section('title', 'Exam Centers Enrolled Students')

@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Exam Centers Students</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Exam Centers</a></li>
                        <li class="breadcrumb-item active">students</li>
                        <li class="breadcrumb-item active">index</li>
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


    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="d-flex align-items-center">
                <img src="{{ $authUserPhoto }}" alt="" class="avatar-sm rounded">
                <div class="ms-3 flex-grow-1">
                    <h5 class="mb-2 card-title">Hello, {{ $authUserName }}</h5>
                    <p class="text-muted mb-0">
                        Here you can manage your exam centers settings
                    </p>
                </div>
            </div>
        </div><!--end col-->
    </div><!--end row-->


    <div class="d-xl-flex">
        <div class="w-100">
            <div class="d-md-flex">
                <div class="w-100">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-xl-3 col-sm-6">
                                    <div class="mt-2">
                                        <h5>Manage Students on {{ $examCenterName }} Exam Centers</h5>
                                    </div>
                                    <button onclick="downloadPDF()" class="btn btn-primary mt-3">FORMATED PDF</button><br>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
    @php
        $badgeColors = [
            'badge-soft-primary',
            'badge-soft-success',
            'badge-soft-danger',
            'badge-soft-warning',
            'badge-soft-info',
            'badge-soft-dark',
            'badge-soft-secondary',
            'badge-soft-light',
        ];

        $statusColors = [
            'active' => 'badge-soft-success',
            'inactive' => 'badge-soft-danger',
            'pending' => 'badge-soft-warning',
        ];
    @endphp

    <table id="datatable-buttons" class="table table-bordered dt-responsive align-middle nowrap w-100">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Student Name</th>
            <th>Registration Number</th>
            <th>Program</th>
        </tr>
    </thead>
    <tbody>
        @foreach($students as $index => $student)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $student->firstname }} {{ $student->lastname }}</td>
            <td>{{ $student->username }}</td>
            <td>{{ $student->program_code }}</td>
        </tr>
        @endforeach
    </tbody>
</table>


@php
$currentDate = now()->format('Y-m-d');
$studentRows = [];
foreach ($students as $index => $student) {
    $studentRows[] = [
        $index + 1,
        $student->firstname . ' ' . $student->lastname,
        $student->username,
        $student->program_code,
    ];
}
@endphp


<script>
function downloadPDF() {
    const headerImage = pdfImages.header;
    const footerImage = pdfImages.footer;

    const reportDetailsTable = [
        [
            {
                text: 'Report Details',
                style: 'tableHeaderBlue',
                colSpan: 4,
                alignment: 'center',
                fillColor: '#10497E',
                color: 'white',
                bold: true
            }, {}, {}, {}
        ],
        [
            { text: 'Report Type', colSpan: 2, alignment: 'center' }, {}, 
            { text: 'Exam Centers Enrolled Students', colSpan: 2, alignment: 'right' }, {}
        ],
        [
            { text: 'Prepared By', colSpan: 2, alignment: 'center' }, {}, 
            { text: @json($authUserName), colSpan: 2, alignment: 'right' }, {}
        ],
        [
            { text: 'CAMPUS', colSpan: 2, alignment: 'center' }, {}, 
            { text: @json($examCenterName), colSpan: 2, alignment: 'right' }, {}
        ],
        [
            { text: 'Date & Signature', colSpan: 2, alignment: 'center' }, {}, 
            {
                stack: [
                    { text: 'Date: {{ $currentDate }}', alignment: 'left' },
                    { text: 'Signature: ____________', alignment: 'right' }
                ],
                colSpan: 2
            }, {}
        ]
    ];

    const studentHeaders = [
        { text: '#', fillColor: '#10497E', color: 'white', bold: true, alignment: 'center' },
        { text: 'Student Name', fillColor: '#10497E', color: 'white', bold: true, alignment: 'center' },
        { text: 'Registration Number', fillColor: '#10497E', color: 'white', bold: true, alignment: 'center' },
        { text: 'Program', fillColor: '#10497E', color: 'white', bold: true, alignment: 'center' }
    ];

    const studentRows = @json($studentRows);

    const docDefinition = {
        pageSize: 'A4',
        pageMargins: [20, 80, 20, 80],

        header: {
            margin: [0, 0, 0, 0],
            image: headerImage,
            width: 595,
            alignment: 'center',
        },

        footer: {
            margin: [0, 0, 0, 0],
            image: footerImage,
            width: 595,
            alignment: 'center',
        },

        content: [
            {
                table: {
                    headerRows: 1,
                    widths: ['25%', '25%', '25%', '25%'], // Evenly distributed across full width
                    body: reportDetailsTable
                },
                layout: {
                    fillColor: function (rowIndex, node, columnIndex) {
                        return null; // no alternating background
                    },
                    hLineColor: () => 'black',
                    vLineColor: () => 'black',
                    hLineWidth: () => 1,
                    vLineWidth: () => 1,
                    paddingLeft: () => 5,
                    paddingRight: () => 5,
                    paddingTop: () => 5,
                    paddingBottom: () => 5
                },
                margin: [0, 20, 0, 20]
                
            },
            {
                text: 'Students List',
                style: 'header',
                margin: [0, 0, 0, 10]
            },
            {
                table: {
                    headerRows: 1,
                    widths: ['*', '*', '*', '*'],
                    body: [
                        studentHeaders,
                        ...studentRows
                    ]
                },
                layout: {
                    hLineColor: () => 'black',
                    vLineColor: () => 'black',
                    hLineWidth: () => 1,
                    vLineWidth: () => 1,
                    paddingLeft: () => 5,
                    paddingRight: () => 5,
                    paddingTop: () => 5,
                    paddingBottom: () => 5
                }
            }
        ],

        styles: {
            header: {
                fontSize: 18,
                bold: true,
            },
            tableHeaderBlue: {
                fillColor: '#10497E',
                color: 'white',
                bold: true
            }
        }
    };

    pdfMake.createPdf(docDefinition).download('students-list.pdf');
}
</script>




</div>

                                </div>
                            </div>

                            <!-- Static Backdrop Modal -->
                                            
                        </div>
                    </div>
                    <!-- end card -->
                </div>
                <!-- end w-100 -->
            </div>
        </div>
    </div>


    <!-- end row -->
@endsection

@push('scripts')
    <!-- Core DataTables -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Responsive -->
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

    <!-- DataTables Buttons -->
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pdfmake/build/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pdfmake/build/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>

    <!-- Other Scripts -->
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/sweet-alerts.init.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>


    <script src="{{ asset('assets/js/pdf-images.js') }}"></script>
    <script src="{{ asset('assets/js/pdf-generator.js') }}"></script>

@endpush
