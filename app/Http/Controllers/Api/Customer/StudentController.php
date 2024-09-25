<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\Customer;
use App\Models\Customer\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Services\Customer\StudentService;

class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function index()
    {
        $data = Student::with([
            'customer',
            'contracts',
            'segment'
        ])
        ->paginate(20);

        $totalPages = $data->lastPage();

        return response()->json(array(
            'error' => false,
            'data' => [
                'data' => $data->items(),
                'total_pages' => $totalPages,
            ],
        ));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->studentService->store($request->all());

            DB::commit();
            return response()->json(array(
                'error' => false,
                'data' => $data,
                'result' => 'Đã thêm mới học viên!',
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
                'result' => 'Chưa thêm được học viên!',
            ));
        }
    }

    public function detail()
    {
        return 'Student Detail';
    }

    public function update(Request $request)
    {
        return 'Student Update';
    }

    public function destroy(Request $request)
    {
        return 'Student Delete';
    }
}
