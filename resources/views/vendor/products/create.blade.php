@extends('vendor.vendorLayout.vendorMaster')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Product</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item active">Products</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <form @if(!isset($product) || empty($product)) action="{{ url('vendor/product-create') }}" @else acion="{{ url('vendor/product-update/'.$product['slug']) }}" @endif method="post" enctype="multipart/form-data">
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
                            <h3 class="card-title">Add Product</h3>
                            <div class="card-tools">
                                <a type="button" class="btn btn-secondary" href="{{ url('vendor/product-index') }}">
                                    <i class="fas fa-arrow-left"></i>
                                    Return to List
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="inputName">Category</label>
                                <select class="form-control" name="category_id" id="category_id">
                                    <option value="">Main Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category['id'] }}" @if(isset($product) && $product['category_id'] == $category['id']) selected @endif style="font-weight: bold;">{{ $category['category_name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="inputName">Product Name</label>
                                <input type="text" id="product_name" name="product_name" class="form-control" @if(isset($product)) value="{{ $product->product_name }}" @else value="{{ old('product_name') }}" @endif>
                            </div>
                            <div class="form-group">
                                <label for="inputName">Product Price</label>
                                <input type="number" id="product_price" name="product_price" class="form-control" @if(isset($product)) value="{{ $product->product_price }}" @else value="{{ old('product_price') }}" @endif>
                            </div>
                            <div class="form-group">
                                <label for="inputDescription">Project Description</label>
                                <textarea id="description" name="description" class="form-control" rows="10">@if(isset($product)) {{ $product->description }} @else {{ old('description') }} @endif</textarea>
                            </div>
                            <div class="form-group">
                                <label for="inputName">Promo Code</label>
                                <select class="form-control" name="promo_code" id="promo_code">
                                    <option value="">Select Promocode</option>
                                    @foreach($coupons as $coupon)
                                        <option value="{{ $coupon['id'] }}" @if(isset($product) && $product['promo_code'] == $coupon['id']) selected @endif style="font-weight: bold;">{{ $coupon['coupon_code'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="inputName">Image</label>
                                <input type="file" id="product_image" name="product_image" class="form-control">
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="status" name="status" @if(isset($product) && $product['status'] == 1) checked @endif>
                                <label for="status">Enable</label>
                            </div>

                            <div class="form-group">
                                <label for="inputName">Subscription Type</label>
                                <select class="form-control" name="subscription_type" id="subscription_type">
                                    <option value="">Select Promocode</option>
                                    <option value="0" @if(isset($product) && $product['subscription_type'] == 0) selected @endif style="font-weight: bold;">Life Time</option>
                                    <option value="1" @if(isset($product) && $product['subscription_type'] == 1) selected @endif style="font-weight: bold;">One Time</option>
                                </select>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <div class="row">
                <div class="col-12 mb-2">
                    <input type="submit" @if(!isset($product)) value="Save" @else value="Update" @endif class="btn btn-success float-right">
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