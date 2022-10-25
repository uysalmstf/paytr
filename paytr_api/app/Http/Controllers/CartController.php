<?php

namespace App\Http\Controllers;

use App\Models\CartProducts;
use App\Models\Carts;
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
}
