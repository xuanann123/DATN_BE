<!-- ========== App Menu ========== -->


<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
            <span class="logo-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="41" viewBox="0 0 40 41" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M26.8908 13.2172L20 7.15434L0 24.7514V0.5H40V8.41127C40 14.4741 31.7647 17.5055 26.8908 13.2172ZM13.1092 27.7828L20 33.8457L40 16.2486V40.5H0V32.5888C0 26.5259 8.23529 23.4945 13.1092 27.7828Z"
                        fill="#FFBB54" />
                </svg>
                <span class="ms-2" style="font-size: 30px; color: rgb(255, 255, 255); font-weight: 500">Coursea</span>
            </span>
            <span class="logo-lg">
                <span class="logo-sm">
                    <img src="{{ asset('theme/admin/assets/images/Union_Yellow.png') }}" alt="" height="22">
                </span>
                <span class="logo-lg">
                    <img src="{{ asset('theme/admin/assets/images/logo-white-text.png') }}" alt=""
                        width="180" height="auto">
                </span>
            </span>
        </a>
        <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('theme/admin/assets/images/Union_Yellow.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('theme/admin/assets/images/logo-white-text.png') }}" alt="" width="180"
                    height="auto">
            </span>
            {{-- <span class="logo-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="30px" height="auto" viewBox="0 0 40 41"
                    fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M26.8908 13.2172L20 7.15434L0 24.7514V0.5H40V8.41127C40 14.4741 31.7647 17.5055 26.8908 13.2172ZM13.1092 27.7828L20 33.8457L40 16.2486V40.5H0V32.5888C0 26.5259 8.23529 23.4945 13.1092 27.7828Z"
                        fill="#FFBB54" />
                </svg>
            </span> --}}
            {{-- <span class="logo-lg d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="auto" viewBox="0 0 40 41"
                    fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M26.8908 13.2172L20 7.15434L0 24.7514V0.5H40V8.41127C40 14.4741 31.7647 17.5055 26.8908 13.2172ZM13.1092 27.7828L20 33.8457L40 16.2486V40.5H0V32.5888C0 26.5259 8.23529 23.4945 13.1092 27.7828Z"
                        fill="#04A4F4" />
                </svg>
                <span class="ms-2" style="font-size: 30px; color: rgb(27, 26, 26); font-weight: 500">Coursea</span>
            </span> --}}
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
                        <i class="ri-dashboard-line"></i> <span>Quản trị hệ thống</span>
                    </a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.charts.revenue') }}"
                       data-bs-toggle="" role="button" aria-expanded="false" aria-controls="">
                        <i class="ri-bar-chart-line"></i> <span>Thống kê doanh thu</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.charts.top-courses') }}"
                       data-bs-toggle="" role="button" aria-expanded="false" aria-controls="">
                        <i class="ri-bar-chart-line"></i> <span>Top khóa học</span>
                    </a>
                </li>



                <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-pages">Quản lý nội dung</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarBanners" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarBanners">
                        <i class="ri-image-2-line"></i> <span>Banners</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarBanners">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.banners.index') }}" class="nav-link"
                                    data-key="t-horizontal">Danh sách banners</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.banners.create') }}" class="nav-link"
                                    data-key="t-detached">Thêm mới banner</a>
                            </li>
                        </ul>
                    </div>
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
                                    Danh sách danh mục</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.categories.create') }}" class="nav-link" data-key="t-job">
                                    Thêm mới danh mục</a>
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
                                <a href="{{ route('admin.vouchers.index') }}" class="nav-link"
                                    data-key="t-one-page">
                                    Danh sách mã</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.vouchers.create') }}" class="nav-link" data-key="t-job">
                                    Thêm mới mã</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarCertificate" data-bs-toggle="collapse"
                        role="button" aria-expanded="false" aria-controls="sidebarCertificate">
                        <i class="ri-graduation-cap-line"></i> <span>Chứng chỉ</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarCertificate">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.certificates.index') }}" class="nav-link" data-key="t-one-page">
                                    Danh sách chứng chỉ</a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- Bài viết --}}
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarPost" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarPost">
                        <i class="ri-notification-2-line"></i> <span>Bài viết</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarPost">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.posts.index') }}" class="nav-link" data-key="t-one-page">
                                    Danh sách bài viết</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.posts.create') }}" class="nav-link" data-key="t-one-page">
                                    Thêm mới bài viết</a>
                            </li>
                        </ul>
                    </div>
                </li>




                {{-- Giao dịch --}}
                <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-pages">Quản lý giao dịch</span>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarMoney" data-bs-toggle="collapse"
                       role="button" aria-expanded="false" aria-controls="sidebarCertificate">
                        <i class="ri-money-euro-circle-line"></i> <span>Giao dịch</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarMoney">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.transactions.history-buy-course') }}" class="nav-link" data-key="t-one-page">
                                    Khóa học đã bán</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.transactions.history-deposit') }}" class="nav-link" data-key="t-one-page">
                                    Giao dịch nạp tiền</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.transactions.history-withdraw') }}" class="nav-link" data-key="t-one-page">
                                    Giao dịch rút tiền</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.transactions.withdraw-money') }}" class="nav-link" data-key="t-one-page">
                                    Yêu cầu rút tiền</a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- Kiểm duyệt --}}
                <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-pages">Quản lý kiểm
                        duyệt</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.approval.courses.list') }}"
                        data-bs-toggle="" role="button" aria-expanded="false" aria-controls="">
                        <i class="ri-book-open-line"></i> <span>Kiểm duyệt khóa học </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.approval.courses.course-outstanding') }}"
                       data-bs-toggle="" role="button" aria-expanded="false" aria-controls="">
                        <i class="ri-book-open-line"></i> <span>Khóa học nổi bật</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.approval.teachers.list') }}"
                        data-bs-toggle="" role="button" aria-expanded="false" aria-controls="">
                        <i class=" ri-folder-user-line"></i> <span>Kiểm duyệt giảng viên </span>
                    </a>
                </li>

                <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-components">Quản lý tài
                        khoản</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarStudent" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarStudent">
                        <i class="ri-account-circle-line"></i> <span data-key="t-base-ui">Quản lý thành viên</span>
                    </a>
                    <div class="collapse menu-dropdown mega-dropdown-menu" id="sidebarStudent">
                        <div class="row">
                            <div class="col-lg-4">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.users.list') }}" class="nav-link"
                                            data-key="t-badges">Tất cả người dùng</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.users.list-teachers') }}" class="nav-link"
                                           data-key="t-alerts">Danh sách giảng viên</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.users.list-admin') }}" class="nav-link"
                                           data-key="t-alerts">Danh sách người quản trị</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.users.create') }}" class="nav-link"
                                            data-key="t-alerts">Thêm mới người dùng</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </li>

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
                                        <a href="{{ route('admin.permissions.index') }}" class="nav-link" data-key="t-alerts">Quyền</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.roles.create') }}" class="nav-link" data-key="t-alerts">Thêm vai trò</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route("admin.roles.index") }}" class="nav-link" data-key="t-badges">Danh sách vai trò</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </li>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
