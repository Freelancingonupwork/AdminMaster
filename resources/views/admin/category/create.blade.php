@extends('admin.adminLayout.master')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Category</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">Category</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <form id="quickForm" @if(!isset($category) || empty($category)) action="{{ url('admin/category-create') }}" @else acion="{{ url('admin/category-update/'.$category['slug']) }}" @endif method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Add Category</h3>
                                <div class="card-tools">
                                    <a type="button" class="btn btn-secondary" href="{{ url('admin/category-index') }}">
                                        <i class="fas fa-arrow-left"></i>
                                        Return to List
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="inputName">Category Name</label>
                                    <input type="text" id="category_name" name="category_name" class="form-control" @if(isset($category)) value="{{ $category->category_name }}" @else value="{{ old('category_name') }}" @endif>
                                </div>
                                <div class="form-group">
                                    <label for="inputName">Category Level</label>
                                    <select class="form-control" name="parent_id" id="parent_id">
                                        <option value="0" @if(isset($category['parent_id']) && $category['parent_id']==0) selected="" @endif style="font-weight: bold;"> Main Category </option>
                                        @foreach($levels as $val)
                                        <option value="{{ $val->id }}" @if(isset($category['parent_id']) && $category['parent_id']==$val->id) selected="" @endif style="font-weight: bold;"> {{ $val->category_name }} </option>
                                        <!-- @foreach($val->subCategory as $subVal)
                                                <option value ="{{ $subVal->id }}" @if(isset($category['parent_id']) && $category['parent_id']==$subVal['id']) selected="" @endif disabled style="font-weight: bold;">&emsp; &raquo; <strong> {{ $subVal->category_name }} </strong></option>
                                            @endforeach -->
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="inputDescription">Category Description</label>
                                    <textarea id="description" name="description" class="form-control" rows="10">@if(isset($category)) {{ $category->description }} @else {{ old('description') }} @endif</textarea>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="status" name="status" @if(isset($category) && $category['status']==1) checked @endif>
                                    <label for="status">Enable</label>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-2">
                        <input type="submit" @if(!isset($category)) value="Save" @else value="Update" @endif class="btn btn-success float-right">
                    </div>
                </div>
            </form>
        </div>
    </section>
    <!-- /.content -->
</div>
@endsection

@section('scripts')

<script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>
<script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/plugins/jquery-validation/additional-methods.min.js') }}"></script>

<script>
    $(function() {
        $('#quickForm').validate({
            rules: {
                category_name: {
                    required: true,
                }
            },
            messages: {
                category_name: {
                    required: "Please Enter Category Name",
                }
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>
<script>
    ClassicEditor
        .create(document.querySelector('#description'))
        .catch(error => {
            console.error(error);
        });
</script>

@endsection
