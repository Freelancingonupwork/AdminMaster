<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Product;
use App\Models\RatingReview;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    // Product listing on front page

    public function listing()
    {
        $productData = Product::with('productCategory', 'productCoupon')->inRandomOrder()->paginate(3);
        return view('front.product.listing')->with(compact('productData'));
    }

    public function productDetails(Request $request, $slug, $id)
    {
        $productDetails = Product::with('productCategory', 'productCoupon')->where(['id' => $id])->first();
        return view('front.product.details')->with(compact('productDetails'));
    }

    public function ratingReview(Request $request, $id)
    {
        $orderData = Order::with('product','coupon')->where(['user_id' => Auth::user()->id, 'id' => $id])->first()->toArray();

        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;

            /* Validate Data */
            $validation = [
                'rating' => ['required'],
                'review' => ['required',/*'min:100'*/'max:250'],
            ];
            $validation_messages = [
                'rating.required' => 'The Rating field is required.',
                'review.required' => 'The Review field is required.',
            ];
            $validator = Validator::make($request->all(), $validation, $validation_messages);
            if ($validator->fails()) {
                return redirect()->back()->with('error',$validator->getMessageBag());
            }

            $ratingReview = new RatingReview();
            $ratingReview->user_id = Auth::user()->id;
            $ratingReview->product_id = $data['product_id'];
            $ratingReview->rating = $data['rating'];
            $ratingReview->review = $data['review'];
            if($ratingReview->save()){
                return redirect()->back()->with('success','Thanks for your review.');
            }

        }
        $ratingReviewDetails = RatingReview::with('users')->where(['product_id' => $orderData['product_id']])->get()->toArray();
        return view('front.product.rating_review')->with(compact('orderData','ratingReviewDetails'));
    }
}
