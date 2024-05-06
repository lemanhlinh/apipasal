<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Countries;

class CountriesController extends Controller
{
    public function index(Request $request)
    {
        return Countries::select('id', 'nicename as name')->orderBy('nicename','ASC')->get();
    }
}
