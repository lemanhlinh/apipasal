<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessPolicy;
use Illuminate\Http\Request;
use App\Models\BusinessPolicyCampus;
use App\Models\BusinessPolicyProduct;

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

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $req = request()->all();
        $newPolicy = new BusinessPolicy();
        $newPolicy->title = $req['title'];
        $newPolicy->type_promotion = $req['type_promotion'];
        $newPolicy->promotion = $req['promotion'];
        $newPolicy->date_start = $req['date_start'];
        $newPolicy->date_end = $req['date_end'];
        $newPolicy->active = $req['active'];
        $newPolicy->save();

        $newCampusesPolicies = [];
        foreach($req['campuses_id'] as $val) {
            $newCampusesPolicy = new BusinessPolicyCampus();
            $newCampusesPolicy->campuses_id = $val;
            $newCampusesPolicy->business_policy_id = $newPolicy->id;
            $newCampusesPolicy->save();
            $newCampusesPolicies[] = $newCampusesPolicy;
        }

        $newPolicyProducts = [];
        foreach($req['products_id'] as $val) {
            $newPolicyProduct = new BusinessPolicyProduct();
            $newPolicyProduct->product_id = $val;
            $newPolicyProduct->business_policy_id = $newPolicy->id;
            $newPolicyProduct->save();
            $newPolicyProducts[] = $newPolicyProduct;
        }
        
        return response()->json([
            'message' => 'Create policy success',
            'data' => $newPolicy
        ]);
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
        $policy = BusinessPolicy::with(['campuses'])->with(['businessPolicyProduct'])->find($id);
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
    public function update(Request $request, BusinessPolicy $businessPolicy)
    {
        $id = request()->id;
        $req = request()->all();
        $policy = BusinessPolicy::find($id);

        if(!$policy) {
            return response()->json([
                'error' => true,
                'message' => 'Business policy record not found!'
            ], 404);
        }

        $policy->title = $req['title'];
        $policy->type_promotion = $req['type_promotion'];
        $policy->promotion = $req['promotion'];
        $policy->date_start = $req['date_start'];
        $policy->date_end = $req['date_end'];
        $policy->active = $req['active'];
        $policy->save();

        $campuses = BusinessPolicyCampus::where('business_policy_id', $id)->get();
        foreach($campuses as $campus) {
            $campus->delete();
        }

        $newCampusesPolicies = [];
        foreach($req['campuses_id'] as $val) {
            $newCampusesPolicy = new BusinessPolicyCampus();
            $newCampusesPolicy->campuses_id = $val;
            $newCampusesPolicy->business_policy_id = $policy->id;
            $newCampusesPolicy->save();
            $newCampusesPolicies[] = $newCampusesPolicy;
        }

        $products = BusinessPolicyProduct::where('business_policy_id', $id)->get();
        foreach($products as $product) {
            $product->delete();
        }

        $newPolicyProducts = [];
        foreach($req['products_id'] as $val) {
            $newPolicyProduct = new BusinessPolicyProduct();
            $newPolicyProduct->product_id = $val;
            $newPolicyProduct->business_policy_id = $policy->id;
            $newPolicyProduct->save();
            $newPolicyProducts[] = $newPolicyProduct;
        }

        return response()->json([
            'message' => 'Update policy success',
            'data' => $policy
        ]);
        
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
