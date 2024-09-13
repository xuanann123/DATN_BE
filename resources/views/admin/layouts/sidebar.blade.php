<!-- ========== App Menu ========== -->


<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="{{ route("admin.dashboard") }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('theme/admin/assets/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('theme/admin/assets/images/logo-dark.png') }}" alt="" height="17">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="{{ route("admin.dashboard") }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('theme/admin/assets/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('theme/admin/assets/images/logo-light.png') }}" alt="" height="17">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">Quản lý thống kê</span></li>
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">
                        <i class="ri-image-2-line"></i> <span>Dashboards</span>
                    </a>
                </li>
                <li class="menu-title"><span data-key="t-menu">Quản lý giao diện</span></li>
         
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarBanners" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarBanners">
                        <i class="ri-image-2-line"></i> <span>Banners</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarBanners">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.banners.index') }}"  class="nav-link"
                                    data-key="t-horizontal">Danh sách</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.banners.create') }}"  class="nav-link"
                                    data-key="t-detached">Thêm mới</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarLayouts" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarLayouts">
                        <i class="ri-layout-3-line"></i> <span data-key="t-layouts">Layouts</span> <span
                            class="badge badge-pill bg-danger" data-key="t-hot">Hot</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarLayouts">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="layouts-horizontal.html"  class="nav-link"
                                    data-key="t-horizontal">Horizontal</a>
                            </li>
                            <li class="nav-item">
                                <a href="layouts-detached.html"  class="nav-link"
                                    data-key="t-detached">Detached</a>
                            </li>
                            <li class="nav-item">
                                <a href="layouts-two-column.html"  class="nav-link"
                                    data-key="t-two-column">Two Column</a>
                            </li>
                            <li class="nav-item">
                                <a href="layouts-vertical-hovered.html"  class="nav-link"
                                    data-key="t-hovered">Hovered</a>
                            </li>
                        </ul>
                    </div>
                </li> <!-- end Dashboard Menu -->

                <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-pages">Quản lý nội dung</span>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarCategories" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarCategories">
                        <i class="bx bx-list-minus"></i> <span>Danh mục</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarCategories">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.categories.index') }}" class="nav-link" data-key="t-one-page">
                                    Danh sách</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.categories.create') }}" class="nav-link" data-key="t-job">
                                    Thêm mới </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarVouchers" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarVouchers">
                        <i class="ri-ticket-2-line"></i> <span>Mã giảm giá</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarVouchers">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.vouchers.index') }}" class="nav-link" data-key="t-one-page">
                                    Danh sách</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.vouchers.create') }}" class="nav-link" data-key="t-job">
                                    Thêm mới </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarCourses" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarCourses">
                        <i class="ri-book-open-line"></i> <span>Khóa học</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarCourses">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.courses.list') }}" class="nav-link" data-key="t-one-page">
                                    Danh sách</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.courses.create') }}" class="nav-link" data-key="t-job">
                                    Thêm mới </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarCourses" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarCourses">
                        <i class="ri-graduation-cap-line"></i> <span>Chứng chỉ</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarCourses">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.courses.list') }}" class="nav-link" data-key="t-one-page">
                                    Danh sách</a>
                            </li>

                        </ul>
                    </div>
                </li>
                <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-components">Quản lý tài
                        khoản</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarStudent" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarStudent">
                        <i class="ri-account-circle-line"></i> <span data-key="t-base-ui">Quản lý học sinh viên</span>
                    </a>
                    <div class="collapse menu-dropdown mega-dropdown-menu" id="sidebarStudent">
                        <div class="row">
                            <div class="col-lg-4">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="ui-alerts.html" class="nav-link" data-key="t-alerts">Thêm mới</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-badges.html" class="nav-link" data-key="t-badges">Danh sách</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarTeacher" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarTeacher">
                        <i class="ri-angularjs-line"></i> <span data-key="t-base-ui">Quản lý giảng viên</span>
                    </a>
                    <div class="collapse menu-dropdown mega-dropdown-menu" id="sidebarTeacher">
                        <div class="row">
                            <div class="col-lg-4">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="ui-alerts.html" class="nav-link" data-key="t-alerts">Thêm mới</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-badges.html" class="nav-link" data-key="t-badges">Danh sách</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarPermission" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarPermission">
                        <i class="ri-angularjs-line"></i> <span data-key="t-base-ui">Quản lý phân quyền</span>
                    </a>
                    <div class="collapse menu-dropdown mega-dropdown-menu" id="sidebarPermission">
                        <div class="row">
                            <div class="col-lg-4">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="ui-alerts.html" class="nav-link" data-key="t-alerts">Thêm mới</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-badges.html" class="nav-link" data-key="t-badges">Danh sách</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </li>
        </div>
        </li>




        </ul>
    </div>
    <!-- Sidebar -->
</div>

<div class="sidebar-background"></div>
</div>
