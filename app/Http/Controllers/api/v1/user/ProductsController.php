<?php

namespace App\Http\Controllers\api\v1\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;

class ProductsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function index()
    {
        $products = Product::all();
        if(is_null($products)){
            return response()->json(['status' => 'error', 'message' => 'No products to show', 'data' => null], 404);
        }
        return response()->json(['status' => 'success', 'message' => null, 'data' => (object)['products' => $products]], 200);
    }
}
