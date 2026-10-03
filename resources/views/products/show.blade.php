@extends('layouts.app')

@section('content')
<!-- BREADCRUMB & BACK BUTTON -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <nav aria-label="breadcrumb" class="mb-0 flex-grow-1">
        <ol class="breadcrumb bg-white p-3 rounded-4 border shadow-sm small mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-house me-1"></i> Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('home', ['category' => $product->category_id]) }}" class="text-decoration-none text-muted">{{ $product->category->name ?? 'Máy chiếu' }}</a></li>
            <li class="breadcrumb-item active text-truncate fw-bold text-dark" style="max-width: 300px;" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>
    <a href="javascript:history.back()" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-bold small text-nowrap shadow-sm bg-white">
        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<div class="row g-4 mb-5">
    <!-- CỘT 1: HÌNH ẢNH SẢN PHẨM -->
    <div class="col-lg-5">
        <div class="bg-white p-4 rounded-4 border shadow-sm position-relative text-center">
            @if($product->sale_price && $product->price > $product->sale_price)
                <span class="tag-discount" style="top: 15px; left: 15px;">Giảm {{ $product->discount_percent }}%</span>
            @endif

            <div class="py-4" style="min-height: 320px; display: flex; align-items: center; justify-content: center;">
                @if($product->image)
                    <img src="{{ Str::startsWith($product->image, 'http') ? $product->image : asset('storage/' . $product->image) }}" 
                         alt="{{ $product->name }}" 
                         class="img-fluid rounded-3"
                         style="max-height: 320px; object-fit: contain;">
                @else
                    <div class="text-muted">Chưa có hình ảnh sản phẩm</div>
                @endif
            </div>

            <div class="p-3 bg-light rounded-3 mt-3 d-flex justify-content-around text-center small">
                <div><i class="fa-solid fa-shield-halved text-success fa-lg mb-1 d-block"></i> Bảo hành {{ $product->warranty ?? '24T' }}</div>
                <div><i class="fa-solid fa-truck-fast text-primary fa-lg mb-1 d-block"></i> Giao hàng 2H</div>
                <div><i class="fa-solid fa-rotate-left text-warning fa-lg mb-1 d-block"></i> 1 đổi 1 30 ngày</div>
            </div>
        </div>
    </div>

    <!-- CỘT 2: THÔNG TIN VÀ ĐẶT HÀNG -->
    <div class="col-lg-7">
        <div class="bg-white p-4 rounded-4 border shadow-sm">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-primary px-2 py-1 fw-bold">{{ $product->brand ?? 'Máy chiếu chính hãng' }}</span>
                <span class="badge bg-success px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i> Còn hàng ({{ $product->stock }} SP)</span>
                <span class="text-muted small ms-auto">Mã SP: <strong>{{ $product->sku ?? 'PRJ-' . $product->id }}</strong></span>
            </div>

            <h3 class="fw-bold text-dark mb-3">{{ $product->name }}</h3>

            <!-- RATINGS -->
            <div class="d-flex align-items-center gap-2 mb-3 pb-3 border-bottom">
                <div class="text-warning fw-bold fs-5">
                    <i class="fa-solid fa-star"></i> {{ $product->average_rating }}
                </div>
                <span class="text-muted small">({{ $product->reviews->count() }} đánh giá từ khách hàng)</span>
            </div>

            <!-- GIÁ BÁN -->
            <div class="p-3 bg-light rounded-3 mb-4 d-flex align-items-baseline gap-3">
                <span class="text-danger fw-bold display-6">
                    {{ number_format($product->sale_price ?? $product->price) }} đ
                </span>
                @if($product->sale_price && $product->price > $product->sale_price)
                    <span class="text-muted text-decoration-line-through fs-5">
                        {{ number_format($product->price) }} đ
                    </span>
                    <span class="badge bg-danger">Tiết kiệm {{ number_format($product->price - $product->sale_price) }}đ</span>
                @endif
            </div>

            <!-- THÔNG SỐ NỔI BẬT KHỦNG -->
            <div class="row g-2 mb-4">
                <div class="col-6 col-md-3">
                    <div class="p-2 border rounded-3 bg-white text-center">
                        <small class="text-muted d-block mb-1">Độ sáng</small>
                        <strong class="text-dark">{{ $product->brightness ? $product->brightness . ' ANSI' : 'N/A' }}</strong>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-2 border rounded-3 bg-white text-center">
                        <small class="text-muted d-block mb-1">Độ phân giải</small>
                        <strong class="text-dark">{{ $product->resolution ?? 'Full HD' }}</strong>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-2 border rounded-3 bg-white text-center">
                        <small class="text-muted d-block mb-1">Công nghệ</small>
                        <strong class="text-dark">{{ $product->display_tech ?? 'LED Cinema' }}</strong>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-2 border rounded-3 bg-white text-center">
                        <small class="text-muted d-block mb-1">Hệ điều hành</small>
                        <strong class="text-dark">{{ $product->os ?? 'Android OS' }}</strong>
                    </div>
                </div>
            </div>

            <!-- MÔ TẢ NGẮN -->
            @if($product->short_description)
                <div class="mb-4">
                    <h6 class="fw-bold text-dark">Đặc điểm nổi bật:</h6>
                    <p class="text-secondary small mb-0">{{ $product->short_description }}</p>
                </div>
            @endif

            <!-- NÚT MUA HÀNG & THÊM GIỎ -->
            @if(!Auth::check() || Auth::user()->role !== 'admin')
                <div class="d-flex flex-wrap gap-3 pt-2">
                    <button type="button" 
                            class="btn btn-primary-custom flex-grow-1 py-3 px-4 rounded-3 fw-bold fs-6 btn-ajax-add-cart"
                            data-url="{{ route('cart.add', $product->id) }}">
                        <i class="fa-solid fa-cart-plus me-2"></i> THÊM VÀO GIỎ HÀNG
                    </button>

                    <button type="button" 
                            class="btn btn-danger py-3 px-4 rounded-3 fw-bold fs-6 btn-buy-now"
                            data-url="{{ route('cart.add', $product->id) }}">
                        <i class="fa-solid fa-bolt me-1"></i> MUA NGAY
                    </button>

                    <button type="button" 
                            class="btn btn-outline-secondary py-3 px-3 rounded-3 btn-ajax-wishlist {{ (!empty($userWishlisted) && $userWishlisted) ? 'text-danger border-danger' : '' }}"
                            data-url="{{ route('wishlist.toggle', $product->id) }}"
                            title="Lưu yêu thích">
                        <i class="fa-solid fa-heart fa-lg"></i>
                    </button>
                </div>
            @else
                <div class="alert alert-warning">
                    Tài khoản Quản trị viên Admin không thể mua hàng trực tiếp. <a href="{{ route('admin.products.edit', $product->id) }}" class="fw-bold">Bấm vào đây để chỉnh sửa thông tin sản phẩm.</a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- CHI TIẾT SẢN PHẨM & ĐÁNH GIÁ KHOẢNG RỘNG -->
<div class="row g-4 mb-5">
    <div class="col-lg-8">
        <!-- TAB MÔ TẢ CHI TIẾT -->
        <div class="bg-white p-4 rounded-4 border shadow-sm mb-4">
            <h5 class="fw-bold text-dark border-bottom pb-3 mb-4"><i class="fa-solid fa-file-lines text-primary me-2"></i> Chi Tiết Sản Phẩm</h5>
            <div class="text-dark lh-lg">
                {!! nl2br(e($product->description ?? 'Chưa có thông tin mô tả chi tiết cho sản phẩm này.')) !!}
            </div>
        </div>

        <!-- KHU VỰC ĐÁNH GIÁ & BÌNH LUẬN -->
        <div class="bg-white p-4 rounded-4 border shadow-sm">
            <h5 class="fw-bold text-dark border-bottom pb-3 mb-4"><i class="fa-solid fa-comments text-warning me-2"></i> Đánh Giá Từ Khách Hàng ({{ $product->reviews->count() }})</h5>

            <!-- FORM GỬI ĐÁNH GIÁ MỚI -->
            @auth
                <div class="p-3 bg-light rounded-3 mb-4">
                    <h6 class="fw-bold text-dark mb-2">Viết đánh giá của bạn:</h6>
                    <form action="{{ route('reviews.store', $product->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Chọn số sao đánh giá:</label>
                            <select name="rating" class="form-select form-select-sm" style="max-width: 200px;">
                                <option value="5" selected>⭐⭐⭐⭐⭐ (5 Sao - Tuyệt vời)</option>
                                <option value="4">⭐⭐⭐⭐ (4 Sao - Rất tốt)</option>
                                <option value="3">⭐⭐⭐ (3 Sao - Bình thường)</option>
                                <option value="2">⭐⭐ (2 Sao - Tạm được)</option>
                                <option value="1">⭐ (1 Sao - Tệ)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <textarea name="comment" class="form-control" rows="3" placeholder="Chia sẻ cảm nhận về chất lượng hình ảnh, độ sáng, dịch vụ..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary-custom btn-sm px-4 fw-bold">Gửi Đánh Giá</button>
                    </form>
                </div>
            @else
                <div class="alert alert-info small mb-4">
                    Vui lòng <a href="{{ route('login') }}" class="fw-bold">Đăng nhập</a> để gửi đánh giá cho sản phẩm này.
                </div>
            @endauth

            <!-- DANH SÁCH BÌNH LUẬN -->
            <div class="list-group list-group-flush">
                @forelse($product->reviews as $rev)
                    <div class="list-group-item px-0 py-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="text-dark"><i class="fa-solid fa-user-circle me-1 text-primary"></i> {{ $rev->user->name ?? 'Khách hàng' }}</strong>
                            <span class="text-muted small">{{ $rev->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="text-warning small mb-2">
                            @for($i=1; $i<=5; $i++)
                                <i class="fa-{{ $i <= $rev->rating ? 'solid' : 'regular' }} fa-star"></i>
                            @endfor
                        </div>
                        <p class="text-secondary small mb-0">{{ $rev->comment }}</p>
                    </div>
                @empty
                    <div class="text-muted text-center py-4">Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá máy chiếu này!</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- BẢNG THÔNG SỐ KỸ THUẬT CHI TIẾT -->
    <div class="col-lg-4">
        <div class="bg-white p-4 rounded-4 border shadow-sm mb-4">
            <h6 class="fw-bold text-dark border-bottom pb-3 mb-3"><i class="fa-solid fa-list text-primary me-2"></i> Bảng Thông Số Kỹ Thuật</h6>
            <table class="table table-sm table-striped small mb-0">
                <tbody>
                    <tr>
                        <th class="w-50 text-secondary">Thương hiệu</th>
                        <td class="fw-bold text-dark">{{ $product->brand ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="text-secondary">Độ sáng</th>
                        <td class="fw-bold text-dark">{{ $product->brightness ? $product->brightness . ' ANSI Lumens' : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="text-secondary">Độ phân giải</th>
                        <td class="fw-bold text-dark">{{ $product->resolution ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="text-secondary">Công nghệ chiếu</th>
                        <td class="fw-bold text-dark">{{ $product->display_tech ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="text-secondary">Hệ điều hành</th>
                        <td class="fw-bold text-dark">{{ $product->os ?? 'Không' }}</td>
                    </tr>
                    <tr>
                        <th class="text-secondary">Bảo hành</th>
                        <td class="fw-bold text-dark">{{ $product->warranty ?? '24 Tháng' }}</td>
                    </tr>
                    @if(!empty($product->specifications))
                        @foreach($product->specifications as $key => $val)
                            <tr>
                                <th class="text-secondary text-capitalize">{{ $key }}</th>
                                <td class="fw-bold text-dark">{{ is_array($val) ? json_encode($val) : $val }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SẢN PHẨM TƯƠNG TỰ -->
@if(count($relatedProducts) > 0)
<div class="mb-5">
    <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-layer-group text-primary me-2"></i> Sản Phẩm Cùng Danh Mục</h5>
    <div class="row g-3">
        @foreach($relatedProducts as $rel)
            <div class="col-6 col-md-3">
                <div class="cps-card h-100 p-3 position-relative d-flex flex-column">
                    <a href="{{ route('products.show', $rel->id) }}" class="product-img-box text-decoration-none">
                        <img src="{{ Str::startsWith($rel->image, 'http') ? $rel->image : asset('storage/' . $rel->image) }}" alt="{{ $rel->name }}">
                    </a>
                    <div class="pt-2 d-flex flex-column flex-grow-1">
                        <a href="{{ route('products.show', $rel->id) }}" class="text-dark text-decoration-none fw-bold small text-truncate mb-2">
                            {{ $rel->name }}
                        </a>
                        <div class="text-danger fw-bold fs-6 mt-auto">
                            {{ number_format($rel->sale_price ?? $rel->price) }} đ
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- JAVASCRIPT AJAX & BUY NOW -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Nút Mua Ngay (Add to cart & redirect immediately to checkout)
    const buyNowBtn = document.querySelector('.btn-buy-now');
    if (buyNowBtn) {
        buyNowBtn.addEventListener('click', function () {
            const url = this.getAttribute('data-url');
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
                window.location.href = "{{ route('checkout.index') }}";
            });
        });
    }
});
</script>
@endsection