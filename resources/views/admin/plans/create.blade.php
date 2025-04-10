@extends('admin.adminLayout.master')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Plan Add</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">Plan Add</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <form id="quickForm" @if(!isset($plan) || empty($plan)) action="{{ url('admin/plan-create') }}" @else action="{{ url('admin/plan-update/'.$plan['slug'].'/'.$plan['id']) }}" @endif method="post" enctype="multipart/form-data">
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
                            <h3 class="card-title">Plan Add</h3>
                            <div class="card-tools">
                                <a type="button" class="btn btn-secondary" href="{{ url('admin/plan-index') }}">
                                    <i class="fas fa-arrow-left"></i>
                                    Return to List
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="inputName">Name</label>
                                <input type="text" id="name" name="name" class="form-control" @if(isset($plan)) value="{{ $plan->name }}" @else value="{{ old('name') }}" @endif>
                            </div>
                            <div class="form-group">
                                <label for="inputDescription">Description</label>
                                <textarea id="description" name="description" class="form-control" rows="10">@if(isset($plan)) {{ $plan->description }} @else {{ old('description') }} @endif</textarea>
                            </div>
                            <div class="form-group">
                                <label for="inputName">Price</label>
                                <input type="number" id="price" name="price" class="form-control" @if(isset($plan)) value="{{ $plan->price }}" @else value="{{ old('price') }}" @endif>
                            </div>

                            <div class="form-group">
                                <label for="inputName">Plan Duration</label>
                                <select class="form-control" name="plan_type" id="plan_type">
                                    <option value="">Select Plan Duration</option>
                                    <option value="0" @if(isset($plan) && $plan['plan_type'] == 0) selected @endif style="font-weight: bold;">Monthly</option>
                                    <option value="1" @if(isset($plan) && $plan['plan_type'] == 1) selected @endif style="font-weight: bold;">Quartarly</option>
                                    <option value="2" @if(isset($plan) && $plan['plan_type'] == 2) selected @endif style="font-weight: bold;">Yearly</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="inputName">Image</label>
                                <input type="file" id="image" name="image" class="form-control">
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="status" name="status" @if(isset($plan) && $plan['status']==1) checked @endif>
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
                    <input type="submit" @if(!isset($plan)) value="Create Plan" @else value="Update Plan" @endif class="btn btn-success float-right">
                </div>
            </div>
        </form>
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
                name: {
                    required: true,
                },
                price : {
                    required: true
                },
                description: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: "Please Enter Plan Name",
                },
                price: {
                    required: "Please enter Plan Price"
                },
                description: {
                    required: "Please write a short description about plan"
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