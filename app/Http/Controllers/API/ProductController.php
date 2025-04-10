<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ProductController extends ApiController
{
    /**
     * @OA\Post(
     *     path="/api/v1/category-list",
     *     tags={"Category List"},
     *     summary="Get Category List",
     *     description="Get Category List",
     *     operationId="getCategoryList",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 properties={
     *                     @OA\Property(property="search", type="string"),
     *                 },
     *             ),
     *         ),
     *     ),
     *     security={{ "bearer":{} }},
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid status value"
     *     ),
     * )
     */
    public function getCategoryList(Request $request)
    {
        try {
            /* Validate Data */
            $validation = [
                'search' => ['nullable', 'string'],
            ];
            $validator = Validator::make($request->all(), $validation);

            if ($validator->fails()) {
                return response()->json(
                    [
                        "status" => "fail",
                        'errors' => $validator->getMessageBag(),
                        'message' => $validator->errors()->first(),
                    ],
                    400
                );
            }

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $getCategoryList = Category::where(function ($query) use ($search) {
                    $query->where('category_name', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%');
                })
                    ->where(['status' => 1])
                    ->get()->toArray();
            } else {
                $getCategoryList = Category::where(['status' => 1])->get()->toArray();
            }
            (int) $limit = 15;
            if (!isset($request->page) && (!isset($request->page))) {
                (int) $page = 1;
            } else {
                (int) $page = $request->page;
            }

            $result = $this->pagination($getCategoryList, $page, $limit);


            if (count($getCategoryList) > 0) {
                return $this->successResponse("Category Found", $result['data'], 200);
            } else {
                return $this->errorResponse("Category Not Found", [], 200);
            }
        } catch (Throwable $th) {
            return response()->json(
                [
                    "status" => "fail",
                    'errors' => $th->getMessage(),
                    "message" => "Something went wrong",
                ],
                500
            );
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/product-list",
     *     tags={"Product List"},
     *     summary="Get Product List",
     *     description="Get Product List",
     *     operationId="getProductList",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 properties={
     *                     @OA\Property(property="search", type="string"),
     *                 },
     *             ),
     *         ),
     *     ),
     *     security={{ "bearer":{} }},
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid status value"
     *     ),
     * )
     */
    public function getProductList(Request $request)
    {
        try {
            /* Validate Data */
            $validation = [
                'search' => ['nullable', 'string'],
            ];
            $validator = Validator::make($request->all(), $validation);

            if ($validator->fails()) {
                return response()->json(
                    [
                        "status" => "fail",
                        'errors' => $validator->getMessageBag(),
                        'message' => $validator->errors()->first(),
                    ],
                    400
                );
            }

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $getProductList = Product::where(function ($query) use ($search) {
                    $query->where('product_name', 'like', '%' . $search . '%')
                        ->orWhere('product_code', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%')
                        ->orWhere('product_price', 'like', '%' . $search . '%')
                        ->orWhere('promo_code', 'like', '%' . $search . '%');
                })
                    ->with('productCategory', 'productCoupon')
                    ->where(['status' => 1])
                    ->get()->toArray();
            } else {
                $getProductList = Product::with('productCategory', 'productCoupon')->where(['status' => 1])->get()->toArray();
            }
            (int) $limit = 15;
            if (!isset($request->page) && (!isset($request->page))) {
                (int) $page = 1;
            } else {
                (int) $page = $request->page;
            }

            $result = $this->pagination($getProductList, $page, $limit);


            if (count($getProductList) > 0) {
                return $this->successResponse("Product Found", $result['data'], 200);
            } else {
                return $this->errorResponse("Product Not Found", [], 200);
            }
        } catch (Throwable $th) {
            return response()->json(
                [
                    "status" => "fail",
                    'errors' => $th->getMessage(),
                    "message" => "Something went wrong",
                ],
                500
            );
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/promocodes",
     *     tags={"Promocodes"},
     *     summary="Get Promocodes",
     *     description="Get Promocodes",
     *     operationId="getCouponList",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 properties={
     *                     @OA\Property(property="search", type="string"),
     *                 },
     *             ),
     *         ),
     *     ),
     *     security={{ "bearer":{} }},
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid status value"
     *     ),
     * )
     */
    public function getCouponList(Request $request)
    {
        try {
            /* Validate Data */
            $validation = [
                'search' => ['nullable', 'string'],
            ];
            $validator = Validator::make($request->all(), $validation);

            if ($validator->fails()) {
                return response()->json(
                    [
                        "status" => "fail",
                        'errors' => $validator->getMessageBag(),
                        'message' => $validator->errors()->first(),
                    ],
                    400
                );
            }

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $getCouponList = Coupon::where(function ($query) use ($search) {
                    $query->where('coupon_option', 'like', '%' . $search . '%')
                        ->orWhere('coupon_code', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%');
                })
                    ->where(['status' => 1])
                    ->get()->toArray();
            } else {
                $getCouponList = Coupon::where(['status' => 1])->get()->toArray();
            }
            (int) $limit = 15;
            if (!isset($request->page) && (!isset($request->page))) {
                (int) $page = 1;
            } else {
                (int) $page = $request->page;
            }

            $result = $this->pagination($getCouponList, $page, $limit);


            if (count($getCouponList) > 0) {
                return $this->successResponse("Promocode Found", $result['data'], 200);
            } else {
                return $this->errorResponse("Promocode Not Found", [], 200);
            }
        } catch (Throwable $th) {
            return response()->json(
                [
                    "status" => "fail",
                    'errors' => $th->getMessage(),
                    "message" => "Something went wrong",
                ],
                500
            );
        }
    }
}
