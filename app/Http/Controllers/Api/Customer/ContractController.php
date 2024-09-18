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

        $data = Contract::orderBy('id', 'DESC')
            ->where('manage_id', $user->id)
            ->orWhereHas('customer', function ($query) use ($user) {
                $query->where('manage_id', $user->id);
            })
            ->with([
                'management' => function ($query) {
                    $query->select('id', 'name');
                },
                'student' => function ($query) {
                    $query->select('id', 'name')->with([
                        'customer' => function ($queryStudent) {
                            $queryStudent->with([
                                'management' => function ($queryCustomer) {
                                    $queryCustomer->select('id', 'name', 'department_id')->with(['department' => function ($query2) {
                                        $query2->select('id', 'title')->with(['campuses' => function ($query3) {
                                            $query3->select('campuses.id', 'campuses.code');
                                        }]);
                                    }]);
                                },
                                'source_info' => function ($query) {
                                    $query->select('id', 'title', 'code');
                                }
                            ]);
                        },
                    ]);
                },
                'bills'
            ])
            ->get();

        return $data;
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $data = $this->contractService->store($request);

            DB::commit();
            return response()->json(array(
                'error' => false,
                'data' => $data,
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
