# Bookstore Customer

Ứng dụng web dành cho khách hàng và nhân viên nội bộ của cửa hàng sách (Bookstore Customer) được xây dựng bằng PHP.  
Dự án phục vụ mục đích học tập và thực hành xây dựng website bán hàng cơ bản.

Dự án đang được chuyển đổi sang Laravel.

Project: https://github.com/khanh779-9/bookstore_customer_laravel

---

## 1. Mô tả dự án

Bookstore Customer là trang web bán hàng cho phép người dùng:

- Xem danh sách sản phẩm
- Xem chi tiết sản phẩm
- Truy xuất dữ liệu sách
- Quản lý sp yêu thích, giỏ hàng dành cho khách hànng
- Thu thập dữ liệu sản phẩm thông qua module crawl
- Xem - quản lý sản phẩm & đối tác, phân phối cho nhân viên
- Thực hiện xử lý giao dịch, đơn hàng cho nhân vuên
- Phân quyền cho user nhân viên
- V.v

---

## 2. Cấu trúc thư mục
```
├── app/ Chứa logic xử lý
├── assets/ Chứa  hình ảnh, banner
├── config/ Cấu hình hệ thống và database
├── crawl/products/ Script thu thập dữ liệu sản phẩm
├── public/ 
│ ├── css/
│ ├── js/
│ └── images/
├── index.php Trang vào trang chính
├── Dockerfile Cấu hình chạy bằng Docker
└── sitemap.xml Hỗ trợ SEO
```

---

## 3. Công nghệ sử dụng

- PHP
- HTML
- CSS
- JavaScript
- Docker (tùy chọn, để chạy demo)

---

## 4. Hướng dẫn cài đặt

### Bước 1: Clone repository

```bash
git clone https://github.com/khanh779-9/bookstore_customer.git
cd bookstore_customer
```

Bước 2: Cài đặt môi trường

Yêu cầu:

PHP 7.x hoặc 8.x

Có thể sử dụng XAMPP, WAMP hoặc Docker

Bước 3: Cấu hình database

Chỉnh sửa file cấu hình trong thư mục config/

Thiết lập các thông tin:

host

username

password

database name

Bước 4: Chạy ứng dụng

Chạy local server:

php -S localhost:8000

Truy cập trình duyệt:

http://localhost:8000
