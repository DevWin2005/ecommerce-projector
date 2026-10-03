<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ECommerceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo Tài khoản Admin & User mẫu
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Quản Trị Viên High-Tech',
                'password' => Hash::make('123456'),
                'role' => 'admin',
                'verify' => now(),
                'email_verified_at' => now(),
            ]
        );

        $customer = User::updateOrCreate(
            ['email' => 'khachhang@gmail.com'],
            [
                'name' => 'Nguyễn Văn Nam',
                'password' => Hash::make('123456'),
                'role' => 'user',
                'verify' => now(),
                'email_verified_at' => now(),
            ]
        );

        // 2. Tạo Danh mục máy chiếu
        $catMini = Category::firstOrCreate(['name' => 'Máy Chiếu Mini Di Động']);
        $catHome = Category::firstOrCreate(['name' => 'Máy Chiếu Gia Đình 4K']);
        $catOffice = Category::firstOrCreate(['name' => 'Máy Chiếu Văn Phòng & Trường Học']);
        $catUST = Category::firstOrCreate(['name' => 'Máy Chiếu Siêu Gần (UST Laser)']);
        $catAccess = Category::firstOrCreate(['name' => 'Màn Chiếu & Phụ Kiện']);

        // 3. Tạo Danh sách Máy chiếu mẫu chuẩn thương mại điện tử
        $productsData = [
            [
                'category_id' => $catMini->id,
                'sku' => 'PRJ-WB-T6M',
                'name' => 'Máy Chiếu Mini Wanbo T6 Max 4K Auto Focus',
                'slug' => 'wanbo-t6-max-4k',
                'brand' => 'Wanbo',
                'price' => 4890000,
                'sale_price' => 4290000,
                'stock' => 25,
                'warranty' => '12 tháng',
                'brightness' => 550,
                'resolution' => 'Full HD 1080p (Hỗ trợ 4K)',
                'display_tech' => 'LCD Sealed Engine',
                'os' => 'Android TV 9.0',
                'image' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=800&q=80',
                'short_description' => 'Máy chiếu di động thông minh tích hợp Loa kép 5W, Lấy nét tự động Auto-Focus và Android TV bản quyền.',
                'description' => 'Wanbo T6 Max là dòng máy chiếu mini cao cấp bán chạy nhất hiện nay. Máy trang bị độ sáng 550 ANSI Lumens, chip xử lý Amlogic T972 mạnh mẽ, hỗ trợ lấy nét điện tử chính xác chỉ trong 2 giây. Hệ thống tản nhiệt khép kín chống bụi vượt trội.',
                'specifications' => [
                    'speaker' => '2 x 5W Stereo',
                    'ports' => '2 x USB, 1 x HDMI, 1 x AV, 1 x 3.5mm Headphone',
                    'connectivity' => 'Wifi 5G Dual-band, Bluetooth 5.0',
                    'weight' => '1.93 kg'
                ],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $catHome->id,
                'sku' => 'PRJ-BC-XT2',
                'name' => 'Máy Chiếu Gia Đình Beecube Xtreme II Rạp Phim Tại Gia',
                'slug' => 'beecube-xtreme-ii',
                'brand' => 'Beecube',
                'price' => 6990000,
                'sale_price' => 5990000,
                'stock' => 18,
                'warranty' => '24 tháng',
                'brightness' => 750,
                'resolution' => 'Native Full HD 1080p Ultra',
                'display_tech' => 'LED Direct Cinema',
                'os' => 'Beecube OS (Android 11)',
                'image' => 'https://images.unsplash.com/photo-1595769816263-9b910be24d5f?auto=format&fit=crop&w=800&q=80',
                'short_description' => 'Biến phòng khách thành rạp chiếu phim với màn hình lên tới 200 inch, công nghệ loa Soundbar sống động.',
                'description' => 'Beecube Xtreme II mang đến trải nghiệm điện ảnh đỉnh cao tại nhà với độ sáng 750 ANSI Lumens vượt trội trong phân khúc. Tích hợp sẵn ứng dụng YouTube 4K, Netflix, VTVgo, FPT Play không quảng cáo.',
                'specifications' => [
                    'speaker' => 'HiFi Soundbar 20W Premium',
                    'ports' => 'HDMI, USB 3.0, Audio Out, LAN RJ45',
                    'connectivity' => 'Wifi 6 High Speed, Bluetooth 5.2',
                    'weight' => '2.5 kg'
                ],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $catOffice->id,
                'sku' => 'PRJ-EPS-FH52',
                'name' => 'Máy Chiếu Văn Phòng Epson EB-FH52 3LCD 4000 Lumens',
                'slug' => 'epson-eb-fh52-3lcd',
                'brand' => 'Epson',
                'price' => 19500000,
                'sale_price' => 17990000,
                'stock' => 12,
                'warranty' => '24 tháng thân máy, 12 tháng bóng đèn',
                'brightness' => 4000,
                'resolution' => 'Full HD (1920x1080)',
                'display_tech' => '3LCD Technology',
                'os' => 'Không',
                'image' => 'https://images.unsplash.com/photo-1526738549149-8e07eca6c147?auto=format&fit=crop&w=800&q=80',
                'short_description' => 'Máy chiếu hội trường văn phòng siêu sáng 4000 Lumens, hoạt động bền bỉ trong không gian nhiều ánh sáng.',
                'description' => 'Epson EB-FH52 sở hữu công nghệ 3LCD cho màu sắc chân thực, sắc nét gấp 3 lần máy chiếu thông thường. Tích hợp kết nối không dây Wifi màn hình từ điện thoại, laptop cực kỳ tiện lợi cho họp hành.',
                'specifications' => [
                    'speaker' => '16W Monaural',
                    'ports' => '2 x HDMI, VGA, USB Type-A, USB Type-B',
                    'connectivity' => 'Wifi 802.11b/g/n tích hợp sẵn',
                    'weight' => '3.1 kg'
                ],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $catMini->id,
                'sku' => 'PRJ-VS-M1P',
                'name' => 'Máy Chiếu Di Động ViewSonic M1+ Harman Kardon Pin 6H',
                'slug' => 'viewsonic-m1-plus',
                'brand' => 'ViewSonic',
                'price' => 8990000,
                'sale_price' => 7890000,
                'stock' => 15,
                'warranty' => '24 tháng',
                'brightness' => 300,
                'resolution' => 'WVGA (Smart Pass-through Full HD)',
                'display_tech' => 'LED DLP',
                'os' => 'ViewSonic Smart OS',
                'image' => 'https://images.unsplash.com/photo-1461151304267-38535e780c79?auto=format&fit=crop&w=800&q=80',
                'short_description' => 'Chân đế thông minh xoay 360 độ, loa kép Harman Kardon cao cấp, tích hợp pin dùng 6 tiếng cắm trại.',
                'description' => 'ViewSonic M1+ là người bạn đồng hành hoàn hảo cho những chuyến du lịch cắm trại ngoài trời. Thiết kế nhỏ gọn vừa lòng bàn tay, ống kính tự động che chắn khi gập chân đế.',
                'specifications' => [
                    'speaker' => 'Dual 3W Harman Kardon',
                    'ports' => 'USB Type-C, HDMI, Micro SD, USB Type-A',
                    'connectivity' => 'Wifi, Bluetooth in/out',
                    'weight' => '0.75 kg'
                ],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $catHome->id,
                'sku' => 'PRJ-SNY-XW5',
                'name' => 'Máy Chiếu Cao Cấp Sony VPL-XW5000ES Native 4K Laser',
                'slug' => 'sony-vpl-xw5000es-4k-laser',
                'brand' => 'Sony',
                'price' => 145000000,
                'sale_price' => 129000000,
                'stock' => 5,
                'warranty' => '36 tháng chính hãng Sony Việt Nam',
                'brightness' => 2000,
                'resolution' => 'Native 4K HDR (4096x2160)',
                'display_tech' => 'Z-Phosphor Laser SXRD',
                'os' => 'Sony Professional Engine',
                'image' => 'https://images.unsplash.com/photo-1593784991095-a205069470b6?auto=format&fit=crop&w=800&q=80',
                'short_description' => 'Đỉnh cao máy chiếu Home Cinema cao cấp nhất với tấm nền Native 4K SXRD nhỏ nhất thế giới và nguồn sáng Laser 20,000 giờ.',
                'description' => 'Sony VPL-XW5000ES sở hữu bộ xử lý hình ảnh X1 Ultimate for projector tối ưu độ tương phản từng khung hình, mang đến màu sắc rực rỡ và độ sâu màu đen tuyệt đối như rạp chiếu IMAX.',
                'specifications' => [
                    'speaker' => 'Không tích hợp (Dùng hệ thống dàn âm thanh bên ngoài)',
                    'ports' => '2 x HDMI 2.0b (HDCP 2.3), RS-232C, LAN, IR In',
                    'connectivity' => 'Ethernet LAN, Control4/Crestron System Support',
                    'weight' => '13 kg'
                ],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $catMini->id,
                'sku' => 'PRJ-SS-FREE2',
                'name' => 'Máy Chiếu Samsung The Freestyle Gen 2 Xoay 180 độ',
                'slug' => 'samsung-the-freestyle-gen-2',
                'brand' => 'Samsung',
                'price' => 14990000,
                'sale_price' => 11990000,
                'stock' => 20,
                'warranty' => '24 tháng',
                'brightness' => 550,
                'resolution' => 'Full HD 1080p HDR10',
                'display_tech' => 'DLP Smart Engine',
                'os' => 'Tizen OS Samsung',
                'image' => 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?auto=format&fit=crop&w=800&q=80',
                'short_description' => 'Thiết kế hình trụ xoay 180 độ chiếu mọi góc tường trần nhà, tích hợp Samsung Gaming Hub không cần console.',
                'description' => 'Freestyle Thế Hệ 2 nâng tầm giải trí di động. Tự động căn chỉnh hình ảnh Auto Keystone và cân bằng màu sắc theo màu tường thông minh.',
                'specifications' => [
                    'speaker' => 'Loa vòm 360 độ 5W',
                    'ports' => 'Micro HDMI, USB-C Power Supply',
                    'connectivity' => 'Wifi 5, Bluetooth 5.2, Tap View Samsung',
                    'weight' => '0.8 kg'
                ],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $catUST->id,
                'sku' => 'PRJ-OPT-D2',
                'name' => 'Máy Chiếu Siêu Gần Optoma CinemaX D2 4K Laser UST',
                'slug' => 'optoma-cinemax-d2-4k-ust',
                'brand' => 'Optoma',
                'price' => 59000000,
                'sale_price' => 52500000,
                'stock' => 7,
                'warranty' => '24 tháng',
                'brightness' => 3000,
                'resolution' => 'True 4K UHD 8.3 triệu điểm ảnh',
                'display_tech' => 'DLP DuraCore Laser',
                'os' => 'Tùy chọn Android Dongle',
                'image' => 'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?auto=format&fit=crop&w=800&q=80',
                'short_description' => 'Đặt cách tường chỉ 30cm cho màn hình khổng lồ 120 inch, tần số quét 240Hz siêu mượt cho game thủ.',
                'description' => 'Optoma D2 mang rạp phim 4K UST cao cấp đến phòng khách gia đình mà không cần treo trần hay đi dây phức tạp. Độ sáng 3000 ANSI Lumens thách thức mọi ánh sáng phòng.',
                'specifications' => [
                    'speaker' => 'Soundbar tích hợp 20W',
                    'ports' => '3 x HDMI 2.0 (eARC), USB Power, Optical Audio Out',
                    'connectivity' => 'eARC cho dàn âm thanh Dolby Atmos',
                    'weight' => '8.4 kg'
                ],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $catAccess->id,
                'sku' => 'ACC-SCR-100E',
                'name' => 'Màn Chiếu Điện Treo Tường 100 Inch Tỷ Lệ 16:9 Điều Khiển Từ Xa',
                'slug' => 'man-chieu-dien-100-inch',
                'brand' => 'Dalite',
                'price' => 1800000,
                'sale_price' => 1450000,
                'stock' => 40,
                'warranty' => '12 tháng',
                'brightness' => null,
                'resolution' => 'Hỗ trợ 4K/Full HD',
                'display_tech' => 'Matte White Fabric Class A',
                'os' => 'Không',
                'image' => 'https://images.unsplash.com/photo-1574375927938-d5a98e8ffe85?auto=format&fit=crop&w=800&q=80',
                'short_description' => 'Màn chiếu điện tự động cuộn bằng remote không dây, chất liệu vải phản xạ ánh sáng siêu nét chống lóa.',
                'description' => 'Màn chiếu điện Dalite 100 inch chuẩn tỷ lệ 16:9 chuyên dụng cho xem phim gia đình và hội thảo. Động cơ điện vận hành êm ái, viền đen phản quang tăng độ tương phản.',
                'specifications' => [
                    'size' => '100 inch (2.21m x 1.25m)',
                    'control' => 'Remote RF không dây + Hộp điều khiển tường',
                    'gain' => '1.1 Gain góc nhìn rộng 160 độ'
                ],
                'is_featured' => false,
                'is_active' => true,
            ],
        ];

        foreach ($productsData as $pData) {
            Product::updateOrCreate(['sku' => $pData['sku']], $pData);
        }

        // 4. Tạo Mã Giảm Giá Coupon
        Coupon::updateOrCreate(
            ['code' => 'MAYCHIEU500K'],
            [
                'type' => 'fixed',
                'value' => 500000,
                'min_order_amount' => 3000000,
                'expires_at' => now()->addDays(60),
                'is_active' => true,
            ]
        );

        Coupon::updateOrCreate(
            ['code' => 'DISCOUNT10'],
            [
                'type' => 'percent',
                'value' => 10,
                'min_order_amount' => 1000000,
                'expires_at' => now()->addDays(90),
                'is_active' => true,
            ]
        );

        // 5. Đánh giá Mẫu (Reviews)
        $p1 = Product::where('sku', 'PRJ-WB-T6M')->first();
        if ($p1) {
            Review::firstOrCreate([
                'product_id' => $p1->id,
                'user_id' => $customer->id,
            ], [
                'rating' => 5,
                'comment' => 'Máy chiếu màu sắc đẹp kinh ngạc, xem phim 1080p ban đêm như ở rạp! Lấy nét tự động cực nhanh.',
                'is_approved' => true,
            ]);
        }

        $p2 = Product::where('sku', 'PRJ-BC-XT2')->first();
        if ($p2) {
            Review::firstOrCreate([
                'product_id' => $p2->id,
                'user_id' => $customer->id,
            ], [
                'rating' => 5,
                'comment' => 'Loa nghe rất hay, không cần cắm thêm loa ngoài. Đèn chiếu sáng nét ngay cả khi bật đèn tuýp mờ.',
                'is_approved' => true,
            ]);
        }

        // 6. Đơn hàng mẫu (Orders)
        if ($p1 && $customer) {
            $order = Order::firstOrCreate([
                'order_code' => 'ORD-982341'
            ], [
                'user_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => '0988776655',
                'shipping_address' => 'Số 15 Lê Văn Lương, Quận Thanh Xuân, Hà Nội',
                'payment_method' => 'bank_transfer',
                'payment_status' => 'paid',
                'status' => 'shipping',
                'subtotal' => $p1->sale_price ?? $p1->price,
                'discount_amount' => 0,
                'shipping_fee' => 0,
                'total_amount' => $p1->sale_price ?? $p1->price,
                'note' => 'Giao trong giờ hành chính giúp tôi.',
            ]);

            OrderItem::firstOrCreate([
                'order_id' => $order->id,
                'product_id' => $p1->id,
            ], [
                'product_name' => $p1->name,
                'product_image' => $p1->image,
                'price' => $p1->sale_price ?? $p1->price,
                'quantity' => 1,
                'subtotal' => $p1->sale_price ?? $p1->price,
            ]);
        }
    }
}
