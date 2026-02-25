# Bookstore Customer

Ứng dụng web dành cho khách hàng của cửa hàng sách (Bookstore Customer) được xây dựng bằng PHP.  
Dự án phục vụ mục đích học tập và thực hành xây dựng website bán hàng cơ bản.

---

## 1. Mô tả dự án

Bookstore Customer là hệ thống web cho phép người dùng:

- Xem danh sách sách
- Xem chi tiết sản phẩm
- Truy xuất dữ liệu sách
- Thu thập dữ liệu sản phẩm thông qua module crawl

Dự án tập trung vào phía khách hàng (customer side) và có thể mở rộng thêm các chức năng như giỏ hàng, đăng ký, đăng nhập và thanh toán.

---

## 2. Cấu trúc thư mục
├── app/ Chứa logic xử lý backend
├── assets/ Chứa CSS, JS, hình ảnh
├── config/ Cấu hình hệ thống và database
├── crawl/products/ Script thu thập dữ liệu sản phẩm
├── public/ Thư mục truy cập từ trình duyệt
│ ├── css/
│ ├── js/
│ └── images/
├── index.php Điểm vào chính của ứng dụng
├── Dockerfile Cấu hình chạy bằng Docker
└── sitemap.xml Hỗ trợ SEO


---

## 3. Công nghệ sử dụng

- PHP
- HTML
- CSS
- JavaScript
- Docker (tùy chọn)

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
