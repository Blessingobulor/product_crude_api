<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of all products.
     */
    public function index()
    {
        // Retrieve all products from the database
        $products = Product::get();

        // Check if there are any products
        if ($products->count() > 0) {
            // Return product collection as resource
            return ProductResource::collection($products);
        } else {
            // Return a response if no records are found
            return response()->json(['message' => 'No record available'], 200);
        }
    }

    /**
     * Store a newly created product in the database.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'required',
            'price'       => 'required|integer',
        ]);

        // Return error response if validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => 'All Fields are Mandatory',
                'errors'  => $validator->messages(),
            ], 422);
        }

        // Create the product
        $product = Product::create([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
        ]);

        // Return success response with product data
        return response()->json([
            'message' => 'Product Created Successfully',
            'data'    => new ProductResource($product),
        ], 200);
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        // Return a single product as resource
        return new ProductResource($product);
    }

    /**
     * Update the specified product in the database.
     */
    public function update(Request $request, Product $product)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'required',
            'price'       => 'required|integer',
        ]);

        // Return error response if validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => 'All Fields are Mandatory',
                'errors'  => $validator->messages(),
            ], 422);
        }

        // Update the product with validated data
        $product->update([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
        ]);

        // Return success response with updated product data
        return response()->json([
            'message' => 'Product Updated Successfully',
            'data'    => new ProductResource($product),
        ], 200);
    }

    /**
     * Remove the specified product from the database.
     */
    public function destroy(Product $product)
    {
        // Delete the product
        $product->delete();

        // Return success response with deleted product data
        return response()->json([
            'message' => 'Product Deleted Successfully',
            'data'    => new ProductResource($product),
        ], 200);
    }
}
