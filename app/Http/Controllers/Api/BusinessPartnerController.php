<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessPartner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Campuses;

class BusinessPartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = BusinessPartner::with(['clue'])->orderBy('id', 'DESC');
        if(request()->input('segment')) {
            $query->where('segment', request()->segment);
        }
 
        $partners = $query->paginate(100);

        $campusesIds = [];
        foreach($partners as $item) {
            if($item->campuses_id != '0' && is_array(json_decode($item->campuses_id))) {
                $campusesIds = array_merge($campusesIds, json_decode($item->campuses_id));
                $campuses = Campuses::whereIn('id', $campusesIds)->get()->keyBy('id');
            }
        }
        foreach ($partners as $partner) {
            $partner->campuses = collect(json_decode($partner->campuses_id, true))->map(function ($id) use ($campuses) {
                return $campuses->get($id);
            });
        }

        return response()->json(array(
            'data' => $partners,
        ));
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
            if (!$request->has('title')) {
                return response()->json(['message' => 'Chưa nhập tên đối tác', 'success' => false], 200);
            }
            $title = $request->title;
            $phone = $request->phone;
            $email = $request->email;
            $type = $request->type;
            $type_campuses = $request->type_campuses;
            $segment = $request->segment;
            $info_partner = $request->info_partner;
            $campuses = $request->campuses;

            $partner = BusinessPartner::create([
                'title' => $title,
                'phone' => $phone,
                'email' => $email,
                'type' => $type,
                'type_campuses' => $type_campuses,
                'segment' => $segment,
                'info_partner' => $info_partner,
                'campuses_id' => json_encode($campuses),
                'active' => 1
            ]);

            $clues = $request->input('clue');
            if ($clues) {
                foreach ($clues as $clue) {
                    if ($clue['title']) {
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
            return  response()->json(array(
                'success' => true,
                'data' => $partner,
                'message' => 'Đã thêm mới đối tác',
            ));
        } catch (\Exception $ex) {
            DB::rollBack();
            \Log::info([
                'message' => $ex->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'success' => false,
                'message' => 'Chưa thêm được đối tác' . $ex->getMessage(),
            ));
        }
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
    public function edit($id)
    {
        $partner = BusinessPartner::with(['clue'])->where('id', $id)->first();
        $campusesIds = $partner->campuses_id ? json_decode($partner->campuses_id) : [];
        $campuses = Campuses::whereIn('id', $campusesIds)->get();
        $partner->campuses = $campuses;
        return $partner;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BusinessPartner  $businessPartner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
    
        try {
            if (!$request->has('title')) {
                return response()->json(['message' => 'Chưa nhập tên đối tác', 'success' => false], 200);
            }
    
            $partner = BusinessPartner::findOrFail($id);
    
            $partner->update([
                'title' => $request->title,
                'phone' => $request->phone,
                'email' => $request->email,
                'type' => $request->type,
                'type_campuses' => $request->type_campuses,
                'segment' => $request->segment,
                'info_partner' => $request->info_partner,
                'campuses_id' => json_encode($request->campuses),
                'active' => 1
            ]);
    
            $clues = $request->input('clue');
            if ($clues) {
                $partner->clue()->delete(); // Xóa các clues cũ
                foreach ($clues as $clue) {
                    if ($clue['title']) {
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
            return response()->json([
                'success' => true,
                'data' => $partner,
                'message' => 'Đã cập nhật đối tác',
            ]);
        } catch (\Exception $ex) {
            DB::rollBack();
            \Log::info([
                'message' => $ex->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Chưa cập nhật được đối tác' . $ex->getMessage(),
            ]);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BusinessPartner  $businessPartner
     * @return \Illuminate\Http\Response
     */

    public function changeActive($id)
    {
        $dataItem = BusinessPartner::findOrFail($id);
        $dataItem->update(['active' => !$dataItem->active]);
        return [
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công'
        ];
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $data = BusinessPartner::findOrFail($id);
            $data->delete();
            DB::commit();
            return response()->json(array(
                'success' => true,
                'message' => 'Đã xóa đối tác',
            ));
        } catch (\Exception $ex) {
            DB::rollBack();
            \Log::info([
                'message' => $ex->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'success' => false,
                'message' => 'Chưa xoá được đối tác',
            ));
        }
    }

}
