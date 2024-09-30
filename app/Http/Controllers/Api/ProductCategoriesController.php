<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\CreateProductCategory;
use App\Http\Requests\Products\UpdateProductCategory;
use App\Models\CourseCategories;
use App\Models\ProductCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ProductCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cat = ProductCategories::with('products')->orderBy('id','DESC')->get();
        return $cat;
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
            ProductCategories::create([
                'title' => $title,
                'active' => 1,
            ]);

            DB::commit();
            return response()->json(array(
                'error' => false,
                'result' => 'Đã thêm mới nhóm sản phẩm',
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
                'result' => 'Chưa thêm được nhóm sản phẩm',
            ));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductCategories  $productCategories
     * @return \Illuminate\Http\Response
     */
    public function show(ProductCategories $productCategories)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductCategories  $productCategories
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductCategories $productCategories)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductCategories  $productCategories
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $title = $request->input('title');
            $cat = ProductCategories::findOrFail($id);
            $cat->update([
                'title' => $title,
                'active' => 1,
            ]);
            DB::commit();
            return response()->json(array(
                'error' => true,
                'result' => 'Cập nhật thành công',
            ));
        } catch (\Exception $exception) {
            \Log::info([
                'message' => $exception->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'error' => true,
                'result' => 'Chưa cập nhật được',
            ));
        }
    }

    public function changeActive($id)
    {
        $campuses = ProductCategories::findOrFail($id);
        $campuses->update(['active' => !$campuses->active]);
        return [
            'status' => true,
            'message' => trans('message.change_active_article_success')
        ];
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductCategories  $productCategories
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductCategories $productCategories)
    {
        //
    }

    public function delete($id) {
        $cat = ProductCategories::findOrFail($id);
        $cat->delete();
        return response()->json(array(
            'error' => false,
            'result' => 'Đã xóa nhóm sản phẩm',
        ));
    }
}
