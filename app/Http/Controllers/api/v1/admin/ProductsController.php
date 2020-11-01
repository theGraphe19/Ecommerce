<?php

namespace App\Http\Controllers\api\v1\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ProductImage;
use App\Product;
use Validator;

class ProductsController extends Controller
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
        $products = Product::all();
        if(is_null($products)){
            return response()->json(['status' => 'error', 'message' => 'No products to show', 'data' => null], 404);
        }
        return response()->json(['status' => 'success', 'message' => null, 'data' => (object)['products' => $products]], 200);
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
            'uniq_id' => 'required|unique:products|regex:/^[A-Za-z0-9]+$/',
            'name' => 'required|regex:/^[A-Za-z0-9 -]+$/',
            'tagline' => 'required|regex:/^[A-Za-z0-9 -]+$/',
            'description' => 'required|regex:/^[A-Za-z0-9 -]+$/',
            'price' => 'required|numeric',
            'category_id' => 'required|numeric',
            'subcategory_id' => 'required|numeric',
            'status' => 'required|in:0,1',
        ];
        
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json(['status' => 'error', 'message' => 'Invalid credentials', 'data' => (object)[$validator->errors()]], 400);
        }

        $product = Product::create([
            'uniq_id' => $request->get('uniq_id'),
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'price' => $request->get('price'),
            'status' => $request->get('status'),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Product saved', 'data' => (object)['product' => $product]], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);
        if(is_null($product)){
            return response()->json(['status' => 'error', 'message' => 'Product not found', 'data' => null], 404);
        }
        return response()->json(['status' => 'success', 'message' => null, 'data' => (object)['product' => $product]], 200);
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
        
        $product = Product::find($id);
        if(is_null($product)){
            return response()->json(['status' => 'error', 'message' => 'Product not found', 'data' => null], 404);
        }
        $rules = [
            'uniq_id' => 'nullable|unique:products|regex:/^[A-Za-z0-9]+$/',
            'name' => 'nullable|regex:/^[A-Za-z0-9 -]+$/',
            'description' => 'nullable|regex:/^[A-Za-z0-9 -]+$/',
            'price' => 'nullable|numeric',
            'status' => 'nullable|in:0,1',
        ];
        
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json(['status' => 'error', 'message' => 'Invalid credentials', 'data' => (object)[$validator->errors()]], 400);
        }

        $product->uniq_id = ( empty($request->get('uniq_id')) ) ? $product->uniq_id : $request->get('uniq_id');
        $product->name = ( empty($request->get('name')) ) ? $product->name : $request->get('name');
        $product->description = ( empty($request->get('description')) ) ? $product->description : $request->get('description');
        $product->price = ( empty($request->get('price')) ) ? $product->price : $request->get('price');
        $product->status = ( empty($request->get('status')) ) ? $product->status : $request->get('status');

        $product->save();

        return response()->json(['status' => 'success', 'message' => 'Product saved', 'data' => (object)['product' => $product]], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if(is_null($product)){
            return response()->json(['status' => 'error', 'message' => 'Product not found', 'data' => null], 404);
        }
        $product->delete();
        return response()->json(['status' => 'success', 'message' => 'Product deleted', 'data' => null], 200);
    }

    public function addimage(Request $request)
    {
        $rules = [
            'prod_id' => 'required|numeric',
            'file' => 'required',
            'type' => 'required|in:main,thumbnail,gallery'
        ];
        
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json(['status' => 'error', 'message' => 'Invalid credentials', 'data' => (object)[$validator->errors()]], 400);
        }

        $product = Product::find($prod_id);
        if(is_null($product)){
            return response()->json(['status' => 'error', 'message' => 'Product not found', 'data' => null], 404);
        }

        if($request->hasFile('file')) {

            $file = $request->file('file');
            $mimetype = $file->getMimeType();

            if(strpos($mimetype, 'video') !== false) {
                
                //if type=main - not allowed - main image has to be image only
                if($request->get('type') == 'main') {
                    return response()->json(['status' => 'error', 'message' => "Main image has to be of image type", 'data' => null], 400);
                }

                $fileNameWithExt = $file->getClientOriginalName();
                $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
                $filename = str_replace(' ', '', $filename);
                $extension = $file->getClientOriginalExtension();
                $fileNameToStore = $filename.'_'.time().'.'.$extension;
                Storage::disk('local')->putFileAs('product_images', $request->file('file'), $fileNameToStore);
                $content = ProductImage::create([
                    'prod_id' => $product->id,
                    'uniq_id' => $product->uniq_id,
                    'file' => $fileNameToStore,
                    'type' => $request->get('type'),
                    'mime' => "video",
                ]);
                return response()->json(['status' => 'success', 'message' => 'Video saved succesfully', 'data' => (object)['link' => "https://developers.thegraphe.com/member-directory/storage/app/content/".$fileNameToStore]], 200);
            }
            if(strpos($mimetype, 'image') !== false) {

                if($request->get('type') == 'main'){

                    //create thumbnail and save both original as well as thumbnail

                    $fileNameWithExt = $file->getClientOriginalName();
                    $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
                    $filename = str_replace(' ', '', $filename);
                    $extension = $file->getClientOriginalExtension();

                    //name and thumbnail
                    $fileNameToStore = $filename.'_'.time().'.'.$extension;
                    $thumbnail = $filename.'_'.time().'.'.$extension;

                    //store image in folders
                    Storage::disk('local')->putFileAs('products/', $request->file('file'), $fileNameToStore);
                    Storage::disk('local')->putFileAs('products/thumbnails', $request->file('file'), $thumbnail);
                    
                    //resize
                    $mediumthumbnailpath = public_path('storage/app/products/thumbnail/'.$mediumthumbnail);
                    $this->createThumbnail($mediumthumbnailpath, 300, 185);

                    //save in db
                    $prod_img = ProductImage::create([
                        'prod_id' => $product->id,
                        'uniq_id' => $product->uniq_id,
                        'file' => $fileNameToStore,
                        'type' => $request->get('type'),
                    ]);
                } else {
                    $fileNameWithExt = $file->getClientOriginalName();
                    $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
                    $filename = str_replace(' ', '', $filename);
                    $extension = $file->getClientOriginalExtension();

                    //name and thumbnail
                    $fileNameToStore = $filename.'_'.time().'.'.$extension;

                    //store image in folders
                    Storage::disk('local')->putFileAs('products/', $request->file('file'), $fileNameToStore);

                    //save in db
                    $prod_img = ProductImage::create([
                        'prod_id' => $product->id,
                        'uniq_id' => $product->uniq_id,
                        'file' => $fileNameToStore,
                        'type' => $request->get('type'),
                    ]);
                }
                
                return response()->json(['status' => 'success', 'message' => 'Image saved succesfully', 'data' => (object)['link' => "https://developers.thegraphe.com/member-directory/storage/app/content/".$fileNameToStore]], 200);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Text not allowed', 'data' => null], 400);
        }
    }
    public function createThumbnail($path, $width, $height)
    {
        $img = Image::make($path)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save($path);
    }
}
