@extends('layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="fa-solid fa-heart text-danger me-2"></i> Danh Sách Sản Phẩm Yêu Thích</h3>
        <p class="text-muted small mb-0">Các mẫu máy chiếu bạn đã quan tâm và lưu lại.</p>
    </div>
    <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-bold small text-nowrap shadow-sm bg-white">
        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại Trang chủ
    </a>
</div>

@if($wishlists->count() > 0)
    <div class="row g-3">
        @foreach($wishlists as $w)
            @php $product = $w->product; @endphp
            @if($product)
                <div class="col-6 col-md-3">
                    <div class="cps-card h-100 p-3 position-relative d-flex flex-column">
                        <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST" class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-light border text-danger rounded-circle" title="Xóa khỏi yêu thích">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </form>

                        <a href="{{ route('products.show', $product->id) }}" class="product-img-box text-decoration-none">
                            <img src="{{ Str::startsWith($product->image, 'http') ? $product->image : asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                        </a>

                        <div class="pt-2 d-flex flex-column flex-grow-1">
                            <a href="{{ route('products.show', $product->id) }}" class="text-dark text-decoration-none fw-bold small text-truncate-2 mb-2" style="height: 38px;">
                                {{ $product->name }}
                            </a>
                            <div class="text-danger fw-bold fs-6 mb-3">
                                {{ number_format($product->sale_price ?? $product->price) }} đ
                            </div>

                            <button type="button" 
                                    class="btn btn-primary-custom btn-sm w-100 py-2 mt-auto btn-ajax-add-cart"
                                    data-url="{{ route('cart.add', $product->id) }}">
                                <i class="fa-solid fa-cart-plus me-1"></i> Thêm Giỏ Hàng
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@else
    <div class="bg-white p-5 rounded-4 text-center border shadow-sm my-4">
        <i class="fa-regular fa-heart fa-4x text-muted mb-3"></i>
        <h4 class="fw-bold text-dark">Danh sách yêu thích trống!</h4>
        <p class="text-muted small mb-4">Bạn chưa chọn lưu mẫu máy chiếu nào.</p>
        <a href="{{ route('home') }}" class="btn btn-primary-custom px-4 py-3 rounded-pill fw-bold">Khám Phá Sản Phẩm</a>
    </div>
@endif
@endsection
