<?php

namespace App\Http\Controllers\api\v1\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Subcategory;
use App\Category;
use Validator;

class SubcategoriesController extends Controller
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
        $subcategories = Subcategory::all();
        if(is_null($subcategories)){
            return response()->json(['status' => 'error', 'message' => 'No subcategories to show', 'data' => null], 404);
        }
        return response()->json(['status' => 'success', 'message' => null, 'data' => (object)['subcategories' => $subcategories]], 200);
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
            'category_id' => 'required|numeric',
            'subcategory' => 'required|unique:subcategories|regex:/^[A-Za-z0-9 -]+$/',
            'slug' => 'required|regex:/^[A-Za-z0-9-]+$/',
        ];
        
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json(['status' => 'error', 'message' => 'Invalid credentials', 'data' => (object)[$validator->errors()]], 400);
        }

        $category = Category::find($request->get('category_id'));
        if(is_null($category)){
            return response()->json(['status' => 'error', 'message' => 'Category not found', 'data' => null], 404);
        }

        $subcategory = Subcategory::create([
            'category_id' => $request->get('category_id'),
            'subcategory' => $request->get('subcategory'),
            'slug' => $request->get('slug'),
            'count' => 0,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Sub-category created', 'data' => (object)['subcategory' => $subcategory]], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $subcategory = Subcategory::find($id);
        if(is_null($subcategory)){
            return response()->json(['status' => 'error', 'message' => 'Sub-category not found', 'data' => null], 404);
        }
        return response()->json(['status' => 'success', 'message' => null, 'data' => (object)['subcategory' => $subcategory]], 200);
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
        $rules = [
            'subcategory' => 'required|unique:subcategories|regex:/^[A-Za-z0-9 -]+$/',
            'slug' => 'required|regex:/^[A-Za-z0-9-]+$/',
        ];
        
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json(['status' => 'error', 'message' => 'Invalid credentials', 'data' => (object)[$validator->errors()]], 400);
        }

        $subcategory = Subcategory::find($id);
        if(is_null($subcategory)){
            return response()->json(['status' => 'error', 'message' => 'Sub-category not found', 'data' => null], 404);
        }

        $subcategory->subcategory = ( empty($request->get('subcategory')) ) ? $subcategory->subcategory : $request->get('subcategory');
        $subcategory->slug = ( empty($request->get('slug')) ) ? $subcategory->slug : $request->get('slug');

        return response()->json(['status' => 'success', 'message' => 'Sub-category updated', 'data' => (object)['subcategory' => $subcategory]], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subcategory = Subcategory::find($id);
        if(is_null($subcategory)){
            return response()->json(['status' => 'error', 'message' => 'Sub-category not found', 'data' => null], 404);
        }
        $subcategory->delete();
        return response()->json(['status' => 'success', 'message' => 'Sub-category deleted', 'data' => null], 200);
    }
}
