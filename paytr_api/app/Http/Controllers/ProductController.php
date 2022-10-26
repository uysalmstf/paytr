<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function create(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|integer',
            'price' => 'required|between:0,99.99',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $product = new Products();
        $product->name = $request->get('name');
        $product->price = $request->get('price');
        $product->category = $request->get('category');
        $product->discount = $request->get('discount');

        if ($product->save()) {
            $response = ['message' => 'Process Done'];
        } else {
            $response = ['errors' => "Process doesn't completed", 422];
        }

        return response($response, 200);
    }

    public function edit(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|integer',
            'product_id' => 'required|integer',
            'price' => 'required|between:0,99.99',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $product = Products::where('id', $request->get('product_id'))->first();
        $product->name = $request->get('name');
        $product->price = $request->get('price');
        $product->discount = $request->get('discount');
        $product->category = $request->get('category');

        if ($product->save()) {
            $response = ['message' => 'Process Done'];
        } else {
            $response = ['errors' => "Process doesn't completed", 422];
        }

        return response($response, 200);
    }

    public function index()
    {
        $products = Products::where('status', 1)->get();

        $productsArray = array();

        foreach ($products as $product) {

            $productItem = array();

            $productItem['id'] = $product->id;
            $productItem['name'] = $product->name;
            $productItem['price'] = $product->price;
            $productItem['discount'] = $product->discount;
            $productItem['category_id'] = $product->category;

            $productsArray[] = $productItem;
        }

        $response = [
            'message' => 'Process done',
            'products' => $productsArray];
        return response($response, 200);
    }
}
