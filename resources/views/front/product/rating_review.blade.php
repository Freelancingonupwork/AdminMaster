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

    .rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center
    }

    .rating>input {
        display: none
    }

    .rating>label {
        position: relative;
        width: 1em;
        font-size: 35px;
        color: goldenrod;
        cursor: pointer
    }

    .rating>label::before {
        content: "\2605";
        position: absolute;
        opacity: 0
    }

    .rating>label:hover:before,
    .rating>label:hover~label:before {
        opacity: 1 !important
    }

    .rating>input:checked~label:before {
        opacity: 1
    }

    .rating:hover>input:checked~label:before {
        opacity: 0.4
    }
</style>
<!-- <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.css"> -->
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
        @if(empty($orderData['product']['product_image']))
        <img class="profile-user-img img-fluid img-circle" src="{{ asset('assets/dist/img/user4-128x128.jpg') }}" id="user_img" alt="User profile picture">
        @else
        <img src="{{ url('storage/'.$orderData['product']['product_image']) }}" alt="" srcset="" style="width: 250px; height: 350px;">
        @endif
    </div>
    <div class="col-sm-6" style="margin: auto;">
        <div class="mt-5">
            <?php

            use App\Models\RatingReview;
            use Illuminate\Support\Facades\Auth;

            if ($orderData['coupon']['amount_type'] == "percent") {
                $discount = $orderData['product']['product_price'] * $orderData['coupon']['amount'] / 100;
            } else {
                $discount = $orderData['coupon']['amount'];
            }
            ?>
            <h3>{{ $orderData['product']['product_name'] }}</h3>
            <b> Product Code : </b><span>({{ $orderData['product']['product_code'] }})</span><br>
            <b> Product Price : </b><span>{{ $orderData['product']['product_price'] }}</span><br>
            <b> Discount &emsp;&emsp; : </b><span>{{ $discount }}</span><br>
            <span>{!! $orderData['product']['description'] !!}</span>

            <?php
            $stars = '';
            $user = true;
            foreach ($ratingReviewDetails as $reviewRating) {
                $currUsrRatingReview = [];
                $rating = '';
                if ($reviewRating['user_id'] == Auth::user()->id) {

                    $currUsrRatingReview = RatingReview::where(['user_id' => Auth::user()->id, 'product_id' => $orderData['product_id']])->first()->toArray();
                    // echo "<pre>";
                    // print_r($currUsrRatingReview['rating']);

                    $rating = $currUsrRatingReview['rating'];
                    $stars = '';
                    for ($i = 1; $i <= 5; $i++) {
                        $stars .= $i <= $rating ? '★' : '☆';
                    }
                    $user = false;
                } else {
                    $user = true;
                }
            }
            ?>

            <div class="row">
                <div class="col-lg-4">
                    <div class="star-rating" style="color:goldenrod; font-size:xx-large;">
                        {{ $stars }}
                    </div>
                </div>
            </div>

            @if($user)
            <form action="{{ url('/rating-review/'.$orderData['id']) }}" method="post" class="rating-submit">
                {{ csrf_field() }}
                <input type="hidden" name="product_id" id="product_id" value="{{ $orderData['product']['id'] }}">
                <div class="rating">
                    <input type="radio" name="rating" value="5" id="5">
                    <label for="5">☆</label>
                    <input type="radio" name="rating" value="4" id="4">
                    <label for="4">☆</label>
                    <input type="radio" name="rating" value="3" id="3">
                    <label for="3">☆</label>
                    <input type="radio" name="rating" value="2" id="2">
                    <label for="2">☆</label>
                    <input type="radio" name="rating" value="1" id="1">
                    <label for="1">☆</label>
                </div>

                <div class="form-group mb-3 mt-1">
                    <textarea type="textarea" class="form-control rating-textarea" id="review" name="review" placeholder="Your Review" required>{{ old('review') }}</textarea>
                </div>

                <div class="write-review mb-5" style="float: right;">
                    <button type="submit" class="btn btn-success">Write Review</button>
                </div>
            </form><br>
            @endif
        </div>
    </div>
    @if(!empty($ratingReviewDetails))
    <div class="col-sm-12 p-2 mt-3">
        <div class="card featured-cards" style="border-radius: 15px; background-color:grey;">
            @foreach($ratingReviewDetails as $ratingReview)
            <div class="card-body">
                <?php
                $rating = $ratingReview['rating'];
                $stars = '';
                for ($i = 1; $i <= 5; $i++) {
                    $stars .= $i <= $rating ? '★' : '☆';
                }
                ?>
                <div class="col-lg p-3">
                    <div class="row">
                        <div class="col-lg-2">
                            @if(!empty($ratingReview['users']['avatar']))
                            <img src="{{ asset('storage/'.$ratingReview['users']['avatar']) }}" style="border-radius:50px;height:100px;width:100px" />
                            @else
                            <img src="{{ asset('assets/dist/img/user4-128x128.jpg') }}" style="border-radius:50px;height:100px;width:100px" />
                            @endif
                        </div>
                        @if ($ratingReview['user_id'] > 0)
                        <div class="col-lg-3 mt-2" style="font-size:xxx-large">
                            {{ $ratingReview['users']['name'] }}
                        </div>
                        @else
                        <div class="col-lg-3 mt-2">
                            Anonymous
                        </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="star-rating" style="color:goldenrod; font-size:xx-large;">
                                {{ $stars }}
                            </div>
                        </div>
                    </div>
                    <div class="row p-3" style="font-size:xxx-large">
                        {{ $ratingReview['review'] }}
                    </div>
                </div>
            </div>
            @endForeach
        </div>
    </div>
    @endif
</div>
@endsection
