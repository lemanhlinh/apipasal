<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessSpending;
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
        //
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
    public function edit(BusinessSpending $businessSpending)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BusinessSpending  $businessSpending
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BusinessSpending $businessSpending)
    {
        //
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
