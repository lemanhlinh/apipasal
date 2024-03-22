<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campuses\CreateCampuses;
use App\Http\Requests\Campuses\UpdateCampuses;
use App\Models\Campuses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class CampusesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $campuses = Campuses::with(['classrooms' => function($q){
            $q->select('id','title','campuses_id');
        }])->with(['departments' => function($q){
            $q->withCount('users');
        }])->orderBy('id', 'DESC')->get();

        foreach ($campuses as $campus) {
            $totalUsersCount = 0;
            foreach ($campus->departments as $department) {
                $totalUsersCount += $department->users_count;
            }
            $campus->total_users_count = $totalUsersCount;
        }

        return $campuses;
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
     * @param  CreateCampuses  $req
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $title = $request->input('title');
            $code = $request->input('code');
            $code_short = $request->input('code_short');
            $type_campuses = $request->input('type_campuses');
            $campuses = Campuses::create([
                'title' => $title,
                'code' => $code,
                'code_short' => $code_short,
                'type_campuses' => $type_campuses,
                'active' => 1,
            ]);
            $classroom = $request->input('classrooms');

            foreach ($classroom as $class){
                $campuses->classrooms()->create([
                    'title' => $class['title']
                ]);
            }

            DB::commit();
            return  response()->json(array(
                'error' => false,
                'result' => 'Đã thêm mới Trung tâm',
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
                'result' => 'Chưa thêm được Trung tâm',
            ));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Campuses  $campuses
     * @return \Illuminate\Http\Response
     */
    public function show(Campuses $campuses)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Campuses  $campuses
     * @return \Illuminate\Http\Response
     */
    public function edit(Campuses $campuses)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Campuses  $campuses
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $title = $request->input('title');
            $code = $request->input('code');
            $code_short = $request->input('code_short');
            $type_campuses = $request->input('type_campuses');
            $campuses = Campuses::findOrFail($id);
            $campuses->update([
                'title' => $title,
                'code' => $code,
                'code_short' => $code_short,
                'type_campuses' => $type_campuses,
                'active' => 1,
            ]);

            $classrooms = $request->input('classrooms');

            $classroomTitles = collect($classrooms)->pluck('title')->all();

            $campuses->classrooms()
                ->whereNotIn('title', $classroomTitles)
                ->delete();

            foreach ($classrooms as $classroom) {
                $campuses->classrooms()->updateOrCreate(
                    ['title' => $classroom['title']]
                );
            }
            DB::commit();
            return response()->json(array(
                'error' => false,
                'result' => 'Cập nhật thành công trung tâm',
            ));
            Session::flash('success', 'Cập nhật thành công trung tâm');
            return redirect()->back();
        } catch (\Exception $exception) {
            \Log::info([
                'message' => $exception->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'error' => true,
                'result' => 'Chưa cập nhật được trung tâm',
            ));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Campuses  $campuses
     * @return \Illuminate\Http\Response
     */
    public function destroy(Campuses $campuses)
    {
        //
    }
}
