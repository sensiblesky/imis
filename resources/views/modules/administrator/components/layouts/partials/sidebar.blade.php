<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

<div data-simplebar class="h-100">

    <!--- Sidemenu -->
    <div id="sidebar-menu">
        <!-- Left Menu Start -->
        <ul class="metismenu list-unstyled" id="side-menu">
            <li class="menu-title" key="t-menu">Menu</li>

            <li>
                <a href="{{ route('admin.dashboard') }}" class="has-arrow waves-effect">
                    <i class="bx bx-home-circle"></i>
                    <span key="t-dashboards">Dashboards</span>
                </a>
            </li>

            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="mdi mdi-account-multiple-outline"></i>
                    <span key="t-dashboards">USERS</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                    <li><a href="{{ route('admin.users.staff') }}" key="t-full-calendar">STAFF</a></li>
                    <li><a href="{{ route('admin.users.students') }}" key="t-tui-calendar">STUDENTS</a></li>
                </ul>
            </li>

            <li>
                <a href="{{ route('admin.notifications.index') }}" class="waves-effect">
                    <i class="bx bx-notification"></i>
                    <span key="t-dashboards">Notifications</span>
                </a>
            </li>


            <li>
                <a href="{{ route('admin.chat.index') }}" class="waves-effect">
                    <i class="bx bx-chat"></i>
                    <span key="t-chat">Live Chat</span>
                </a>
            </li>

        

            <li class="menu-title" key="t-apps">Base Menu</li>

           
            <li>
                <a href="{{ route('admin.system') }}" class="">
                <i class="mdi mdi-progress-wrench"></i>
                    <span key="t-dashboards">System Setting</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.logs.logfile') }}" class="">
                <i class="mdi mdi-history"></i>
                    <span key="t-dashboards">System Log File</span>
                </a>
            </li>



            <li>
                <a href="{{ route('admin.system.info') }}" class="">
                <i class="mdi mdi-information-outline"></i>
                    <span key="t-dashboards">System Info & Status</span>
                </a>
            </li>


            <li>
                <a href="{{ route('admin.permissions.index') }}" class="">
                <i class="mdi mdi-server-security"></i>
                    <span key="t-dashboards">Permissions</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.system.backup') }}" class="">
                <i class="mdi mdi-cloud-lock-outline"></i>
                    <span key="t-dashboards">Backups</span>
                </a>
            </li>


            

            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="mdi mdi-security"></i>
                    <span key="t-dashboards">Auditing</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                    <li><a href="{{ route('admin.logs.authentication') }}" key="t-full-calendar">Authentication Logs</a></li>
                    <li><a href="{{ route('admin.logs.gen') }}" key="t-tui-calendar">General Logs</a></li>
                </ul>
            </li>


            


            

        </ul>
    </div>
    <!-- Sidebar -->
</div>
</div>
<!-- Left Sidebar End -->