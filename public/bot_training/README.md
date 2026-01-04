# API Dữ liệu cho Training Chatbot Mira AI

## Tổng quan
Hệ thống cung cấp 2 endpoint API trả về dữ liệu JSON đầy đủ để training chatbot Mira AI:

1. **products_data.php** - Dữ liệu sản phẩm
2. **store_info.php** - Thông tin nhà sách & chính sách

## API Endpoints

### 1. API Dữ liệu Sản phẩm

#### Truy cập
```
http://localhost/public/bot_traning/products_data.php
```

hoặc trên production:

```
https://your-domain.com/public/bot_traning/products_data.php
```

#### Mô tả
Trả về toàn bộ thông tin chi tiết về sản phẩm bao gồm sách và văn phòng phẩm.

### 2. API Thông tin Nhà sách

#### Truy cập
```
http://localhost/public/bot_traning/store_info.php
```

hoặc trên production:

```
https://your-domain.com/public/bot_traning/store_info.php
```

#### Mô tả
Trả về toàn bộ thông tin về nhà sách, chính sách, điều khoản và FAQs.

---

## Cấu trúc Dữ liệu Chi tiết

### API 1: Products Data (products_data.php)

#### Response Structure

```json
{
  "success": true,
  "metadata": {
    "total_products": 50,
    "total_books": 40,
    "total_stationery": 10,
    "generated_at": "2025-12-22 10:30:00",
    "categories": [...],
    "book_categories": [...]
  },
  "products": [
    {
      "id": 1,
      "name": "Tên sản phẩm",
      "type": "book",
      "description": "Mô tả chi tiết sản phẩm...",
      "price": 45000,
      "stock_quantity": 129,
      "sold_quantity": 47,
      "image": "toan10_canhdieu_tap_1.jpg",
      "image_url": "/assets/images/products/toan10_canhdieu_tap_1.jpg",
      "category": {
        "id": 1,
        "name": "Sách",
        "description": "..."
      },
      "unit": {
        "id": 1,
        "name": "Quyển"
      },
      "provider": {
        "id": 1,
        "name": "Nhà cung cấp ABC",
        "address": "...",
        "phone": "...",
        "email": "..."
      },
      "book_details": {
        "book_id": 1,
        "category_name": "Sách giáo khoa",
        "year_published": "2024",
        "author": {
          "id": 1,
          "full_name": "Nguyễn Văn A",
          "first_name": "Nguyễn",
          "middle_name": "Văn",
          "last_name": "A",
          "address": "...",
          "phone": "...",
          "email": "..."
        },
        "publisher": {
          "id": 1,
          "name": "NXB Giáo dục",
          "address": "...",
          "phone": "...",
          "email": "..."
        }
      },
      "promotions": [
        {
          "id": 1,
          "name": "Giảm giá mùa tựu trường",
          "discount_rate": 10,
          "start_date": "2025-08-01",
          "end_date": "2025-09-30",
          "discounted_price": 40500
        }
      ],
      "reviews": {
        "total_reviews": 5,
        "average_rating": 4.5,
        "reviews_list": [
          {
            "id": 1,
            "customer_id": 1,
            "customer_name": "Nguyễn Văn B",
            "rating": 5,
            "comment": "Sách rất tốt!",
            "created_at": "2025-11-04 19:09:22"
          }
        ]
      },
      "stock_status": "in_stock",
      "final_price": 40500
    }
  ]
}
```

---

### API 2: Store Info (store_info.php)

#### Response Structure

```json
{
  "success": true,
  "generated_at": "2025-12-22 10:30:00",
  "data": {
    "store_info": {
      "name": "BookZone",
      "tagline": "Khai Nguồn Hứng Sáng Tạo",
      "description": "...",
      "mission": "...",
      "contact": {
        "address": "180 Cao Lỗ, Quận 8, TP.HCM",
        "phone": "(+84) 0239 482 958",
        "email": "qkhanh12.duration060@passinbox.com",
        "working_hours": "Thứ Hai - Chủ Nhật: 8:00 - 22:00"
      },
      "history": [...],
      "core_values": [...]
    },
    "privacy_policy": {
      "title": "Chính sách bảo mật",
      "summary": "...",
      "highlights": [...],
      "sections": [...]
    },
    "return_policy": {
      "title": "Chính sách đổi trả",
      "summary": "...",
      "highlights": [...],
      "conditions": [...],
      "process": [...],
      "refund_methods": [...],
      "contact": {...}
    },
    "warranty_policy": {
      "title": "Chính sách bảo hành",
      "summary": "...",
      "warranty_periods": [...],
      "warranty_coverage": [...],
      "warranty_process": [...],
      "important_notes": [...]
    },
    "shipping_policy": {
      "title": "Vận chuyển & Giao hàng",
      "summary": "...",
      "shipping_methods": [...],
      "shipping_fee_policy": [...],
      "delivery_process": [...],
      "order_tracking": [...],
      "delivery_issues": [...]
    },
    "payment_info": {
      "payment_methods": [...]
    },
    "faqs": [
      {
        "category": "Đặt hàng",
        "questions": [
          {
            "question": "...",
            "answer": "..."
          }
        ]
      }
    ]
  }
}
```

---

## Các trường dữ liệu Products API
- `total_products`: Tổng số sản phẩm
- `total_books`: Tổng số sách
- `total_stationery`: Tổng số văn phòng phẩm
- `generated_at`: Thời gian tạo dữ liệu
- `categories`: Danh sách các danh mục sản phẩm
- `book_categories`: Danh sách các loại sách

### Product Fields
- `id`: ID sản phẩm
- `name`: Tên sản phẩm
- `type`: Loại sản phẩm ('book' hoặc 'vpp')
- `description`: Mô tả chi tiết
- `price`: Giá gốc
- `final_price`: Giá sau khuyến mãi
- `stock_quantity`: Số lượng tồn kho
- `sold_quantity`: Số lượng đã bán
- `image`: Tên file ảnh
- `image_url`: Đường dẫn đầy đủ đến ảnh
- `category`: Thông tin danh mục
- `unit`: Đơn vị tính
- `provider`: Thông tin nhà cung cấp
- `book_details`: Chi tiết sách (chỉ có khi type='book')
  - `author`: Thông tin tác giả
  - `publisher`: Thông tin nhà xuất bản
  - `year_published`: Năm xuất bản
  - `category_name`: Loại sách
- `promotions`: Danh sách khuyến mãi đang áp dụng
- `reviews`: Thông tin đánh giá
- `stock_status`: Trạng thái tồn kho ('in_stock', 'low_stock', 'out_of_stock')

---

## Lưu ý khi training chatbot

### 1. Kết hợp cả 2 nguồn dữ liệu
Chatbot cần được training với cả:
- **Products Data**: Để trả lời về sản phẩm, giá cả, tồn kho
- **Store Info**: Để trả lời về chính sách, liên hệ, câu hỏi thường gặp

### 2. Xử lý dữ liệu null

### Store Info
- `name`: Tên nhà sách
- `tagline`: Khẩu hiệu
- `description`: Mô tả chi tiết về nhà sách
- `mission`: Sứ mệnh
- `contact`: Thông tin liên hệ (địa chỉ, điện thoại, email, giờ làm việc)
- `history`: Lịch sử phát triển qua các năm
- `core_values`: Giá trị cốt lõi

### Privacy Policy
- `title`: Tiêu đề chính sách
- `summary`: Tóm tắt
- `highlights`: Điểm nổi bật
- `sections`: Các mục chi tiết (thu thập, sử dụng, bảo mật thông tin)

### Return Policy
- `conditions`: Điều kiện đổi trả
- `process`: Quy trình đổi trả từng bước
- `refund_methods`: Các hình thức hoàn tiền

### Warranty Policy
- `warranty_periods`: Thời hạn bảo hành theo loại sản phẩm
- `warranty_coverage`: Phạm vi bảo hành
- `warranty_process`: Quy trình bảo hành

### Shipping Policy
- `shipping_methods`: Các hình thức vận chuyển
- `shipping_fee_policy`: Chính sách phí vận chuyển
- `delivery_process`: Quy trình giao hàng
- `order_tracking`: Cách theo dõi đơn hàng
- `delivery_issues`: Xử lý các vấn đề giao hàng

### Payment Info
- `payment_methods`: Các phương thức thanh toán được hỗ trợ

### FAQs
- Câu hỏi thường gặp được phân loại theo chủ đề
- Mỗi câu hỏi có câu trả lời chi tiết

---

### 1. Xử lý dữ liệu null
Một số trường có thể là `null` nếu không có dữ liệu. Cần xử lý:
- `book_details.author`: Có thể null nếu sách không có tác giả
- `book_details.publisher`: Có thể null nếu không có nhà xuất bản
- `promotions`: Có thể rỗng nếu không có khuyến mãi

### 3. Các loại câu hỏi chatbot có thể trả lời

#### Về sản phẩm (từ products_data.php):
- "Có sách nào về [chủ đề]?"
- "Giá của [tên sách] là bao nhiêu?"
- "[Tên sách] còn hàng không?"
- "Cho tôi xem sách của tác giả [tên tác giả]"

#### Về chính sách & thông tin nhà sách (từ store_info.php):
- "Chính sách đổi trả như thế nào?"
- "Thời gian bảo hành sản phẩm là bao lâu?"
- "Có những hình thức thanh toán nào?"
- "Phí vận chuyển là bao nhiêu?"
- "Địa chỉ cửa hàng ở đâu?"
- "Giờ làm việc của cửa hàng?"
- "Làm thế nào để liên hệ với BookZone?"
- "Lịch sử của nhà sách BookZone?"

#### Về khuyến mãi (từ products_data.php):
- "Sản phẩm nào đang giảm giá?"
- "Khuyến mãi gì đang diễn ra?"
- "[Tên sách] có giảm giá không?"

#### Về đánh giá (từ products_data.php):
- "Sách nào được đánh giá cao nhất?"
- "Đánh giá về [tên sách] như thế nào?"
- "Có review về [tên sách] không?"

#### Về tồn kho (từ products_data.php):
- "[Tên sách] còn bao nhiêu quyển?"
- "Sản phẩm nào sắp hết hàng?"
- "Sách nào bán chạy nhất?"

#### Về FAQs (từ store_info.php):
- "Làm thế nào để đặt hàng?"
- "Tôi có thể hủy đơn hàng không?"
- "Làm sao để theo dõi đơn hàng?"
- "Tôi quên mật khẩu phải làm sao?"

### 4. Gợi ý xử lý cho AI

#### Tìm kiếm sản phẩm
```python
# Tìm theo tên (fuzzy search)
def search_products(query, products):
    query_lower = query.lower()
    results = []
    for product in products:
        if query_lower in product['name'].lower() or 
           query_lower in product['description'].lower():
            results.append(product)
    return results
```

#### Lọc theo giá
```python
def filter_by_price(products, min_price, max_price):
    return [p for p in products 
            if min_price <= p['final_price'] <= max_price]
```

#### Sắp xếp theo đánh giá
```python
def sort_by_rating(products):
    return sorted(products, 
                  key=lambda p: p['reviews']['average_rating'], 
                  reverse=True)
```

## Test các API

### Test Products Data API

#### Sử dụng cURL
```bash
curl http://localhost/public/bot_traning/products_data.php
```

#### Lưu vào file
```bash
curl http://localhost/public/bot_traning/products_data.php > products.json
```

### Test Store Info API

#### Sử dụng cURL
```bash
curl http://localhost/public/bot_traning/store_info.php
```

#### Lưu vào file
```bash
curl http://localhost/public/bot_traning/store_info.php > store_info.json
```

### Test tất cả và gộp lại

#### Sử dụng Python
```python
import requests
import json

# Lấy dữ liệu sản phẩm
products_response = requests.get('http://localhost/public/bot_traning/products_data.php')
products_data = products_response.json()

# Lấy dữ liệu thông tin nhà sách
store_response = requests.get('http://localhost/public/bot_traning/store_info.php')
store_data = store_response.json()

# Gộp lại
training_data = {
    'products': products_data,
    'store_info': store_data
}

# Lưu vào file
with open('training_data_complete.json', 'w', encoding='utf-8') as f:
    json.dump(training_data, f, ensure_ascii=False, indent=2)

print(f"✓ Tổng số sản phẩm: {products_data['metadata']['total_products']}")
print(f"✓ Thông tin nhà sách: {store_data['data']['store_info']['name']}")
print(f"✓ Số FAQs: {len(store_data['data']['faqs'])}")
```

---

## Kiểm tra dữ liệu training

### Checklist để đảm bảo chất lượng training

**Products Data:**
- [ ] Có đầy đủ thông tin sản phẩm (tên, giá, mô tả)
- [ ] Thông tin tác giả, nhà xuất bản đầy đủ
- [ ] Khuyến mãi được cập nhật chính xác
- [ ] Đánh giá sản phẩm có đủ thông tin
- [ ] Trạng thái tồn kho chính xác

**Store Info:**
- [ ] Thông tin liên hệ đầy đủ và chính xác
- [ ] Các chính sách được mô tả rõ ràng
- [ ] Quy trình (đổi trả, bảo hành, giao hàng) có đủ các bước
- [ ] FAQs bao phủ các chủ đề chính
- [ ] Thông tin thanh toán đầy đủ

---

## Sử dụng dữ liệu cho Training

### Bước 1: Tải dữ liệu
```bash
# Tải Products Data
curl http://localhost/public/bot_traning/products_data.php > products.json

# Tải Store Info
curl http://localhost/public/bot_traning/store_info.php > store_info.json
```

### Bước 2: Xử lý và làm sạch dữ liệu
```python
import json

# Đọc dữ liệu
with open('products.json', 'r', encoding='utf-8') as f:
    products = json.load(f)

with open('store_info.json', 'r', encoding='utf-8') as f:
    store_info = json.load(f)

# Tạo training dataset
training_dataset = []

# 1. Training cho câu hỏi về sản phẩm
for product in products['products']:
    training_dataset.append({
        'intent': 'product_info',
        'entities': {
            'product_name': product['name'],
            'product_id': product['id']
        },
        'response_data': product
    })

# 2. Training cho câu hỏi về chính sách
for section in ['privacy_policy', 'return_policy', 'warranty_policy', 'shipping_policy']:
    policy = store_info['data'][section]
    training_dataset.append({
        'intent': section,
        'response_data': policy
    })

# 3. Training cho FAQs
for faq_category in store_info['data']['faqs']:
    for qa in faq_category['questions']:
        training_dataset.append({
            'intent': 'faq',
            'question': qa['question'],
            'answer': qa['answer'],
            'category': faq_category['category']
        })

# Lưu training dataset
with open('chatbot_training_dataset.json', 'w', encoding='utf-8') as f:
    json.dump(training_dataset, f, ensure_ascii=False, indent=2)
```

### Bước 3: Tạo intents và entities cho chatbot

```python
# Tạo danh sách intents
intents = [
    'product_search',      # Tìm kiếm sản phẩm
    'product_price',       # Hỏi giá
    'product_availability',# Hỏi tồn kho
    'product_review',      # Hỏi đánh giá
    'promotion_info',      # Hỏi khuyến mãi
    'return_policy',       # Chính sách đổi trả
    'warranty_policy',     # Chính sách bảo hành
    'shipping_info',       # Thông tin vận chuyển
    'payment_method',      # Phương thức thanh toán
    'store_contact',       # Liên hệ
    'store_hours',         # Giờ làm việc
    'faq',                 # Câu hỏi thường gặp
]

# Tạo entities
entities = [
    'product_name',        # Tên sản phẩm
    'product_category',    # Danh mục sản phẩm
    'author_name',         # Tên tác giả
    'publisher_name',      # Nhà xuất bản
    'price_range',         # Khoảng giá
    'policy_type',         # Loại chính sách
]
```

---

## Test API

### Sử dụng cURL
```bash
curl http://localhost/public/bot_traning/products_data.php
```

### Lưu vào file
```bash
curl http://localhost/public/bot_traning/products_data.php > products.json
```

### Kiểm tra với Python
```python
import requests
import json

response = requests.get('http://localhost/public/bot_traning/products_data.php')
data = response.json()

print(f"Tổng số sản phẩm: {data['metadata']['total_products']}")
print(f"Sách: {data['metadata']['total_books']}")
print(f"VPP: {data['metadata']['total_stationery']}")

# In 5 sản phẩm đầu tiên
for product in data['products'][:5]:
    print(f"- {product['name']}: {product['final_price']} VNĐ")
```
---

## Cập nhật dữ liệu

Dữ liệu được lấy real-time từ database mỗi khi gọi API. Không cần cache vì dùng cho training.

**Products Data**: Cập nhật tự động từ database
**Store Info**: Dữ liệu tĩnh, cần sửa trong file nếu có thay đổi chính sách

---

## Troubleshooting

### Lỗi 500 Internal Server Error
- Kiểm tra kết nối database trong `app/config.php`
- Kiểm tra các model files có tồn tại không
- Xem error log: `tail -f /path/to/error.log`

### Dữ liệu trống
- Kiểm tra database có dữ liệu không
- Kiểm tra bảng `sanpham`, `sach`, `vanphongpham`

### Thiếu thông tin
- Một số sản phẩm có thể không có đầy đủ thông tin (author, publisher)
- Đây là dữ liệu hợp lệ, cần xử lý null trong chatbot

### Lỗi 500 Internal Server Error
- Kiểm tra kết nối database trong `app/config.php` (chỉ cho products_data.php)
- Kiểm tra các model files có tồn tại không (chỉ cho products_data.php)
- Xem error log: `tail -f /path/to/error.log`

### Dữ liệu trống
- **Products**: Kiểm tra database có dữ liệu không
- **Store Info**: Kiểm tra file store_info.php có lỗi syntax không

### Thiếu thông tin
- Một số sản phẩm có thể không có đầy đủ thông tin (author, publisher)
- Đây là dữ liệu hợp lệ, cần xử lý null trong chatbot

---

## Tích hợp với Mira AI

### Framework gợi ý

**Dialogflow:**
```javascript
// Intent: product_search
const productName = agent.parameters.product_name;
const productsData = await fetch('http://your-domain.com/public/bot_traning/products_data.php')
  .then(res => res.json());
  
const product = productsData.products.find(p => 
  p.name.toLowerCase().includes(productName.toLowerCase())
);

if (product) {
  agent.add(`${product.name} có giá ${product.final_price.toLocaleString()}đ. ${product.description}`);
} else {
  agent.add('Xin lỗi, chúng tôi không tìm thấy sản phẩm này.');
}
```

**Rasa:**
```yaml
# domain.yml
intents:
  - product_search
  - policy_inquiry
  - store_contact

responses:
  utter_product_info:
    - text: "{product_name} có giá {price}đ. Còn {stock} sản phẩm trong kho."
  
  utter_return_policy:
    - text: "Chính sách đổi trả: {policy_summary}"
```

**Custom AI (Python):**
```python
import requests
from sentence_transformers import SentenceTransformer
import numpy as np

class BookZoneChatbot:
    def __init__(self):
        self.model = SentenceTransformer('paraphrase-multilingual-MiniLM-L12-v2')
        self.load_data()
    
    def load_data(self):
        # Load products
        products_resp = requests.get('http://localhost/public/bot_traning/products_data.php')
        self.products = products_resp.json()['products']
        
        # Load store info
        store_resp = requests.get('http://localhost/public/bot_traning/store_info.php')
        self.store_info = store_resp.json()['data']
        
        # Tạo embeddings
        self.product_texts = [
            f"{p['name']} {p['description']}" for p in self.products
        ]
        self.product_embeddings = self.model.encode(self.product_texts)
    
    def search_product(self, query):
        query_embedding = self.model.encode([query])
        similarities = np.dot(query_embedding, self.product_embeddings.T)[0]
        top_idx = np.argmax(similarities)
        
        if similarities[top_idx] > 0.5:  # Ngưỡng tương đồng
            return self.products[top_idx]
        return None
    
    def get_policy(self, policy_type):
        policy_map = {
            'đổi trả': 'return_policy',
            'bảo hành': 'warranty_policy',
            'vận chuyển': 'shipping_policy',
            'bảo mật': 'privacy_policy'
        }
        
        policy_key = policy_map.get(policy_type)
        if policy_key:
            return self.store_info[policy_key]
        return None
    
    def answer_faq(self, question):
        for category in self.store_info['faqs']:
            for qa in category['questions']:
                if self.is_similar(question, qa['question']):
                    return qa['answer']
        return None
    
    def is_similar(self, q1, q2, threshold=0.7):
        emb1 = self.model.encode([q1])
        emb2 = self.model.encode([q2])
        similarity = np.dot(emb1, emb2.T)[0][0]
        return similarity > threshold

# Sử dụng
bot = BookZoneChatbot()

# Tìm sản phẩm
product = bot.search_product("sách toán 10")
if product:
    print(f"Tìm thấy: {product['name']} - {product['final_price']}đ")

# Hỏi chính sách
policy = bot.get_policy("đổi trả")
if policy:
    print(f"Chính sách: {policy['summary']}")

# Trả lời FAQ
answer = bot.answer_faq("Làm sao để đặt hàng?")
if answer:
    print(f"Trả lời: {answer}")
```

---

## Support

Nếu có vấn đề, kiểm tra:
1. PHP version >= 7.4
2. PDO extension enabled
3. MySQL/MariaDB running
4. Database credentials đúng
5. Các model files tồn tại


## Summary

Bạn đã có 2 API endpoints hoàn chỉnh:

1. **products_data.php** 
   - Dữ liệu sản phẩm từ database
   - Bao gồm: sách, văn phòng phẩm, giá, khuyến mãi, đánh giá
   - Real-time data

2. **store_info.php**
   - Thông tin nhà sách
   - Chính sách: bảo mật, đổi trả, bảo hành, vận chuyển
   - FAQs, thông tin liên hệ, thanh toán
   - Static data

**Sử dụng cả 2 để training chatbot Mira AI toàn diện!**

---

**Tạo bởi:** Bookstore Customer System  
**Mục đích:** Training Mira AI Chatbot  
**Phiên bản:** 2.0  
**Ngày cập nhật:** 2025-12-22
