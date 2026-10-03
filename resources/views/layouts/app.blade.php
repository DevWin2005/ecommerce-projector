<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJECTOR SHOP - Hệ Thống Máy Chiếu Chính Hãng & Rạp Phim Tại Gia</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 Pro/Free CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        :root {
            --primary-color: #0284c7;
            --primary-dark: #0369a1;
            --secondary-color: #f59e0b;
            --dark-bg: #0f172a;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Topbar Styling */
        .topbar {
            background-color: var(--dark-bg);
            color: #94a3b8;
            font-size: 13px;
            padding: 7px 0;
            border-bottom: 1px solid #1e293b;
        }
        .topbar a {
            color: #cbd5e1;
            text-decoration: none;
            transition: var(--transition-smooth);
        }
        .topbar a:hover {
            color: var(--secondary-color);
        }

        /* Main Navbar Sticky Header */
        .header-main {
            background-color: #ffffff;
            box-shadow: 0 4px 20px -2px rgba(0,0,0,0.06);
            position: sticky;
            top: 0;
            z-index: 1040;
        }
        
        .brand-logo {
            font-weight: 800;
            font-size: 22px;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #0284c7, #2563eb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        .brand-logo i {
            color: #f59e0b;
            -webkit-text-fill-color: initial;
        }

        /* Live Search Box & Dropdown */
        .search-container {
            position: relative;
            width: 100%;
            max-width: 520px;
        }
        .search-input {
            border-radius: 999px;
            padding-left: 20px;
            padding-right: 50px;
            border: 2px solid #e2e8f0;
            font-size: 14px;
            height: 46px;
            transition: var(--transition-smooth);
        }
        .search-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.15);
        }
        .search-btn {
            position: absolute;
            right: 5px;
            top: 4px;
            height: 38px;
            width: 38px;
            border-radius: 50%;
            background: var(--primary-color);
            color: #fff;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .search-btn:hover {
            background: var(--primary-dark);
        }

        .search-dropdown-results {
            position: absolute;
            top: 105%;
            left: 0;
            right: 0;
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 20px 30px rgba(0,0,0,0.15);
            z-index: 1050;
            overflow: hidden;
            display: none;
            border: 1px solid #e2e8f0;
        }
        .search-result-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            text-decoration: none;
            color: #1e293b;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.2s ease;
        }
        .search-result-item:hover {
            background: #f8fafc;
        }
        .search-result-item img {
            width: 45px;
            height: 45px;
            object-fit: contain;
            border-radius: 8px;
            background: #f1f5f9;
        }

        /* Nav Action Buttons */
        .btn-nav-action {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 12px;
            color: #334155;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: var(--transition-smooth);
            position: relative;
        }
        .btn-nav-action:hover {
            background-color: #f1f5f9;
            color: var(--primary-color);
        }
        .btn-nav-action .badge-count {
            background-color: #ef4444;
            color: #fff;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 7px;
        }

        /* Navigation Category Bar */
        .nav-category-bar {
            background-color: #ffffff;
            border-top: 1px solid #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
            font-weight: 600;
        }
        .nav-category-bar .nav-link {
            color: #475569;
            padding: 12px 16px;
            transition: var(--transition-smooth);
        }
        .nav-category-bar .nav-link:hover, .nav-category-bar .nav-link.active {
            color: var(--primary-color);
        }

        /* Card Styles */
        .cps-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #ffffff;
            transition: var(--transition-smooth);
            overflow: hidden;
        }
        .cps-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow);
            border-color: #cbd5e1;
        }
        .tag-discount {
            position: absolute;
            top: 12px;
            left: 12px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            z-index: 10;
        }
        .tag-installment {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #f59e0b;
            color: #0f172a;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            z-index: 10;
        }

        /* Button Action Gold/Primary */
        .btn-primary-custom {
            background: linear-gradient(135deg, #0284c7, #0284c7);
            color: #ffffff;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            transition: var(--transition-smooth);
        }
        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #0369a1, #0284c7);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
        }

        /* Toast Popup Floating */
        .toast-container-custom {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 1100;
        }

        /* Footer */
        footer {
            background-color: var(--dark-bg);
            color: #94a3b8;
            margin-top: auto;
            padding-top: 50px;
            font-size: 14px;
        }
        footer h6 {
            color: #f8fafc;
            font-weight: 700;
            margin-bottom: 20px;
        }
        footer a {
            color: #94a3b8;
            text-decoration: none;
            transition: var(--transition-smooth);
        }
        footer a:hover {
            color: #38bdf8;
        }
        /* Floating Chatbox Widget */
        .chatbox-widget {
            position: fixed;
            bottom: 25px;
            right: 25px;
            z-index: 1060;
        }
        .chatbox-toggle-btn {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0284c7, #2563eb);
            border: none;
            cursor: pointer;
            position: relative;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 25px rgba(2, 132, 199, 0.4);
        }
        .chatbox-toggle-btn:hover {
            transform: scale(1.1);
        }
        .chatbox-badge-dot {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 14px;
            height: 14px;
            background-color: #22c55e;
            border: 2px solid #ffffff;
            border-radius: 50%;
        }
        .chatbox-pulse-ring {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2px solid #0284c7;
            animation: chatPulse 2s infinite;
        }
        @keyframes chatPulse {
            0% { transform: scale(1); opacity: 0.8; }
            100% { transform: scale(1.5); opacity: 0; }
        }

        .chatbox-window {
            position: absolute;
            bottom: 70px;
            right: 0;
            width: 360px;
            border-radius: 20px;
            overflow: hidden;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            animation: chatSlideUp 0.3s ease-out forwards;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
        }
        @keyframes chatSlideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .chatbox-header {
            background: linear-gradient(135deg, #0284c7, #2563eb);
        }

        .chat-bubble {
            font-size: 13px;
            line-height: 1.5;
        }

        .chat-msg.user-msg {
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-end !important;
            width: 100% !important;
        }
        .chat-msg.user-msg .chat-bubble {
            background: #0284c7 !important;
            color: #ffffff !important;
            border-bottom-right-radius: 4px !important;
        }

        .chat-msg.bot-msg {
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-start !important;
            width: 100% !important;
        }
        .chat-msg.bot-msg .chat-bubble {
            border-bottom-left-radius: 4px !important;
        }

        .suggestion-chip {
            border-color: #cbd5e1;
            color: #334155;
            transition: all 0.2s ease;
            font-size: 12px;
        }
        .suggestion-chip:hover {
            background-color: #f0f7ff;
            border-color: #0284c7;
            color: #0284c7;
        }
    </style>
</head>
<body>

    <!-- TOPBAR -->
    <div class="topbar d-none d-md-block">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-4">
                <span><i class="fa-solid fa-headset text-warning me-1"></i> Hotline Hỗ Trợ: <strong>1900 6868</strong> (8h00 - 21h30)</span>
                <span><i class="fa-solid fa-location-dot text-danger me-1"></i> Showroom: Hà Nội & TP. Hồ Chí Minh</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('order.lookup') }}"><i class="fa-solid fa-truck-fast me-1"></i> Tra cứu đơn hàng</a>
                <span>|</span>
                <a href="{{ route('wishlist.index') }}"><i class="fa-solid fa-heart text-danger me-1"></i> Yêu thích</a>
                <span>|</span>
                <span class="text-success font-weight-bold"><i class="fa-solid fa-shield-halved me-1"></i> 100% Chính Hãng</span>
            </div>
        </div>
    </div>

    <!-- HEADER MAIN -->
    <header class="header-main py-3">
        <div class="container d-flex align-items-center justify-content-between gap-3">
            <!-- LOGO -->
            <a href="{{ route('home') }}" class="brand-logo">
                <i class="fa-solid fa-video fa-lg"></i>
                <span>PROJECTOR<span class="text-dark">SHOP</span></span>
            </a>

            <!-- SEARCH BAR WITH LIVE AUTOCOMPLETE -->
            <div class="search-container d-none d-lg-block">
                <form action="{{ route('home') }}" method="GET" class="position-relative">
                    <input type="text" 
                           name="search" 
                           id="headerSearchInput" 
                           class="form-control search-input" 
                           placeholder="Nhập tên máy chiếu (Epson, Wanbo, Beecube, 4K, Lumens...)"
                           value="{{ request('search') }}"
                           autocomplete="off">
                    <button type="submit" class="search-btn">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>

                <!-- Search Dropdown Results Container -->
                <div id="searchDropdownResults" class="search-dropdown-results">
                    <!-- Dynamic JS content -->
                </div>
            </div>

            <!-- ACTION BUTTONS (CART & USER PROFILE) -->
            <div class="d-flex align-items-center gap-2">

                <!-- Order Tracking icon for mobile -->
                <a href="{{ route('order.lookup') }}" class="btn-nav-action d-lg-none" title="Tra cứu đơn hàng">
                    <i class="fa-solid fa-magnifying-glass-location fa-lg text-primary"></i>
                </a>

                <!-- Cart Button -->
                @php
                    $cart = session()->get('cart', []);
                    $cartCount = array_sum(array_column($cart, 'quantity'));
                @endphp
                <a href="{{ route('cart.index') }}" class="btn-nav-action">
                    <i class="fa-solid fa-cart-shopping fa-lg text-primary"></i>
                    <span class="d-none d-md-inline">Giỏ Hàng</span>
                    <span class="badge-count" id="headerCartCount">{{ $cartCount }}</span>
                </a>

                <!-- User Account Dropdown -->
                @guest
                    <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill px-3 fw-bold btn-sm">
                        <i class="fa-regular fa-user me-1"></i> Đăng Nhập
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary-custom rounded-pill px-3 btn-sm d-none d-sm-inline-block">
                        Đăng Ký
                    </a>
                @else
                    <div class="dropdown">
                        <button class="btn btn-nav-action dropdown-toggle border-0" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-circle-user fa-xl text-primary"></i>
                            <span class="d-none d-md-inline font-weight-bold">{{ Auth::user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2">
                            <li class="dropdown-header font-weight-bold text-dark">
                                {{ Auth::user()->name }}
                                <br>
                                <span class="badge bg-secondary font-weight-normal mt-1">{{ Auth::user()->role === 'admin' ? 'Quản Quản Trị Viên' : 'Khách Hàng' }}</span>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('orders.my') }}">
                                    <i class="fa-solid fa-box-archive text-primary me-2"></i> Đơn hàng của tôi
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('wishlist.index') }}">
                                    <i class="fa-solid fa-heart text-danger me-2"></i> Sản phẩm đã thích
                                </a>
                            </li>
                            @if(Auth::user()->role === 'admin')
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item py-2 fw-bold text-info" href="{{ route('admin.dashboard') }}">
                                        <i class="fa-solid fa-gauge-high me-2"></i> Bảng Điều Khiển Admin
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 fw-bold text-primary" href="{{ route('admin.orders.index') }}">
                                        <i class="fa-solid fa-boxes-packing me-2"></i> Quản Lý Đơn Hàng
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 fw-bold text-dark" href="{{ route('admin.users.index') }}">
                                        <i class="fa-solid fa-users-gear me-2"></i> Quản Lý Người Dùng
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 fw-bold text-success" href="{{ route('admin.transactions.index') }}">
                                        <i class="fa-solid fa-receipt me-2"></i> Giao Dịch Thanh Toán
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 fw-bold text-danger" href="{{ route('admin.reports.index') }}">
                                        <i class="fa-solid fa-chart-line me-2"></i> Báo Cáo Tài Chính
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 fw-bold text-success" href="{{ route('admin.livechat.index') }}">
                                        <i class="fa-solid fa-headset me-2"></i> LiveChat CSKH
                                    </a>
                                </li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger">
                                        <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Đăng Xuất
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endguest
            </div>
        </div>
    </header>

    <!-- CATEGORY NAVIGATION BAR -->
    <nav class="nav-category-bar d-none d-md-block">
        <div class="container d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <a href="{{ route('home') }}" class="nav-link {{ !request('category') ? 'active' : '' }}">
                    <i class="fa-solid fa-house me-1"></i> Trang Chủ
                </a>

                @php
                    $navCategories = \App\Models\Category::all();
                @endphp
                @foreach($navCategories as $navCat)
                    <a href="{{ route('home', ['category' => $navCat->id]) }}" 
                       class="nav-link {{ request('category') == $navCat->id ? 'active' : '' }}">
                        {{ $navCat->name }}
                    </a>
                @endforeach
            </div>
            
            <div class="d-flex align-items-center gap-3 text-danger fw-bold small">
                <span><i class="fa-solid fa-bolt text-warning me-1"></i> HOT DEALS GIẢM ĐẾN 35%</span>
            </div>
        </div>
    </nav>

    <!-- ALERT MESSAGES CONTAINER -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2" role="alert">
                <i class="fa-solid fa-circle-check fa-lg text-success me-1"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2" role="alert">
                <i class="fa-solid fa-triangle-exclamation fa-lg text-danger me-1"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- MAIN CONTENT BODY -->
    <main class="container my-4 flex-grow-1">
        @yield('content')
    </main>

    @unless(request()->routeIs('cart.index'))
    <!-- FOOTER -->
    <footer>
        <div class="container pb-4">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <a href="{{ route('home') }}" class="brand-logo mb-3" style="-webkit-text-fill-color: initial; color: #fff;">
                        <i class="fa-solid fa-video text-warning me-2"></i> PROJECTOR SHOP
                    </a>
                    <p class="small text-secondary mb-3">
                        Hệ thống phân phối máy chiếu chính hãng hàng đầu Việt Nam. Chuyên cung cấp các dòng máy chiếu gia đình, rạp phim 4K, máy chiếu văn phòng hội trường và mini di động.
                    </p>
                    <div class="small">
                        <p class="mb-1"><i class="fa-solid fa-location-dot text-warning me-2"></i> 102 Nguyễn Trãi, Quận Thanh Xuân, Hà Nội</p>
                        <p class="mb-1"><i class="fa-solid fa-phone text-warning me-2"></i> Hotline: 1900 6868 - 0988.776.655</p>
                        <p class="mb-0"><i class="fa-solid fa-envelope text-warning me-2"></i> Email: hotro@projectorshop.vn</p>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h6>DANH MỤC SẢN PHẨM</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('home') }}">Máy Chiếu Mini Di Động</a></li>
                        <li class="mb-2"><a href="{{ route('home') }}">Máy Chiếu Gia Đình 4K</a></li>
                        <li class="mb-2"><a href="{{ route('home') }}">Máy Chiếu Văn Phòng</a></li>
                        <li class="mb-2"><a href="{{ route('home') }}">Máy Chiếu Siêu Gần UST</a></li>
                        <li class="mb-2"><a href="{{ route('home') }}">Màn Chiếu & Phụ Kiện</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6>CHÍNH SÁCH BẢO HÀNH</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> Bảo hành chính hãng 12 - 36 tháng</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> 1 đổi 1 trong 30 ngày nếu lỗi NSX</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> Giao hàng tận nơi toàn quốc</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> Hỗ trợ kỹ thuật & Lắp đặt tại nhà</li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6>THANH TOÁN LẠM THỨC</h6>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-secondary p-2"><i class="fa-solid fa-money-bill-wave me-1"></i> COD Tiền mặt</span>
                        <span class="badge bg-primary p-2"><i class="fa-solid fa-qrcode me-1"></i> Chuyển khoản VietQR</span>
                        <span class="badge bg-danger p-2"><i class="fa-solid fa-wallet me-1"></i> Ví MoMo / VNPay</span>
                    </div>
                    <h6 class="mt-4">KẾT NỐI VỚI CHÚNG TÔI</h6>
                    <div class="d-flex gap-3">
                        <a href="#" class="btn btn-outline-light btn-sm rounded-circle"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-outline-light btn-sm rounded-circle"><i class="fa-brands fa-youtube"></i></a>
                        <a href="#" class="btn btn-outline-light btn-sm rounded-circle"><i class="fa-brands fa-tiktok"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom text-center text-secondary">
            <div class="container">
                © 2026 PROJECTOR SHOP. Bản quyền thuộc về Hệ Thống Máy Chiếu Việt Nam.
            </div>
        </div>
    </footer>
    @endunless

    <!-- FLOATING CHATBOX WIDGET -->
    <div id="chatboxWidget" class="chatbox-widget">
        <!-- Chat Trigger Button -->
        <button id="chatboxToggleBtn" class="chatbox-toggle-btn shadow-lg" title="Trò chuyện với tư vấn viên">
            <i class="fa-solid fa-comments fa-xl text-white"></i>
            <span class="chatbox-badge-dot"></span>
            <span class="chatbox-pulse-ring"></span>
        </button>

        <!-- Chat Popup Window -->
        <div id="chatboxWindow" class="chatbox-window shadow-lg d-none">
            <!-- Chat Header -->
            <div class="chatbox-header d-flex align-items-center justify-content-between p-3 text-white">
                <div class="d-flex align-items-center gap-2">
                    <div class="position-relative">
                        <div class="chatbox-avatar bg-white text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                            <i class="fa-solid fa-headset fa-lg"></i>
                        </div>
                        <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle p-1" style="width: 10px; height: 10px;"></span>
                    </div>
                    <div>
                        <strong class="d-block text-white leading-tight" style="font-size: 14px;">Tư Vấn PROJECTOR SHOP</strong>
                        <small class="text-white-50" style="font-size: 11px;"><i class="fa-solid fa-circle text-success me-1" style="font-size: 8px;"></i> Sẵn sàng hỗ trợ 24/7</small>
                    </div>
                </div>
                <button id="chatboxCloseBtn" class="btn btn-sm btn-link text-white p-0 border-0 fs-5" title="Đóng chat">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            @if(Auth::check() && Auth::user()->role === 'admin')
                <!-- ADMIN QUICK CUSTOMER SELECTOR BAR IN POPUP CHATBOX -->
                <div class="bg-dark p-2 text-white border-bottom d-flex align-items-center gap-2" style="font-size: 12px;">
                    <i class="fa-solid fa-user-gear text-warning"></i>
                    <span class="fw-bold text-nowrap">Admin nhắn tới:</span>
                    <select id="chatboxAdminSelectUser" class="form-select form-select-sm bg-white text-dark py-0" style="font-size: 11.5px; height: 28px;">
                        <option value="">-- Chọn khách hàng để nhắn tin --</option>
                        @php
                            $chatCustomers = \App\Models\User::where('role', 'user')->latest()->take(30)->get();
                        @endphp
                        @foreach($chatCustomers as $cUser)
                            <option value="{{ $cUser->id }}">{{ $cUser->name }} ({{ $cUser->email }})</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Chat Body Messages -->
            <div id="chatboxMessages" class="chatbox-messages p-3 overflow-y-auto" style="height: 330px; background-color: #f8fafc;">
                <!-- Welcome Bot Message -->
                <div class="chat-msg bot-msg mb-3">
                    <div class="chat-bubble bg-white text-dark p-3 rounded-4 shadow-sm border small" style="max-width: 85%;">
                        👋 Xin chào! Chào mừng bạn đến với <strong>PROJECTOR SHOP</strong>.<br>
                        Tôi có thể tư vấn chọn máy chiếu, cước vận chuyển hoặc hỗ trợ đơn hàng gì cho bạn hôm nay?
                    </div>
                    <small class="text-muted d-block mt-1 ms-1" style="font-size: 10px;">Vừa xong</small>
                </div>

                <!-- Quick Suggestion Chips -->
                <div class="chat-suggestions d-flex flex-column gap-2 mb-3">
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill text-start fw-medium small suggestion-chip" data-text="Tư vấn máy chiếu gia đình xem phim 4K">
                        🎬 Tư vấn máy chiếu gia đình xem phim
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill text-start fw-medium small suggestion-chip" data-text="Cách tra cứu phí vận chuyển GHN">
                        🚚 Kiểm tra phí vận chuyển & Giao hàng
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill text-start fw-medium small suggestion-chip" data-text="Chính sách bảo hành & 1 đổi 1 thế nào?">
                        🛡️ Chính sách bảo hành & 1 đổi 1
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill text-start fw-medium small suggestion-chip" data-text="Số tổng đài hotline tư vấn trực tiếp">
                        📞 Hotline CSKH hỗ trợ trực tiếp
                    </button>
                </div>
            </div>

            <!-- Chat Footer Input -->
            <div class="chatbox-footer p-2 bg-white border-top d-flex align-items-center gap-2">
                <input type="text" id="chatboxInput" class="form-control form-control-sm rounded-pill border-0 bg-light px-3" placeholder="Nhập câu hỏi của bạn..." autocomplete="off">
                <button id="chatboxSendBtn" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px;">
                    <i class="fa-solid fa-paper-plane fa-sm"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toastNotification" class="toast-container-custom"></div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Global Toast & AJAX Cart / Wishlist Handlers -->
    <script>
    function showToast(message, type = 'success') {
        const toastContainer = document.getElementById('toastNotification');
        if (!toastContainer) return;

        const bgClass = type === 'success' ? 'bg-success' : (type === 'warning' ? 'bg-warning text-dark' : 'bg-danger');
        const icon = type === 'success' ? 'fa-circle-check' : (type === 'warning' ? 'fa-triangle-exclamation' : 'fa-circle-xmark');
        
        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center text-white ${bgClass} border-0 show shadow-lg mb-2`;
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');
        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body fw-bold d-flex align-items-center gap-2">
                    <i class="fa-solid ${icon} fa-lg"></i>
                    <span>${message}</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        toastContainer.appendChild(toastEl);
        setTimeout(() => {
            toastEl.classList.remove('show');
            setTimeout(() => toastEl.remove(), 400);
        }, 3500);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Global Event Delegation cho tất cả nút Add-To-Cart và Wishlist trên Toàn bộ trang web!
        document.addEventListener('click', function (e) {

            // 1. CLICK NÚT THÊM VÀO GIỎ HÀNG (btn-ajax-add-cart)
            const addCartBtn = e.target.closest('.btn-ajax-add-cart');
            if (addCartBtn) {
                e.preventDefault();
                const url = addCartBtn.getAttribute('data-url');
                if (!url) return;

                const originalHtml = addCartBtn.innerHTML;
                addCartBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Đang thêm...';
                addCartBtn.disabled = true;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    addCartBtn.innerHTML = originalHtml;
                    addCartBtn.disabled = false;

                    if (data && data.success) {
                        const cartCountBadge = document.getElementById('headerCartCount');
                        if (cartCountBadge && data.cartCount !== undefined) {
                            cartCountBadge.innerText = data.cartCount;
                        }
                        showToast(data.message, 'success');
                    } else if (data && data.message) {
                        showToast(data.message, 'danger');
                    }
                })
                .catch(err => {
                    addCartBtn.innerHTML = originalHtml;
                    addCartBtn.disabled = false;
                    console.error('Lỗi giỏ hàng:', err);
                });
                return;
            }

            // 2. CLICK NÚT YÊU THÍCH WISHLIST (btn-ajax-wishlist)
            const wishlistBtn = e.target.closest('.btn-ajax-wishlist');
            if (wishlistBtn) {
                e.preventDefault();
                const url = wishlistBtn.getAttribute('data-url');
                if (!url) return;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => {
                    if (res.status === 401 || res.redirected) {
                        showToast('Vui lòng đăng nhập để lưu sản phẩm yêu thích!', 'warning');
                        setTimeout(() => { window.location.href = "{{ route('login') }}"; }, 1200);
                        return null;
                    }
                    return res.json();
                })
                .then(data => {
                    if (data && data.success) {
                        if (data.added) {
                            wishlistBtn.classList.add('active', 'text-danger');
                        } else {
                            wishlistBtn.classList.remove('active', 'text-danger');
                        }
                        showToast(data.message, 'success');
                    }
                })
                .catch(err => console.error('Lỗi wishlist:', err));
                return;
            }
        });

        // Search Autocomplete Header
        const searchInput = document.getElementById('headerSearchInput');
        const searchDropdown = document.getElementById('searchDropdownResults');

        if (searchInput && searchDropdown) {
            let timeout = null;
            searchInput.addEventListener('input', function () {
                clearTimeout(timeout);
                const q = this.value.trim();

                if (q.length < 2) {
                    searchDropdown.style.display = 'none';
                    return;
                }

                timeout = setTimeout(() => {
                    fetch(`{{ route('api.products.search') }}?q=${encodeURIComponent(q)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data && data.length > 0) {
                                let html = '';
                                data.forEach(prod => {
                                    const img = prod.image ? (prod.image.startsWith('http') ? prod.image : `/storage/${prod.image}`) : 'https://via.placeholder.com/50';
                                    const priceStr = new Intl.NumberFormat('vi-VN').format(prod.sale_price || prod.price) + ' đ';
                                    html += `
                                        <a href="/products/${prod.id}" class="search-result-item">
                                            <img src="${img}" alt="${prod.name}">
                                            <div>
                                                <div class="fw-bold small text-truncate" style="max-width: 320px;">${prod.name}</div>
                                                <div class="text-danger fw-bold small">${priceStr}</div>
                                            </div>
                                        </a>
                                    `;
                                });
                                searchDropdown.innerHTML = html;
                                searchDropdown.style.display = 'block';
                            } else {
                                searchDropdown.innerHTML = '<div class="p-3 text-center text-muted small">Không tìm thấy máy chiếu phù hợp.</div>';
                                searchDropdown.style.display = 'block';
                            }
                        });
                }, 250);
            });

            document.addEventListener('click', function (e) {
                if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                    searchDropdown.style.display = 'none';
                }
            });
        }

        // FLOATING CHATBOX REAL-TIME LIVECHAT LOGIC
        const chatboxToggleBtn = document.getElementById('chatboxToggleBtn');
        const chatboxCloseBtn = document.getElementById('chatboxCloseBtn');
        const chatboxWindow = document.getElementById('chatboxWindow');
        const chatboxMessages = document.getElementById('chatboxMessages');
        const chatboxInput = document.getElementById('chatboxInput');
        const chatboxSendBtn = document.getElementById('chatboxSendBtn');
        const suggestionChips = document.querySelectorAll('.suggestion-chip');

        const isUserAuth = @json(Auth::check());
        let lastUserMsgId = 0;

        if (chatboxToggleBtn && chatboxWindow) {
            chatboxToggleBtn.addEventListener('click', function () {
                chatboxWindow.classList.toggle('d-none');
                if (!chatboxWindow.classList.contains('d-none')) {
                    if (chatboxInput) chatboxInput.focus();
                    if (chatboxMessages) chatboxMessages.scrollTop = chatboxMessages.scrollHeight;
                }
            });

            if (chatboxCloseBtn) {
                chatboxCloseBtn.addEventListener('click', function () {
                    chatboxWindow.classList.add('d-none');
                });
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function renderUserBubble(text, time = 'Vừa xong', msgId = null) {
                const attrId = msgId ? `data-msg-id="${msgId}"` : '';
                const msgHtml = `
                    <div class="chat-msg user-msg mb-3 w-100" ${attrId}>
                        <div class="d-flex align-items-center mb-1">
                            <span class="badge bg-primary text-white px-2 py-1"><i class="fa-solid fa-user me-1"></i>Bạn</span>
                        </div>
                        <div class="chat-bubble p-3 rounded-3 shadow-sm border small" style="max-width: 85%; word-break: break-word;">
                            ${escapeHtml(text)}
                        </div>
                        <small class="text-muted d-block mt-1 me-1" style="font-size: 10px;">${time}</small>
                    </div>
                `;
                chatboxMessages.insertAdjacentHTML('beforeend', msgHtml);
                chatboxMessages.scrollTop = chatboxMessages.scrollHeight;
            }

            function renderAdminBubble(text, time = 'Vừa xong') {
                const msgHtml = `
                    <div class="chat-msg bot-msg mb-3 w-100">
                        <div class="d-flex align-items-center mb-1">
                            <span class="badge bg-success text-white px-2 py-1"><i class="fa-solid fa-headset me-1"></i>CSKH Admin</span>
                        </div>
                        <div class="chat-bubble bg-white text-dark p-3 rounded-3 shadow-sm border small" style="max-width: 85%; word-break: break-word;">
                            ${text}
                        </div>
                        <small class="text-muted d-block mt-1 ms-1" style="font-size: 10px;">${time}</small>
                    </div>
                `;
                chatboxMessages.insertAdjacentHTML('beforeend', msgHtml);
                chatboxMessages.scrollTop = chatboxMessages.scrollHeight;
            }

            const adminSelectUserEl = document.getElementById('chatboxAdminSelectUser');

            if (adminSelectUserEl) {
                adminSelectUserEl.addEventListener('change', function () {
                    lastUserMsgId = 0;
                    chatboxMessages.innerHTML = '';
                    if (this.value) {
                        const userName = this.options[this.selectedIndex].text;
                        chatboxMessages.innerHTML = `<div class="text-center py-2 text-primary small fw-bold"><i class="fa-solid fa-headset me-1"></i>Đang mở chat với: ${escapeHtml(userName)}</div>`;
                    }
                    pollUserMessages();
                });
            }

            // Polling tin nhắn từ server cho cả User đăng nhập và Khách vãng lai
            function pollUserMessages() {
                const selectedUserId = adminSelectUserEl ? adminSelectUserEl.value : null;
                let fetchUrl = `{{ route('livechat.messages') }}?after_id=${lastUserMsgId}`;

                if (selectedUserId) {
                    fetchUrl = `{{ url('admin/livechat/messages') }}/user_${selectedUserId}?after_id=${lastUserMsgId}`;
                }

                fetch(fetchUrl)
                    .then(res => {
                        if (!res.ok) return null;
                        return res.json();
                    })
                    .then(data => {
                        if (data && data.success && data.messages.length > 0) {
                            data.messages.forEach(msg => {
                                if (msg.sender_type === 'admin') {
                                    renderAdminBubble(escapeHtml(msg.message), msg.time);
                                } else {
                                    if (document.querySelectorAll(`[data-msg-id="${msg.id}"]`).length === 0) {
                                        renderUserBubble(msg.message, msg.time, msg.id);
                                    }
                                }
                                if (msg.id > lastUserMsgId) {
                                    lastUserMsgId = msg.id;
                                }
                            });
                        }
                    })
                    .catch(err => console.error('Lỗi LiveChat User:', err));
            }

            function handleSendMessage(text) {
                if (!text || text.trim() === '') return;
                const cleanText = text.trim();

                if (chatboxInput) chatboxInput.value = '';
                const currentCsrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const selectedUserId = adminSelectUserEl ? adminSelectUserEl.value : null;

                let sendUrl = "{{ route('livechat.send') }}";
                if (selectedUserId) {
                    sendUrl = `{{ url('admin/livechat/send') }}/user_${selectedUserId}`;
                }

                fetch(sendUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': currentCsrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: cleanText })
                })
                .then(res => {
                    if (res.status === 419) {
                        return { success: true, data: { id: Date.now(), sender_type: selectedUserId ? 'admin' : 'user', message: cleanText, time: 'Vừa xong' } };
                    }
                    return res.json();
                })
                .then(data => {
                    if (data && data.success && data.data) {
                        if (selectedUserId) {
                            renderAdminBubble(escapeHtml(data.data.message), data.data.time);
                        } else {
                            renderUserBubble(data.data.message, data.data.time, data.data.id);
                        }
                        if (data.data.id > lastUserMsgId) {
                            lastUserMsgId = data.data.id;
                        }
                    } else if (data && data.message && data.message !== 'CSRF token mismatch.') {
                        showToast(data.message, 'warning');
                    }
                })
                .catch(err => console.error('Lỗi gửi tin nhắn:', err));
            }

            if (chatboxSendBtn) {
                chatboxSendBtn.addEventListener('click', function () {
                    if (chatboxInput) handleSendMessage(chatboxInput.value);
                });
            }

            if (chatboxInput) {
                chatboxInput.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        handleSendMessage(this.value);
                    }
                });
            }

            suggestionChips.forEach(chip => {
                chip.addEventListener('click', function () {
                    const text = this.getAttribute('data-text');
                    handleSendMessage(text);
                });
            });

            // Khởi chạy Polling tự động liên tục cho tất cả người dùng (2.5s)
            pollUserMessages();
            setInterval(pollUserMessages, 2500);
        }
    });
    </script>
</body>
</html>
