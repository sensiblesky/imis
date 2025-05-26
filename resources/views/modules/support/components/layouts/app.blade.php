<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Dashboard') | {{ $systemSettings->app_name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('modules.support.components.layouts.partials.head')
    @stack('css')
</head>

<body data-sidebar="dark">
    <div id="layout-wrapper">
        @include('modules.support.components.layouts.partials.header')

        @include('modules.support.components.layouts.partials.sidebar')

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>

            @include('modules.support.components.layouts.partials.footer')
        </div>
    </div>

    @include('modules.support.components.layouts.partials.right-sidebar')
    @include('modules.support.components.layouts.partials.scripts')
    @stack('scripts')
</body>
</html>
