<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerCustomer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CustomerCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return CustomerCustomer::orderBy('updated_at', 'DESC')
        ->with([
            'user' => function ($query) {
                $query->select('id', 'name', 'department_id');
            },
            'user.department' => function ($query) {
                $query->select('id', 'title')->with('campuses:id,title,code');
            }
        ])
        ->get();
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

            switch ($request->segment) {
                case 1:
                    $segmentDetail = [
                        'children' => $request->segmentInfo->children, 
                    ];
                    break;
                case 2:
                    $segmentDetail = [
                        'children' => $request->segmentInfo->children, 
                    ];
                    break;
                case 3:
                    $segmentDetail = [
                        'academic_year' => $request->segmentInfo->academic_year,
                        'district' => $request->segmentInfo->district,
                        'district_name' => $request->segmentInfo->district_name,
                        'school' => $request->segmentInfo->school,
                        'school_name' => $request->segmentInfo->school_name,
                        'class' => $request->segmentInfo->class,
                        'parent' => $request->segmentInfo->parent,
                    ];
                    break;
                case 4:
                    $segmentDetail = [
                        'academic_year' => $request->segmentInfo->academic_year,
                        'district' => $request->segmentInfo->district,
                        'district_name' => $request->segmentInfo->district_name,
                        'school' => $request->segmentInfo->school,
                        'school_name' => $request->segmentInfo->school_name,
                        'major' => $request->segmentInfo->major,
                        'major_name' => $request->segmentInfo->major_name,
                    ];
                    break;
                case 5:
                    $segmentDetail = [
                        'company' => $request->segmentInfo->company,
                        'position' => $request->segmentInfo->position,
                        'work' => $request->segmentInfo->work,
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
                'date_registration' => $request->date_registration,
                'product_category' => $request->product_category,
                'product' => $request->product,
                'manage_id' => $user->id,
                'active' => 1,
            ];

            return response()->json(array(
                'error' => false,
                'data' => $request->segmentInfo,
                'result' => 'Đã thêm mới khách hàng!',
            ));

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
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustomerCustomer  $businessPartner
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $partner = CustomerCustomer::with(['clue','campuses'])->where('id',$id)->first();
        return $partner;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomerCustomer  $businessPartner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $title = $request->input('title');
            $phone = $request->input('phone');
            $email = $request->input('email');
            $type = $request->input('type');
            $type_campuses = $request->input('type_campuses');
            $segment = $request->input('segment');
            $info_partner = $request->input('info_partner');
            $campuses = $request->input('campuses');
            $partner = CustomerCustomer::findOrFail($id);
            $partner->update([
                'title' => $title,
                'phone' => $phone,
                'email' => $email,
                'type' => $type,
                'type_campuses' => $type_campuses,
                'segment' => $segment,
                'info_partner' => $info_partner,
                'campuses_id' => $campuses?$campuses['id']:null,
                'active' => 1,
            ]);

            $clues = $request->input('clue');
            if ($clues){
                $clueTitles = collect($clues)->pluck('title')->all();
                $partner->clue()
                    ->whereNotIn('title', $clueTitles)
                    ->delete();
                foreach ($clues as $clue){
                    if ( $clue['title']){
                        $partner->clue()->create([
                            'title' => $clue['title'],
                            'phone' => $clue['phone'],
                            'email' => $clue['email'],
                            'position' => $clue['position'],
                            'birthday' => Carbon::parse($clue['birthday'])->toDateString(),
                            'active' => 1,
                        ]);
                    }
                }
            }


            DB::commit();
            return response()->json(array(
                'error' => false,
                'result' => 'Cập nhật thành công đối tác',
            ));
            Session::flash('success', 'Cập nhật thành công đối tác');
        } catch (\Exception $exception) {
            Log::info([
                'message' => $exception->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'error' => true,
                'result' => 'Chưa cập nhật được đối tác',
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
