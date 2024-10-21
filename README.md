# Website Hệ Thống Học Trực Tuyến Tân Tiến

![Laravel](https://img.shields.io/badge/laravel-^10.0-red)
![PHP](https://img.shields.io/badge/PHP-^8.1-blue)
![MySQL](https://img.shields.io/badge/mysql-^8.0-orange)
![License](https://img.shields.io/badge/license-MIT-green)

Dự án chúng tôi sẽ cung cấp những khoá học bổ ích. Điều đặc biệt có thể kiếm tiền trên hệ thống bằng cách tạo ra những khoá học và được kiểm duyệt.

## Mục Lục

- [Cài Đặt](#cài-đặt)
- [Cấu Hình](#cấu-hình)
- [Sử Dụng](#sử-dụng)
- [Tính Năng](#tính-năng)
- [Kiểm Thử](#kiểm-thử)
- [Đóng Góp](#đóng-góp)
- [Quy Ước Mã Nguồn](#mã-nguồn)
- [Quy Ước Cách Đặt Tên](#quy-ước-cách-đặt-tên)

## Cài Đặt

### Yêu Cầu

- PHP 8.1 hoặc cao hơn
- Composer
- MySQL 8.0 hoặc cao hơn
- Node.js và npm

### Các Bước

1. **Clone kho lưu trữ**

    ```bash
    git clone https://github.com/tên-tài-khoản/cái-repository.git
    cd cái-repository
    ```

2. **Cài đặt các phụ thuộc**

    ```bash
    composer install
    npm install
    npm run dev
    ```

3. **Sao chép file `.env`**

    ```bash
    cp .env.example .env
    ```

4. **Tạo khóa ứng dụng**

    ```bash
    php artisan key:generate
    ```

5. **Cấu hình cơ sở dữ liệu**

    Cập nhật file `.env` với thông tin cơ sở dữ liệu của bạn:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=tên_cơ_sở_dữ_liệu
    DB_USERNAME=tên_tài_khoản
    DB_PASSWORD=mật_khẩu
    ```

6. **Chạy các migration**

    ```bash
    php artisan migrate --seed
    ```

7. **Khởi động máy chủ phát triển**

    ```bash
    php artisan serve
    ```

## Cấu Hình

- **Biến Môi Trường**: Tùy chỉnh file `.env` để cấu hình các thiết lập như email, cache và API bên thứ ba.
- **Lịch Trình**: Cấu hình các tác vụ định kỳ trong `app/Console/Kernel.php`.

## Sử Dụng

- Truy cập ứng dụng qua `http://localhost:8000`.
- Sử dụng hệ thống xác thực cung cấp để đăng nhập hoặc đăng ký.
- Quản lý các thực thể thông qua bảng điều khiển quản trị (nếu có).

## Tính Năng

- **Xác Thực**: Đăng ký người dùng, đăng nhập và đặt lại mật khẩu.
- **Hoạt Động CRUD**: Toàn bộ các hoạt động CRUD cho tất cả các thực thể chính.
- **API RESTful**: Các điểm cuối API được tài liệu hóa tốt với xác thực JWT.
- **Thiết Kế Responsive**: Giao diện thân thiện với di động sử dụng Bootstrap 5.

## Kiểm Thử

Chạy bộ kiểm thử bằng PHPUnit:

```bash
php artisan test
```
## Đóng Góp
Muốn đẩy code lên GIT.
- Tạo nhánh tính năng của bạn (git checkout -b feature/tính-năng-của-bạn).
- Commit các thay đổi của bạn (git commit -am 'Thêm tính năng mới').
- Đẩy lên nhánh (git push origin feature/tính-năng-của-bạn). 
- Mở pull request.

## Quy Ước Mã Nguồn
- **app/**:Chứa mã nguồn của ứng dụng, bao gồm các Controllers, Models và Middleware.
- **config/**:Các tệp cấu hình cho ứng dụng.
- **database/**:Các migration, seeders và factories.
- **resources/**:Các tệp giao diện người dùng (views, stylesheets, scripts).
- **routes/**:Các tệp định nghĩa các tuyến đường của ứng dụng.

## Quy Ước Cách Đặt Tên
- **Tên Biến**:Sử dụng camelCase cho tên biến. Ví dụ: $userProfile, $orderItems.
- **Tên Hàm**:Sử dụng camelCase cho tên hàm. Ví dụ: getUserProfile(), calculateTotalPrice().
- **Tên Class**:Sử dụng PascalCase cho tên class. Ví dụ: UserProfileController, OrderService.
- **Hằng Số**:Sử dụng chữ hoa và gạch dưới cho hằng số. Ví dụ: MAX_ATTEMPTS, DEFAULT_TIMEOUT.
- **Tên Tệp:**:Sử dụng snake_case cho tên tệp. Ví dụ: user_profile_controller.php, order_service.php.