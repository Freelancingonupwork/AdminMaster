<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function index()
    {
        $couponData = Coupon::get()->toArray();
        return view('admin.coupon.index')->with(compact('couponData'));
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            $validation = [
                'coupon_option' => ['required'],
                'amount' => ['required', 'nullable', 'max:255'],
                'amount_type' => ['required'],
                'expiry_date' => ['required', 'date', 'nullable', 'max:255'],
            ];
            $validator = Validator::make($data, $validation);
            $input = $request->except(['_token']);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->getMessageBag());
            }

            if (empty($data['coupon_code'])) {
                $coupon_code = Str::random(8);
            } else {
                $coupon_code = $data['coupon_code'];
            }

            if (empty($data['status'])) {
                $status = 0;
            } else {
                $status = 1;
            }

            $slug = Str::slug($coupon_code);

            $coupon = new Coupon();
            $coupon->coupon_option = $data['coupon_option'];
            $coupon->coupon_code = $coupon_code;
            $coupon->amount = $data['amount'];
            $coupon->amount_type = $data['amount_type'];
            $coupon->expiry_date = $data['expiry_date'];
            $coupon->slug = $slug;
            $coupon->status = $status;
            $coupon->save();
            return redirect('/admin/coupon-index')->with('success', 'Coupon inserted Successfully !!!');
        }
        return view('admin.coupon.create');
    }

    public function update(Request $request, $slug, $id)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            // echo "<pre>"; print_r($data); die;
            $validation = [
                'coupon_option' => ['required'],
                'amount' => ['required', 'nullable', 'max:255'],
                'amount_type' => ['required'],
                'expiry_date' => ['required', 'date', 'nullable', 'max:255'],
            ];
            $validator = Validator::make($data, $validation);
            $input = $request->except(['_token']);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->getMessageBag());
            }

            if ($data['coupon_option'] == "manual") {
                $validation = [
                    'coupon_code' => ['required', 'string', 'max:20']
                ];

                $validator = Validator::make($data, $validation);
                $input = $request->except(['_token']);
                if ($validator->fails()) {
                    return redirect()->back()->withInput()->withErrors($validator->getMessageBag());
                }
            }

            if (empty($data['coupon_code'])) {
                $coupon_code = Str::random(8);
            } else {
                $coupon_code = $data['coupon_code'];
            }

            if (empty($data['status'])) {
                $status = 0;
            } else {
                $status = 1;
            }

            $slug = Str::slug($coupon_code);

            $updateCoupon = Coupon::where(['id' => $id])->update(['coupon_option' => $data['coupon_option'], 'coupon_code' => $coupon_code, 'slug' => $slug, 'amount' => $data['amount'], 'amount_type' => $data['amount_type'], 'expiry_date' => $data['expiry_date'], 'status' => $status]);
            return redirect('/admin/coupon-index')->with('success', 'Category updated successfully !!!');
        }

        $coupon = Coupon::where(['id' => $id])->first();

        return view('admin.coupon.create')->with(compact('coupon'));
    }

    public function destroy($slug, $id)
    {
        Coupon::where(['slug' => $slug, 'id' => $id])->delete();
        return redirect()->back();
    }
}
