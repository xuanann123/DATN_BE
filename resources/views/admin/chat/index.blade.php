@extends('admin.layouts.master')

@section('title')
    {{ $title }}
@endsection

@section('style-libs')
    <link rel="stylesheet" href="{{ asset('theme/admin/assets/libs/glightbox/css/glightbox.min.css') }}">
@endsection

@section('content')
    <div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1">
        <div class="chat-leftsidebar">
            <div class="px-4 pt-4 mb-3">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h5 class="mb-4"> Những cuộc trò chuyện</h5>
                    </div>
                    <div class="flex-shrink-0">
                        <div data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="bottom" title="Add Contact">

                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-soft-success btn-sm">
                                <i class="ri-add-line align-bottom"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="search-box">
                    <input type="text" class="form-control bg-light border-light" placeholder="Search here...">
                    <i class="ri-search-2-line search-icon"></i>
                </div>
            </div> <!-- .p-4 -->

            <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#chats" role="tab">
                        Trò chuyện
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#contacts" role="tab">
                        Liên hệ
                    </a>
                </li>
            </ul>

            <div class="tab-content text-muted">
                <div class="tab-pane active" id="chats" role="tabpanel">
                    <div class="chat-room-list pt-3" data-simplebar>
                        <div class="d-flex align-items-center px-4 mb-2">
                            <div class="flex-grow-1">
                                <h4 class="mb-0 fs-11 text-muted text-uppercase">Danh sách thành viên hoạt động</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <div data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="bottom"
                                    title="New Message">
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-soft-success btn-sm shadow-none">
                                        <i class="ri-add-line align-bottom"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="chat-message-list">

                            <ul class="list-unstyled chat-list chat-user-list" id="userList">

                            </ul>
                        </div>

                        <div class="d-flex align-items-center px-4 mt-4 pt-2 mb-2">
                            <div class="flex-grow-1">
                                <h4 class="mb-0 fs-11 text-muted text-uppercase">Nhóm</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <div data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="bottom"
                                    title="Create group">
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-soft-success btn-sm">
                                        <i class="ri-add-line align-bottom"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="chat-message-list">

                            <ul class="list-unstyled chat-list chat-user-list mb-0" id="channelList">
                                <li class="active">
                                    <a href="javascript: void(0);">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 chat-user-img online align-self-center me-2 ms-0">
                                                <div class="avatar-xxs"> <img
                                                        src="{{ asset('theme/admin/assets/images/users/multi-user.jpg') }}"
                                                        class="rounded-circle img-fluid userprofile" alt=""><span
                                                        class="user-status"></span> </div>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <p class="text-truncate mb-0">Kênh Chat Chung</p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- End chat-message-list -->
                    </div>
                </div>
                <div class="tab-pane" id="contacts" role="tabpanel">
                    <div class="chat-room-list pt-3" data-simplebar>
                        <div class="sort-contact">
                        </div>
                    </div>
                </div>
            </div>
            <!-- end tab contact -->
        </div>
        <!-- end chat leftsidebar -->
        <!-- Start User chat -->
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
                                                    <div
                                                        class="flex-shrink-0 chat-user-img online user-own-img align-self-center me-3 ms-0">
                                                        <img src="{{ asset('theme/admin/assets/images/users/multi-user.jpg') }}"
                                                            class="rounded-circle avatar-xs" alt="">
                                                        <span class="user-status"></span>
                                                    </div>
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <h5 class="text-truncate mb-0 fs-16"><a
                                                                class="text-reset username" data-bs-toggle="offcanvas"
                                                                href="#userProfileCanvasExample"
                                                                aria-controls="userProfileCanvasExample">#Kênh Chat Hệ
                                                                Thống</a></h5>
                                                        <p class="text-truncate text-muted fs-14 mb-0 userStatus">
                                                            <small>Bao nhiêu thành viên đang online ?</small>
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
                                                                <input type="text"
                                                                    class="form-control bg-light border-light"
                                                                    placeholder="Search here..."
                                                                    onkeyup="searchMessages()" id="searchMessage">
                                                                <i class="ri-search-2-line search-icon"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>

                                            <li class="list-inline-item d-none d-lg-inline-block m-0">
                                                <button type="button" class="btn btn-ghost-secondary btn-icon"
                                                    data-bs-toggle="offcanvas" data-bs-target="#userProfileCanvasExample"
                                                    aria-controls="userProfileCanvasExample">
                                                    <i data-feather="info" class="icon-sm"></i>
                                                </button>
                                            </li>

                                            <li class="list-inline-item m-0">
                                                <div class="dropdown">
                                                    <button class="btn btn-ghost-secondary btn-icon" type="button"
                                                        data-bs-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i data-feather="more-vertical" class="icon-sm"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item d-block d-lg-none user-profile-show"
                                                            href="#"><i
                                                                class="ri-user-2-fill align-bottom text-muted me-2"></i>
                                                            View Profile</a>
                                                        <a class="dropdown-item" href="#"><i
                                                                class="ri-inbox-archive-line align-bottom text-muted me-2"></i>
                                                            Archive</a>
                                                        <a class="dropdown-item" href="#"><i
                                                                class="ri-mic-off-line align-bottom text-muted me-2"></i>
                                                            Muted</a>
                                                        <a class="dropdown-item" href="#"><i
                                                                class="ri-delete-bin-5-line align-bottom text-muted me-2"></i>
                                                            Delete</a>
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

                                </ul>
                                <!-- end chat-conversation-list -->
                            </div>
                            <div class="alert alert-warning alert-dismissible copyclipboard-alert px-4 fade show "
                                id="copyClipBoard" role="alert">
                                Message copied
                            </div>
                        </div>
                        <div id="currentUser" data-user-id="{{ auth()->user()->id }}"></div>
                        <div class="position-relative" id="channel-chat">
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
                                                    <div
                                                        class="flex-shrink-0 chat-user-img online user-own-img align-self-center me-3 ms-0">
                                                        <img src="assets/images/users/avatar-2.jpg"
                                                            class="rounded-circle avatar-xs" alt="">
                                                    </div>
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <h5 class="text-truncate mb-0 fs-16"><a
                                                                class="text-reset username" data-bs-toggle="offcanvas"
                                                                href="#userProfileCanvasExample"
                                                                aria-controls="userProfileCanvasExample">Lisa
                                                                Parker</a></h5>
                                                        <p class="text-truncate text-muted fs-14 mb-0 userStatus">
                                                            <small>24 Members</small>
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
                                                                <input type="text"
                                                                    class="form-control bg-light border-light"
                                                                    placeholder="Search here..."
                                                                    onkeyup="searchMessages()" id="searchMessage">
                                                                <i class="ri-search-2-line search-icon"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>

                                            <li class="list-inline-item d-none d-lg-inline-block m-0">
                                                <button type="button" class="btn btn-ghost-secondary btn-icon"
                                                    data-bs-toggle="offcanvas" data-bs-target="#userProfileCanvasExample"
                                                    aria-controls="userProfileCanvasExample">
                                                    <i data-feather="info" class="icon-sm"></i>
                                                </button>
                                            </li>

                                            <li class="list-inline-item m-0">
                                                <div class="dropdown">
                                                    <button class="btn btn-ghost-secondary btn-icon" type="button"
                                                        data-bs-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i data-feather="more-vertical" class="icon-sm"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item d-block d-lg-none user-profile-show"
                                                            href="#"><i
                                                                class="ri-user-2-fill align-bottom text-muted me-2"></i>
                                                            View Profile</a>
                                                        <a class="dropdown-item" href="#"><i
                                                                class="ri-inbox-archive-line align-bottom text-muted me-2"></i>
                                                            Archive</a>
                                                        <a class="dropdown-item" href="#"><i
                                                                class="ri-mic-off-line align-bottom text-muted me-2"></i>
                                                            Muted</a>
                                                        <a class="dropdown-item" href="#"><i
                                                                class="ri-delete-bin-5-line align-bottom text-muted me-2"></i>
                                                            Delete</a>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                            <!-- end chat user head -->
                            <div class="chat-conversation p-3 p-lg-4" id="chat-conversation" data-simplebar>
                                <ul class="list-unstyled chat-conversation-list" id="channel-conversation">


                                </ul>
                                <!-- end chat-conversation-list -->

                            </div>
                            <div class="alert alert-warning alert-dismissible copyclipboard-alert px-4 fade show "
                                id="copyClipBoardChannel" role="alert">
                                Message copied
                            </div>
                        </div>

                        <!-- end chat-conversation -->

                        <div class="chat-input-section p-3 p-lg-4">

                            <form>
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
                                        <div class="chat-input-feedback">
                                            Please Enter a Message
                                        </div>
                                        <input type="text" class="form-control chat-input bg-light border-light"
                                            id="message" name="message" placeholder="Nhập tin nhắn..."
                                            autocomplete="off">
                                    </div>
                                    <div class="col-auto">
                                        <div class="chat-input-links ms-2">
                                            <div class="links-list-item">
                                                <button id="send"
                                                    class="btn btn-success chat-send waves-effect waves-light">
                                                    <i class="ri-send-plane-2-fill align-bottom"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

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
    <!-- end chat-wrapper -->
    {{-- <div class="col-md-12">
        <div class="card">
            <div class="card-header">CHAT</div>
            <div class="card-body">
                <div class="row p-2">
                    <div class="col-10">
                        <div class="row">
                            <div class="col-12 border rounded-lg p-3">
                                <ul class="list-unstyled overflow-auto" id="messages" style="min-height: 45vh">
                                </ul>
                            </div>
                        </div>
                        <form>
                            <div class="row py-3">
                                <div class="col-10">
                                    <input type="text" id="message" name="message" class="form-control">
                                </div>
                                <div class="col-2">
                                    <button id="send" class="btn btn-primary w-100">Gửi</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-2">
                        <p><strong>Người dùng online</strong></p>
                        <ul id="users" class="list-unstyled overflow-auto text-info" style="min-height: 45vh">

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
@endsection

@section('script-libs')
    <script type="module">
        const messagesElement = document.getElementById('messages');
        const userElement = document.getElementById('userList');

        //Dùng presense
        Echo.join('chat')
            //Hiển thị danh sách người dùng
            .here((users) => {
                users.forEach((user) => {
                    // Tạo các phần tử HTML tương ứng

                    // Tạo thẻ li
                    const li = document.createElement('li');

                    // Tạo thẻ div chính cho bố cục
                    const mainDiv = document.createElement('div');
                    mainDiv.classList.add('d-flex', 'align-items-center');

                    // Phần div cho ảnh đại diện
                    const avatarDiv = document.createElement('div');
                    avatarDiv.classList.add('flex-shrink-0', 'chat-user-img', 'online', 'align-self-center',
                        'me-2',
                        'ms-0');

                    const avatarInnerDiv = document.createElement('div');
                    avatarInnerDiv.classList.add('avatar-xxs');

                    // Ảnh đại diện
                    const img = document.createElement('img');
                    // Ảnh đại diện


                    if (user.avatar == null) {
                        img.src ='https://png.pngtree.com/png-clipart/20210608/ourlarge/pngtree-dark-gray-simple-avatar-png-image_3418404.jpg';
                        img.alt = user.name;
                        img.classList.add('rounded-circle', 'img-fluid', 'userprofile');
                    } else {
                        img.src = '{{ asset('storage') }}' + '/' + user.avatar; // Đường dẫn avatar từ API
                        img.classList.add('rounded-circle', 'img-fluid', 'userprofile');
                        img.alt = user.name;
                    }

                    // Trạng thái người dùng
                    const userStatus = document.createElement('span');
                    userStatus.classList.add('user-status');

                    // Gắn ảnh và trạng thái vào div avatar
                    avatarInnerDiv.appendChild(img);
                    avatarInnerDiv.appendChild(userStatus);
                    avatarDiv.appendChild(avatarInnerDiv);

                    // Tạo div chứa tên người dùng
                    const nameDiv = document.createElement('div');
                    nameDiv.classList.add('flex-grow-1', 'overflow-hidden');

                    const userName = document.createElement('p');
                    userName.classList.add('text-truncate', 'mb-0');
                    userName.innerText = user.name; // Gán tên người dùng

                    // Gắn tên vào div
                    nameDiv.appendChild(userName);

                    // Gắn avatarDiv và nameDiv vào mainDiv
                    mainDiv.appendChild(avatarDiv);
                    mainDiv.appendChild(nameDiv);


                    //Tạo thẻ a
                    const a = document.createElement('a');
                    a.appendChild(mainDiv);
                    //Gắmh link với người dùng
                    li.setAttribute('id', user.id);

                    // Gắn mainDiv vào li
                    li.appendChild(a);

                    // Thêm li vào danh sách
                    userElement.appendChild(li);
                })
            })
            .joining((user) => {
                const li = document.createElement('li');

                // Tạo thẻ div chính cho bố cục
                const mainDiv = document.createElement('div');
                mainDiv.classList.add('d-flex', 'align-items-center');

                // Phần div cho ảnh đại diện
                const avatarDiv = document.createElement('div');
                avatarDiv.classList.add('flex-shrink-0', 'chat-user-img', 'online', 'align-self-center',
                    'me-2',
                    'ms-0');

                const avatarInnerDiv = document.createElement('div');
                avatarInnerDiv.classList.add('avatar-xxs');

                // Ảnh đại diện
                const img = document.createElement('img');

                if (user.avatar == null) {
                    img.src =
                        'https://png.pngtree.com/png-clipart/20210608/ourlarge/pngtree-dark-gray-simple-avatar-png-image_3418404.jpg';
                    img.alt = user.name;

                    img.classList.add('rounded-circle', 'img-fluid', 'userprofile');

                } else {
                    img.src = '{{ asset('storage') }}' + '/' + user.avatar; // Đường dẫn avatar từ API
                    img.classList.add('rounded-circle', 'img-fluid', 'userprofile');
                    img.alt = user.name;
                }


                // Trạng thái người dùng
                const userStatus = document.createElement('span');
                userStatus.classList.add('user-status');

                // Gắn ảnh và trạng thái vào div avatar
                avatarInnerDiv.appendChild(img);
                avatarInnerDiv.appendChild(userStatus);
                avatarDiv.appendChild(avatarInnerDiv);

                // Tạo div chứa tên người dùng
                const nameDiv = document.createElement('div');
                nameDiv.classList.add('flex-grow-1', 'overflow-hidden');

                const userName = document.createElement('p');
                userName.classList.add('text-truncate', 'mb-0');
                userName.innerText = user.name; // Gán tên người dùng

                // Gắn tên vào div
                nameDiv.appendChild(userName);

                // Gắn avatarDiv và nameDiv vào mainDiv
                mainDiv.appendChild(avatarDiv);
                mainDiv.appendChild(nameDiv);


                //Tạo thẻ a
                const a = document.createElement('a');
                a.appendChild(mainDiv);
                //Gắmh link với người dùng
                li.setAttribute('id', user.id);

                // Gắn mainDiv vào li
                li.appendChild(a);

                // Thêm li vào danh sách
                userElement.appendChild(li);
            })
            .leaving((user) => {
                //Xoá thành người dùng khi ở trạng thái rời phòng
                const element = document.getElementById(user.id);
                element.parentNode.removeChild(element);
            }).listen('MessageSent', (event) => {
                const li = document.createElement('li');
                li.setAttribute('id', event.user.id);
                //Lấy id hiện tại
                const currentUserId = document.getElementById('currentUser').getAttribute('data-user-id');
                //Log thử xem có dữ liệu ủa người dùng hay khong
                // console.log(currentUserId.id);
                // Kiểm tra nếu người gửi là bạn, thì thêm class 'right', ngược lại là 'left'
                if (event.user.id == currentUserId) {
                    li.classList.add('chat-list', 'right');
                } else {
                    li.classList.add('chat-list', 'left');
                }

                // Tạo cấu trúc nội dung của tin nhắn
                const conversationDiv = document.createElement('div');
                conversationDiv.classList.add('conversation-list');

                const avatarDiv = document.createElement('div');
                avatarDiv.classList.add('chat-avatar');
                const avatarImg = document.createElement('img');
//Xử lý ảnh nếu rỗng thông tin người dùng
                if (event.user.avatar == null) {
                    avatarImg.src =
                        'https://png.pngtree.com/png-clipart/20210608/ourlarge/pngtree-dark-gray-simple-avatar-png-image_3418404.jpg';
                    avatarImg.alt = event.user.name;
                    avatarImg.classList.add('rounded-circle', 'img-fluid', 'userprofile');
                } else {
                    avatarImg.src = '{{ asset('storage') }}' + '/' + event.user.avatar; // Đường dẫn avatar từ API
                    avatarImg.alt = event.user.name;
                    avatarImg.classList.add('rounded-circle', 'img-fluid', 'userprofile');
                }

                avatarDiv.appendChild(avatarImg);

                const userChatContent = document.createElement('div');
                userChatContent.classList.add('user-chat-content');

                const ctextWrap = document.createElement('div');
                ctextWrap.classList.add('ctext-wrap');

                const ctextWrapContent = document.createElement('div');
                ctextWrapContent.classList.add('ctext-wrap-content');
                ctextWrapContent.id = event.message_id; // Gán id của tin nhắn để theo dõi

                const messageParagraph = document.createElement('p');
                messageParagraph.classList.add('mb-0', 'ctext-content');
                messageParagraph.innerText = event.message; // Nội dung tin nhắn

                // Gắn các phần tử con vào đúng vị trí
                ctextWrapContent.appendChild(messageParagraph);
                ctextWrap.appendChild(ctextWrapContent);
                userChatContent.appendChild(ctextWrap);
                conversationDiv.appendChild(avatarDiv);
                conversationDiv.appendChild(userChatContent);

                li.appendChild(conversationDiv);



                // Thêm thẻ li vào danh sách tin nhắn
                messagesElement.appendChild(li);
            });
    </script>
    <script type="module">
        //Lấy ô input này ra để lấy giá trị của nó
        const messageElement = document.getElementById('message');
        const sentElement = document.getElementById('send');
        sentElement.addEventListener('click', (e) => {
            // Chặn lại thao tác load form
            e.preventDefault();
            //Gửi dữ liệu = axios.post
            window.axios.post('chat/message', {
                message: messageElement.value
            });
            //  alert("OK mà" + messageElement.value);
            //Khi gửi xong đặt lại giá trị set lại rỗng
            messageElement.value = '';
        });
    </script>
    <!-- glightbox js -->
    <script src="{{ asset('theme/admin/assets/libs/glightbox/js/glightbox.min.js') }}"></script>

    <!-- fgEmojiPicker js -->
    <script src="{{ asset('theme/admin/assets/libs/fg-emoji-picker/fgEmojiPicker.js') }}"></script>

    <!-- chat init js -->
    <script src="{{ asset('theme/admin/assets/js/pages/chat.init.js') }}"></script>
@endsection
