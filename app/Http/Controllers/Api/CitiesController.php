<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cities;

class CitiesController extends Controller
{
    public function index(Request $request)
    {
        return Cities::select('name', 'code')->orderBy('name','ASC')->get();
    }
}
