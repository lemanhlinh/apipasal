<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessPolicy;
use Illuminate\Http\Request;

class BusinessPolicyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $policy = BusinessPolicy::with(['campuses'])->with(['businessPolicyProduct'])->orderBy('id', 'DESC')->get();
        return $policy;
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BusinessPolicy  $businessPolicy
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BusinessPolicy $businessPolicy)
    {
        //
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
}
