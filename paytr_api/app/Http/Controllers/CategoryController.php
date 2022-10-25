<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Categories::where('status', 1)->orderBy('name')->get();

        $categoryArray = array();

        foreach ($categories as $category){

            $catArr['id'] = $category->id;
            $catArr['name'] = $category->name;

            $categoryArray[] = $catArr;
        }

        $response = [
            'message' =>  'Process done',
            'categories' => $categoryArray];
        return response($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        $category = new Categories();
        $category->name = $request->get('name');
        $category->userid = auth()->user()['id'];

        if ($category->save()) {
            $response = ['message' =>  'Process Done'];
        } else {
            $response = ['errors' =>  "Process doesn't completed", 422];
        }

        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'id' => 'required|integer',
        ]);

        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        $category = Categories::where('id', $request->get('id'))->first();
        $category->name = $request->get('name');

        if ($category->save()) {
            $response = ['message' =>  'Edit Process Done'];
        } else {
            $response = ['errors' =>  "Process doesn't completed", 422];
        }

        return response($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        $category = Categories::where('id', $request->get('id'))->first();
        $category->status = 0;

        if ($category->save()) {
            $response = ['message' =>  'Delete Process Done'];
        } else {
            $response = ['errors' =>  "Process doesn't completed", 422];
        }

        return response($response, 200);
    }
}
