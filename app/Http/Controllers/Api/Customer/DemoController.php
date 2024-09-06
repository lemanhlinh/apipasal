<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

use App\Models\CustomerDemo;
use App\Models\CustomerDemoCustomer;

class DemoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $demo = CustomerDemo::orderBy('id', 'DESC')
        ->where('user_id', $user->id)
        ->with([
            'user_manage' => function ($query) {
                $query->select('id', 'name', 'department_id')->with(['department' => function ($query2) {
                    $query2->select('id', 'title')->with(['campuses' => function($query3){
                        $query3->select('campuses.id', 'campuses.code');
                    }]);
                }]);
            },
            'demo' => function ($query) {
                $query->select('id', 'title');
            },
            'campuses' => function ($query) {
                $query->select('id', 'title');
            }
        ])
        ->get();

        foreach ($demo as $item) {
            $item->consulting_detail =  json_decode($item->consulting_detail);
        } 

        return response()->json(array(
            'error' => false,
            'data' => $demo,
            'message' => 'success!',
        ));
    }

    public function store(Request $request)
    {
        // return response()->json(array(
        //     'error' => false,
        //     'message' => 'Cập nhật khách hàng thành công!',
        //     'data' => $request->all()
        // ));

        DB::beginTransaction();
        try {
            $user = Auth::user(); 

            $data = [
                'title' => $request->title,
                'campuses_type' => $request->campuses_type,
                'campuses_id' => $request->campuses_type == 3 ? $request->campuses_id : 0,
                'demo_id' => $request->demo_id,
                'type' => $request->type,
                'address' => $request->address,
                'date_start' => Carbon::parse($request->date_start)->format('Y-m-d'),
                'date_end' => Carbon::parse($request->date_end)->format('Y-m-d'),
                'schedule' => $request->schedule,
                'study' => $request->study,
                'speaker_id' => 0,
                'user_manage_id' => $request->user_manage_id,
                'user_id' => $user->id,
                'active' => 1,
                'invite' => 0,
            ];

            $demo = CustomerDemo::create($data);

            DB::commit();
            return response()->json(array(
                'error' => false,
                'data' => $demo,
                'message' => 'Đã thêm mới Demo trải nghiệm!',
            ));
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::info([
                'message' => $ex->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'error' => true,
                'message' => 'Chưa thêm được Demo trải nghiệm!',
            ));
        }
    }
}
