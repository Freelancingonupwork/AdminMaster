@extends('front.frontLayout.master')

@section('styles')
@endsection

@section('content')

<div class="row mt-5" style="margin: auto;">
    <div class="card-title">
        <h1>My Orders</h1>
        <div class="float-right">
            <a @if(!isset($type) || empty($type)) href="{{ url('order-list/'.'subscription') }}" @else href="{{ url('order-list') }}" @endif class="btn btn-info">@if(!isset($type) || empty($type)) Subscription History @else Order History @endif</a>
        </div>
    </div>
    <div class="card-body">
        @if(!isset($type) || empty($type))
        <table id="example2" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Discount</th>
                    <th>Shipping</th>
                    <th>Order Date</th>
                    <th>Payment Status</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orderData as $key => $order)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $order['product']['product_name'] }}</td>
                    <td>{{ $order['product']['product_price'] }}</td>
                    <td>{{ $order['cart']['product_discount'] }}</td>
                    <td>{{ $order['cart']['shipping'] }}</td>
                    <td>{{ $order['order_date'] }}</td>
                    <td>@if($order['is_paid'] == 1) <span>Paid</span> @else <span>Pending @endif</span></td>
                    <td>{{ $order['cart']['product_total'] }}</td>
                    <td><a href="{{ url('/rating-review/'. $order['id']) }}" class="btn btn-primary btn-sm">Give Rating</a></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Sr. No.</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Discount</th>
                    <th>Shipping</th>
                    <th>Order Date</th>
                    <th>Payment Status</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </tfoot>
        </table>
        @else
        <table id="example2" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Email</th>
                    <th>Plan Name</th>
                    <th>Plan Value</th>
                    <th>Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orderData as $key => $order)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $order['user_email'] }}</td>
                    <td>{{ $order['plan_name'] }}</td>
                    <td>{{ $order['plan_value'] }}</td>
                    <td>@if($order['subscription_type'] == 0) <span>Monthly</span> @elseif($order['subscription_type'] == 1) <span>Quartarly</span> @else <span>Yearly</span> @endif</td>
                    <td>{{ $order['start_date'] }}</td>
                    <td>{{ $order['end_date'] }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Sr. No.</th>
                    <th>Email</th>
                    <th>Plan Name</th>
                    <th>Plan Value</th>
                    <th>Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <!-- <th></th> -->
                </tr>
            </tfoot>
        </table>
        @endif

        <!-- <div>
            @if(isset($order) && !empty($order))
            <a href="{{ url('/checkout') }}" class="btn btn-info float-right mt-2"> Checkout </a>
            @endif
        </div> -->
    </div>
</div>
@endsection

@section('scripts')
@endsection
