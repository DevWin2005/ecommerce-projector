<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN PANEL - Projector Shop Quản Trị Hệ Thống</title>
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
            --admin-primary: #0284c7;
            --admin-dark: #0f172a;
            --admin-sidebar-bg: #1e293b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Header Navbar */
        .admin-header {
            background-color: var(--admin-dark);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .admin-brand {
            font-weight: 800;
            font-size: 20px;
            color: #ffffff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Navigation Links Bar */
        .admin-nav-bar {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }

        .admin-nav-link {
            color: #475569;
            font-weight: 600;
            font-size: 13.5px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 7px;
            text-decoration: none;
            transition: all 0.2s ease;
            border-bottom: 3px solid transparent;
        }

        .admin-nav-link:hover, .admin-nav-link.active {
            color: var(--admin-primary);
            border-bottom-color: var(--admin-primary);
            background-color: #f0f9ff;
        }
    </style>
</head>
<body>

    <!-- ADMIN HEADER -->
    <header class="admin-header py-2 text-white">
        <div class="container-fluid px-4 d-flex align-items-center justify-content-between">
            <a href="{{ route('admin.dashboard') }}" class="admin-brand">
                <i class="fa-solid fa-gauge-high text-warning"></i>
                <span>PROJECTOR<span class="text-info">ADMIN</span></span>
            </a>

            <div class="d-flex align-items-center gap-3">
                <div class="dropdown">
                    <button class="btn btn-dark dropdown-toggle rounded-pill px-3 border-secondary text-white fw-bold btn-sm d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-circle-user fa-lg text-info"></i>
                        <span>{{ Auth::user()->name ?? 'Admin' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2">
                        <li class="dropdown-header font-weight-bold text-dark">Tài Khoản Quản Trị</li>
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
            </div>
        </div>
    </header>

    <!-- ADMIN NAVIGATION BAR WITH ALL MODULE LINKS -->
    <nav class="admin-nav-bar d-none d-md-block">
        <div class="container-fluid px-4 d-flex align-items-center flex-wrap">
            <!-- Tổng quan -->
            <a href="{{ route('admin.dashboard') }}" 
               class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line text-primary"></i> Tổng Quan
            </a>

            <!-- Quản lý Đơn hàng -->
            <a href="{{ route('admin.orders.index') }}" 
               class="admin-nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                <i class="fa-solid fa-boxes-packing text-primary"></i> Quản Lý Đơn Hàng
            </a>

            <!-- Quản lý Sản phẩm -->
            <a href="{{ route('admin.products.index') }}" 
               class="admin-nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                <i class="fa-solid fa-video text-dark"></i> Quản Lý Sản Phẩm
            </a>

            <!-- Quản lý Danh mục -->
            <a href="{{ route('admin.categories.index') }}" 
               class="admin-nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group text-warning"></i> Quản Lý Danh Mục
            </a>

            <!-- Quản lý Người dùng -->
            <a href="{{ route('admin.users.index') }}" 
               class="admin-nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <i class="fa-solid fa-users-gear text-info"></i> Quản Lý Người Dùng
            </a>

            <!-- Giao dịch Thanh toán -->
            <a href="{{ route('admin.transactions.index') }}" 
               class="admin-nav-link {{ request()->routeIs('admin.transactions*') ? 'active' : '' }}">
                <i class="fa-solid fa-receipt text-success"></i> Giao Dịch Thanh Toán
            </a>

            <!-- Báo cáo Tài chính -->
            <a href="{{ route('admin.reports.index') }}" 
               class="admin-nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie text-danger"></i> Báo Cáo Tài Chính
            </a>

            <!-- LiveChat Hỗ Trợ -->
            <a href="{{ route('admin.livechat.index') }}" 
               class="admin-nav-link {{ request()->routeIs('admin.livechat*') ? 'active' : '' }}">
                <i class="fa-solid fa-headset text-success"></i> LiveChat CSKH
            </a>

            <!-- Mã Giảm giá -->
            <a href="{{ route('admin.coupons.index') }}" 
               class="admin-nav-link {{ request()->routeIs('admin.coupons*') ? 'active' : '' }}">
                <i class="fa-solid fa-ticket text-secondary"></i> Mã Giảm Giá
            </a>
        </div>
    </nav>

    <!-- ALERT NOTIFICATIONS -->
    <div class="container-fluid px-4 mt-3">
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

    <!-- MAIN BODY CONTENT -->
    <main class="container-fluid px-4 my-4 flex-grow-1">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="bg-dark text-white-50 text-center py-3 mt-auto small">
        © 2026 PROJECTOR SHOP ADMIN PANEL. Hệ thống quản trị máy chiếu chính hãng.
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
