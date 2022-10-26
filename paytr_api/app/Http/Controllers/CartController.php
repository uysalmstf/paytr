<?php

namespace App\Http\Controllers;

use App\Models\CartProducts;
use App\Models\Carts;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'amount' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $activeCart = Carts::where('status', 1)->where('user_id', auth()->user()['id'])->first();

        if ($activeCart == null) {
            $activeCart = 0;
            $cart = new Carts();
            $cart->user_id = auth()->user()['id'];

            if ($cart->save()) {
                $activeCart = $cart->id;
            }
        } else {

            $activeCart = $activeCart->id;
        }

        $activeCartChoosenProduct = CartProducts::where('cart_id', $activeCart)->where('product_id', $request->get('product_id'))->first();
        if ($activeCartChoosenProduct == null) {

            $cartProduct = new CartProducts();

            $cartProduct->cart_id = $activeCart;
            $cartProduct->product_id = $request->get('product_id');
            $cartProduct->amount = $request->get('amount');

            if ($cartProduct->save()) {
                $response = ['message' => 'Process Done'];
            } else {
                $response = ['errors' => "Process doesn't completed", 422];
            }
        } else {

            $cartProduct = $activeCartChoosenProduct;
            $cartProduct->amount = $request->get('amount');

            if ($cartProduct->save()) {
                $response = ['message' => 'Amount Updated'];
            } else {
                $response = ['errors' => "Process doesn't completed", 422];
            }
        }

        return response($response, 200);
    }

    public function index()
    {
        //sadece aktif sepetin bilgileri gösterilmektedir. Aksi istenmediği görülmüştür.
        $activeCart = Carts::where('status', 1)->where('user_id', auth()->user()['id'])->first();

        $activeCartDetail = array();

        $totalPrice = 0;

        if ($activeCart == null) {

            $response = [
                'message' => 'Process done',
                'cart_detail' => array(),
                'total_price' => $totalPrice
            ];
        } else {

            $activeCartProducts = CartProducts::where('cart_id', $activeCart->id)->orderBy('created_at')->get();
            if ($activeCartProducts == null) {

                $response = [
                    'message' => 'Process done',
                    'cart_detail' => array(),
                    'total_price' => $totalPrice
                ];
            } else {

                foreach ($activeCartProducts as $product) {

                    $productDetail = Products::where('id', $product->product_id)->first();

                    if ($productDetail != null ) {

                        $productDetailArr = array();

                        $productDetailArr['name'] = $productDetail->name;
                        $productDetailArr['price'] = $productDetail->price;
                        $productDetailArr['amount'] = $product->amount;
                        $productDetailArr['discount'] = $productDetail->discount;
                        $productDetailArr['discount_price'] = 0;

                        if ($productDetail->discount > 0) {

                            $productDetail->price -= $productDetail->price * $productDetail->discount / 100;
                            $productDetailArr['discount_price'] = $productDetail->price;
                        }

                        $totalPrice += ($product->amount * $productDetail->price);

                        $activeCartDetail[] = $productDetailArr;
                    }
                }

                $response = [
                    'message' => 'Process done',
                    'cart_detail' => $activeCartDetail,
                    'total_price' => $totalPrice
                ];
            }
        }


        return response($response, 200);
    }

    public function removeProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $activeCart = Carts::where('status', 1)->where('user_id', auth()->user()['id'])->first();

        if ($activeCart == null) {
            return response(['errors' => 'Active Cart Not Found'], 422);
        } else {

            if (CartProducts::where('product_id', $request->get('product_id'))->where('cart_id', $activeCart->id)->delete()){
                $response = ['message' => 'Product Removed'];
            } else {
                $response = ['errors' => "Process doesn't completed", 422];
            }

            return response($response, 200);
        }

    }

    public function close(Request $request)
    {
        $activeCart = Carts::where('status', 1)->where('user_id', auth()->user()['id'])->first();

        $totalPrice = 0;

        if ($activeCart == null) {

            $response = [
                'message' => 'Process done',
            ];
        } else {

            $activeCartProducts = CartProducts::where('cart_id', $activeCart->id)->orderBy('created_at')->get();
            if ($activeCartProducts == null) {

                $response = [
                    'message' => 'Process done',
                    'cart_detail' => array(),
                    'total_price' => $totalPrice
                ];
            } else {

                foreach ($activeCartProducts as $product) {

                    $productDetail = Products::where('id', $product->product_id)->first();

                    if ($productDetail != null) {

                        if ($productDetail->discount > 0) {

                            $productDetail->price -= $productDetail->price * $productDetail->discount / 100;
                        }

                        $totalPrice += ($product->amount * $productDetail->price);

                        $product->price = $productDetail->price;
                        $product->save();
                    }
                }

                $activeCart->total_price = $totalPrice;
                $activeCart->status = 2; //tamamlanmış sepet

                if ($activeCart->save()) {

                    $response = [
                        'message' => 'Process done',
                    ];
                } else {

                    $response = ['errors' => "Process doesn't completed", 422];
                }

            }
        }

        return response($response, 200);
    }
}
