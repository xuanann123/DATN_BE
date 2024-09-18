@extends('admin.layouts.master')

@section('title')
    {{ $title }}
@endsection

@section('style-libs')
    <style>
        .content {
            max-width: 200px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card mt-n4 mx-n4">
                <div class="bg-warning-subtle">
                    <div class="card-body pb-0 px-4">
                        <div class="row mb-3">
                            <div class="col-md">
                                <div class="row align-items-center g-3">
                                    <div class="col-md-auto">
                                        <div class="avatar-md">
                                            <div class="avatar-title bg-white rounded-circle">
                                                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTrY7dJj0QnImGcypj9oBdr9u9joHrxgaKY_g&s" alt="" class="avatar-xs">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div>
                                            <h4 class="fw-bold" id="course-title">Introduction to
                                                Machine Learning</h4>
                                            <div class="hstack gap-3 flex-wrap">
                                                <div><i class="ri-building-line align-bottom me-1"></i>
                                                    <span id="instructor-name">John Doe</span>
                                                </div>
                                                <div class="vr"></div>
                                                <div>Category : <span class="fw-medium" id="course-category">Computer
                                                        Science</span>
                                                </div>
                                                <div class="vr"></div>
                                                <div>Submitted : <span class="fw-medium" id="submitted-date">15 May,
                                                        2023</span></div>
                                                <div class="vr"></div>
                                                <div class="badge rounded-pill bg-warning" id="course-status">Pending</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-auto">
                                        <div class="hstack gap-1 flex-wrap">
                                            <button type="button" class="btn btn-danger">Reject</button>
                                            <button type="button" class="btn btn-success">Approve</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <ul class="nav nav-tabs-custom border-bottom-0" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#course-overview"
                                    role="tab">
                                    Overview
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#course-content" role="tab">
                                    Content
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="tab-content text-muted">
                <div class="tab-pane fade show active" id="course-overview" role="tabpanel">
                    <div class="row">
                        <div class="col-xl-9 col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-muted">
                                        <h6 class="mb-3 fw-semibold text-uppercase">Course Description
                                        </h6>
                                        <p id="course-description">This course provides a comprehensive
                                            introduction to machine learning concepts and techniques.
                                            Students will learn the fundamentals of machine learning,
                                            including supervised and unsupervised learning, feature
                                            engineering, and model evaluation. Through hands-on projects
                                            and real-world case studies, students will gain practical
                                            experience in applying machine learning algorithms to solve
                                            complex problems.</p>

                                        <div class="pt-3 border-top border-top-dashed mt-4">
                                            <h6 class="mb-3 fw-semibold text-uppercase">What You'll
                                                Learn</h6>
                                            <ul class="ps-4 vstack gap-2" id="learning-objectives">
                                                <li>Understand the core principles and algorithms of
                                                    machine learning</li>
                                                <li>Implement and apply popular machine learning
                                                    techniques using Python</li>
                                                <li>Preprocess and prepare data for machine learning
                                                    tasks</li>
                                                <li>Evaluate and fine-tune machine learning models</li>
                                                <li>Develop skills to tackle real-world machine learning
                                                    problems</li>
                                                <li>Gain hands-on experience with popular machine
                                                    learning libraries such as scikit-learn and
                                                    TensorFlow</li>
                                            </ul>
                                        </div>

                                        <div class="pt-3 border-top border-top-dashed mt-4">
                                            <h6 class="mb-3 fw-semibold text-uppercase">Requirements
                                            </h6>
                                            <ul class="ps-4 vstack gap-2" id="course-requirements">
                                                <li>Basic understanding of programming concepts</li>
                                                <li>Familiarity with Python programming language</li>
                                                <li>Basic knowledge of linear algebra and statistics
                                                </li>
                                                <li>A computer with Python 3.7+ installed</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Course Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive table-card">
                                        <table class="table table-borderless align-middle mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="fw-medium">Duration</td>
                                                    <td id="course-duration">8 weeks</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-medium">Lectures</td>
                                                    <td id="lecture-count">24</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-medium">Quizzes</td>
                                                    <td id="quiz-count">8</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-medium">Assignments</td>
                                                    <td id="assignment-count">4</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-medium">Skill Level</td>
                                                    <td id="skill-level">Intermediate</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-medium">Language</td>
                                                    <td id="course-language">English</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-medium">Price</td>
                                                    <td id="course-price">$79.99</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="course-content" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Course Content</h5>
                            <div class="accordion" id="courseContentAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="week1Header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#week1Collapse" aria-expanded="true"
                                            aria-controls="week1Collapse">
                                            Week 1: Introduction to Machine Learning (3 hours)
                                        </button>
                                    </h2>
                                    <div id="week1Collapse" class="accordion-collapse collapse show"
                                        aria-labelledby="week1Header" data-bs-parent="#courseContentAccordion">
                                        <div class="accordion-body">
                                            <ul class="list-unstyled vstack gap-2">
                                                <li><a href="" data-bs-toggle="modal"
                                                        data-bs-target="#viewLesson">1.1 What is Machine
                                                        Learning? (30 minutes, bấm vào đây là preview
                                                        nhé)</a></li>
                                                <li>1.2 Types of Machine Learning (45 minutes)</li>
                                                <li>1.3 Applications of Machine Learning (45 minutes)
                                                </li>
                                                <li>1.4 Setting up the Python Environment (30 minutes)
                                                </li>
                                                <li>1.5 Introduction to NumPy and Pandas (30 minutes)
                                                </li>
                                            </ul>
                                            <p><strong>Learning Materials:</strong> Lecture slides,
                                                Python environment setup guide, NumPy and Pandas cheat
                                                sheets</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="week2Header">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#week2Collapse"
                                            aria-expanded="false" aria-controls="week2Collapse">
                                            Week 2: Data Preprocessing and Exploratory Data Analysis (4
                                            hours)
                                        </button>
                                    </h2>
                                    <div id="week2Collapse" class="accordion-collapse collapse"
                                        aria-labelledby="week2Header" data-bs-parent="#courseContentAccordion">
                                        <div class="accordion-body">
                                            <ul class="list-unstyled vstack gap-2">
                                                <li>2.1 Data Cleaning and Handling Missing Values (60
                                                    minutes)</li>
                                                <li>2.2 Feature Scaling and Normalization (45 minutes)
                                                </li>
                                                <li>2.3 Handling Categorical Data (45 minutes)</li>
                                                <li>2.4 Exploratory Data Analysis Techniques (60
                                                    minutes)</li>
                                                <li>2.5 Data Visualization with Matplotlib and Seaborn
                                                    (30 minutes)</li>
                                            </ul>
                                            <p><strong>Learning Materials:</strong> Lecture slides, Data
                                                preprocessing notebook, EDA case study dataset</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="week3Header">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#week3Collapse"
                                            aria-expanded="false" aria-controls="week3Collapse">
                                            Week 3: Supervised Learning - Regression (4 hours)
                                        </button>
                                    </h2>
                                    <div id="week3Collapse" class="accordion-collapse collapse"
                                        aria-labelledby="week3Header" data-bs-parent="#courseContentAccordion">
                                        <div class="accordion-body">
                                            <ul class="list-unstyled vstack gap-2">
                                                <li>3.1 Introduction to Regression (30 minutes)</li>
                                                <li>3.2 Linear Regression (60 minutes)</li>
                                                <li>3.3 Polynomial Regression (45 minutes)</li>
                                                <li>3.4 Regularization Techniques: Ridge and Lasso (60
                                                    minutes)</li>
                                                <li>3.5 Evaluating Regression Models (45 minutes)</li>
                                            </ul>
                                            <p><strong>Learning Materials:</strong> Lecture slides,
                                                Regression algorithms notebook, Housing price dataset
                                                for practice</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="week4Header">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#week4Collapse"
                                            aria-expanded="false" aria-controls="week4Collapse">
                                            Week 4: Supervised Learning - Classification (4 hours)
                                        </button>
                                    </h2>
                                    <div id="week4Collapse" class="accordion-collapse collapse"
                                        aria-labelledby="week4Header" data-bs-parent="#courseContentAccordion">
                                        <div class="accordion-body">
                                            <ul class="list-unstyled vstack gap-2">
                                                <li>4.1 Introduction to Classification (30 minutes)</li>
                                                <li>4.2 Logistic Regression (60 minutes)</li>
                                                <li>4.3 Decision Trees (45 minutes)</li>
                                                <li>4.4 Support Vector Machines (60 minutes)</li>
                                                <li>4.5 Evaluating Classification Models (45 minutes)
                                                </li>
                                            </ul>
                                            <p><strong>Learning Materials:</strong> Lecture slides,
                                                Classification algorithms notebook, Iris dataset for
                                                practice</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="week5Header">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#week5Collapse"
                                            aria-expanded="false" aria-controls="week5Collapse">
                                            Week 5: Unsupervised Learning (3 hours)
                                        </button>
                                    </h2>
                                    <div id="week5Collapse" class="accordion-collapse collapse"
                                        aria-labelledby="week5Header" data-bs-parent="#courseContentAccordion">
                                        <div class="accordion-body">
                                            <ul class="list-unstyled vstack gap-2">
                                                <li>5.1 Introduction to Unsupervised Learning (30
                                                    minutes)</li>
                                                <li>5.2 K-Means Clustering (60 minutes)</li>
                                                <li>5.3 Hierarchical Clustering (45 minutes)</li>
                                                <li>5.4 Principal Component Analysis (PCA) (45 minutes)
                                                </li>
                                            </ul>
                                            <p><strong>Learning Materials:</strong> Lecture slides,
                                                Unsupervised learning notebook, Customer segmentation
                                                dataset</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="week6Header">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#week6Collapse"
                                            aria-expanded="false" aria-controls="week6Collapse">
                                            Week 6: Ensemble Methods (3 hours)
                                        </button>
                                    </h2>
                                    <div id="week6Collapse" class="accordion-collapse collapse"
                                        aria-labelledby="week6Header" data-bs-parent="#courseContentAccordion">
                                        <div class="accordion-body">
                                            <ul class="list-unstyled vstack gap-2">
                                                <li>6.1 Introduction to Ensemble Methods (30 minutes)
                                                </li>
                                                <li>6.2 Bagging and Random Forests (60 minutes)</li>
                                                <li>6.3 Boosting: AdaBoost and Gradient Boosting (60
                                                    minutes)</li>
                                                <li>6.4 Stacking (30 minutes)</li>
                                            </ul>
                                            <p><strong>Learning Materials:</strong> Lecture slides,
                                                Ensemble methods notebook, Credit risk dataset</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="week7Header">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#week7Collapse"
                                            aria-expanded="false" aria-controls="week7Collapse">
                                            Week 7: Model Evaluation and Hyperparameter Tuning (3 hours)
                                        </button>
                                    </h2>
                                    <div id="week7Collapse" class="accordion-collapse collapse"
                                        aria-labelledby="week7Header" data-bs-parent="#courseContentAccordion">
                                        <div class="accordion-body">
                                            <ul class="list-unstyled vstack gap-2">
                                                <li>7.1 Cross-validation Techniques (45 minutes)</li>
                                                <li>7.2 Bias-Variance Tradeoff (30 minutes)</li>
                                                <li>7.3 Hyperparameter Tuning: Grid Search and Random
                                                    Search (60 minutes)</li>
                                                <li>7.4 Model Selection and Evaluation Metrics (45
                                                    minutes)</li>
                                            </ul>
                                            <p><strong>Learning Materials:</strong> Lecture slides,
                                                Model evaluation notebook, Hyperparameter tuning case
                                                study</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="week8Header">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#week8Collapse"
                                            aria-expanded="false" aria-controls="week8Collapse">
                                            Week 8: Final Project and Course Wrap-up (4 hours)
                                        </button>
                                    </h2>
                                    <div id="week8Collapse" class="accordion-collapse collapse"
                                        aria-labelledby="week8Header" data-bs-parent="#courseContentAccordion">
                                        <div class="accordion-body">
                                            <ul class="list-unstyled vstack gap-2">
                                                <li>8.1 Final Project Overview and Guidelines (30
                                                    minutes)</li>
                                                <li>8.2 Working on the Final Project (180 minutes)</li>
                                                <li>8.3 Course Recap and Future Learning Paths (30
                                                    minutes)</li>
                                            </ul>
                                            <p><strong>Learning Materials:</strong> Final project
                                                dataset, Project rubric, Additional resources for
                                                further learning</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="viewLesson" tabindex="-1" aria-labelledby="viewLesson" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewLesson">Evaluation Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Course:</strong> React Fundamentals</p>
                                        <p><strong>Student:</strong> John Doe</p>
                                        <p><strong>Date:</strong> 02 Jan, 2023</p>
                                        <p><strong>Rating:</strong> <span class="badge bg-success">4.5
                                                <i class="ri-star-fill"></i></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Course Type:</strong> Online</p>
                                        <p><strong>Instructor:</strong> Jane Smith</p>
                                    </div>
                                </div>
                                <hr>
                                <h6>Content:</h6>
                                <p>Great course! The content was well-structured and easy to follow. The
                                    instructor explained complex concepts in a very understandable way.
                                    I particularly enjoyed the practical exercises and real-world
                                    examples. However, I think the course could benefit from more
                                    advanced topics in the later sections. Overall, I'm very satisfied
                                    and feel much more confident in my React skills now.</p>
                                <hr>
                                <iframe width="560" height="315"
                                    src="https://www.youtube.com/embed/TfKOFRpqSME?si=xJ7qlCmqYCRUTpIu"
                                    title="YouTube video player" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script-libs')
    <script src="{{ asset('theme/admin/assets/libs/list.js/list.min.js') }}"></script>
    <script src="{{ asset('theme/admin/assets/libs/list.pagination.js/list.pagination.min.js') }}"></script>

    <!--crypto-orders init-->
    <script src="{{ asset('theme/admin/assets/js/pages/crypto-orders.init.js') }}"></script>
@endsection
