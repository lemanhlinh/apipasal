<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list_permission = Permission::orderBy('display_name','ASC')->get();
        return $list_permission;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function rolePermission()
    {
        $role_id = request()->role_id;
        $list_permission = Permission::orderBy('display_name','ASC')->with('role_permission', function($query) use ($role_id) {
            $query->where('role_id', $role_id);
        })->get();

        return $list_permission;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request  $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TimeStudy  $timeStudy
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TimeStudy  $timeStudy
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TimeStudy  $timeStudy
     * @return \Illuminate\Http\Response
     */
    public function update(Request  $request, $id)
    {
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TimeStudy  $timeStudy
     * @return \Illuminate\Http\Response
     */

}
