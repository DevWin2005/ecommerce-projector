@extends('layouts.app')

@section('content')
<style>
    /* Hero Carousel Banner */
    .hero-banner {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 24px;
        color: #ffffff;
        padding: 40px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 30px rgba(0,0,0,0.15);
    }
    .hero-banner::after {
        content: '';
        position: absolute;
        right: -100px;
        bottom: -100px;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(2,132,199,0.3) 0%, rgba(0,0,0,0) 70%);
        border-radius: 50%;
    }

    /* Brand Pills Bar */
    .brand-pill {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        padding: 8px 18px;
        font-weight: 700;
        font-size: 13px;
        color: #334155;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .brand-pill:hover, .brand-pill.active {
        background: #0284c7;
        color: #ffffff;
        border-color: #0284c7;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
    }

    /* Filter Card Sidebar */
    .filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }

    /* Wishlist Heart Button */
    .btn-wishlist {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #cbd5e1;
        cursor: pointer;
        transition: all 0.2s ease;
        z-index: 15;
    }
    .btn-wishlist:hover, .btn-wishlist.active {
        color: #ef4444;
        background: #ffffff;
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.25);
    }

    /* Product Image Wrapper */
    .product-img-box {
        height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px;
        position: relative;
    }
    .product-img-box img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
        transition: transform 0.3s ease;
    }
    .cps-card:hover .product-img-box img {
        transform: scale(1.08);
    }

    .spec-badge {
        background-color: #f8fafc;
        border: 1px solid #f1f5f9;
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 6px;
        color: #475569;
    }
</style>

<!-- 1. HERO BANNER & SLIDER -->
<div class="hero-banner mb-4">
    <div class="row align-items-center">
        <div class="col-lg-7 mb-4 mb-lg-0">
            <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill mb-3">
                <i class="fa-solid fa-sparkles me-1"></i> BÁN CHẠY NHẤT 2026
            </span>
            <h1 class="fw-extrabold display-5 mb-3" style="font-weight: 800;">
                KHO MÁY CHIẾU 4K & RẠP PHIM TẠI GIA
            </h1>
            <p class="text-light opacity-75 lead fs-6 mb-4">
                Trải nghiệm điện ảnh đỉnh cao với màn hình lên tới 300 inch. Trả góp 0% lãi suất, giao hàng siêu tốc 2H toàn quốc.
            </p>
            <div class="d-flex flex-wrap gap-3">
                <a href="#productList" class="btn btn-primary-custom px-4 py-3 rounded-pill fw-bold">
                    <i class="fa-solid fa-cart-shopping me-2"></i> KHÁM PHÁ NGAY
                </a>
                <a href="{{ route('order.lookup') }}" class="btn btn-outline-light px-4 py-3 rounded-pill fw-bold">
                    <i class="fa-solid fa-truck-fast me-2"></i> TRA CỨU ĐƠN HÀNG
                </a>
            </div>
        </div>
        <div class="col-lg-5 text-center">
            <img src="https://images.unsplash.com/photo-1595769816263-9b910be24d5f?auto=format&fit=crop&w=800&q=80" 
                 alt="Projector Shop" 
                 class="img-fluid rounded-4 shadow-lg border border-secondary"
                 style="max-height: 280px; object-fit: cover;">
        </div>
    </div>
</div>

<!-- 2. KHỐI TIÊU CHÍ BÁN HÀNG (VALUE PROPOSITION) -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="p-3 bg-white rounded-4 border d-flex align-items-center gap-3 shadow-sm h-100">
            <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 fs-4">
                <i class="fa-solid fa-shield-check"></i>
            </div>
            <div>
                <div class="fw-bold small text-dark">Chính Hãng 100%</div>
                <div class="text-muted" style="font-size: 11px;">Đền x2 nếu phát hiện hàng giả</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="p-3 bg-white rounded-4 border d-flex align-items-center gap-3 shadow-sm h-100">
            <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 fs-4">
                <i class="fa-solid fa-truck-ramp-box"></i>
            </div>
            <div>
                <div class="fw-bold small text-dark">Miễn Phí Vận Chuyển</div>
                <div class="text-muted" style="font-size: 11px;">Toàn quốc cho đơn từ 1.000.000đ</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="p-3 bg-white rounded-4 border d-flex align-items-center gap-3 shadow-sm h-100">
            <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3 fs-4">
                <i class="fa-solid fa-rotate-left"></i>
            </div>
            <div>
                <div class="fw-bold small text-dark">1 Đổi 1 Trong 30 Ngày</div>
                <div class="text-muted" style="font-size: 11px;">Nếu có lỗi từ nhà sản xuất</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="p-3 bg-white rounded-4 border d-flex align-items-center gap-3 shadow-sm h-100">
            <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-3 fs-4">
                <i class="fa-solid fa-percent"></i>
            </div>
            <div>
                <div class="fw-bold small text-dark">Trả Góp 0% Lãi Suất</div>
                <div class="text-muted" style="font-size: 11px;">Thủ tục duyệt online 5 phút</div>
            </div>
        </div>
    </div>
</div>

<!-- 3. THANH THƯƠNG HIỆU NỔI BẬT (BRANDS BAR) -->
<div class="bg-white p-3 rounded-4 border shadow-sm mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="fw-bold small text-uppercase text-secondary me-3">
            <i class="fa-solid fa-award text-primary me-1"></i> Thương hiệu:
        </div>
        <div class="d-flex align-items-center flex-wrap gap-2">
            <a href="{{ route('home') }}" class="brand-pill {{ !request('brand') ? 'active' : '' }}">
                Tất cả
            </a>
            @foreach($brands as $b)
                <a href="{{ route('home', array_merge(request()->query(), ['brand' => $b])) }}" 
                   class="brand-pill {{ request('brand') == $b ? 'active' : '' }}">
                    {{ $b }}
                </a>
            @endforeach
        </div>
    </div>
</div>

<!-- 4. KHUNG BỘ LỌC VÀ DẠNG LƯỚI SẢN PHẨM -->
<div class="row g-4" id="productList">
    <!-- SIDEBAR BỘ LỌC NÂNG CAO -->
    <div class="col-lg-3">
        <div class="filter-card">
            <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center justify-content-between">
                <span><i class="fa-solid fa-filter text-primary me-2"></i> BỘ LỌC TÌM KIẾM</span>
                @if(request()->anyFilled(['category', 'brand', 'resolution', 'min_price', 'max_price', 'search']))
                    <a href="{{ route('home') }}" class="text-danger small text-decoration-none fw-normal">Reset</a>
                @endif
            </h6>

            <form action="{{ route('home') }}" method="GET">
                <!-- GIỮ LẠI CÁC QUERY HIỆN CÓ -->
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                <!-- 1. DANH MỤC -->
                <div class="mb-4">
                    <label class="form-label fw-bold small text-uppercase text-muted">Danh mục sản phẩm</label>
                    <div class="list-group list-group-flush small">
                        <a href="{{ route('home', request()->except('category')) }}" 
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 px-0 py-2 {{ !request('category') ? 'fw-bold text-primary' : 'text-dark' }}">
                            <span><i class="fa-solid fa-layer-group me-2 text-secondary"></i> Tất cả danh mục</span>
                            <span class="badge bg-light text-dark rounded-pill">{{ \App\Models\Product::count() }}</span>
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ route('home', array_merge(request()->query(), ['category' => $cat->id])) }}" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 px-0 py-2 {{ request('category') == $cat->id ? 'fw-bold text-primary' : 'text-dark' }}">
                                <span><i class="fa-solid fa-angle-right me-2 text-muted"></i> {{ $cat->name }}</span>
                                <span class="badge bg-light text-dark rounded-pill">{{ $cat->products_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- 2. ĐỘ PHÂN GIẢI -->
                <div class="mb-4">
                    <label class="form-label fw-bold small text-uppercase text-muted">Độ phân giải</label>
                    <select name="resolution" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Tất cả độ phân giải --</option>
                        <option value="1080p" {{ request('resolution') == '1080p' ? 'selected' : '' }}>Full HD 1080p</option>
                        <option value="4K" {{ request('resolution') == '4K' ? 'selected' : '' }}>4K UHD Cao Cấp</option>
                    </select>
                </div>

                <!-- 3. KHOẢNG GIÁ (PRICE RANGE) -->
                <div class="mb-4">
                    <label class="form-label fw-bold small text-uppercase text-muted">Khoảng giá (VND)</label>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Từ..." value="{{ request('min_price') }}">
                        <span>-</span>
                        <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Đến..." value="{{ request('max_price') }}">
                    </div>
                    <button type="submit" class="btn btn-outline-primary btn-sm w-100 fw-bold">Lọc Theo Giá</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MAIN PRODUCT LIST GRID -->
    <div class="col-lg-9">
        <!-- HEADER LỌC RÚT GỌN & SẮP XẾP -->
        <div class="bg-white p-3 rounded-4 border shadow-sm mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="fw-bold text-dark">
                Hiển thị <span class="text-primary">{{ $products->total() }}</span> máy chiếu
                @if(request('search'))
                    cho từ khóa "<span class="text-danger">{{ request('search') }}</span>"
                @endif
            </div>

            <!-- SẮP XẾP (SORTING) -->
            <div class="d-flex align-items-center gap-2">
                <span class="small text-muted font-weight-bold">Sắp xếp:</span>
                <select class="form-select form-select-sm" style="width: auto;" onchange="location = this.value;">
                    <option value="{{ route('home', array_merge(request()->query(), ['sort' => 'latest'])) }}" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>
                        Mới nhất
                    </option>
                    <option value="{{ route('home', array_merge(request()->query(), ['sort' => 'price_asc'])) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                        Giá: Thấp đến Cao
                    </option>
                    <option value="{{ route('home', array_merge(request()->query(), ['sort' => 'price_desc'])) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                        Giá: Cao đến Thấp
                    </option>
                    <option value="{{ route('home', array_merge(request()->query(), ['sort' => 'popular'])) }}" {{ request('sort') == 'popular' ? 'selected' : '' }}>
                        Nổi bật nhất
                    </option>
                </select>
            </div>
        </div>

        <!-- GRID SẢN PHẨM -->
        <div class="row g-3">
            @forelse($products as $product)
                @php
                    $isWishlisted = false;
                    if(Auth::check()) {
                        $isWishlisted = \App\Models\Wishlist::where('user_id', Auth::id())->where('product_id', $product->id)->exists();
                    }
                @endphp
                <div class="col-6 col-md-4">
                    <div class="cps-card h-100 p-3 position-relative d-flex flex-column">
                        <!-- TAGS -->
                        @if($product->sale_price && $product->price > $product->sale_price)
                            <span class="tag-discount">Giảm {{ $product->discount_percent }}%</span>
                        @endif
                        <span class="tag-installment">Trả góp 0%</span>

                        <!-- WISHLIST HEART BUTTON -->
                        <button type="button" 
                                class="btn-wishlist btn-ajax-wishlist {{ $isWishlisted ? 'active' : '' }}" 
                                data-url="{{ route('wishlist.toggle', $product->id) }}"
                                title="Yêu thích">
                            <i class="fa-solid fa-heart"></i>
                        </button>

                        <!-- PRODUCT IMAGE -->
                        <a href="{{ route('products.show', $product->id) }}" class="product-img-box text-decoration-none">
                            @if($product->image)
                                <img src="{{ Str::startsWith($product->image, 'http') ? $product->image : asset('storage/' . $product->image) }}" 
                                     alt="{{ $product->name }}">
                            @else
                                <div class="text-muted small">No Image</div>
                            @endif
                        </a>

                        <!-- PRODUCT CONTENT -->
                        <div class="pt-2 d-flex flex-column flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-light text-primary fw-bold px-2 py-1">
                                    {{ $product->brand ?? 'Chính hãng' }}
                                </span>
                                <span class="small text-warning fw-bold">
                                    <i class="fa-solid fa-star"></i> {{ $product->average_rating }}
                                </span>
                            </div>

                            <a href="{{ route('products.show', $product->id) }}" 
                               class="text-dark text-decoration-none fw-bold small text-truncate-2 mb-2" 
                               title="{{ $product->name }}"
                               style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 38px;">
                                {{ $product->name }}
                            </a>

                            <!-- PRICES -->
                            <div class="mb-2">
                                <span class="text-danger fw-bold fs-6 me-2">
                                    {{ number_format($product->sale_price ?? $product->price) }} đ
                                </span>
                                @if($product->sale_price && $product->price > $product->sale_price)
                                    <span class="text-muted small text-decoration-line-through">
                                        {{ number_format($product->price) }} đ
                                    </span>
                                @endif
                            </div>

                            <!-- QUICK SPECS -->
                            <div class="d-flex flex-wrap gap-1 mb-3">
                                @if($product->brightness)
                                    <span class="spec-badge"><i class="fa-solid fa-sun text-warning me-1"></i>{{ $product->brightness }} ANSI</span>
                                @endif
                                @if($product->resolution)
                                    <span class="spec-badge"><i class="fa-solid fa-tv text-info me-1"></i>{{ $product->resolution }}</span>
                                @endif
                            </div>

                            <!-- ADD TO CART BUTTON AJAX -->
                            @if(!Auth::check() || Auth::user()->role !== 'admin')
                                <button type="button" 
                                        class="btn btn-primary-custom btn-sm w-100 py-2 mt-auto btn-ajax-add-cart"
                                        data-url="{{ route('cart.add', $product->id) }}">
                                    <i class="fa-solid fa-cart-plus me-1"></i> Thêm Vào Giỏ
                                </button>
                            @else
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-outline-warning btn-sm w-100 mt-auto">
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Sửa (Admin)
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="bg-white p-5 rounded-4 text-center border shadow-sm">
                        <i class="fa-solid fa-box-open fa-3x text-muted mb-3"></i>
                        <h5 class="fw-bold text-dark">Không tìm thấy máy chiếu nào phù hợp!</h5>
                        <p class="text-muted small mb-3">Thử bỏ chọn bộ lọc hoặc tìm kiếm từ khóa khác.</p>
                        <a href="{{ route('home') }}" class="btn btn-primary-custom btn-sm px-4">Xem Toàn Bộ Máy Chiếu</a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- PAGINATION LINK -->
        <div class="mt-4 d-flex justify-content-center">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

@endsection