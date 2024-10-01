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
        $calendar = CalendarLearn::orderBy('id','DESC')->get();
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
    public function store(Request  $request)
    {
        DB::beginTransaction();
        try {
            $title = $request->input('title');
            if(!$title) {
                return response()->json(array(
                    'success' => false,
                    'message' => 'Vui lòng nhập lịch học',
                ));
            }
            $days = $request->input('days');
            CalendarLearn::create([
                'title' => $title,
                'days' => $days,
                'active' => 1,
            ]);

            DB::commit();
            return response()->json(array(
                'success' => true,
                'message' => 'Đã thêm mới Lịch học',
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
                'message' => 'Chưa thêm được lịch học ',
            ));
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
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $title = $request->input('title');
            $days = $request->input('days');
            $cat = CalendarLearn::findOrFail($id);
            $cat->update([
                'title' => $title,
                'days' => $days,
                'active' => 1,
            ]);
            DB::commit();
            return response()->json(array(
                'success' => true,
                'message' => 'Cập nhật thành công',
            ));
        } catch (\Exception $exception) {
            \Log::info([
                'message' => $exception->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'success' => false,
                'message' => 'Chưa cập nhật được',
            ));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CalendarLearn  $calendarLearn
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = CalendarLearn::findOrFail($id);
        $data->delete($id);

        return [
            'success' => true,
            'message' => trans('message.delete_page_success')
        ];
    }

    public function changeActive($id)
    {
        $data = CalendarLearn::findOrFail($id);
        $data->update(['active' => !$data->active]);
        return [
            'success' => true,
            'message' => trans('message.change_active_article_success')
        ];
    }
}
