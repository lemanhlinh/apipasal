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
    public function index(Request $request)
    {
        $query = Products::with(['cat' => function($q){
            $q->select('id','title','active');
        }])->with(['courses'])->orderBy('id','DESC');

        if ($request->has('cat_id')) {
            $query->where('cat_id', $request->cat_id);
        }
    
        $products = $query->get();

        foreach ($products as $product) {
            foreach ($product->courses as $courses) {
                $courses->ordering = $courses->pivot->ordering;
            }
        }

        return $products;
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
            $cat = $request->input('cat');
            $code = $request->input('code');
            $price = $request->input('price');
            $type = $request->input('type');
            $product = Products::create([
                'title' => $title,
                'cat_id' => $cat['id'],
                'code' => $code,
                'price' => $price,
                'type' => $type,
                'active' => 1,
            ]);

            $courses = $request->input('courses');
            if (isset($courses)) {
                foreach ($courses as $coursId) {
                    $product->courses()->attach($coursId['id'], ['ordering' => $coursId['ordering']]);
                }
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
            $cat = $request->input('cat');
            $code = $request->input('code');
            $price = $request->input('price');
            $type = $request->input('type');
            $product = Products::findOrFail($id);
            $product->update([
                'title' => $title,
                'cat_id' => $cat['id'],
                'code' => $code,
                'price' => $price,
                'type' => $type,
                'active' => 1,
            ]);

            $product->courses()->detach();

            $courses = $request->input('courses');
            if (isset($courses)) {
                foreach ($courses as $coursId) {
                    $product->courses()->attach($coursId['id'], ['ordering' => $coursId['ordering']]);
                }
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

    /**
     * @param $id
     * @return array
     */
    public function changeActive($id)
    {
        $campuses = Products::findOrFail($id);
        $campuses->update(['active' => !$campuses->active]);
        return [
            'status' => true,
            'message' => trans('message.change_active_article_success')
        ];
    }
}
