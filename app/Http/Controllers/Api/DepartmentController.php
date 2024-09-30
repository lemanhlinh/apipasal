<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Department\CreateDepartment;
use App\Http\Requests\Department\UpdateDepartment;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departments = Department::with(['user_manage','users','regencies','campuses'])->whereIsRoot()->orderBy('id', 'DESC')->get();
        return $departments;
    }

    public function listAll()
    {
        $departments = Department::with(['user_manage','users','regencies','campuses'])->orderBy('ordering','ASC')->orderBy('id', 'DESC')->get()->toTree();
        foreach ($departments as $department){
            $department->title_rename = $department->title;
            if ($department->children){
                $traverse = function ($categories, $prefix = '-') use (&$traverse) {
                    foreach ($categories as $category) {
                        $category->title_rename = $prefix.' '.$category->title;
                        $traverse($category->children, $prefix.'-');
                    }
                };

                $traverse($department->children);
            }
        }
        $departments = $this->flattenChildren($departments);
        return $departments;
    }

    public function listSub($id)
    {
        $departments[0] = Department::with(['user_manage','users','regencies','campuses'])->orderBy('ordering','ASC')->descendantsAndSelf($id)->toTree()->first();
        foreach ($departments as $department){
            $department->title_rename = $department->title;
            if ($department->children){
                $traverse = function ($categories, $prefix = '-') use (&$traverse) {
                    foreach ($categories as $category) {
                        $category->title_rename = $prefix.' '.$category->title;
                        $traverse($category->children, $prefix.'-');
                    }
                };

                $traverse($department->children);
            }
        }
        $departments = $this->flattenChildren($departments);
        return $departments;
    }

    public function flattenChildren($nodes, $parent = null) {
        $flattenedNodes = [];

        foreach ($nodes as $node) {
            $node['parent'] = $parent; // Thêm giá trị parent cho node hiện tại

            $flattenedNodes[] = $node;

            if (!empty($node['children'])) {
                $children = $this->flattenChildren($node['children'], $node); // Truyền node hiện tại làm parent cho children
                $flattenedNodes = array_merge($flattenedNodes, $children);
                unset($node['children']);
            }
        }

        return $flattenedNodes;
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
            $code = $request->input('code');
            $campuses = $request->input('campuses');
            $type_office = $request->input('type_office');
            $user_id = $request->input('user_manage');
            $ordering = $request->input('ordering');
            $department = Department::create([
                'title' => $title,
                'code' => $code,
                'type_office' => $type_office,
                'user_id' => $user_id['id'],
                'active' => 1,
                'ordering' => $ordering,
            ]);
            if (isset($campuses)) {
                foreach ($campuses as $campuseId) {
                    if ($campuseId){
                        $department->campuses()->attach($campuseId['id']);
                    }
                }
            }

            $parent =  $request->input('parent');
            if ($parent){
                $note = Department::find($parent['id']);
                $note->appendNode($department);
            }

            DB::commit();
            return response()->json(array(
                'error' => false,
                'result' => 'Đã thêm mới phòng ban',
            ));
        } catch (\Exception $ex) {
            DB::rollBack();
            \Log::info([
                'message' => $ex->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'success' => true,
                'message' => 'Chưa thêm được phòng ban',
            ));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function edit(Department $department)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request  $request, $id)
    {
        DB::beginTransaction();
        try {
            $title = $request->input('title');
            $code = $request->input('code');
            $campuses = $request->input('campuses');
            $type_office = $request->input('type_office');
            $user_id = $request->input('user_manage');
            $ordering = $request->input('ordering');
            $department = Department::findOrFail($id);
            $department->update([
                'title' => $title,
                'code' => $code,
                'type_office' => $type_office,
                'user_id' => $user_id['id'],
                'active' => 1,
                'ordering' => $ordering,
            ]);

            $parent =  $request->input('parent');
            if ($parent){
                $note = Department::find($parent['id']);
                $note->appendNode($department);
            }

            $department->campuses()->detach();

            if (isset($campuses)) {
                foreach ($campuses as $campuseId) {
                    if ($campuseId){
                        $department->campuses()->attach($campuseId['id']);
                    }
                }
            }

            DB::commit();
            return response()->json(array(
                'error' => false,
                'result' => 'Cập nhật thành công phòng ban',
            ));
        } catch (\Exception $exception) {
            \Log::info([
                'message' => $exception->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'success' => true,
                'message' => 'Chưa cập nhật được phòng ban',
            ));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy(Department $department)
    {
        //
    }

    /**
     * @param $id
     * @return array
     */
    public function changeActive($id)
    {
        $campuses = Department::findOrFail($id);
        $campuses->update(['active' => !$campuses->active]);
        return [
            'success' => true,
            'message' => trans('message.change_active_article_success')
        ];
    }

    public function delete($id) {
        $cat = Department::findOrFail($id);
        $cat->delete();
        return response()->json(array(
            'success' => false,
            'message' => 'Đã xóa phòng ban',
        ));
    }
}
