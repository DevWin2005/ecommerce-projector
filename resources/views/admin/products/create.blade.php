@extends('layouts.admin')

@section('content')
<div class="container my-4">
    <h2>Thêm Máy Chiếu Mới</h2>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary mb-3">Quay lại danh sách</a>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tên Máy Chiếu <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="VD: Máy chiếu Epson EB-X06" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label>Danh Mục <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-control" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Thương hiệu</label>
                    <input type="text" name="brand" class="form-control" placeholder="Epson, Wanbo, Sony..." value="{{ old('brand') }}">
                </div>
                <div class="form-group">
                    <label>Giá bán (VNĐ) <span class="text-danger">*</span></label>
                    <input type="number" name="price" class="form-control" placeholder="VD: 12500000" value="{{ old('price') }}" required>
                </div>
                <div class="form-group">
                    <label>Hình Ảnh Máy Chiếu</label>
                    <input type="file" name="image" class="form-control-file" accept="image/*">
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Độ Sáng (ANSI Lumens)</label>
                    <input type="number" name="brightness" class="form-control" placeholder="VD: 3600" value="{{ old('brightness') }}">
                </div>
                <div class="form-group">
                    <label>Độ Phân Giải</label>
                    <select name="resolution" class="form-control">
                        <option value="HD 720p" {{ old('resolution') == 'HD 720p' ? 'selected' : '' }}>HD 720p</option>
                        <option value="Full HD 1080p" {{ old('resolution', 'Full HD 1080p') == 'Full HD 1080p' ? 'selected' : '' }}>Full HD 1080p</option>
                        <option value="4K UHD" {{ old('resolution') == '4K UHD' ? 'selected' : '' }}>4K UHD</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Công Nghệ Chiếu</label>
                    <input type="text" name="display_tech" class="form-control" placeholder="DLP, 3LCD, Laser..." value="{{ old('display_tech') }}">
                </div>
                <div class="form-group">
                    <label>Loa Tích Hợp (JSON Spec)</label>
                    <input type="text" name="speaker" class="form-control" placeholder="VD: 2x 5W Harman Kardon" value="{{ old('speaker') }}">
                </div>
                <div class="form-group">
                    <label>Cổng Kết Nối (JSON Spec)</label>
                    <input type="text" name="ports" class="form-control" placeholder="VD: HDMI x2, USB, Audio 3.5mm" value="{{ old('ports') }}">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block my-3">Lưu Máy Chiếu</button>
    </form>
</div>
@endsection