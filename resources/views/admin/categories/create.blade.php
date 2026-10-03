@extends('layouts.admin')

@section('content')
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white font-weight-bold">
                    <h4>Thêm Danh Mục Máy Chiếu Mới</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.categories.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="font-weight-bold">Tên Danh Mục <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="VD: Máy Chiếu Mini, Máy Chiếu Văn Phòng..." value="{{ old('name') }}" required>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Quay lại danh sách</a>
                            <button type="submit" class="btn btn-success font-weight-bold">Lưu Danh Mục</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection