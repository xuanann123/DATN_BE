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

        .highlight {
            background-color: rgb(253, 253, 229);

        }

        /* Style cho tin nhắn được active */
        .active {
            background-color: #dcdbec;
            /* Màu nền nổi bật */
            border-radius: 8px;
            /* Thêm viền màu vàng */
            padding: 5px;
            /* Tăng khoảng cách để dễ nhìn */
            transition: background-color 1s, transform 1s;
            /* Thêm hiệu ứng chuyển động */
        }

        /* Nếu bạn muốn hiệu ứng nhấp nháy khi tìm thấy */
        @keyframes highlightAnimation {
            0% {
                background-color: #92b0db;
            }

            50% {
                background-color: rgb(224, 224, 212);
            }

            100% {
                background-color: #c3c1be;
            }
        }

        .active {
            border-radius: 5px;
            animation: highlightAnimation 1s ease-in-out infinite;
        }
    </style>
@endsection

@section('content')
    <div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1">

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
                                                        <h5 class="text-truncate mb-0 fs-16"><a class="text-reset username"
                                                                data-bs-toggle="offcanvas" href="#userProfileCanvasExample"
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
                                                        <a class="dropdown-item" href="{{ route('admin.qna.delete.all') }}"
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
                                                                <div class="dropdown-menu"> <a
                                                                        class="dropdown-item reply-message"
                                                                        href="#"><i
                                                                            class="ri-reply-line me-2 text-muted align-bottom"></i>Reply</a>
                                                                    <a class="dropdown-item" href="#"><i
                                                                            class="ri-share-line me-2 text-muted align-bottom"></i>Forward</a>
                                                                    <a class="dropdown-item copy-message" href="#"><i
                                                                            class="ri-file-copy-line me-2 text-muted align-bottom"></i>Copy</a>
                                                                    <a class="dropdown-item" href="#"><i
                                                                            class="ri-bookmark-line me-2 text-muted align-bottom"></i>Bookmark</a>
                                                                    <a class="dropdown-item delete-item" href="#"><i
                                                                            class="ri-delete-bin-5-line me-2 text-muted align-bottom"></i>Delete</a>
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
                                                            <div class="dropdown align-self-start message-box-drop"> <a
                                                                    class="dropdown-toggle" href="#" role="button"
                                                                    data-bs-toggle="dropdown" aria-haspopup="true"
                                                                    aria-expanded="false"> <i class="ri-more-2-fill"></i>
                                                                </a>
                                                                <div class="dropdown-menu"> <a
                                                                        class="dropdown-item reply-message"
                                                                        href="#"><i
                                                                            class="ri-reply-line me-2 text-muted align-bottom"></i>Reply</a>
                                                                    <a class="dropdown-item" href="#"><i
                                                                            class="ri-share-line me-2 text-muted align-bottom"></i>Forward</a>
                                                                    <a class="dropdown-item copy-message"
                                                                        href="#"><i
                                                                            class="ri-file-copy-line me-2 text-muted align-bottom"></i>Copy</a>
                                                                    <a class="dropdown-item" href="#"><i
                                                                            class="ri-bookmark-line me-2 text-muted align-bottom"></i>Bookmark</a>
                                                                    <a class="dropdown-item delete-item" href="#"><i
                                                                            class="ri-delete-bin-5-line me-2 text-muted align-bottom"></i>Delete</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="conversation-name"><span class="d-none name">Lisa
                                                                Parker</span><small class="text-muted time">{{ $message->created_at }}
                                                                am</small> <span class="text-success check-message-icon"><i
                                                                    class="bx bx-check-double"></i></span></div>
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
            const sendButton = document.getElementById('send');
            const messageInput = document.getElementById('message');
            const messagesList = document.getElementById('messages');
            const nameUser = document.getElementById('nameUserLogin').value;
            const imageUser = document.getElementById('imageUserLogin').value;

            console.log(nameUser);
            console.log(imageUser);


            // Gửi câu hỏi đến server và nhận kết quả
            async function sendMessage() {
                const question = messageInput.value;

                if (!question) {
                    alert("Hãy nhập câu hỏi trước khi gửi!");
                    return;
                }
                // Hiển thị tin nhắn đã gửi
                const userMessageHtml =
                    `
<li class="chat-list right">
                                                <div class="conversation-list">
                                                    <div class="user-chat-content">
                                                        <div class="ctext-wrap">
                                                            <div class="ctext-wrap-content" id="5">
                                                                <p class="mb-0 ctext-content">${question}</p>
                                                            </div>
                                                            <div class="dropdown align-self-start message-box-drop"> <a
                                                                    class="dropdown-toggle" href="#" role="button"
                                                                    data-bs-toggle="dropdown" aria-haspopup="true"
                                                                    aria-expanded="false"> <i class="ri-more-2-fill"></i>
                                                                </a>
                                                                <div class="dropdown-menu"> <a
                                                                        class="dropdown-item reply-message"
                                                                        href="#"><i
                                                                            class="ri-reply-line me-2 text-muted align-bottom"></i>Reply</a>
                                                                    <a class="dropdown-item" href="#"><i
                                                                            class="ri-share-line me-2 text-muted align-bottom"></i>Forward</a>
                                                                    <a class="dropdown-item copy-message" href="#"><i
                                                                            class="ri-file-copy-line me-2 text-muted align-bottom"></i>Copy</a>
                                                                    <a class="dropdown-item" href="#"><i
                                                                            class="ri-bookmark-line me-2 text-muted align-bottom"></i>Bookmark</a>
                                                                    <a class="dropdown-item delete-item" href="#"><i
                                                                            class="ri-delete-bin-5-line me-2 text-muted align-bottom"></i>Delete</a>
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

`
                // `<li class="chat-message user-message"><div class="message-content">${question}</div></li>`;
                messagesList.innerHTML += userMessageHtml;
                messageInput.value = "";

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
                    const data = await response.json();

                    const answer = data.answer;

                    // Hiển thị câu trả lời từ AI
                    const answerHtml =
                        `
                         <li class="chat-list left" id="1">
                                                <div class="conversation-list">
                                                    <div class="chat-avatar"><img
                                                            src="https://dl.memuplay.com/new_market/img/com.smartwidgetlabs.chatgpt.icon.2023-06-18-09-30-08.png"
                                                            alt=""></div>
                                                    <div class="user-chat-content">
                                                        <div class="ctext-wrap">
                                                            <div class="ctext-wrap-content" id="1">
                                                                <p class="mb-0 ctext-content">${answer}</p>
                                                            </div>
                                                            <div class="dropdown align-self-start message-box-drop"> <a
                                                                    class="dropdown-toggle" href="#" role="button"
                                                                    data-bs-toggle="dropdown" aria-haspopup="true"
                                                                    aria-expanded="false"> <i class="ri-more-2-fill"></i>
                                                                </a>
                                                                <div class="dropdown-menu"> <a
                                                                        class="dropdown-item reply-message"
                                                                        href="#"><i
                                                                            class="ri-reply-line me-2 text-muted align-bottom"></i>Reply</a>
                                                                    <a class="dropdown-item" href="#"><i
                                                                            class="ri-share-line me-2 text-muted align-bottom"></i>Forward</a>
                                                                    <a class="dropdown-item copy-message"
                                                                        href="#"><i
                                                                            class="ri-file-copy-line me-2 text-muted align-bottom"></i>Copy</a>
                                                                    <a class="dropdown-item" href="#"><i
                                                                            class="ri-bookmark-line me-2 text-muted align-bottom"></i>Bookmark</a>
                                                                    <a class="dropdown-item delete-item" href="#"><i
                                                                            class="ri-delete-bin-5-line me-2 text-muted align-bottom"></i>Delete</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="conversation-name"><span class="d-none name">Lisa
                                                                Parker</span><small class="text-muted time">{{ $message->created_at }}
                                                                am</small> <span class="text-success check-message-icon"><i
                                                                    class="bx bx-check-double"></i></span></div>
                                                    </div>
                                                </div>
                                            </li>`;
                    messagesList.innerHTML += answerHtml;

                } catch (error) {
                    console.error('Error:', error);
                    const errorHtml =
                        `<li class="chat-message error-message"><div class="message-content">Đã có lỗi xảy ra.</div></li>`;
                    messagesList.innerHTML += errorHtml;
                }
            }
            // Bắt sự kiện enter
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
