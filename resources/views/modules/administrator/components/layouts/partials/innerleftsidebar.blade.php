<div class="card filemanager-sidebar me-md-2">
    <div class="card-body">

        <div class="d-flex flex-column h-100">
            <div class="mb-4">
                <div class="mb-3">
                    <div class="dropdown">
                        <button class="btn btn-danger w-100" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                System Settings
                        </button>
                    </div>
                </div>
                <ul class="list-unstyled categories-list">
                    
                    <li>
                        <a href="{{ route('admin.system') }}" class="text-body d-flex align-items-center">
                            <i class="mdi mdi-wrench font-size-16 text-muted me-2"></i> <span class="me-auto">General Settings</span> 
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.system.smtp') }}" class="text-body d-flex align-items-center">
                            <i class="mdi mdi-email-send-outline font-size-16 text-muted me-2"></i> <span class="me-auto">SMTP Gateway</span> 
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.system.sms') }}" class="text-body d-flex align-items-center">
                            <i class="mdi mdi-message-minus-outline font-size-16 text-muted me-2"></i> <span class="me-auto">SMS Gateway</span> 
                        </a>
                    </li>
                    

                    <li>
                        <a href="{{ route('admin.system.jobs') }}" class="text-body d-flex align-items-center">
                            <i class="mdi mdi-timeline-clock-outline font-size-16 text-muted me-2"></i> <span class="me-auto">System Jobs</span> 
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.system.optimize') }}" class="text-body d-flex align-items-center">
                            <i class="mdi mdi-database-refresh-outline font-size-16 text-muted me-2"></i> <span class="me-auto">System Optimize</span> 
                        </a>
                    </li>

                    


                    
                </ul>


                <div class="mb-3">
                    <div
                     class="dropdown">
                        <button class="btn btn-primary w-100" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Organization Settings
                        </button>
                    </div>
                </div>
                

                <ul class="list-unstyled categories-list">
                    <li>
                        <a href="{{ route('admin.system.campus') }}" class="text-body d-flex align-items-center">
                            <i class="mdi mdi-bank font-size-16 text-muted me-2"></i> <span class="me-auto">Campuses</span> 
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.system.department') }}" class="text-body d-flex align-items-center">
                            <i class="mdi mdi-axis font-size-16 text-muted me-2"></i> <span class="me-auto">Departments</span> 
                        </a>
                    </li>
                </ul>

                
            </div>

            
        </div>

    </div>
</div>