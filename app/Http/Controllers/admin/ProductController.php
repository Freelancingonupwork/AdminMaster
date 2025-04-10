<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Plan;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $productData = Product::with('productCategory', 'productCoupon')->get()->toArray();
        return view('admin.products.index')->with(compact('productData'));
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();


            $validation = [
                'category_id' => ['required'],
                'product_name' => ['required', 'string', 'nullable', 'max:255'],
                'product_price' => ['required', 'nullable', 'max:255'],
                'promo_code' => ['required'],
            ];
            $validator = Validator::make($data, $validation);
            $input = $request->except(['_token']);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->getMessageBag());
            }

            if (empty($data['status'])) {
                $status = 0;
            } else {
                $status = 1;
            }

            if (empty($data['subscription_type'])) {
                $subscription_type = 0;
            } else {
                $subscription_type = 1;
            }

            if (!isset($data['product_image']) || empty($data['product_image'])) {
                $productImage = '';
            } else {
                $productImage = time() . '.' . $data['product_image']->extension();
                if (! Storage::disk('public')->exists("/products/product_images")) {
                    Storage::disk('public')->makeDirectory("/products/product_images"); //creates directory
                }
                $request->product_image->storeAs("/products/product_images/", $productImage, 'public');

                $productImage = "/products/product_images/$productImage";
            }


            // if (!isset($data['product_image']) || empty($data['product_image'])) {
            //     $productImage = "";
            // } else {
            //     $productImage = time() . '.' . $data['product_image']->extension();
            //     $data['product_image']->move(public_path('/uploads/products'), $productImage);
            // }

            $slug =  Str::slug($data['product_name']);

            $productCode = $slug . "-" . $data['product_price'];

            $product = new Product;
            $product->category_id = $data['category_id'];
            $product->product_name = $data['product_name'];
            $product->product_code = $productCode;
            $product->slug = $slug;
            $product->description = $data['description'];
            $product->product_price = $data['product_price'];
            $product->product_image = $productImage;
            $product->promo_code = $data['promo_code'];
            $product->status = $status;
            $product->subscription_type = $subscription_type;
            $product->save();
            return redirect('/admin/product-index')->with('success', 'Product inserted Successfully !!!');
        }

        $categories = Category::where('parent_id', 0)->get()->toArray();
        $coupons = Coupon::get()->toArray();
        return view('admin.products.create')->with(compact('categories', 'coupons'));
    }

    public function update(Request $request, $slug, $id)
    {
        $product = Product::with('productCategory', 'productCoupon')->where(['id' => $id])->first();
        if ($request->isMethod('post')) {
            $data = $request->all();

            $validation = [
                'category_id' => ['required'],
                'product_name' => ['required', 'string', 'nullable', 'max:255'],
                'product_price' => ['required', 'nullable', 'max:255'],
                'promo_code' => ['required'],
            ];
            $validator = Validator::make($data, $validation);
            $input = $request->except(['_token']);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->getMessageBag());
            }

            if (empty($data['status'])) {
                $status = 0;
            } else {
                $status = 1;
            }

            if (empty($data['subscription_type'])) {
                $subscription_type = 0;
            } else {
                $subscription_type = 1;
            }

            if (!isset($data['product_image']) || empty($data['product_image'])) {
                $productImage = $product['product_image'];
            } else {
                $productImage = time() . '.' . $data['product_image']->extension();
                if (!Storage::disk('public')->exists("/product/product_images")) {
                    Storage::disk('public')->makeDirectory("/product/product_images"); //creates directory
                }
                if (Storage::disk('public')->exists("/product/" . $product['product_image'])) {
                    Storage::disk('public')->delete("/product/" . $product['product_image']);
                }
                $request->product_image->storeAs("product/product_images", $productImage, 'public');

                $productImage = "product/product_images/$productImage";
            }

            $slug =  Str::slug($data['product_name']);

            $productCode = $slug . "-" . $data['product_price'];

            $updateProduct = Product::where(['id' => $id])->update(['category_id' => $data['category_id'], 'product_name' => $data['product_name'], 'product_code' => $productCode, 'slug' => $slug, 'description' => $data['description'], 'product_price' => $data['product_price'], 'product_image' => $productImage, 'promo_code' => $data['promo_code'], 'status' => $status, 'subscription_type' => $subscription_type]);

            return redirect('/admin/product-index')->with('success', 'Product updated Successfully !!!');
        }
        $categories = Category::where('parent_id', 0)->get()->toArray();
        $coupons = Coupon::get()->toArray();
        return view('admin.products.create')->with(compact('categories', 'coupons', 'product'));
    }

    public function destroy($slug, $id)
    {
        Product::where(['slug' => $slug, 'id' => $id])->delete();
        return redirect()->back();
    }
}
