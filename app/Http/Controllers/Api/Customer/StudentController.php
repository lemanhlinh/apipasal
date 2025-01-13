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

    public function index(Request $request)
    {
        $query = Student::with([
            'customer' => function($query) {
                $query->with([
                    'country' => function ($query) {
                        $query->select('id', 'name');
                    },
                    'city' => function ($query) {
                        $query->select('id', 'name', 'code');
                    },
                    'district' => function ($query) {
                        $query->select('id', 'name', 'code');
                    },
                    'management' => function ($query1) {
                        $query1->select('id', 'name', 'department_id')->with(['department' => function ($query2) {
                            $query2->select('id', 'title')->with(['campuses' => function ($query3) {
                                $query3->select('campuses.id', 'campuses.code');
                            }]);
                        }]);
                    },
                ]);
            },
            'contracts' => function($query) {
                $query->with(['product', 'bills', 'product_category']);
            },
            'segment' => function ($query) {
                $query->with(['market']);
            },
        ])
        ->orderBy('id', 'DESC');

        $telephone = $request->telephone;

        if ($request->has('telephone') && $telephone) {
            $query->whereHas('customer', function ($query) use ($telephone) {
                $query->where('phone', $telephone);
            });
        }

        $students = $query->paginate(20);

        $totalPages = $students->lastPage();
        $data = $students->items();

        foreach ($data as $item) {
            $item->segment->parent = json_decode($item->segment->parent);
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
        return $this->handleTransaction(function() use ($request) {
            $data = $this->studentService->store($request->all());
            return response()->json(array(
                'error' => false,
                'data' => $data,
                'message' => 'Đã thêm mới học viên!',
            ));
        }, 'Chưa thêm được học viên!'); 
    }

    public function detail(Request $request)
    {
        $data = Student::whereHas('customer', function ($query) use ($request) {
                $query->where('phone', $request->telephone);
            })
            ->with([
                'customer' => function ($query) {
                    $query->with([
                        'management' => function ($query) {
                            $query->select('id', 'name', 'department_id')->with(['department' => function ($queryDepartment) {
                                $queryDepartment->select('id', 'title')->with(['campuses' => function ($queryCampus) {
                                    $queryCampus->select('campuses.id', 'campuses.code');
                                }]);
                            }]);
                        },
                        'country' => function ($query) {
                            $query->select('id', 'name');
                        },
                        'city' => function ($query) {
                            $query->select('id', 'name', 'code');
                        },
                        'district' => function ($query) {
                            $query->select('id', 'name', 'code');
                        },
                        'segment' => function ($query) {
                            $query->with([
                                'district' => function ($queryDistrict) {
                                    $queryDistrict->select('id', 'name', 'code');
                                },
                                'market' => function ($queryMarket) {
                                    $queryMarket->select('id', 'title');
                                },
                            ]);
                        },
                        'students',
                    ]);
                },
                'segment' => function ($query) {
                    $query->with([
                        'district' => function ($queryDistrict) {
                            $queryDistrict->select('id', 'name', 'code');
                        },
                        'market' => function ($queryMarket) {
                            $queryMarket->select('id', 'title');
                        },
                    ]);
                },
            ])
            ->first();

        if ($data) {
            $data->customer->source_info;
            $data->customer->consulting_detail =  json_decode($data->consulting_detail);
            foreach ($data->customer->segment as $segmentItem) {
                $segmentItem->parent = json_decode($segmentItem->parent);
            }
        }

        return response()->json(array(
            'error' => false,
            'message' => 'Thành công',
            'data' => $data
        ));
    }

    public function update(Request $request)
    {
        return 'Student Update';
    }

    public function destroy(Request $request)
    {
        return 'Student Delete';
    }

    public function waiting(Request $request)
    {
        $students = Student::where('status', 0)->get();
        return 'Student Waiting';
    }
}
