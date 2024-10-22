<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessSpending;
use App\Models\BusinessSpendingDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Campuses;
use App\Models\Department;

class BusinessSpendingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $campuse_id = request()->input("campuses_id");
        
        $spendings = BusinessSpending::with(['spendingCampuses'])
        ->where('year', date('Y'))
        ->orderBy('id', 'DESC')
        ->get();

        if($campuse_id) {
            $departments = Department::with(['user_manage','users','regencies','campuses'])->orderBy('id', 'DESC')->get()->toTree();
            $filter_departments = [];
            foreach($departments as $item) {
                foreach($item->campuses as $val) {
                    if($val->id == $campuse_id) {
                        $filter_departments[] = $item;
                        break;
                    }
                }
            }
            $departments = $filter_departments;
            
            $flatArray = [];
            $flatten = function ($departments) use (&$flatten, &$flatArray) {
                foreach ($departments as $department) {
                    $flatArray[] = $department;
                    if (isset($department['children']) && count($department['children']) > 0) {
                        $flatten($department['children']);
                    }
                }
            };
        
            $flatten($departments);
            $departmentIds = [];
            foreach ($flatArray as $item) {
                $departmentIds[] = $item->id;
            }
    
            $spendings = BusinessSpending::with(['spendingCampuses' => function ($query) use ($departmentIds) {
                $query->whereIn('department_id', $departmentIds);
            }])
            ->where('year', date('Y'))
            ->orderBy('id', 'DESC')
            ->get();
    
        }

        $tong_kpi_doanh_thu = 0;
        $tong_kpi_hoc_vien = 0;
        $tong_kpi_dataKH = 0;
        $tong_kpi_ty_le_chot = 0;

        foreach($spendings as $item) {
            foreach($item->spendingCampuses as $val) {
                $tong_kpi_doanh_thu += $val->kpi_doanh_thu;
                $tong_kpi_hoc_vien += $val->kpi_hoc_vien;
                $tong_kpi_dataKH += $val->kpi_dataKH;
                $tong_kpi_ty_le_chot += $val->kpi_ty_le_chot;

                $item->tong_kpi_doanh_thu = $tong_kpi_doanh_thu;
                $item->tong_kpi_hoc_vien = $tong_kpi_hoc_vien;
                $item->tong_kpi_dataKH = $tong_kpi_dataKH;
                $item->tong_kpi_ty_le_chot = round($tong_kpi_hoc_vien / $tong_kpi_dataKH * 100);
            }

        }
        return $spendings;
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
        try {
            DB::beginTransaction();
            $req = $request->all();
            $businessSpending = new BusinessSpending();
            $businessSpending->title = $req['title'];
            $businessSpending->month = $req['month'];
            $businessSpending->year = date('Y');
            $businessSpending->active = $req['active'];
            $businessSpending->save();

            foreach ($req['spendingCampuses'] as $department_id => $departments) {
                $spendingDepartment = new BusinessSpendingDepartment();
                $spendingDepartment->kpi_doanh_thu = $departments['kpi_doanh_thu'];
                $spendingDepartment->kpi_hoc_vien = $departments['kpi_hoc_vien'];
                $spendingDepartment->kpi_dataKH = $departments['kpi_dataKH'];
                $spendingDepartment->kpi_ty_le_chot = $departments['kpi_ty_le_chot'];
                $spendingDepartment->spending_id = $businessSpending->id;
                $spendingDepartment->department_id = (int)$department_id;
                $spendingDepartment->save();
                
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Thêm mới chỉ tiêu thành công!'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi khi thêm mới chỉ tiêu.' . $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BusinessSpending  $businessSpending
     * @return \Illuminate\Http\Response
     */
    public function show(BusinessSpending $businessSpending)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BusinessSpending  $businessSpending
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, BusinessSpending $businessSpending)
    {
        $id = $request->id;

        $businessSpending = BusinessSpending::find($id);
        if (!$businessSpending) {
            return response()->json([
                'error' => true,
                'message' => 'Business spending record not found!'
            ], 404);
        }
        $businessSpending->department = BusinessSpendingDepartment::where('spending_id', $id)->get();

        return response()->json([
            'error' => false,
            'data' => $businessSpending
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BusinessSpending  $businessSpending
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $req = $request->all();
    
            $businessSpending = BusinessSpending::findOrFail($id);
            $businessSpending->title = $req['title'];
            $businessSpending->month = $req['month'];
            $businessSpending->year = date('Y');
            $businessSpending->active = $req['active'];
            $businessSpending->save();
    
            foreach ($req['spendingCampuses'] as $department_id => $departments) {
                $spendingDepartment = BusinessSpendingDepartment::where('spending_id', $businessSpending->id)
                                                                ->where('department_id', (int)$department_id)
                                                                ->first();
                if ($spendingDepartment) {
                    $spendingDepartment->kpi_doanh_thu = $departments['kpi_doanh_thu'];
                    $spendingDepartment->kpi_hoc_vien = $departments['kpi_hoc_vien'];
                    $spendingDepartment->kpi_dataKH = $departments['kpi_dataKH'];
                    $spendingDepartment->kpi_ty_le_chot = $departments['kpi_ty_le_chot'];
                    $spendingDepartment->save();
                } else {
                    $spendingDepartment = new BusinessSpendingDepartment();
                    $spendingDepartment->kpi_doanh_thu = $departments['kpi_doanh_thu'];
                    $spendingDepartment->kpi_hoc_vien = $departments['kpi_hoc_vien'];
                    $spendingDepartment->kpi_dataKH = $departments['kpi_dataKH'];
                    $spendingDepartment->kpi_ty_le_chot = $departments['kpi_ty_le_chot'];
                    $spendingDepartment->spending_id = $businessSpending->id;
                    $spendingDepartment->department_id = (int)$department_id;
                    $spendingDepartment->save();
                }
            }
    
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật chỉ tiêu thành công!'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi khi cập nhật chỉ tiêu. ' . $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BusinessSpending  $businessSpending
     * @return \Illuminate\Http\Response
     */
    public function destroy(BusinessSpending $businessSpending)
    {
        //
    }

    public function changeActive($id)
    {
        $dataItem = BusinessSpending::findOrFail($id);
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
            $businessSpending = BusinessSpending::findOrFail($id);
            BusinessSpendingDepartment::where('spending_id', $businessSpending->id)->delete();
            $businessSpending->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa đối tác',
            ]);
        } catch (\Exception $ex) {
            DB::rollBack();
            \Log::info([
                'message' => $ex->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Chưa xoá được đối tác',
            ]);
        }
    }

}
