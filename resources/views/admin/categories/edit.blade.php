@extends('layouts.admin')

@section('content')
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark font-weight-bold">
                    <h4>Chỉnh Sửa Danh Mục Máy Chiếu</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label class="font-weight-bold">Tên Danh Mục <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Quay lại danh sách</a>
                            <button type="submit" class="btn btn-warning font-weight-bold">Cập Nhật Danh Mục</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection