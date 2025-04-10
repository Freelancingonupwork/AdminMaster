@extends('admin.adminLayout.master')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>CMS Add</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">CMS Add</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <form @if(!isset($cms) || empty($cms)) action="{{ url('admin/cms-create') }}" @else href="{{ url('admin/cms-update/'.$cms['slug']) }}" @endif method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">CMS Add</h3>
                            <div class="card-tools">
                                <a type="button" class="btn btn-secondary" href="{{ url('admin/cms-index') }}">
                                    <i class="fas fa-arrow-left"></i>
                                    Return to List
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="inputName">Title</label>
                                <input type="text" id="title" name="title" class="form-control" @if(isset($cms)) value="{{ $cms->title }}" @else value="{{ old('title') }}" @endif>
                            </div>
                            <div class="form-group">
                                <label for="inputName">Keywords</label>
                                <input type="text" id="keyword" name="keyword" class="form-control" @if(isset($cms)) value="{{ $cms->keyword }}" @else value="{{ old('keyword') }}" @endif>
                            </div>
                            <div class="form-group">
                                <label for="inputDescription">Project Description</label>
                                <textarea id="description" name="description" class="form-control" rows="10">@if(isset($cms)) {{ $cms->description }} @else {{ old('description') }} @endif</textarea>
                            </div>
                            <div class="form-group">
                                <label for="inputName">Image</label>
                                <input type="file" id="image" name="image" class="form-control">
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <div class="row">
                <div class="col-12 mb-2">
                    <input type="submit" @if(!isset($cms)) value="Create CMS" @else value="Update CMS" @endif class="btn btn-success float-right">
                </div>
            </div>
        </form>
    </section>
    <!-- /.content -->
</div>
@endsection

@section('scripts')

<script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#description'))
        .catch(error => {
            console.error(error);
        });
</script>

@endsection