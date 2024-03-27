<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessPartner;
use Illuminate\Http\Request;

class BusinessPartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $partners = BusinessPartner::with(['clue','campuses'])->orderBy('id', 'DESC')->get();
        return $partners;
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
     * @param  \App\Models\BusinessPartner  $businessPartner
     * @return \Illuminate\Http\Response
     */
    public function show(BusinessPartner $businessPartner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BusinessPartner  $businessPartner
     * @return \Illuminate\Http\Response
     */
    public function edit(BusinessPartner $businessPartner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BusinessPartner  $businessPartner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BusinessPartner $businessPartner)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BusinessPartner  $businessPartner
     * @return \Illuminate\Http\Response
     */
    public function destroy(BusinessPartner $businessPartner)
    {
        //
    }
}
