<!-- ========== App Menu ========== -->


 <div class="app-menu navbar-menu">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <!-- Dark Logo-->
                <a href="index.html" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset("theme/admin/assets/images/logo-sm.png")}}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset("theme/admin/assets/images/logo-dark.png")}}" alt="" height="17">
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="index.html" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset("theme/admin/assets/images/logo-sm.png")}}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset("theme/admin/assets/images/logo-light.png")}}" alt="" height="17">
                    </span>
                </a>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>

            <div id="scrollbar">
                <div class="container-fluid">

                    <div id="two-column-menu">
                    </div>
                    <ul class="navbar-nav" id="navbar-nav">

                         <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarDashboards" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Dashboards</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarDashboards">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="dashboard-analytics.html" class="nav-link" data-key="t-analytics"> Analytics </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="dashboard-crm.html" class="nav-link" data-key="t-crm"> CRM </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="index.html" class="nav-link" data-key="t-ecommerce"> Ecommerce </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="dashboard-crypto.html" class="nav-link" data-key="t-crypto"> Crypto </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="dashboard-projects.html" class="nav-link" data-key="t-projects"> Projects </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="dashboard-nft.html" class="nav-link" data-key="t-nft"> NFT</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="dashboard-job.html" class="nav-link" data-key="t-job">Job</a>
                                    </li>
                                </ul>
                            </div>
                        </li> <!-- end Dashboard Menu -->
                        <li class="menu-title"><span data-key="t-menu">Quản lý dữ liệu</span></li>
                        
                       

                        <li class="nav-item">
                            <a href="{{ route('.adminbanners.index') }}" class="nav-link">
                                <i class="ri-image-2-line"></i> <span>Banners</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="{{ route('.admincategories.index') }}" class="nav-link">
                                <i class="ri-pages-line"></i> <span>Danh mục</span>
                            </a>
                        </li> 

                        <li class="nav-item">
                            <a href="{{ route('.adminvouchers.index') }}" class="nav-link">
                                <i class="ri-ticket-2-line"></i> <span>Mã giảm giá</span>
                            </a>
                        </li> 


                        

                        

                
                        <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-components">Quản lý quyền và tài khoản</span></li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarUI" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarUI">
                                <i class="ri-account-circle-line"></i> <span data-key="t-base-ui">Quản lý thành viên</span>
                            </a>
                            <div class="collapse menu-dropdown mega-dropdown-menu" id="sidebarUI">
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
                            <a class="nav-link menu-link" href="#sidebarUI" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarUI">
                                <i class="ri-angularjs-line"></i> <span data-key="t-base-ui">Quản lý phân quyền</span>
                            </a>
                            <div class="collapse menu-dropdown mega-dropdown-menu" id="sidebarUI">
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

                        

                    </ul>
                </div>
                <!-- Sidebar -->
            </div>

            <div class="sidebar-background"></div>
        </div>
