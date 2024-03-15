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
        $product = Products::with('cat')->get();
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
    public function store(CreateProduct $req)
    {
        DB::beginTransaction();
        try {
            $data = $req->validated();
            $product = Products::create([
                'title' => $data['title'],
                'cat_id' => $data['cat_id'],
                'code' => $data['code'],
                'price' => $data['price'],
                'type' => $data['type'],
                'active' => 1,
            ]);
            $courses = $data['courses'];
            foreach ($courses as $class){
                $product->productCourses()->create([
                    'product_id' => $product->id,
                    'course_id' => $class->course_id,
                    'ordering' => $class->ordering,
                ]);
            }

            DB::commit();
            Session::flash('success', 'Đã thêm mới sản phẩm');
            return redirect()->back();
        } catch (\Exception $ex) {
            DB::rollBack();
            \Log::info([
                'message' => $ex->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            Session::flash('danger', 'Chưa thêm được sản phẩm');
            return redirect()->back();
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
    public function update(UpdateProduct $req, $id)
    {
        DB::beginTransaction();
        try {
            $data = $req->validated();
            $product = Products::findOrFail($id);
            $product->update([
                'title' => $data['title'],
                'cat_id' => $data['cat_id'],
                'code' => $data['code'],
                'price' => $data['price'],
                'type' => $data['type'],
                'active' => 1,
            ]);

            $courses = $data['courses'];
            foreach ($courses as $class){
                $product->productCourses()->updateOrCreate([
                    'product_id' => $product->id,
                    'course_id' => $class->course_id,
                    'ordering' => $class->ordering,
                ]);
            }
            DB::commit();
            Session::flash('success', 'Cập nhật thành công');
            return redirect()->back();
        } catch (\Exception $exception) {
            \Log::info([
                'message' => $exception->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            Session::flash('danger', 'Chưa cập nhật được');
            return back();
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
