<!doctype html>
<html lang="en">
    <head>
        @include('layouts.partials.dashboard.head')
    </head>

    <body data-sidebar="dark">
        <!-- Begin page -->
        <div id="layout-wrapper">
            @include('layouts.partials.dashboard.header')
            @include('layouts.partials.dashboard.sidebar')

            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">
                <div class="page-content">
                    <div class="container-fluid">
                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">@yield('title')</h4>
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            @yield('breadcrumb')
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        @yield('content')
                    </div>
                </div>

                @include('layouts.partials.dashboard.footer')
            </div>
            <!-- end main content-->
        </div>
        <!-- END layout-wrapper -->

        <!-- Right Sidebar -->
        <div class="right-bar">
            <div data-simplebar class="h-100">
                @include('layouts.partials.dashboard.right-sidebar')
            </div>
        </div>
        <div class="rightbar-overlay"></div>

        @include('layouts.partials.dashboard.scripts')
        @yield('scripts')
    </body>
</html> 