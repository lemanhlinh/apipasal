<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessSettingDemoExperience;
use App\Models\BusinessSettingSourceCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessSettingDemoExperienceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return BusinessSettingDemoExperience::orderBy('id','DESC')->get();
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
            $code = $request->input('code');
            BusinessSettingDemoExperience::create([
                'title' => $title,
                'code' => $code,
                'active' => 1,
            ]);

            DB::commit();
            return response()->json(array(
                'error' => false,
                'result' => 'Đã thêm mới loại demo trải nghiệm',
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
                'result' => 'Chưa thêm được loại demo trải nghiệm',
            ));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BusinessSettingDemoExperience  $businessSettingDemoExperience
     * @return \Illuminate\Http\Response
     */
    public function show(BusinessSettingDemoExperience $businessSettingDemoExperience)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BusinessSettingDemoExperience  $businessSettingDemoExperience
     * @return \Illuminate\Http\Response
     */
    public function edit(BusinessSettingDemoExperience $businessSettingDemoExperience)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BusinessSettingDemoExperience  $businessSettingDemoExperience
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $title = $request->input('title');
            $code = $request->input('code');
            $cat = BusinessSettingDemoExperience::findOrFail($id);
            $cat->update([
                'title' => $title,
                'code' => $code,
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
     * @param  \App\Models\BusinessSettingDemoExperience  $businessSettingDemoExperience
     * @return \Illuminate\Http\Response
     */
    public function destroy(BusinessSettingDemoExperience $businessSettingDemoExperience)
    {
        //
    }
}
