<?php

namespace App\Http\Controllers\api\v1\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;
use Validator;

class CategoriesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();
        if(is_null($categories)){
            return response()->json(['status' => 'error', 'message' => 'No products to show', 'data' => null], 404);
        }
        return response()->json(['status' => 'success', 'message' => null, 'data' => (object)['categories' => $categories]], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'category' => 'required|unique:categories|regex:/^[A-Za-z0-9 -]+$/',
            'slug' => 'required|regex:/^[A-Za-z0-9-]+$/',
        ];
        
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json(['status' => 'error', 'message' => 'Invalid credentials', 'data' => (object)[$validator->errors()]], 400);
        }

        $category = Category::create([
            'category' => $request->get('category'),
            'slug' => $request->get('slug'),
            'count' => 0,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Category saved', 'data' => (object)['category' => $category]], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::find($id);
        if(is_null($category)){
            return response()->json(['status' => 'error', 'message' => 'Category not found', 'data' => null], 404);
        }
        return response()->json(['status' => 'success', 'message' => null, 'data' => (object)['category' => $category]], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if(is_null($category)){
            return response()->json(['status' => 'error', 'message' => 'Category not found', 'data' => null], 404);
        }
        $rules = [
            'category' => 'nullable|regex:/^[A-Za-z0-9 -]+$/',
            'slug' => 'nullable|regex:/^[A-Za-z0-9-]+$/',
        ];
        
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json(['status' => 'error', 'message' => 'Invalid credentials', 'data' => (object)[$validator->errors()]], 400);
        }

        $category->category = ( empty($request->get('category')) ) ? $category->category : $request->get('category');
        $category->slug = ( empty($request->get('slug')) ) ? $category->slug : $request->get('slug');

        $category->save();

        return response()->json(['status' => 'success', 'message' => 'Category updated', 'data' => (object)['category' => $category]], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if(is_null($category)){
            return response()->json(['status' => 'error', 'message' => 'Category not found', 'data' => null], 404);
        }
        $category->delete();
        return response()->json(['status' => 'success', 'message' => 'Category deleted', 'data' => null], 200);
    }
}
