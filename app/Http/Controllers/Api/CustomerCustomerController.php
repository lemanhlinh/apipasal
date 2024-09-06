<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class CustomerCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $customer = CustomerCustomer::orderBy('id', 'DESC')
        ->where('manage_id', $user->id)
        ->with([
            'management' => function ($query) {
                $query->select('id', 'name', 'department_id')->with(['department' => function ($query2) {
                    $query2->select('id', 'title')->with(['campuses' => function($query3){
                        $query3->select('campuses.id', 'campuses.code');
                    }]);
                }]);
            },
            'source_info' => function ($query) {
                $query->select('id', 'title', 'code');
            }
        ])
        ->get();

        foreach ($customer as $item) {
            $item->consulting_detail =  json_decode($item->consulting_detail);
        }

        return $customer;
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
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $request = (object) $request->all();
            $segmentDetail = [];

            switch ($request->segment) {
                case 1:
                    $segmentDetail = [
                        'children' => $request->segmentInfo['children'], 
                    ];
                    break;
                case 2:
                    $segmentDetail = [
                        'children' => $request->segmentInfo['children'], 
                    ];
                    break;
                case 3:
                    $segmentDetail = [
                        'academic_year' => $request->segmentInfo['academic_year'],
                        'district' => $request->segmentInfo['district'],
                        'district_name' => $request->segmentInfo['district_name'],
                        'school' => $request->segmentInfo['school'],
                        'school_name' => $request->segmentInfo['school_name'],
                        'class' => $request->segmentInfo['class'],
                        'parent' => $request->segmentInfo['parent'],
                    ];
                    break;
                case 4:
                    $segmentDetail = [
                        'academic_year' => $request->segmentInfo['academic_year'],
                        'district' => $request->segmentInfo['district'],
                        'district_name' => $request->segmentInfo['district_name'],
                        'school' => $request->segmentInfo['school'],
                        'school_name' => $request->segmentInfo['school_name'],
                        'major' => $request->segmentInfo['major'],
                        'major_name' => $request->segmentInfo['major_name'],
                    ];
                    break;
                case 5:
                    $segmentDetail = [
                        'company' => $request->segmentInfo['company'],
                        'position' => $request->segmentInfo['position'],
                        'work' => $request->segmentInfo['work'],
                    ];
                    break;
            }


            $data = [
                'title' => $request->title,
                'phone' => $request->phone,
                'email' => $request->email,
                'sex' => $request->sex,
                'year_birth' => $request->year_birth,
                'country' => $request->country,
                'city' => $request->city,
                'district' => $request->district,
                'address' => $request->address,
                'segment' => $request->segment,
                'segment_detail' => json_encode($segmentDetail),
                'source' => $request->source,
                'source_detail' => $request->source_detail,
                'issue' => $request->issue,
                'consulting_detail' => json_encode($request->consulting_detail),
                'consulting' => $request->consulting,
                'potential' => $request->potential,
                'date_registration' => Carbon::createFromFormat('dmY', $request->date_registration)->format('Y-m-d'),
                'product_category' => $request->product_category,
                'product' => $request->product,
                'contract' => $request->contract ? 1 : 0,
                'manage_id' => $user->id,
                'active' => 1,
            ];

            $customer = CustomerCustomer::create($data);

            DB::commit();
            return response()->json(array(
                'error' => false,
                'data' => $customer,
                'result' => 'Đã thêm mới khách hàng!',
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
                'result' => 'Chưa thêm được khách hàng!',
            ));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CustomerCustomer  $businessPartner
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerCustomer $businessPartner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // return response()->json(array(
        //     'error' => false,
        //     'message' => 'Cập nhật khách hàng thành công!',
        //     'data' => $request->all()
        // ));

        DB::beginTransaction();
        try {
            $data = [];

            if ($request->issue) {
                $data['issue'] = $request->issue;
            }

            if ($request->consulting) {
                $data['consulting'] = $request->consulting;
            }

            if ($request->potential) {
                $data['potential'] = $request->potential;
            }

            if ($request->consulting_detail) {
                $data['consulting_detail'] = json_encode($request->consulting_detail);
            }

            if ($request->consulting_date) {
                $data['consulting_date'] = Carbon::createFromFormat('dmY', $request->consulting_date)->format('Y-m-d');
            }

            if (isset($request->contract)) {
                $data['contract'] = $request->contract ? 1 : 0;
            }

            if ($request->product) {
                $data['product'] = $request->product;
            }

            if ($request->product_category) {
                $data['product_category'] = $request->product_category;
            }

            if ($request->date_registration) {
                $data['date_registration'] = Carbon::createFromFormat('d/m/Y', $request->date_registration)->format('Y-m-d');
            }

            $customer = CustomerCustomer::findOrFail($request->id);

            $customer->update($data); 

            DB::commit();
            return response()->json(array(
                'error' => false,
                'message' => 'Cập nhật khách hàng thành công!',
                'data' => $data
            ));
        } catch (\Exception $exception) {
            Log::info([
                'message' => $exception->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'error' => true,
                'message' => 'Cập nhật khách hàng không thành công!',
            ));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomerCustomer  $businessPartner
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerCustomer $businessPartner)
    {
        //
    } 
}
