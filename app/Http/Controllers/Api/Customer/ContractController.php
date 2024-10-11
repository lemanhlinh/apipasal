<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use App\Models\Customer\Contract;

use App\Services\Customer\ContractService;

class ContractController extends Controller
{
    protected $contractService;

    public function __construct(ContractService $contractService)
    {
        $this->contractService = $contractService;
    }

    public function index()
    {
        $user = Auth::user();

        $model = Contract::orderBy('id', 'DESC')
            ->where('manage_id', $user->id)
            ->orWhereHas('student.customer', function ($query) use ($user) {
                $query->where('manage_id', $user->id);
            })
            ->with([
                'product_category',
                'product',
                'management' => function ($query) {
                    $query->select('id', 'name', 'department_id')->with(['department' => function ($query2) {
                        $query2->select('id', 'title')->with(['campuses' => function ($query3) {
                            $query3->select('campuses.id', 'campuses.code');
                        }]);
                    }]);
                },
                'user_create' => function ($query) {
                    $query->select('id', 'name', 'department_id')->with(['department' => function ($query2) {
                        $query2->select('id', 'title')->with(['campuses' => function ($query3) {
                            $query3->select('campuses.id', 'campuses.code');
                        }]);
                    }]);
                },
                'student' => function ($query) {
                    $query->with([
                        'segment' => function ($query) {
                            $query->with(['market']);
                        },
                        'customer',
                    ]);
                },
                'bills'
            ]);

        $contracts = $model->paginate(20);

        $totalPages = $contracts->lastPage();
        $data = $contracts->items();

        foreach ($data as $contract) {
            $contract->student->segment->parent = json_decode($contract->student->segment->parent);
            $contract->student->customer->source;
        }

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
        DB::beginTransaction();
        try {
            $data = [];
            
            foreach ($request->contracts as $contract) {
                $contract['student_id'] = $request->student_id;
                $contract['active'] = 0;
                $data [] = $this->contractService->store($contract);
            }

            DB::commit();
            return response()->json(array(
                'error' => false,
                'data' => [
                    'student_id' => $request->student_id,
                    'contracts' => $data
                ],
                'result' => 'Đã thêm mới khách hàng!',
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
                'result' => 'Chưa thêm được khách hàng!',
            ));
        }
    }
}
