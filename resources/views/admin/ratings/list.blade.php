@extends('admin.layouts.master')

@section('title')
    {{ $title }}
@endsection

@section('style-libs')
    <style>
        .content{
            max-width: 200px;
        }
    </style>
@endsection

@section('content')
                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0">Course Evaluations</h4>
                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Course Evaluations</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end page title -->

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <div class="flex-grow-1">
                                        <h4 class="card-title mb-0">All Evaluations</h4>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="hstack text-nowrap gap-2">
                                            <button class="btn btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#filterModal"><i
                                                    class="ri-filter-2-line me-1 align-bottom"></i> Filters</button>
                                            <button class="btn btn-soft-success">Export</button>
                                            <button type="button" id="dropdownMenuLink1" data-bs-toggle="dropdown"
                                                aria-expanded="false" class="btn btn-soft-info"><i
                                                    class="ri-more-2-fill"></i></button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink1">
                                                <li><a class="dropdown-item" href="#">All</a></li>
                                                <li><a class="dropdown-item" href="#">Last Week</a></li>
                                                <li><a class="dropdown-item" href="#">Last Month</a></li>
                                                <li><a class="dropdown-item" href="#">Last Year</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <div class="search-box">
                                            <input type="text" class="form-control search"
                                                placeholder="Search for evaluations...">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-auto ms-auto">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="text-muted">Sort by: </span>
                                            <select class="form-control mb-0" data-choices data-choices-search-false
                                                id="choices-single-default">
                                                <option value="Date">Date</option>
                                                <option value="Rating">Rating</option>
                                                <option value="Course Name">Course Name</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive table-card mt-3 mb-1">
                                    <table class="table align-middle table-nowrap" id="customerTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="sort" data-sort="date">Date</th>
                                                <th class="sort" data-sort="course_name">Course Name</th>
                                                <th class="sort" data-sort="student_name">Student Name</th>
                                                <th class="sort" data-sort="rating">Rating</th>
                                                <th class="sort" data-sort="content">Content</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all">
                                            <tr>
                                                <td class="date">02 Jan, 2023</td>
                                                <td class="course_name"><a href="">React Fundamentals (Cái này có modal nhé)</a></td>
                                                <td class="student_name">John Doe</td>
                                                <td class="rating"><span class="badge bg-danger">1.5 <i
                                                            class="ri-star-fill"></i></span></td>
                                                <td class="content text-truncate">Great course!
                                                    The content was and easy to follow...</td>
                                                <td>
                                                    <button class="btn btn-sm btn-soft-primary" data-bs-toggle="modal"
                                                        data-bs-target="#viewEvaluationModal1">View Details</button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="date">15 Jan, 2023</td>
                                                <td class="course_name"><a href="">Advanced JavaScript</a></td>
                                                <td class="student_name">Jane Smith</td>
                                                <td class="rating"><span class="badge bg-warning">3.5 <i
                                                            class="ri-star-fill"></i></span></td>
                                                <td class="content text-truncate">Good, but could
                                                    be more in-depth. The course covered many topics...</td>
                                                <td>
                                                    <button class="btn btn-sm btn-soft-primary" data-bs-toggle="modal"
                                                        data-bs-target="#viewEvaluationModal2">View Details</button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="date">28 Jan, 2023</td>
                                                <td class="course_name"><a href="">Python for Beginners</a></td>
                                                <td class="student_name">Mike Johnson</td>
                                                <td class="rating"><span class="badge bg-success">4.8 <i
                                                            class="ri-star-fill"></i></span></td>
                                                <td class="content text-truncate">Excellent
                                                    introduction to Python! As a complete beginner...</td>
                                                <td>
                                                    <button class="btn btn-sm btn-soft-primary" data-bs-toggle="modal"
                                                        data-bs-target="#viewEvaluationModal3">View Details</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="noresult" style="display: none">
                                        <div class="text-center">
                                            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                                colors="primary:#121331,secondary:#08a88a"
                                                style="width:75px;height:75px"></lord-icon>
                                            <h5 class="mt-2">Sorry! No Result Found</h5>
                                            <p class="text-muted mb-0">We've searched more than 150+ evaluations We did not
                                                find any evaluations for you search.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <div class="pagination-wrap hstack gap-2">
                                        <a class="page-item pagination-prev disabled" href="#">
                                            Previous
                                        </a>
                                        <ul class="pagination listjs-pagination mb-0"></ul>
                                        <a class="page-item pagination-next" href="#">
                                            Next
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Modal -->
                <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel"
                    aria-hidden="true">
                    <!-- Filter Modal content remains unchanged -->
                </div>

                <!-- View Evaluation Modal -->
                <div class="modal fade" id="viewEvaluationModal1" tabindex="-1"
                    aria-labelledby="viewEvaluationModalLabel1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewEvaluationModalLabel1">Evaluation Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Course:</strong> React Fundamentals</p>
                                        <p><strong>Student:</strong> John Doe</p>
                                        <p><strong>Date:</strong> 02 Jan, 2023</p>
                                        <p><strong>Rating:</strong> <span class="badge bg-success">4.5 <i
                                                    class="ri-star-fill"></i></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Course Type:</strong> Online</p>
                                        <p><strong>Instructor:</strong> Jane Smith</p>
                                    </div>
                                </div>
                                <hr>
                                <h6>Content:</h6>
                                <p>Great course! The content was well-structured and easy to follow. The instructor
                                    explained complex concepts in a very understandable way. I particularly enjoyed the
                                    practical exercises and real-world examples. However, I think the course could benefit
                                    from more advanced topics in the later sections. Overall, I'm very satisfied and feel
                                    much more confident in my React skills now.</p>
                                <hr>
                                <h6>Ratings Breakdown:</h6>
                                <ul>
                                    <li>Content Quality: 5/5</li>
                                    <li>Instructor Effectiveness: 4/5</li>
                                    <li>Course Materials: 5/5</li>
                                    <li>Practical Application: 4/5</li>
                                </ul>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional View Evaluation Modals for other entries -->
                <div class="modal fade" id="viewEvaluationModal2" tabindex="-1"
                    aria-labelledby="viewEvaluationModalLabel2" aria-hidden="true">
                    <!-- Similar structure as viewEvaluationModal1, but with Jane Smith's details -->
                </div>

                <div class="modal fade" id="viewEvaluationModal3" tabindex="-1"
                    aria-labelledby="viewEvaluationModalLabel3" aria-hidden="true">
                    <!-- Similar structure as viewEvaluationModal1, but with Mike Johnson's details -->
                </div>
            <!-- container-fluid -->
        <!-- End Page-content -->

        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <script>
                            document.write(new Date().getFullYear())
                        </script> © Velzon.
                    </div>
                    <div class="col-sm-6">
                        <div class="text-sm-end d-none d-sm-block">
                            Design & Develop by Themesbrand
                        </div>
                    </div>
                </div>
            </div>
        </footer>
@endsection

@section('script-libs')
    <script src="{{ asset('theme/admin/assets/libs/list.js/list.min.js') }}"></script>
    <script src="{{ asset('theme/admin/assets/libs/list.pagination.js/list.pagination.min.js') }}"></script>

    <!--crypto-orders init-->
    <script src="{{ asset('theme/admin/assets/js/pages/crypto-orders.init.js') }}"></script>
@endsection
