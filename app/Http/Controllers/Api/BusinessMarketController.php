<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessMarket;
use Illuminate\Http\Request;

class BusinessMarketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $markets = BusinessMarket::with(['volume','facebook','history'])->orderBy('id', 'DESC')->get();
        return $markets;
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
     * @param  \App\Models\BusinessMarket  $businessMarket
     * @return \Illuminate\Http\Response
     */
    public function show(BusinessMarket $businessMarket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BusinessMarket  $businessMarket
     * @return \Illuminate\Http\Response
     */
    public function edit(BusinessMarket $businessMarket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BusinessMarket  $businessMarket
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BusinessMarket $businessMarket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BusinessMarket  $businessMarket
     * @return \Illuminate\Http\Response
     */
    public function destroy(BusinessMarket $businessMarket)
    {
        //
    }
}
