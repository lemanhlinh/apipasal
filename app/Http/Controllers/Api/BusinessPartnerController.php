<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessPartner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

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
            $partner = BusinessPartner::create([
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
                foreach ($clues as $clue){
                    if ($clue['title']){
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
                'error' => false,
                'data' => $partner,
                'result' => 'Đã thêm mới đào tạo',
            ));
        } catch (\Exception $ex) {
            DB::rollBack();
            \Log::info([
                'message' => $ex->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'error' => true,
                'result' => 'Chưa thêm được đào tạo',
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
        $partner = BusinessPartner::with(['clue','campuses'])->where('id',$id)->first();
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
            $title = $request->input('title');
            $phone = $request->input('phone');
            $email = $request->input('email');
            $type = $request->input('type');
            $type_campuses = $request->input('type_campuses');
            $segment = $request->input('segment');
            $info_partner = $request->input('info_partner');
            $campuses = $request->input('campuses');
            $partner = BusinessPartner::findOrFail($id);
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
            \Log::info([
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
     * @param  \App\Models\BusinessPartner  $businessPartner
     * @return \Illuminate\Http\Response
     */
    public function destroy(BusinessPartner $businessPartner)
    {
        //
    }
}
