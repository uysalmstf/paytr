<?php

namespace App\Http\Controllers;

use App\Models\Favourites;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavouriteController extends Controller
{
    public function addOrRemoveFromFavourites(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $isAddedFavourites = Favourites::where('product_id', $request->get('product_id'))->where('user_id', auth()->user()['id'])->first();
        if ($isAddedFavourites != null) {

            if (Favourites::destroy($isAddedFavourites->id)) {
                $response = ['message' => 'Remove favourite Process Done'];
            } else {
                $response = ['errors' => "Process doesn't completed", 422];
            }
        } else {

            $favourite = new Favourites();
            $favourite->user_id = auth()->user()['id'];
            $favourite->product_id = $request->get('product_id');

            if ($favourite->save()) {
                $response = ['message' => 'Add Favourites Process Done'];
            } else {
                $response = ['errors' => "Process doesn't completed", 422];
            }
        }

        return response($response, 200);

    }

    public function index()
    {
        $userFavouritesList = Favourites::where('user_id', auth()->user()['id'])->get();

        $userFavList = array();

        foreach ($userFavouritesList as $item) {

            $favItem = array();

            $favItem['product_id'] = $item->product_id;

            $userFavList[] = $favItem;
        }

        $response = [
            'message' => 'Process done',
            'favs' => $userFavList];
        return response($response, 200);
    }
}
