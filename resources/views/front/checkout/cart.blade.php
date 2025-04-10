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

    <?php

    use Illuminate\Support\Facades\Session;

    $total = 0; ?>
    @foreach($cartDetails as $cart)
    <div class="col-sm-4 mt-3" style="margin: auto;">
        @if(empty($cart['product_image']))
        <img class="profile-user-img img-fluid img-circle" src="{{ asset('assets/dist/img/user4-128x128.jpg') }}" id="user_img" alt="User profile picture">
        @else
        <img src="{{ url('storage/'.$cart['product_image']) }}" alt="" srcset="" style="width: 250px; height: 350px;">
        @endif
    </div>

    <div class="col-sm-6" style="margin: auto;">
        <div class="mt-5">
            <h3>{{ $cart['product_name'] }}</h3>
            <b> Product Code : </b><span>({{ $cart['product']['product_code'] }})</span><br>
            <b> Category &emsp;&emsp;: </b><span>{{ $cart['product_category']['category_name'] }}</span><br>
            <span>{!! $cart['product']['description'] !!}</span>
        </div>
    </div>
    @endforeach
</div>
<hr style="height: 3px;border-width: 2px;color: #ffffff;background-color: #ffffff;margin-top: 2rem;">

<div class="row mt-5" style="margin: auto;">
    <div class="card-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Discount</th>
                    <th>Shipping</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($cartDetails as $key => $cart)
                <?php
                $grandTotal = 0;
                if (Session::has('grandTotal')) {
                    $grandTotal = Session::get('grandTotal');
                    $grandTotal = $grandTotal[0]->grandTotal;
                }
                ?>
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $cart['product_name'] }}</td>
                    <td>{{ $cart['product_price'] }}</td>
                    <td>{{ $cart['product_discount'] }}</td>
                    <td>{{ $cart['shipping_charge'] }}</td>
                    <td>{{ $cart['product_total'] }}</td>
                    <td><a href="" class="btn btn-danger"><i class="fas fa-trash"></i></a></td>
                </tr>
                @endforeach
                @if(isset($grandTotal) && !empty($grandTotal))
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <th>Total Payable </th>
                    <td>{{ $grandTotal }}</td>
                    <td></td>
                </tr>
                @endif
            </tbody>
        </table>

        <div>
            @if(isset($cartDetails) && !empty($cartDetails))
            <a href="{{ url('/checkout') }}" class="btn btn-info float-right mt-2"> Checkout </a>
            @endif
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
    toastr.success("{{ session('success') }}");
    @endif
</script>
@endsection
