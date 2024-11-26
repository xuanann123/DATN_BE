@extends('admin.layouts.master')

@section('title')
    {{ $title }}
@endsection

@section('style-libs')
    <link rel="stylesheet" href="{{ asset('theme/admin/assets/libs/glightbox/css/glightbox.min.css') }}">
    <style>
        .chat-message {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .user-message .message-content {
            background-color: #d1e7dd;
            padding: 10px;
            border-radius: 8px;
            max-width: 70%;
            margin-left: auto;
        }

        .ai-message .message-content {
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 8px;
            max-width: 70%;
        }

        .error-message .message-content {
            background-color: #f5c6cb;
            padding: 10px;
            border-radius: 8px;
            max-width: 70%;
        }
    </style>
@endsection

@section('content')
    <div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1">
        <div class="chat-leftsidebar">
            <div class="px-4">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h5 class="mb-4">Hỏi đáp</h5>
                    </div>
                </div>
            </div> <!-- .p-4 -->
            <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" data-bs-toggle="tab" href="#chats" role="tab" aria-selected="true">
                        Lịch sử tìm kiếm
                    </a>
                </li>

            </ul>

            <div class="tab-content text-muted">
                <div class="tab-pane active" id="chats" role="tabpanel">
                    <div class="chat-room-list pt-3" data-simplebar="init">
                        <div class="simplebar-wrapper" style="margin: -16px 0px 0px;">
                            <div class="simplebar-height-auto-observer-wrapper">
                                <div class="simplebar-height-auto-observer"></div>
                            </div>
                            <div class="simplebar-mask">
                                <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                    <div class="simplebar-content-wrapper" tabindex="0" role="region"
                                        aria-label="scrollable content" style="height: auto; overflow: hidden;">
                                        <div class="simplebar-content" style="padding: 16px 0px 0px;">


                                            <div class="d-flex align-items-center px-4 mt-1 pt-2 mb-2">
                                                <div class="flex-grow-1">
                                                    <h4 class="mb-0 fs-11 text-muted text-uppercase">Lịch sử câu hỏi</h4>
                                                </div>

                                            </div>

                                            <div class="chat-message-list">

                                                <ul class="list-unstyled chat-list chat-user-list mb-0" id="channelList">
                                                    <li>
                                                        <a href="{{ request()->fullUrlWithQuery(['status' => 'today']) }}"
                                                            class="unread-msg-user">
                                                            <div class="d-flex align-items-center">
                                                                <div
                                                                    class="flex-shrink-0 chat-user-img align-self-center me-2 ms-0">
                                                                    <div class="avatar-xxs">
                                                                        <div
                                                                            class="avatar-title bg-light rounded-circle text-body">
                                                                            #</div>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1 overflow-hidden">
                                                                    <p class="text-truncate mb-0">Hôm nay</p>
                                                                </div>
                                                                <div>
                                                                    <div class="flex-shrink-0 ms-2"><span
                                                                            class="badge bg-dark-subtle text-body rounded p-1">{{ $count['today'] }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ request()->fullUrlWithQuery(['status' => 'yesterday']) }}"
                                                            class="unread-msg-user">
                                                            <div class="d-flex align-items-center">
                                                                <div
                                                                    class="flex-shrink-0 chat-user-img align-self-center me-2 ms-0">
                                                                    <div class="avatar-xxs">
                                                                        <div
                                                                            class="avatar-title bg-light rounded-circle text-body">
                                                                            #</div>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1 overflow-hidden">
                                                                    <p class="text-truncate mb-0">Hôm qua</p>
                                                                </div>
                                                                <div>
                                                                    <div class="flex-shrink-0 ms-2"><span
                                                                            class="badge bg-dark-subtle text-body rounded p-1">{{ $count['yesterday'] }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ request()->fullUrlWithQuery(['status' => 'last-week']) }}"
                                                            class="unread-msg-user">
                                                            <div class="d-flex align-items-center">
                                                                <div
                                                                    class="flex-shrink-0 chat-user-img align-self-center me-2 ms-0">
                                                                    <div class="avatar-xxs">
                                                                        <div
                                                                            class="avatar-title bg-light rounded-circle text-body">
                                                                            #</div>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1 overflow-hidden">
                                                                    <p class="text-truncate mb-0">7 ngày trước đó</p>
                                                                </div>
                                                                <div>
                                                                    <div class="flex-shrink-0 ms-2"><span
                                                                            class="badge bg-dark-subtle text-body rounded p-1">{{ $count['last_week'] }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ request()->fullUrlWithQuery(['status' => 'last-month']) }}"
                                                            class="unread-msg-user">
                                                            <div class="d-flex align-items-center">
                                                                <div
                                                                    class="flex-shrink-0 chat-user-img align-self-center me-2 ms-0">
                                                                    <div class="avatar-xxs">
                                                                        <div
                                                                            class="avatar-title bg-light rounded-circle text-body">
                                                                            #</div>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1 overflow-hidden">
                                                                    <p class="text-truncate mb-0">30 ngày trước đó</p>
                                                                </div>
                                                                <div>
                                                                    <div class="flex-shrink-0 ms-2"><span
                                                                            class="badge bg-dark-subtle text-body rounded p-1">{{ $count['last_month'] }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <!-- End chat-message-list -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="simplebar-placeholder" style="width: 300px; height: 650px;"></div>
                        </div>
                        <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                            <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                        </div>
                        <div class="simplebar-track simplebar-vertical" style="visibility: hidden;">
                            <div class="simplebar-scrollbar"
                                style="height: 0px; transform: translate3d(0px, 0px, 0px); display: none;"></div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="contacts" role="tabpanel">
                    <div class="chat-room-list pt-3" data-simplebar="init">
                        <div class="simplebar-wrapper" style="margin: -16px 0px 0px;">
                            <div class="simplebar-height-auto-observer-wrapper">
                                <div class="simplebar-height-auto-observer"></div>
                            </div>
                            <div class="simplebar-mask">
                                <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                    <div class="simplebar-content-wrapper" tabindex="0" role="region"
                                        aria-label="scrollable content" style="height: auto; overflow: hidden;">
                                        <div class="simplebar-content" style="padding: 16px 0px 0px;">
                                            <div class="sort-contact">
                                                <div class="mt-3">
                                                    <div class="contact-list-title">A </div>
                                                    <ul id="contact-sort-A" class="list-unstyled contact-list">
                                                        <li>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 me-2">
                                                                    <div class="avatar-xxs"> <span
                                                                            class="avatar-title rounded-circle bg-primary fs-10">AC</span>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <p class="text-truncate contactlist-name mb-0">Alice
                                                                        Cruickshank</p>
                                                                </div>
                                                                <div class="flex-shrink-0">
                                                                    <div class="dropdown"> <a href="#"
                                                                            class="text-muted" data-bs-toggle="dropdown"
                                                                            aria-haspopup="true" aria-expanded="false"> <i
                                                                                class="ri-more-2-fill"></i> </a>
                                                                        <div class="dropdown-menu dropdown-menu-end"> <a
                                                                                class="dropdown-item" href="#"><i
                                                                                    class="ri-pencil-line text-muted me-2 align-bottom"></i>Edit</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-forbid-2-line text-muted me-2 align-bottom"></i>Block</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-delete-bin-6-line text-muted me-2 align-bottom"></i>Remove</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="mt-3">
                                                    <div class="contact-list-title">B </div>
                                                    <ul id="contact-sort-B" class="list-unstyled contact-list">
                                                        <li>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 me-2">
                                                                    <div class="avatar-xxs"> <img
                                                                            src="assets/images/users/avatar-4.jpg"
                                                                            class="img-fluid rounded-circle"
                                                                            alt=""> </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <p class="text-truncate contactlist-name mb-0">Barrett
                                                                        Brown</p>
                                                                </div>
                                                                <div class="flex-shrink-0">
                                                                    <div class="dropdown"> <a href="#"
                                                                            class="text-muted" data-bs-toggle="dropdown"
                                                                            aria-haspopup="true" aria-expanded="false"> <i
                                                                                class="ri-more-2-fill"></i> </a>
                                                                        <div class="dropdown-menu dropdown-menu-end"> <a
                                                                                class="dropdown-item" href="#"><i
                                                                                    class="ri-pencil-line text-muted me-2 align-bottom"></i>Edit</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-forbid-2-line text-muted me-2 align-bottom"></i>Block</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-delete-bin-6-line text-muted me-2 align-bottom"></i>Remove</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="mt-3">
                                                    <div class="contact-list-title">C </div>
                                                    <ul id="contact-sort-C" class="list-unstyled contact-list">
                                                        <li>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 me-2">
                                                                    <div class="avatar-xxs"> <span
                                                                            class="avatar-title rounded-circle bg-primary fs-10">CK</span>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <p class="text-truncate contactlist-name mb-0">Chris
                                                                        Kiernan</p>
                                                                </div>
                                                                <div class="flex-shrink-0">
                                                                    <div class="dropdown"> <a href="#"
                                                                            class="text-muted" data-bs-toggle="dropdown"
                                                                            aria-haspopup="true" aria-expanded="false"> <i
                                                                                class="ri-more-2-fill"></i> </a>
                                                                        <div class="dropdown-menu dropdown-menu-end"> <a
                                                                                class="dropdown-item" href="#"><i
                                                                                    class="ri-pencil-line text-muted me-2 align-bottom"></i>Edit</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-forbid-2-line text-muted me-2 align-bottom"></i>Block</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-delete-bin-6-line text-muted me-2 align-bottom"></i>Remove</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 me-2">
                                                                    <div class="avatar-xxs"> <span
                                                                            class="avatar-title rounded-circle bg-primary fs-10">CT</span>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <p class="text-truncate contactlist-name mb-0">Clifford
                                                                        Taylor</p>
                                                                </div>
                                                                <div class="flex-shrink-0">
                                                                    <div class="dropdown"> <a href="#"
                                                                            class="text-muted" data-bs-toggle="dropdown"
                                                                            aria-haspopup="true" aria-expanded="false"> <i
                                                                                class="ri-more-2-fill"></i> </a>
                                                                        <div class="dropdown-menu dropdown-menu-end"> <a
                                                                                class="dropdown-item" href="#"><i
                                                                                    class="ri-pencil-line text-muted me-2 align-bottom"></i>Edit</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-forbid-2-line text-muted me-2 align-bottom"></i>Block</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-delete-bin-6-line text-muted me-2 align-bottom"></i>Remove</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="mt-3">
                                                    <div class="contact-list-title">E </div>
                                                    <ul id="contact-sort-E" class="list-unstyled contact-list">
                                                        <li>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 me-2">
                                                                    <div class="avatar-xxs"> <span
                                                                            class="avatar-title rounded-circle bg-primary fs-10">EE</span>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <p class="text-truncate contactlist-name mb-0">Edith
                                                                        Evans</p>
                                                                </div>
                                                                <div class="flex-shrink-0">
                                                                    <div class="dropdown"> <a href="#"
                                                                            class="text-muted" data-bs-toggle="dropdown"
                                                                            aria-haspopup="true" aria-expanded="false"> <i
                                                                                class="ri-more-2-fill"></i> </a>
                                                                        <div class="dropdown-menu dropdown-menu-end"> <a
                                                                                class="dropdown-item" href="#"><i
                                                                                    class="ri-pencil-line text-muted me-2 align-bottom"></i>Edit</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-forbid-2-line text-muted me-2 align-bottom"></i>Block</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-delete-bin-6-line text-muted me-2 align-bottom"></i>Remove</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="mt-3">
                                                    <div class="contact-list-title">F </div>
                                                    <ul id="contact-sort-F" class="list-unstyled contact-list">
                                                        <li>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 me-2">
                                                                    <div class="avatar-xxs"> <img
                                                                            src="assets/images/users/avatar-3.jpg"
                                                                            class="img-fluid rounded-circle"
                                                                            alt=""> </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <p class="text-truncate contactlist-name mb-0">Frank
                                                                        Thomas</p>
                                                                </div>
                                                                <div class="flex-shrink-0">
                                                                    <div class="dropdown"> <a href="#"
                                                                            class="text-muted" data-bs-toggle="dropdown"
                                                                            aria-haspopup="true" aria-expanded="false"> <i
                                                                                class="ri-more-2-fill"></i> </a>
                                                                        <div class="dropdown-menu dropdown-menu-end"> <a
                                                                                class="dropdown-item" href="#"><i
                                                                                    class="ri-pencil-line text-muted me-2 align-bottom"></i>Edit</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-forbid-2-line text-muted me-2 align-bottom"></i>Block</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-delete-bin-6-line text-muted me-2 align-bottom"></i>Remove</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="mt-3">
                                                    <div class="contact-list-title">G </div>
                                                    <ul id="contact-sort-G" class="list-unstyled contact-list">
                                                        <li>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 me-2">
                                                                    <div class="avatar-xxs"> <span
                                                                            class="avatar-title rounded-circle bg-primary fs-10">GB</span>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <p class="text-truncate contactlist-name mb-0">Gilbert
                                                                        Beer</p>
                                                                </div>
                                                                <div class="flex-shrink-0">
                                                                    <div class="dropdown"> <a href="#"
                                                                            class="text-muted" data-bs-toggle="dropdown"
                                                                            aria-haspopup="true" aria-expanded="false"> <i
                                                                                class="ri-more-2-fill"></i> </a>
                                                                        <div class="dropdown-menu dropdown-menu-end"> <a
                                                                                class="dropdown-item" href="#"><i
                                                                                    class="ri-pencil-line text-muted me-2 align-bottom"></i>Edit</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-forbid-2-line text-muted me-2 align-bottom"></i>Block</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-delete-bin-6-line text-muted me-2 align-bottom"></i>Remove</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="mt-3">
                                                    <div class="contact-list-title">J </div>
                                                    <ul id="contact-sort-J" class="list-unstyled contact-list">
                                                        <li>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 me-2">
                                                                    <div class="avatar-xxs"> <img
                                                                            src="assets/images/users/avatar-4.jpg"
                                                                            class="img-fluid rounded-circle"
                                                                            alt=""> </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <p class="text-truncate contactlist-name mb-0">Janette
                                                                        Caster</p>
                                                                </div>
                                                                <div class="flex-shrink-0">
                                                                    <div class="dropdown"> <a href="#"
                                                                            class="text-muted" data-bs-toggle="dropdown"
                                                                            aria-haspopup="true" aria-expanded="false"> <i
                                                                                class="ri-more-2-fill"></i> </a>
                                                                        <div class="dropdown-menu dropdown-menu-end"> <a
                                                                                class="dropdown-item" href="#"><i
                                                                                    class="ri-pencil-line text-muted me-2 align-bottom"></i>Edit</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-forbid-2-line text-muted me-2 align-bottom"></i>Block</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-delete-bin-6-line text-muted me-2 align-bottom"></i>Remove</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 me-2">
                                                                    <div class="avatar-xxs"> <img
                                                                            src="assets/images/users/avatar-7.jpg"
                                                                            class="img-fluid rounded-circle"
                                                                            alt=""> </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <p class="text-truncate contactlist-name mb-0">Joseph
                                                                        Siegel</p>
                                                                </div>
                                                                <div class="flex-shrink-0">
                                                                    <div class="dropdown"> <a href="#"
                                                                            class="text-muted" data-bs-toggle="dropdown"
                                                                            aria-haspopup="true" aria-expanded="false"> <i
                                                                                class="ri-more-2-fill"></i> </a>
                                                                        <div class="dropdown-menu dropdown-menu-end"> <a
                                                                                class="dropdown-item" href="#"><i
                                                                                    class="ri-pencil-line text-muted me-2 align-bottom"></i>Edit</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-forbid-2-line text-muted me-2 align-bottom"></i>Block</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-delete-bin-6-line text-muted me-2 align-bottom"></i>Remove</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 me-2">
                                                                    <div class="avatar-xxs"> <img
                                                                            src="assets/images/users/avatar-1.jpg"
                                                                            class="img-fluid rounded-circle"
                                                                            alt=""> </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <p class="text-truncate contactlist-name mb-0">Justyn
                                                                        Wisoky</p>
                                                                </div>
                                                                <div class="flex-shrink-0">
                                                                    <div class="dropdown"> <a href="#"
                                                                            class="text-muted" data-bs-toggle="dropdown"
                                                                            aria-haspopup="true" aria-expanded="false"> <i
                                                                                class="ri-more-2-fill"></i> </a>
                                                                        <div class="dropdown-menu dropdown-menu-end"> <a
                                                                                class="dropdown-item" href="#"><i
                                                                                    class="ri-pencil-line text-muted me-2 align-bottom"></i>Edit</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-forbid-2-line text-muted me-2 align-bottom"></i>Block</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-delete-bin-6-line text-muted me-2 align-bottom"></i>Remove</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="mt-3">
                                                    <div class="contact-list-title">K </div>
                                                    <ul id="contact-sort-K" class="list-unstyled contact-list">
                                                        <li>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 me-2">
                                                                    <div class="avatar-xxs"> <img
                                                                            src="assets/images/users/avatar-5.jpg"
                                                                            class="img-fluid rounded-circle"
                                                                            alt=""> </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <p class="text-truncate contactlist-name mb-0">Keaton
                                                                        King</p>
                                                                </div>
                                                                <div class="flex-shrink-0">
                                                                    <div class="dropdown"> <a href="#"
                                                                            class="text-muted" data-bs-toggle="dropdown"
                                                                            aria-haspopup="true" aria-expanded="false"> <i
                                                                                class="ri-more-2-fill"></i> </a>
                                                                        <div class="dropdown-menu dropdown-menu-end"> <a
                                                                                class="dropdown-item" href="#"><i
                                                                                    class="ri-pencil-line text-muted me-2 align-bottom"></i>Edit</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-forbid-2-line text-muted me-2 align-bottom"></i>Block</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-delete-bin-6-line text-muted me-2 align-bottom"></i>Remove</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="mt-3">
                                                    <div class="contact-list-title">L </div>
                                                    <ul id="contact-sort-L" class="list-unstyled contact-list">
                                                        <li>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 me-2">
                                                                    <div class="avatar-xxs"> <img
                                                                            src="assets/images/users/avatar-2.jpg"
                                                                            class="img-fluid rounded-circle"
                                                                            alt=""> </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <p class="text-truncate contactlist-name mb-0">Lisa
                                                                        Parker</p>
                                                                </div>
                                                                <div class="flex-shrink-0">
                                                                    <div class="dropdown"> <a href="#"
                                                                            class="text-muted" data-bs-toggle="dropdown"
                                                                            aria-haspopup="true" aria-expanded="false"> <i
                                                                                class="ri-more-2-fill"></i> </a>
                                                                        <div class="dropdown-menu dropdown-menu-end"> <a
                                                                                class="dropdown-item" href="#"><i
                                                                                    class="ri-pencil-line text-muted me-2 align-bottom"></i>Edit</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-forbid-2-line text-muted me-2 align-bottom"></i>Block</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-delete-bin-6-line text-muted me-2 align-bottom"></i>Remove</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="mt-3">
                                                    <div class="contact-list-title">M </div>
                                                    <ul id="contact-sort-M" class="list-unstyled contact-list">
                                                        <li>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 me-2">
                                                                    <div class="avatar-xxs"> <span
                                                                            class="avatar-title rounded-circle bg-primary fs-10">MM</span>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <p class="text-truncate contactlist-name mb-0">Marian
                                                                        Moen</p>
                                                                </div>
                                                                <div class="flex-shrink-0">
                                                                    <div class="dropdown"> <a href="#"
                                                                            class="text-muted" data-bs-toggle="dropdown"
                                                                            aria-haspopup="true" aria-expanded="false"> <i
                                                                                class="ri-more-2-fill"></i> </a>
                                                                        <div class="dropdown-menu dropdown-menu-end"> <a
                                                                                class="dropdown-item" href="#"><i
                                                                                    class="ri-pencil-line text-muted me-2 align-bottom"></i>Edit</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-forbid-2-line text-muted me-2 align-bottom"></i>Block</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-delete-bin-6-line text-muted me-2 align-bottom"></i>Remove</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="mt-3">
                                                    <div class="contact-list-title">N </div>
                                                    <ul id="contact-sort-N" class="list-unstyled contact-list">
                                                        <li>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 me-2">
                                                                    <div class="avatar-xxs"> <img
                                                                            src="assets/images/users/avatar-6.jpg"
                                                                            class="img-fluid rounded-circle"
                                                                            alt=""> </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <p class="text-truncate contactlist-name mb-0">Nellie
                                                                        Cornett</p>
                                                                </div>
                                                                <div class="flex-shrink-0">
                                                                    <div class="dropdown"> <a href="#"
                                                                            class="text-muted" data-bs-toggle="dropdown"
                                                                            aria-haspopup="true" aria-expanded="false"> <i
                                                                                class="ri-more-2-fill"></i> </a>
                                                                        <div class="dropdown-menu dropdown-menu-end"> <a
                                                                                class="dropdown-item" href="#"><i
                                                                                    class="ri-pencil-line text-muted me-2 align-bottom"></i>Edit</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-forbid-2-line text-muted me-2 align-bottom"></i>Block</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-delete-bin-6-line text-muted me-2 align-bottom"></i>Remove</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="mt-3">
                                                    <div class="contact-list-title">R </div>
                                                    <ul id="contact-sort-R" class="list-unstyled contact-list">
                                                        <li>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 me-2">
                                                                    <div class="avatar-xxs"> <span
                                                                            class="avatar-title rounded-circle bg-primary fs-10">RD</span>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <p class="text-truncate contactlist-name mb-0">Ronald
                                                                        Downey</p>
                                                                </div>
                                                                <div class="flex-shrink-0">
                                                                    <div class="dropdown"> <a href="#"
                                                                            class="text-muted" data-bs-toggle="dropdown"
                                                                            aria-haspopup="true" aria-expanded="false"> <i
                                                                                class="ri-more-2-fill"></i> </a>
                                                                        <div class="dropdown-menu dropdown-menu-end"> <a
                                                                                class="dropdown-item" href="#"><i
                                                                                    class="ri-pencil-line text-muted me-2 align-bottom"></i>Edit</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-forbid-2-line text-muted me-2 align-bottom"></i>Block</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-delete-bin-6-line text-muted me-2 align-bottom"></i>Remove</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="mt-3">
                                                    <div class="contact-list-title">S </div>
                                                    <ul id="contact-sort-S" class="list-unstyled contact-list">
                                                        <li>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 me-2">
                                                                    <div class="avatar-xxs"> <img
                                                                            src="assets/images/users/avatar-5.jpg"
                                                                            class="img-fluid rounded-circle"
                                                                            alt=""> </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <p class="text-truncate contactlist-name mb-0">Sarah
                                                                        Beattie</p>
                                                                </div>
                                                                <div class="flex-shrink-0">
                                                                    <div class="dropdown"> <a href="#"
                                                                            class="text-muted" data-bs-toggle="dropdown"
                                                                            aria-haspopup="true" aria-expanded="false"> <i
                                                                                class="ri-more-2-fill"></i> </a>
                                                                        <div class="dropdown-menu dropdown-menu-end"> <a
                                                                                class="dropdown-item" href="#"><i
                                                                                    class="ri-pencil-line text-muted me-2 align-bottom"></i>Edit</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-forbid-2-line text-muted me-2 align-bottom"></i>Block</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-delete-bin-6-line text-muted me-2 align-bottom"></i>Remove</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="mt-3">
                                                    <div class="contact-list-title">V </div>
                                                    <ul id="contact-sort-V" class="list-unstyled contact-list">
                                                        <li>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 me-2">
                                                                    <div class="avatar-xxs"> <img
                                                                            src="assets/images/users/avatar-10.jpg"
                                                                            class="img-fluid rounded-circle"
                                                                            alt=""> </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <p class="text-truncate contactlist-name mb-0">Victor
                                                                        Beahan</p>
                                                                </div>
                                                                <div class="flex-shrink-0">
                                                                    <div class="dropdown"> <a href="#"
                                                                            class="text-muted" data-bs-toggle="dropdown"
                                                                            aria-haspopup="true" aria-expanded="false"> <i
                                                                                class="ri-more-2-fill"></i> </a>
                                                                        <div class="dropdown-menu dropdown-menu-end"> <a
                                                                                class="dropdown-item" href="#"><i
                                                                                    class="ri-pencil-line text-muted me-2 align-bottom"></i>Edit</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-forbid-2-line text-muted me-2 align-bottom"></i>Block</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-delete-bin-6-line text-muted me-2 align-bottom"></i>Remove</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="mt-3">
                                                    <div class="contact-list-title">W </div>
                                                    <ul id="contact-sort-W" class="list-unstyled contact-list">
                                                        <li>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 me-2">
                                                                    <div class="avatar-xxs"> <img
                                                                            src="assets/images/users/avatar-2.jpg"
                                                                            class="img-fluid rounded-circle"
                                                                            alt=""> </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <p class="text-truncate contactlist-name mb-0">Wayne
                                                                        Runte</p>
                                                                </div>
                                                                <div class="flex-shrink-0">
                                                                    <div class="dropdown"> <a href="#"
                                                                            class="text-muted" data-bs-toggle="dropdown"
                                                                            aria-haspopup="true" aria-expanded="false"> <i
                                                                                class="ri-more-2-fill"></i> </a>
                                                                        <div class="dropdown-menu dropdown-menu-end"> <a
                                                                                class="dropdown-item" href="#"><i
                                                                                    class="ri-pencil-line text-muted me-2 align-bottom"></i>Edit</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-forbid-2-line text-muted me-2 align-bottom"></i>Block</a>
                                                                            <a class="dropdown-item" href="#"><i
                                                                                    class="ri-delete-bin-6-line text-muted me-2 align-bottom"></i>Remove</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="simplebar-placeholder" style="width: 0px; height: 0px;"></div>
                        </div>
                        <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                            <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                        </div>
                        <div class="simplebar-track simplebar-vertical" style="visibility: hidden;">
                            <div class="simplebar-scrollbar" style="height: 0px; display: none;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end tab contact -->
        </div>

        <div class="user-chat w-100 overflow-hidden">
            <div class="chat-content d-lg-flex">
                <!-- start chat conversation section -->
                <div class="w-100 overflow-hidden position-relative">
                    <!-- conversation user -->
                    <div class="position-relative">
                        <div class="position-relative" id="users-chat">
                            <div class="p-3 user-chat-topbar">
                                <div class="row align-items-center">
                                    <div class="col-sm-4 col-8">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 d-block d-lg-none me-3">
                                                <a href="javascript: void(0);" class="user-chat-remove fs-18 p-1"><i
                                                        class="ri-arrow-left-s-line align-bottom"></i></a>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <h5 class="text-truncate mb-0 fs-16"><a
                                                                class="text-reset username" data-bs-toggle="offcanvas"
                                                                href="#userProfileCanvasExample"
                                                                aria-controls="userProfileCanvasExample"><img
                                                                    src="https://dl.memuplay.com/new_market/img/com.smartwidgetlabs.chatgpt.icon.2023-06-18-09-30-08.png"
                                                                    class="rounded-circle avatar-xs" alt=""> Chat
                                                                Openai Hệ
                                                                Thống</a></h5>
                                                        <p class="text-truncate text-muted fs-14 mb-0 userStatus">
                                                            <small id="countMember"></small>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-8 col-4">
                                        <ul class="list-inline user-chat-nav text-end mb-0">
                                            <li class="list-inline-item m-0">

                                                <div class="dropdown">
                                                    <button class="btn btn-ghost-secondary btn-icon" type="button"
                                                        data-bs-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i data-feather="search" class="icon-sm"></i>
                                                    </button>
                                                    <div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-lg">
                                                        <div class="p-2">
                                                            <div class="search-box">
                                                                <input type="text" id="searchInputChatOpenai"
                                                                    class="form-control bg-light border-light"
                                                                    placeholder="Search here...">
                                                                <i class="ri-search-2-line search-icon"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>


                                            <li class="list-inline-item m-0">
                                                <div class="dropdown">
                                                    <button class="btn btn-ghost-secondary btn-icon" type="button"
                                                        data-bs-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i data-feather="more-vertical" class="icon-sm"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.qna.delete.all') }}"
                                                            onclick="return confirm('Bạn muốn xoá toàn bộ câu hỏi?')"><i
                                                                class="ri-delete-bin-5-line align-bottom text-muted me-2"></i>
                                                            Xoá toàn bộ câu hỏi</a>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                            <!-- end chat user head -->
                            <div class="chat-conversation p-3 p-lg-4 " id="chat-conversation" data-simplebar>

                                <ul class="list-unstyled chat-conversation-list" id="messages">
                                    @if ($qnaHistory->count() > 0)
                                        @foreach ($qnaHistory as $message)
                                            <li class="chat-list right">
                                                <div class="conversation-list">
                                                    <div class="user-chat-content">
                                                        <div class="ctext-wrap">
                                                            <div class="ctext-wrap-content" id="5">
                                                                <p class="mb-0 ctext-content">{{ $message->question }}</p>
                                                            </div>
                                                            <div class="dropdown align-self-start message-box-drop"> <a
                                                                    class="dropdown-toggle" href="#" role="button"
                                                                    data-bs-toggle="dropdown" aria-haspopup="true"
                                                                    aria-expanded="false"> <i class="ri-more-2-fill"></i>
                                                                </a>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item copy-message"
                                                                        href="#"><i
                                                                            class=" ri-pencil-fill me-2 text-muted align-bottom"></i>Chỉnh
                                                                        sửa</a>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="conversation-name"><span class="d-none name">Frank
                                                                Thomas</span><small
                                                                class="text-muted time">{{ \Carbon\Carbon::parse($message->created_at)->format('d/m/Y H:i') }}

                                                                am</small> <span class="text-success check-message-icon"><i
                                                                    class="bx bx-check-double"></i></span></div>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="chat-list left" id="1">
                                                <div class="conversation-list">
                                                    <div class="chat-avatar"><img
                                                            src="https://dl.memuplay.com/new_market/img/com.smartwidgetlabs.chatgpt.icon.2023-06-18-09-30-08.png"
                                                            alt=""></div>
                                                    <div class="user-chat-content">
                                                        <div class="ctext-wrap">
                                                            <div class="ctext-wrap-content" id="1">
                                                                <p class="mb-0 ctext-content">{{ $message->answer }}</p>
                                                            </div>
                                                            
                                                        </div>
                                                        <div class="conversation-name"><span class="d-none name">Lisa
                                                                Parker</span>
                                                            <small
                                                                class="text-muted time">{{ \Carbon\Carbon::parse($message->created_at)->format('d/m/Y H:i') }}

                                                                am</small> <span class="text-success check-message-icon"><i
                                                                    class="bx bx-check-double"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                                <!-- end chat-conversation-list -->
                            </div>
                            <div class="alert alert-warning alert-dismissible copyclipboard-alert px-4 fade show "
                                id="copyClipBoard" role="alert">
                                Message copied
                            </div>
                        </div>

                        <div class="chat-input-section p-3 p-lg-4">
                            <form id="chatForm">
                                <div class="row g-0 align-items-center">
                                    <div class="col-auto">
                                        <div class="chat-input-links me-2">
                                            <div class="links-list-item">
                                                <button type="button" class="btn btn-link text-decoration-none emoji-btn"
                                                    id="emoji-btn">
                                                    <i class="bx bx-smile align-middle"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="chat-input-feedback">Please Enter a Message</div>
                                        <input type="text" class="form-control chat-input bg-light border-light"
                                            id="message" name="message" placeholder="Nhập câu hỏi..."
                                            autocomplete="off">
                                    </div>
                                    <div class="col-auto">
                                        <div class="chat-input-links ms-2">
                                            <div class="links-list-item">
                                                <button id="send"
                                                    class="btn btn-success chat-send waves-effect waves-light"
                                                    onclick="sendMessage()">
                                                    <i class="ri-send-plane-2-fill align-bottom"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <input type="hidden" value="{{ auth()->user()->name }}" id="nameUserLogin">
                        <input type="hidden" value="{{ auth()->user()->avatar }}" id="imageUserLogin">

                        <div class="replyCard">
                            <div class="card mb-0">
                                <div class="card-body py-3">
                                    <div class="replymessage-block mb-0 d-flex align-items-start">
                                        <div class="flex-grow-1">
                                            <h5 class="conversation-name"></h5>
                                            <p class="mb-0"></p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <button type="button" id="close_toggle"
                                                class="btn btn-sm btn-link mt-n2 me-n3 fs-18">
                                                <i class="bx bx-x align-middle"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script-libs')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            //Nút gửi
            const sendButton = document.getElementById('send');
            //Câu hỏi đi của người dùng
            const messageInput = document.getElementById('message');
            //Đổ lại lịch sử câu hỏi người dùng
            const messagesList = document.getElementById('messages');
            const nameUser = document.getElementById('nameUserLogin').value;
            const imageUser = document.getElementById('imageUserLogin').value;

            // Khởi tạo bộ đếm cho id duy nhất
            let messageId = 1;

            // Gửi câu hỏi đến server và nhận kết quả
            async function sendMessage() {
                const question = messageInput.value;

                if (!question) {
                    alert("Hãy nhập câu hỏi trước khi gửi!");
                    return;
                }

                // Hiển thị tin nhắn đã gửi
                const userMessageHtml =
                    `<li class="chat-list right">
                        <div class="conversation-list">
                            <div class="user-chat-content">
                                <div class="ctext-wrap">
                            <div class="ctext-wrap-content">
                                <p class="mb-0 ctext-content">${question}</p>
                            </div>
                            <div class="dropdown align-self-start message-box-drop">
                                <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="ri-more-2-fill"></i>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item reply-message" href="#"><i class="ri-reply-line me-2 text-muted align-bottom"></i>Reply</a>
                                    <a class="dropdown-item" href="#"><i class="ri-share-line me-2 text-muted align-bottom"></i>Forward</a>
                                    <a class="dropdown-item copy-message" href="#"><i class=" ri-pencil-fill me-2 text-muted align-bottom"></i>Copy</a>
                                    <a class="dropdown-item" href="#"><i class="ri-bookmark-line me-2 text-muted align-bottom"></i>Bookmark</a>
                                    <a class="dropdown-item delete-item" href="#"><i class="ri-delete-bin-5-line me-2 text-muted align-bottom"></i>Delete</a>
                                </div>
                            </div>
                        </div>
                        <div class="conversation-name">
                            <span class="d-none name">Frank Thomas</span>
                            <small class="text-muted time">{{ \Carbon\Carbon::parse($message->created_at)->format('d/m/Y H:i') }} am</small>
                            <span class="text-success check-message-icon"><i class="bx bx-check-double"></i></span>
                        </div>
                    </div>
                </div>
            </li>`;
                //Thêm câu hỏi ngay khi người dùng hỏi
                messagesList.innerHTML += userMessageHtml;
                //Reset value khi thêm câu hỏi hiển thị ra màn hình 
                messageInput.value = "";

                //Xử lý fetch gửi dữ liệu đi
                try {
                    const response = await fetch('/admin/qna/ask', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify({
                            question
                        })
                    });
                    //Nhận về dữ liệu data => answer
                    const data = await response.json();
                    const answer = data.answer;

                    // Tạo HTML cho câu trả lời với id duy nhất
                    const uniqueId = `ai-answer-${messageId++}`;
                    //Đổ câu trả lời vừa rồi ra 
                    const answerHtml =
                        `<li class="chat-list left">
                    <div class="conversation-list">
                        <div class="chat-avatar">
                            <img src="https://dl.memuplay.com/new_market/img/com.smartwidgetlabs.chatgpt.icon.2023-06-18-09-30-08.png" alt="">
                        </div>
                        <div class="user-chat-content">
                            <div class="ctext-wrap">
                                <div class="ctext-wrap-content">
                                    <p class="mb-0 ctext-content" id="${uniqueId}"></p>
                                </div>
                               
                            </div>
                            <div class="conversation-name">
                                <span class="d-none name">Lisa Parker</span>
                                <small class="text-muted time">{{ $message->created_at }} am</small>
                                <span class="text-success check-message-icon"><i class="bx bx-check-double"></i></span>
                            </div>
                        </div>
                    </div>
                </li>`;
                    //Hiển thị ra
                    messagesList.innerHTML += answerHtml;

                    //Lấy được id thằng đổ ra
                    const answerElement = document.getElementById(uniqueId);
                    // Hiển thị câu trả lời từ từ
                    displayTextGradually(answerElement, answer);

                } catch (error) {
                    console.error('Error:', error);
                    const errorHtml =
                        `<li class="chat-message error-message"><div class="message-content">Đã có lỗi xảy ra.</div></li>`;
                    messagesList.innerHTML += errorHtml;
                }
            }

            // Hiển thị văn bản từ từ trong phần tử HTML
            function displayTextGradually(element, text, speed = 50) {
                let index = 0;
                const interval = setInterval(() => {
                    element.textContent += text[index];
                    index++;
                    if (index >= text.length) {
                        clearInterval(interval);
                    }
                }, speed);
            }
            // Bắt sự kiện khi nhấn nút gửi
            sendButton.addEventListener('click', function(event) {
                event.preventDefault();
                sendMessage();
            });
        });
    </script>
    <script>
        document.getElementById('searchInputChatOpenai').addEventListener('keyup', function() {
            // Lấy giá trị từ input
            const searchQuery = this.value;

            // Gửi yêu cầu AJAX tới server
            if (searchQuery.length > 0) {
                fetch(`/admin/qna/search?query=${encodeURIComponent(searchQuery)}`)
                    .then(response => response.json())
                    .then(data => {
                        // Xử lý kết quả từ server
                        highlightMessages(data);
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                // Xóa highlight nếu ô tìm kiếm trống
                removeHighlights();
            }
        });

        function highlightMessages(data) {
            removeHighlights(); // Xóa highlight cũ trước khi thêm mới
            data.forEach(message => {
                const elements = document.querySelectorAll('.chat-message');
                elements.forEach(element => {
                    if (element.textContent.includes(message.question) || element.textContent.includes(
                            message.answer)) {
                        element.classList.add('active'); // Thêm class active để highlight
                    }
                });
            });
        }
        // Hàm để xóa highlight
        function removeHighlights() {
            const highlightedElements = document.querySelectorAll('.active');
            highlightedElements.forEach(el => el.classList.remove('active'));
        }
    </script>




    <!-- glightbox js -->
    <script src="{{ asset('theme/admin/assets/libs/glightbox/js/glightbox.min.js') }}"></script>

    <!-- fgEmojiPicker js -->
    <script src="{{ asset('theme/admin/assets/libs/fg-emoji-picker/fgEmojiPicker.js') }}"></script>

    <!-- chat init js -->
    <script src="{{ asset('theme/admin/assets/js/pages/chat.init.js') }}"></script>
@endsection
