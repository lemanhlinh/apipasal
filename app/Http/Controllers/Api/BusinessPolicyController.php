<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessPolicy;
use Illuminate\Http\Request;
use App\Models\BusinessPolicyCampus;
use App\Models\BusinessPolicyProduct;
use Illuminate\Support\Facades\DB;
use App\Models\Campuses;
use App\Models\Products;
use Carbon\Carbon;

class BusinessPolicyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $campuses_id = request()->campuses_id;

        $q = BusinessPolicy::with(['campuses', 'businessPolicyProducts.product'])->whereHas('campuses', function($query) use ($campuses_id) {
            if ($campuses_id) {
                $query->where('campuses_id', $campuses_id);
            }
        });

        $type = request()->type;
        if($type) {
            $q->where('type', $type);
        }

        $month = request()->month;
        if ($month) {
            $startDate = Carbon::create(null, $month, 1)->startOfMonth()->toDateString();
            $endDate = Carbon::create(null, $month, 1)->endOfMonth()->toDateString();
            $q->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('date_start', [$startDate, $endDate])
                      ->orWhereBetween('date_end', [$startDate, $endDate])
                      ->orWhere(function($query) use ($startDate, $endDate) {
                          $query->where('date_start', '<=', $startDate)
                                ->where('date_end', '>=', $endDate);
                      });
            });
        }

        $policy = $q->orderBy('id', 'DESC')->paginate(100);

        return response()->json([
            'success' => true,
            'data' => $policy
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        try {
            DB::beginTransaction();
            $req = request()->all();
    
            $newPolicy = BusinessPolicy::create([
                'title' => $req['title'],
                'type_promotion' => $req['type_promotion'],
                'promotion' => $req['promotion'],
                'date_start' => $req['date_start'],
                'date_end' => $req['date_end'],
                'active' => $req['active'],
                'type' => $req['type']
            ]);
    
            $campusesIds = $req['allCampuses'] == 1 ? Campuses::pluck('id')->toArray() : $req['campuses_id'];
            $campusesPolicies = array_map(function($campusId) use ($newPolicy) {
                return [
                    'campuses_id' => $campusId,
                    'business_policy_id' => $newPolicy->id
                ];
            }, $campusesIds);
            BusinessPolicyCampus::insert($campusesPolicies);
    
            $productsIds = $req['allProducts'] == 1 ? Products::pluck('id')->toArray() : $req['products_id'];
            $policyProducts = array_map(function($productId) use ($newPolicy) {
                return [
                    'product_id' => $productId,
                    'business_policy_id' => $newPolicy->id
                ];
            }, $productsIds);
            BusinessPolicyProduct::insert($policyProducts);
    
            DB::commit();
            return response()->json([
                'message' => 'Đã thêm mới chính sách',
                'data' => $newPolicy,
                'success' => true
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Thêm mới chính sách thất bại: ' . $e->getMessage(),
                'success' => false
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BusinessPolicy  $businessPolicy
     * @return \Illuminate\Http\Response
     */
    public function show(BusinessPolicy $businessPolicy)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BusinessPolicy  $businessPolicy
     * @return \Illuminate\Http\Response
     */
    public function edit(BusinessPolicy $businessPolicy)
    {
        $id = request()->id;
        $policy = BusinessPolicy::with(['campuses'])->with(['businessPolicyProducts.product'])->find($id);
        if(!$policy) {
            return response()->json([
                'error' => true,
                'message' => 'Business policy record not found!'
            ], 404);
        }

        return response()->json([
            'error' => false,
            'data' => $policy
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BusinessPolicy  $businessPolicy
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        try {
            DB::beginTransaction();
            $req = request()->all();
    
            $policy = BusinessPolicy::findOrFail($id);
    
            $policy->update([
                'title' => $req['title'],
                'type_promotion' => $req['type_promotion'],
                'promotion' => $req['promotion'],
                'date_start' => $req['date_start'],
                'date_end' => $req['date_end'],
                'active' => $req['active'],
                'type' => $req['type']
            ]);
    
            BusinessPolicyCampus::where('business_policy_id', $policy->id)->delete();
    
            $campusesIds = $req['allCampuses'] == 1 ? Campuses::pluck('id')->toArray() : $req['campuses_id'];
            $campusesPolicies = array_map(function($campusId) use ($policy) {
                return [
                    'campuses_id' => $campusId,
                    'business_policy_id' => $policy->id
                ];
            }, $campusesIds);
            BusinessPolicyCampus::insert($campusesPolicies);
    
            BusinessPolicyProduct::where('business_policy_id', $policy->id)->delete();
    
            $productsIds = $req['allProducts'] == 1 ? Products::pluck('id')->toArray() : $req['products_id'];
            $policyProducts = array_map(function($productId) use ($policy) {
                return [
                    'product_id' => $productId,
                    'business_policy_id' => $policy->id
                ];
            }, $productsIds);
            BusinessPolicyProduct::insert($policyProducts);
    
            DB::commit();
            return response()->json([
                'message' => 'Đã cập nhật chính sách',
                'data' => $policy,
                'success' => true
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Cập nhật chính sách thất bại: ' . $e->getMessage(),
                'success' => false
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BusinessPolicy  $businessPolicy
     * @return \Illuminate\Http\Response
     */
    public function destroy(BusinessPolicy $businessPolicy)
    {
        //
    }

    public function changeActive($id)
    {
        $dataItem = BusinessPolicy::findOrFail($id);
        $dataItem->update(['active' => !$dataItem->active]);
        return [
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công'
        ];
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $data = BusinessPolicy::findOrFail($id);
            $data->delete();
            DB::commit();
            return response()->json(array(
                'success' => true,
                'message' => 'Đã xóa đối tác',
            ));
        } catch (\Exception $ex) {
            DB::rollBack();
            \Log::info([
                'message' => $ex->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'success' => false,
                'message' => 'Chưa xoá được đối tác',
            ));
        }
    }
}
