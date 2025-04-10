@extends('front.frontLayout.master')

@section('styles')
<style>
    .alert {
        border-radius: 1.25rem;
    }

    .alert-dismissible .btn-close {
        position: absolute;
        top: 0;
        right: 0;
        z-index: 2;
        padding: 1rem 1rem;
    }
</style>
<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.css">
@endsection

@section('content')
<div class="row">

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if (Session::has('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>{{ Session::get('success') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if (Session::has('error'))
    <div class="alert alert-error alert-dismissible fade show" role="alert">
        <strong>{{ Session::get('error') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @foreach($productData as $product)
    <div class="col-sm-4 pl-5">
        <h3>{{ $product->product_name }}</h3>
        @if(empty($product->product_image))
        <img class="profile-user-img img-fluid img-circle" src="{{ asset('assets/dist/img/user4-128x128.jpg') }}" id="user_img" alt="User profile picture">
        @else
        <img src="{{ url('storage/'.$product->product_image) }}" alt="" srcset="" style="width: 200px; height: 150px;">
        @endif
        <p>{!! $product->description !!}</p>
        <p>Code : {{ $product->product_code }}</p>
        <p>Price : {{ $product->product_price }}</p>
        <?php
        if ($product->productCoupon->amount_type == "percent") {
            $discount = $product->product_price * $product->productCoupon->amount / 100;
        } else {
            $discount = $product->productCoupon->amount;
        }
        ?>
        <p>Discount : {{ $discount }}</p>
        <p>Category : {{ $product->productCategory->category_name }}</p>

        <div class="button">
            <a href="{{ url('product-details/'.$product->slug.'/'.$product->id) }}" class="btn btn-success">Add To Cart</a>
        </div>
    </div>
    @endforeach

    <div class="pagination" style="justify-content: center;">

        {!! $productData->links() !!}
    </div>
</div>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    @if(Session::has('success'))
    toastr.options = {
        "closeButton": true,
        "progressBar": true
    }
    toastr.success("{{ session('success') }}");
    @endif
</script>
@endsection
