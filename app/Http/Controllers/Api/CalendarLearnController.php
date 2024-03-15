<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Schedule\CreateCalendarLearn;
use App\Http\Requests\Schedule\UpdateCalendarLearn;
use App\Models\CalendarLearn;
use App\Models\CourseCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CalendarLearnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $calendar = CalendarLearn::all();
        return $calendar;
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
    public function store(CreateCalendarLearn $req)
    {
        DB::beginTransaction();
        try {
            $data = $req->validated();
            CalendarLearn::create([
                'title' => $data['title'],
                'days' => $data['days'],
                'active' => 1,
            ]);

            DB::commit();
            Session::flash('success', 'Đã thêm mới Lịch học');
            return redirect()->back();
        } catch (\Exception $ex) {
            DB::rollBack();
            \Log::info([
                'message' => $ex->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            Session::flash('danger', 'Chưa thêm được Lịch học');
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CalendarLearn  $calendarLearn
     * @return \Illuminate\Http\Response
     */
    public function show(CalendarLearn $calendarLearn)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CalendarLearn  $calendarLearn
     * @return \Illuminate\Http\Response
     */
    public function edit(CalendarLearn $calendarLearn)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CalendarLearn  $calendarLearn
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCalendarLearn $req, $id)
    {
        DB::beginTransaction();
        try {
            $data = $req->validated();
            $cat = CalendarLearn::findOrFail($id);
            $cat->update([
                'title' => $data['title'],
                'days' => $data['days'],
                'active' => 1,
            ]);
            DB::commit();
            Session::flash('success', 'Cập nhật thành công');
            return redirect()->back();
        } catch (\Exception $exception) {
            \Log::info([
                'message' => $exception->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            Session::flash('danger', 'Chưa cập nhật được');
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CalendarLearn  $calendarLearn
     * @return \Illuminate\Http\Response
     */
    public function destroy(CalendarLearn $calendarLearn)
    {
        //
    }
}
