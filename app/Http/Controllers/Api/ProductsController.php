<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\CreateProduct;
use App\Http\Requests\Products\UpdateProduct;
use App\Models\ProductCategories;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $product = Products::with(['cat','productCourses'])->orderBy('id','DESC')->get();
        return $product;
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
        DB::beginTransaction();
        try {
            $title = $request->input('title');
            $cat_id = $request->input('cat_id');
            $code = $request->input('code');
            $price = $request->input('price');
            $type = $request->input('type');
            $product = Products::create([
                'title' => $title,
                'cat_id' => $cat_id,
                'code' => $code,
                'price' => $price,
                'type' => $type,
                'active' => 1,
            ]);
            $courses = $request->input('courses');
            foreach ($courses as $class){
                $product->productCourses()->create([
                    'product_id' => $product->id,
                    'course_id' => $class->course_id,
                    'ordering' => $class->ordering,
                ]);
            }

            DB::commit();
            return response()->json(array(
                'error' => false,
                'message' => 'Đã thêm mới sản phẩm'
            ));
        } catch (\Exception $ex) {
            DB::rollBack();
            \Log::info([
                'message' => $ex->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'error' => true,
                'message' => 'Chưa thêm được sản phẩm'
            ));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function show(Products $products)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function edit(Products $products)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $title = $request->input('title');
            $cat_id = $request->input('cat_id');
            $code = $request->input('code');
            $price = $request->input('price');
            $type = $request->input('type');
            $product = Products::findOrFail($id);
            $product->update([
                'title' => $title,
                'cat_id' => $cat_id,
                'code' => $code,
                'price' => $price,
                'type' => $type,
                'active' => 1,
            ]);

            $courses = $request->input('courses');
            foreach ($courses as $class){
                $product->productCourses()->updateOrCreate([
                    'product_id' => $product->id,
                    'course_id' => $class->course_id,
                    'ordering' => $class->ordering,
                ]);
            }
            DB::commit();
            return response()->json(array(
                'error' => false,
                'message' => 'Cập nhật thành công'
            ));
        } catch (\Exception $exception) {
            \Log::info([
                'message' => $exception->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'error' => true,
                'message' => 'Chưa cập nhật được'
            ));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function destroy(Products $products)
    {
        //
    }
}
