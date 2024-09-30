<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Courses\CreateCourses;
use App\Http\Requests\Courses\UpdateCourses;
use App\Models\CourseCategories;
use App\Models\Courses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses = Courses::with('courseCategory')->orderBy('id','DESC')->get();
        return $courses;
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
            $cat_id = $request->input('cat_id');
            $course_category = $request->input('course_category');
            $code = $request->input('code');
            $number_course = $request->input('number_course');
            Courses::create([
                'title' => $title,
                'cat_id' => $course_category['id'],
                'code' => $code,
                'number_course' => $number_course,
                'active' => 1,
            ]);

            DB::commit();
            return response()->json(array(
                'error' => false,
                'result' => 'Đã thêm mới khóa học',
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
                'result' => 'Chưa thêm được khóa học',
            ));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Courses  $courses
     * @return \Illuminate\Http\Response
     */
    public function show(Courses $courses)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Courses  $courses
     * @return \Illuminate\Http\Response
     */
    public function edit(Courses $courses)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Courses  $courses
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $title = $request->input('title');
            $cat_id = $request->input('cat_id');
            $course_category = $request->input('course_category');
            $code = $request->input('code');
            $number_course = $request->input('number_course');
            $cat = Courses::findOrFail($id);
            $cat->update([
                'title' => $title,
                'cat_id' => $course_category['id'],
                'code' => $code,
                'number_course' => $number_course,
                'active' => 1,
            ]);
            DB::commit();
            return response()->json(array(
                'error' => false,
                'result' => 'Cập nhật thành công',
            ));
        } catch (\Exception $exception) {
            \Log::info([
                'message' => $exception->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'error' => true,
                'result' => 'Chưa cập nhật được',
            ));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Courses  $courses
     * @return \Illuminate\Http\Response
     */
    public function destroy(Courses $courses)
    {
        //
    }

    public function changeActive($id)
    {
        $courseCategory = Courses::findOrFail($id);
        $courseCategory->update(['active' => !$courseCategory->active]);
        return [
            'status' => true,
            'message' => 'Cập nhật trạng thái thành công'
        ];
    }

    public function delete($id) {
        DB::beginTransaction();
        try {
            $cat = Courses::findOrFail($id);
            $cat->delete();
            DB::commit();
            return response()->json(array(
                'success' => true,
                'message' => 'Đã xóa khóa học',
            ));
        } catch (\Exception $exception) {
            DB::rollBack();
            \Log::info([
                'message' => $exception->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'error' => true,
                'message' => 'Chưa xóa được khóa học ' . $exception->getMessage(),
            ));
        }
    }
}
