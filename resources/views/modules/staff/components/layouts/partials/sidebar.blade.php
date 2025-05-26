<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

<div data-simplebar class="h-100">

    <!--- Sidemenu -->
    <div id="sidebar-menu">
        <!-- Left Menu Start -->
        <ul class="metismenu list-unstyled" id="side-menu">
            <li class="menu-title" key="t-menu">Menu</li>

            <li>
                <a href="{{ route('staff.dashboard') }}" class="has-arrow waves-effect">
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
                    <li><a href="{{ route('staff.users.students') }}" key="t-tui-calendar">STUDENTS</a></li>
                </ul>
            </li>


            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="mdi mdi-home-city-outline"></i>
                    <span key="t-dashboards">Exam Centers</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                    <li><a href="{{ route('staff.exam.centers.settings') }}" key="t-tui-calendar">Settings</a></li>
                </ul>
                <ul class="sub-menu" aria-expanded="false">
                    <li><a href="{{ route('staff.users.students') }}" key="t-tui-calendar">Enrolled Students</a></li>
                </ul>
            </li>

            <li>
                <a href="{{ route('staff.notifications.index') }}" class="waves-effect">
                    <i class="bx bx-notification"></i>
                    <span key="t-dashboards">Notifications</span>
                </a>
            </li>
        </ul>
    </div>
    <!-- Sidebar -->
</div>
</div>
<!-- Left Sidebar End -->