<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessSpending;
use App\Models\BusinessSpendingDepartment;
use Illuminate\Http\Request;

class BusinessSpendingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $spendings = BusinessSpending::with(['spendingCampuses'])->orderBy('id', 'DESC')->get();
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
        $req = $request->all();
    
        foreach ($req as $item) {
            $businessSpending = new BusinessSpending();
            $businessSpending->title = $item['title'];
            $businessSpending->month = $item['month'];
            $businessSpending->active = $item['active'];
            $businessSpending->save();
    
            foreach ($item['department'] as $department) {
                $spendingDepartment = new BusinessSpendingDepartment();
                $spendingDepartment->revenue_expenditure = $department['revenue_expenditure'];
                $spendingDepartment->student_expenses = $department['student_expenses'];
                $spendingDepartment->customer_expenses = $department['customer_expenses'];
                $spendingDepartment->ti_le_chot_expenses = $department['ti_le_chot_expenses'];
                $spendingDepartment->spending_id = $businessSpending->id;
                $spendingDepartment->department_id = $department['department_id'];
                $spendingDepartment->save();
            }
        }
    
        return response()->json([
            'error' => false,
            'message' => 'Business spending records created successfully!'
        ], 201);
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
        if(!$businessSpending) {
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
        $req = $request->all();
    
        $businessSpending = BusinessSpending::find($id);
        if ($businessSpending) {
            foreach ($req as $item) {
                $businessSpending->title = $item['title'];
                $businessSpending->month = $item['month'];
                $businessSpending->active = $item['active'];
                $businessSpending->save();
    
                foreach ($item['department'] as $department) {
                    $spendingDepartment = BusinessSpendingDepartment::where('spending_id', $department['spending_id'])
                        ->where('department_id', $department['department_id'])
                        ->first();
    
                    if ($spendingDepartment) {
                        $spendingDepartment->revenue_expenditure = $department['revenue_expenditure'];
                        $spendingDepartment->student_expenses = $department['student_expenses'];
                        $spendingDepartment->customer_expenses = $department['customer_expenses'];
                        $spendingDepartment->ti_le_chot_expenses = $department['ti_le_chot_expenses'];
                        $spendingDepartment->save();
                    } else {
                        $spendingDepartment = new BusinessSpendingDepartment();
                        $spendingDepartment->revenue_expenditure = $department['revenue_expenditure'];
                        $spendingDepartment->student_expenses = $department['student_expenses'];
                        $spendingDepartment->customer_expenses = $department['customer_expenses'];
                        $spendingDepartment->ti_le_chot_expenses = $department['ti_le_chot_expenses'];
                        $spendingDepartment->spending_id = $department['spending_id'];
                        $spendingDepartment->department_id = $department['department_id'];
                        $spendingDepartment->save();
                    }
                }
            }
    
            return response()->json([
                'error' => false,
                'message' => 'Business spending records updated successfully!'
            ], 200);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Business spending record not found!'
            ], 404);
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
}
