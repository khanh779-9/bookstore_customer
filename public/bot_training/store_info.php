<?php
/**
 * API endpoint để xuất toàn bộ thông tin nhà sách dưới dạng JSON
 * Bao gồm: Giới thiệu, Liên hệ, Chính sách bảo mật, Đổi trả, Bảo hành, Vận chuyển
 * Dùng cho training chatbot Mira AI
 */

// Thiết lập header cho JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

try {
    // Thông tin chung về nhà sách
    $storeInfo = [
        'name' => 'BookZone',
        'tagline' => 'Khai Nguồn Hứng Sáng Tạo',
        'description' => 'Chào mừng đến với BookZone, nơi mỗi sản phẩm không chỉ là một công cụ mà còn là nguồn cảm hứng cho sự sáng tạo và học hỏi. Chúng tôi tin rằng những vật dụng nhỏ bé trên bàn làm việc có sức mạnh to lớn để biến ý tưởng thành hiện thực. Vì vậy, chúng tôi ra đời với sứ mệnh mang đến những sản phẩm văn phòng phẩm chất lượng, đa dạng và thân thiện với môi trường, phục vụ cho mọi nhu cầu từ học tập, công việc đến đam mê nghệ thuật.',
        'mission' => 'Mang đến những sản phẩm văn phòng phẩm chất lượng, đa dạng và thân thiện với môi trường',
        
        'contact' => [
            'address' => '180 Cao Lỗ, Quận 8, TP.HCM',
            'phone' => '(+84) 0239 482 958',
            'email' => 'qkhanh12.duration060@passinbox.com',
            'working_hours' => 'Thứ Hai - Chủ Nhật: 8:00 - 22:00',
            'map_url' => 'https://www.google.com/maps?q=Trường+Đại+Học+Công+Nghệ+Sài+Gòn+180+Cao+Lỗ+Quận+8+TP.HCM'
        ],
        
        'history' => [
            [
                'year' => '2015',
                'title' => 'Thành lập cửa hàng',
                'description' => 'Cửa hàng nhỏ đầu tiên ra đời từ niềm đam mê về giấy và bút, mang đến những sản phẩm được chọn lọc kĩ lưỡng cho cộng đồng địa phương.'
            ],
            [
                'year' => '2018',
                'title' => 'Mở chi nhánh thứ hai',
                'description' => 'Mở rộng quy mô để phục vụ được nhiều khách hàng hơn, đồng thời giới thiệu thêm nhiều dòng sản phẩm sáng tạo và độc đáo.'
            ],
            [
                'year' => '2020',
                'title' => 'Ra mắt website bán hàng',
                'description' => 'Đưa BookZone lên không gian số, giúp khách hàng trên cả nước dễ dàng tiếp cận và mua sắm tiện lợi hơn.'
            ],
            [
                'year' => '2022',
                'title' => 'Tổ chức sự kiện cộng đồng đầu tiên',
                'description' => 'Tổ chức workshop calligraphy, tạo sân chơi sáng tạo và kết nối những người có cùng đam mê, khẳng định vai trò gắn kết cộng đồng.'
            ]
        ],
        
        'core_values' => [
            [
                'title' => 'Chất Lượng',
                'icon' => 'patch-check-fill',
                'description' => 'Chúng tôi cam kết cung cấp những sản phẩm có chất lượng tối ưu, được lựa chọn kỹ lưỡng từ nhà sản xuất uy tín trong và ngoài nước.'
            ],
            [
                'title' => 'Sáng Tạo',
                'icon' => 'lightbulb-fill',
                'description' => 'Luôn tìm kiếm và cập nhật những sản phẩm độc đáo, khơi nguồn cảm hứng sáng tạo cho người dùng trong công việc và học tập.'
            ],
            [
                'title' => 'Tận Tâm',
                'icon' => 'people-fill',
                'description' => 'Khách hàng là trọng tâm trong mọi hoạt động. Đội ngũ của chúng tôi luôn sẵn sàng hỗ trợ bạn một cách nhiệt tình nhất.'
            ]
        ]
    ];
    
    // Chính sách bảo mật
    $privacyPolicy = [
        'title' => 'Chính sách bảo mật',
        'summary' => 'BookZone cam kết bảo vệ quyền riêng tư của bạn. Chính sách bảo mật này giải thích cách chúng tôi thu thập, sử dụng, chia sẻ và bảo vệ thông tin cá nhân của bạn.',
        'highlights' => [
            'GDPR Compliant - Tuân thủ các tiêu chuẩn bảo vệ dữ liệu quốc tế',
            'SSL 256-bit - Mã hóa tất cả dữ liệu',
            'Không bán dữ liệu cá nhân cho bên thứ ba',
            'Sao lưu dữ liệu hàng ngày'
        ],
        'sections' => [
            [
                'title' => 'Thông tin chúng tôi thu thập',
                'content' => 'Chúng tôi thu thập thông tin cá nhân khi bạn đăng ký tài khoản, đặt hàng, hoặc tương tác với dịch vụ của chúng tôi. Bao gồm: Họ tên, địa chỉ email, số điện thoại, địa chỉ giao hàng, thông tin thanh toán.'
            ],
            [
                'title' => 'Cách chúng tôi sử dụng thông tin',
                'content' => 'Xử lý đơn hàng và giao dịch, Gửi thông báo về đơn hàng và cập nhật, Cải thiện dịch vụ khách hàng, Gửi email marketing (nếu bạn đồng ý), Phân tích và cải thiện website.'
            ],
            [
                'title' => 'Chia sẻ thông tin',
                'content' => 'Chúng tôi không bán hoặc cho thuê thông tin cá nhân của bạn. Thông tin chỉ được chia sẻ với: Đối tác vận chuyển (để giao hàng), Cổng thanh toán (để xử lý giao dịch), Cơ quan pháp luật (khi có yêu cầu hợp pháp).'
            ],
            [
                'title' => 'Bảo mật dữ liệu',
                'content' => 'Mã hóa SSL/TLS cho tất cả dữ liệu truyền tải, Mật khẩu được hash bằng bcrypt, Tường lửa và hệ thống phát hiện xâm nhập, Sao lưu dữ liệu định kỳ, Kiểm tra bảo mật định kỳ.'
            ],
            [
                'title' => 'Quyền của bạn',
                'content' => 'Truy cập và xem thông tin cá nhân, Chỉnh sửa hoặc cập nhật thông tin, Xóa tài khoản và dữ liệu, Từ chối nhận email marketing, Yêu cầu xuất dữ liệu cá nhân.'
            ],
            [
                'title' => 'Cookie và công nghệ theo dõi',
                'content' => 'Chúng tôi sử dụng cookie để: Duy trì phiên đăng nhập, Ghi nhớ giỏ hàng, Phân tích lưu lượng truy cập, Cá nhân hóa trải nghiệm người dùng.'
            ],
            [
                'title' => 'Thời gian lưu trữ',
                'content' => 'Thông tin tài khoản: Cho đến khi bạn yêu cầu xóa, Lịch sử đơn hàng: 5 năm (theo quy định pháp luật), Dữ liệu phân tích: 2 năm.'
            ],
            [
                'title' => 'Liên hệ về bảo mật',
                'content' => 'Nếu bạn có thắc mắc về chính sách bảo mật, vui lòng liên hệ: Email: qkhanh12.duration060@passinbox.com, Hotline: (+84) 0239 482 958'
            ]
        ]
    ];
    
    // Chính sách đổi trả
    $returnPolicy = [
        'title' => 'Chính sách đổi trả',
        'summary' => 'Tự tin mua sắm với quy trình đổi trả rõ ràng, linh hoạt và ưu tiên quyền lợi khách hàng.',
        'highlights' => [
            '30 ngày - Cửa sổ đổi trả',
            'Miễn phí phí vận chuyển cho hàng lỗi',
            '5-7 ngày - Thời gian xử lý',
            'An tâm 1 đổi 1 cho sản phẩm lỗi do nhà sản xuất'
        ],
        'conditions' => [
            [
                'title' => 'Sản phẩm được đổi trả',
                'items' => [
                    'Sản phẩm lỗi do nhà sản xuất',
                    'Sản phẩm giao sai mẫu mã, số lượng',
                    'Sản phẩm bị hư hỏng trong quá trình vận chuyển',
                    'Sản phẩm không đúng mô tả trên website'
                ]
            ],
            [
                'title' => 'Điều kiện đổi trả',
                'items' => [
                    'Trong vòng 30 ngày kể từ ngày nhận hàng',
                    'Sản phẩm còn nguyên tem, nhãn mác, bao bì',
                    'Chưa qua sử dụng, còn mới 100%',
                    'Có hóa đơn mua hàng hoặc mã đơn hàng'
                ]
            ],
            [
                'title' => 'Sản phẩm không được đổi trả',
                'items' => [
                    'Sách, văn phòng phẩm đã qua sử dụng',
                    'Sản phẩm khuyến mãi, giảm giá đặc biệt',
                    'Sản phẩm được làm theo yêu cầu riêng',
                    'Sản phẩm hết thời hạn đổi trả'
                ]
            ]
        ],
        'process' => [
            [
                'step' => 1,
                'title' => 'Liên hệ yêu cầu đổi trả',
                'description' => 'Gọi hotline hoặc gửi email với mã đơn hàng, lý do đổi trả và hình ảnh sản phẩm (nếu có lỗi).'
            ],
            [
                'step' => 2,
                'title' => 'Xác nhận từ BookZone',
                'description' => 'Nhân viên sẽ kiểm tra và phản hồi trong vòng 24 giờ. Nếu đủ điều kiện, chúng tôi sẽ hướng dẫn bạn gửi hàng về.'
            ],
            [
                'step' => 3,
                'title' => 'Gửi sản phẩm về',
                'description' => 'Đóng gói cẩn thận và gửi về địa chỉ của BookZone. Nếu lỗi do nhà sản xuất/vận chuyển, chúng tôi sẽ chịu phí ship.'
            ],
            [
                'step' => 4,
                'title' => 'Kiểm tra sản phẩm',
                'description' => 'BookZone nhận hàng và kiểm tra tình trạng. Thời gian xử lý: 3-5 ngày làm việc.'
            ],
            [
                'step' => 5,
                'title' => 'Hoàn tiền hoặc đổi sản phẩm',
                'description' => 'Hoàn tiền qua tài khoản ngân hàng (3-7 ngày) hoặc đổi sản phẩm mới và giao lại cho bạn.'
            ]
        ],
        'refund_methods' => [
            'Chuyển khoản ngân hàng: 3-7 ngày làm việc',
            'Ví điện tử (Momo, ZaloPay): 1-3 ngày làm việc',
            'Hoàn tiền mặt tại cửa hàng: Ngay lập tức'
        ],
        'contact' => [
            'hotline' => '(+84) 0239 482 958',
            'email' => 'qkhanh12.duration060@passinbox.com',
            'working_hours' => 'Thứ Hai - Thứ Sáu: 8:00 - 17:00'
        ]
    ];
    
    // Chính sách bảo hành
    $warrantyPolicy = [
        'title' => 'Chính sách bảo hành',
        'summary' => 'Tất cả sản phẩm BookZone đều được bảo hành chuyên nghiệp với thời gian phù hợp loại sản phẩm.',
        'highlights' => [
            'Đa dạng 7-12 tháng tùy loại sản phẩm',
            'Sửa chữa chuyên nghiệp tại trung tâm',
            'Xử lý nhanh chóng 5-7 ngày',
            'Miễn phí sửa chữa cho lỗi do nhà sản xuất'
        ],
        'warranty_periods' => [
            [
                'product_type' => 'Sách & Văn phòng phẩm thông thường',
                'period' => '7 ngày',
                'description' => 'Đổi trả nếu có lỗi in ấn, thiếu trang, rách'
            ],
            [
                'product_type' => 'Sản phẩm điện tử (máy tính, đồng hồ)',
                'period' => '12 tháng',
                'description' => 'Bảo hành chính hãng, sửa chữa hoặc thay thế linh kiện'
            ],
            [
                'product_type' => 'Dụng cụ học tập cao cấp',
                'period' => '6 tháng',
                'description' => 'Bảo hành về chất lượng sản phẩm'
            ],
            [
                'product_type' => 'Quà tặng, phụ kiện',
                'period' => '3 tháng',
                'description' => 'Bảo hành lỗi kỹ thuật'
            ]
        ],
        'warranty_coverage' => [
            [
                'title' => 'Được bảo hành',
                'items' => [
                    'Lỗi do nhà sản xuất',
                    'Hư hỏng trong quá trình sử dụng bình thường',
                    'Lỗi kỹ thuật của sản phẩm',
                    'Linh kiện hỏng trong thời gian bảo hành'
                ]
            ],
            [
                'title' => 'Không được bảo hành',
                'items' => [
                    'Sản phẩm hết thời hạn bảo hành',
                    'Hư hỏng do sử dụng sai cách, tai nạn',
                    'Sản phẩm đã qua sửa chữa tại nơi không ủy quyền',
                    'Tem bảo hành bị rách, mất hoặc sửa đổi',
                    'Hư hỏng do thiên tai, hỏa hoạn'
                ]
            ]
        ],
        'warranty_process' => [
            [
                'step' => 1,
                'title' => 'Liên hệ yêu cầu bảo hành',
                'description' => 'Gọi hotline hoặc mang sản phẩm đến cửa hàng với hóa đơn và phiếu bảo hành.'
            ],
            [
                'step' => 2,
                'title' => 'Kiểm tra sản phẩm',
                'description' => 'Nhân viên kiểm tra tình trạng và xác nhận bảo hành. Nếu đủ điều kiện, sản phẩm sẽ được gửi đến trung tâm bảo hành.'
            ],
            [
                'step' => 3,
                'title' => 'Sửa chữa hoặc thay thế',
                'description' => 'Trung tâm bảo hành tiến hành sửa chữa hoặc thay thế sản phẩm/linh kiện lỗi. Thời gian: 5-7 ngày làm việc.'
            ],
            [
                'step' => 4,
                'title' => 'Nhận sản phẩm',
                'description' => 'BookZone thông báo khi sản phẩm đã sửa xong. Bạn có thể nhận tại cửa hàng hoặc yêu cầu giao hàng (miễn phí nếu lỗi NSX).'
            ]
        ],
        'important_notes' => [
            'Bảo quản phiếu bảo hành và hóa đơn mua hàng',
            'Không tự ý tháo rời hoặc sửa chữa sản phẩm',
            'Liên hệ ngay khi phát hiện lỗi để được hỗ trợ kịp thời',
            'Thời gian bảo hành được tính từ ngày mua hàng ghi trên hóa đơn'
        ],
        'contact' => [
            'hotline' => '(+84) 0239 482 958',
            'email' => 'qkhanh12.duration060@passinbox.com',
            'address' => '180 Cao Lỗ, Quận 8, TP.HCM'
        ]
    ];
    
    // Chính sách vận chuyển & giao hàng
    $shippingPolicy = [
        'title' => 'Vận chuyển & Giao hàng',
        'summary' => 'Hợp tác với các đối tác vận chuyển uy tín, đảm bảo sản phẩm đến tay bạn nhanh chóng và an toàn.',
        'highlights' => [
            'Giao nhanh 1-2 ngày',
            'Giao hàng toàn quốc',
            'Miễn phí vận chuyển cho đơn hàng trên 500,000đ',
            'Theo dõi đơn hàng realtime'
        ],
        'shipping_methods' => [
            [
                'method' => 'Giao nhanh',
                'time' => '1-2 ngày',
                'fee' => '30,000đ - 50,000đ',
                'description' => 'Áp dụng cho khu vực nội thành TP.HCM và các thành phố lớn. Giao hàng trong ngày nếu đặt trước 14h.'
            ],
            [
                'method' => 'Giao tiêu chuẩn',
                'time' => '3-5 ngày',
                'fee' => '20,000đ - 35,000đ',
                'description' => 'Áp dụng cho tất cả các tỉnh thành trên toàn quốc. Thời gian có thể kéo dài hơn với vùng xa.'
            ],
            [
                'method' => 'Giao hàng hẹn giờ',
                'time' => 'Theo lịch hẹn',
                'fee' => '50,000đ - 80,000đ',
                'description' => 'Bạn chọn khung giờ nhận hàng cụ thể. Phù hợp khi bạn có lịch trình cố định.'
            ],
            [
                'method' => 'Nhận tại cửa hàng',
                'time' => 'Ngay sau khi đặt hàng',
                'fee' => 'Miễn phí',
                'description' => 'Đặt hàng online và đến nhận tại cửa hàng 180 Cao Lỗ, Quận 8, TP.HCM. Tiết kiệm thời gian và chi phí.'
            ]
        ],
        'shipping_fee_policy' => [
            'Miễn phí vận chuyển cho đơn hàng từ 500,000đ trở lên',
            'Phí vận chuyển được tính dựa trên: Trọng lượng, Kích thước gói hàng, Khoảng cách địa lý',
            'Phí vận chuyển sẽ được hiển thị rõ ràng trước khi bạn thanh toán',
            'Có thể áp dụng các chương trình freeship đặc biệt trong các dịp khuyến mãi'
        ],
        'shipping_partners' => [
            'Giao Hàng Nhanh (GHN)',
            'Giao Hàng Tiết Kiệm (GHTK)',
            'J&T Express',
            'Viettel Post',
            'VNPost'
        ],
        'delivery_process' => [
            [
                'step' => 1,
                'title' => 'Đặt hàng thành công',
                'description' => 'Bạn nhận email xác nhận đơn hàng với mã đơn hàng và thông tin chi tiết.'
            ],
            [
                'step' => 2,
                'title' => 'Xác nhận & đóng gói',
                'description' => 'BookZone xác nhận đơn hàng và bắt đầu đóng gói sản phẩm cẩn thận. Thời gian: 2-4 giờ trong giờ hành chính.'
            ],
            [
                'step' => 3,
                'title' => 'Giao cho đối tác vận chuyển',
                'description' => 'Đơn hàng được bàn giao cho đơn vị vận chuyển. Bạn nhận mã vận đơn để theo dõi.'
            ],
            [
                'step' => 4,
                'title' => 'Đang vận chuyển',
                'description' => 'Theo dõi hành trình của đơn hàng qua mã vận đơn trên website của đối tác vận chuyển.'
            ],
            [
                'step' => 5,
                'title' => 'Giao hàng thành công',
                'description' => 'Shipper liên hệ và giao hàng đến địa chỉ của bạn. Vui lòng kiểm tra sản phẩm trước khi nhận.'
            ]
        ],
        'order_tracking' => [
            'Đăng nhập vào tài khoản và xem "Đơn hàng của tôi"',
            'Sử dụng mã vận đơn để tra cứu trên website đối tác vận chuyển',
            'Nhận thông báo qua email/SMS khi đơn hàng thay đổi trạng thái',
            'Liên hệ hotline để được hỗ trợ tra cứu'
        ],
        'delivery_issues' => [
            [
                'issue' => 'Không có người nhận',
                'solution' => 'Shipper sẽ liên hệ và thử giao lại lần 2. Nếu vẫn không liên hệ được, đơn hàng sẽ được trả về kho.'
            ],
            [
                'issue' => 'Địa chỉ không chính xác',
                'solution' => 'Vui lòng liên hệ hotline ngay để cập nhật địa chỉ giao hàng mới.'
            ],
            [
                'issue' => 'Hàng bị hư hỏng khi nhận',
                'solution' => 'Từ chối nhận hàng và chụp ảnh bằng chứng. Liên hệ BookZone để được đổi hàng mới miễn phí.'
            ],
            [
                'issue' => 'Giao sai sản phẩm',
                'solution' => 'Liên hệ ngay với BookZone, chúng tôi sẽ đổi hàng đúng và chịu toàn bộ phí vận chuyển.'
            ]
        ],
        'important_notes' => [
            'Vui lòng cung cấp địa chỉ giao hàng chính xác và số điện thoại liên lạc được',
            'Kiểm tra kỹ sản phẩm trước khi nhận hàng và thanh toán',
            'Giữ lại hóa đơn và phiếu bảo hành (nếu có) để được hỗ trợ tốt nhất',
            'Thời gian giao hàng có thể thay đổi trong các dịp lễ, Tết hoặc thời tiết xấu'
        ],
        'contact' => [
            'hotline' => '(+84) 0239 482 958',
            'email' => 'qkhanh12.duration060@passinbox.com',
            'support_hours' => 'Thứ Hai - Chủ Nhật: 8:00 - 22:00'
        ]
    ];
    
    // Thông tin thanh toán
    $paymentInfo = [
        'payment_methods' => [
            [
                'method' => 'Tiền mặt (COD)',
                'description' => 'Thanh toán khi nhận hàng. Áp dụng cho tất cả đơn hàng.',
                'fee' => 'Miễn phí'
            ],
            [
                'method' => 'Chuyển khoản ngân hàng',
                'description' => 'Chuyển khoản trước, chúng tôi giao hàng sau khi xác nhận thanh toán.',
                'fee' => 'Miễn phí',
                'bank_info' => [
                    'bank_name' => 'Ngân hàng TMCP Á Châu (ACB)',
                    'account_number' => '123456789',
                    'account_name' => 'BOOKZONE',
                    'branch' => 'Chi nhánh TP.HCM'
                ]
            ],
            [
                'method' => 'Ví điện tử (Momo, ZaloPay)',
                'description' => 'Thanh toán nhanh chóng qua ví điện tử.',
                'fee' => 'Miễn phí'
            ],
            [
                'method' => 'Quẹt thẻ tại cửa hàng',
                'description' => 'Thanh toán bằng thẻ ATM/Credit khi nhận hàng tại cửa hàng.',
                'fee' => 'Miễn phí'
            ]
        ]
    ];
    
    // FAQs - Câu hỏi thường gặp
    $faqs = [
        [
            'category' => 'Đặt hàng',
            'questions' => [
                [
                    'question' => 'Làm thế nào để đặt hàng?',
                    'answer' => 'Bạn có thể đặt hàng trực tuyến qua website, gọi hotline (+84) 0239 482 958, hoặc đến trực tiếp cửa hàng tại 180 Cao Lỗ, Quận 8, TP.HCM.'
                ],
                [
                    'question' => 'Tôi có thể hủy hoặc thay đổi đơn hàng không?',
                    'answer' => 'Bạn có thể hủy hoặc thay đổi đơn hàng trong vòng 2 giờ sau khi đặt. Sau thời gian này, vui lòng liên hệ hotline để được hỗ trợ.'
                ],
                [
                    'question' => 'Tôi có cần tạo tài khoản để mua hàng không?',
                    'answer' => 'Không bắt buộc, nhưng tạo tài khoản giúp bạn theo dõi đơn hàng, lưu địa chỉ giao hàng và nhận ưu đãi đặc biệt.'
                ]
            ]
        ],
        [
            'category' => 'Thanh toán',
            'questions' => [
                [
                    'question' => 'BookZone hỗ trợ những hình thức thanh toán nào?',
                    'answer' => 'Chúng tôi chấp nhận: Tiền mặt (COD), Chuyển khoản ngân hàng, Ví điện tử (Momo, ZaloPay), Quẹt thẻ tại cửa hàng.'
                ],
                [
                    'question' => 'Thông tin thanh toán của tôi có an toàn không?',
                    'answer' => 'Hoàn toàn an toàn. Chúng tôi sử dụng mã hóa SSL 256-bit và không lưu trữ thông tin thẻ của bạn.'
                ],
                [
                    'question' => 'Tôi có thể thanh toán trước một phần không?',
                    'answer' => 'Hiện tại chúng tôi chưa hỗ trợ thanh toán trả góp hay thanh toán một phần. Vui lòng thanh toán toàn bộ giá trị đơn hàng.'
                ]
            ]
        ],
        [
            'category' => 'Vận chuyển',
            'questions' => [
                [
                    'question' => 'Thời gian giao hàng là bao lâu?',
                    'answer' => 'Giao nhanh: 1-2 ngày (nội thành), Giao tiêu chuẩn: 3-5 ngày (toàn quốc). Thời gian có thể thay đổi tùy khu vực.'
                ],
                [
                    'question' => 'Phí vận chuyển là bao nhiêu?',
                    'answer' => 'Miễn phí cho đơn hàng từ 500,000đ. Các đơn hàng khác: 20,000đ - 50,000đ tùy khu vực và hình thức giao hàng.'
                ],
                [
                    'question' => 'Làm sao để theo dõi đơn hàng?',
                    'answer' => 'Đăng nhập tài khoản và xem "Đơn hàng của tôi", hoặc sử dụng mã vận đơn để tra cứu trên website đối tác vận chuyển.'
                ]
            ]
        ],
        [
            'category' => 'Đổi trả & Bảo hành',
            'questions' => [
                [
                    'question' => 'Tôi có thể đổi trả sản phẩm không?',
                    'answer' => 'Có, trong vòng 30 ngày nếu sản phẩm lỗi, sai mẫu mã hoặc không đúng mô tả. Sản phẩm phải còn nguyên tem, chưa sử dụng.'
                ],
                [
                    'question' => 'Bảo hành sản phẩm như thế nào?',
                    'answer' => 'Tùy loại sản phẩm: Sách & VPP (7 ngày), Điện tử (12 tháng), Dụng cụ cao cấp (6 tháng), Quà tặng (3 tháng).'
                ],
                [
                    'question' => 'Chi phí đổi trả ai chịu?',
                    'answer' => 'BookZone chịu phí vận chuyển nếu lỗi do nhà sản xuất hoặc giao sai hàng. Các trường hợp khác, khách hàng chịu phí.'
                ]
            ]
        ],
        [
            'category' => 'Tài khoản',
            'questions' => [
                [
                    'question' => 'Làm sao để đăng ký tài khoản?',
                    'answer' => 'Click vào "Đăng ký" trên website, điền thông tin cá nhân và email. Xác nhận email để hoàn tất đăng ký.'
                ],
                [
                    'question' => 'Tôi quên mật khẩu, phải làm sao?',
                    'answer' => 'Click "Quên mật khẩu" trên trang đăng nhập, nhập email đăng ký. Chúng tôi sẽ gửi link đặt lại mật khẩu.'
                ],
                [
                    'question' => 'Làm sao để xóa tài khoản?',
                    'answer' => 'Liên hệ hotline (+84) 0239 482 958 hoặc email để yêu cầu xóa tài khoản. Chúng tôi sẽ xử lý trong vòng 3-5 ngày.'
                ]
            ]
        ]
    ];
    
    // Tổng hợp tất cả dữ liệu
    $response = [
        'success' => true,
        'generated_at' => date('Y-m-d H:i:s'),
        'data' => [
            'store_info' => $storeInfo,
            'privacy_policy' => $privacyPolicy,
            'return_policy' => $returnPolicy,
            'warranty_policy' => $warrantyPolicy,
            'shipping_policy' => $shippingPolicy,
            'payment_info' => $paymentInfo,
            'faqs' => $faqs
        ]
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Lỗi khi lấy dữ liệu thông tin nhà sách',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
