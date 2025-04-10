@extends('front.frontLayout.master')

@section('styles')
<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.css">
@endsection

@section('content')
<div class="row">

    @if (Session::has('success'))
    <div class="alert alert-success" style="height:50px; border-radius:20px;">
        <a href="#" class="close" data-dismiss="alert" aria-label="close" style="text-decoration: none; height:50px; border-radius:0.75px;">x</a>
        <p>{{ Session::get('success') }}</p>
    </div>
    @endif

    @if (Session::has('error'))
    <div class="alert alert-danger" style="height:50px; border-radius:20px;">
        <strong>{{ Session::get('error') }}</strong>
        <a href="#" class="close" data-dismiss="alert" aria-label="close" style="text-decoration: none;">x</a>
    </div>
    @endif

    <div class="col-sm-4 mt-3 mb-3" style="margin: auto;">
        @if(empty($productDetails->product_image))
        <img class="profile-user-img img-fluid img-circle" src="{{ asset('assets/dist/img/user4-128x128.jpg') }}" id="user_img" alt="User profile picture">
        @else
        <img src="{{ url('storage/'.$productDetails->product_image) }}" alt="" srcset="" style="width: 250px; height: 350px;">
        @endif
    </div>
    <div class="col-sm-6" style="margin: auto;">
        <div class="mt-5">
            <?php
            if ($productDetails->productCoupon->amount_type == "percent") {
                $discount = $productDetails->product_price * $productDetails->productCoupon->amount / 100;
            } else {
                $discount = $productDetails->productCoupon->amount;
            }
            ?>
            <h3>{{ $productDetails->product_name }}</h3>
            <b> Product Code : </b><span>({{ $productDetails->product_code }})</span><br>
            <b> Product Price : </b><span>{{ $productDetails->product_price }}</span><br>
            <b> Category &emsp;&emsp;: </b><span>{{ $productDetails->productCategory->category_name }}</span><br>
            <b> Discount &emsp;&emsp; : </b><span>{{ $discount }}</span><br>
            <span>{!! $productDetails->description !!}</span>
            <form action="{{ url('add-to-cart') }}" method="post">

                {{ csrf_field() }}
                <input type="hidden" name="product_id" id="product_id" value="{{ $productDetails->id }}">
                <input type="hidden" name="product_name" id="product_name" value="{{ $productDetails->product_name }}">
                <input type="hidden" name="category_id" id="category_id" value="{{ $productDetails->productCategory->id }}">
                <input type="hidden" name="product_price" id="product_price" value="{{ $productDetails->product_price }}">
                <input type="hidden" name="coupon_id" id="coupon_id" value="{{ $productDetails->productCoupon->id }}">
                <input type="hidden" name="product_image" id="product_image" value="{{ $productDetails->product_image }}">

                @auth
                <div class="button mt-3 mb-3">
                    <button type="submit" class="btn btn-success">Add To Cart</a>
                </div>
                @else
                <div class="button mt-3 mb-3">
                    <a class="btn btn-success" data-bs-toggle="modal" href="#exampleModalToggle">Add To Cart</a>
                </div>
                @endauth
            </form>
        </div>
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
    toastr.success("{{ session('error') }}");
    @else
    toastr.options = {
        "closeButton": true,
        "progressBar": true
    }
    toastr.error("{{ session('error') }}");
    @endif
</script>
@endsection
