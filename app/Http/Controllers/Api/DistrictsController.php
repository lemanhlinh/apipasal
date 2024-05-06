<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\District;

class DistrictsController extends Controller
{
    public function index(Request $request)
    {
        return District::select('name', 'code', 'city_code')
        ->where(function ($query) use ($request) {
            if ($request->city_code) {
                $query->where("city_code", $request->city_code);
            }
        })
        ->orderBy('name','ASC')->get();
    }
}
