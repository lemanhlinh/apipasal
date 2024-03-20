<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Courses\CreateCourseCategory;
use App\Http\Requests\Courses\UpdateCourseCategory;
use App\Models\Campuses;
use App\Models\CourseCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CourseCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cat = CourseCategories::with('courses')->orderBy('id','DESC')->get();
        return $cat;
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
            CourseCategories::create([
                'title' => $title,
                'active' => 1,
            ]);

            DB::commit();
            return response()->json(array(
                'error' => false,
                'result' => 'Đã thêm mới nhóm khóa học',
            ));
        } catch (\Exception $ex) {
            DB::rollBack();
            \Log::info([
                'message' => $ex->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'error' => false,
                'result' => 'Chưa thêm được nhóm khóa học',
            ));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CourseCategories  $courseCategories
     * @return \Illuminate\Http\Response
     */
    public function show(CourseCategories $courseCategories)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CourseCategories  $courseCategories
     * @return \Illuminate\Http\Response
     */
    public function edit(CourseCategories $courseCategories)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CourseCategories  $courseCategories
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCourseCategory $req, $id)
    {
        DB::beginTransaction();
        try {
            $data = $req->validated();
            $cat = CourseCategories::findOrFail($id);
            $cat->update([
                'title' => $data['title'],
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
     * @param  \App\Models\CourseCategories  $courseCategories
     * @return \Illuminate\Http\Response
     */
    public function destroy(CourseCategories $courseCategories)
    {
        //
    }
}
