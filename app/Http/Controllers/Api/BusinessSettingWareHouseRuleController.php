<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessSettingSourceCustomer;
use App\Models\BusinessSettingWareHouseRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessSettingWareHouseRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return BusinessSettingWareHouseRule::orderBy('id','DESC')->get();
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
            $count_day = $request->input('count_day');
            $time_in_warehouse = $request->input('time_in_warehouse');
            BusinessSettingWareHouseRule::create([
                'count_day' => $count_day,
                'time_in_warehouse' => $time_in_warehouse,
                'active' => 1,
            ]);

            DB::commit();
            return response()->json(array(
                'error' => false,
                'result' => 'Đã thêm mới ngày vào kho',
            ));
        } catch (\Exception $ex) {
            DB::rollBack();
            \Log::info([
                'message' => $ex->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'error' => false,
                'result' => 'Chưa thêm được ngày vào kho',
            ));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BusinessSettingWareHouseRule  $businessSettingWareHouseRule
     * @return \Illuminate\Http\Response
     */
    public function show(BusinessSettingWareHouseRule $businessSettingWareHouseRule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BusinessSettingWareHouseRule  $businessSettingWareHouseRule
     * @return \Illuminate\Http\Response
     */
    public function edit(BusinessSettingWareHouseRule $businessSettingWareHouseRule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BusinessSettingWareHouseRule  $businessSettingWareHouseRule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $count_day = $request->input('count_day');
            $time_in_warehouse = $request->input('time_in_warehouse');
            $cat = BusinessSettingWareHouseRule::findOrFail($id);
            $cat->update([
                'count_day' => $count_day,
                'time_in_warehouse' => $time_in_warehouse,
                'active' => 1,
            ]);
            DB::commit();
            return response()->json(array(
                'error' => false,
                'result' => 'Cập nhật thành công',
            ));
        } catch (\Exception $exception) {
            \Log::info([
                'message' => $exception->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'error' => false,
                'result' => 'Chưa cập nhật được',
            ));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BusinessSettingWareHouseRule  $businessSettingWareHouseRule
     * @return \Illuminate\Http\Response
     */
    public function destroy(BusinessSettingWareHouseRule $businessSettingWareHouseRule)
    {
        //
    }
}
