<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Customer\Classes;

use App\Services\Customer\ClassService;

class ClassController extends Controller
{
    protected $classService;
    public function __construct(ClassService $classService)
    {
        $this->classService = $classService;
    }

    public function index(Request $request)
    { 
        $query = Classes::orderBy('id', 'DESC')
            ->with([
                'class',
                'course',
                'course_category',
                'calendar_learn',
                'day_shift_learn',
                'time_study',
                'user_admission',
                'campuse',
                'opening' => function($queryOpening) {
                    $queryOpening->orderBy('id', 'DESC');
                }
            ]);

        $classes = $query->paginate(20); 

        $totalPages = $classes->lastPage();
        $data = $classes->items();

        return response()->json(array(
            'error' => false,
            'data' => [
                'data' => $data,
                'total_pages' => $totalPages,
            ],
        ));
    }

    public function store(Request $request)
    {
        return $this->handleTransaction(function () use ($request) {
            $data = $this->classService->store($request->all());

            return response()->json(array(
                'error' => false,
                'data' => $data,
                'message' => 'Thêm lịch khai giảng thành công!',
            ));
        }, 'Thêm lịch khai giảng không thành công!');
    }

    public function addOpening(Request $request)
    {
        return $this->handleTransaction(function () use ($request) {
            $data = $this->classService->addOpening($request->all());

            return response()->json(array(
                'error' => false,
                'data' => $data,
                'message' => 'Lùi lịch khai giảng thành công!',
            ));
        }, 'Lùi lịch khai giảng không thành công!');
    }
}
