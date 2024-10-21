import '../bootstrap';
//window echo lấy trong boostrap
//Echo cái channel vouchers ra => muốn kích hoạt event thì dùng listen 
const messagesElement = document.getElementById('messages');
const userElement = document.getElementById('userList');
const countMember = document.getElementById("countMember");
//Hiển thị tin nhắn ngay sau khi tài trang lần đầu
document.addEventListener('DOMContentLoaded', function () {
    window.axios.get('chat/api')
        .then(response => {
            const messages = response.data.messages;
            messages.forEach(message => {
                displayMessage(message); // Hàm hiển thị tin nhắn
            });
        })
        .catch(error => {
            console.error('Error fetching messages:', error);
        });
});

//Dùng presense
Echo.join('chat')
    //Hiển thị danh sách người dùng
    .here((users) => {
        //Hiển thị tin nhắn nhóm từ trước đã lưu trên database
        countMember.innerText = users.length + " thành viên đang online";
        users.forEach((user) => {
            // Tạo các phần tử HTML tương ứng
            // Tạo thẻ li
            const li = document.createElement('li');
            li.setAttribute('onclick', `greetUser(${user.id})`);
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
                img.src =
                    'https://png.pngtree.com/png-clipart/20210608/ourlarge/pngtree-dark-gray-simple-avatar-png-image_3418404.jpg';
                img.alt = user.name;
                img.classList.add('rounded-circle', 'img-fluid', 'userprofile');
            } else {
                img.src = 'http://127.0.0.1:8000/storage' + '/' + user.avatar; // Đường dẫn avatar từ API
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
        li.setAttribute('onclick', `greetUser(${user.id})`);
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
            img.src = 'http://127.0.0.1:8000/storage' + '/' + user.avatar; // Đường dẫn avatar từ API
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
        //Hiển thị tin tắn
        displayMessage(event);
    });

function displayMessage(event) {
    const li = document.createElement('li');
    li.setAttribute('id', event.message_id);

    // Lấy id người dùng hiện tại từ thẻ HTML có id="currentUser"
    const currentUserId = document.getElementById('currentUser').getAttribute('data-user-id');

    // Kiểm tra nếu người gửi là bạn, thì thêm class 'right', ngược lại là 'left'
    if (event.user.id == currentUserId) {
        li.classList.add('chat-list', 'right');
    } else {
        li.classList.add('chat-list', 'left');
    }
    // Tạo cấu trúc nội dung của tin nhắn
    const conversationDiv = document.createElement('div');
    conversationDiv.classList.add('conversation-list');

    // Phần avatar
    const avatarDiv = document.createElement('div');
    avatarDiv.classList.add('chat-avatar');
    const avatarImg = document.createElement('img');

    // Kiểm tra nếu user không có avatar, thì sử dụng ảnh mặc định
    if (!event.user.avatar) {
        avatarImg.src =
            'https://png.pngtree.com/png-clipart/20210608/ourlarge/pngtree-dark-gray-simple-avatar-png-image_3418404.jpg';
    } else {
        avatarImg.src = 'http://127.0.0.1:8000/storage' + '/' + event.user.avatar;
    }

    avatarImg.alt = event.user.name;
    avatarImg.classList.add('rounded-circle', 'img-fluid', 'userprofile');
    avatarDiv.appendChild(avatarImg);

    // Phần nội dung tin nhắn
    const userChatContent = document.createElement('div');
    userChatContent.classList.add('user-chat-content');

    const ctextWrap = document.createElement('div');
    ctextWrap.classList.add('ctext-wrap');

    const ctextWrapContent = document.createElement('div');
    ctextWrapContent.classList.add('ctext-wrap-content');
    ctextWrapContent.id = event.message_id;

    const messageParagraph = document.createElement('p');
    messageParagraph.classList.add('mb-0', 'ctext-content');
    messageParagraph.innerText = event.message;

    // Gắn các phần tử vào nhau
    ctextWrapContent.appendChild(messageParagraph);
    ctextWrap.appendChild(ctextWrapContent);
    userChatContent.appendChild(ctextWrap);
    conversationDiv.appendChild(avatarDiv);
    conversationDiv.appendChild(userChatContent);
    li.appendChild(conversationDiv);

    // Thêm thẻ li vào danh sách tin nhắn
    const messagesElement = document.getElementById('messages'); // Đảm bảo bạn có id này trong HTML
    messagesElement.appendChild(li);

    // Cuộn xuống cuối tin nhắn
    messagesElement.scrollTop = messagesElement.scrollHeight;

}
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