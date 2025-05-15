<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    // declear a function index  for the api
    public function index(){

        $products = Product::get();
        if($products->count() > 0)

        {

            return ProductResource::collection($products);

        }

        else{

                return response()->json(['message' => 'No record available'], 200);
        }


    }
    // function for store product data

    public function store(Request $request){
        // validating the input field
        $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required',
                'price' => 'required|integer',

        ]);

        // adding an if statement to check if the fields are validated
        if ($validator->fails()) {
                return response()->json([
                'message' => 'All Fields are Mandatory',
                'errors'=> $validator->messages(),
            ], 422);
        }

        // create the product
        $product = Product::create([

            'name' => $request->name,
            'description' =>  $request->description,
            'price' => $request->price,
        ]);

        return response()->json([

            'message' => 'Product Created Successfully',
            'data' => new ProductResource($product)

        ],200);

    }

     // function for show product data

    public function show(Product $product){

        return new ProductResource($product);

    }

     // function for update product data
    public function update(Request $request, Product $product){

            $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'description' => 'required',
                    'price' => 'required|integer',

        ]);

        // adding an if statement to check if the fields are validated
        if ($validator->fails()) {
                    return response()->json([
                    'message' => 'All Fields are Mandatory',
                    'errors'=> $validator->messages(),
            ], 422);
        }

        // create the product
        $product->update([

                'name' => $request->name,
                'description' =>  $request->description,
                'price' => $request->price,
        ]);

        return response()->json([

                'message' => 'Product Updated Successfully',
                'data' => new ProductResource($product)

        ],200);

    }


     // function for destroy product data

    public function destroy(Product $product){
        $product->delete();
        return response()->json([

                'message' => 'Product Deleted Successfully',
                'data' => new ProductResource($product)

        ],200);

    }
}
