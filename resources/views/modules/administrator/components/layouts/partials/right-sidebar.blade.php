<div class="right-bar">
    <div data-simplebar class="h-100">
        <div class="rightbar-title px-3 py-4">
            <a href="javascript:void(0);" class="right-bar-toggle float-end">
                <i class="mdi mdi-close"></i>
            </a>
            <h5 class="m-0">Settings</h5>
        </div>

        <!-- Settings -->
        <hr class="mt-0" />
        <div class="p-4">
            <h6 class="mb-3">Layout Mode</h6>
            <div class="form-check form-switch mb-3">
                <input type="checkbox" class="form-check-input theme-choice" id="light-mode-switch" checked />
                <label class="form-check-label" for="light-mode-switch">Light Mode</label>
            </div>
            <div class="form-check form-switch mb-3">
                <input type="checkbox" class="form-check-input theme-choice" id="dark-mode-switch" data-bsStyle="{{ asset('assets/css/bootstrap-dark.min.css') }}" data-appStyle="{{ asset('assets/css/app-dark.min.css') }}" />
                <label class="form-check-label" for="dark-mode-switch">Dark Mode</label>
            </div>
        </div>
    </div>
</div>

<div class="rightbar-overlay"></div>
